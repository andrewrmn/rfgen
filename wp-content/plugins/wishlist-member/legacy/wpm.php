<?php
// for development purposes
// if version is 7524 then we display errors
global $wlm_globalrev;
$wlm_globalrev = '7524' == '{'.'GLOBALREV}';
if(!$wlm_globalrev) {
	@ini_set('display_errors', 'off'); // do not display errors if official package
} else {
	@ini_set('display_errors', 'on'); // do not display errors if official package
}

if (isset($_GET['wlmdebug'])) {
        if ( empty( $GLOBALS['wlm_cookies'] ))  $GLOBALS['wlm_cookies'] = new StdClass;
	setcookie('wlmdebug', $GLOBALS['wlm_cookies']->wlmdebug = (int) $_GET['wlmdebug']);
}
if (!empty($GLOBALS['wlm_cookies']->wlmdebug)) {
	define('WLMERRORREPORTING', $GLOBALS['wlm_cookies']->wlmdebug + 0);
	error_reporting(WLMERRORREPORTING);
} else {
	if(isset($GLOBALS['wlm_cookies']->wlmdebug)) {
		setcookie('wlmdebug', '');
	}
	/*
	 * From now on we want to display error messages that needs to be fixed
	 * For now, we include WARNINGS but we want those taken care of as well
	 * in the future.  And perhaps even NOTICES as well.  But for now, we just
	 * stick with the very important ERRORS.
	 */
	$error_reporting = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR;
	if ((int) phpversion() >= 5) {
		$error_reporting = $error_reporting | E_RECOVERABLE_ERROR;
	}
	if ((defined('WP_DEBUG') && WP_DEBUG) || $wlm_globalrev) {
		$error_reporting = $error_reporting | E_WARNING; //|E_STRICT;
		if (defined('E_DEPRECATED')) {
			$error_reporting = $error_reporting | E_DEPRECATED;
		}
		$_GET['wlmdebug'] = $error_reporting;
	}
	if((defined('WP_DEBUG') && WP_DEBUG) || !function_exists('ini_set') || $wlm_globalrev) {
		define('WLMERRORREPORTING', $error_reporting);
		error_reporting(WLMERRORREPORTING);
	}
}

require_once(dirname(__FILE__) . '/core/Functions.php');
// require_once(dirname(__FILE__) . '/core/WishListMemberCache.php');
require_once(dirname(__FILE__) . '/core/WishlistAPIQueue.php');
require_once(dirname(__FILE__) . '/core/WishListEmailBroadcast.php');
require_once(dirname(__FILE__) . '/core/Class.php');
require_once(dirname(__FILE__) . '/core/WLMDB.php');
require_once(dirname(__FILE__) . '/core/PluginMethods.php');
require_once(dirname(__FILE__) . '/core/WishListWidget.php');
require_once(dirname(__FILE__) . '/core/WishListAcl.php');
require_once(dirname(__FILE__) . '/core/WishlistDebug.php');
require_once(dirname(__FILE__) . '/core/api-helper/functions.php');
require_once(dirname(__FILE__) . '/core/TinyMCEPlugin.php');
require_once(dirname(__FILE__) . '/core/WishListXhr.php');
require_once(dirname(__FILE__) . '/core/ContentControl.php');
require_once(dirname(__FILE__) . '/core/NavMenu.php');

// -----------------------------------------
// Our plugin class
if (!class_exists('WishListMember')) {

	class WishListMember extends WishListMemberPluginMethods {

		var $extensions;
		var $wp_upload_path = '';
		var $wp_upload_path_relative = '';
		var $access_control = null;

		var $integration_errors = array();
		var $active_integration_indicators = array();

		var $content_control = null;

		// -----------------------------------------
		// Constructor call
		function __construct() {
			$x = func_get_args();
			$this->GMT = get_option('gmt_offset') * 3600;

			$this->Constructor(__FILE__, $x[0], $x[1], $x[2], $x[3]);

			$extensions = glob($this->pluginDir . '/extensions/*.php');
			foreach ((array) $extensions AS $k => $ex) {
				if (basename($ex) == 'api.php') {
					unset($extensions[$k]);
				}
			}
			sort($extensions);
			$this->extensions = $extensions;

			// constant
			define('WLMDEFAULTEMAILPERHOUR', '100');
			define('WLMDEFAULTEMAILPERMINUTE', '30');
			define('WLMMEMORYALLOCATION', '128M');
			define('WLMUNSUBKEY', 'ffa4017f6494a6637ca2636031d29eb7');
			define('WLMREGCOOKIESECRET', 'z4tyh(*&^%tghjgyu#$RFGYUnbh9654rtyhg89ingt54');
			//make sure the value is set. if not, direct level reg won't work
			$wlmregcookietimeout = $this->GetOption('reg_cookie_timeout') ? $this->GetOption('reg_cookie_timeout') : 600;
			define('WLMREGCOOKIETIMEOUT', $wlmregcookietimeout);
			define('WLMREGISTERURL', home_url ( '?/register' ) );

			define('DUPLICATEPOST_TIMEOUT', 3600); // we block duplicate POSTS for one hour
			define('WLM_BACKUP_PATH', WP_CONTENT_DIR . '/wishlist-backup/wishlist-member/');

			if (substr($this->Version, -11) == '7524' && !defined('WLMEMBER_EXPERIMENTAL')) {
				define('WLMEMBER_EXPERIMENTAL', 1);
			}

			// the WP upload path;
			$folder = str_replace(ABSPATH, '', get_option('upload_path'));

			if ($folder == '') {
				$folder = 'wp-content/uploads';
			}

			$this->wp_upload_path_relative = $folder;
			$this->wp_upload_path = ABSPATH . $folder;
		}

		// -----------------------------------------
		// Plugin activation
		function Activate() {
			global $wpdb;

			$this->CoreActivate();

			/* create WishList Member DB Tables */
			$this->CreateWLMDBTables();

			/* This is where you place code that runs on plugin activation */

			/* load all initial values */
			require($this->pluginDir . '/core/InitialValues.php');
			if(is_array($WishListMemberInitialData)) {
				foreach ($WishListMemberInitialData AS $key => $value) {
					$this->AddOption($key, $value);
				}
			}
			include_once($this->pluginDir . '/core/OldValues.php');
			if(is_array($WishListMemberOldInitialValues)) {
				foreach($WishListMemberOldInitialValues AS $key => $values) {
					foreach((array) $values AS $value) {
						if(strtolower(preg_replace('/\s/', '', $this->GetOption($key))) == strtolower(preg_replace('/\s/', '', $value))) {
							$this->SaveOption($key, $WishListMemberInitialData[$key]);
						}
					}
				}
			}

			// update lostinfo email subject
			if(!$this->GetOption('lostinfo_email_subject_spam_fix_re') && $this->GetOption('lostinfo_email_subject') == 'RE: Your membership login info') {
				$this->SaveOption('lostinfo_email_subject', $WishListMemberInitialData['lostinfo_email_subject']);
				$this->SaveOption('lostinfo_email_subject_spam_fix_re', 1);
			}

			$apikey = $this->GetOption('genericsecret');
			if (empty($apikey)) {
				$apikey = wlm_generate_password( 50, false );
			}

			$this->AddOption('WLMAPIKey', $apikey);

			/* set email sender information */
			// $user = $this->Get_UserData(1);//causes error
			$user = new WP_User(1);
			if ( $user ) {
				$name = trim($user->first_name . ' ' . $user->last_name);
				if (!$name) {
					$name = $user->display_name;
				}
				if (!$name) {
					$name = $user->user_nicename;
				}
				if (!$name) {
					$name = $user->user_login;
				}
				$this->AddOption('email_sender_name', $name);
				$this->AddOption('email_sender_address', $user->user_email);
				$this->AddOption('newmembernotice_email_recipient', $user->user_email);
			}

			/* add file protection htaccess */
			$this->FileProtectHtaccess(!($this->GetOption('file_protection') == 1));

			$wpm_levels = $this->GetOption('wpm_levels');
			/* membership levels cleanup */
			if( is_array( $wpm_levels ) && count( $wpm_levels ) ) {
				foreach ($wpm_levels AS $key => $level) {
					/* add slugs to membership levels that don't have slugs */
					if (empty($level['slug'])) {
						$level['slug'] = $this->SanitizeString($level['name']);
					}

					/*
					turn off sequential upgrade for levels that match any of the ff:
					- no upgrade method specified
					- no upgrade to specified and method is not remove
					- have 0-day moves
					*/
					if (
						// no upgrade method at all
						empty( $level['upgradeMethod'] )
						// no upgrade destination and method is not REMOVE
						|| ( empty( $level['upgradeTo'] ) && $level['upgradeMethod'] != 'REMOVE' )
						//  0-Day Moves
						|| ( $level['upgradeMethod'] == 'MOVE' && !( (int) $level['upgradeAfter'] ) && empty( $level['upgradeSchedule'] ) )
					) {
						$level['upgradeMethod'] = '0';
						$level['upgradeTo'] = '0';
						$level['upgradeAfter'] = '0';
					}

					/* Migrate Add To Feature to Level Actions */
					if ( (isset($level['addToLevel']) && is_array($level['addToLevel']) && count($level['addToLevel']) > 0 ) ) {
						$data = [
							"level_action_event" => "added",
							"level_action_method" => "add",
							"action_levels" => array_keys($level['addToLevel']),
							"inheritparent" => isset($level['inheritparent']) ? $level['inheritparent'] : 0,
							"sched_toggle" => "after",
							"sched_after_term" => "0",
							"sched_after_period" => "days"
						];
						$this->LevelOptions->save_option($key,'scheduled_action',$data);
						$this->SaveOption('addto_feature_moved', 1 );
					}
					if ( (isset($level['removeFromLevel']) && is_array($level['removeFromLevel']) && count($level['removeFromLevel']) > 0 ) ) {
						$data = [
							"level_action_event" => "added",
							"level_action_method" => "remove",
							"action_levels" => array_keys($level['removeFromLevel']),
							"sched_toggle" => "after",
							"sched_after_term" => "0",
							"sched_after_period" => "days"
						];
						$this->LevelOptions->save_option($key,'scheduled_action',$data);
						$this->SaveOption('addto_feature_moved', 1 );
					}
					//lets remove Add To Level feature data
					unset($level['addToLevel']);
					unset($level['removeFromLevel']);

					$wpm_levels[$key] = $level;
				}
			} else {
				$wpm_levels = array();
			}
			$this->SaveOption('wpm_levels', $wpm_levels);

			// default login limit error
			if(trim($this->GetOption('login_limit_error')) === '') {
				$this->SaveOption('login_limit_error', $WishListMemberInitialData['login_limit_error']);
			}

			// default minimum password length
			if(trim($this->GetOption('min_passlength')) === '') {
				$this->SaveOption('min_passlength', $WishListMemberInitialData['min_passlength']);
			}

			/* Sync Membership Content */
			$this->SyncContent();

			/* migrate old cydec (qpp) stuff to new cydec. qpp is now a separate deal */
			if ($this->GetOption('cydec_migrated') != 1) {
				if ($this->AddOption('cydecthankyou', $this->GetOption('qppthankyou'))) {
					$this->DeleteOption('qppthankyou');
				}

				if ($this->AddOption('cydecsecret', $this->GetOption('qppsecret'))) {
					$this->DeleteOption('qppsecret');
				}

				if ($this->GetOption('lastcartviewed') == 'qpp') {
					$this->SaveOption('lastcartviewed', 'cydec');
				}

				$wpdb->query("UPDATE `{$this->Tables->userlevel_options}` SET `option_value`=REPLACE(`option_value`,'QPP','CYDEC') WHERE `option_name`='transaction_id' AND `option_value` LIKE 'QPP\_%'");

				$this->SaveOption('cydec_migrated', 1);
			}

			$this->RemoveCronHooks();
			if (!empty($GLOBALS['wp_rewrite'])) {
				if(function_exists('apache_get_modules')) {
					$GLOBALS['wp_rewrite']->flush_rules();
				}
			}

			/* migrate file protection settings to table */
			$this->migrate_file_protection();

			/* migrate folder protection settings */
			$this->FolderProtectionMigrate(); // really old to old migration
			$this->migrate_folder_protection(); // old to new migration

			// Migrate old widget if active to new one that uses Class
			$this->MigrateWidget();

			#fix for the 7month activation
			#set to automatically re-activate after 7 days
			$Month = 60 * 60 * 24 * 30;
			$checkafter = 60 * 60 * 24 * 7;
			$this->SaveOption('LicenseLastCheck', $WPWLTime - $Month + ($checkafter));

			// migrate data for scheduled add, move and remove to new format
	      	$this->MigrateScheduledLevelsMeta();

			/*
			 * we clear xxxssapxxx% entries in the database
			 * removed in WLM 2.8 to prevent security issues
			 */
			$wpdb->query("DELETE FROM `{$this->Tables->options}` WHERE `option_name` LIKE 'xxxssapxxx%'");

			// include($this->pluginDir . '/lib/integration.shoppingcarts.php');
			// $this->AddOption('ActiveShoppingCarts', array_keys($wishlist_member_shopping_carts));
		}

		// -----------------------------------------
		// Plugin Deactivation
		function Deactivate() {
			//$this->Backup_Generate();
			// we delete magic page
			wp_delete_post($this->MagicPage(false), true);
			// remove file protection htaccess
			$this->FileProtectHtaccess(true);
			// remove the cron schedule. Glen Barnhardt 4/16/2010
			$this->RemoveCronHooks();
		}

		function HelpImproveNotification() {
			if ( ! is_admin() ) { return; }

			if ( isset( $_GET["helpimprove"] ) ) {
				if ( $_GET["helpimprove"] == 1 ) {
					$info_to_send = array(
					  "send_wlmversion"=>"on",
					  "send_phpversion"=>"on",
					  "send_apachemod"=>"on",
					  "send_webserver"=>"on",
					  "send_language"=>"on",
					  "send_apiused"=>"on",
					  "send_payment"=>"on",
					  "send_autoresponder"=>"on",
					  "send_webinar"=>"on",
					  "send_nlevels"=>"on",
					  "send_nmembers"=>"on",
					  "send_sequential"=>"on",
					  "send_customreg"=>"on"
					);
					$this->SaveOption('WLMSiteTracking',wlm_maybe_serialize($info_to_send));
					$this->SaveOption('show_helpimprove', 1);
					echo "<div class='updated fade'>" . __('<p>Thank You for helping us improve our product.</p>', 'wishlist-member') . "</div>";
				} else {
					$this->SaveOption('show_helpimprove', 1);
				}
			}

// 			$show_helpimprove = $this->GetOption('show_helpimprove');

// 			if ( $show_helpimprove === false ) {
// 					$yes = esc_url(add_query_arg( 'helpimprove', '1' ));
// 					$no = esc_url(add_query_arg( 'helpimprove', '0' ));
// 				echo <<<sc
// 					<div class='update-nag'>
// 						Do you want help improve WishList Member by sending anonymous usage statistics to our servers? &nbsp;
// 						<a href="{$yes}">Yes</a>&nbsp;|&nbsp;<a href="{$no}">No</a>
// 					</div>
// sc;
			// }
		}

		function WLMCronSchedules( $schedules ) {
		    $schedules['wlm_minute'] = array(
		        'interval' => 60,
		        'display'  => __( 'Every Minute (added by WishList Member)', 'wishlist-member'),
		    );
		    $schedules['wlm_15minutes'] = array(
		        'interval' => 900,
		        'display'  => __( 'Every 15 Minute (added by WishList Member)', 'wishlist-member'),
		    );
		    //add other intervals here
		    return $schedules;
		}

		function RemoveCronHooks() {
			$hooks = apply_filters( 'wishlistmember_remove_cron_hooks', array(
				'wishlistmember_eway_sync',
				'wishlistmember_1shoppingcart_check_orders_status',
				'wishlistmember_1shoppingcart_get_new_orders_detail',
				'wishlistmember_1shoppingcart_process_orders',
				'wishlistmember_1shoppingcart_update_orders_id',
				'wishlistmember_api_queue',
				'wishlistmember_arb_sync',
				'wishlistmember_attachments_load',
				'wishlistmember_check_level_cancelations',
				'wishlistmember_check_scheduled_cancelations',
				'wishlistmember_email_queue',
				'wishlistmember_import_queue',
				'wishlistmember_backup_queue',
				'wishlistmember_expring_members_notification',
				'wishlistmember_ifs_sync',
				'wishlistmember_registration_notification',
				'wishlistmember_email_confirmation_reminders',
				'wishlistmember_run_scheduled_user_levels',
				'wishlistmember_run_user_level_actions',
				'wishlistmember_syncmembership_count',
				'wishlistmember_unsubscribe_expired',
				'wishlistmember_migrate_file_protection',
			) );
			$scheds = (array) get_option('cron');
			foreach ($scheds AS $sched) {
				if (is_array($sched)) {
					foreach (array_keys($sched) AS $hook) {
						if (substr($hook, 0, 15) == 'wishlistmember_') {
							$hooks[] = $hook;
						}
					}
				}
			}
			$hooks = array_unique($hooks);

			foreach ($hooks AS $hook) {
				wp_clear_scheduled_hook($hook);
			}
		}

		// -----------------------------------------
		// Admin Head
		function AdminHead() {
			if (!(current_user_can('manage_posts') )) {
				echo "<style type=\"text/css\">\n\n/* WishList Member */\ndivul#dashmenu{ display:none; }\n#wphead{ border-top-width:2px; }\n#screen-meta a.show-settings{display:none;}\n</style>\n";
			}
		}

		function ErrorHandler($errno, $errmsg, $errfile, $errline) {
			static $errcodes;

			if (!isset($errcodes)) {
				$errcodes = array(
					E_ERROR => 'Fatal run-time error',
					E_WARNING => 'Run-time warning',
					E_PARSE => 'Compile-time parse error',
					E_NOTICE => 'Run-time notice',
					E_CORE_ERROR => 'Fatal initial startup error',
					E_CORE_WARNING => 'Initial startup warning',
					E_COMPILE_ERROR => 'Fatal compile-time error',
					E_COMPILE_WARNING => 'Compile-time warnings',
					E_USER_ERROR => 'User-generated error',
					E_USER_WARNING => 'User-generated warning',
					E_USER_NOTICE => 'User-generated notice',
					E_STRICT => 'E_STRICT error',
					E_RECOVERABLE_ERROR => 'Catchable fatal error',
					E_DEPRECATED => 'E_DEPRECATED error',
					E_USER_DEPRECATED => 'E_USER_DEPRECATED error'
				);
			}

			if (substr($errfile, 0, strlen($this->pluginDir)) == $this->pluginDir) {
				echo '<br />WishList Member Debug. [This is a notification for developers who are working in WordPress debug mode.]';
				if (wlm_arrval($_GET, 'wlmdebug')) {
					$code = $errcodes[$errno];
					echo "<br />{$code}<br />$errmsg<br />Location: $errfile line number $errline<br />";
				}
			}
			return false;
		}

		// -----------------------------------------
		// Init Hook
		function UnsubJavaScript() {
			echo '<script type="text/javascript">alert("';
			_e('You have been unsubscribed from our mailing list.', 'wishlist-member');
			echo '");</script>';
		}

		function ResubJavaScript() {
			echo '<script type="text/javascript">alert("';
			_e('You have been resubscribed to our mailing list.', 'wishlist-member');
			echo '");</script>';
		}

		// -----------------------------------------
		// Init Hook
		function Init() {
			// set $wlm_cookies global varibale
			$GLOBALS['wlm_cookies'] = new wlm_cookies;
			// $this->cache = new WishListMemberCache($this->PluginSlug, $this->GetOption('custom_cache_folder'));

			// process ping from HQ
			if(isset($_GET['_wlping_']) && isset($_GET['_hash_'])) {
				$this->process_wlping($_GET['_wlping_'], $_GET['_hash_']);
			}

			if($GLOBALS['pagenow'] == 'wp-login.php' && !isset($_COOKIE['wlm_login_cookie'])) {
				$_COOKIE['wlm_login_cookie'] = 'WLM Login check';
			}

			//check for access levels
			//do not allow wlm to run it's own access_protection
			//let's control it via another plugin. That is much cleane
			global $wpdb;
			if(defined('WLMERRORREPORTING'))
				set_error_handler(array(&$this, 'ErrorHandler'), WLMERRORREPORTING);

			$this->MigrateLevelData();

			// migrate data pertaining to each content's membership level
			// this prepares us for user level content
			$this->MigrateContentLevelData();



			/*
			 * Handle request for anonymous data
			 */
			if (isset($_POST['wlm_anon'])) {
				if ($this->ValidateRequestForAnonData($_POST['wlm_anon_time'], $_POST['wlm_anon_hash'])) {
					echo wlm_maybe_serialize($this->ReturnAnonymousData());
				}
				exit;
			}

			/*
			 * Short Codes
			 */
			$this->wlmshortcode = new \WishListMember\Shortcodes;

			/*
			 * Generate Transient Hash Session
			 * and Javascript Code
			 */
			if (isset($_GET['wlm_th'])) {
				list($field, $name) = explode(':', $_GET['wlm_th']);
				header("Content-type:text/javascript");
				$ckname = md5('wlm_transient_hash');
				$hash = md5($_SERVER['REMOTE_ADDR'] . microtime());
				wlm_setcookie("{$ckname}[{$hash}]", $hash, 0, '/');
				echo "<!-- \n\n";
				if ($field == 'field' && !empty($name)) {
					echo 'document.write("<input type=\'hidden\' name=\'' . $name . '\' value=\'' . $hash . '\' />");';
					echo 'document.write("<input type=\'hidden\' name=\'bn\' value=\'WishListProducts_SP\' />");';
				} else {
					echo 'var wlm_cookie_hash="' . $hash . '";';
				}
				echo "\n\n// -->";
				exit;
			}
			/*
			 * End Transient Hash Code
			 */

			$wpm_levels = (array) $this->GetOption('wpm_levels');

			// load $this->attachments with list of attachments including resized versions
			/*
			 * WP Cron Hooks
			 */
			// Sync Membership
			if (!wp_next_scheduled('wishlistmember_syncmembership_count')) {
				wp_schedule_event(time(), 'daily', 'wishlistmember_syncmembership_count');
			}

			// Send Queued Email
			if (!wp_next_scheduled('wishlistmember_email_queue')) {
				wp_schedule_event(time(), 'wlm_minute', 'wishlistmember_email_queue');
			}

			// Process Queued Import
			if (!wp_next_scheduled('wishlistmember_import_queue')) {
				wp_schedule_event(time(), 'hourly', 'wishlistmember_import_queue');
			}

			// Process Queued Import
			if (!wp_next_scheduled('wishlistmember_backup_queue')) {
				wp_schedule_event(time(), 'wlm_minute', 'wishlistmember_backup_queue');
			}

			//process api queue
			if (!wp_next_scheduled('wishlistmember_api_queue')) {
				wp_schedule_event(time(), 'hourly', 'wishlistmember_api_queue');
			}

			// Unsubscribe Expired Members
			if (!wp_next_scheduled('wishlistmember_unsubscribe_expired')) {
				wp_schedule_event(time(), 'hourly', 'wishlistmember_unsubscribe_expired');
			}

			// Schedule the cron to run the cancelling of memberships. Glen Barnhardt 4-16-2010
			if (!wp_next_scheduled('wishlistmember_check_scheduled_cancelations')) {
				wp_schedule_event(time(), 'hourly', 'wishlistmember_check_scheduled_cancelations');
			}

			// Schedule the cron to run the cancelling of waiting level cancellations. Glen Barnhardt 10-27-2010
			if (!wp_next_scheduled('wishlistmember_check_level_cancelations')) {
				wp_schedule_event(time(), 'hourly', 'wishlistmember_check_level_cancelations');
			}

			// Schedule the cron to run the notification of members with incomplete registration. Fel Jun 10-27-2010
			if (!wp_next_scheduled('wishlistmember_registration_notification')) {
				wp_schedule_event(time(), 'hourly', 'wishlistmember_registration_notification');
			}

			// Schedule the cron to run the notification of members with incomplete registration. Fel Jun 10-27-2010
			if (!wp_next_scheduled('wishlistmember_email_confirmation_reminders')) {
				wp_schedule_event(time(), 'hourly', 'wishlistmember_email_confirmation_reminders');
			}

			// Schedule the cron to run the notification for expiring members. Peter 02-20-2013
			if (!wp_next_scheduled('wishlistmember_expring_members_notification')) {
				wp_schedule_event(time(), 'daily', 'wishlistmember_expring_members_notification');
			}

			// Schedule the cron to run User Level modifications
			if (!wp_next_scheduled('wishlistmember_run_scheduled_user_levels')) {
				// schedule the event daily.
				wp_schedule_event(time(), 'hourly', 'wishlistmember_run_scheduled_user_levels');
			}

			// Schedule the cron to run User Level Actions
			if (!wp_next_scheduled('wishlistmember_run_user_level_actions')) {
				// schedule the event daily.
				wp_schedule_event(time(), 'hourly', 'wishlistmember_run_user_level_actions');
			}

			// Schedule the cron to run file protection migration
			if (!wp_next_scheduled('wishlistmember_migrate_file_protection')) {
				// schedule the event twice daily.
				wp_schedule_event(time(), 'twicedaily', 'wishlistmember_migrate_file_protection');
			}

			if (wlm_arrval($_GET, 'wlmfile')) {
				$this->FileProtectLoadAttachments();
				$this->FileProtect(wlm_arrval($_GET, 'wlmfile'));
			}
			if (wlm_arrval($_GET, 'wlmfolder')) {
				if ($this->GetOption('folder_protection') == 1) {
					$this->FolderProtect(wlm_arrval($_GET, 'wlmfolder'), wlm_arrval($_GET, 'restoffolder'));
				}
			}

			$wpm_current_user = wp_get_current_user();

			if ((isset($_GET['wlmfolderinfo']) ) && ( $wpm_current_user->caps['administrator'] )) {

				//echo "<link rel='stylesheet' type='text/css' href='". get_bloginfo('wpurl'). "/wp-admin/css/colors-fresh.css'    />";
				echo "<link rel='stylesheet' type='text/css' href='" . get_bloginfo('wpurl') . "/wp-admin/css/wp-admin.css'    />";

				/* $files=glob($_GET['wlmfolderinfo']."/*.*");
				  foreach ($files as $file) {
				  echo "$file<br>";
				  }
				 */

				// security check. we dont want display list of all files on the  server right? we make it limited only to folder protection folder even for admin
				$needle = $this->GetOption('rootOfFolders');
				// echo "<br>needle->".$needle;
				$haystack = $_GET['wlmfolderinfo'];
				// echo "<br>haystack->".$haystack;
				$pos = strpos($haystack, $needle);

				if ($pos === false) {


					// echo "<br> string needle NOT found in haystack";
					die();
				} else {

					//echo "<br>string needle found in haystack";
				}



				if ($handle = opendir(wlm_arrval($_GET, 'wlmfolderinfo'))) {
					?>
					<div style="padding-top:5px;padding-left:20px;">
						<table>
							<tr>
								<th> URL</th>
							</tr>
							<?php
							while (false !== ($file = readdir($handle))) {
								// do something with the file
								// note that '.' and '..' is returned even
								if (!( ($file == '.') || ($file == '..') || ($file == '.htaccess'))) {
									?>
									<tr>

										<td> <?php echo $_GET['wlmfolderLinkinfo']; ?>/<?php echo $file ?> </td>

									</tr>

									<?php
								}
							}
							?>
						</table>
					</div>
					<?php
					closedir($handle);
				}


				die();
			}


			if (wlm_arrval($_GET, 'clearRecentPosts')) {
				if (is_admin()) {
					$this->DeleteOption('RecentPosts');
				}
			}

			// email confirmation
			if (wlm_arrval($_GET, 'wlmconfirm')) {
				list($uid, $hash) = explode('/', $_GET['wlmconfirm'], 2);
				$user = new \WishListMember\User($uid, true);
				$levelID = $user->ConfirmByHash($hash);
				if ($levelID) {
					// send welcome email
					$userinfo = $user->UserInfo->data;

					//get first name and last name using get_user_meta as $userinfo only got the display name
					$usermeta = get_user_meta($userinfo->ID, $key, $single);

					delete_user_meta( $userinfo->ID, 'wlm_email_confirmation_reminder' );

					if($this->GetOption('auto_login_after_confirm')) {
						$this->WPMAutoLogin($uid);
						$_POST['log'] = $userinfo->user_login;
						$this->Login( $userinfo->user_login, $userinfo );
					}
				}
			}

			/* we just save the original post and get data just in case we need them later */
			$this->OrigPost = $_POST;
			$this->OrigGet = $_GET;
			/* remove unsecure information */

			unset($this->OrigPost['password']);
			unset($this->OrigGet['password']);
			unset($this->OrigPost['password1']);
			unset($this->OrigGet['password1']);
			unset($this->OrigPost['password2']);
			unset($this->OrigGet['password2']);

			/* load extensions */
			foreach ((array) $this->extensions AS $extension) {
				include_once($extension);
				$this->RegisterExtension($WLMExtension['Name'], $WLMExtension['URL'], $WLMExtension['Version'], $WLMExtension['Description'], $WLMExtension['Author'], $WLMExtension['AuthorURL'], $WLMExtension['File']);
			}

			if (strpos( urldecode($_SERVER['REQUEST_URI']), '/wlmapi/2.0/') !== false) {
				if (file_exists($this->pluginDir . '/core/API2.php')) {
					require_once('core/API2.php');
					preg_match('/\/wlmapi\/2\.0\/(xml|json|php)?\//i', urldecode($_SERVER['REQUEST_URI']), $return_type);
					$return_type = $return_type[1];
					$wlmapi = new WLMAPI2('EXTERNAL');
					switch ($wlmapi->return_type) {
						case 'XML':
							header('Content-type: text/xml');
							break;
						case 'JSON':
							header('Content-type: application/json');
							break;
						default:
							header('Content-type: text/plain');
							break;
					}

					@ob_end_clean(); // clean output buffering to make sure nothing gets sent over with our API response
					echo $wlmapi->result;

					//record API used
					$api_used = $this->GetOption("WLMAPIUsed");
					$date = date("Y-m-d");
					if ($api_used) {
						$api_used = (array) wlm_maybe_unserialize($api_used);
						if (isset($api_used["api2"]) && $api_used["api2"]["date"] == $date) {
							$request = (int) $api_used["api2"]["request"];
							$api_used["api2"]["request"] = $request + 1;
						} else {
							$arr = array("request" => 1, "date" => $date);
							$api_used["api2"] = $arr;
						}
					} else {
						$arr = array("request" => 1, "date" => $date);
						$api_used["api2"] = $arr;
					}
					$this->SaveOption('WLMAPIUsed', wlm_maybe_serialize((array) $api_used));

					exit;
				}
			}

			if (!defined('WLMCANSPAM')) {
				define('WLMCANSPAM', sprintf(__("If you no longer wish to receive communication from us:\n%1\$s=%2\$s\n\nTo update your contact information:\n%3\$s", 'wishlist-member'), get_bloginfo("url") . '/?wlmunsub', '%s', get_bloginfo('wpurl') . '/wp-admin/profile.php'));
			}

			$this->Permalink = (bool) get_option('permalink_structure'); // we get permalink status

			if (wlm_arrval($_POST, 'cookiehash'))
				@wlm_setcookie('wishlist_reg_cookie', $GLOBALS['wlm_cookies']->wishlist_reg_cookie = stripslashes(wlm_arrval($_POST, 'cookiehash')), 0, '/');

			if (wlm_arrval($_GET, 'wlmunsub')) {
				list($uid, $key) = explode('/', $_GET['wlmunsub']);
				$mykey = substr(md5($uid . WLMUNSUBKEY), 0, 10);
				$user = $this->Get_UserData($uid);
				if ($user->ID && $mykey == $key) {
					$this->Update_UserMeta($user->ID, 'wlm_unsubscribe', 1);
					if ($this->GetOption('unsub_notification') == 1) {
						$recipient_email = trim($this->GetOption('unsubscribe_notice_email_recipient')) == '' ? get_bloginfo('admin_email') : $this->GetOption('unsubscribe_notice_email_recipient');
						$this->send_email_template('admin_unsubscribe_notice', $user->ID, array(), $recipient_email);
					}

					$this->send_unsubscribe_notification_to_user( $user );

					$url = $this->UnsubscribeURL();
					if ($url) {
						header('Location:' . $url);
						exit;
					} else {
						add_action('wp_head', array(&$this, 'UnsubJavaScript'));
					}
				}
			}

			if (wlm_arrval($_GET, 'wlmresub')) {
				list($uid, $key) = explode('/', $_GET['wlmresub']);
				$mykey = substr(md5($uid . WLMUNSUBKEY), 0, 10);
				$user = $this->Get_UserData($uid);
				if ($user->ID && $mykey == $key) {
					$this->Delete_UserMeta( $user->ID, 'wlm_unsubscribe');
				}
				$url = $this->ResubscribeURL();
				if ($url) {
					header('Location:' . $url);
					exit;
				} else {
					add_action('wp_head', array(&$this, 'ResubJavaScript'));
				}
			}

			if (wlm_arrval($_GET, 'loginlimit')) {
				$GLOBALS['error'] = $this->GetOption('login_limit_error');
			}

			// process registration URL...
			$scuri = $this->RegistrationURL();

			if (wlm_arrval($_GET, 'wpm_download_sample_csv') == 1)
				$this->SampleImportCSV();

			if ($scuri) {
				// strip out trailing .php
				$scuri = preg_replace('/\.php$/i', '', $scuri);

				// match the URL with an SC Method
				$scuris = array_keys((array) $this->SCIntegrationURIs);
				foreach ((array) $scuris AS $x) {
					if ($this->GetOption($x) == $scuri) {
						$scuri = $x;
						break;
					}
				}

				// get the method name to call for the shoppingcart
				if (isset($this->SCIntegrationURIs[$scuri])) {
					$scmethod = $this->SCIntegrationURIs[$scuri];
					$_POST['WishListMemberAction'] = 'WPMRegister';
				} else {
					do_action( 'wishlistmember_paymentprovider_handler', $scuri );
					// not a valid SC Integration URI - we terminate.
					$this->CartIntegrationTerminate($scuri);
					// not a valid SC Integration URI - we redirect to homepage
					/*
					  header("Location: ".get_bloginfo('url'));
					  exit;
					 */
				}
			}



			switch (wlm_arrval($_POST, 'WishListMemberAction')) {
				case 'ResetPrivacyEmailTemplates':
					$this->reset_privacy_template();
					break;
				case 'SaveCustomRegForm':
					$this->SaveCustomRegForm();
					break;
				case 'CloneCustomRegForm':
					$this->CloneCustomRegForm(wlm_arrval($_POST, 'form_id'));
					break;
				case 'DeleteCustomRegForm':
					$this->DeleteCustomRegForm(wlm_arrval($_POST, 'form_id'));
					break;
				case 'SaveMembershipLevels':
					$this->SaveMembershipLevels();
					break;
				case 'SaveMembershipContent':
					$this->SaveMembershipContent();
					break;
				case 'SaveMembershipContentPayPerPost':
					$this->SaveMembershipContentPayPerPost();
					break;
				case 'EasyFolderProtection':
					$this->EasyFolderProtection();
					break;
				case 'FolderProtectionParentFolder':
					$this->FolderProtectionParentFolder();
					break;
				case 'SaveMembersData':
					$this->SaveMembersData();
					break;
				case 'MoveMembership':
					$this->MoveMembership();
					break;
				case 'ImportMembers':
					// $this->ImportMembers();
					$this->QueueImportMembers();
					break;
				case 'ExportMembers':
					$this->ExportMembers();
					break;
				case 'ExportMembersChunked':
					$this->ExportMembersChunked();
					break;
				case 'ExportSettingsToFile':
					$this->ExportSettingsToFile();
					break;
				// case 'CacheFolderUpdate':
				// 	$this->CacheFolderUpdate();
				// 	break;
				/* start - backup stuff */

				case 'BackupSettings':
					$this->Backup_Generate();
					break;
				case 'RestoreSettings';
					$this->Backup_Restore($_POST['SettingsName'], false);
					break;
				case 'ImportSettings';
					$this->Backup_Import(wlm_arrval($_POST, 'backup_first') == 1);
					break;
				case 'ExportSettings':
					$this->Backup_Download(wlm_arrval($_POST, 'SettingsName'));
					break;
				case 'DeleteSettings':
					$this->Backup_Delete(wlm_arrval($_POST, 'SettingsName'));
					break;
				case 'ResetSettings':
					$this->ResetSettings();
					break;
				/* end - backup stuff */
				case 'SaveSequential':
					$this->SaveSequential();
					break;
				case 'WPMRegister':
					// Added by Admin
					if (true === wlm_admin_in_admin()) {
						$wpm_errmsg = '';
						$registered = $this->WPMRegister($_POST, $wpm_errmsg);
						if ($registered) {
							$_POST = array('msg' => __('<b>New Member Added.</b>', 'wishlist-member'));
						} else {
							$_POST['notice'] = $wpm_errmsg;
						}
					} elseif ($_POST) {
						$docart = true;
						/*
						 * this is an attempt to prevent duplicate shopping cart registration posts
						 * from being processed it will definitely have its side effects but let's
						 * give it a try and see if people will complain
						 */

						if ($this->GetOption('PreventDuplicatePosts') && $scmethod) {

							// do not check for duplicate posts for PayPalPS short URL
							if(($scmethod['class'] == 'WLM_INTEGRATION_PAYPAL' && !empty($_GET['pid'])) ) {

							// do not check for duplicate posts on Stripe's action=sync
							} elseif(($scmethod['class'] == 'WLM_INTEGRATION_STRIPE' && ($_GET['stripe_action'] == 'sync')) ) {

							} else {
								$now = time();
								$recentposts = (array) $this->GetOption('RecentPosts');
								/*
								 * we now compute posthash from both $_GET and $_POST and not
								 * just from $_POST because some integrations don't send $_POST
								 * data but $_GET.
								 */
								$posthash = md5(serialize($_GET) . serialize($_POST));

								asort($recentposts);
								foreach ((array) array_keys((array) $recentposts) AS $k) {
									if ($recentposts[$k] < $now) {
										unset($recentposts[$k]);
									}
								}
								if ($recentposts[$posthash]) {
									$docart = false;
									$url = $this->DuplicatePostURL();
									if ($url == $this->RequestURL()) {
										$url = get_bloginfo('url');
									}
									header("Location: {$url}");
									exit;
								} else {
									$recentposts[$posthash] = $now + DUPLICATEPOST_TIMEOUT;
								}
								$this->SaveOption('RecentPosts', $recentposts);
							}
						}
						if ($docart) {
							// we save original $_POST to see if it will change
							$op = serialize($_POST);
							if (!class_exists($scmethod['class'])) {
								include_once($this->pluginDir . '/lib/' . $scmethod['file']);
							}
							$this->RegisterClass($scmethod['class']);
							call_user_func(array(&$this, $scmethod['method']));

							//record shopping cart used
							$shoppingcart_used = $this->GetOption("WLMShoppinCartUsed");
							$date = date("Y-m-d H:i:s");
							if ($shoppingcart_used) {
								$shoppingcart_used = (array) wlm_maybe_unserialize($shoppingcart_used);
								$shoppingcart_used[$scmethod['method']] = $date;
							} else {
								$shoppingcart_used[$scmethod['method']] = $date;
							}
							$this->SaveOption('WLMShoppinCartUsed', wlm_maybe_serialize((array) $shoppingcart_used));
							/*
							  // $_POST didn't changed - nothing happened, we redirect to homepage. This avoids 404 to be returned for the SC URIs
							  if(serialize($_POST)==$op){
							  header("Location: ".get_bloginfo('url'));
							  exit;
							  }
							 */
						}
						$this->CartIntegrationTerminate();
					}
					break;
				case 'EmailBroadcast':
					// email broadcast
					$this->EmailBroadcast();
					break;
				case 'DoMarketPlaceActions':
					// marketplace actions
					$this->DoMarketPlaceActions();
			}

			// check that each level has a reg URL specified
			$changed = false;
			foreach ((array) array_keys((array) $wpm_levels) AS $k) {
				if (!$wpm_levels[$k]['url']) {
					$wpm_levels[$k]['url'] = $this->PassGen(6);
					$changed = true;
				}
			}
			if ($changed
			)
				$this->SaveOption('wpm_levels', $wpm_levels);

			// check if all levels have expirations specified
			$unspecifiedexpiration = array();
			foreach ((array) $wpm_levels AS $level) {
				if (!wlm_arrval($level, 'expire') && !wlm_arrval($level, 'noexpire') && wlm_arrval($level, 'name')) {
					$unspecifiedexpiration[] = $level['name'];
				}
			}
			if (count($unspecifiedexpiration)) {
				$GLOBALS['unspecifiedexpiration'] = $unspecifiedexpiration;
				// add_action('admin_notices', array(&$this, 'ErrNoExpire'));
			}

			$wpm_current_user = wp_get_current_user();
			// No profile editing for members
			if ($wpm_current_user->ID && basename(dirname($_SERVER['PHP_SELF'])) == 'wp-admin' && basename($_SERVER['PHP_SELF']) == 'profile.php' && !$this->GetOption('members_can_update_info') && !$wpm_current_user->caps['administrator'] && !$this->GetOption('members_can_update_info') && !current_user_can('level_8')) {
				header('Location:' . get_bloginfo('url'));
				exit;
			}



			// Do not allow access to Dashboard for non-admins
			if ($wpm_current_user->ID && basename(dirname($_SERVER['PHP_SELF'])) . '/' . basename($_SERVER['PHP_SELF']) == 'wp-admin/index.php' && !current_user_can('edit_posts') && !current_user_can('level_8')) {
				header('Location:profile.php');
				exit;
			}

			if ($wpm_current_user->ID) {
				if(empty($GLOBALS['wlm_cookies']->wlm_user_sequential)) {
					$this->do_sequential($wpm_current_user->ID);
					$this->process_scheduled_level_actions($wpm_current_user->ID);
					wlm_setcookie('wlm_user_sequential', 1, time() + 3600, home_url('/', 'relative'));
					wlm_setcookie('wlm_user_sequential', 1, time() + 3600, site_url('/', 'relative'));
				}
			}

			// spawn cron job if requested
			if (wlm_arrval($_GET, 'wlmcron') == 1) {
				spawn_cron();
				exit;
			}

			// send registration notification by force without waiting for the cron
			if (wlm_arrval($_GET, 'regnotification') == 1) {
				$this->NotifyRegistration();
				exit;
			}

			// send registration notification by force without waiting for the cron
			if (wlm_arrval($_GET, 'emailconfirmationreminders') == 1) {
				$this->wishlistmember_email_confirmation_reminders();
				exit;
			}

			// send expiring members notification by force without waiting for the cron
			if (wlm_arrval($_GET, 'expnotification') == 1) {
				$this->ExpiringMembersNotification();
				exit;
			}

			if (wlm_arrval($_GET, 'wlmprocessapiqueues') > 0) {
				$tries = wlm_arrval($_GET, 'wlmapitries');
				$tries = $tries ? $tries:3;
				$this->ProcessApiQueue(wlm_arrval($_GET, 'wlmprocessapiqueues'), $tries);
				exit;
			}

			if (wlm_arrval($_GET, 'wlmprocessbroadcast') > 0) {
				$x = $this->SendQueuedMail();
				exit;
			}

			if (wlm_arrval($_GET, 'wlmprocessimport') > 0) {
				$x = $this->ProcessImportMembers();
				exit;
			}

			if (wlm_arrval($_GET, 'wlmprocessbackup') > 0) {
				$x = $this->ProcessBackupQueue();
				exit;
			}

			if (wlm_arrval($_GET, 'syncmembership') > 0) {
				$wpm_current_user = wp_get_current_user();
				if ( $wpm_current_user->caps['administrator'] ) {
					$this->SyncMembershipCount();
					echo "Done!";
					exit;
				}
			}

			// temporary fix for wpm_useraddress
			$this->FixUserAddress(1);

			//get term_ids for OnlyShowContentForLevel
			$this->taxonomyIds = array();

			$this->taxonomies = get_taxonomies( array( '_builtin' => false, 'hierarchical' => true ), 'names' );
			array_unshift($this->taxonomies, 'category');
			foreach ($this->taxonomies AS $taxonomy) {
				add_action($taxonomy . '_edit_form_fields', array(&$this, 'CategoryForm'));
				add_action($taxonomy . '_add_form_fields', array(&$this, 'CategoryForm'));
				add_action('create_' . $taxonomy, array(&$this, 'SaveCategory'));
				add_action('edit_' . $taxonomy, array(&$this, 'SaveCategory'));
			}
			$this->taxonomyIds = get_terms($this->taxonomies, array('fields' => 'ids', 'get' => 'all', 'orderby' => 'none'));
			// Cateogry Protection
			//error_reporting($error_reporting);
		}

		// Permanent Fix to recent comments
		// to enhance performance on large sites
		function RecentComments($comments = null, $obj = null) {
			if (is_active_widget(false, false, 'recent-comments', true) === false) {
				return $comments;
			}
			if (empty($comments)) {
				return $comments;
			}
			if (current_user_can('moderate_comments')) {
				return $comments;
			}

			global $current_user;
			$levels = $this->GetMembershipLevels($current_user->ID);
			remove_filter('the_comments', array(&$this, 'RecentComments'), 10, 2);

			// we only limit the number if no post_id is specified
			if (!$obj->query_vars['post_id']) {

				$limit = $obj->query_vars['number'];
				$obj->query_vars['number'] = 30;
			}

			$all_comments = $obj->query($obj->query_vars);

			if (!empty($current_user->ID)) {
				// Get posts/pages logged in member has access to
				$user_comments = $this->GetMembershipContent('posts', $levels);
				$user_comments = array_merge($user_comments, (array) $user_comments = $this->GetMembershipContent('pages', $levels));

				$protected_types = (array) $this->GetOption('protected_custom_post_types');
				$protected_types = is_array( $protected_types ) ? $protected_types : array();
				foreach($protected_types as $protected_type) {
					$user_comments = array_merge($user_comments, (array) $user_comments = $this->GetMembershipContent($protected_type, $levels));
				}

				$protect = $this->ProtectedIds();
				$comments = array();
				foreach ($protect AS $pc) {
					if (!in_array($pc, (array) $user_comments))
						$comments = array_merge($comments, (array) $pc);
				}

			} else {
				$protect = $this->ProtectedIds();
				$comments = array();
				foreach ($protect AS $pc) {
					$comments = array_merge($comments, (array) $pc);
				}
			}

			$the_comments = array();
			foreach ($all_comments as $c) {
				if (!in_array($c->comment_post_ID, $comments)) {
					$the_comments[] = $c;
				}
				// we only check limit if limit is set
				if (!empty($limit) && count($the_comments) >= $limit) {
					break;
				}
			}



			add_filter('the_comments', array(&$this, 'RecentComments'), 10, 2);
			return $the_comments;
		}

		function ErrNoLevels() {
			$wl = 'membershiplevels';
			if (wlm_arrval($_GET, 'wl') != $wl) {
				$addlevelurl = $this->GetMenu($wl);
				echo '<div class="error fade"><p>';
				printf(__("<strong>WishList Member Notice:</strong> No Membership Levels added yet. <a href='admin.php%1\$s'>Click here</a> to add a new membership level now.", 'wishlist-member'), $addlevelurl->URL);
				echo '</p></div>';
			}
		}

		function ErrNoExpire() {
			$wl = 'membershiplevels';
			$addlevelurl = $this->GetMenu($wl);
			$ue = $GLOBALS['unspecifiedexpiration'];
			$s = ' ';
			if (count($ue) > 1) {
				$ue[count($ue) - 1] = 'and ' . $ue[count($ue) - 1];
				$s = 's ';
			}
			$ue = str_replace(', and', ' and', '<b>' . implode(', ', $ue) . '</b>');
			echo '<div class="error fade"><p>';
			printf(__("<strong>WishList Member Notice:</strong> No expiration specified for membership level%1\$s%2\$s. <a href='admin.php%3\$s'>Click here</a> to correct this error.</strong>", 'wishlist-member'), $s, $ue, $addlevelurl->URL);
			echo '</p></div>';
		}

		function PreparePostPageOptions() {
			global $WishListMemberInstance;
			//only allow specific roles to access post/page options
			$wlmpageoptions_role_access = $this->GetOption("wlmpageoptions_role_access");
			$wlmpageoptions_role_access = $wlmpageoptions_role_access === false ? false : $wlmpageoptions_role_access;
			$wlmpageoptions_role_access = is_string( $wlmpageoptions_role_access ) ? array() : $wlmpageoptions_role_access;
			if ( is_array( $wlmpageoptions_role_access ) ) {
				$wlmpageoptions_role_access[] = "administrator";
				$wlmpageoptions_role_access = array_unique( $wlmpageoptions_role_access );
				$user = wp_get_current_user();
				$access = array_intersect ( $wlmpageoptions_role_access, (array) $user->roles );
				if ( count( $access ) <= 0 ) return false; //only roles with access can use this
			}

			$post_types = array( 'post', 'page', 'attachment' ) + get_post_types( array( '_builtin' => false ) );
			foreach ( $post_types AS $post_type ) {
				if( wlm_post_type_is_excluded( $post_type ) ) {
						continue;
					}
					add_meta_box( 'wlm_postpage_metabox', __( 'WishList Member', 'wishlist-member' ), array( &$WishListMemberInstance, 'PostPageOptions' ), $post_type );
			}
		}

		// -----------------------------------------
		// Post / Page Options Hook
		function PostPageOptions( $post = null ) {
			if( empty( $post ) ) {
				$post = $GLOBALS['post'];
			}

			if ($post->post_type == 'page') {
				$allindex = 'allpages';
				$ContentType = 'pages';
			} elseif ($post->post_type == 'post') {
				$allindex = 'allposts';
				$ContentType = 'posts';
			} else {
				$ContentType = $post->post_type;
				$allindex = 'all' . $post->post_type;
			}
			$wpm_levels = $this->GetOption('wpm_levels');
			// $wpm_access = array_flip($this->GetContentLevels($ContentType, $post->ID));
			$wpm_access = $this->GetContentLevels($ContentType, $post->ID);
			if (!$post->ID) {
				$wpm_protect = (bool) $this->GetOption('default_protect');
				$wlm_payperpost = (bool) $this->GetOption('default_ppp');
				$wlm_payperpost_free = false;
				$wlm_protection_inherit = false;
			} else {
				$wpm_protect = $this->Protect($post->ID);

				if($post->post_status == 'auto-draft')
					$wlm_payperpost = (bool) $this->GetOption('default_ppp');
				else
					$wlm_payperpost = $this->PayPerPost($post->ID);

				$wlm_payperpost_free = $this->Free_PayPerPost($post->ID);
				$wlm_inherit_protection = $this->SpecialContentLevel($post->ID, "Inherit");
				$wlm_payperpost_afterlogin = (int) $this->SpecialContentLevel( $post->ID, 'PayPerPost_AfterLogin' );
			}

			//Fix by Andy. If post is  fully new, we follow defualt protection by force.
			if ($post->post_status == 'auto-draft') {
				$wpm_protect = (bool) $this->GetOption('default_protect');
			}

			//Fix by Andy. If post is new but saved, we follow   user selected option to protect.
			if ($post->post_status == 'draft') {
				$wpm_protect = $this->Protect($post->ID) == 'Y';
			}
			//End fix

			$protection_settings = (int) $wpm_protect;
			if($wlm_inherit_protection) $protection_settings = 2;

			// grab levels and protection of parent/s
			$parent_levels = array();

			$taxonomy_names = get_taxonomies( array( '_builtin' => false ), 'names' );
			array_unshift( $taxonomy_names, 'category' );
			$taxonomies     = wp_get_object_terms( $post->ID, $taxonomy_names, array( 'fields' => 'ids' ) );

			$parent_protect = false;
			$protected_taxonomies = array();
			if(!is_wp_error($taxonomies) AND !empty($taxonomies)) {
				$parent_protect = false;
				foreach($taxonomies AS $taxonomy) {
					if( $this->CatProtected( $taxonomy ) ) {
						$parent_protect = true;
						$protected_taxonomies[] = $taxonomy;
						$parent_levels = array_merge( $parent_levels, $this->GetContentLevels('categories', $taxonomy, null, null, $immutable ) );
					}
				}
			}

			$ancestor = get_post_ancestors( $post->ID );
			if(!empty($ancestor)) {
				$parent_protect = $this->Protect( $ancestor[0] ) == 'Y';
				$parent_levels = array_merge( $parent_levels, $this->GetContentLevels( get_post_type( $ancestor[0] ), $ancestor[0], null, null, $immutable ) );
			}


			include($this->pluginDir . '/admin/post_page_options/main.php');
		}

		/**
		 * Save Post Page Options
		 * Action: wp_insert_post, edit_attachment
		 * @used-by WishListMember3_Hooks::save_postpage_settings()
		 *
		 * @param integer $pid   Post ID         @since 3.7
		 * @param object  $xpost Post Object     @since 3.7
		 */
		function SavePostPage($pid = null, $xpost = null) {

			switch (wlm_arrval($_POST, 'post_type')) {
				case 'page':
					$ContentType = 'pages';
					break;
				case 'post':
					$ContentType = 'posts';
					break;
				default:
					$ContentType = isset($_POST['post_type']) ? $_POST['post_type'] : '';
			}

			// save if set pass protection settings
			// since WishList Member 3.7
			if( $x = wlm_arrval( $_POST, 'pass_content_protection' ) ) {
				$this->SpecialContentLevel( $_POST['post_ID'], 'Pass_Content_Protection', $x );
			}

			// parent changed, find an ancestor that passes protection
			// since WishList Member 3.7
			if(
				wlm_arrval( $_POST, 'wlm_inherit_protection' ) != 'Y' && // current post does not inherit
				( !isset( $_POST['wlm_old_post_parent'] ) || !empty( $_POST['wlm_old_post_parent'] ) ) && // old post parent is not empty
				!empty( $xpost ) && $xpost->ID == wlm_arrval( $_POST, 'post_ID' ) && // $xpost is not empty $xpost is the current state of the post
				$_POST['wlm_old_post_parent'] != $xpost->post_parent // old post parent is not the same as the $xpost post parent
			) {
				$ancestors = get_post_ancestors( $_POST['post_ID'] );
				foreach( $ancestors as $ancestor ) { // find an ancestor that qualifies
					// ancestors that inherits protection are not qualified
					if( $this->SpecialContentLevel( $ancestor, 'Inherit' ) == 'Y' ) {
						continue;
					}
					if(
						$this->SpecialContentLevel( $ancestor, 'Protection' ) == 'Y' && // ancestor must be protected
						$this->SpecialContentLevel( $ancestor, 'Pass_Content_Protection' ) == 'Y' // and ancestor must be set to pass protection
					) {
						$_POST['wlm_inherit_protection'] = 'Y'; // set inheritance to true and let protection inheritance take care of the rest
					}
					break;
				}
			}

			if (wlm_arrval($_POST, 'wpm_protect') OR wlm_arrval($_POST,'wlm_inherit_protection')) {
				$this->PayPerPost($_POST['post_ID'], $_POST['wlm_payperpost']);
				$this->Free_PayPerPost($_POST['post_ID'], $_POST['wlm_payperpost_free']);
				$this->SpecialContentLevel( $_POST['post_ID'], 'PayPerPost_AfterLogin', $_POST['wlm_payperpost_afterlogin'] );

				// user post
				// $user_post_access = isset($_POST['user_post_access']) ? $_POST['user_post_access'] : '';
				// $remove_user_post_access = isset($_POST['remove_user_post_access']) ? $_POST['remove_user_post_access'] : '';

				// $user_post = (array) $user_post_access;
				// $remove_user_post = (array) $remove_user_post_access;
				// $user_post = array_diff( (array) $user_post, (array) $remove_user_post);
				// $this->AddPostUsers($ContentType, $_POST['post_ID'], $user_post);
				// $this->RemovePostUsers($ContentType, $_POST['post_ID'], $remove_user_post);

				//specific system pages
				$option_names = array(
					"non_members_error_page_internal" => "non_members_error_page_internal_" . $_POST['post_ID'],
					"non_members_error_page" => "non_members_error_page_" . $_POST['post_ID'],
					"wrong_level_error_page_internal" => "wrong_level_error_page_internal_" . $_POST['post_ID'],
					"wrong_level_error_page" => "wrong_level_error_page_" . $_POST['post_ID'],
					"membership_cancelled_internal" => "membership_cancelled_internal_" . $_POST['post_ID'],
					"membership_cancelled" => "membership_cancelled_" . $_POST['post_ID'],
					"membership_expired_internal" => "membership_expired_internal_" . $_POST['post_ID'],
					"membership_expired" => "membership_expired_" . $_POST['post_ID'],
					"membership_forapproval_internal" => "membership_forapproval_internal_" . $_POST['post_ID'],
					"membership_forapproval" => "membership_forapproval_" . $_POST['post_ID'],
					"membership_forconfirmation_internal" => "membership_forconfirmation_internal_" . $_POST['post_ID'],
					"membership_forconfirmation" => "membership_forconfirmation_" . $_POST['post_ID'],
				);

				// saving of specific system pages optimized by mike lopez
				foreach (array_keys($option_names) AS $index) {
					if (substr($index, -9) == '_internal') {
						continue;
					}
					$index_internal = $index . '_internal';
					$value = trim($_POST[$option_names[$index]]);
					$value_internal = (int) $_POST[$option_names[$index_internal]];
					if (empty($value_internal) && empty($value)) {
						$this->DeleteOption($option_names[$index]);
						$this->DeleteOption($option_names[$index_internal]);
					} elseif ($value_internal > 0) {
						$this->DeleteOption($option_names[$index]);
						$this->SaveOption($option_names[$index_internal], $value_internal);
					} else {
						$this->SaveOption($option_names[$index], $value);
						$this->SaveOption($option_names[$index_internal], $value_internal);
					}
				}

				// content protection
				$inherit_protection = isset($_POST['wlm_inherit_protection']) && $_POST['wlm_inherit_protection'] == 'Y';
				if($inherit_protection) {
					$this->inherit_protection($_POST['post_ID']);
				} else {
					$this->SpecialContentLevel( $_POST['post_ID'], 'Inherit', 'N' );
					$this->do_not_pass_protection = true;
					$this->Protect($_POST['post_ID'], $_POST['wpm_protect']);
					// $this->SetContentLevels($ContentType, $_POST['post_ID'], $_POST['wpm_access'] ? array_keys((array) $_POST['wpm_access']) : array());
					$this->SetContentLevels($ContentType, $_POST['post_ID'], $_POST['wpm_access'] ? $_POST['wpm_access'] : array());
				}
				$this->pass_protection( $_POST['post_ID'], $ContentType == 'categories' );
			}

			// By Andy: Commnet protection wil be off for new post
			if (wlm_arrval($_POST, '_wp_http_referer') == '/wp-admin/post-new.php') {
				$oldlevels = $this->GetContentLevels('comments', $id);
				$levels = array_unique(array_merge($oldlevels, $_POST['wpm_access'] ? array_keys((array) $_POST['wpm_access']) : array()));
				$this->SetContentLevels('comments', $_POST['post_ID'], $levels);
			}
		}

		// -----------------------------------------
		// Delete user Hook
		function DeleteUser($id) {
			$levels = $this->GetMembershipLevels($id);
			$usr = $this->Get_UserData($id);
			if ($usr->ID) {
				foreach ((array) $levels AS $level) {
					$this->ARUnsubscribe($usr->first_name, $usr->last_name, $usr->user_email, $level);
				}
			}
		}

		function DeletedUser() {
			if($this->NODELETED_USER_HOOK) return;
			$this->SyncMembership(true);
		}

		// -----------------------------------------
		// Update profile Hook
		function ProfileUpdate() {
			if (!isset($_POST['wlm_updating_profile'])) {
				return;
			}
			$wpm_current_user = wp_get_current_user();

			if (wlm_arrval($_POST, 'wlm_unsubscribe')) {
				$this->Delete_UserMeta($_POST['user_id'], 'wlm_unsubscribe');
			} else {
				$this->Update_UserMeta($_POST['user_id'], 'wlm_unsubscribe', 1);
				$this->send_unsubscribe_notification_to_user( $_POST['user_id'] );
			}

			if ($wpm_current_user->caps['administrator']) {
				if (wlm_arrval($_POST, 'wlm_reset_limit_counter')) {
					$this->Delete_UserMeta($_POST['user_id'], 'wpm_login_counter');
				}
				if (wlm_arrval($_POST, 'wpm_delete_member')) {
					if (wlm_arrval($_POST, 'user_id') > 1) {
						wp_delete_user(wlm_arrval($_POST, 'user_id'));
					}
					$msg = __('<b>User DELETED.</b>', 'wishlist-member');
					$this->DeleteUser(wlm_arrval($_POST, 'user_id'));
				} elseif (wlm_arrval($_POST, 'wpm_send_reset_email')) {
					$msg = __('<b>Reset Password Link Sent to User.</b>', 'wishlist-member');
					do_action( 'retrieve_password/wlminternal', $_POST['user_login'] );
				} else {
					$this->SetMembershipLevels($_POST['user_id'], $_POST['wpm_levels']);
					// txn ids & timestamps
					foreach ((array) $_POST['wpm_levels'] AS $k) {
						if (preg_match('#.+[-/,:]#', $_POST['lvltime'][$k])) {
							$gmt = get_option('gmt_offset');
							if ($gmt >= 0) {
								$gmt = '+' . $gmt;
							}
							$gmt = ' ' . $gmt . ' GMT';
						} else {
							$gmt = '';
						}
						$this->SetMembershipLevelTxnID($_POST['user_id'], $k, $_POST['txnid'][$k]);
						$this->UserLevelTimestamp($_POST['user_id'], $k, strtotime($_POST['lvltime'][$k] . $gmt),true);
					}
					$this->Update_UserMeta($_POST['user_id'], 'wpm_login_limit', $_POST['wpm_login_limit']);
					$msg = __('Member Profile Updated.', 'wishlist-member');
				}
			}
			// address
			foreach ((array) $_POST['wpm_useraddress'] AS $k => $v) {
				$_POST['wpm_useraddress'][$k] = stripslashes($v);
			}
			$this->Update_UserMeta($_POST['user_id'], 'wpm_useraddress', $_POST['wpm_useraddress']);

			// custom fields
			$custom_fields = explode(',', $_POST['wlm_custom_fields_profile']);
			if (!empty($custom_fields)) {
				foreach ($custom_fields AS $field) {
					$this->Update_UserMeta($_POST['user_id'], 'custom_' . $field, $_POST[$field]);
				}
			}

			// custom hidden fields
			$custom_fields = explode(',', $_POST['wlm_custom_fields_profile_hidden']);
			if (!empty($custom_fields)) {
				foreach ($custom_fields AS $field) {
					$this->Update_UserMeta($_POST['user_id'], 'custom_' . $field, $_POST[$field]);
				}
			}

			// password hint
			if ($this->GetOption('password_hinting')) {
				$this->Update_UserMeta($_POST['user_id'], 'wlm_password_hint', trim($_POST['passwordhint']));
			}

			// consent to market
			if ( $this->GetOption( 'privacy_enable_consent_to_market' ) ) {
				$this->Update_UserMeta( $_POST['user_id'], 'wlm_consent_to_market', wlm_arrval( $_POST, 'wlm_consent_to_market' ) + 0 );
			}

			// tos accepted
			if ( $this->GetOption( 'privacy_require_tos_on_registration' ) ) {
				$this->Update_UserMeta( $_POST['user_id'], 'wlm_tos_accepted', wlm_arrval( $_POST, 'wlm_tos_accepted' ) + 0 );
			}

			if (in_array($_REQUEST['wp_http_referer'], array('wlm', 'http://wlm'))) {
				$link = $this->GetMenu('members');
				header("Location:admin.php" . $link->URL . '&msg=' . urlencode($msg));
				exit;
			}
		}

		// -----------------------------------------
		// Login Hook
		function Login( $user_login, $user ) {

			// we want run seq upgrade once at login time to make sure user will be assigned to all levels.
			$sequential_individual_call_name = 'wlm_is_doing_sequential_for_' . $user->ID;
			delete_transient( $sequential_individual_call_name );

			if ($this->LoginCounter($user)) {
				// do not check if redirects if login was done via Ajax
				if(wp_doing_ajax()) {
					return;
				}

				//make sure we do logs once every minute
				//sometimes this is triggered multiple times in 1sec, esp. in admin when session ends
				$client_ip = wlm_get_client_ip();
				$transient_name = sprintf( 'done_login_logs_%d_%s', $user->ID, $client_ip );
				if ( get_transient( $transient_name ) == false ) {
					// \WishListMember\Logs::add( $user->ID, 'login', 'login', array( 'ip' => $client_ip ) );
					set_transient( $transient_name, 1, MINUTE_IN_SECONDS );
				}

				// save IP
				$this->Update_UserMeta($user->ID, 'wpm_login_ip', $_SERVER['REMOTE_ADDR']);
				$this->Update_UserMeta($user->ID, 'wpm_login_date', time());

				if ( apply_filters( 'wishlistmember_login_redirect_override', false ) ) {
					return;
				}

				do_action( 'wishlistmember_after_login');

				// If admin doesn't want WLM to handle login redirect then just return it so WP will handle the redirect instead
				if(!$this->GetOption('enable_login_redirect_override')) {
					return;
				}

				// redirect user to default WP login URL if cookies are disabled on client's browser
				if ( isset($_POST['wlm_redirect_to']) && empty( $_COOKIE ) ) {
					wp_safe_redirect( wp_login_url() );
					exit();
				}

				if(empty($_COOKIE) && empty($_GET['wlmconfirm'])) {
					return;
				}

				//admin wants to go to wp-admin?
				//wordpress always sets the redirect_to to admin url when it's empty
				if (substr($_POST['redirect_to'], 0, strlen(admin_url())) == admin_url()) {
					if ($user->caps['administrator']) {
						/*
						  header('Location:'.$_POST['redirect_to']);
						  exit();
						 */
						// instead of redirecting ourselves, we just let WP handle redirects for admins
						return;
					}
					// now let's force a wishlist-member redirect
					$_POST['redirect_to'] = 'wishlistmember';
				}

				if (!empty($_POST['wlm_redirect_to'])) {
					if (wlm_arrval($_POST, 'wlm_redirect_to') == 'wishlistmember') {
						$_POST['redirect_to'] = 'wishlistmember';
					} else {
						header('Location:' . $_POST['wlm_redirect_to']);
						exit;
					}
				}

				if (wlm_arrval($_POST, 'redirect_to') == 'wishlistmember' || !$user->caps['administrator']) {

					// if redirect_to is not wishlistmember, then we let WP handle things for us
					if(wlm_arrval($_POST, 'redirect_to') != 'wishlistmember' && !$this->GetOption('enable_login_redirect_override')) {
						return;
					}
					// get levels
					$levels = (array) array_flip($this->GetMembershipLevels($user->ID));

					// fetch all levels
					$wpm_levels = $this->GetOption('wpm_levels');

					// inject pay per post settings
					$this->InjectPPPSettings($wpm_levels, 'U-' . $user->ID);

					// ** USERS WITH NO LEVEL SHOULD USE THE GLOBAL REDIRECT ** //
					// no levels? redirect to homepage
					// if (!count($levels))
					// 	header("Location:" . get_bloginfo('url'));

					//if no level, use the global
					$url = '---';
					if ( count($levels) ) {
						// sort levels by level order and subscription timestamp
						$ts = $this->UserLevelTimestamps($user->ID);
						foreach ((array) array_keys((array) $levels) AS $level) {

							if (empty($wpm_levels[$level]['levelOrder'])) {
								$levelOrder = sprintf("%04d", 0); // This make 0 digit like  string 0000!
							} else {
								$levelOrder = sprintf("%04d", $wpm_levels[$level]['levelOrder']);
							}
							$levels[$level] = $levelOrder . ',' . $ts[$level] . ',' . $level;
						}

						asort($levels);

						// remove user level and make it the first entry to assure that it is the last option
						$ulevel = array('U-' . $user->ID => $levels['U-' . $user->ID]);
						unset($levels['U-' . $user->ID]);
						$levels = $ulevel + $levels;

						// fetch the last level in the array
						$levels = array_keys((array) $levels);
						$level = array_pop($levels);

						// $url = $wpm_levels[$level]['custom_login_redirect'] ? '' : '---';
					}

					// now let's get that after login page
					if ($url == '---') {
						// Get default after login page
						$type = $this->GetOption('after_login_type');
						if ( $type === false ) {
							$url = $this->GetOption('after_login_internal');
							$url = $url ? get_permalink($url) : $this->GetOption('after_login');
						} else {
							if ( $type == "text" ) {
								$url = $this->MagicPage() ."?sp=" ."after_login";
							} elseif ( $type == "internal" ) {
								$url = $this->GetOption('after_login_internal');
								$url = get_permalink($url);
							} else {
								$url = $this->GetOption('after_login');
							}
							if ( !$url ) $url = $this->MagicPage() ."?sp=" ."after_login";
						}
					} elseif ($url == '') {
						// per level login reg is homepage
						$url = get_bloginfo('url');
					} else {
						// get permalink of per level after login page
						$url = get_permalink($url);
					}

					// if no after login url specified then set it to homepage
					if (!$url) $url = get_bloginfo('url');

					$url = apply_filters('wlm_after_login_redirect', $url, $level, $user);

					// redirect
					header("Location:" . $url);
					exit;
				}
			}
		}

		// Gets the user ID of the current user before the wp_logout function is called.
		// Wordpress added wp_set_current_user( 0 ); on build 46265 (Oct-12-2019) which broke the after logout redirect because
		// the global $current_user is now cleared when the function Logout() is triggered.
		function GetUserIDBeforeLogout() {
			global $current_user;
			$this->wlm_current_user = $current_user->ID;
		}

		// -----------------------------------------
		// Logout Hook
		function Logout() {
			global $current_user;

			$current_user_id = ($current_user->ID) ? $current_user->ID : $this->wlm_current_user;

			/* we no longer reduce the counter on log-out to avoid abusers from
			 * gaining sequential access using the same login info by logging in
			 * then logging out sequentially
			 */
			// remove current IP from the login counter list
			// $counter=(array)$this->Get_UserMeta($current_user->ID,'wpm_login_counter');
			// unset($counter[$_SERVER['REMOTE_ADDR']]);
			// $this->Update_UserMeta($current_user->ID,'wpm_login_counter',$counter);


			// Fix on logout error when hide backend feature of Better WP Security plugin is enabled.
			// This should also fix other errors when wp_logout function is called on plugins_loaded event.

			// Skip processing the logout event if WLM doesn't have permission.
			if( !$this->GetOption('enable_logout_redirect_override') ) {
				return;
			}

			if(is_null($GLOBALS['wp_rewrite'])) {
				$wp_rewrite = new WP_Rewrite();

				$GLOBALS['wp_rewrite'] = $wp_rewrite;
			}

			if ( apply_filters( 'wishlistmember_logout_redirect_override', false ) ) {
				return;
			}

			do_action( 'wishlistmember_after_logout');

			if (
				( wlm_arrval($_REQUEST, 'redirect_to') == '' && $this->NoLogoutRedirect !== true  )
				||
				( $this->GetOption('enable_logout_redirect_override') )

				)  { // we only do the logout redirect if this is not TRUE
				// get levels
				$levels = array_flip($this->GetMembershipLevels($current_user_id));

				// now let's get that after logout page
				//
				// no levels? redirect to homepage
				if (!count($levels)) {
					$url = site_url('wp-login.php', 'login');
				} else {
					$url = '---'; // Todo,  if we want add logout redirect to each level
				}

				if ($url == '---') {
					// Get default after logout page
					$type = $this->GetOption('after_logout_type');
					if ( $type === false ) {
						$url = $this->GetOption('after_logout_internal');
						$url = $url ? get_permalink($url) : $this->GetOption('after_logout');
					} else {
						if ( $type == "text" ) {
							$url = $this->MagicPage() ."?sp=" ."after_logout";
						} elseif ( $type == "internal" ) {
							$url = $this->GetOption('after_logout_internal');
							$url = get_permalink($url);
						} else {
							$url = $this->GetOption('after_logout');
						}
						if ( !$url ) $url = $this->MagicPage() ."?sp=" ."after_logout";
					}
				} elseif ($url == '') {
					// per level logout reg is homepage
					$url = get_bloginfo('url');
				} else {
					// get permalink of per level after logout page
					$url = get_permalink($url);
				}

				// if no after logout url specified then set it to homepage
				if (!$url) $url = get_bloginfo('url');

				// remove user level and make it the first entry to assure that it is the last option
				unset($levels['U-' . $current_user_id]);
				$level = array_pop(array_keys($levels));

				$url = apply_filters('wlm_after_logout_redirect', $url, $level);

				//redirect
				header("Location:" . $url);
				exit;
			}
		}

		/**
		 * DEPRECATED: Send password reset email
		 * @param string $user_login
		 */
		function RetrievePassword( $user_login ) {
			trigger_error( __( 'This function is deprecated and will be removed in the future.', 'wishlist-member' ), E_USER_DEPRECATED );
			do_action( 'retrieve_password/wlminternal', $user_login );
		}

		// -----------------------------------------
		// Footer Hook
		function Footer() {
			// terms of service & privacy policy
			$privacy = array();
			if( $this->GetOption( 'privacy_display_tos_on_footer' ) && $this->GetOption( 'privacy_tos_page' ) ) {
				$page = get_page( $this->GetOption( 'privacy_tos_page' ) );
				$privacy[] = sprintf( '<a href="%s" target="_blank">%s</a>', get_permalink( $page->ID ), $page->post_title );
			}
			if( $this->GetOption( 'privacy_display_pp_on_footer' ) && $this->GetOption( 'privacy_pp_page' ) ) {
				$page = get_page( $this->GetOption( 'privacy_pp_page' ) );
				$privacy[] = sprintf( '<a href="%s" target="_blank">%s</a>', get_permalink( $page->ID ), $page->post_title );
			}
			if( $privacy ) {
				printf( '<p align="center">%s</p>', implode( ' | ', $privacy ) );
			}

			// show affiliate link
			if ($this->GetOption('show_linkback')) {
				$url = 'http://member.wishlistproducts.com/';
				$aff = $this->GetOption('affiliate_id');
				if ( $aff && !empty($aff)  ) {
					if ( wp_http_validate_url($aff) ) {
						$url = esc_url($aff);
					} else {
						$url = 'https://member.wishlistproducts.com/wlp.php?af=' . $aff;
					}
				}
				echo '<p align="center">' . sprintf(__('Powered by WishList Member - <a href="%1$s" target="_blank" title="Membership Software">Membership Software</a>', 'wishlist-member'), $url) . '</p>';
			}
		}

		// -----------------------------------------
		// Exclude certain pages from the list
		function ExcludePages($pages, $noerror = false) {
			$x = array_unique(array_merge($pages, array($this->MagicPage(false))));
			if (!$noerror) {
				foreach(array('non_members_error_page', 'wrong_level_error_page', 'after_registration', 'membership_cancelled', 'membership_expired', 'membership_forapproval', 'membership_forconfirmation', 'unsubscribe', 'after_logout') AS $page_type) {

					$x[] = in_array($this->GetOption($page_type . '_type'), array(false, 'internal')) ? $this->GetOption( $page_type . '_internal') : '';
				}

				//get the specific pages
				$y = $this->GetSpecificSystemPagesID();

				$x = array_merge($x, $y);


				if ($this->GetOption('exclude_pages')) {
					$wpm_levels = (array) $this->GetOption('wpm_levels');
					// exclude after reg pages for each level
					foreach ((array) $wpm_levels AS $level) {
						if( $level['custom_afterreg_redirect'] && $level['afterreg_redirect_type'] == 'page' && is_numeric( $level['afterreg_page'] ) )
							$x[] = $level['afterreg_page'];
					}
				}
			}
			return array_unique($x);
		}

		// -----------------------------------------
		// Registration: Handle 404
		function The404($content) {
			// check if 404 is a category page request
			$cat = $GLOBALS['wp_query']->query_vars['cat'];
			if ($cat) {
				// if it's a category, check if the category has posts in it...
				$cat = get_category($cat);
				if ($cat && $cat->count) {
					// if the category has posts in it then chances are we are just hiding content
					// so we get the proper redirect URL...
					$redirect = is_user_logged_in() ? $this->WrongLevelURL() : $this->NonMembersURL();
					// and redirect
					header("Location:" . $redirect);
					exit;
				}
			}
			return $content;
		}

		// -----------------------------------------
		// Registration Page Handling
		function RegistrationPage($content) {
			static $return_value;
			if ( isset( $_GET['sp'] ) ) return $content; //for wlm3 custom_error_page, see wlm3 hooks

			$postid = ''; // Run-time Notice fix
			if(isset($post))
				$postid = isset($post->ID) ? $post->ID : '';

			if(!is_null($return_value) && !is_admin() && $postid == $this->MagicPage(false)){
				return $return_value;
			}

			$posts = $content;
			if (is_page() && count($posts)) {
				$post = &$posts[0];
				if ($post->ID == $this->MagicPage(false)) {
					$reg = $_GET['reg'];
					$payperpost = $this->IsPPPLevel($reg);
					$fallback = $this->IsFallbackURL($reg);
					$forapproval = $this->IsForApprovalRegistration($reg);
					if ($fallback && array_key_exists('email', $_POST)) {
						$user = $this->Get_UserData(0, 'temp_' . md5($_POST['email']));
						if (!$user) {
							$GLOBALS['wlm_fallback_error'] = 1;
						} else {
							$redirect = $this->GetContinueRegistrationURL(wlm_arrval($_POST, 'email'));
							header('Location:' . $redirect);
							exit;
						}
					}
					$wpm_levels = $this->GetOption('wpm_levels');
					if ((!$wpm_levels[$reg] && !$payperpost && !$fallback && !$forapproval) || !$this->RegistrationCookie(false, $hash, $reg)) {
						header("Location:" . get_bloginfo('url'));
						exit;
					}
					$this->RegistrationCookie(null, $hash, $reg);
					add_filter('body_class', array( $this, 'add_wlm_registration_body_class' ) );
					$post->post_content = $this->RegContent();
					if ($payperpost) {
						$post->post_title = sprintf(__('Register for %1$s Pay Per Post', 'wishlist-member'), $payperpost->post_title);
					} elseif ($forapproval) {
						if(strrpos($forapproval["level"], "payperpost") !== false){
							$post->post_title = sprintf(__('Register %1$s Pay Per Post', 'wishlist-member'), $forapproval['level_settings']['name']);
						}else{
							$post->post_title = sprintf(__('Register for %1$s', 'wishlist-member'), $forapproval['level_settings']['name']);
						}
					} elseif ($fallback) {
						$post->post_title = sprintf(__('Enter Your Email to Continue', 'wishlist-member'), $wpm_levels[$reg]['name']);
						$post->post_content = $this->RegFallbackContent();
					} else {
						$post->post_title = sprintf(__('Register for %1$s', 'wishlist-member'), $wpm_levels[$reg]['name']);
					}
				}
			}

			unset($post); // <- very important so the loop below does not overwrite the value of the first entry in $posts

			$hasreg = false;
			foreach ($posts AS $post) {
				if (preg_match('/\[(wlm_|wlm)*register.+]/i', $post->post_content)) {
					$hasreg = true;
					break;
				}
			}

			if ($hasreg) {
				$this->force_registrationform_scripts_and_styles = true;
			}

			$return_value = $posts;

			return $posts;
		}

		function add_wlm_registration_body_class( $classes ) {
			$classes[] = 'wishlistmember-registration-form';
			return $classes;
		}

                /**
                 * Check if we are in category view.
                 * @global type $wp_query
                 * @return boolean
                 */
                function wlm_is_category(){
                    global $wp_query;

                    if (is_page() OR is_single()) {
                    	return false;
                    }


                    if ( isset($wp_query->query['category_name'] ) &&  ( $wp_query->query['category_name']!='' )  ){
                        return true;
                    }else{
                        return false;
                    }
                }

		// -----------------------------------------
		// The Heart of It All
		function Process () {
			global $wp_query;

			// get current user
			$wpm_current_user = wp_get_current_user();

			// give everything if user is admin or super admin
			if ( ( isset( $wpm_current_user->caps['administrator'] ) && $wpm_current_user->caps['administrator'] ) || array_intersect( ['administrator'], (array) $wpm_current_user->roles ) || current_user_can( 'manage_sites' ) ) {
				return;
			}

			// no protection for tag pages
			if( is_tag() ) {
				return;
			}

			$wlm_is_category = $this->wlm_is_category();

			// ensure that the requested URL is the canonical URL
			redirect_canonical();

			// Construct Full Request URL
			$wpm_request_url = $this->RequestURL();

		/**
	     * Filters the redirect URL before it is set
	     *
	     * Allow others to hook at the very beginning of the protection
	     * process and let them do their own protection checks
	     *
	     * - (string) "STOP" - do not proceed with an further protection checking
	     * - (string) "NOACCESS", "CANCELLED", "EXPIRED", "FORCONFIRMATION", "FORAPPROVAL" - redirect to appropriate WLM error page
			 * - (string) Valid URL - redirect to this URL if post ID does not match post ID of $wpm_request_url
	     *
	     * @since 3.7
	     *
	     * @param string $redirect_url    The redirect URL
	     * @param string $wpm_request_url The currently requested URL
	     */
	    $redirect_url = apply_filters( 'wishlistmember_process_protection', '', $wpm_request_url );
		/**
 	     * Generate corresponding URL if $redirect_url is any of:
 	     * NOACCESS, CANCELLED, EXPIRED, FORCONFIRMATION, FORAPPROVAL
		*/

		$is_error_page = false;
		if(in_array( $redirect_url , array( 'NOACCESS', 'CANCELLED', 'EXPIRED', 'FORCONFIRMATION', 'FORAPPROVAL' ))) {
			$is_error_page = true;
		}
			switch( $redirect_url  ) {
				case 'STOP': // don't proceed further with protection checking
					return;
					break;
				case 'NOACCESS':
					$redirect_url = is_user_logged_in() ? $this->WrongLevelURL() : $this->NonMembersURL();
					break;
				case 'CANCELLED':
					$redirect_url = $this->CancelledURL();
					break;
				case 'EXPIRED':
					$redirect_url = $this->ExpiredURL();
					break;
				case 'FORCONFIRMATION':
					$redirect_url = $this->ForConfirmationURL();
					break;
				case 'FORAPPROVAL':
					$redirect_url = $this->ForApprovalURL();
					break;
			}
			if( filter_var( $redirect_url, FILTER_VALIDATE_URL ) ) {

				// If the wishlistmember_process_protection filter returned an error page then just redirect it.
				if($is_error_page) {
					wp_redirect( $redirect_url, 302, 'WishList Member' );
					exit;
				}

				/**
				 * To prevent a redirect loop, we Stop processing if Post IDs of $redirect_url
				 * and $wpm_request_url are the same. Otherwise, we redirect to $redirect_url.
				 */
				if( url_to_postid( $redirect_url ) == url_to_postid( $wpm_request_url ) ) {
					return;
				} else {
					wp_redirect( $redirect_url, 302, 'WishList Member' );
				}
				exit;
			}

			// we're in a 404, no need to check for protection
			if( is_404() ) {
				return;
			}

			// get all levels
			$wpm_levels = (array) $this->GetOption( 'wpm_levels' );

			// process attachments
			if ( is_attachment() ) {
				$aid = $wp_query->query_vars['attachment_id'];
				if ( ! $aid && $wp_query->post->post_type == 'attachment' ) {
					$aid = $wp_query->post->ID;
				}
				$attachment = get_post( $aid );
				// no protection for attachment pages with no parent pages
				if ( ! $attachment->post_parent ) {
					// grant access, unprotected
					return;
				}

				/*
				 * check for protection inheritance from parent post and clone
				 * protection from the parent if inheritance is enabled
				 */
				$inherit = $this->SpecialContentLevel( $aid, 'Inherit' ) ? 'Y' : 'N';
				if ( $inherit == 'Y' ) {
					$type = get_post_type( $attachment->post_parent ) == 'page' ? 'pages' : 'posts';
					$this->CloneProtection( $attachment->post_parent, $aid, $type, 'posts' );
				}
			}

			// process pages and posts
			if ( is_page() OR is_single() ) {
				// grant access, WishList Member special page
				if ( in_array($wp_query->post->ID, $this->ExcludePages( array() ) ) ) {
					return;
				}

				// grant access, special URL
				$regurl = $this->make_thankyou_url( '' );
				foreach ( (array) $wpm_levels AS $wpml ) {
					if ( $regurl . $wpml['url'] == $wpm_request_url ) {
						return;
					}
				}

				// grant access, payperpost and user owns it
				if ( in_array( $wp_query->post->ID, $this->GetMembershipContent( $wp_query->post->post_type, 'U-' . $wpm_current_user->ID ) ) ) {
					return;
				}

				// Check if comment is protected
				$comment_protected = $this->SpecialContentLevel( $wp_query->post->ID, 'Protection', null, '~COMMENT' );

				// Hide comments if comment is protected and the user is not Logged in
				if( $comment_protected && ! $wpm_current_user->ID ) {
						add_filter('comments_template', array( $this, 'NoComments' ) );
				}

				/*
				 * grant access, not protected
				 * note: post becomes protected if any of the following is true
				 * - it has protect_after_more is enabled and there is a more tag in the post content
				 * - it is marked as protected
				 */
				if ( ! ( $this->GetOption( 'protect_after_more' ) && strpos( $wp_query->post->post_content, '<!--more-->' ) !== false ) && ! $this->Protect( $wp_query->post->ID ) ) {
					// grant access, unprotected
					return;
				}
			}

			// process categories
			if ( $wlm_is_category || is_tax() ) {
				if ($wlm_is_category) { // category
					$cat_ID = get_query_var( 'cat' );
          // Following may happen when "Only show content for each membership level" is set to "yes" and catagory is protected and user have no access to this catagory.
          if ( $cat_ID == '' ) {
              $redirect = is_user_logged_in() ? $this->WrongLevelURL() : $this->NonMembersURL();
							wp_redirect( $redirect, 302, 'WishList Member' );
              exit;
          }
				} else { // other taxonomy
					$cat_ID = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
					$cat_ID = $cat_ID->term_id;
				}

				// grant access, in a category but no $cat_ID or category is not protected at all
				if( ! $cat_ID || ! $this->CatProtected( $cat_ID ) ) {
					return;
				}
			}

			/*
			 * If the page being viewed is not on the list of types we protect then return it.
			 * This fixes the issue where the homepage and 404 pages are being set as protected.
			 */
			if( !$wlm_is_category && !is_tax() && !is_attachment() && !is_page() && !is_single() ) {
				return;
			}

			/*
			 * At this point we know that protection is required for the content being requsted.
			 * All we're doing at this point is checking if the user has access to the content being
			 * requsted and performing the proper redirects.
			 */

			// non member URL is default redirect URL
			$redirect_url = $this->NonMembersURL();

			if ( $wpm_current_user->ID ) {
				$content_levels = array();
				$comments_levels = array();

				// get required levels for content
				if( $wlm_is_category || is_tax() ) {
					// get required levels for categories / taxonomies
					$content_levels = $this->GetContentLevels('categories', $cat_ID);
				} else {
					// get required levels for posts, pages and attachments

					// get protected custom post types
					$protected_types = $this->GetOption( 'protected_custom_post_types' );
					if( ! is_array( $protected_types ) ) {
						$protected_types = array();
					}
					// add pages, posts and attachments to protected_post_types as we protect them by default
					$protected_types[] = 'pages';
					$protected_types[] = 'posts';
					$protected_types[] = 'attachment';

					// get current post type
					$post_type = get_post_type( $wp_query->post );

					// get required levels if we protect the post type
					if( in_array( $post_type, array_merge( $protected_types, array( 'post', 'page' ) ) ) ) {
						$content_levels = $this->GetContentLevels( $post_type, $wp_query->post->ID );
					}
					// get required levels for comments on this post
					$comments_levels = $this->GetContentLevels( 'comments', $wp_query->post->ID );
				}
				// make sure $content_levels is an array
				if( ! is_array( $content_levels) ) {
					$content_levels = array();
				}
				// make sure $comments_levels is an array
				if( ! is_array( $comments_levels ) ) {
					$comments_levels = array();
				}

				// get comment's protection status
				$comments_protected = in_array( 'Protection', $comments_levels );

				// get all of the current user's levels
				$the_user_levels = new \WishListMember\User( $wpm_current_user->ID );
				if( $the_user_levels->ID ) {
					// set defaults
					$expired_levels = $pending_levels = $unconfirmed_levels = $cancelled_levels = array();
					$active_levels = array();

					$the_user_levels = $the_user_levels->Levels;

					$wlm_is_category = $wlm_is_category || is_tax();
					$is_page_post = is_singular( array( 'page', 'post' ) );
					$is_page = is_page();

					// go through each of the user's levels that match our required levels
					foreach( $the_user_levels AS &$level ) {
						if( $level->Active ) {
							// allcategories checking
							if( isset( $wpm_levels[ $level->Level_ID ][ 'allcategories' ] ) && $wlm_is_category ) {
								// grant access, user has access to all categories and we're in a category so no further processing required
								return;
							}

							// keep track of active levels
							$active_levels[] = $level->Level_ID;

							if( $is_page_post ) {
								if( $is_page ) {
									// add level with allpages to $content_levels
									if( isset( $wpm_levels[ $level->Level_ID ][ 'allpages' ] ) ) {
										$content_levels[] = $level->Level_ID;
									}
								} else {
									// add level with allposts to $content_levels
									if( isset( $wpm_levels[ $level->Level_ID ][ 'allposts' ] ) ) {
										$content_levels[] = $level->Level_ID;
									}
								}
								// add level with allcomments to $comments_levels
								if( isset( $wpm_levels[ $level->Level_ID ][ 'allcomments' ] ) ) {
									$comments_levels[] = $level->Level_ID;
								}
							}

						}

						// status checking
						switch( true ) {
							case $level->Cancelled;
								$cancelled_levels[] = $level->Level_ID;
								break;
							case $level->Expired;
								$expired_levels[] = $level->Level_ID;
								break;
							case $level->Pending;
								$pending_levels[] = $level->Level_ID;
								break;
							case $level->UnConfirmed;
								$unconfirmed_levels[] = $level->Level_ID;
								break;
						}
					}
					unset( $level );

					// remove active levels that are not required
					$active_levels = array_intersect( $active_levels, $content_levels );

					// remove active levels that are not required for comments
					$comments_levels = array_intersect( $active_levels, (array) $comments_levels );

					if( $active_levels || ( is_page() && $allpages ) || ( is_singular( 'post' ) && $allposts ) ) {
						// grant access, we still have active levels left ( categories, posts, pages, custom post types )
						// but first we check for comment access if we're in a singular page
						if( is_singular() ) {
							if( $comments_protected && ! $comments_levels && ! $allcomments ) {
								// deny access to comments
								add_filter( 'comments_template', array( $this, 'NoComments' ) );
							}
						}
						// grant access
						return;
					} else {
						// deny access, no levels left. get $redirect_url prioritized as listed below
						switch( true ) {
							case array_intersect( $unconfirmed_levels, $content_levels );
								$redirect_url = $this->ForConfirmationURL( $redirect_url );
								break;
							case array_intersect( $pending_levels, $content_levels );
								$redirect_url = $this->ForApprovalURL( $redirect_url );
								break;
							case array_intersect( $expired_levels, $content_levels );
								$redirect_url = $this->ExpiredURL( $redirect_url );
								break;
							case array_intersect( $cancelled_levels, $content_levels );
								$redirect_url = $this->CancelledURL( $redirect_url );
								break;
							default:
								$redirect_url = $this->WrongLevelURL( $redirect_url );
						}
					}
				}
			}
			// still here? deny access
			wp_redirect( $redirect_url, 302, 'WishList Member' );
			exit;
		}

		// -----------------------------------------
		// Process Private Tags
		function TheContent($content) {
			global $current_user, $wp_query;
			$wpm_levels = (array) $this->GetOption('wpm_levels');

			/* process private tags */
			$content = $this->PrivateTags($content, $regtags);

			/* process merge codes */

			// in-page registration form
			foreach ((array) $regtags AS $level => $regtag) {
				// render the the reg form only when were supposed to
				if (preg_match_all('/\[' . $regtag . '\]/i', $content, $match)) {

					// Don't process old register shotrtcodes if configured
				    // This will reduce the number of shortcodes WLM is registering,
				    // Specially helpful with sites with large number of levels
					if($this->GetOption('disable_legacy_reg_shortcodes')) {
						if(strpos($match[0][0], 'wlm_register_'))
							continue;
					}

					$content = str_replace($match[0], $this->RegContent($level, true), $content);
				}
			}

			if (is_feed()) {
				$uid = $this->VerifyFeedKey(wlm_arrval($_GET, 'wpmfeedkey'));
				if (!$uid) {
					$pid = $wp_query->post->ID;
					if ($this->Protect($pid)) {
						$excerpt_length = apply_filters('excerpt_length', 55);
						$excerpt_more = '';

						$content = strip_tags($content);
						$content = preg_split('/[\s]/', $content);

						if (count($content) > $excerpt_length) {
							list($content) = array_chunk($content, $excerpt_length);
							$excerpt_more = apply_filters('excerpt_more', ' [...]');
						}

						$content = implode(' ', $content) . $excerpt_more;
					}
				}
			}

			return $content;
		}

		// -----------------------------------------
		// Auto insert more tag
		function TheMore($posts) {
			if (is_page() || is_single() || is_admin()) {
				return $posts;
			}

			$isfeed = is_feed();
			$authenticatedfeed = false;
			if ($isfeed && isset($_GET['wpmfeedkey'])) {
				$authenticatedfeed = $this->VerifyFeedKey(wlm_arrval($_GET, 'wpmfeedkey'));
			}

			$autoinsert = $this->GetOption('auto_insert_more');
			$protectaftermore = $this->GetOption('protect_after_more');
			$insertat = $this->GetOption('auto_insert_more_at') + 0;
			if ($insertat < 1) {
				// $insertat = 50;
				$insertat = 0;
			}

			if(!is_array($posts)) {
				return $posts;
			}

			for ($i = 0; $i < count($posts); $i++) {
				$content = trim($posts[$i]->post_content);
				$morefound = stristr($content, '<!--more-->');
				if ($morefound === false && $autoinsert) {
					$content = preg_split('/([\s<>\[\]])/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
					$tag = false;
					$wordcnt = 0;
					for ($ii = 0; $ii < count($content); $ii++) {
						$char = trim($content[$ii]);
						if ($tag === false && trim($content[$ii + 1]) != '') {
							if ($char == '<' || $char == '[') {
								$tag = $char == '<' ? '>' : ']';
							}
						} elseif ($char == $tag) {
							$tag = false;
						}
						if (!$tag && $char != '>' && $char != ']' && $char != '') {
							$wordcnt++;
						}
						if ($wordcnt >= $insertat) {
							$content[$ii].=' <!--more--> ';
							break;
						}
					}
					$content = implode('', $content);
				}
				if ($morefound || $autoinsert) {
					// if it's not an authenticated feed then we only return content before the "more" tag
					if ($isfeed && $protectaftermore && !$authenticatedfeed) {
						$content = preg_split('/<!--more-->/i', $content);
						$content = force_balance_tags($content[0]);
					}
				}
				$posts[$i]->post_content = $content;
			}
			return $posts;
		}

		// -----------------------------------------
		// Feed Links
		function FeedLink($link, $key = null) {
			if (is_null($key)) {
				$key = $this->FeedKey();
			}
			if ($key) {
				$param = 'wpmfeedkey=' . $key;
				if (!strpos($link, '?')) {
					$param = '?' . $param;
				} else {
					$param = '&' . $param;
				}
				$link.=$param;
			}
			return $link;
		}

		// -----------------------------------------
		// We want all mails sent by WordPress to have our configured sender's name and address
		// Overridden by the AR
		function MailFrom($c) {
			if(!isset($this->SendingMail)) {
				$this->SendingMail = false;
			}

			if ($this->SendingMail !== true) {
				return $c; // we don't change anything if mail's not being sent by WishList Member
			}
			if(isset($this->ARSender)) {
				if (is_array($this->ARSender)) {
					$x = $this->ARSender['email'];
				} else {
					$x = $this->GetOption('email_sender_address');
				}
			} else {
				$x = wlm_arrval( $this, 'wlm_mail_from_email' ) ?: $this->GetOption('email_sender_address');
			}
			if (!$x) {
				$x = $c;
			}

			// allow further filtering of sender email if sending a template
			if(!empty($this->email_template) && !empty($this->email_template_user_id)) {
				$x = apply_filters('wishlistmember_template_mail_from_email', $x, $this->email_template, $this->email_template_user_id);
			}
			return $x;
		}

		function MailFromName($c) {

			if(!isset($this->SendingMail)) {
				$this->SendingMail = false;
			}

			if ($this->SendingMail !== true) {
				return $c; // we don't change anything if mail's not being sent by WishList Member
			}
			if(isset($this->ARSender)) {
				if (is_array($this->ARSender)) {
					$x = $this->ARSender['name'];
				} else {
					$x = $this->GetOption('email_sender_name');
				}
			} else {
				$x = wlm_arrval( $this, 'wlm_mail_from_name' ) ?: $this->GetOption('email_sender_name');
			}
			if (!$x) {
				$x = $c;
			}

			// allow further filtering of sender name if sending a template
			if(!empty($this->email_template) && !empty($this->email_template_user_id)) {
				$x = apply_filters('wishlistmember_template_mail_from_name', $x, $this->email_template, $this->email_template_user_id);
			}
			return $x;
		}

		function MailSubject( $wp_mail ) {
			$this->MailSubject = trim( wlm_arrval( $this, 'MailSubject' ) );
			if( ! empty ($this->MailSubject ) ) {
				$wp_mail['subject'] = $this->MailSubject;
			}
			$this->MailSubject = '';
			return $wp_mail;
		}

		// -----------------------------------------
		// Hide's Prev/Next Links as per Configuration
		function OnlyShowPrevNextLinksForLevel($where) {
			global $wpdb;
			if (is_admin()) {
				return $where;
			}
			if (!$this->GetOption('only_show_content_for_level')) {
				return $where;
			}

			$id = $GLOBALS['current_user']->ID;

			if ($id) {
				if (!$GLOBALS['current_user']->caps['administrator'] || is_feed()) {
					$wpm_levels = $this->GetOption('wpm_levels');
					$levels = $this->GetMembershipLevels($id, false, true);

					// get all protected posts
					$protected = $this->ProtectedIds();

					$enabled_types = (array) $this->GetOption('protected_custom_post_types');
					$enabled_types[] = 'post';
					$enabled_types = "'" . implode("','", $enabled_types) . "'";
					$all = $wpdb->get_col("SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` IN ({$enabled_types})");
					$unp = array_diff($all, $protected);
					$ids = array_merge((array) $ids, (array) $unp);
					$allpages = $allposts = false;

					// retrieve post ids
					if ($allposts) {
						$ids = array_merge($ids, $wpdb->get_col("SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type`='post' AND `post_status` IN ('publish','private')"));
					} else {
						$ids = array_merge($ids, $x = $this->GetMembershipContent('posts', $levels));
						// foreach ((array) $levels AS $level)
						// 	$ids = array_merge($ids, $x = $this->GetMembershipContent('posts', $level));
					}

					//retrieve custom post types id
					foreach ((array) $this->GetOption('protected_custom_post_types') as $custom_type) {
						$ids = array_merge($ids, $x = $this->GetMembershipContent($custom_type, $levels));
						// foreach ($levels AS $level) {
						// 	$ids = array_merge($ids, $x = $this->GetMembershipContent($custom_type, $level));
						// }
					}

					$protected = array_diff($all, $ids);
				}
			} else {
				$protected = $this->ProtectedIds();
			}

			$protected[] = 0;
			$protected = implode(',', $protected);
			$where.=" AND p.ID NOT IN ({$protected})";
			return $where;
		}

		// -----------------------------------------
		// Hide's Content as per Configuration
		function OnlyShowContentForLevel($content) {
			global $wpdb;

			/**
			 * Filters the $content as passed by the WordPress pre_get_posts action
			 * @param \WP_Query $content
			 */
			$content = apply_filters( 'wishlistmember_only_show_content_for_level', $content );
			// stop if $content is empty
			if( empty( $content ) ) {
				return;
			}

			// if we're trying to view post or page content then just return the content to be processed by our the_content page.  this avoids 404 pages to be displayed on hidden pages.

			$pagename = isset($content->query['pagename']) ? $content->query['pagename'] : '';
			$pageid = isset($content->query['page_id']) ? $content->query['page_id'] : '';
			$name = isset($content->query['name']) ? $content->query['name'] : '';
			$p = isset($content->query['p']) ? $content->query['p'] : '';

			if ((is_single() && ($name || $p)) || (is_page() && ($pagename || $pageid)))
				return;

			$is_search = is_search();
			if ($is_search && !$this->GetOption('hide_from_search'))
				return;

			if (!is_feed() && !$this->GetOption('only_show_content_for_level'))
				return;


			$exclude_ids = $is_search ? $this->ExcludePages(array()) : array();
			// $include_ids = array();

			if (!is_admin()) {
				$id = $GLOBALS['current_user']->ID;
				if (is_feed() && isset($_GET['wpmfeedkey'])) {
					$wpmfeedkey = $_GET['wpmfeedkey'];
					$id = $this->VerifyFeedKey($wpmfeedkey);
				}
				if ($id) {
					if (!isset($GLOBALS['current_user']->caps['administrator']) || is_feed()) {
						$wpm_levels = $this->GetOption('wpm_levels');
						$levels = $this->GetMembershipLevels($id, false, true);

						// get all protected pages
						$protected = $this->ProtectedIds();
						$enabled_types = (array) $this->GetOption('protected_custom_post_types');
						$enabled_types[] = 'post';
						$enabled_types[] = 'page';
						$enabled_types[] = 'attachment';
						$enabled_types = "'" . implode("','", $enabled_types) . "'";
						$all = $wpdb->get_col("SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` IN ({$enabled_types})");
						$unp = array_diff($all, $protected);
						$ids = isset($ids) ? $ids : '';
						$ids = array_merge((array) $ids, (array) $unp);

						// do we have all posts/pages enabled for any of the member's levels?
						$allpages = $allposts = false;
						foreach ((array) $levels AS $level) {
							$allposts = $allposts | isset($wpm_levels[$level]['allposts']);
							$allpages = $allpages | isset($wpm_levels[$level]['allpages']);
						}

						// retrieve page ids
						if ($allpages) {
							$ids = array_merge($ids, $wpdb->get_col("SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type`='page' AND `post_status` IN ('publish','private')"));
						} else {
							$ids = array_merge($ids, $x = $this->GetMembershipContent('pages', $levels));
							// foreach ((array) $levels AS $level)
							// 	$ids = array_merge($ids, $x = $this->GetMembershipContent('pages', $level));
						}

						// retrieve post ids
						if ($allposts) {
							$ids = array_merge($ids, $wpdb->get_col("SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type`='post' AND `post_status` IN ('publish','private')"));
						} else {
							$ids = array_merge($ids, $x = $this->GetMembershipContent('posts', $levels));
							// foreach ((array) $levels AS $level)
							// 	$ids = array_merge($ids, $x = $this->GetMembershipContent('posts', $level));
						}

						// Retrieve attachment ids
						$ids = array_merge($ids, $x = $this->GetMembershipContent('attachments', $levels));

						//retrieve custom post types id
						foreach ((array) $this->GetOption('protected_custom_post_types') as $custom_type) {
							$ids = array_merge($ids, $x = $this->GetMembershipContent($custom_type, $levels));
							// foreach ($levels AS $level) {
							// 	$ids = array_merge($ids, $x = $this->GetMembershipContent($custom_type, $level));
							// }
						}

						$no_access_ids = array_diff($all, $ids);
						$exclude_ids = array_merge($exclude_ids, $no_access_ids);
					}
				} else {
					// public (not logged in)
					if (!is_feed() OR (is_feed() && $this->GetOption('rss_hide_protected'))) {
						$post_types = array();
						if( !empty( $content->query_vars['post_type'] ) ) {
							// if post_type is provided, use it
							$post_types = (array) $content->query_vars['post_type'];
						} elseif ( !empty( $content->tax_query->queried_terms ) ) {
							// if tax_query->queried_terms is provided, get post_types from it
							foreach( array_keys( $content->tax_query->queried_terms ) as $term ) {
								$tax = get_taxonomy( $term );
								$post_types = array_merge( $post_types, $tax->object_type );
							}
						}
						$exclude_ids = $this->ProtectedIds( $post_types );
					}
				}
			}
			if (count($exclude_ids)) {
				$exclude_ids = array_unique(array_merge($exclude_ids, (array) $content->query_vars['post__not_in']));
				$content->query_vars['post__not_in'] = $exclude_ids;
			}
			/*
			 * **** this is no longer needed ****
			  if (count($include_ids)) {
			  $include_ids = array_unique(array_merge($include_ids, (array) $content->query_vars['post__in']));
			  $content->query_vars['post__in'] = $include_ids;
			  }
			 */
		}

		function OnlyListPagesForLevel($pages) {
			if ($this->GetOption('only_show_content_for_level') && !wlm_arrval($GLOBALS['current_user']->caps, 'administrator')) {
				if ($GLOBALS['current_user']->ID) {
					$wpm_levels = $this->GetOption('wpm_levels');
					$levels = $this->GetMembershipLevels($GLOBALS['current_user']->ID, false, true);
					// is the user a member of a level that can view all pages?
					$allpages = false;
					foreach ((array) $levels AS $level) {
						$allpages = $allpages | isset($wpm_levels[$level]['allpages']);
					}
					if ($allpages
					)
						return $pages;

					// retrieve pages that the user can't view
					$protect = $this->ProtectedIds();
					$xpages = $this->GetMembershipContent('pages');
					$allowed = array();
					foreach ((array) $levels AS $level) {
						$allowed = array_merge((array) $allowed, (array) $xpages[$level]);
					}
					$allowed = array_merge((array) $allowed, (array) $this->GetMembershipContent('pages', 'U-' . $GLOBALS['current_user']->ID));
					$pages = array_merge($pages, array_diff($protect, $allowed));
				} else {
					$pages = array_merge($pages, $this->ProtectedIds());
				}

				$pages = array_unique($pages);

				//filter so that we are only excluding pages.
				//adding a lot of ID's in excludes greatly affects performance
				global $wpdb;
				$real_pages = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE `post_type`='page'");
				$pages = array_intersect($pages, $real_pages);

				$k = array_search('', $pages);
				if ($k !== false
				)
					unset($pages[$k]);
			}
			return $pages;
		}

		function OnlyListCatsForLevel($cats) {
			global $current_user;

			if ($this->GetOption('only_show_content_for_level') && !wlm_arrval($GLOBALS['current_user']->caps, 'administrator')) {
				/* if (is_category() && !defined('ONLYLISTCATS')) {
				  define('ONLYLISTCATS', 1);
				  if ($this->Permalink)
				  return $cats; // we only return full cats on first run if permalinks are set
				  } */
				//I remove this part of code to show only cats accessible by the user on the cat widget. This code allows users to see
				//cats after visiting other pages, it only prevents them from seeing it on the hompage when permalink is on.

				$wpm_levels = $this->GetOption('wpm_levels');
				$levels = $this->GetMembershipLevels($current_user->ID, false, true);

				$notallowed = (array) $this->taxonomyIds;
				$allowed = $this->GetMembershipContent('categories', $levels);

				foreach ($notallowed AS $i => $cat) {
					if (in_array($cat, $allowed) || !$this->CatProtected($cat)) {
						unset($notallowed[$i]);
					}
				}

				if (count((array) $notallowed)) {
					$notallowed[] = 0; // wp 2.8 fix?
					$notallowed = implode(',', $notallowed);
					$cats.=" AND t.term_id NOT IN ({$notallowed}) ";
				}
			}
			return $cats;
		}

		// -----------------------------------------
		// Category Protection Form
		function CategoryForm($tag) {
			$add = empty($tag->term_id);
			$tax = get_taxonomy($add ? $tag : $tag->taxonomy);
			$tax_label = $tax->labels->singular_name;
			if (!$tax_label) {
				$tax_label = $tax->labels->name;
			}

			$checked = $tag->term_id ? (int) $this->CatProtected($tag->term_id) : (int) $this->GetOption('default_protect');

			$chkyes = $checked ? 'checked="checked"' : '';
			$chkno = $checked ? '' : 'checked="checked"';

			$lbl = sprintf(__('Protect this %s?', 'wishlist-member'), $tax_label);
			$yes = __('Yes', 'wishlist-member');
			$no = __('No', 'wishlist-member');
			if ($add) {
				echo <<<STRING
				<div class="form-field">
					<label>{$lbl}</label>
					<label style="display:inline"><input style="width:auto" type="radio" name="wlmember_protect_category" {$chkyes} value="yes" /> {$yes}</label> &nbsp; <label style="display:inline"><input style="width:auto" type="radio" name="wlmember_protect_category" {$chkno} value="no" /> {$no}</label>
				</div>
STRING;
			} else {
				echo <<<STRING
				<tr class="form-field">
					<th scope="row">{$lbl}</th>
					<td><label style="display:inline"><input style="width:auto" type="radio" name="wlmember_protect_category" {$chkyes} value="yes" /> {$yes}</label> &nbsp; <label style="display:inline"><input style="width:auto" type="radio" name="wlmember_protect_category" {$chkno} value="no" /> {$no}</label></td>
				</tr>
STRING;
			}
		}

		// -----------------------------------------
		// Save Category
		function SaveCategory($id) {
			global $wpdb;
			$id = abs($id);
			switch (wlm_arrval($_POST, 'wlmember_protect_category')) {
				case 'yes':
					$this->CatProtected($id, 'Y');
					break;
				case 'no':
					$this->CatProtected($id, 'N');
					break;
			}

//			$this->SetContentLevelsDeep('category', $id, $this->GetContentLevels('categories', $id));
		}

		// -----------------------------------------
		// Edit Profile Page
		function ProfilePage() {
			global $current_user;

			$profileuser = $this->Get_UserData($GLOBALS['profileuser']->ID);
			$mlevels = $this->GetMembershipLevels($profileuser->ID);

			if ($this->access_control->current_user_can('wishlistmember3_members/manage')) {
				$custom_fields_form = $this->GetCustomRegFields();
			} else {
				$custom_fields_form = $this->GetCustomRegFields($mlevels);
			}

			// Let's remove the WishList Member address fields in the $custom_fields_form to prevent duplicates.
			foreach($custom_fields_form as $key => $c_fields_form) {
				$address_fields = array('company', 'address1', 'address2', 'city', 'state', 'zip', 'country', 'website');
				if(in_array($key, $address_fields))
					unset($custom_fields_form[$key]);
			}

			$custom_fields = implode('', $custom_fields_form);
			$custom_fields = str_replace(array('<td class="label">', '</td><td'), array('<th scope="row">', '</th><td'), $custom_fields);

			// if password hinting is enabled, display the password hint for the member
			if ($this->GetOption('password_hinting')) {
				$custom_fields .= '<tr class="li_fld field_text required">
										<th scope="row">Password Hint:</th>
										<td class="fld_div">
											<input class="fld" type="text" name="passwordhint" size="20" value="' . $profileuser->wlm_password_hint . '">
											<div class="desc"></div>
										</td>
									</tr>';
			}

			/* consent to market */
			$consent_to_market = '';
			if ( $this->GetOption( 'privacy_enable_consent_to_market' ) ) {
				$txt01 = __( 'Consent to Market', 'wishlist-member' );
				$checked = $this->Get_UserMeta( $profileuser->ID, 'wlm_consent_to_market' ) ? 'checked' : '';
				$consent_to_market = <<<STRING
				<tr valign="top">
					<th scope="row"></th>
					<td><label><input type="checkbox" name="wlm_consent_to_market" value="1" {$checked} /> {$txt01}</label></td>
				</tr>
STRING;
			}

			/* require tos on registration */
			$tos_on_registration = '';
			if ( $this->GetOption( 'privacy_require_tos_on_registration' ) ) {
				$txt01 = __( 'Terms of Service Accepted', 'wishlist-member' );
				$checked = $this->Get_UserMeta( $profileuser->ID, 'wlm_tos_accepted' ) ? 'checked' : '';
				$readonly = $checked ? 'onclick="return false;" readonly' : '';
				$tos_on_registration = <<<STRING
				<tr valign="top">
					<th scope="row"></th>
					<td><label><input type="checkbox" name="wlm_tos_accepted" value="1" {$checked} {$readonly} /> {$txt01}</label></td>
				</tr>
STRING;
			}

			/* data privacy section */
			$data_privacy = '';
			if( $consent_to_market || $tos_on_registration ) {
				$data_privacy = sprintf('<h3>%s</h3><table class="form-table">%s%s</table>', __( 'Data Privacy', 'wishlist-member' ), $consent_to_market, $tos_on_registration );
			}

			$postdata = $user_custom_fields = $this->GetUserCustomFields($profileuser->ID);
			$postdata = array_intersect_key($postdata, $custom_fields_form);

			$user_custom_fields = array_diff_key($user_custom_fields, $custom_fields_form);
			$hastos = isset($user_custom_fields['terms_of_service']);

			if (($this->access_control->current_user_can('wishlistmember3_edit_custom_fields') || $this->access_control->current_user_can('wishlistmember3_members/manage')) && $user_custom_fields) {
				foreach ($user_custom_fields AS $custom_name => $custom_value) {

					// Let's remove the WishList Member address fields as well as the firstname and lastname to avoid duplicates.
					$address_fields = array('company', 'address1', 'address2', 'city', 'state', 'zip', 'country', 'website', 'firstname', 'lastname');
					if(in_array($custom_name, $address_fields))
						continue;

					if ($custom_name != 'terms_of_service') {
						$custom_fields.='<tr><th scope="row"><span style="color:gray">' . $custom_name . '</span></th><td>';
						$custom_fields.='<input type="text" name="' . $custom_name . '" value="' . htmlentities(stripslashes(implode(' ', (array) $custom_value)), ENT_QUOTES) . '" />';
						$custom_fields.='</td></tr>';
					}
				}
			}
			if ($hastos) {
				$custom_fields.='<tr><th scope="row">' . __('Terms of Service', 'wishlist-member') . '</th><td>';
				if ($user_custom_fields['terms_of_service']) {
					$custom_fields.='Accepted';
				} else {
					$custom_fields.='&nbsp;';
				}
				$custom_fields.='</td></tr>';
			}

			$custom_fields_heading = $custom_fields ? __('<h3>Additional Registration Information</h3>', 'wishlist-member') : '';
			$custom_fields = $custom_fields ? $custom_fields_heading . '<table class="form-table wpm_regform_table WishListMemberCustomFields">' . $custom_fields . '</table>' : '';
			if ($custom_fields) {
				$custom_fields.='<input type="hidden" name="wlm_custom_fields_profile" value="' . implode(',', array_keys($custom_fields_form)) . '" />';
				if (($this->access_control->current_user_can('wishlistmember3_edit_custom_fields') || $this->access_control->current_user_can('wishlistmember3_members/manage')) && $user_custom_fields) {
					$custom_fields.='<input type="hidden" name="wlm_custom_fields_profile_hidden" value="' . implode(',', array_keys($user_custom_fields)) . '" />';
				}

				$postdata = (array) $postdata;
				array_walk_recursive($postdata, 'wlm_xss_sanitize');
				$postdata = json_encode(array_diff($postdata, array('')), JSON_UNESCAPED_UNICODE);
				$postdata2 = stripslashes($postdata);
				echo <<<STRING
<script type="text/javascript">
var wlm_regform_values = eval({$postdata2});
</script>
<script type="text/javascript" src="{$this->pluginURL}/js/regform_prefill.js"></script>
STRING;
			}

			$mailcheck = $profileuser->wlm_unsubscribe == 1 ? '' : 'checked="true"';
			$txt01 = __('Subscribed to Email Broadcast Mailing List', 'wishlist-member');
			$mailinglist = <<<STRING
			<tr valign="top">
				<th scope="row"></th>
				<td><label><input type="checkbox" name="wlm_unsubscribe" value="1" {$mailcheck} /> {$txt01}</label></td>
			</tr>
STRING;
			$txt01 = __('WishList Member Feed URL', 'wishlist-member');
			$wlm_feed_url = <<<STRING
			<tr valign="top">
				<th scope="row">{$txt01}</th>
				<td><a href="{$profileuser->wlm_feed_url}">{$profileuser->wlm_feed_url}</a></td>
			</tr>
STRING;
			// retrieve address
			$wpm_useraddress = $profileuser->wpm_useraddress;
			$countries = '<select name="wpm_useraddress[country]">';
			foreach ((array) $this->Countries() AS $country) {
				if(isset($profileuser->wpm_useraddress['country'])) {
					$selected = $country == $profileuser->wpm_useraddress['country'] ? ' selected="true" ' : '';
				}
					$selected = isset($selected) ? $selected : '';
					$countries.='<option' . $selected . '>' . $country . '</option>';

			}

			$wpm_useraddress_company = isset($wpm_useraddress['company']) ? stripslashes($wpm_useraddress['company']) : '';
			$wpm_useraddress_address1 = isset($wpm_useraddress['address1']) ? stripslashes($wpm_useraddress['address1']) : '';
			$wpm_useraddress_address2= isset($wpm_useraddress['address2']) ? stripslashes($wpm_useraddress['address2']) : '';
			$wpm_useraddress_city = isset($wpm_useraddress['city']) ? stripslashes($wpm_useraddress['city']) : '';
			$wpm_useraddress_state = isset($wpm_useraddress['state']) ? stripslashes($wpm_useraddress['state']) : '';
			$wpm_useraddress_zip = isset($wpm_useraddress['zip']) ? stripslashes($wpm_useraddress['zip']) : '';

			$txtaddress = __('Address', 'wishlist-member');
			$txtcompany = __('Company', 'wishlist-member');
			$txtcity = __('City', 'wishlist-member');
			$txtstate = __('State', 'wishlist-member');
			$txtzip = __('Zip', 'wishlist-member');
			$txtcountry = __('Country', 'wishlist-member');
			$addresssection = <<<STRING
				   <h3>{$txtaddress}</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">{$txtcompany}</th>
						<td><input type="text" name="wpm_useraddress[company]" value="{$wpm_useraddress_company}" size="30" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">{$txtaddress}</th>
						<td><input type="text" name="wpm_useraddress[address1]" value="{$wpm_useraddress_address1}" size="40" /><br /><input type="text" name="wpm_useraddress[address2]" value="{$wpm_useraddress_address2}" size="40" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">{$txtcity}</th>
						<td><input type="text" name="wpm_useraddress[city]" value="{$wpm_useraddress_city}" size="30" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">{$txtstate}</th>
						<td><input type="text" name="wpm_useraddress[state]" value="{$wpm_useraddress_state}" size="30" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">{$txtzip}</th>
						<td><input type="text" name="wpm_useraddress[zip]" value="{$wpm_useraddress_zip}" size="10" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">{$txtcountry}</th>
						<td>{$countries}</td>
					</tr>
				</table>
STRING;

			if ($this->access_control->current_user_can('wishlistmember3_members/manage')) {
				$wpm_levels = $this->GetOption('wpm_levels');
				$options = array();
				foreach ((array) $wpm_levels AS $id => $level) {
					$checked = in_array($id, $mlevels) ? 'checked="true"' : '';
					if ($checked) {
						$txnid = '<input type="text" name="txnid[' . $id . ']" value="' . $this->GetMembershipLevelsTxnID($profileuser->ID, $id) . '" size="20" style="text-align:center" />';
						$lvltime = '<input type="text" name="lvltime[' . $id . ']" value="' . gmdate('F d, Y h:i:sa', $this->UserLevelTimestamp($profileuser->ID, $id) + $this->GMT) . '" size="25" style="text-align:center" />';
						$lvl_parent = $this->LevelParent($id,$profileuser->ID);
						$lvl_parent = $lvl_parent && isset($wpm_levels[$lvl_parent]) ? $wpm_levels[$lvl_parent]["name"] : "";
					} else {
						$txnid = '';
						$lvltime = '';
						$lvl_parent = '';
					}
					$strike = isset($strike) ? $strike : '';
					$strike2 = isset($strike2) ? $strike2 : '';
					$options[] = '<tr><td style="padding:0;margin:0"><label><input type="checkbox" name="wpm_levels[]" value="' . $id . '" ' . $checked . ' /> ' . $strike . $level['name'] . $strike2 . '</label></td><td style="padding:0 5px;margin:0">' . $txnid . '</td><td style="padding:0 5px;margin:0">' . $lvltime . '</td><td style="padding:0 5px;margin:0;text-align:center">' . $lvl_parent . '</td></tr>';
				}
				$options = '<table cellpadding="2" cellspacing="4"><tr><td style="padding:0;margin:0;font-size:1em"><strong>' . __('Level', 'wishlist-member') . '</strong></td><td style="padding:0 5px;margin:0;font-size:1em;text-align:center"><strong>' . __('Transaction ID', 'wishlist-member') . '</strong></td><td style="padding:0 5px;margin:0;font-size:1em;text-align:center"><strong>' . __('Date Added to Level', 'wishlist-member') . '</strong></td><td style="padding:0 5px;margin:0;font-size:1em;text-align:center"><strong>' . __('Parent Level', 'wishlist-member') . '</strong></td></tr>' . implode('', $options) . '</table>';

				$registered = date('F d, Y h:ia', $this->UserRegistered($profileuser));
				$regip = $profileuser->wpm_registration_ip;
				$loginip = $profileuser->wpm_login_ip;

				//fix issue when no login record shows date in 1970/1969
				if ( !empty( $profileuser->wpm_login_date ) ) {
					$lastlogin = gmdate("F d, Y h:ia", (int) $profileuser->wpm_login_date + $this->GMT); //+$gmt);
				} else {
					$lastlogin = "No login record yet.";
				}

				$blacklisturl = $this->GetMenu('members');
				$blacklisturl = $blacklisturl->URL . '&mode=blacklist';
				$eblacklisturl = $blacklisturl . '&eappend=' . $profileuser->user_email;
				$blacklisturl = $blacklisturl . '&append=';

				if ($this->access_control->current_user_can('wishlistmember3_members/manage')) {
					$txt01 = __('Login Limit', 'wishlist-member');
					$txt01b = __('IPs Logged in Today', 'wishlist-member');
					$txt02 = __('Special Values:', 'wishlist-member');
					$txt03 = __('<b>0</b> or Blank: Use default settings', 'wishlist-member');
					$txt04 = __('<b>-1</b>: No limit for this user', 'wishlist-member');
					$loginlimit = <<<STRING
					<tr valign="top">
						<th scope="row">{$txt01}</th>
						<td>
							<input type="text" name="wpm_login_limit" value="{$profileuser->wpm_login_limit}" size="3" style="width:50px" /> IPs per day<br />
							{$txt02}<br />
								&raquo; {$txt03}<br />
								&raquo; {$txt04}
						</td>
					</tr>
STRING;
					$current_loggedin = (array) $profileuser->wpm_login_counter;
					$today = date('Ymd');
					foreach ((array) $current_loggedin AS $k => $v) {
						if ($v != $today
						)
							unset($current_loggedin[$k]);
					}
					if (count($current_loggedin)) {
						$reset_limit_counter = __('Reset Limit Counter', 'wishlist-member');
						$reset_limit_counter2 = '<div><label><input type="checkbox" name="wlm_reset_limit_counter" value="1" /> ' . $reset_limit_counter . '</label></div>';
						$current_loggedin = implode('<br />', array_keys((array) $current_loggedin));
					} else {
						$current_loggedin = __('This user has not yet logged in for the day', 'wishlist-member');
					}

					$reset_limit_counter2 = isset($reset_limit_counter2) ? $reset_limit_counter2 : '';
					$current_loggedin = <<<STRING
					<tr valign="top">
						<th scope="row">{$txt01b}</th>
						<td>
							{$current_loggedin}
							{$reset_limit_counter2}
						</td>
STRING;
				}

				$delete = '';
				if ($current_user->ID != $profileuser->ID && $profileuser->ID > 1) {
					$txt01 = __('Update Member Profile', 'wishlist-member');
					$txt02 = __('Delete This Member', 'wishlist-member');
					$txt03 = __('Warning!\\n\\nAre you sure you want to delete this user?', 'wishlist-member');
					$txt04 = __('Last Warning!\\n\\nAre you really sure that you want to delete this user?\\nNote that this action cannot be undone.', 'wishlist-member');
					$txt05 = __('Send Reset Password Link to User', 'wishlist-member');
					$delete = <<<STRING
					<tr valign="top">
						<th scope="row"></th>
						<td>
							<input type="hidden" name="user_login" value="{$profileuser->user_login}">
							<input class="button-primary" type="submit" value="{$txt01}" />
							<input class="button-secondary" type="submit" name="wpm_send_reset_email" value="{$txt05}" />
							&nbsp;&nbsp;
							<input class="button-secondary" type="submit" name="wpm_delete_member" value="{$txt02}" onclick="if(confirm('{$txt03}') && confirm('{$txt04}')){this.form.pass1.value='';return true;}else{return false;}" />
						</td>
					</tr>
STRING;
				}



				$txt01 = __('Membership Level', 'wishlist-member');
				$txt02 = __('Registered', 'wishlist-member');
				$txt03 = __('Email', 'wishlist-member');
				$txt04 = __('add to blacklist', 'wishlist-member');
				$txt05 = __('Date', 'wishlist-member');
				$txt06 = __('Last Login', 'wishlist-member');

				$wpmstuff = <<<STRING
				<h3>WishList Member</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">{$txt01}</th>
						<td>{$options}</td>
					</tr>
					{$mailinglist}
					{$wlm_feed_url}
					<tr valign="top">
						<th scope="row">{$txt02}</th>
						<td>{$txt03}: {$profileuser->user_email} &nbsp; <a href="admin.php{$eblacklisturl}">{$txt04} &raquo;</a><br />{$txt05}: {$registered}<br />IP: {$regip} &nbsp; <a href="admin.php{$blacklisturl}{$regip}">{$txt04} &raquo;</a></td>
					</tr>
					<tr valign="top">
						<th scope="row">{$txt06}</th>
						<td>{$txt05}: {$lastlogin}<br />IP: {$loginip} &nbsp; <a href="admin.php{$blacklisturl}{$loginip}">{$txt04} &raquo;</a></td>
					</tr>
					{$loginlimit}
					{$current_loggedin}
					{$delete}
				</table>

				{$addresssection}

				{$data_privacy}

				{$custom_fields}

STRING;
			} else {
				$wpmstuff = "<table class='form-table'>{$consent_to_market}{$mailinglist}{$wlm_feed_url}</table>{$addresssection}{$custom_fields}";
			}
			echo <<<STRING
<div id="WishListMemberUserProfile">
{$wpmstuff}
	<input type="hidden" name="wlm_updating_profile" value="1" />
</div>
STRING;

			$nodeIndex = $this->access_control->current_user_can('wishlistmember3_members/manage') ? 0 : 3;
			echo <<<STRING
				<script type="text/javascript">
					function MoveWLMember(){
						try{
							var x=document.getElementById('WishListMemberUserProfile');
							var p=x.parentNode;
							var s=p.getElementsByTagName('h3');
							p.insertBefore(x,s[{$nodeIndex}]);
						}catch(e){}
					}
					MoveWLMember();
				</script>
STRING;
		}

		// -----------------------------------------
		// So that we can choose to return either a 404 or a 200 when viewing registration pages...
		function RewriteRules($rules = null) {
			$rules['register/(.+?)'] = 'index.php';
			return $rules;
		}

		// -----------------------------------------
		// Don't show comments...
		function NoComments() {
			return ($this->pluginDir . '/comments.php');
		}

		// -----------------------------------------
		// WP Head Hook
		function WPHead() {
			global $post;
			echo "<!-- Running WishList Member v{$this->Version} -->\n";
			$p_id = isset($post->ID) ? $post->ID : '';

			if ($p_id == $wpmpage = $this->MagicPage(false)) {
				echo '<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW" />';
				echo "\n";
				echo '<META NAME="GOOGLEBOT" CONTENT="NOARCHIVE"/ >';
				echo "\n";
				echo '<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"/ >';
				echo "\n";
				echo '<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE"/ >';
				echo "\n";
				echo '<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 02 Aug 1999 01:02:03 GMT">';
				echo "\n";
			}

			$wlm_css = $this->GetOption('wlm_css'); //wlm3
			if ( $wlm_css === false ) {
				echo "<style type='text/css'>\n". $this->GetOption('reg_form_css') . "\n\n\n" . $this->GetOption('sidebar_widget_css') . "\n\n\n" . $this->GetOption('login_mergecode_css') . "\n</style>";
			} else {
				echo "<style type='text/css'>\n" .$wlm_css ."\n</style>";
			}
		}

		// run SyncMembership daily
		function SyncMembershipCount() {
			$this->SyncMembership(true);
		}

		function ProcessApiQueue($limit = 10,$tries=3) {
			//process infusionsoft sc api queue
			if(class_exists("WLM_INTEGRATION_INFUSIONSOFT_INIT")){
				$WLM_INTEGRATION_INFUSIONSOFT_INIT = new WLM_INTEGRATION_INFUSIONSOFT_INIT;
				if (isset($WLM_INTEGRATION_INFUSIONSOFT_INIT) && method_exists($WLM_INTEGRATION_INFUSIONSOFT_INIT, 'ifscProcessQueue')) {
					$WLM_INTEGRATION_INFUSIONSOFT_INIT->ifscProcessQueue($limit,$tries);
				}
			}
			//process 1ShoppingCart api queue for New Orders
			if(class_exists("WLM_INTEGRATION_1SHOPPINGCART_INIT")){
				$WLM_INTEGRATION_1SHOPPINGCART_INIT = new WLM_INTEGRATION_1SHOPPINGCART_INIT;
				if (isset($WLM_INTEGRATION_1SHOPPINGCART_INIT) && method_exists($WLM_INTEGRATION_1SHOPPINGCART_INIT, 'ProcessOneSCQueueForIncompleteReg')) {
					$WLM_INTEGRATION_1SHOPPINGCART_INIT->ProcessOneSCQueueForIncompleteReg($limit);
				}
			}
		}

		/**
		 * Send Queued Mail. Called via WP Cron
		 * @global object $wpdb WordPress Database Object
		 */
		function SendQueuedMail( $limit = null ) {

			//is still sending? return
			if ( get_transient( 'wlm_is_sending_broadcast' ) !== false ) return false;

			global $wpdb;
			$mlimit = $this->GetOption('email_memory_allocation');
			$mlimit = ($mlimit == "" ? WLMMEMORYALLOCATION : $mlimit);
			@ini_set('memory_limit', $mlimit); // request for more memory
			ignore_user_abort(true);

			// is $limit specified? if so, use it. if not, read from email_per_minute setting
			if ( is_null( $limit ) ) {
				$limit = $this->GetOption('email_per_minute');
			}
			$limit += 0;
			// no limit yet? let's set it to the default setting
			if ( $limit < 1 ) $limit = WLMDEFAULTEMAILPERMINUTE;

			// retrieve queued mails
			$mails = $this->GetEmailBroadcastQueue(null,false,false,$limit);
			$totalcnt = 0;
			$failedcnt = 0;
			$mailcnt = count($mails);
			$date_sent = "";

			if ( $mailcnt > 0 ) set_transient( 'wlm_is_sending_broadcast', 1, MINUTE_IN_SECONDS );
			else return false;

			if ( $mails ) {
				// go through and send the emails
				foreach ( (array) $mails AS $mail) {

					$user = $this->Get_UserData($mail->userid);
					$mailed =  false;
					if ( $user ) { //if user exists
						$subject = $mail->subject;
						$message = $mail->text_body;
						$footer = $mail->footer;
						$sent_as = $mail->sent_as;

						$footer = @unserialize($footer);
						if ( is_array( $footer )  ) {
							$footer_array = array();
							if ( isset($footer['signature']) ) $footer_array[] = $footer['signature'];
							$footer_array[] = sprintf(WLMCANSPAM, $user->ID . '/' . substr(md5($user->ID . WLMUNSUBKEY), 0, 10));
							if ( isset($footer['address']) ) $footer_array[] = $footer['address'];
							$footer = "\n\n" .implode( "\n\n", $footer_array);
						} else {
							//add unsubcribe and user details link
							$footer = $mail->footer ."\n\n" . sprintf(WLMCANSPAM, $user->ID . '/' . substr(md5($user->ID . WLMUNSUBKEY), 0, 10));
						}

						//process shortcodes
						$shortcode_data = $this->wlmshortcode->manual_process($user->ID, $message, true);
						//lets make sure that it is an array
						if (!is_array($shortcode_data)) {
							$shortcode_data = array();
						}
						/* strip tags for membership levels */
						if ($shortcode_data['wlm_memberlevel']) {
							$shortcode_data['wlm_memberlevel'] = strip_tags($shortcode_data['wlm_memberlevel']);
						}
						if ($shortcode_data['wlmmemberlevel']) {
							$shortcode_data['wlmmemberlevel'] = strip_tags($shortcode_data['wlmmemberlevel']);
						}
						if ($shortcode_data['memberlevel']) {
							$shortcode_data['memberlevel'] = strip_tags($shortcode_data['memberlevel']);
						}

						if ($sent_as == "html") {
							$fullmsg = $message . nl2br($footer);
							$mailed = $this->SendHTMLMail($user->user_email, $subject, stripslashes($fullmsg), $shortcode_data, false, null, 'UTF-8', $mail->from_name, $mail->from_email);

						} else {
							$fullmsg = $message . $footer;
							// $fullmsg = wordwrap($fullmsg);
							$mailed = $this->SendMail($user->user_email, $subject, stripslashes($fullmsg), $shortcode_data, false, null, 'UTF-8', $mail->from_name, $mail->from_email);

						}
					}

					// update total count of emails processed
					if ($mailed) { // if sent
						$totalcnt++;
						//delete from the queue record
						$this->DeleteEmailBroadcastQueue($mail->id);
					} else { // if failed
						$failedcnt++;
						//update the queue record as failed
						$this->FailEmailBroadcastQueue($mail->id);
					}
				}

				// save last send date
				$date_sent = date("F j, Y, h:i:s A", current_time('timestamp'));
				$this->SaveOption('WLM_Last_Queue_Sent', $date_sent);
			}

			$log = "#SENDING QUEUE#=> #Limit:" . $limit . " #Query Count:" . $mailcnt . " #Sent:" . $totalcnt . " #Failed:" . $failedcnt . " #Last Queue Sent:" . $date_sent;
			$ret = $this->LogEmailBroadcastActivity($log);

			//let delete the transient
			delete_transient('wlm_is_sending_broadcast');

			//let process her again
			$url = get_home_url() ."?wlmprocessbroadcast=1";
			wp_remote_get( $url, array( 'timeout'=>10, 'blocking' => false ) );

			return $totalcnt;
		}

		/*
		 * Disables RSS Enclosures for non-authenticated feeds
		 */

		function RSSEnclosure($data) {
			$authenticatedfeed = $this->VerifyFeedKey(wlm_arrval($_GET, 'wpmfeedkey'));
			if ($authenticatedfeed) {
				return $data;
			} else {
				return '';
			}
		}

		/**
		 * Schedule the loading of attachments
		 */
		function ScheduleReloadAttachments() {
			wp_schedule_single_event(time(), 'wishlistmember_attachments_load');
			spawn_cron(time());
		}

		/**
		 * Load the attachments
		 */
		function ReloadAttachments() {
			$this->FileProtectLoadAttachments();
		}

		function WishListWidget_register_widgets() {
			// curl need to be installed
			register_widget('WishListWidget');
		}

		/**
		 * Auto Remove From Feature hook for Add Action
		 * @param integer $uid User ID
		 * @param array $new_levels New Membership Levels
		 */
		function DoAutoAddRemove($uid, $new_levels = '', $removed_levels = '') {
			$new_levels = (array) $new_levels;
			$removed_levels = (array) $removed_levels;
			$wlmuser = new \WishListMember\User( $uid );
			foreach ($removed_levels as $key => $value) {
				if ( strpos( $value, "U-" ) !== false ) {
					unset ( $removed_levels[ $key ] );
				}
			}
			foreach ($new_levels as $key => $value) {
				if ( strpos( $value, "U-" ) !== false ) {
					unset ( $new_levels[ $key ] );
				}
			}
			//prevent infinite loop, dont run this for levels with parent
			foreach ( $new_levels as $key=>$lvl ) {
				if( isset( $wlmuser->Levels[$lvl] ) ) {
					if( $wlmuser->Levels[$lvl]->ParentLevel ) {
						unset( $new_levels[$key] ); //dont do add remove for child levels
					}
				} else {
					unset( $new_levels[$key] ); //only add levels that user has
				}
			}

			if ( count( $new_levels ) <= 0 && count( $removed_levels ) <= 0 ) {
				return;
			}

			$wlmuser->DoAddRemove( $new_levels, $removed_levels );
			$this->UpdateChildStatus( $uid, $new_levels );
		}

		/**
		 * Auto Remove From Feature hook for Remove action
		 * @param integer $uid User ID
		 * @param array $new_levels New Membership Levels
		 */
		function Remove_DoAutoAddRemove($uid, $removed_levels = '') {
			$removed_levels = (array) $removed_levels;
			$wlmuser = new \WishListMember\User( $uid );
			foreach ($removed_levels as $key => $value) {
				if ( strpos( $value, "U-" ) !== false ) {
					unset ( $removed_levels[ $key ] );
				}
			}
			if ( count( $removed_levels ) <= 0 ) return;

			$wlmuser->DoAddRemove( $removed_levels, array(), "remove" );
		}

		/**
		 * Auto Remove From Feature hook for Cancel action
		 * @param integer $uid User ID
		 * @param array $new_levels New Membership Levels
		 */
		function Cancel_DoAutoAddRemove($uid, $cancel_levels = '') {
			$cancel_levels = (array) $cancel_levels;
			$wlmuser = new \WishListMember\User( $uid );
			$wlmuser->DoAddRemove( $cancel_levels, array(), "cancel" );
		}

		/**
		 * Remove Child of Parent Levels hook
		 * @param integer $uid User ID
		 * @param array $removed_levels Removed Membership Levels
		 * @param array $new_levels New Membership Levels
		 */
		function DoRemoveChildLevels($uid, $removed_levels = array(), $new_levels = array() ) {
			//** we remove this part then we change to Level Actions from Add To feature
			//** because levels with parent automatically inherits the status of its parent
			// $wpm_levels = $this->GetOption('wpm_levels');
			// foreach ($removed_levels as $key => $lvl) {
			// 	$inherit = isset( $wpm_levels[$lvl]['inheritparent'] ) && $wpm_levels[$lvl]['inheritparent'] == '1' ? true:false;
			// 	if ( ! $inherit ) {
			// 		unset( $removed_levels[$key] );
			// 	}
			// }
			$this->RemoveChildLevels( $uid, $removed_levels );
		}

		/**
		 * Update Status of Child when Parent Levels changed hook
		 * @param integer $uid User ID
		 * @param array $parent_levels Removed Membership Levels
		 */
		function DoUpdateChildStatus( $uid, $parent_levels ) {
			//** we remove this part then we change to Level Actions from Add To feature
			//** because levels with parent automatically inherits the status of its parent
			// $wpm_levels = $this->GetOption('wpm_levels');
			// foreach ($parent_levels as $key => $lvl) {
			// 	$inherit = isset( $wpm_levels[$lvl]['inheritparent'] ) && $wpm_levels[$lvl]['inheritparent'] == '1' ? true:false;
			// 	if ( ! $inherit ) {
			// 		unset( $parent_levels[$key] );
			// 	}
			// }
			$this->UpdateChildStatus( $uid, $parent_levels );
		}

		/**
		 * Set Expire Status of Child with Parent Levels
		 * @param assorted $expire Expire Status
		 * @param integer $uid User ID
		 * @param array $parent_level Removed Membership Level
		 */
		function DoExpireChildStatus(  $expire_date, $uid, $level ) {
			$p = $this->LevelParent( $level, $uid );
			$wpm_levels = $this->GetOption('wpm_levels');

			//** we remove this part then we change to Level Actions from Add To feature
			//** because levels with parent automatically inherits the status of its parent
			// $inherit = isset( $wpm_levels[$p]['inheritparent'] ) && $wpm_levels[$p]['inheritparent'] == '1' ? true:false;
			// if ( ! $inherit ) {
			// 	return $expire_date;
			// }

			if( $p ) {
				$p_expire_date = $this->LevelExpireDate($p, $uid);
				if( $expire_date === false ) {
					$expire_date = $p_expire_date;
				} else if ( $p_expire_date !== false &&  $p_expire_date < $expire_date ) {
					$expire_date = $p_expire_date;
				}
			}
			return $expire_date;
		}

		/**
		 * Notify Users with Incomplete Registration
		 * Called by WP-Cron
		 */
		function NotifyRegistration() {
			// let's call 3.0's function hook installed
			return $this->incomplete_registration_notification();
		}

		/**
		 * Called by WP-Cron
		 *
		 * (recoded for WLM 3.0)
		 */
		function ExpiringMembersNotification() {
			$lastsent = $this->GetOption('expnotification_last_sent');
			$lastsent = empty($lastsent) ? 0 : date('m/d/Y', $lastsent);

			// if ($lastsent == date('m/d/Y')) return;

			$dont_send_when_unsubscribed = $this->GetOption('dont_send_reminder_email_when_unsubscribed');
			$wpm_levels = $this->GetOption('wpm_levels');

			$expiring_users = $this->GetExpiringMembers();
			$users = array();

			foreach(array('admin','user') AS $type) {
				foreach ($expiring_users[$type] as $u) {

					$lid = $u['level_id'];
					$uid = $u['user_id'];

					if(empty($users[$uid])) {
						$users[$uid] = new \WishListMember\User($uid, true);
					}

					$umeta_name = sprintf( '_expiring_email_sent_%s_%s_%s', $type, $lid, $users[$uid]->Levels[$lid]->ExpiryDate );
					if( $this->Get_UserMeta( $uid, $umeta_name ) ) {
						continue;
					}

					$macros = array(
						'[memberlevel]' => $wpm_levels[$lid]['name'],
						'[expirydate]' => date('M d, Y', $users[$uid]->Levels[$lid]->ExpiryDate),
					);

					if($type == 'user') {
						if(!$dont_send_when_unsubscribed || !$user->UserInfo->data->wlm_unsubscribe) {
							$this->send_email_template('expiring_level', $u['user_id'], $macros); // send to user
							$this->Update_UserMeta( $uid, $umeta_name, time() );
						}
					} else {
						$this->send_email_template('expiring_level_admin', $u['user_id'], $macros, $this->GetOption( 'email_sender_address' ) ); // send to admin
						$this->Update_UserMeta( $uid, $umeta_name, time() );
					}
				}
			}
			$this->SaveOption('expnotification_last_sent', time());
		}

		/**
		 * Send Email Reminders for members that needs to confirm their email
		 * Called by WP-Cron
		 */
		function EmailConfirmationReminders() {
			if (!$this->GetOption('email_conf_send_after')) {
				return;
			}

			// Get all members with for confirmation
			$for_confirmation_users = $this->GetUsersForConfirmation(); //get users with incomplete registration

			foreach ($for_confirmation_users as $id => $user) {

				$first_notification = (float)$this->GetOption('email_conf_send_after');
				$add_notification_count = (int)$this->GetOption('email_conf_how_many') + 1;
				$add_notification_freq = (int)$this->GetOption('email_conf_send_every');
				$email_confirmation_reminder = (array)$user['wlm_email_confirmation_reminder'];
				$send = false;
				$count = isset($email_confirmation_reminder['count']) ? $email_confirmation_reminder['count'] : 0;
				$lastsend = isset($email_confirmation_reminder['lastsend']) ? $email_confirmation_reminder['lastsend'] : time();
				$t_diff = (time() - $lastsend)/3600;
				$t_diff = $t_diff < 0 ? 0 : round($t_diff,3);
				if($count <= 0 && $t_diff >= $first_notification){
					$send = true;
				}elseif($count < $add_notification_count && $t_diff >= $add_notification_freq){
					$send = true;
				}

				if ($send) {
					$wpm_levels = $this->GetOption('wpm_levels');

					$macros = array(
						'[memberlevel]' => trim( $wpm_levels[$email_confirmation_reminder['wpm_id']]['name'] ),
						'[username]'    => $user['username'],
						'[password]'    => "********",
						'[confirmurl]'  => get_bloginfo( 'url' ) . '/index.php?wlmconfirm=' . $id . '/' . md5( trim( $user['email'] ) . '__' . trim( $user['username'] ) . '__' . $email_confirmation_reminder['wpm_id'] . '__' . $this->GetAPIKey() ),
					);

					$this->send_email_template('email_confirmation', $id, $macros);
					$email_confirmation_reminder["count"] = $count + 1;
					$email_confirmation_reminder["lastsend"] = time();
					update_user_meta($id, 'wlm_email_confirmation_reminder',$email_confirmation_reminder);
				}

			}
		}

		/**
		 * Filter for wp_get_nav_menu_items
		 * Handles the hiding/showing of Menu items
		 *
		 * @global object $current_user
		 * @param array $items Array of menu items
		 * @return array Filtered array of menu items
		 */
		function OnlyListNavMenuItemsForLevel($items) {
			global $current_user;
			/*
			 * we only filter when only_show_content_for_level is enabled
			 * or if the current user is an administrator
			 */
			if ($this->GetOption('only_show_content_for_level') && !$GLOBALS['current_user']->caps['administrator']) {

				/* get all levels */
				$wpm_levels = $this->GetOption('wpm_levels');

				/*
				 * save $items to $orig
				 * and set $items to empty array
				 */
				$orig = $items;
				$items = array();
				$protected_custom_post_types = $this->GetOption('protected_custom_post_types');
				/* if a user is logged in */
				if ($current_user->ID) {
					/* get all levels for this user */
					$levels = $this->GetMembershipLevels($current_user->ID, false, true);

					/* process content */
					$allcategories = $allpages = $allposts = false;
					foreach ($levels AS $level) {
						if (!$allcategories) {
							if (isset($wpm_levels[$level]['allcategories']))
								$allcategories = true;
						}
						if (!$allpages) {
							if (isset($wpm_levels[$level]['allpages']))
								$allpages = true;
						}
						if (!$allposts) {
							if (isset($wpm_levels[$level]['allposts']))
								$allposts = true;
						}
					}
					$categories = $pages = $posts = array();

					/* categories */
					if (!$allcategories)
						$categories = $this->GetMembershipContent('categories', $levels);

					/* pages */
					if (!$allpages)
						$pages = $this->GetMembershipContent('pages', $levels);

					/* posts */
					if (!$allposts) {
						$posts = $this->GetMembershipContent('posts', $levels);
						//retrieve custom post types id
						foreach ((array) $protected_custom_post_types as $custom_type) {
							$posts = array_merge($posts, $x = $this->GetMembershipContent($custom_type, $levels));
							// foreach ($levels AS $level) {
							// 	$ids = array_merge($ids, $x = $this->GetMembershipContent($custom_type, $level));
							// }
						}
					}

					/*
					 * go through each menu item and remove anything
					 * that the user does not have access to
					 */
					foreach ($orig AS $item) {
						if (in_array($item->object, $this->taxonomies)) {
							if ($allcategories OR !$this->CatProtected($item->object_id) OR in_array($item->object_id, $categories))
								$items[] = $item;
						}
						elseif ($item->object == 'page') {
							if ($allpages OR !$this->Protect($item->object_id) OR in_array($item->object_id, $pages))
								$items[] = $item;
						}
						elseif ($item->object == 'post') {
							if ($allposts OR !$this->Protect($item->object_id) OR in_array($item->object_id, $posts))
								$items[] = $item;
						}
						elseif( in_array($item->object, (array) $protected_custom_post_types)) {
							if ($allposts OR !$this->Protect($item->object_id) OR in_array($item->object_id, $posts))
								$items[] = $item;
						}
						else {
							$items[] = $item;
						}
					}
					/* if a user is not logged in */
				} else {
					/*
					 * go through each menu item and
					 * remove all protected content
					 */

					foreach ($orig AS $item) {
						if (in_array($item->object, $this->taxonomies)) {
							if (!$this->CatProtected($item->object_id))
								$items[] = $item;
						}
						elseif ($item->object == 'page' || $item->object == 'post') {
							if (!$this->Protect($item->object_id))
								$items[] = $item;
						}
						elseif( in_array($item->object, (array) $protected_custom_post_types)) {
							if (!$this->Protect($item->object_id))
								$items[] = $item;
						}
						else {
							$items[] = $item;
						}
					}
				}
				/*
				 * re-organize menus, make sure that
				 * hierarchy remains meaningful
				 */

				/* first we collect all IDs from $items to make it easier to search */
				$item_ids = array();
				foreach ($items AS $key => $item) {
					$item_ids[$item->ID] = $key;
				}

				/* next, we collect all parent IDs from $orig */
				$parent_ids = array();
				foreach ($orig AS $item) {
					$parent_ids[$item->ID] = $item->menu_item_parent;
				}

				/* then we walk through and fix the parent IDs if needed */
				for ($i = 0; $i < count($items); $i++) {
					$item = &$items[$i];
					$parent = $item->menu_item_parent;
					while (!isset($item_ids[$parent])) {
						$parent = $parent_ids[$parent];
						if ($parent == 0
						)
							break;
					}
					$item->menu_item_parent = $parent;
				}
			}
			/* return the filtered menu item */
			return $items;
		}

		/**
		 * TempEmailSanitize
		 * is a filter that hooks to sanitize_email
		 * and makes sure that our temp email address
		 * which we use for shopping cart integrations
		 * go through.
		 *
		 * @param string $email
		 * @return string
		 */
		function TempEmailSanitize($email) {
			if (
					wlm_arrval($_POST, 'orig_email') && wlm_arrval($_POST, 'email') == wlm_arrval($_POST, 'username') && wlm_arrval($_POST, 'email') == 'temp_' . md5(wlm_arrval($_POST, 'orig_email'))
			) {
				return $_POST['email'];
			}
			return $email;
		}

		function Plugin_Update_Notice($transient) {
			static $our_transient_response;

			if(empty($transient)) $transient = new stdClass;

			$version = current_user_can('update_plugins') ? wlm_arrval($_REQUEST, 'wlm3_rollback') : '';

			if ($this->Plugin_Is_Latest() && !$version) {
				return $transient;
			}

			if (!$our_transient_response) {
				$package = $this->Plugin_Download_Url();
				if ($package === false)
					return $transient;

				$file = $this->PluginFile;

				$our_transient_response = array(
					$file => (object) array(
						'id' => 'wishlist-member-' . time(),
						'slug' => $this->PluginSlug,
						'plugin' => $file,
						'new_version' => $version ? $version : $this->Plugin_Latest_Version(),
						'url' => 'http://member.wishlistproducts.com/',
						'package' => $package,
						'requires_php' => WLM3_MIN_PHP_VERSION,
					)
				);
			}

			$transient->response = array_merge((array) $transient->response, (array) $our_transient_response);

			return $transient;
		}

		function Plugin_Info_Hook($res, $action, $args) {
			if ($res === false && $action == 'plugin_information' && $args->slug == $this->PluginSlug) {
				$res = new stdClass();
				$res->name = 'WishList Member&trade;';
				$res->slug = $this->PluginSlug;
				$res->version = $this->Plugin_Latest_Version();
				$res->author = WLM3_PLUGIN_AUTHOR;
				$res->author_profile = WLM3_PLUGIN_AUTHORURI;
				$res->homepage = WLM3_PLUGIN_URI;
				$res->active_installs = wlm_arrval((array)wp_remote_get('http://wishlistactivation.com/counter.php'), 'body') + 0;
				$res->requires = WLM3_MIN_WP_VERSION;
				$res->requires_php = WLM3_MIN_PHP_VERSION;
				$res->banners = [
					'low' => $this->pluginURL3 . '/ui/images/bg-wlm3-plugin.png', // 772x250 jpeg or png image
				];
				$res->sections = array(
					'description' => '<p><strong>WishList Member&trade;</strong> is a powerful, yet easy to use membership software solution that can turn any WordPress site into a full-blown membership site.</p>'
					. '<p>Simply install the plugin, and within minutes you’ll have your own membership site up and running… complete with protected, members-only content, integrated payments, member management, and so much more!</p>',

					'changelog' => '<p>WishList Member&trade; Changelog can be viewed <a href="https://customers.wishlistproducts.com/plugin/wishlist-member-2/" target="_blank">HERE</a>.</p>',

					'support' => '<p>WishList Member&trade; offers support using the following options:</p>'
					. '<ul>'
					. '<li><a href="https://help.wishlistproducts.com/article-categories/video-tutorials/" target="_blank" title="Video Tutorials">Tutorials</a></li>'
					. '<li><a href="https://help.wishlistproducts.com/" target="_blank" title="Help">Help Docs</a></li>'
					. '<li><a href="http://codex.wishlistproducts.com/" target="_blank" title="API Documents">API Docs</a></li>'
					. '<li><a href="https://customers.wishlistproducts.com/support/" target="_blank" title="Support">Support</a></li>'
					. '</ul>',
				);
			}
			return $res;
		}

		function Pre_Upgrade($return, $plugin) {
			$plugin = (isset($plugin['plugin'])) ? $plugin['plugin'] : '';
			if ($plugin == $this->PluginFile) {
				$dir = sys_get_temp_dir() . '/' . sanitize_title('wishlist-member-upgrade-' . get_bloginfo('url'));

				$this->Recursive_Delete($dir);

				$this->Recursive_Copy($this->pluginDir . '/extensions', $dir . '/extensions');
				$this->Recursive_Copy($this->pluginDir . '/lang', $dir . '/lang');
			}
			return $return;
		}

		function Post_Upgrade($return, $plugin) {
			$plugin = (isset($plugin['plugin'])) ? $plugin['plugin'] : '';
			if ($plugin == $this->PluginFile) {
				$dir = sys_get_temp_dir() . '/' . sanitize_title('wishlist-member-upgrade-' . get_bloginfo('url'));

				$this->Recursive_Copy($this->pluginDir . '/extensions', $dir . '/extensions');
				$this->Recursive_Copy($this->pluginDir . '/lang', $dir . '/lang');

				$this->Recursive_Copy($dir . '/extensions', $this->pluginDir . '/extensions');
				$this->Recursive_Copy($dir . '/lang', $this->pluginDir . '/lang');

				$this->Recursive_Delete($dir);
			}
			return $return;
		}

		function OnlyShowCommentsForLevel($where) {
			$wpm_levels = $this->GetOption('wpm_levels');
			$id = 0;
			if (is_user_logged_in()) {
				$id = $GLOBALS['current_user']->ID;
			}
			if (isset($_GET['wpmfeedkey'])) {
				$wpmfeedkey = $_GET['wpmfeedkey'];
				$id = $this->VerifyFeedKey($wpmfeedkey);
			}
			if ($id) {
				if (current_user_can('activate_plugins')) {
					return $where;
				}
				$levels = $this->GetMembershipLevels($id, $names, true);
				foreach ($levels AS $level) {
					if ($wpm_levels[$level]['comments']) {
						return $where;
					}
				}
				$protected_comments = $this->GetMembershipContent('comments', $levels);

				$comments = array(0);
				foreach ($protected_comments AS $comment) {
					$comments = array_merge($comments, (array) $comment);
				}
				$comments = implode(',', array_map('wlm_abs_int', array_unique($comments)));
				$where .= ' AND comment_post_ID NOT IN (' . $comments . ') ';
			} else {
				$protected_comments = $this->GetMembershipContent('comments');
				$protect = $this->ProtectedIds();
				$protect[] = 0;
				foreach ($protected_comments AS $pc) {
					$protect = array_merge($protect, (array) $pc);
				}
				$protect = implode(',', array_map('wlm_abs_int', array_unique($protect)));
				$where .= ' AND comment_post_ID NOT IN (' . $protect . ') ';
			}
			return $where;
		}

		function frontend_scripts_and_styles() {
			$magicpage = is_page($this->MagicPage(false));
			$fallback = $magicpage | $this->IsFallBackURL(wlm_arrval($_GET, 'reg'));

			if (wlm_arrval($this, 'force_registrationform_scripts_and_styles') === true || $magicpage || $fallback) {
				wp_enqueue_script('jquery-ui-core');
				wp_enqueue_script('wishlist_member_regform_prefill', $this->pluginURL . '/js/regform_prefill.js', array(), $this->Version);
				wp_enqueue_script('thickbox');
				wp_enqueue_style('thickbox');
				wp_enqueue_script('tb_images', $this->pluginURL . '/js/thickbox_images.js', array(), $this->Version);

				switch( $this->GetOption( 'FormVersion' ) ) {
					case 'improved':
						wp_enqueue_script('wishlist_member_improved_registration_js', $this->pluginURL . '/js/improved_registration_form_frontend.js', 'jquery-ui', $this->Version);
						wp_enqueue_style('wishlist_member_improved_registration_css', $this->pluginURL . '/css/improved_registration_form_frontend.css', 'jquery-ui', $this->Version);
						break;
					case 'themestyled':
						// scripts are enqueued as needed by wlm_form_field()
						break;
					default:
						wp_enqueue_style('wishlist_member_custom_reg_form_css', $this->pluginURL . '/css/registration_form_frontend.css', array(), $this->Version);
				}

				add_action('wp_print_scripts', array($this, 'regpage_form_data'));
			}
		}

		function regpage_form_data() {

			$this->RegPageFormData = isset($this->RegPageFormData) ? $this->RegPageFormData : '';
			foreach ((array) $this->RegPageFormData AS $k => $v) {
				if(!empty($v)) {
					$this->RegPageFormData[$k] = @stripslashes((string) $v);
				}
			}
			$data = array_diff((array) $this->RegPageFormData, array(''));

			//do not prefill temporary email
			foreach ($data as $k => $v) {
				if (stripos($v, '@temp.mail') !== false) {
					unset($data[$k]);
				}
			}
			array_walk_recursive($data, 'wlm_xss_sanitize');
			$postdata = json_encode($data);
			if (!empty($data)) {
				echo <<<STRING
				<script type="text/javascript">
					var wlm_regform_values = eval({$postdata});
				</script>
STRING;
			}
		}

		function UpdateNag() {
			$current_screen = get_current_screen();
			if(preg_match('/^update/', $current_screen->id)) {
				return;
			}
			if (!$this->Plugin_Is_Latest()) {
				$latest_wpm_ver = $this->Plugin_Latest_Version();
				if (!$latest_wpm_ver) {
					$latest_wpm_ver = $this->Version;
				}

				global $current_user ;
				$user_id = $current_user->ID;
								$dismiss_meta = 'dismiss_wlm_update_notice_' . $latest_wpm_ver;
				if ( !get_user_meta($user_id, $dismiss_meta ) && current_user_can( 'update_plugins' )) {
					echo "<div class='update-nag'>";
					printf(__("The most current version of WishList Member is v%s.", 'wishlist-member'), $latest_wpm_ver);
					echo " ";
					echo "<a href='" . $this->Plugin_Update_Url() . "'>";
					_e("Please update now. ", 'wishlist-member');
					echo "</a> | ";
					echo '<a href="' . esc_url(add_query_arg( 'dismiss_notice', '0' )) . '"> Dismiss </a>';
					echo "</div>";
				}
			}
		}

		function dismiss_wlm_update_notice() {

			global $current_user ;
			$user_id = $current_user->ID;

			/* If user clicks to ignore the notice, add that to their user meta */
					if (!$this->Plugin_Is_Latest()) {
			$latest_wpm_ver = $this->Plugin_Latest_Version();
			if (!$latest_wpm_ver) {
                            $latest_wpm_ver = $this->Version;
			}

						$dismiss_meta = 'dismiss_wlm_update_notice_'. $latest_wpm_ver;
						if ( isset($_GET['dismiss_notice']) && '0' == $_GET['dismiss_notice'] ) {
							add_user_meta($user_id, $dismiss_meta, 'true', true);
						}
					}
		}



		function AcceptHQAnnouncement() {

				global $current_user ;
				$user_id = $current_user->ID;
				$dismiss_meta = 'dismiss_hq_notice';
				$announcement = $this->Get_Announcement();
                if (!empty($announcement) && !get_user_meta($user_id, $dismiss_meta ) && current_user_can( 'update_plugins' )) {
				    echo "<br/><div class='update-nag'>";
				    _e( $announcement, 'wishlist-member');
				    echo " ";
				    echo '<a href="' . esc_url(add_query_arg( 'dismiss_hq_notice', '0' )) . '"> Dismiss </a>';
				    echo "</div>";
				}
		}

		function dismiss_hq_announcement() {

			global $current_user;
			$user_id = $current_user->ID;

			/* If user clicks to ignore the notice, add that to their user meta */
			if (isset($_GET['dismiss_hq_notice']) && '0' == $_GET['dismiss_hq_notice']) {
					$dismiss_meta = 'dismiss_hq_notice';
					add_user_meta($user_id, $dismiss_meta, 'true', true);
			}

		}

		function dismiss_wlm_nag() {
			if(!empty($_POST['nag_name'])) {
				$this->AddOption($_POST['nag_name'], time());
			}
		}

		function WLMUserSearch_Ajax() {
			$search = wlm_arrval($_POST, 'search');
			$search_by = trim(wlm_arrval($_POST, 'search_by'));
			$url = trim(wlm_arrval($_POST, 'url'));

			$search_results = array();
			switch($search_by){
				case 'by_level':
					if (empty($search)) {
						die();
					}
					$search_results = $this->MemberIDs($search);
					break;
				default:
					$search = trim($search);
					if (empty($search)) {
						die();
					}
					$search_results = new \WishListMember\User_Search($search);
					$search_results = $search_results->results;
			}

			if(wlm_arrval($_POST, 'return_raw') == 1) {
				if(count($search_results)) {
					$get_users = array(
						'include' => $search_results,
						'fields'  => array('ID', 'user_login', 'display_name', 'user_email')
					);
					$data = array('success' => 1, 'data' => get_users($get_users));
				} else {
					$data = array('success' => 0, 'data' => array());
				}
				wp_die(json_encode($data));
			}

			if (count($search_results)) {
				$output = '';
				$alternate = '.';
				foreach ($search_results AS $uid) {
					$user = get_userdata($uid);
					$name = trim($user->user_firstname . ' ' . $user->user_lastname);
					if ($name == '')
						$name = $user->user_login;
					$alternate = $alternate ? '' : ' alternate';
					$output .= sprintf('<tr class="user_%2$d' . $alternate . '">
						<td class="num">%2$d</td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td class="select_link"><a href="%1$s">[select]</a></tr>', $url . $uid, $uid, $name, $user->user_login, $user->user_email);
				}
				$output = '<table class="widefat"><thead><tr>
					<th class="num">ID</th>
					<th>Name</th>
					<th>Username</th>
					<th>Email</th>
					<th>&nbsp;</th>
					</tr></thead><tbody>' . $output . '</tbody></table>';
				echo $output;
			}
			wp_die();
		}

		function WLMUpdate_MembershipLevel() {
			$ret = $this->SaveMembershipLevels();
			if ( isset( $_POST['err'] ) && !empty( $_POST['err'] ) ) {
				echo $_POST['err'];
			}
			die();
		}

		function WLM_PayPerPost_Search() {
			$func = wlm_arrval($_GET, 'callback');
			if($func) {
				$data = array();
				$limit = sprintf('%d,%d', $_POST['page'] - 1, $_POST['page_limit']);
				$data['posts'] = $this->GetPayPerPosts(array('ID','post_title','post_type'), false, $_POST['search'], $limit, $total, $query);
				$data['total'] = $total;
				$data['query'] = $query;
				printf('%s(%s)', $func, json_encode($data));
			}
			die();
		}

		function DashboardFeeds() {
			$maxitems = 2;
			$defaults = array(
				'url' => 'http://feeds.feedburner.com/wishlistmembernews',
				'age' => 7,
				'dismiss' => 'dashboard_feed_dismissed',
			);

			$args = wp_parse_args( $_POST, $defaults );
			$rss = fetch_feed($args['url']);
			if (!is_wp_error($rss)) {
				$maxitems = $rss->get_item_quantity(1);
				$rss_items = $rss->get_items(0, $maxitems);
			}

			$dismiss_timestamp = $this->GetOption( $args['dismiss'] ) + 0;

			$date_now = strtotime("now");
			$rss_content = "";
			$results = array();
			if ($maxitems > 0) {
				// Loop through each feed item and display each item as a hyperlink.
				foreach ($rss_items as $item) {
					$timestamp = $item->get_date('U');
					$item_date = date(get_option('date_format'), $timestamp);
					$date_diff = $date_now - $timestamp;
					$date_diff = $date_diff/86400;
					//only show feeds less than 7 days old
					if($date_diff >= $args['age']) continue;
					if($timestamp <= $dismiss_timestamp) continue;

					$results[] = array(
						'title' => $item->get_title(),
						'content' => $item->get_content(),
						'description' => $item->get_description(),
						'permalink' => $item->get_permalink(),
					);
				}
			}
			wp_send_json($results);
		}

		/**
		* Adds WP Editor TinyMCE ligbox content
		*/
		function AddEditorLightBoxMarkup () {
			global $current_screen;
			if($current_screen->base != 'post') return;
			$page = isset( $_GET['page'] ) ? $_GET['page']: '';
			if ( current_user_can('edit_posts') && current_user_can('edit_pages') && is_admin() ) {
				wishlistmember_instance()->wlmshortcode->enqueue_shortcode_inserter_js();
				ob_start();
				include wishlistmember_instance()->pluginDir3 . '/helpers/tinymce_shortcode_inserter_lightbox.php';
				echo ob_get_clean();
			}
		}
		/**
		 * hook that adds additional levels
		 * if specified during integration
		 *
		 * used for upsells
		 */
		function Add_Additional_Levels() {
			$user = get_user_by('login', $_POST['username']);

			$additional_levels = $this->Get_UserMeta($user->ID, 'additional_levels');

			if (!is_array($additional_levels)) { // we assume $additional_levels is in simple CSV format if it's not an array
				$additional_levels = explode(',', $additional_levels);
				array_walk( $additional_levels, function(&$var) { $var = trim( $var ); } );
			}

			/*
			 * each additional level can be passed as a tab-delimited string
			 * containing level, transaction id and timestamp so we go through
			 * each additional level and check for those
			 */
			$transaction_ids = array();
			$timestamps = array();

			foreach ($additional_levels AS &$additional_level) {
				list($additional_level, $transaction_id, $timestamp) = explode("\t", $additional_level);
				if (trim($transaction_id))
					$transaction_ids[$additional_level] = trim($transaction_id);
				if (trim($timestamp))
					$timestamps[$additional_level] = trim($timestamp);
			}
			unset($additional_level);

			if (!empty($additional_levels)) {
				$this->ValidateLevels($additional_levels, null, null, null, true);
				if (!empty($additional_levels)) {
					$levels = array_merge($additional_levels, $this->GetMembershipLevels($user->ID));

					$this->SetMembershipLevels($user->ID, $levels, false, null, true, true, true);

					$default_txn = $this->GetMembershipLevelsTxnID($user->ID, $_POST['wpm_id']);
					$default_ts = $this->Get_UserLevelMeta($user->ID, $_POST['wpm_id'], 'timestamp');

					$txn = array();
					$ts = array();

					foreach ($additional_levels AS $level) {
						$txn[$level] = empty($transaction_ids[$level]) ? $default_txn : $transaction_ids[$level];
						$ts[$level] = empty($timestamps[$level]) ? $default_ts : $timestamps[$level];
					}

					$this->SetMembershipLevelTxnIDs($user->ID, $txn);
					$this->UserLevelTimestamps($user->ID, $ts);
				}
				$this->Delete_UserMeta($user->ID, 'additional_levels');
			}
		}

		function Remove_Pending_To_Add_Autoresponder($id, $level, $type) {
			foreach ($level as $levels) {
				//checks if there's a flag for pending autoresponders
				if ($this->Get_UserLevelMeta($id, $levels, 'autoresponder_add_pending_admin_approval') || $this->Get_UserLevelMeta($id, $levels, 'autoresponder_add_pending_email_confirmation')) {
					$this->Delete_UserLevelMeta($id, $levels, $type);

					//if all flags are clear, add the member to the autoresponder list...
					if (!$this->Get_UserLevelMeta($id, $levels, 'autoresponder_add_pending_admin_approval') && !$this->Get_UserLevelMeta($id, $levels, 'autoresponder_add_pending_email_confirmation')) {
						$usr = $this->Get_UserData($id);
						if ($usr->ID) {
							$this->ARSubscribe($usr->first_name, $usr->last_name, $usr->user_email, $levels);
						}
					}
				}
			}
		}

		function UnsubscribeExpired() {

			$unsubscribe_expired = $this->GetOption('unsubscribe_expired_members') ? $this->GetOption('unsubscribe_expired_members') : 0;
			if ($unsubscribe_expired) {

				$expired_members = $this->ExpiredMembersID();

				foreach($expired_members as $key => $expired_ids) {

					foreach($expired_ids as $expired_id) {

						$unsubscribe_expired = $this->Get_UserMeta($expired_id, 'unsubscribe_expired_members_processed');

						if($unsubscribe_expired) {
							$unsubscribe_expired = wlm_maybe_unserialize($unsubscribe_expired);

							if (!in_array($key, $unsubscribe_expired)) {

								$user = $this->Get_UserData($expired_id);
								$this->ARUnsubscribe($user->first_name, $user->last_name, $user->user_email, $key);

								$unsubscribe_expired[] = $key;

								$this->Update_UserMeta($expired_id, 'unsubscribe_expired_members_processed', wlm_maybe_serialize( (array) $unsubscribe_expired ));
							}

						} else {
							$user = $this->Get_UserData($expired_id);
							$this->ARUnsubscribe($user->first_name, $user->last_name, $user->user_email, $key);
							$this->Update_UserMeta($expired_id, 'unsubscribe_expired_members_processed', wlm_maybe_serialize( (array) $key ));
						}
					}

				}
			}
		}


		function PasswordHinting($error) {
				$user = get_user_by('login', $_POST['log']);
				$passwordhint = $this->Get_UserMeta($user->ID, 'wlm_password_hint');
                                $match_text=__("The password you entered for the username", 'wishlist-member');
				if ((trim($passwordhint) != "")) {
					if (preg_match("/".$match_text."/i", $error)) {
						$error .= "<br/ > <strong> ".__("Password Hint:","wishlist-member")." </strong>" . $passwordhint;
					}
				}
			return $error;
		}
		function PasswordHintingEmail() {
			echo '<script>
				jQuery(document).ready(function() {

				   //resize the login form
				   jQuery("#login").css("width", "340px");
				   //remove p tag wrap on the get new password button
				   jQuery("#wp-submit").unwrap();

					jQuery("#wlpasswordhintsubmit").click(function() {
						jQuery("#wlpasswordhintsubmit").attr("disabled", true).val("'.__("Sending Pass Hint....","wishlist-member").'");

						ajaxurl = "'.admin_url("admin-ajax.php").'";

						jQuery.post(
							ajaxurl,
							{
								action: "PasswordHintSubmit",
								user_login: jQuery("#user_login").val()
							},
							function(data,status){
								if(status!="success"){
									message = "'.__("Connection problem. Please check that you are connected to the internet.","wishlist-member").'";
								} else if(data.error!="ok") {
									alert(data.error);
									jQuery("#wlpasswordhintsubmit").attr("disabled", false).val("'.__("Send Password Hint","wishlist-member").'");
								} else {
									alert(data.message);
									jQuery("#wlpasswordhintsubmit").fadeOut();
								}
							},
							"json"
						);
						return false;
					});
				});

			</script>';
			echo '<input type="submit"  name="wlpasswordhintsubmit" id="wlpasswordhintsubmit" class="button button-large" value="'.__("Send Password Hint","wishlist-member").'" />';

		}

		function PasswordHintSubmit() {

			header( "Content-Type: application/json" );
			if ( strpos( $_POST['user_login'], '@' ) ) {
					$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
					if ( empty( $user_data ) ) {
						$error = __("There is no user registered with that email address.","wishlist-member");
					}
			} else {
					$login = trim($_POST['user_login']);
				$user_data = get_user_by('login', $login);
			}

			if ( !$user_data ) {
				$error = 'Invalid username or e-mail.';
			} else {
					$macros = array(
						'[passwordhint]' => trim($this->Get_UserMeta($user_data->data->ID, 'wlm_password_hint'))
					);

					if($macros['passwordhint']) {
						$error = __("The Username/Email you entered does not have a Password Hint.","wishlist-member");
					} else {
						$this->send_email_template('password_hint', $user_data->data->ID, $macros);
						$message = __("Successfully submitted password hint, please check your email.","wishlist-member");
						$error = __("ok","wishlist-member");
					}
			}

			$response = json_encode( array( 'error' => $error, 'message' => $message ) );
			echo $response;
			exit;
		}

		public function UserRegisteredCleanup($uid, $data) {
			global $wpdb;
			if ($this->GetOption('enable_short_registration_links') == 1) {
				$results = $wpdb->get_results("SELECT ID, `option_name`,`option_value` FROM `{$this->Tables->options}` WHERE `option_value` like '%||{$data['email']}'");
				foreach ($results as $r) {
					$wpdb->delete($this->Tables->options, array('ID' => $r->ID));
				}
			}
			$this->SyncMembership();
		}

		//Deletes user's saved search in the options table
		function WLMDeleteSavedSearch_Ajax(){
			if(isset($_POST['option_name']) && !empty($_POST['option_name'])) {
				$this->DeleteOption($_POST['option_name']);
			}
			exit;
		}

		/**
		 * Pre-upgrade checking
		 */
		function Upgrade_Check() {
			if ( !empty( $_GET['wlm3_rollback'] ) ) return;
			if (basename($_SERVER['SCRIPT_NAME']) == 'update.php' && $_GET['action'] == 'upgrade-plugin' && $_GET['plugin'] == $this->PluginFile) {
				$check_result = trim($this->ReadURL(add_query_arg('check', '1', $this->Plugin_Download_Url()), 10, true, true));
				if ($check_result != 'allowed') {
					header('Location: ' . $check_result);
					exit;
				}
			}
		}

		function update_protection_ajax() {
			@ini_set('zlib.output_compression', 1);

			$result = new stdClass;
			$result->success = 1;
			$result->data = new stdClass;

			$protection = $_POST['protection'] == 'Unprotected' ? 'N' : 'Y';

			$x_content_type = (bool) $_POST['manage_comments'] ? '~COMMENT' : $_POST['content_type'];

			switch($_POST['protection']) {
				case 'Unprotected':
				case 'Protected':
					$this->do_not_pass_protection = true;
					switch($_POST['content_type']) {
						case 'categories':
							$this->CatProtected($_POST['content_id'], $protection);
							break;
						case 'folders':
							$this->FolderProtected($_POST['content_id'], $protection);
							break;
						default:
							$this->SpecialContentLevel($_POST['content_id'], 'Protection', $protection, $x_content_type);
					}
					$this->SetContentLevels($x_content_type, $_POST['content_id'], $_POST['levels']);
					$this->SpecialContentLevel($_POST['content_id'], 'Inherit', 'N', $x_content_type);
					break;
				case 'Inherited':
					$this->inherit_protection($_POST['content_id'], $_POST['content_type'] == 'categories', (bool) $_POST['manage_comments']);
					break;
			}

			$result->data->protection = $_POST['protection'];

			switch($_POST['content_type']) {
				case 'categories':
					$result->data->padlock = (int) $this->CatProtected($_POST['content_id']);
					break;
				case 'folders':
					$result->data->padlock = (int) $this->FolderProtected($_POST['content_id']);
					break;
				default:
					$result->data->padlock = (int) $this->SpecialContentLevel($_POST['content_id'], 'Protection', null, $x_content_type);
			}

			$levels = $this->GetContentLevels($x_content_type, $_POST['content_id'], true, false, $immutable);

			$result->data->levels     = implode(', ', $levels);
			$result->data->immutable  = implode(',',$immutable);
			$result->data->level_keys = implode(',',array_keys($levels));

			if(!in_array($x_content_type,array('categories','folders','files','~COMMENT'))) {
				switch($_POST['payperpost']) {
					case 'Disabled':
						$this->PayPerPost($_POST['content_id'], 'N');
						break;
					case 'Paid':
						$this->PayPerPost($_POST['content_id'], 'Y');
						$this->Free_PayPerPost($_POST['content_id'], 'N');
						break;
					case 'Free':
						$this->PayPerPost($_POST['content_id'], 'Y');
						$this->Free_PayPerPost($_POST['content_id'], 'Y');
						break;
				}
				$result->data->payperpost = $_POST['payperpost'];
			}

			if('folders' == $_POST['content_type']) {
				switch($_POST['forcedownload']) {
					case 'Yes':
						$this->FolderForceDownload($_POST['content_id'], true);
						break;
					case 'No':
						$this->FolderForceDownload($_POST['content_id'], false);
						break;
				}
				$result->data->forcedownload = $_POST['forcedownload'];
			}

			if(is_array($_POST['post_users'])) {
				$post_users = $_POST['post_users'];
				foreach($post_users AS $key => $value) {
					if(!preg_match('/^U-\d+$/', $value)) {
						unset($post_users[$key]);
					}
				}

				$remove = array_diff(
					$orig = $this->GetPostUsers($_POST['content_type'], $_POST['content_id']),
					$post_users
				);

				if($remove) {
					$this->RemovePostUsers($_POST['content_type'], $_POST['content_id'], $remove);
				}
				if($post_users) {
					$this->AddPostUsers($_POST['content_type'], $_POST['content_id'], $post_users);
				}

				$result->data->post_users = $this->count_post_users($_POST['content_id'], $_POST['content_type']);
			}

			$this->pass_protection( $_POST['content_id'], $_POST['content_type'] == 'categories' );

			echo json_encode($result);
			wp_die();
		}

		function get_ppp_users_ajax() {
			@ini_set('zlib.output_compression', 1);
			$post_id = (int) wlm_arrval($_POST, 'post_id');
			$post_type = get_post_type($post_id);
			if(empty($post_type)) {
				echo json_encode(array('success' => 0));
			} else {
				$users = str_replace('U-', '', array_values($this->GetPostUsers($post_type, $post_id)));
				if(!empty($users)) {
					$filter = array(
						'fields' => array('ID', 'user_login', 'user_email', 'display_name'),
						'include' => $users
					);
					$users = get_users($filter);
				}
				echo json_encode(array('success' => 1, 'data' => $users));
			}
			wp_die();
		}

		function contenttab_bulk_action_ajax() {
			@ini_set('zlib.output_compression', 1);
			$success = 0;
			$data = array();
			$msg = '';
			$x_content_type = (bool) $_POST['manage_comments'] ? '~COMMENT' : $_POST['content_type'];
			$bulk_action = wlm_arrval($_POST, 'bulk_action');

			switch($bulk_action) {
				case 'protection':
					$protection = $_POST['bulk_action_value'] == 'Unprotected' ? 'N' : 'Y';
					$data = array();
					foreach($_POST['content_ids'] AS $content_id) {
						switch($_POST['bulk_action_value']) {
							case 'Unprotected':
							case 'Protected':
								switch($_POST['content_type']) {
									case 'categories':
										$this->CatProtected($content_id, $protection);
										break;
									case 'folders':
										$this->FolderProtected($content_id, $protection);
										$data[$content_id]['htaccess']='ok';
										break;
									default:
										$this->SpecialContentLevel($content_id, 'Protection', $protection, $x_content_type);
								}
								$this->SpecialContentLevel($content_id, 'Inherit', 'N', $x_content_type);
								$data[$content_id]['padlock'] = $_POST['bulk_action_value'] == 'Protected' ? 1 : 0;
								break;
							case 'Inherited':
								$this->inherit_protection($content_id, $_POST['content_type'] == 'categories', (bool) $_POST['manage_comments'], $new_protect, $new_levels);
								$data[$content_id]['padlock'] = $new_protect ? 1 : 0;
								$new_levels = $this->level_ids_to_level_names($new_levels);
								$data[$content_id]['new_level_keys'] = empty($new_levels) ? '' : array_keys($new_levels);
								$data[$content_id]['new_levels'] = empty($new_levels) ? '&nbsp;&mdash;' : implode(', ', $new_levels);
								break;
						}
						$data[$content_id]['label'] = $_POST['bulk_action_value'];
					}
					$success = 1;
					$msg = sprintf(__('Protection status set to "%s" for selected items', 'wishlist-member'), $_POST['bulk_action_value']);
				break;
				case 'add_levels':
				case 'remove_levels':
					$the_levels = (array) $_POST['bulk_action_value'];
					foreach($_POST['content_ids'] AS $content_id) {
						if(!$this->SpecialContentLevel($content_id, 'Inherit', null, $x_content_type)) {
							$current_levels = $this->GetContentLevels($x_content_type, $content_id, true, false, $immutable);
							$the_levels = array_diff($the_levels, $immutable);
							if($bulk_action == 'add_levels') {
								$new_levels = array_unique(array_merge(array_keys($current_levels), $the_levels));
								$current_levels = $current_levels + $this->level_ids_to_level_names($the_levels);

							} else {
								$new_levels = array_diff(array_keys($current_levels), $the_levels);
								$current_levels = array_diff_key($current_levels, array_flip($the_levels));
							}
							$this->SetContentLevels($x_content_type, $content_id, array_merge($new_levels, $immutable));

							$data[$content_id]['new_level_keys'] = empty($current_levels) ? '' : array_keys($current_levels);
							$data[$content_id]['new_levels'] = empty($current_levels) ? '&nbsp;&mdash;' : implode(', ', $current_levels);
							$data[$content_id]['immutable'] = implode(',', $immutable);
						}
					}

					$the_levels = $this->level_ids_to_level_names($the_levels);
					$success = 1;
					$msg = sprintf(__("The following membership levels were %s the selected items: %s", 'wishlist-member'), $bulk_action == 'add_levels' ? 'ADDED to' : 'REMOVED from', implode(', ', $the_levels));
				break;
				case 'ppp':
					$data = array();
					foreach($_POST['content_ids'] AS $content_id) {
						switch($_POST['bulk_action_value']) {
							case 'Disabled':
								$this->PayPerPost($content_id, 'N');
							break;
							case 'Paid':
								$this->PayPerPost($content_id, 'Y');
								$this->Free_PayPerPost($content_id, 'N');
							break;
							case 'Free':
								$this->PayPerPost($content_id, 'Y');
								$this->Free_PayPerPost($content_id, 'Y');
							break;
						}
						$data[$content_id] = $_POST['bulk_action_value'];
					}
					$success = 1;
					$msg = sprintf(__('Pay Per Post status set to "%s" for selected items', 'wishlist-member'), $_POST['bulk_action_value']);
				break;
				case 'pppusers':
					$add     = wlm_arrval($_POST, 'ppp_add');
					$remove  = wlm_arrval($_POST, 'ppp_remove');
					$data    = array();
					foreach($_POST['content_ids'] AS $content_id) {
						if(count($remove)) {
							$this->RemovePostUsers($_POST['content_type'], $content_id, $remove);
						}
						if(count($add)) {
							$this->AddPostUsers($_POST['content_type'], $content_id, $add);
						}
						$data[$content_id] = $this->count_post_users( $content_id, $_POST['content_type'] );
					}
					$success = 1;
					$msg = sprintf(__('Pay Per Post Users updated for selected items', 'wishlist-member'), $_POST['bulk_action_value']);
				break;
				case 'force_download':
					$data = array();
					foreach($_POST['content_ids'] AS $content_id) {
						$this->FolderForceDownload($content_id, $_POST['bulk_action_value'] == 'Yes');
						$data[$content_id] = $_POST['bulk_action_value'];
					}
					$success = 1;
					$msg = sprintf(__('Force download %s for selected folders', 'wishlist-member'), $_POST['bulk_action_value'] == 'Yes' ? 'enabled' : 'disabled');
				break;
				default:
					$msg = 'Invalid bulk action';
			}
			echo json_encode(array('success' => $success, 'msg' => $msg, 'data' => $data));
			wp_die();
		}

		function wlm_unschedule_single() {
			$level = wlm_arrval($_POST, 'level');
			$user = wlm_arrval($_POST, 'user');
			switch (wlm_arrval($_POST, 'schedule_type')) {
				case 'remove':
					$this->Delete_UserLevelMeta($user, $level, 'scheduled_remove');
				break;
				case 'cancel':
					$this->Delete_UserLevelMeta($user, $level, 'wlm_schedule_level_cancel');
					$this->Delete_UserLevelMeta($user, $level, 'schedule_level_cancel_reason');
				break;
				case 'add':
				case 'move':
					$levels = array_diff((array) $this->GetMembershipLevels($user), array($level));
					$this->SetMembershipLevels($user, $levels);
				break;
			}
		}

		function Widget($args = array(), $return = false) {
			$args = (array) $args;
			if($return) {
				$args['return'] = true;
			}
			$x = new WishListWidget;
			if($return) {
				return $x->widget($args, array());
			} else {
				$x->widget($args, array());
			}
		}

		// Added in WLM 2.9, this will update the
		// previous WLM widget (registered through wp_register_sidebar_widget) to the new WLMWidget Class
		// if it's currently active onthe clients widgets
		function MigrateWidget() {

			$active_widgets = get_option( 'sidebars_widgets' );

			foreach( (array) $active_widgets as $widget => $values) {
				if ($widget != 'array_version') {

					$counter = 0;
					foreach( (array) $values as $value) {
						if($value == 'wishlist-member') {
							$active_widgets[ $widget ][$counter] = 'wishlistwidget-' . 1;
							$wlm_widget_content[1] = array (
							    'title'        => $this->GetOption('widget_title'),
							    'title2'          => $this->GetOption('widget_title2'),
							    'wpm_widget_hiderss'         => $this->GetOption('widget_hiderss'),
							    'wpm_widget_hideregister'        => $this->GetOption('widget_hideregister'),
							    'wpm_widget_nologinbox' => $this->GetOption('widget_nologinbox'),
							    'wpm_widget_hidelevels'  => $this->GetOption('widget_hidelevels'),
							    'wpm_widget_fieldwidth'    => $this->GetOption('widget_fieldwidth'),
							);
							update_option( 'widget_wishlistwidget', $wlm_widget_content );
						}
						$counter++;
					}
				}
			}
			update_option( 'sidebars_widgets', $active_widgets );
		}

		function Run_FileProtect_Migration() {
			wlm_set_time_limit(0);
			$old_protect = (array) $this->GetOption('FileProtect');
			$api_queue = new WishlistAPIQueue;
			$queue = $api_queue->get_queue('file_protect_migrate');
			foreach($queue AS $q) {
				$v = unserialize($q->value);
				if(is_array($v) && count($v) == 2) {
					list($action, $file_attachment_id) = $v;
					switch($action) {
						case 'inherit':
							$this->inherit_protection($file_attachment_id);
						break;
						case 'set':
							$levels = array();
							foreach ( array_keys( $old_protect ) AS $level ) {
								if(in_array($file_attachment_id, (array) $old_protect[$level])){
									if($level == 'Protection') {
										$this->Protect($file_attachment_id, 'Y');
									}else{
										$levels[] = $level;
									}
								}
							}
							$this->SetContentLevels( 'attachment', $file_attachment_id, $levels );
						break;
					}
				}
				$api_queue->delete_queue($q->ID);
			}
		}

		function shortcodes_init() {
			//get levels
			$wpm_levels = $this->GetOption('wpm_levels');

			//shortcodes array
			$wlm_shortcodes = array(
				'Member' => array(
					array('title' => 'First Name', 'value' => '[wlm_firstname]'),
					array('title' => 'Last Name', 'value' => '[wlm_lastname]'),
					array('title' => 'Email', 'value' => '[wlm_email]'),
					array('title' => 'Username', 'value' => '[wlm_username]'),
				),
				'Access' => array(
					array('title' => 'Membership Levels', 'value' => '[wlm_memberlevel]'),
					array('title' => 'Pay Per Posts', 'value' => '[wlm_userpayperpost sort="ascending"]'),
					array('title' => 'RSS Feed', 'value' => '[wlm_rss]'),
					array('title' => 'Content Levels', 'value' => '[wlm_contentlevels type="comma" link_target="_blank" class="wlm_contentlevels" show_link="1" salespage_only="1"]'),
				),
				'Login' => array( 
					array('title' => 'Login Form', 'value' => '[wlm_loginform]'),
					array('title' => 'Login URL', 'value' => '[wlm_loginurl]'),
					array('title' => 'Log out URL', 'value' => '[wlm_logouturl]')
				),
				'Profile' => array(
					array('title' => 'Profile Form', 'value' => '[wlm_profileform hide_mailinglist=no]'),
					array('title' => 'Profile URL', 'value' => '[wlm_profileurl]'),
				),
			);
			
			if( $wpm_levels ) {
				$wlm_shortcodes['Join Date'] = array();
				$wlm_shortcodes['Expiration Date'] = array();
				foreach ((array) $wpm_levels AS $level) {
					if (strpos($level['name'], '/') === false) {
						$wlm_shortcodes['Join Date'][] = array('title' => "{$level['name']}", 'value' => "[wlm_joindate {$level['name']}]");
						$wlm_shortcodes['Expiration Date'][] = array('title' => "{$level['name']}", 'value' => "[wlm_expiration {$level['name']}]");
					}
				}
			}

			$wlm_shortcodes['Address'] = array(
				array('title' => 'Company', 'value' => '[wlm_company]'),
				array('title' => 'Address', 'value' => '[wlm_address]'),
				array('title' => 'Address 1', 'value' => '[wlm_address1]'),
				array('title' => 'Address 2', 'value' => '[wlm_address2]'),
				array('title' => 'City', 'value' => '[wlm_city]'),
				array('title' => 'State', 'value' => '[wlm_state]'),
				array('title' => 'Zip', 'value' => '[wlm_zip]'),
				array('title' => 'Country', 'value' => '[wlm_country]'),
			);

			//custom fields shortcode
			$custom_fields = $this->GetCustomFieldsMergeCodes();
			$this->custom_fields_merge_codes = $custom_fields ?: array();
			if ( count( $custom_fields ) ) {
				$wlm_shortcodes['Custom Fields'] = array();
				foreach ( $custom_fields AS $custom_field ) {
					$wlm_shortcodes['Custom Fields'] = array('title' => $custom_field, 'value' => $custom_field);
				}
			}
			
			$wlm_shortcodes['Other'] = array(
				array('title' => 'Website', 'value' => '[wlm_website]'),
				array('title' => 'AOL Instant Messenger', 'value' => '[wlm_aim]'),
				array('title' => 'Yahoo Instant Messenger', 'value' => '[wlm_yim]'),
				array('title' => 'Jabber', 'value' => '[wlm_jabber]'),
				array('title' => 'Biography', 'value' => '[wlm_biography]'),
			);
				

			if( \WishListMember\Level::any_can_autocreate_account_for_integration() ) {
				$wlm_shortcodes[] = array('title' => 'Auto-generated Password', 'value' => '[wlm_autogen_password]');
			}

			//mergecodes array
			$wlm_mergecodes[] = array('title' => 'Is Member', 'value' => '[wlm_ismember]', 'type'=>"merge");
			$wlm_mergecodes[] = array('title' => 'Non-Member', 'value' => '[wlm_nonmember]', 'type'=>"merge");
			$wlm_mergecodes[] = array('title' => "Private Tags", 'value' => "", 'jsfunc'=>"wlmtnmcelbox_vars.show_private_tags_lightbox" );

			// reg form shortcodes
			$wlm_mergecodes[] = array('title' => "Registration Forms", 'value' => "", 'jsfunc'=>"wlmtnmcelbox_vars.show_reg_form_lightbox" );

			// $wlm_mergecodes are actually called Shortcodes
			$this->short_codes = $wlm_mergecodes = apply_filters( 'wlm_mergecodes', $wlm_mergecodes );
			// $wlm_shortcodes are actually called Mergecodes
			$this->merge_codes = $wlm_shortcodes = apply_filters( 'wlm_shortcodes', $wlm_shortcodes );

			$wlmshortcode_role_access = $this->GetOption("wlmshortcode_role_access");
			$wlmshortcode_role_access = $wlmshortcode_role_access === false ? false : $wlmshortcode_role_access;
			$wlmshortcode_role_access = is_string( $wlmshortcode_role_access ) ? array() : $wlmshortcode_role_access;
			if ( is_array( $wlmshortcode_role_access ) ) {
				$wlmshortcode_role_access[] = "administrator";
				$wlmshortcode_role_access = array_unique( $wlmshortcode_role_access );
			} else {
				$wlmshortcode_role_access = false;
			}

			if (!isset($_GET['page']) || $this->MenuID != $_GET['page']) {
				// Don't initiate tinymce (shortcode inserter) on admin-ajax.php to avoid conflicts with profile builders
				if(basename($_SERVER['PHP_SELF']) != 'admin-ajax.php') {
					global $WLMTinyMCEPluginInstanceOnly;
					if (!isset($WLMTinyMCEPluginInstanceOnly)) { //instantiate the class only once
						$WLMTinyMCEPluginInstanceOnly = new WLMTinyMCEPluginOnly( $wlmshortcode_role_access );
						add_action('admin_init', array(&$WLMTinyMCEPluginInstanceOnly, 'TNMCE_PluginJS'), 1);
					}
					$WLMTinyMCEPluginInstanceOnly->RegisterShortcodes("Mergecodes", array(), array(), 0, null, $wlm_shortcodes );
					$WLMTinyMCEPluginInstanceOnly->RegisterShortcodes("Shortcodes", array(), array(), 0, null, $wlm_mergecodes );
					if ( count( $this->IntegrationShortcodes ) > 0 ) {
						$WLMTinyMCEPluginInstanceOnly->RegisterShortcodes("Integrations", array(), array(), 0, null, $this->IntegrationShortcodes );
					}
				}
			}

			// $this->integration_shortcodes(); //lets try to load it above
		}

		function integration_shortcodes() {
			//register tinymce plugin for integrations
			global $WLMTinyMCEPluginInstanceOnly;
			if ( $WLMTinyMCEPluginInstanceOnly && count( $this->IntegrationShortcodes ) > 0 ) {
				$WLMTinyMCEPluginInstanceOnly->RegisterShortcodes("Integrations", array(), array(), 0, null, $this->IntegrationShortcodes );
			}
		}

		function IntegrationErrors() {
			if(!empty($this->integration_errors)) {
				$ActiveShoppingCarts = (array) $this->GetOption('ActiveShoppingCarts');
				foreach((array) $this->integration_errors AS $key => $error) {
					if(in_array($key, $ActiveShoppingCarts)) {
						$show_error = true;
						if( wlm_arrval( $_GET, 'page' ) == 'WishListMember' && wlm_arrval( $_GET, 'wl' ) == 'integration' ) {
							$show_error = false;
						} else {
							if( ! empty( $this->active_integration_indicators[$key] ) && is_array( $this->active_integration_indicators[$key] ) ) {
								foreach( $this->active_integration_indicators[$key] AS $option ) {
									$show_error = $show_error & ( (bool) $this->GetOption( $option ) );
								}
							}
						}
						if( $show_error ) {
							printf( '<div class="error">%s</div>', $error );
						}
					}
				}
			}
		}

		function _privacy_user_request_mergecodes( $data ) {
			$user = $this->Get_UserData( $data['email'] );
			if(!$user) $user = $data['message_recipient'];
			if( ! $user ) return false;

			$merge_codes = array (
				'[firstname]' => $user->first_name,
				'[lastname]' => $user->last_name,
				'[username]' => $user->user_login,
				'[email]' => $data['email'] ? $data['email'] : $user->user_email,
				'[request]' => $data['description'] ? $data['description'] : '###DESCRIPTION###',
				'[expiration]' => $data['expiration'] ? $data['expiration'] : '###EXPIRATION###',
				'[link]' => $data['link'] ? $data['link'] : '###LINK###',
			);

			foreach($data AS $key => $value) {
				if (! is_object($value) ) {
					$merge_codes['[' . $key . ']'] = sprintf( '%s', $value );
				}

			}
			return $merge_codes;
		}

		function _privacy_process_email_template( $content, $template, $data ) {
			$merge_codes = $this->_privacy_user_request_mergecodes ( $data );
			if( ! $merge_codes ) return $content;

			$content = str_replace( array_keys( $merge_codes ), array_values( $merge_codes ), $template );
			$this->SendingMail = true;
			return $content;
		}

		function privacy_user_request_email_subject( $content, $blogname, $data ) {
			$template = trim( $this->GetOption( 'privacy_email_template_request_subject' ) );
			if( ! $template ) return $content;
			return $this->_privacy_process_email_template( $content, $template, $data );
		}
		function privacy_user_request_email( $content, $data ) {
			$template = trim( $this->GetOption( 'privacy_email_template_request' ) );
			if( ! $template ) return $content;
			return $this->_privacy_process_email_template( $content, $template, $data );
		}

		function privacy_user_delete_email( $content, $data ) {
			$template = trim( $this->GetOption( 'privacy_email_template_delete' ) );
			if( ! $template ) return $content;
			$this->MailSubject = $this->_privacy_process_email_template( '', trim( $this->GetOption( 'privacy_email_template_delete_subject' ) ), $data );
			return $this->_privacy_process_email_template( $content, $template, $data );
		}

		function privacy_personal_data_email( $content, $request_id ) {
			$template = trim( $this->GetOption( 'privacy_email_template_download' ) );
			if( ! $template ) return $content;
			$request = wp_get_user_request_data( $request_id );
			if( ! $request ) return $content;

			$data = array(
				'email' => $request->email
			);
			$this->MailSubject = $this->_privacy_process_email_template( '', trim( $this->GetOption( 'privacy_email_template_download_subject' ) ), $data );
			return $this->_privacy_process_email_template( $content, $template, $data );
		}

		function register_privacy_personal_data_eraser( $erasers ) {
			$erasers['wishlist-member-user'] = array(
				'eraser_friendly_name' => __( 'WishList Member', 'wishlist-member'),
				'callback'             => array( $this, 'privacy_personal_data_eraser' ),
			);

			return $erasers;
		}

		function privacy_personal_data_eraser( $email_address ) {
			$user = get_user_by( 'email', $email_address );

			if( ! $user ) {
				return new WP_Error( 'wlm-personal-data-eraser_invalid-email', __( 'Invalid email address', 'wishlist-member') );
			}

			$messages = array();
			$items_removed = 0;

			// delete last login date
			$this->Delete_UserMeta( $user->ID, 'wpm_login_date' );
			// $messages[] = __( 'Last login date erased', 'wishlist-member' );
			$items_removed++;
			// delete last login IP
			$this->Delete_UserMeta( $user->ID, 'wpm_login_ip' );
			// $messages[] = __( 'Last login IP erased', 'wishlist-member' );
			$items_removed++;
			// delete registration IP
			$this->Delete_UserMeta( $user->ID, 'wpm_registration_ip' );
			// $messages[] = __( 'Registration IP erased', 'wishlist-member' );
			$items_removed++;
			// delete login counter
			$this->Delete_UserMeta( $user->ID, 'wpm_login_counter' );
			// $messages[] = __( 'Login counter erased', 'wishlist-member' );
			$items_removed++;

			return array(
				'items_removed' => $items_removed,
				'items_retained' => 0,
				'messages'  => $messages,
				'done' => true
			);
		}

		function register_privacy_personal_data_exporter( $exporters ) {
			$exporters['wishlist-member-user'] = array(
				'exporter_friendly_name' => __( 'WishList Member Data', 'wishlist-member'),
				'callback' => array( $this, 'privacy_personal_data_exporter' ),
			);

			return $exporters;
		}

		function privacy_personal_data_exporter( $email_address ) {
			$data_to_export = array();
			$user_data_to_export = array();
			$user_levels_to_export = array();
			$user_ppps_to_export = array();

			$user = new \WishListMember\User( $email_address, true );
			unset( $user->WL );
			if( ! $user ) {
				return array(
					'data' => $user,
					'done' => true,
				);
			}

			$user = json_decode( json_encode ( $user ), true );

			/* User Data */
			$names = array(
				'wlm_feed_url' => __( 'Private RSS Feed', 'wishlist-member' ),
				'wpm_registration_ip' => __( 'Registration IP Address', 'wishlist-member' ),
				'custom_terms_of_service' => __( 'Terms of Service', 'wishlist-member' ),
				'wpm_login_date' => __( 'Last Login Date', 'wishlist-member' ),
				'wpm_login_ip' => __( 'Last Login IP', 'wishlist-member' ),
				'wpm_useraddress' => __( 'Address', 'wishlist-member' ),
				'sequential' => __( 'Sequential Upgrade', 'wishlist-member' ),
				'custom_stripe_cust_id' => __( 'Stripe ID', 'wishlist-member' ),
				'stripe_cust_id' => __( 'Stripe ID', 'wishlist-member' ),
				'eway_cust_id' => __( 'eWay ID', 'wishlist-member' ),
				'wlminfusionsoft_contactid' => __( 'Infusionsoft ID', 'wishlist-member' ),
				'wlm_password_hint' => __( 'Password Hint', 'wishlist-member' ),
			);

			foreach( $names AS $key => $name) {
				$value = $user['UserInfo']['data'][$key];
				switch( $key ) {
					case 'wpm_login_date':
						$value = $value > 1 ? date( 'Y-m-d H:i:s', $value ) : '';
						break;
					case 'wpm_useraddress':
						if( ! is_array( $value ) ) {
							$value = array();
						}
						foreach($value AS &$v) {
							$v = trim( $v );
						}
						unset( $v );
						if( $value['country'] == 'Select Country' ) {
							$value['country'] = '';
						}
						$value = trim( preg_replace( '/\n+/', '<br>', implode( "\n", $value ) ) );
						break;
					case 'custom_terms_of_service':
						$value = $value ? __( 'Accepted', 'wishlist-member' ) : '';
						break;
					default:
						$value = trim( $value );
				}
				if( $value ) {
					$user_data_to_export[] = array(
						'name' => $name,
						'value' => $value
					);
				}
			}

			$fields = array_diff_key( $user['UserInfo']['data']['wldata'], $names );
			if( $fields ) {
				foreach( $fields AS $name => $value ) {
					if( ! preg_match( '/^custom_/', $name ) ) continue;
					if( is_array( $value ) ) {
						$value = implode( "<br>", $value );
					}

					if( ! is_scalar( $value ) ) continue;

					if( $value ) {
						$user_data_to_export[] = array(
							'name' => ucwords( strtolower ( preg_replace( '/[^A-Za-z0-9]+/', ' ', preg_replace( '/^custom_/', '', $name ) ) ) ),
							'value' => $value
						);
					}
				}
			}

			$data_to_export[] = array(
				'group_id' => 'wishlist-member-user',
				'group_label' => __( 'WishList Member User Data', 'wishlist-member'),
				'item_id' => "wlm-user-{$user->ID}",
				'data' => $user_data_to_export,
			);

			/* Membership Levels */
			if( $user['Levels'] ) {
				foreach( $user['Levels'] AS $level_id => $level ) {
					if( $level['Active'] ) {
						$value = sprintf( "%s : %s<br>%s : %s", __( 'Transaction ID', 'wishlist-member' ), $level['TxnID'], __( 'Registration Date', 'wishlist-member' ), date( 'Y-m-d H:i:s', $level['Timestamp'] ) );
						if( $level['ExpiryDate'] ) {
							$value .= sprintf("<br>%s : %s", __( 'Expiration Date', 'wishlist-member' ), date('Y-m-d H:i:s', $level['ExpiryDate'] ) );
						}
						$user_levels_to_export[] = array(
							'name' => $level['Name'],
							'value' => $value
						);
					}
				}

				if( $user_levels_to_export ) {
					$data_to_export[] = array(
						'group_id' => 'wishlist-member-user-levels',
						'group_label' => __( 'WishList Member Levels', 'wishlist-member'),
						'item_id' => "wlm-userlevels-{$user->ID}",
						'data' => $user_levels_to_export,
					);
				}
			}

			/* Pay Per Posts */
			if( $user['PayPerPosts'] ) {
				foreach( $user['PayPerPosts']['_all_'] AS $post_id ) {
					$user_ppps_to_export[] = array(
						'name' => __( 'URL', 'wishlist-member' ),
						'value' => get_permalink( $post_id )
					);
				}
				if( $user_ppps_to_export ) {
					$data_to_export[] = array(
						'group_id' => 'wishlist-member-user-ppps',
						'group_label' => __( 'WishList Member Pay Per Posts', 'wishlist-member'),
						'item_id' => "wlm-userppps-{$user->ID}",
						'data' => $user_ppps_to_export,
					);
				}
			}

			return array(
				'data' => $data_to_export,
				'done' => true,
			);
		}

		function reset_privacy_template() {
			require 'core/InitialValues.php';

			$target = wlm_arrval( $_POST, 'target' );
			$valid_targets = array(
				'privacy_email_template_request',
				'privacy_email_template_download',
				'privacy_email_template_delete',
				'member_unsub_notification_body',
			);

			$template_names = array(
				__( 'User Request Email Template', 'wishlist-member' ),
				__( 'Download Fulfilled Email Template', 'wishlist-member' ),
				__( 'Erasure Fulfilled Email Template', 'wishlist-member' ),
				__( 'Unsubscribe Notification Email Template', 'wishlist-member' ),
			);
			$template_names = array_combine( $valid_targets, $template_names );

			if( ! in_array( $target, $valid_targets ) ) {
				return;
			}

			$subject = preg_replace( '/_body$/', '', $target ) . '_subject';

			$this->SaveOption( $target, $WishListMemberInitialData[$target] );
			$this->SaveOption( $subject, $WishListMemberInitialData[$subject] );

			$_POST = array( 'msg' => sprintf( '%s %s', $template_names[$target], __( 'was reset.', 'wishlist-member') ) );
		}
	}

}
