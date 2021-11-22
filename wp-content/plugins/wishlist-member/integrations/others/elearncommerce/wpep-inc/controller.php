<?php

class WPEPAddOnWishList extends WPEP_AddOn_Integration {

  protected static $_instance;

  /**
   * @return WPEPAddOnWishList
   */
  public static function instance() {
    if( self::$_instance === null )
      self::$_instance = new self();

    return self::$_instance;
  }

  public function get_name() {
    return "WishList Integration";
  }

  public function get_alias() {
    return "wishlist-member-integration";
  }

  public function get_information() {
    return [
      'official'     => true,
      'store_id'     => '3024',
      'store_name'   => 'eLearnCommerce Wishlist Member Integration',
      'version'      => '1.4',
      'link'         => 'https://codestore.codeiscode.com/downloads/wpep-wishlistmember-integration/'
    ];
  }

  /**
   * @var WPEPAddOnWishListContent
   */
  public $contentLibraryIntegration;

  /**
   * @var WPEP_Integration_License_And_Updates
   */
  public $licenseAndUpdatesIntegration;

  /**
   * Runs after WPEP has been fully loaded.
   * @return void
   */
  public function init() {
    $this->_setup_content_library();
    // $this->_setup_license_and_updates();
  }

  public function has_requirements_met() {
    if( function_exists( 'wlmapi_get_member_levels' ) )
      return true;

    return [
      'WishList Member' => 'https://member.wishlistproducts.com/'
    ];
  }

  private function _setup_content_library() {
    global $WishListMemberInstance;
    if( !class_exists( 'WPEP_Content_Library_Integration' ) )
      return;

    if( !class_exists( 'WPEPAddOnWishListContent' ) )
      require_once( $WishListMemberInstance->pluginDir3 . '/integrations/others/elearncommerce/wpep-inc/content.php' );
      $this->contentLibraryIntegration = new WPEPAddOnWishListContent();
  }

  private function _setup_license_and_updates() {
    if( !class_exists( 'WPEP_Integration_License_And_Updates' ) ) return;

    $plugin_information = $this->get_information();

    $this->licenseAndUpdatesIntegration = new WPEP_Integration_License_And_Updates(
      $plugin_information['store_name'],
      intval( $plugin_information['store_id'] ),
      'wpep-wishlist-member',
      $plugin_information['version'],
      'wpep_addon_wish_list_'
    );

    $this->licenseAndUpdatesIntegration->setup_license_and_updates(
      $WishListMemberInstance->pluginDir3 . '/integrations/others/elearncommerce/', false
    );
  }

}
