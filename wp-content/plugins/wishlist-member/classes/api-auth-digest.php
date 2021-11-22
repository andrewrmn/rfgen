<?php

namespace WishListMember;

/**
 * HTTP Digest Authentication for WLM API
 * @since 3.7
 * @author Mike Lopez <mike@wishlistproducts.com>
 */
class API_Auth_Digest {
	/**
	 * authentication status
	 *
	 * @var boolean
	 */
	private $status = true;
	/**
	 * http auth realm
	 *
	 * @var string
	 */
	private $realm = '';
	/**
	 * nonce transient name prefix
	 *
	 * @var string
	 */
	private $nonce_transient_prefix = 'wlm_api_auth_digest_nonce_';
  /**
   * nonce time to live
   * @var integer
   */
  private $nonce_ttl = 30;
  
	/**
	 * Constructor
	 * - Generates the $this->realm
	 * - Checks valid HTTP digest auth data
	 * - sets $this->status
	 */
	function __construct() {
		// generate realm name
		$this->realm = 'WishList Member @ ' . preg_replace( '/^.+?:\/\//', '', get_bloginfo( 'url' ) );

		// grab all valid api keys
		$users = array( 'wishlist' => wishlistmember_instance()->GetAPIKey() ) + ( new APIKey() )->get_all_keys();

		// request for digest auth if there is none
		if ( empty( $_SERVER['PHP_AUTH_DIGEST'] ) ) {
			$this->unauthorized();
			return;
		}

		// check for valid username
		if ( ! ( $data = $this->http_digest_parse( $_SERVER['PHP_AUTH_DIGEST'] ) ) || ! isset( $users[ $data['username'] ] ) ) {
			$this->unauthorized();
			return;
		}

		// generate the valid response
		$A1             = md5( $data['username'] . ':' . $this->realm . ':' . $users[ $data['username'] ] );
		$A2             = md5( $_SERVER['REQUEST_METHOD'] . ':' . $data['uri'] );
		$valid_response = md5( $A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2 );
		// response do not match
		if ( $data['response'] != $valid_response ) {
			$this->unauthorized();
			return;
		}
		
		// verify noncece validity
		if ( ! $this->verify_nonce( $data['nonce'], $data['nc'] ) ) {
			$this->unauthorized( true );
			return;
		}

	}
  
	/**
	 * Return the value of $this->status
	 *
	 * @return boolean
	 */
	public function status() {
		return (bool) $this->status;
	}
  
	/**
	 * Saves nonce to database
	 *
	 * @param [type] $name  [description]
	 * @param array  $value [description]
	 */
	private function set_nonce( $name, $value = array() ) {
		return set_transient( $this->nonce_transient_prefix . $name, $value, $this->nonce_ttl ) ? $name : false;
	}
  
	/**
	 * Verifies the nonce by ensuring that it exists and that the counter has not yet been used
	 *
	 * @param  string  $name nonce name
	 * @param  integer $nc  nonce counter
	 * @return boolean
	 */
	private function verify_nonce( $name, $nc ) {
		$nc         = (int) $nc;
		$nonce_data = get_transient( $this->nonce_transient_prefix . $name );
		if ( $nonce_data === false ) {
			return false;
		}
		$nonce_data = (array) $nonce_data;
		if ( in_array( $nc, $nonce_data ) ) {
			return false;
		}
		$nonce_data[] = $nc;
		return $this->set_nonce( $name, $nonce_data ) ? true : false;
	}
  
  /**
	 * Sends 401 and WWW-Authenticate headers
   * and sets status to false
   */
	private function unauthorized( $stale = false ) {
		$nonce = $this->set_nonce( uniqid() );
		if ( $nonce ) {
			  header( 'HTTP/1.1 401 Unauthorized' );
				$stale = $stale ? 'stale="true" ' : '';
			  header( 'WWW-Authenticate: Digest ' . $stale . 'realm="' . $this->realm . '",qop="auth",nonce="' . $nonce . '",opaque="' . md5( $this->realm ) . '"' );
		}
		$this->status = false;
	}
  
	/**
	 * Parses the digest
	 * (sourced from https://www.php.net/manual/en/features.http-auth.php)
	 *
	 * @param  string $digest Digest data
	 * @return array|false
	 */
	private function http_digest_parse( $digest ) {
		// protect against missing data
		$needed_parts = array(
			'nonce'    => 1,
			'nc'       => 1,
			'cnonce'   => 1,
			'qop'      => 1,
			'username' => 1,
			'uri'      => 1,
			'response' => 1,
		);
		$data         = array();
		$keys         = implode( '|', array_keys( $needed_parts ) );

		preg_match_all( '@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', stripslashes( $digest ), $matches, PREG_SET_ORDER );

		foreach ( $matches as $m ) {
			$data[ $m[1] ] = $m[3] ? $m[3] : $m[4];
			unset( $needed_parts[ $m[1] ] );
		}

		return $needed_parts ? false : $data;
	}
}
