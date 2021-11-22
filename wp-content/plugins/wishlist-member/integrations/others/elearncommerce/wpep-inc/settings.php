<?php

class WPEPAddOnWishListSettings extends WPEP_AddOn_Settings_Integration {

  /**
   * @var WPEPAddOnWishListSettings
   */
  protected static $_instance;

  /**
   * @return WPEPAddOnWishListSettings
   */
  public static function instance() {
    if( self::$_instance === null )
      self::$_instance = new self();

    return self::$_instance;
  }

  public function get_name() {
    return $this->get_controller()->get_name();
  }

  public function get_plugin_basename() {
    return plugin_basename( $WishListMemberInstance->pluginDir3 . '/integrations/others/elearncommerce/' );
  }

  public function get_alias() {
    return $this->get_controller()->get_alias();
  }

  public function get_option_prefix() {
    return 'wpep_addon_wish_list_';
  }

  public function get_controller() {
    return WPEPAddOnWishList::instance();
  }

  public function get_field_list() {
    $field_list = [];

    if( $this->get_controller()->licenseAndUpdatesIntegration !== null )
      $field_list = $this->get_controller()->licenseAndUpdatesIntegration->get_license_key_field_as_indexed_array();

    return $this->get_controller()->contentLibraryIntegration->administrationCoordinator->get_global_options_settings( $field_list );
  }

}