<?php

/**
 * Core Class for WishList Member 3.0
 * @author Mike Lopez <mike@wishlistproducts.com>
 * @package wishlistmember3
 *
 * @version $Rev$
 * $LastChangedBy$
 * $LastChangedDate$
 */
if (!defined('ABSPATH'))
	die();
if (!class_exists('WishListMember3_Core')) {
	/**
	 * Core Class for WishList Member 3.0
	 * @package wishlistmember3
	 * @subpackage classes
	 */
	class WishListMember3_Core extends WishListMember {
		
		/**
		 * Overloaded instance methods from other objects
		 * @var array
		 */
		private $instance_methods = array();

		/**
		 * Overloaded Constructor of Class.php
		 * Initialize main plugin variables
		 */
		function constructor3($pluginfile, $sku, $menuid, $title, $link) {
			global $wpdb;
			global $wp;

			// todo remove this in 3.1
			if( isset( $_GET['page'] ) && $_GET['page'] == 'WishListMember3' ) {
				header( 'Location: ' . add_query_arg( 'page', $menuid ) );
				exit;
			}

			require_once(ABSPATH . '/wp-admin/includes/plugin.php');

			$this->scripts = [];
			$this->styles = [];

			$this->ProductSKU = $sku;
			$this->MenuID = $menuid;
			$this->Title = $title;
			$this->Link = $link;
			$this->Menus = array();

			$this->BlogCharset 		= get_option('blog_charset');
			$this->TablePrefix 		= $wpdb->prefix . 'wlm_';
			$this->OptionsTable 	= $this->TablePrefix . 'options';
			$this->PluginOptionName = 'WishListMemberOptions';

			$this->Version 			= WLM3_PLUGIN_VERSION;
			$this->WPVersion 		= $GLOBALS['wp_version'];

			$this->pluginPath 		= $pluginfile;
			$this->pluginDir3 		= dirname($this->pluginPath);
			$this->PluginFile 		= basename(dirname($pluginfile)) . '/' . basename($pluginfile);
			$this->PluginSlug 		= sanitize_title_with_dashes( WLM3_PLUGIN_NAME );
			$this->pluginBasename 	= plugin_basename($this->pluginPath);
			$this->pluginURL3 		= plugins_url('', '/') . basename($this->pluginDir3);

			// Path to Legacy WLM
			$this->legacy_wlm_dir = $this->pluginDir3 . '/legacy';
			$this->legacy_wlm_url = $this->pluginURL3 . '/legacy';

			// $this->pagination_items	= array(10,25,50,100,250,500);
			$this->pagination_items	= array(10,25,50,100,200,"Show All");

			$this->xhr = new WishListXhr($this);
			$this->emailbroadcast = new WishListEmailBroadcast;
			$this->wlmshortcode = new \WishListMember\Shortcodes;
			//content control
			$this->content_control = new WLM3_ContentControl($this);

			$this->_url_label_markup = '%1$s &nbsp; <a href="" class="wlm-popover copy-url clipboard">Copy URL</a>';

			$this->copy_command = sprintf(__('Press %s-C to copy', 'wishlist-member'), (strpos($_SERVER['HTTP_USER_AGENT'], 'Mac OS X') ? 'Command' : 'Ctrl'));


			$this->page_templates = array();
			$page_templates = glob($this->pluginDir . '/resources/page_templates/*.php');
			if($page_templates) {
				foreach($page_templates AS $page_template) {
					$template = preg_replace('/\.php$/', '', basename($page_template));
					include($page_template);
					$this->page_templates[$template] = trim( $content );
				}
			}

			include $this->pluginDir3 . '/helpers/level_email_defaults.php';
			$this->level_email_defaults = $level_email_defaults;

			include $this->pluginDir3 . '/helpers/level_defaults.php';
			$this->level_defaults = array_merge( $level_defaults, $level_email_defaults );

			include $this->pluginDir3 . '/helpers/ppp_email_defaults.php';
			$this->ppp_email_defaults = $ppp_email_defaults;

			include $this->pluginDir3 . '/helpers/ppp_defaults.php';
			$this->ppp_defaults = $ppp_defaults;

			ob_start();

			$this->LoadTables();

			// load preload files for all integrations
			foreach(array('payments', 'emails', 'others') AS $integration_type) {
				$dir = $this->pluginDir3 . '/integrations/' . $integration_type . '/*';
				$integrations = glob($dir . '*', GLOB_ONLYDIR | GLOB_MARK);
				foreach($integrations AS $integration) {
					$preload = $integration . 'preload.php';
					if(file_exists($preload)) {
						include_once($preload);
					}
				}
			}

			$this->js_date_format = $this->php2js_date_format( get_option( 'date_format' ) );
			$this->js_time_format = $this->php2js_date_format( get_option( 'time_format' ) );
			$this->js_datetime_format = $this->js_date_format . ' ' . $this->js_time_format;

			// we want to make sure that we have the necessary default data for the current version
			$cver = $this->GetOption('CurrentVersion');

			// if CurrentVersion is empty, then we assume it's the first time WLM is installed
			if(empty($cver)) {
				$this->first_install();
			}

			// this block runs when the version number changes
			if ($cver != $this->Version) {
				$this->version_changed( $cver, $this->Version );
			}

			// migrate / update pay per post settings
			$ppp_settings = $this->GetOption( 'payperpost' );
			if( !is_array( $ppp_settings ) ) $ppp_settings = [];

			$old_afterregredirect = count($ppp_settings) && !isset($ppp_settings['afterreg_redirect_type']); // 2.9 doesn't have this property
			$old_loginredirect = count($ppp_settings) && !isset($ppp_settings['login_redirect_type']); // 2.9 doesn't have this property

			$ppp_settings = array_merge( $this->ppp_defaults, $ppp_settings );

			// migrate settings for per level after reg redirect page
			if( $old_afterregredirect && wlm_arrval($ppp_settings, 'afterregredirect') && $ppp_settings['afterregredirect'] != '---' && is_null( $ppp_settings['custom_afterreg_redirect'] ) ) {
				$ppp_settings['custom_afterreg_redirect'] = 1;
				$ppp_settings['afterreg_redirect_type'] = 'page';
				$ppp_settings['afterreg_page'] = $ppp_settings['afterregredirect'];
			}

			// migrate settings for per level after reg redirect page
			if( $old_loginredirect && wlm_arrval($ppp_settings, 'loginredirect') && $ppp_settings['loginredirect'] != '---' && is_null( $ppp_settings['custom_login_redirect'] ) ) {
				$ppp_settings['custom_login_redirect'] = 1;
				$ppp_settings['login_redirect_type'] = 'page';
				$ppp_settings['login_page'] = $ppp_settings['loginredirect'];
			}
			$this->SaveOption( 'payperpost', $ppp_settings );

			$pd = basename($this->pluginDir3) . '/lang';
			load_plugin_textdomain('wishlist-member',false,$pd);
		}


		/**
		 * This method gets called on the first install of WishList Member
		 * Note: That this is not called on upgrade
		 */
		function first_install() {
			// set form version to improved
			$this->SaveOption( 'FormVersion', 'themestyled' );

			// paypal smart payment buttons
			$spb = $this->GetOption('paypalec_spb');
			$spb['enable'] = 1;
			$this->SaveOption('paypalec_spb', $spb);

			// Activate Stripe payment integration by default on fresh installs
			$this->SaveOption('ActiveShoppingCarts', ['integration.shoppingcart.stripe.php']);
			$this->first_install = true;
		}


		/**
		 * Called when version has changed
		 * i.e. when WLM is upgraded or downgraded
		 */
		function version_changed( $old_version, $new_version) {

			// force license recheck on new version
			$this->DeleteOption('LicenseLastCheck');

			// create rollback if previous version is less than current version
			$this->create_rollback_version( $old_version );

			// save the current version
			$this->SaveOption('CurrentVersion', $new_version);

			// run activation code
			$this->Activate();

			$this->SaveOption( 'prevent_ppp_deletion', '1' );

			$this->update_level_data();

			$this->version_has_changed = array( $old_version, $new_version );
		}


		/**
		 * Update level data to 3.0 standards
		 */
		function update_level_data() {
			// make sure all levels have default values
			$wpm_levels = $this->GetOption( 'wpm_levels' );
			$to_per_level = [
				'require_email_confirmation_start' => $this->GetOption( 'email_conf_send_after' ),
				'require_email_confirmation_send_every' => $this->GetOption( 'email_conf_send_every' ),
				'require_email_confirmation_howmany' => $this->GetOption( 'email_conf_how_many' ),
				'require_email_confirmation_sender_name' => $this->GetOption( 'email_sender_name' ),
				'require_email_confirmation_sender_email' => $this->GetOption( 'email_sender_address' ),
				'require_email_confirmation_subject' => $this->GetOption( 'confirm_email_subject' ),
				'require_email_confirmation_message' => $this->GetOption( 'confirm_email_message' ),
				'require_admin_approval_free_user1_sender_name' => $this->GetOption( 'email_sender_name' ),
				'require_admin_approval_free_user1_sender_email' => $this->GetOption( 'email_sender_address' ),
				'require_admin_approval_free_user1_subject' => $this->GetOption( 'requireadminapproval_email_subject' ),
				'require_admin_approval_free_user1_message' => $this->GetOption( 'requireadminapproval_email_message' ),
				'require_admin_approval_free_user2_sender_name' => $this->GetOption( 'email_sender_name' ),
				'require_admin_approval_free_user2_sender_email' => $this->GetOption( 'email_sender_address' ),
				'require_admin_approval_free_user2_subject' => $this->GetOption( 'registrationadminapproval_email_subject' ),
				'require_admin_approval_free_user2_message' => $this->GetOption( 'registrationadminapproval_email_message' ),
				'require_admin_approval_paid_user1_sender_name' => $this->GetOption( 'email_sender_name' ),
				'require_admin_approval_paid_user1_sender_email' => $this->GetOption( 'email_sender_address' ),
				'require_admin_approval_paid_user1_subject' => $this->GetOption( 'requireadminapproval_email_subject' ),
				'require_admin_approval_paid_user1_message' => $this->GetOption( 'requireadminapproval_email_message' ),
				'require_admin_approval_paid_user2_sender_name' => $this->GetOption( 'email_sender_name' ),
				'require_admin_approval_paid_user2_sender_email' => $this->GetOption( 'email_sender_address' ),
				'require_admin_approval_paid_user2_subject' => $this->GetOption( 'registrationadminapproval_email_subject' ),
				'require_admin_approval_paid_user2_message' => $this->GetOption( 'registrationadminapproval_email_message' ),
				'incomplete_notification' => $this->GetOption( 'incomplete_notification' ),
				'incomplete_start' => $this->GetOption( 'incomplete_notification_first' ),
				'incomplete_send_every' => $this->GetOption( 'incomplete_notification_add_every' ),
				'incomplete_howmany' => $this->GetOption( 'incomplete_notification_add' ),
				'incomplete_sender_name' => $this->GetOption( 'email_sender_name' ),
				'incomplete_sender_email' => $this->GetOption( 'email_sender_address' ),
				'incomplete_subject' => $this->GetOption( 'incnotification_email_subject' ),
				'incomplete_message' => $this->GetOption( 'incnotification_email_message' ),
				'newuser_notification_admin' => $this->GetOption( 'notify_admin_of_newuser' ),
				'newuser_admin_subject' => $this->GetOption( 'newmembernotice_email_subject' ),
				'newuser_admin_message' => $this->GetOption( 'newmembernotice_email_message' ),
				'newuser_user_sender_name' => $this->GetOption( 'email_sender_name' ),
				'newuser_user_sender_email' => $this->GetOption( 'email_sender_address' ),
				'newuser_user_subject' => $this->GetOption( 'register_email_subject' ),
				'newuser_user_message' => $this->GetOption( 'register_email_body' ),
				'expiring_admin_send' => $this->GetOption( 'expiring_notification_days' ),
				'expiring_notification_user' => $this->GetOption( 'expiring_notification' ),
				'expiring_user_send' => $this->GetOption( 'expiring_notification_days' ),
				'expiring_user_sender_name' => $this->GetOption( 'email_sender_name' ),
				'expiring_user_sender_email' => $this->GetOption( 'email_sender_address' ),
				'expiring_user_subject' => $this->GetOption( 'expiringnotification_email_subject' ),
				'expiring_user_message' => $this->GetOption( 'expiringnotification_email_message' ),
				'cancel_sender_name' => $this->GetOption( 'email_sender_name' ),
				'cancel_sender_email' => $this->GetOption( 'email_sender_address' ),
				'cancel_subject' => $this->GetOption( 'cancel_email_subject' ),
				'cancel_message' => $this->GetOption( 'cancel_email_message' ),
				'uncancel_sender_name' => $this->GetOption( 'email_sender_name' ),
				'uncancel_sender_email' => $this->GetOption( 'email_sender_address' ),
				'uncancel_subject' => $this->GetOption( 'uncancel_email_subject' ),
				'uncancel_message' => $this->GetOption( 'uncancel_email_message' ),
			];

			// migrate / fix / auto-correct certain level settings
			$reg_forms = $this->GetOption( 'regpage_form' );
			$reg_befores = $this->GetOption( 'regpage_before' );
			$reg_afters = $this->GetOption( 'regpage_after' );

			if( !is_array( $reg_forms ) ) $reg_forms = [];
			foreach( $wpm_levels AS $level_id => &$level ) {
				$old_afterregredirect = !isset($level['afterreg_redirect_type']); // 2.9 doesn't have this property
				$old_loginredirect = !isset($level['login_redirect_type']); // 2.9 doesn't have this property

				$level = array_merge( $to_per_level, $level );
				$level = array_merge( $this->level_defaults, $level );

				// migrate settings for per level after reg redirect page
				if( $old_afterregredirect && $level['afterregredirect'] != '---' && is_null( $level['custom_afterreg_redirect'] ) ) {
					$level['custom_afterreg_redirect'] = 1;
					$level['afterreg_redirect_type'] = 'page';
					$level['afterreg_page'] = $level['afterregredirect'];
				}

				// migrate settings for per level after login redirect page
				if( $old_loginredirect && $level['loginredirect'] != '---' && is_null( $level['custom_login_redirect'] ) ) {
					$level['custom_login_redirect'] = 1;
					$level['login_redirect_type'] = 'page';
					$level['login_page'] = $level['loginredirect'];
				}

				// migrate expiration options
				if( is_null( $level['expire_option'] ) ) {
					$level['expire_option'] = (int) empty( $level['noexpire'] );
				} elseif( empty( $level['noexpire'] ) && empty( $level['expire_option'] ) ) {
					// fix the value as noexpire and expire_option should never be both empty
					$level['expire_option'] = 1;
				} elseif ( !empty( $level['noexpire'] ) && !empty($level['expire_option'] ) ) {
					// fix the value as noexpire and expire_option should never be both set
					$level['expire_option'] = 0;
				}

				$level['noexpire'] = (int) !empty( $level['noexpire'] ); // set noexpire to integer value

				// make sure that expiration makes sense
				if( $level['expire_option'] == 1 && empty( $level['expire'] ) ) {
					$level['expire_option'] = 0;
					$level['noexpire'] = 1;
				}

				// custom registration forms
				if( is_null( $level['enable_custom_reg_form'] ) ) {
					if( !empty( $reg_forms[$level_id] ) ) {
						$level['enable_custom_reg_form'] = 1;
						$level['custom_reg_form'] = $reg_forms[$level_id];
					} else {
						$level['enable_custom_reg_form'] = 0;
					}
				}
				// html before reg form
				if( is_null( $level['regform_before'] ) ) {
					$level['regform_before'] = (string) wlm_arrval( $reg_befores, $level_id );
				}
				// html after reg form
				if( is_null( $level['regform_after'] ) ) {
					$level['regform_after'] = (string) wlm_arrval( $reg_afters, $level_id );
				}
				if( is_null( $level['enable_header_footer'] ) ) {
					$level['enable_header_footer'] = (int) (bool) trim( $level['regform_before'] . $level['regform_after'] );
				}
			}
			unset( $level );
			$this->SaveOption( 'wpm_levels', $wpm_levels );			
		}

		/**
		 * Parses a menu array and "normalizes" its keys and titles
		 * Note: This function calls itself to process submenus
		 * 
		 * @param  array  $items  menu items
		 * @param  array  $parent parent menu
		 * @return array          parsed menu items
		 */
		function parse_menu($items, $parent = []) {
			static $first = true;
			if($first) {
				$first = false;
				$items = apply_filters('wishlist_member_menu', $items);
			}
			$hide_legacy_features = !$this->GetOption( 'show_legacy_features' );
			foreach($items AS $key => &$item) {
				$item['title'] = __( wlm_arrval( $item, 'title'), 'wishlist-member' );
				$item['name'] = __( wlm_arrval( $item, 'name'), 'wishlist-member' );
				if(is_array($parent) && $parent){
					$item['key'] = sprintf('%s%s', trailingslashit( $parent['key']), $item['key']);
					$item['title'] = sprintf( '%s | %s', $parent['title'], $item['title'] );
				}

				$item['legacy'] = (bool) $hide_legacy_features && (bool) apply_filters('wishlist_member_legacy_menu', !empty($item['legacy']), $item['key']);
				if ($item['legacy']) {
					unset($items[$key]);
					continue;
				}

				if($item['key'] != 'dashboard') { // always allow dashboard
					// remove menu item if user does not have proper capabilities
					if( !$this->access_control->current_user_can('wishlistmember3_' . $item['key']) || ( isset( $item['wp_capability'] ) && !$this->access_control->current_user_can( $item['wp_capability'] ) ) ) {
						unset($items[$key]);
						continue;
					}
				}

				if ( !isset($item['sub']) || !is_array($item['sub']) ) $item['sub'] = array();
				$item['sub'] = apply_filters('wishlist_member_submenu', $item['sub'], $item['key']);

				if(is_array($item['sub']) && $item['sub']) {
					$item['sub'] = $this->parse_menu($item['sub'], $item);
					$item['key'] = $item['sub'][0]['key'];
					$item['title'] = $item['sub'][0]['title'];
				}
			}
			unset($item);
			return array_values($items);
		}

		/**
		 * Gets menu items at the specified menu $level from ui/menu.json
		 * @uses  WishListMember3_Core::parse_menu to parse menu items
		 * 
		 * @param  integer $level menu level
		 * @return array          menu items for the level requested
		 */
		function get_menus($level) {
			static $menus;
			$key = wlm_arrval($_GET,'wl');
			if(empty($key)) $key = $this->get_default_menu();

			$level = $this->is_show_wizard() ? 2 : $level;

			if(empty($menus)) {
				$menus = json_decode(file_get_contents($this->pluginDir3 . '/ui/menu.json'), true);
				$menus = $this->parse_menu($menus);
			}

			$menu = $menus;
			if(!$level) return $menu;

			$parts = array_pad(array_slice(explode('/', trim($key)), 0, $level),$level,'');

			$x = 0;
			while(is_string($part = array_shift($parts))) {
				foreach($menu AS $m) {
					$key = explode('/', $m['key'])[$x];
					if($key == $part) {
						$menu = $m['sub'];
						break;
					}
				}
				$x++;
			}
			return $menu;
		}

		function get_current_menu_item() {
			$wl = array_diff(explode('/',wlm_arrval($_GET,'wl')), array(''));
			$wl = array_slice( $wl, 0, 3 );
			if(!empty($wl)) {
				$menus = $this->get_menus(count($wl)-1);
				$key = preg_quote('/' . array_pop($wl), '/');
			} else {
				$menus = $this->get_menus(0);
				$key = preg_quote($this->get_default_menu(), '/');
			}
			$return = array();
			foreach($menus AS $menu) {
				$mkey = substr($menu['key'], 0, 1) == '/' ? $menu['key'] : '/' . $menu['key'];
				if(preg_match('/'.$key.'$/', $mkey)) {
					return $menu;
				}
			}

			$menus = $this->get_menus(0);
			$return = wlm_arrval( $menus, 0 );
			$_GET['wl'] = wlm_arrval( $return, 'key' );
			return $return;
		}

		/**
		 * Generates and returns the menu link for the specified $key and menu $level
		 */
		function get_menu_link($key, $level) {
			$wl = $key;

			$url = wlm_arrval($this, 'ajaxurl');
			if(empty($url)) $url = false;

			$remove_args = array();
			if( $url ) {
				parse_str( parse_url( $url, PHP_URL_QUERY ), $remove_args );
			} else {
				$remove_args = $_GET;
			}

			if(!$level && 'dashboard' == $key) {
				$return = remove_query_arg('wl', $url);
			} else {
				$return = add_query_arg('wl', $wl, $url);
			}

			unset( $remove_args[ 'wl' ] );
			unset( $remove_args[ 'page' ] );
			if( $remove_args ) {
				$remove_args = array_keys( $remove_args );
			}
			$remove_args[] = 'dummy';
			$return = remove_query_arg( $remove_args, $return );

			$return = explode( '#', $return );
			return $return[0];
		}

		/**
		 * Checks if the specified $key active for the specified menu $level
		 */
		function is_menu_active($link, $level = null) {
			$current = wlm_arrval($_GET,'wl');
			parse_str($link, $new);
			$new = wlm_arrval($new, 'wl');
			if(is_int($level)) {
				$current = implode('/',array_slice(explode('/', $current), 0, $level + 1));
				$new = implode('/',array_slice(explode('/', $new), 0, $level + 1));
			}
	        return $current == $new;
		}

		/**
		 * Generates the admin page including the sidebar and tertiary
		 * level menu items.
		 */
		function admin_page() {
			$this->user_interface();
			include_once $this->pluginDir3 . '/helpers/loading-screen.php';
			include_once $this->pluginDir3 . '/helpers/toaster.php';
		}
		function user_interface() {
			$ui_path = $this->pluginDir3 . '/ui/includes';
			$ui_url = $this->pluginURL3 . '/ui/';

			include $ui_path . '/header.php';

			echo '<div class="app-container" id="wlm3-app-container" style="position:relative">';

			// Sidebar
			$sidebar = include $ui_path . '/sidebar.php';

			// Main Content
			if($sidebar) {
				echo '<div id="the-content" class="app-content main-content">';
			} else {
				echo '<div id="the-content" class="app-content">';
			}

			$this->show_admin_page();

			echo '</div>'; // the-content

			// WordPress Footer
			printf("<footer class='container-fluid text-right text-muted'><em><a class='small' href='%s' target='_blank'>WordPress</a> <span class='small'>(%s)</span></em></footer>", 'https://wordpress.org/', apply_filters('update_footer', ''));

			echo '</div>'; // app-container
			
			include $ui_path . '/footer.php';
		}

		function get_default_menu() {
			return $this->is_show_wizard() ? "setup/getting-started" : "dashboard";
		}

		function is_show_wizard() {
			$show_wizard = false;
			if ( $this->GetOption('LicenseStatus') != 1 ) {
				$show_wizard = true;
			} else {
				$wpm_levels = $this->GetOption('wpm_levels');
				$wizard_ran = $this->GetOption('wizard_ran');
				if ( count( $wpm_levels ) <= 0 && !$wizard_ran ) {
					$show_wizard = true;
				} else {
					if ( !$wizard_ran ) $this->SaveOption('wizard_ran', 1);
				}
			}
			return $show_wizard;
		}

		function format_title($title) {
			return $this->Title . ' | ' . $title;
		}

		function show_admin_page() {
			//message holder for js display_message
			// echo '<div class="row"><div class="col-md-12 wlm-message-holder"></div></div>';
			// echo '<div class="alert alert-success"><i class="wlm-icons md-24">check_circle</i> Congrats!! You have successfully read this message!</div>';
			echo '<div class="alert wlm-message-holder toaster"></div>';

			include_once $this->pluginDir3 . '/helpers/license_nag.php';

			// third (& fourth) level menu
			$menus = $this->get_menus(2);
			if($menus) {
				echo "<div style='position: relative'>";																								
				echo '<ul id="wlm3-tabbar" class="nav nav-tabs responsive-tabs header-tab">';
				foreach($menus AS $menu) {
					if($menu['legacy']) continue;
					$link = $this->get_menu_link($menu['key'], 2);
			        $active = $this->is_menu_active($link, 2) ? ' active' : '';
					if(count($menu['sub'])) {
						printf('<li role="presentation" class="dropdown nav-item"><a data-toggle="dropdown" class="%s menu4 dropdown-toggle" data-title="%s" href="%s" target="_parent">%s<span class="caret"></span></a><ul class="dropdown-menu">', $active, $this->format_title($menu['title']), $link, $menu['name']);
						foreach($menu['sub'] AS $sub) {
							if($menu['legacy']) continue;
							printf('<li><a data-title="%s" href="%s#%s">%s</a></li>', $this->format_title($sub['title']), $link, $sub['key'], $sub['name']);
						}
						echo '</li></ul>';
					} else {
						printf('<li role="presentation" class="nav-item"><a class="%s nav-link" data-title="%s" href="%s" target="_parent">%s</a></li>', $active, $this->format_title($menu['title']), $link, $menu['name']);
					}
				}
				echo '</ul>';
				echo "<ul style='position: absolute; top: 0; right: 0' class ='list-unstyled pull-right d-flex justify-content-end header-icons -with-tabs'>";
				echo "<li>
					<a href='https://help.wishlistproducts.com/article-categories/video-tutorials/' title='Video Tutorials' target='_blank'> <i class='wlm-icons md-24'>ondemand_video</i></a>
					</li>";
				echo "<li>
					<a href='https://help.wishlistproducts.com/' title='Help' target='_blank'> <i class='wlm-icons md-24'>find_in_page</i></a>
					</li>";
				echo "<li>
					<a href='https://customers.wishlistproducts.com/support/' title='Support' target='_blank'> <i class='wlm-icons md-24'>support_icon</i></a>
					</li>";
				echo '</ul>';				
				echo '</div>';
			}
			$this->ajaxurl = null;

			// Body
			echo '<div id="the-screen" class="container-fluid pb-5">';
			echo '<div class="row">';
			echo '<div class="col-md-12">';
			$wl = $this->show_screen();
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}

		function get_screen() {
			static $wl;
			if( empty( $wl ) ) {
				$wl = implode( '/', array_diff( preg_split( '/[\/#]/', wlm_arrval( $_GET, 'wl' ) ),array( '' ) ) );
				if( empty( $wl ) ) $wl = $this->get_default_menu();
				$wl = apply_filters( 'wishlistmember_current_admin_screen', $wl, true );
			}
			return $wl;
		}

		/**
		 * Shows the admin screen as per requested menu item
		 */
		function show_screen() {
			$base = $this->pluginDir3.'/ui/admin_screens/';
			$wl = $this->get_screen();

			$this->show_notices($wl, $base);
			do_action('wishlistmember_pre_admin_screen', $wl, $base);
			do_action('wishlistmember_admin_screen', $wl, $base);
			do_action('wishlistmember_post_admin_screen', $wl, $base);

			return $wl;
		}

		function show_notices($wl, $base) {
			//add space at the top of dashboard
			$class = $wl == "dashboard" ? "mb-3" : "";
			echo '<div class="row ' .$class .'">';
			echo 	'<div class="col-md-12">';
			do_action('wishlistmember_admin_screen_notices', $wl, $base);
			echo 	'</div>';
			echo '</div>';
		}

		function to_js_vars($vars, $main) {
			printf("<script type='text/javascript'>\n%s = %s;\n</script>", $main, json_encode($vars));
		}

		function get_payperposts() {
			$ppps = call_user_func_array(array($this,'GetPayPerPosts'), func_get_args());
			$none = true;
			foreach($ppps AS &$posts) {
				if(count($posts)) {
					$none = false;
					foreach($posts AS &$post) {
						$post = (array) $post;
						$post['name'] = $post['post_title'];
						$post['id'] = sprintf( 'payperpost-%d', $post['ID'] );
					}
					unset($post);
				}
			}
			unset($posts);
			return $none ? array() : $ppps;
		}

		function get_incompleteregistration_count() {
			global $wpdb;
			$ids = $wpdb->get_col("SELECT ID FROM `{$wpdb->users}` WHERE `user_login` REGEXP 'temp_[a-f0-9]{32}' AND `user_login`=`user_email`");
			
			if($ids) {
				$users = new WP_User_Query( [ 'include' => $ids ] );
				return $users->get_total();
			} else {
				return 0;
			}
		}

		function get_nonmembers_ids() {
			global $wpdb;
			return $wpdb->get_col("SELECT `ID` FROM `{$wpdb->users}` WHERE `ID` NOT IN (SELECT DISTINCT `user_id` FROM `{$this->Tables->userlevels}`)");
		}

		function get_screen_js() {
			$js_url = $this->pluginURL3.'/ui/js/';
			$wl = implode('/', array_diff(explode('/',wlm_arrval($_GET,'wl')),array('')));
			if(empty($wl)) $wl = $this->get_default_menu();

			$wl = apply_filters('wishlistmember_current_admin_screen', $wl, true );
			$base = $this->pluginDir3 . '/ui/js/admin_js/';

			while(strlen($wl) > 1 && !file_exists($base . $wl . '.js')) $wl = dirname($wl);
			if($wl) {
				$wl .='.js';
				$js_file = $this->pluginDir3.'/ui/js/admin_js/'.$wl;
				if( is_file( $js_file ) ) {
					$js_url .= 'admin_js/' . $wl;
					return $js_url;
				}
			}
			return '';
		}

		function get_country_list() {
			static $country_list;
			if(is_null($country_list)) {
				$country_list = include_once $this->pluginDir3 . '/helpers/countries.php';
			}
			return $country_list;
		}

		function php2js_date_format( $php_date_format ) {
			static $php2js_dates;
			if(is_null($php2js_dates)) {
				$php2js_dates = include_once $this->pluginDir3 . '/helpers/php2jsdates.php';
			}
			$php_parts = str_split( $php_date_format );
			$js_parts = array();

			foreach( $php_parts AS $part ) {
				$js_parts[] = isset( $php2js_dates[$part] ) ? $php2js_dates[$part] : $part;
			}
			return implode( '', $js_parts );
		}

		/**
		 * Generate tooltip and return or print it
		 * @param  string  $tooltip      Tooltip message
		 * @param  string  $tooltip_size (optional) Defalt 'sm': Tooltip message size (i.e. md);
		 * @param  boolean $return       (optional) Defaule false: True to return tooltip markup instead of printing it
		 * @param  array   $options      (optional) Default [ 'icon' => 'help' ]: Additional options Ex. [ 'icon-class' => 'md-20', 'icon' => 'some-icon', 'style' => 'css-style' ]
		 * @return string                Tooltip markup if $return is TRUE
		 */
		function tooltip( $tooltip, $tooltip_size = 'sm', $return = false, $options = array() ) {
			// set default tooltip size if empty
			$tooltip_size = trim( $tooltip_size );
			if( empty( $tooltip_size ) ) {
				$tooltip_size = 'sm';
			}
			// set default $options
			$options = wp_parse_args(
				$options,
				array(
					'icon-class' => '',
					'icon' => 'help',
					'style' => '',
				)
			);
			
			$text = '<a href="#" data-size="%s" class="wlm-icons help-icon %s" title="%s" style="%s">%s</a>';
			$tooltip = sprintf( $text, $tooltip_size, $options['icon-class'], htmlentities( $tooltip, ENT_QUOTES ), $options['style'], $options['icon'] );
			if( $return ) {
				return $tooltip;
			} else {
				echo $tooltip;
			}
		}

		function get_js( $js ) {
			return sprintf('%s/assets/js/%s', $this->pluginURL3, $js);
		}
		function get_css( $css ) {
			return sprintf('%s/assets/css/%s' , $this->pluginURL3, $css);
		}

		function get_latest_membership_level($user_id) {
			global $wpdb;

			$values = array_keys($this->GetOption('wpm_levels'));
			$format = implode(',',array_fill(0, count($values), '%s'));
			$values[] = $user_id;

			$query = $wpdb->prepare("SELECT `a`.`level_id` FROM `{$this->Tables->userlevels}` `a` LEFT JOIN `{$this->Tables->userlevel_options}` `b` ON `a`.`ID`=`b`.`userlevel_id` WHERE `a`.`level_id` IN({$format}) AND `a`.`user_id`=%s AND `b`.`option_name`='registration_date' ORDER BY `b`.`option_value` DESC, `a`.`ID` DESC LIMIT 1", $values);

			return $wpdb->get_var($query);

		}

		/**
		 * Get Custom Fields from Custom Registration Forms
		 * @return array
		 */
		function get_custom_fields() {
			$forms = $this->GetCustomRegForms();
			$skip = ['username', 'password', 'password1', 'password2', 'email'];
			$custom_fields = [];
			foreach($forms AS $form) {
				if(empty($form->option_value['form_dissected'])) {
					$form->option_value['form_dissected'] = wlm_dissect_custom_registration_form($form->option_value);
					$this->SaveOption($form->option_name, $form->option_value);
				}
				$data = $form->option_value['form_dissected'];
				foreach($data['fields'] AS $field) {
					if(empty($field['attributes']['name']) || in_array($field['attributes']['name'], $skip)) {
						continue;
					}
					$custom_fields[$field['attributes']['name']] = $field;
				}
			}
			/*
			 * wishlist_member_custom_fields filter documentation:
			 * https://github.com/wishlistproducts/wlm3beta/wiki/filter:-wishlist_member_custom_fields
			 */
			return apply_filters( 'wishlist_member_custom_fields', $custom_fields );
		}

		/**
		 * Get User Custom Fields with values
		 * @return array
		 */
		function get_user_custom_fields( $userid ) {
			$custom_fields = $this->get_custom_fields();
			// $custom_fields = apply_filters( 'wishlist_member_other_fields', $custom_fields, $profileuser->ID );
			$user_custom_fields = $this->GetUserCustomFields( $userid );
			foreach ( $custom_fields as $key => $value ) {
				if ( ! isset( $user_custom_fields[$value['attributes']['name']] ) ) continue;
				$user_value = $user_custom_fields[ $value['attributes']['name'] ];
				switch ( $value['type'] ) {
					case "radio":
				    	foreach ( $value['options'] as $k => $v ) {
				    		if ( $user_value == $v['value'] ) $custom_fields[$key]['options'][$k]["checked"] = 1;
				    		else $custom_fields[$key]['options'][$k]["checked"] = 0;
				    	}
					case "checkbox":
						$user_value = is_array( $user_value ) ? $user_value : array();
						if ( count( $user_value ) <= 0 ) break;
				    	foreach ( $value['options'] as $k => $v ) {
				    		if ( in_array( $v['value'], $user_value ) ) $custom_fields[$key]['options'][$k]["checked"] = 1;
				    		else $custom_fields[$key]['options'][$k]["checked"] = 0;
				    	}
				    	break;
					case "select":
				    	foreach ( $value['options'] as $k => $v ) {
				    		if (  htmlentities($user_value) == $v['value'] ) $custom_fields[$key]['options'][$k]["selected"] = 1;
				    		else $custom_fields[$key]['options'][$k]["selected"] = 0;
				    	}
						break;
					default:
						$custom_fields[$key]['attributes']['value'] = $user_value;
				}
			}

			$custom_fields = array_diff_key( $custom_fields, array_flip( array( 'firstname', 'lastname', 'email', 'address1', 'address2', 'city', 'state', 'zip', 'country', 'company' ) ) );

			return apply_filters( 'wishlist_member_user_custom_fields', $custom_fields, $userid );
		}

		function create_rollback_version( $version ) {
			if( !file_exists( WLM_ROLLBACK_PATH ) ) {
				mkdir( WLM_ROLLBACK_PATH, 0755, true );
			}
			touch( WLM_ROLLBACK_PATH . $version );
		}

		function make_thankyou_url( $slug ) {
			$base = '/register/';
			if( '' == trim( get_option( 'permalink_structure' ) ) ) {
				$base = '/index.php' . $base;
			}
			return home_url( $base . $slug );
		}

		function get_official_versions() {
			$official_versions = trim( file_get_contents( $this->pluginDir3 . '/versions.txt' ) );
			return $official_versions ? explode( "\n", trim( preg_replace( '/\s+/', "\n", $official_versions ) ) ) : [];
		}
		
		/**
		 * Allow adding of additional methods to WishList Member's core class without having to extend it
		 */
		function overload() {
			/**
			 * Filter: wishlistmember_instance_methods
			 * Expects an associative array in the following format:
			 * [
			 * 	'method_name' => [ callable $function, boolean $deprecated ],
			 * ]
			 */
			$this->instance_methods = apply_filters( 'wishlistmember_instance_methods', array() );
		}
		
		/**
		 * Calls overloaded function added by the wishlistmember_instance_methods filter
		 * @param  string $method_name
		 * @param  array  $arguments
		 * @return mixed
		 */
		function __call( $method_name, $arguments ) {
			if( isset( $this->instance_methods[$method_name] ) ) {
				list( $function, $deprecated ) = array_pad( $this->instance_methods[$method_name], 2, false );
				if( $deprecated ) {
					trigger_error( 'Deprecated method: ' . $method_name, E_USER_DEPRECATED );
				}
				return call_user_func_array( $function, $arguments );
			} elseif ( array_key_exists( $method_name, (array) $this->imported_functions ) ) {
				// old way of overloading integration methods
				// $this->imported_functions is populated ::RegisterClass()
				$arguments = (array) $arguments;
				array_unshift( $arguments, $this );
				return call_user_func_array( array( $this->imported_functions[$method_name], $method_name ), $arguments );
			}
			trigger_error( 'Undefined WishList Member method: ' . $method_name, E_USER_ERROR );
		}
	}
}