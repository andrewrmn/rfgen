<?php

namespace WishListMember;

/**
 * Handles Multiple API Keys for WishList Member API 2.0
 */
class APIKey {
	/**
	 * Array of API Keys
	 *
	 * @var array
	 */
	private $keys = array();

	/**
	 * Constructor
	 */
	function __construct() {
		global $WishListMemberInstance;
		$keys       = $WishListMemberInstance->GetOption( 'WLMAPIKeys' );
		$this->keys = is_array( $keys ) ? $keys : array();
	}

	/**
	 * Retrieves API Key for $key_name
	 *
	 * @param  string $key_name Name of the API Key
	 * @return string API Key
	 */
	function get( $key_name ) {
		$key_name = $this->validate_key_name( $key_name );
		return wlm_arrval( $this->keys, $key_name ) ?: false;
	}

	/**
	 * Adds a new API Key
	 *
	 * @param string      $key_name Name of the API Key
	 * @param string|null $key API Key. Auto-generated if empty.
	 * @return string|false $key API Key on success or false if the $key_name already exists or if an error occured during saving.
	 */
	function add( $key_name, $key = null ) {
		$key_name = $this->validate_key_name( $key_name );
		if ( empty( $this->keys[ $key_name ] ) ) {
			return $this->update( $key_name, $key );
		}
		return false;
	}

	/**
	 * Updates an API Key. Adds it if $key_name does not exist
	 *
	 * @param string      $key_name Name of the API Key
	 * @param string|null $key API Key. Auto-generated if empty.
	 * @return string|false $key API Key on success or false if an error occured during saving.
	 */
	function update( $key_name, $key = null ) {
		$key_name = $this->validate_key_name( $key_name );
		$old_keys = $this->keys;

		$key = trim( $key ) ?: $this->generate();

		$this->keys[ $key_name ] = $key;
		if ( $this->save() ) {
			return $key;
		} else {
			$this->keys = $old_keys;
			return false;
		}
	}

	/**
	 * Deletes an API Key
	 *
	 * @param  string $key_name Name of the API Key
	 * @return bool
	 */
	function delete( $key_name ) {
		$key_name = $this->validate_key_name( $key_name );
		$old_keys = $this->keys;

		unset( $this->keys[ $key_name ] );
		if ( $this->save() ) {
			return true;
		} else {
			$this->keys = $old_keys;
			return false;
		}
	}

	/**
	 * Retrieves all API Keys
	 * @return array
	 */
	function get_all_keys() {
		return $this->keys;
	}

	/**
	 * Generates a unique API Key with a length of 32
	 *
	 * @uses wlm_generate_password();
	 *
	 * @return string API Key
	 */
	private function generate() {
		return wlm_generate_password( 32, false );
	}

	/**
	 * Validates $key_name. Key name must be a non-numeric string. Throws an exception if validation fails.
	 *
	 * @param  string $key_name Key name to validate
	 * @return string Trimmed $key_name
	 */
	private function validate_key_name( $key_name ) {
		if ( ! is_scalar( $key_name ) || is_numeric( $key_name ) || is_bool( $key_name ) || ! trim( $key_name ) ) {
			throw new Exception( 'Key name must be a non-numeric string' );
		}
		return trim( $key_name );
	}

	/**
	 * Saves API keys to WishList Member Options table
	 *
	 * @uses WishListMemberCore::SaveOption
	 *
	 * @return bool
	 */
	private function save() {
		global $WishListMemberInstance;
		return $WishListMemberInstance->SaveOption( 'WLMAPIKeys', $this->keys );
	}
}
