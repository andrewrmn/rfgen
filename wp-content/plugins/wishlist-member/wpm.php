<?php
/*
 * Plugin Name: WishList Member&trade;
 * Plugin URI: https://member.wishlistproducts.com/
 * Description: <strong>WishList Member&trade;</strong> is the most comprehensive membership plugin for WordPress users. It allows you to create multiple membership levels, protect desired content and much more. For more WordPress tools please visit the <a href="http://wishlistproducts.com/blog" target="_blank">WishList Products Blog</a>. Requires at least WordPress 4.0 (version 4.9.6 or higher recommended) and PHP 5.4 (version 7.2 or higher recommended)
 * Author: WishList Products
 * Version: 3.9.7524
 * Author URI: https://wishlistproducts.com/
 * License: GPLv2
 * Text Domain: wishlist-member
 * SVN: 7524
 */

// Plugin Info (Update this to match the plugin header above)
define( 'WLM3_PLUGIN_NAME', 'WishList Member&trade;' );
define( 'WLM3_PLUGIN_URI', 'https://member.wishlistproducts.com/' );
define( 'WLM3_PLUGIN_VERSION', '3.9.7524' );
define( 'WLM3_PLUGIN_AUTHOR', 'WishList Products' );
define( 'WLM3_PLUGIN_AUTHORURI', 'https://wishlistproducts.com/' );

define( 'WLM3_MIN_WP_VERSION', '4.0' );
define( 'WLM3_MIN_PHP_VERSION', '5.4' );

define( 'WLM3_SKU', '8901' );
define( 'WLM_ROLLBACK_PATH', WP_CONTENT_DIR . '/wishlist-rollback/wishlist-member/' );

if ( class_exists( 'WishListMember' ) || class_exists( 'WishListMember3' ) ) {
	wp_die( sprintf( '<p>Another version of WishList Member is already running.</p><p><a href="%s">Go Back</a></p>', $_SERVER['HTTP_REFERER'] ) );
}

if ( ! require_once 'versioncheck.php' ) {
	return;
}

require_once 'includes/autoloader.php';

require_once 'legacy/wpm.php'; // legacy WLM

require_once 'classes/wishlist-member3-core.php';
require_once 'classes/wishlist-member3-actions.php';
require_once 'classes/wishlist-member3-hooks.php';
require_once 'classes/wishlist-member3.php';

if ( class_exists( 'WishListMember3' ) ) {
	// make sure $WishListMemberInstance is global
	global $WishListMemberInstance;
	$WishListMemberInstance = new WishListMember3( __FILE__, WLM3_SKU, 'WishListMember', 'WishList Member', 'WishListMember' );
	/**
	 * Helper function to return $WishListMemberInstance
	 * 
	 * Long term goal is to avoid using $WishListMemberInstance dire
	 * @return (object) \WishListMember3
	 */
	function wishlistmember_instance() {
		return $GLOBALS['WishListMemberInstance'];
	}
	require_once 'includes/features.php';
	require_once 'includes/compatibility.php';
	require_once 'legacy/init.php';
	require_once 'legacy/api1/loader.php'; // legacy WLMAPI
	
	// additional methods
	wishlistmember_instance()->overload();
}