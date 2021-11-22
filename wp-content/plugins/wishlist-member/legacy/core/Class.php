<?php
/**
 * Core Class for WishList Member
 * @author Mike Lopez <mjglopez@gmail.com>
 * @package wishlistmember
 *
 * @version $Rev: 7514 $
 * $LastChangedBy: mike $
 * $LastChangedDate: 2021-01-14 13:28:49 -0500 (Thu, 14 Jan 2021) $
 */
if (!defined('ABSPATH'))
	die();
if (!class_exists('WishListMemberCore')) {

	/**
	 * Core WishList Member Class
	 * @package wishlistmember
	 * @subpackage classes
	 */
	class WishListMemberCore {

		const ActivationURLs = 'wishlistactivation.com';
		const ActivationMaxRetries = 5;
		
		/**
		 * Integration shortcodes
		 * @var array
		 */
		public $IntegrationShortcodes = array();
		/**
		 * Short codes
		 * @var array
		 */
		public $short_codes = array();
		/**
		 * Merge codes
		 * @var array
		 */
		public $merge_codes = array();

		// -----------------------------------------
		// Constructor
		function Constructor($pluginfile, $sku, $menuid, $title, $link) { // constructor
			global $wpdb;
			require_once( ABSPATH . 'wp-admin/includes/plugin.php');

			$this->PluginOptionName = 'WishListMemberOptions';
			$this->TablePrefix = $wpdb->prefix . 'wlm_';
			$this->OptionsTable = $this->TablePrefix . 'options';

			// Run this before we include pluggable.php so our wp_password_change_notification gets regognize first.
			if ( $this->GetOption( 'disable_password_change_email_for_admin' ) ) {
				if ( !function_exists( 'wp_password_change_notification' ) ) {
				    function wp_password_change_notification() {}
				}
				include_once( ABSPATH . 'wp-includes/pluggable.php');
			}

			// character encoding
			$this->BlogCharset = get_option('blog_charset');

			$this->ProductSKU = $sku;
			$this->MenuID = $menuid;
			$this->Title = $title;
			$this->Link = $link;

			$this->Version = WLM3_PLUGIN_VERSION;
			$this->pluginPath = $pluginfile;
			$this->pluginDir = dirname($this->pluginPath);
			$this->PluginFile = basename(dirname($pluginfile)) . '/' . basename($pluginfile);
			$this->PluginSlug = sanitize_title_with_dashes(WLM3_PLUGIN_NAME);
			$this->pluginBasename = plugin_basename($this->pluginPath);
			// this method works even if the WLM folder is just a symlink
			// $this->pluginURL = plugins_url('', '/') . basename($this->pluginDir);
			$this->pluginURL = plugins_url('', $this->pluginPath);

			$this->xhr = new WishListXhr($this);
			$this->emailbroadcast = new WishListEmailBroadcast;
			$this->LevelOptions = new \WishListMember\Level_Options($this->TablePrefix);
			$this->Menus = array();
			$this->MarketplaceCheckURL = 'http://wishlist-marketplace.s3.amazonaws.com/trigger.txt';
			// $market_place = get_transient('wlm_marketplace_check_url_value');
			// if ($market_place === false) {
			// 	$market_place = $this->ReadURL($this->MarketplaceCheckURL, 10, true, true);
			// 	set_transient('wlm_marketplace_check_url_value', $market_place, 86400);
			// }
			$this->Marketplace = false;

			$this->ClearOptions();
			$this->DataMigration();

			if (wlm_arrval($_POST, 'WishListMemberAction') == 'Save') {
				$this->SaveOptions();
			}

			add_action('admin_notices', array(&$this, 'ActivationWarning'));
			add_action('init', array(&$this, 'WPWLKeyProcess'));

			$this->LoadTables();
		}

		/*
		 * Our own hook run everytime an option is updated
		 * couldn't find a way to implement this via wordpress hooks
		 */

		function OptionSaveHook($option_name, $option_value) {
			if ($option_name == 'mask_passwords_in_emails') {
				$this->SaveOption('track-mask_passwords_in_emails', array(time(), $option_value));
			}
		}

		/**
		 * Load WishList Member Tables
		 */
		function LoadTables($force_reload = false) {
			global $wpdb;

			$this->Tables = get_transient( 'wlm_tables' );
			if($force_reload OR !$this->Tables OR $this->Version != $this->GetOption('CurrentVersion')) {
				// prepare table names
				$tables = $wpdb->get_col("SHOW TABLES LIKE '{$this->TablePrefix}%'");

				$keys = preg_replace('/^'.preg_quote($this->TablePrefix).'/i', '', $tables);
				$this->Tables = (object) array_combine($keys, $tables);
				set_transient( 'wlm_tables', $this->Tables, 60*60*24 );
			}
		}

		/**
		 * Core Activation Routine
		 */
		function CoreActivate() {
			$this->CreateCoreTables();
		}

		/**
		 * Displays Beta Tester Message
		 */
		function BetaTester($return) {
			$url = 'http://member.wishlistproducts.com/';
			$aff = $this->GetOption('affiliate_id');
			if ( $aff && !empty($aff)  ) {
				if ( wp_http_validate_url($aff) ) {
					$url = esc_url($aff);
				} else {
					$url = 'https://member.wishlistproducts.com/wlp.php?af=' . $aff;
				}
			}

			$message = "This is a <strong><a href='{$url}'>WishList Member</a></strong> Beta Test Site.";
			if (is_admin()) {
				echo '<div class="error fade"><p>';
				echo $message;
				echo '</p></div>';
			} else {
				echo '<div style="background:#FFEBE8; border:1px solid #CC0000; border-radius:3px; padding:0.2em 0.6em;">';
				echo $message;
				echo '</div>';
			}
			return $return;
		}

		/**
		 * Adds an admin menu
		 * @param string $key Menu Key
		 * @param string $name Menu Name
		 * @param string $file Menu File
		 */
		function AddMenu($key, $name, $file, $hasSubMenu = false) {
			$this->Menus[$key] = array('Name' => $name, 'File' => $file, 'HasSubMenu' => (bool) $hasSubMenu);
		}

		/**
		 * Retrieves a menu object.  Also displays an HTML version of the menu if the $html parameter is set to true
		 * @param string $key The index/key of the menu to retrieve
		 * @param boolean $html If true, it echoes the url in as an HTML link
		 * @return object|false Returns the menu object if successful or false on failure
		 */
		function GetMenu($key, $html = false) {
			$obj = $this->Menus[$key];
			if ($obj) {
				$obj = (object) $obj;
				$obj->URL = '?page=' . $this->MenuID . '&wl=' . $key;
				$obj->HTML = '<a href="' . $obj->URL . '">' . $obj->Name . '</a>';
				if ($html)
					echo $obj->HTML;
				return $obj;
			}else {
				return false;
			}
		}

		function get_admin_page_to_include(&$include = null, &$wl = null) {
			$wl = trim((string) wlm_arrval($_GET, 'wl'));
			$include = '';
			if(isset($this->Menus[$wl])) {
				$menu = $this->Menus[$wl];
				$include = $this->pluginDir . '/admin/' . $menu['File'];
			}
			if (!$include || !file_exists($include) || !is_file($include)) {
				$include = $this->pluginDir . '/admin/dashboard.php';
				$wl = '';
			}
		}

		/**
		 * Includes the correct admin interface baesd on the query variable "wl"
		 */
		function AdminPage() {
			$this->get_admin_page_to_include($include, $wl);
			echo '<div class="wrap wishlist_member_admin">';
			include($include);
			if (WP_DEBUG) {
				echo '<p>' . get_num_queries() . ' queries in ';
				timer_stop(1);
				echo 'seconds.</p>';
			}
			echo '</div>';
			if ($wl != '') {
				echo <<<STRING

				<script>
					jQuery(function($){
						$('#adminmenu #toplevel_page_WishListMember .wp-submenu li').removeClass('current');
						$('#adminmenu #toplevel_page_WishListMember .wp-submenu a[href$=wl\\\\={$wl}]').parent().addClass('current');
					});
				</script>

STRING;
			}
		}

		/**
		 * Displays the content for the "Other" Tab
		 */
		function OtherTab() {
			if (!@readfile('http://www.wishlistproducts.com/download/list.html')) {
				echo'<div class="wrap wishlist_member_admin">', __('<h2>Other WishList Products Plugins</h2><p>For more WordPress tools and resources please visit the <a href="http://wishlistproducts.com/blog" target="_blank">WishList Products Blog</a></p>', 'wishlist-member') . '</div>';
			}
		}

		/**
		 * Displays the interface where the customer can enter the license information
		 */
		function WPWLKey() {
			?>
			<div class="wrap wishlist_member_admin">
				<h2>WishList Products License Information</h2>
				<p><?php _e('Please enter your WishList Products Key below to activate this plugin', 'wishlist-member'); ?></p>
				<form method="post">
					<table class="form-table">
						<tr valign="top">
							<th scope="row" style="border:none;white-space:nowrap;" class="WLRequired"><?php _e('WishList Products Key', 'wishlist-member'); ?></th>
							<td style="border:none" width="1">
								<input type="text" name="<?php $this->Option('LicenseKey', true); ?>" placeholder="WishList Products Key" value="<?php $this->OptionValue(); ?>" size="48" />
							</td>
							<td style="border:none">
								<?php _e('This was sent to the email you used during your purchase', 'wishlist-member'); ?>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type="hidden" value="0" name="<?php $this->Option('LicenseLastCheck'); ?>" />
								<?php
								$this->Options();
								$this->RequiredOptions();
								?>
								<input type="hidden" value="<strong>License Information Saved</strong>" name="WLSaveMessage" />
								<input type="hidden" value="Save" name="WishListMemberAction" />
								<input type="submit" value="Save WishList Products License Key" name="Submit" class="button-primary" />
							</td>
						</tr>
					</table>
				</form>
			</div>
			<?php
		}

		function ActivationWarning() {
			if (!current_user_can('manage_options')) {
				return;
			}
			$rets = $this->GetOption('LicenseRets', true, true);
			if (is_admin() && $rets > 0 && $rets < self::ActivationMaxRetries) {
				$msg = get_transient('wlm_serverunavailable');
				if (!empty($msg)) {
					echo $msg;
				}
			}
		}

		/**
		 * Checks whether a url is exempted from licensing
		 * @param string $url the url to test
		 * @return boolean
		 */
		function isURLExempted($url) {
			$patterns = array(
				'/^[^\.]+$/',
				'/^.+\.loc$/',
				'/^.+\.local$/',
				'/^.+?-liquidwebsites\.com$/', // liquid web staging
				'/^.+?\.wpengine\.com$/', // wpengine cnames
				'/^staging[0-9]*\.[^\.]+\..+$/'
			);
			$res = trim(parse_url($url, PHP_URL_HOST));
			foreach($patterns AS $pattern) {
				if(preg_match($pattern, $res)) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Processes the license information
		 */
		function WPWLKeyProcess() {
			
			// no processing if sku is empty
			if( empty( $this->ProductSKU ) ) {
				return;
			}
		
			if( !function_exists( 'current_user_can' ) ) {
				return;
			}
			if( isset( $_REQUEST['wordpress_wishlist_deactivate'] ) && !current_user_can( 'administrator' ) ) {
				return;
			}

			$WPWLKey = $this->GetOption('LicenseKey');
			$WPWLEmail = $this->GetOption('LicenseEmail');

			//bypass activation for
			if ($WPWLKey == '****************' || $this->isURLExempted(strtolower(get_bloginfo('url')))) {
				$this->DeleteOption( 'LicenseRets', 'LicenseSubscription', 'LicenseExpiration' );
				$WPWLKey = $this->SaveOption('LicenseKey', '');
				$this->SaveOption('LicenseLastCheck', time());
				$this->SaveOption('LicenseStatus', 1);
				return;
			}

			if(empty($WPWLEmail)) {
				$WPWLEmail = 'Aliens!!!'; // set dummy value, just bear with me for now
			}

			$LicenseStatus = $this->GetOption('LicenseStatus');
			$Retries = $this->GetOption('LicenseRets', true, true) + 0;

			$this->isBetaTester = $WPWLEmail == 'beta@wishlistproducts.com';
			if ($this->isBetaTester) {
				add_action('admin_notices', array(&$this, 'BetaTester'));
				add_action('the_content', array(&$this, 'BetaTester'));
			}
			$WPWLLast = $this->GetOption('LicenseLastCheck');
			$WPWLPID = $this->ProductSKU;
			$WPWLCheck = md5("{$WPWLKey}_{$WPWLPID}_" . ($WPWLURL = strtolower(get_bloginfo('url'))));
			$WPWLKeyAction = wlm_arrval($_POST, 'wordpress_wishlist_deactivate') == $WPWLPID ? 'deactivate' : 'activate';
			$WPWLTime = time();
			$Month = 60 * 60 * 24 * 30;

			if ( empty($WPWLKey) && empty($WPWLEmail) && $WPWLKeyAction != 'deactivate' ) {
				$this->DeleteOption( 'LicenseKey', 'LicesneStatus' );
				return;
			}
			if ($WPWLTime - $Month > $WPWLLast || $WPWLKeyAction == 'deactivate') {

				$urls = explode(',', self::ActivationURLs);
				$urlargs = array(
					'',
					'',
					urlencode($WPWLKey),
					urlencode($WPWLPID),
					urlencode($WPWLCheck),
					urlencode($WPWLEmail),
					urlencode($WPWLURL),
					urlencode($WPWLKeyAction),
					urlencode($this->Version)
				);
				foreach ($urls AS &$url) {
					$urlargs[0] = 'http://%s/activ8.php?key=%s&pid=%d&check=%s&email=%s&url=%s&%s=1&ver=%s&json=1';
					$urlargs[1] = $url;
					$url = call_user_func_array('sprintf', $urlargs);
				}

				$WPWLStatus = $WPWLCheckResponse = 0;
				if ($WPWLKeyAction == 'deactivate' OR (!empty($WPWLKey) && !empty($WPWLEmail) && trim($WPWLKey) != '' && trim($WPWLEmail) != '')) {
					$WPWLStatus = $this->ReadURL($urls, 10);
					if(false !== $WPWLStatus) {
						$WPWLResult = json_decode($WPWLStatus);
						$WPWLStatus = $WPWLCheckResponse = $WPWLResult->activated;
						if($WPWLStatus) {
							$this->SaveOption('LicenseEmail', $WPWLResult->email); // save email returned from activation
							$this->SaveOption('LicenseSubscription', !empty($WPWLResult->subscription));
							$this->SaveOption('LicenseExpiration', $WPWLResult->renewal_date);
						} else {
							$WPWLStatus = $WPWLCheckResponse = $WPWLResult->msg;
						}
					}

				}

				if ($WPWLStatus === false) {
					if ($Retries >= self::ActivationMaxRetries || $LicenseStatus != 1) {
						$WPWLStatus = $WPWLCheckResponse = 'Unable to contact License Activation Server. <a href="http://wlplink.com/go/activation" target="_blank">Click here for more info.</a>';
					} else {
						$this->SaveOption('LicenseRets', $Retries + 1, true);
						$WPWLStatus = $this->GetOption('LicenseStatus');

						// set the message as a transient
						$msg = '<div class="error fade"><p>';
						$msg .= __('Warning: Unable to contact License Activation Server. We will keep on trying. <a href="http://wlplink.com/go/activation" target="_blank">Click here for more info.</a>', 'wishlist-member');
						$msg .= '</p></div>';
						set_transient('wlm_serverunavailable', $msg, 60 * 60 * 12);
					}

					//staggered rechecks
					//if there is an error with wlm servers, check after an hour
					//so that we won't keep making requests
					$Month = 60 * 60 * 24 * 30;
					$checkafter = 60 * 60 * 24 * 7;
					//For testing check after a minute
					//$checkafter = 60;
					$this->SaveOption('LicenseLastCheck', $WPWLTime - $Month + ($checkafter));
				} else {
					$this->SaveOption('LicenseRets', 0, true);
					$this->SaveOption('LicenseLastCheck', $WPWLTime);
					$this->DeleteOption('activation_problem_notice_sent');
				}

				$WPWLStatus = trim($WPWLStatus);
				$this->SaveOption('LicenseStatus', $WPWLStatus);

				if ($WPWLKeyAction == 'deactivate') {
					$this->DeleteOption( 'LicenseRets', 'LicenseStatus', 'LicenseSubscription', 'LicenseExpiration', 'LicenseKey', 'LicenseLastCheck' );
				}
			}

			$this->WPWLCheckResponse = (isset($WPWLCheckResponse) ? $WPWLCheckResponse : '');
			if ($this->GetOption('LicenseStatus') != '1') {
				$_POST['msg'] = '';
				add_action('admin_notices', array(&$this, 'WPWLKeyResponse'), 1);
			}
		}

		/**
		 * Displays the license processing status
		 */
		function WPWLKeyResponse() {
			if (!current_user_can('manage_options')) {
				return;
			}
			if (strlen($this->WPWLCheckResponse) > 1)
				echo '<div class="notice notice-error" id="message"><p><strong>' . $this->WPWLCheckResponse . '</strong></p></div>';
		}

		/**
		 * Send an email notification to the admin if license activation server cannot be reached
		 */
		function SendActivationErrorNotice() {
			if(!get_transient('last_activation_email_notice') && $this->GetOption('activation_problem_notice_sent') < 3 && $this->GetOption('send_activation_problem_notice')) {
				$this->SendMail(
					$this->GetOption('email_sender_address'),
					__('WishList Member Cannot Reach The Activation Server', 'wishlist-member'),
					sprintf(__("This message is to inform you that your installation of WishList Member on %s is currently unable to reach the WishList Products License Activation Server.  This could be caused by an adjustment made to the site URL, firewall issues, network issues, etc.

There is a 30 day grace period in place.  This means that WishList Member is still functioning but it will be unable to function on the URL once 30 days pass if WishList Member continues to be unable to reach the WishList Products License Activation Server.

Note that the system will send this email message once every 48 hours for a maximum of 3 times or until the issue is resolved.

Please visit the link below for more information on how to resolve this connection issue.

http://wlplink.com/go/activation

Thank you.", 'wishlist-member'), home_url()),
					array()
				);
				$this->SaveOption('activation_problem_notice_sent', $this->GetOption('activation_problem_notice_sent') + 1);
				set_transient('last_activation_email_notice', time(), 60*60*48); // do not send in the next 48 hours
			}
		}

		/**
		 * Returns the Query String. Pass a GET variable and that gets removed.
		 */
		function QueryString() {
			$args = func_get_args();
			$args[] = 'msg';
			$args[] = 'err';
			$get = array();
			parse_str($_SERVER['QUERY_STRING'], $querystring);
			foreach ((array) $querystring AS $key => $value)
				$get[$key] = "{$key}={$value}";
			foreach ((array) array_keys((array) $get) AS $key) {
				if (in_array($key, $args))
					unset($get[$key]);
			}
			return implode('&', $get);
		}

		/**
		 * Sets up an array of form options
		 * @param string $name of the option
		 * @param boolean $required Specifies if the option is a required option
		 */
		function Option($name = '', $required = false) {
			if ($name) {
				$this->FormOption = $name;
				$this->FormOptions[$name] = (bool) $required;
				echo $name;
			} else {
				echo $this->FormOption;
			}
		}

		/**
		 * Retrieves the value of the form option that was previously set with Option method
		 * @param boolean $return Specifies whether to return the value or just output it to the browser
		 * @param string $default Default value to display
		 * @return string The value of the option
		 */
		function OptionValue($return = false, $default = '') {
			if (wlm_arrval($_POST, 'err')) {
				$x = $_POST[$this->FormOption];
			} else {
				$x = $this->GetOption($this->FormOption);
			}
			if (!strlen($x))
				$x = $default;
			if ($return)
				return $x;
			echo htmlentities($x, ENT_QUOTES, $this->BlogCharset);
		}

		/**
		 * Outputs selected="true" to the browser if $value is equal to the value of the option that was previously set
		 * @param string $value
		 */
		function OptionSelected($value) {
			$x = $this->OptionValue(true);
			if ($x == $value)
				echo ' selected="true"';
		}

		/**
		 * Outputs checked="true" to the browser if $value is equal to the value of the option that was previously set
		 * @param string $value
		 */
		function OptionChecked($value) {
			$x = $this->OptionValue(true);
			if ($x == $value)
				echo ' checked="true"';
		}

		/**
		 * Echoes form options that were set as a comma delimited string
		 * @param boolean $html echoes form options as the value of a hidden input field with the name "WLOptions"
		 */
		function Options($html = true) {
			$value = implode(',', array_keys((array) $this->FormOptions));
			if ($html) {
				echo '<input type="hidden" name="WLOptions" value="' . $value . '" />';
			} else {
				echo $value;
			}
		}

		/**
		 * Echoes REQUIRED form options that were set as a comma delimited string
		 * @param boolean $html echoes form options as the value of a hidden input field with the name "WLRequiredOptions"
		 */
		function RequiredOptions($html = true) {
			$value = implode(',', array_keys((array) $this->FormOptions, true));
			if ($html) {
				echo '<input type="hidden" name="WLRequiredOptions" value="' . $value . '" />';
			} else {
				echo $value;
			}
		}

		/**
		 * Clears the form options array
		 */
		function ClearOptions() {
			$this->FormOptions = array();
		}

		// -----------------------------------------
		// Saves Options
		/**
		 * Saves the form options passed by POST
		 * @param boolean $showmsg whether to display the "Settings Saved" message or not
		 * @return boolean Returns false if a required field is not set
		 */
		function SaveOptions($showmsg = true) {
			foreach ((array) $_POST AS $k => $v) {
				if (!is_array($v))
					$_POST[$k] = trim(stripslashes($v));
			}
			$required = explode(',', $_POST['WLRequiredOptions']);
			foreach ((array) $required AS $req) {
				if ($req && !$_POST[$req]) {
					$_POST['err'] = __('Fields marked with an asterisk (*) are required', 'wishlist-member');
					return false;
				}
			}
			$options = explode(',', $_POST['WLOptions']);
			foreach ((array) $options AS $option) {
				$this->SaveOption($option, $_POST[$option]);
			}
			if ($showmsg)
				$_POST['msg'] = $_POST['WLSaveMessage'] ? $_POST['WLSaveMessage'] : __('Settings Saved', 'wishlist-member');
		}

		/**
		 * Retrieves an option's value
		 * @param string $option The name of the option
		 * @param boolean $dec (optional) True to decrypt the return value
		 * @param boolean $no_cache (optional) True to skip cache data
		 * @return string The option value
		 */
		function GetOption($option, $dec = null, $no_cache = null) {
			if(wlm_arrval( $_GET, 'fv' ) && $option == 'FormVersion') {
				return $_GET['fv'];
			}
			global $wpdb;
			$cache_key = $option;
			$cache_group = $this->OptionsTable;

			if (is_null($dec))
				$dec = false;
			if (is_null($no_cache))
				$no_cache = false;

			$value = ($no_cache === true) ? false : wlm_cache_get($cache_key, $cache_group);
			if ($value === false) {
				$row = $wpdb->get_row($wpdb->prepare("SELECT `option_value` FROM `{$this->OptionsTable}` WHERE `option_name`='%s' LIMIT 1", $option));
				if (!is_object($row)){
					return false;
				}
				$value = $row->option_value;

				$value = wlm_maybe_unserialize($value);

				/**
				 * Filter the option value
				 * @param mixed  $value              Option value after being processed by wlm_maybe_unserialize
				 * @param string $option             Name of the option being requested
				 * @param mixed  $row->option_value  Raw value returned by $wpdb->get_row
				 */
				$value = apply_filters( 'wishlistmember_get_option', $value, $option, $row->option_value );

				wlm_cache_set($cache_key, $value, $cache_group);
			}
			if ($dec) {
				$value = $this->WLMDecrypt($value);
			}
			return $value;
		}

		/**
		 * Deletes the option names passed as parameters
		 */
		function DeleteOption() {
			global $wpdb;
			$cache_group = $this->OptionsTable;
			$x = func_get_args();

			foreach ($x as $option) {
				$cache_key = $option;
				$wpdb->query($wpdb->prepare("DELETE FROM `{$this->OptionsTable}` WHERE `option_name`='%s'", $option));
				wlm_cache_delete($cache_key, $cache_group);
			}
		}

		/**
		 * Saves an option
		 * @param string $option Name of the option
		 * @param string $value Value of option
		 * @param $enc (default false) True to encrypt $value
		 */
		function SaveOption($option, $value, $enc = false) {
			global $wpdb;
			$cache_key = $option;
			$cache_group = $this->OptionsTable;
			if ( $enc ) {
				$value = $this->WLMEncrypt( $value );
			}

			$x = $this->GetOption( $option );
			if ( false === $x ) {
				$x = $this->AddOption( $option, $value, $enc );
				$this->OptionSaveHook( $option, $value );
				return $x ? true : false;
			} else {
				$data = array(
					'option_name' => $option,
					'option_value' => wlm_maybe_serialize( $value ),
				);
				$where = array(
					'option_name' => $option
				);
				$x = $wpdb->update( $this->OptionsTable, $data, $where );
				$this->OptionSaveHook( $option, $value );

				wlm_cache_delete( $cache_key, $cache_group );
				return $x ? true : false;
			}
		}

		/**
		 * Adds a new option. Will not add it if the option already exists.
		 * @param string $option Name of the option
		 * @param string $value Value of option
		 * @param $enc (default false) True to encrypt $value
		 */
		function AddOption($option, $value, $enc = false) {
			global $wpdb;
			$cache_key = $option;
			$cache_group = $this->OptionsTable;
			$x = $this->GetOption($option);
			if ($x === false) {
				if ($enc)
					$value = $this->WLMEncrypt($value);
				$data = array(
					'option_name' => $option,
					'option_value' => wlm_maybe_serialize($value)
				);
				$x = $wpdb->insert($this->OptionsTable, $data);
				wlm_cache_delete($cache_key, $cache_group);
			}
			return $x ? true : false;
		}

		/**
		 * Reads the content of a URL using WordPress WP_Http class if possible
		 * @param string|array $url The URL to read. If array, then each entry is checked if the previous entry fails
		 * @param int $timeout (optional) Optional timeout. defaults to 5
		 * @param bool $file_get_contents_fallback (optional) true to fallback to using file_get_contents if WP_Http fails. defaults to false
		 * @return mixed FALSE on Error or the Content of the URL that was read
		 */
		function ReadURL($url, $timeout = null, $file_get_contents_fallback = null, $wget_fallback = null) {
			$urls = (array) $url;
			if (is_null($timeout))
				$timeout = 30;
			if (is_null($file_get_contents_fallback))
				$file_get_contents_fallback = false;
			if (is_null($wget_fallback))
				$wget_fallback = false;

			$x = false;
			foreach ($urls AS $url) {
				if (class_exists('WP_Http')) {
					$http = new WP_Http;
					$req = $http->request($url, array('timeout' => $timeout));
					$x = (is_wp_error($req) OR is_null($req) OR $req === false) ? false : ($req['response']['code'] == '200' ? $req['body'] . '' : false);
				} else {
					$file_get_contents_fallback = true;
				}

				//Andy - fix for can not load WishList member page error.
				//$old_settings = ini_get('allow_url_fopen');
				//@ini_set('allow_url_fopen',1);
				if ($x === false && ini_get('allow_url_fopen') && $file_get_contents_fallback) {
					$x = file_get_contents($url);
				}
				//@ini_set('allow_url_fopen',$old_settings);

				if ($x === false && $wget_fallback) {
					exec('wget -T ' . $timeout . ' -q -O - "' . $url . '"', $output, $error);
					if ($error) {
						$x = false;
					} else {
						$x = trim(implode("\n", $output));
					}
				}

				if ($x !== false) {
					return $x;
				}
			}
			return $x;
		}

		/**
		 * Just return False
		 * @return boolean Always False
		 */
		function ReturnFalse() {
			return false;
		}

		/**
		 * Register an external class and its methods for overloading
		 * @param string $classname Name of Class to Register
		 */
		function RegisterClass($classname) {
			if (!isset($this->imported))
				$this->imported = array();
			if (!isset($this->imported_functions))
				$this->import_functions = array();

			$import = new $classname;
//			$import_name = get_class($import);
			$import_functions = get_class_methods($import);

			array_push($this->imported, array($classname, $import));
			foreach ((array) $import_functions AS $key => $fxn_name) {
				$this->imported_functions[$fxn_name] = &$import;
			}
		}

		/**
		 * Simple obfuscation to garble some text
		 * @param string $string String to obfuscate
		 * @return string Obfucated string
		 */
		function WLMEncrypt($string) {
			$string = serialize($string);
			$hash = md5($string);
			$string = base64_encode($string);
			for ($i = 0; $i < strlen($string); $i++) {
				$c = $string[$i];
				$o = ord($c);
				$o = $o << 1;
				$string[$i] = chr($o);
			}
			return str_rot13(base64_encode($string)) . $hash;
		}

		/**
		 * Simple un-obfuscation to restore garbled text
		 * @param string $string String to un-obfuscate
		 * @return string Un-obfucated string
		 */
		function WLMDecrypt($string) {
			/* if $string is not a string then return $string, get it? */
			if (!is_string($string))
				return $string;

			$orig = $string;
			$hash = trim(substr($string, -32));

			/* no possible hash in the end, not encrypted */
			if (!preg_match('/^[a-f0-9]{32}$/', $hash)) {
				return $string;
			}

			$string = base64_decode(str_rot13(substr($string, 0, -32)));
			for ($i = 0; $i < strlen($string); $i++) {
				$c = $string[$i];
				$o = ord($c);
				$o = $o >> 1;
				$string[$i] = chr($o);
			}
			$string = base64_decode($string);

			if (md5($string) == $hash) {
				// call Decrypt again until it can no longer be decrypted
				return $this->WLMDecrypt(unserialize($string));
			} else {
				return $orig;
			}
		}

		/**
		 * Retrieves the API Key
		 * @return string API Key
		 */
		function GetAPIKey() {
			$secret = $this->GetOption('WLMAPIKey');
			if (!$secret)
				$secret = $this->GetOption('genericsecret');
			return $secret;
		}

		/**
		 * Retrieves the tooltip id
		 * @return string Tooltip
		 */
		function Tooltip($tooltpid) {
			$thisTooltip = '<a class="wishlist-tooltip help" rel="#' . $tooltpid . '" href="help"><span>&nbsp;<i class="icon-question-sign"></i> </span></a>';
			return $thisTooltip;
		}

		/**
		 * Remove bad char from string
		 * @param string $string String to be cleaned
		 * @return  Cleaned string
		 */
		function CleanInput($string) {
			$string = str_replace(array('<', '>', '"'), '', $string);
			return $string;
		}

		/**
		 * Migrate data to table
		 */
		function DataMigration() {
			ignore_user_abort(true);
			global $wpdb;
			$wlm_migrated_name = $this->PluginOptionName . '_Migrated';
			$wlm_migrated = get_option($wlm_migrated_name) + 0;

			if ($wlm_migrated != 1) {
				$wlm_migrated = 1;

				$this->CreateCoreTables();
				$this->PluginOptions = $this->WLMDecrypt(get_option($this->PluginOptionName));
				if (is_array($this->PluginOptions)) {
					foreach ($this->PluginOptions AS $name => $value) {
						if (is_string($value) && (strlen($value) > 64 || substr($name, 0, 3) == 'xxx')) {
							$autoload = 'no';
						} else {
							$autoload = 'yes';
						}
						$data = array(
							'option_name' => $name,
							'option_value' => wlm_maybe_serialize($value),
							'autoload' => $autoload
						);
						$x = $wpdb->insert($this->OptionsTable, $data);
						if ($x === false) {
							$wlm_migrated = 0;
						}
					}
				}
				update_option($wlm_migrated_name, $wlm_migrated);
			}
			return $this->DataMigrated = $wlm_migrated;
		}

		/**
		 * Create options table
		 */
		function CreateCoreTables() {
			global $wpdb;

			/*
			 * Important: This now makes use of dbDelta function
			 *
			 * Please refer to the following URL for instructions:
			 * http://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
			 *
			 * VIOLATORS OF dbDelta RULES WILL BE PROSECUTED :D
			 */

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$charset_collate = $wpdb->get_charset_collate();

			$table = $this->OptionsTable;
			$structure = "CREATE TABLE {$table} (
			  ID bigint(20) NOT NULL AUTO_INCREMENT,
			  option_name varchar(64) NOT NULL,
			  option_value longtext NOT NULL,
			  autoload varchar(20) NOT NULL DEFAULT 'yes',
			  PRIMARY KEY  (ID),
			  UNIQUE KEY option_name (option_name),
			  KEY autoload (autoload)
			) {$charset_collate};";
			dbDelta($structure);
			/* reload table names */
			$this->LoadTables(true);
		}

	}

}
?>
