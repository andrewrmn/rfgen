<?php
/**
 * WishList Member 3.1 Version Requirements Check
 */

if( (!defined('WP_DEBUG') || !WP_DEBUG) ) error_reporting( 0 );

global $wp_version;

if ( version_compare( PHP_VERSION, WLM3_MIN_PHP_VERSION, '<' ) || version_compare( $wp_version, WLM3_MIN_WP_VERSION, '<' ) ) {

	class WishListMember3_Requirements_Not_Met {

		var $plugin_file;
		var $update_url;
		var $license_key;
		var $previous_version;
		var $message = '';

		function __construct() {
			global $wp_version, $wpdb;

			// grab plugin file
			$this->plugin_file = basename( dirname( __FILE__ ) ) . '/wpm.php';

			// grab license key
			$this->license_key = trim( $wpdb->get_var( $x = "SELECT `option_value` FROM `{$wpdb->prefix}wlm_options` WHERE `option_name` = 'LicenseKey'" ) );

			// grab previous version
			$this->previous_version = trim( $wpdb->get_var( "SELECT `option_value` FROM `{$wpdb->prefix}wlm_options` WHERE `option_name` = 'CurrentVersion'" ) );

			// check php and wp version compatibility
			$php = version_compare( PHP_VERSION, WLM3_MIN_PHP_VERSION, '<' );
			$wp = version_compare( $wp_version, WLM3_MIN_WP_VERSION, '<' );

			if( $php && $wp ) {
				$this->message = sprintf( '<p><strong>%s</strong></p><p>%s</p>', __( 'WishList Member 3.1 requires PHP 5.4 or higher and WordPress 4.0 or higher in order to function.', 'wishlist-member' ), __( 'It appears you are currently running a lower version of PHP and a lower version of WordPress so WishList Member is currently not functioning. Please upgrade PHP on your hosting account and WordPress on this site to enable WishList Member 3.1 to function.', 'wishlist-member' ) );
			} else if ( $php ) {
				$this->message = sprintf( '<p><strong>%s</strong></p><p>%s</p>', __( 'WishList Member 3.1 requires PHP 5.4 or higher in order to function.', 'wishlist-member' ), __( 'It appears you are currently running a lower version of PHP so WishList Member is currently not functioning. You will need to upgrade PHP on your hosting account to 5.4 or higher in order to enable WishList Member 3.1 to function.', 'wishlist-member' ) );
			} else if ( $wp ) {
				$this->message = sprintf( '<p><strong>%s</strong></p><p>%s</p>', __( 'WishList Member 3.1 requires WordPress 4.0 or higher in order to function.', 'wishlist-member' ), __( 'It appears you are currently running a lower version of WordPress so WishList Member is currently not functioning. You will need to upgrade WordPress on your site to 4.0 or higher in order to enable WishList Member 3.1 to function.', 'wishlist-member' ) );
			}

			$this->message .= '<p>' . __( 'Note: You can <a href="___wlm3updateurl___">click here</a> to roll back to your previous version of WishList Member in the meantime.', 'wishlist-member' ) . '</p>';

			// Menu
			add_action( 'admin_menu', array( $this, 'menu' ) );

			// Admin Notice
			add_action( 'admin_notices', array( $this, 'notice' ) );

			// Rollback
			if( isset( $_GET['action'] ) && isset( $_GET['plugin'] ) && $_GET['action'] == 'upgrade-plugin' && $_GET['plugin'] == $this->plugin_file ) {
				add_filter( 'site_transient_update_plugins', array( $this, 'update_plugin_transient' ) );
			}
		}

		// Menu
		function menu() {
			// generate update url for later use
			$this->update_url = wp_nonce_url( 'update.php?action=upgrade-plugin&plugin=' . $this->plugin_file, 'upgrade-plugin_' . $this->plugin_file );

			// add menu page
			add_menu_page( 'WishList Member', 'WishList Member', 'manage_options', 'WishListMember', array( $this, 'page' ), plugins_url( '', __FILE__ ) . '/ui/images/WishListMember-logomark-16px-wp.svg', '2.01');
		}
		// Page
		function page() {
			printf( '<div class="wrap"><h1>WishList Member</h1></div>' );
		}

		// Admin Notice
		function notice() {
			// only display to admins
			if( !current_user_can( 'manage_options' ) ) return;

			printf( '<div class="notice notice-error">%s</div>', str_replace( '___wlm3updateurl___', $this->update_url, $this->message ) );
		}

		// Rollback hook - we change the update_plugins transient based on our needs
		function update_plugin_transient( $transient ) {
			if( !is_object( $transient ) ) {
				$transient = new stdClass;
				$transient->response = array();
			}

			// no license key - abort
			if( !$this->license_key ) return $transient;

			// no previous version - abort
			if( !$this->previous_version ) return $transient;

			// version format not valid - abort
			if( !preg_match( '/\d+.\d+.\d+/', $this->previous_version ) ) return $transient;

			// previous version is greater than 3.1 - abort
			if( version_compare( $this->previous_version, '3.1.0', '>=' ) ) return $transient;

			// inject our download URL
			if( !isset( $transient->response[ $this->plugin_file ] ) ) {
				$transient->response[ $this->plugin_file ] = new stdClass;
			}
			$url = 'http://wishlistproducts.com/download/' . $this->license_key . '/==' . base64_encode( pack( 'i', WLM3_SKU ) );
			$url = add_query_arg( 'version', $this->previous_version, $url );
			$transient->response[ $this->plugin_file ]->package = $url;
			unset( $transient->response[ $this->plugin_file ]->new_version );

			// return modified transient
			return $transient;
		}
	}

	// initialize
	new WishListMember3_Requirements_Not_Met;

	// version requirements no met
	return false;
}

// version all good
return true;