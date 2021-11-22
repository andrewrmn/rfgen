<?php

namespace WishListMember;

defined( 'ABSPATH' ) || die();

/**
 * WishList Member Autoresponder helper class
 * Easy way to grab settings for specific autoresponder
 */
class Autoresponder {
	var $settings;
	private $autoresponder;
	function __construct( $autoresponder ) {
		global $WishListMemberInstance;

		$this->autoresponder = $autoresponder;
		// grab autoresponder settings or set $settings to empty array
		$this->settings = wlm_arrval( $WishListMemberInstance->GetOption( 'Autoresponders' ), $autoresponder ) ?: array();
	}

	/**
	 * Save whatever is stored in the settings property
	 */
	function save_settings() {
		global $WishListMemberInstance;

		// get autoresponders
		$autoresponders = $WishListMemberInstance->GetOption( 'Autoresponders' ) ?: array();

		// update settings for $autoresponder
		$autoresponders[ $this->autoresponder ] = $this->settings;

		// save autoresponders
		$WishListMemberInstance->SaveOption( 'Autoresponders', $autoresponders );
	}
}
