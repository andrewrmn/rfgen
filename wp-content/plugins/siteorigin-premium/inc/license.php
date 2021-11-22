<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class SiteOrigin_Premium_License
 *
 * Encapsulates license related functionality. Makes any EDD license related calls to our API.
 *
 */
class SiteOrigin_Premium_License {
	
	// 'valid'/'invalid' are returned by EDD and 'active'/'inactive' are set locally.
	const STATUS_VALID = 'valid';
	const STATUS_ACTIVE = 'active';
	const STATUS_INVALID = 'invalid';
	const STATUS_INACTIVE = 'inactive';
	const STATUS_EXPIRED = 'expired';

	private $license_key = '';
	private $edd_actions;
	
	public function __construct( $license_key ) {
		$this->license_key = trim( $license_key );
		$this->edd_actions = new SiteOrigin_Premium_EDD_Actions();
	}
	
	
	/**
	 * Check whether the license key is valid and not expired.
	 *
	 * @return string The status
	 */
	public function check_license_key() {
		
		$license_data = $this->edd_actions->check_license( $this->license_key );
		
		return $this->update_status( $license_data );
	}
	
	/**
	 * Send a request to the SiteOrigin Premium servers to activate this license.
	 *
	 * @return string The status
	 */
	public function activate_license(){
		
		$license_data = $this->edd_actions->activate_license( $this->license_key );
		
		return $this->update_status( $license_data );
	}
	
	/**
	 * Do a database check to see if the license has been activated.
	 *
	 * @return bool
	 */
	function is_active() {
		$status = get_option( 'siteorigin_premium_license_status' );
		$active_statuses = array( SiteOrigin_Premium_License::STATUS_ACTIVE, SiteOrigin_Premium_License::STATUS_VALID );
		return in_array( $status, $active_statuses ) && get_option( 'siteorigin_premium_key' ) == $this->license_key;
	}
	
	
	/**
	 * Check the response from EDD to see whether the license is valid and not expired and update the option in the DB.
	 *
	 * @param stdClass $license_data License data from the EDD endpoint
	 *
	 * @return string The status.
	 */
	private function update_status( $license_data ) {
		if ( ! isset( $license_data->success ) ) {
			$status = SiteOrigin_Premium_License::STATUS_INACTIVE;
		} else if ( ! isset( $license_data->license ) ) {
			$status = SiteOrigin_Premium_License::STATUS_INVALID;
		} else {
			$status = $license_data->license;
		}
		
		// If status was returned by EDD as 'invalid' check if it has expired.
		if ( $status == SiteOrigin_Premium_License::STATUS_EXPIRED ||
			 ( $status == SiteOrigin_Premium_License::STATUS_INVALID && isset( $license_data->expires ) ) ) {
			$license_expires = new DateTime( $license_data->expires );
			$now = new DateTime();
			
			if ( $license_expires < $now ) {
				$status = SiteOrigin_Premium_License::STATUS_EXPIRED;
			}
		}
		
		update_option( 'siteorigin_premium_license_status', $status );
		
		return $status;
	}
}
