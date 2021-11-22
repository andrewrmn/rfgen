<?php

namespace WishListMember\Autoresponders;

if ( ! class_exists( '\cksdk' ) ) {
	global $WishListMemberInstance;
	include_once $WishListMemberInstance->pluginDir . '/extlib/convertkit/cksdk.php';
}

class ConvertKit {

	static function user_registered( $user_id, $data ) {
		self::added_to_level( $user_id, array( $data['wpm_id'] ) );
	}

	static function added_to_level( $user_id, $level_id ) {
		$level_id = wlm_remove_inactive_levels( $user_id, $level_id );
		self::process( $user_id, $level_id, 'added' );
	}

	static function removed_from_level( $user_id, $level_id ) {
		self::process( $user_id, $level_id, 'removed' );
	}

	static function uncancelled_from_level( $user_id, $levels ) {
		self::process( $user_id, $levels, 'uncancelled' );
	}

	static function cancelled_from_level( $user_id, $levels ) {
		self::process( $user_id, $levels, 'cancelled' );
	}

	static function process( $email_or_id, $levels, $action ) {
		static $interface;

		// get email address
		if ( is_numeric( $email_or_id ) ) {
			$userdata = get_userdata( $email_or_id );
		} elseif ( filter_var( $email_or_id, FILTER_VALIDATE_EMAIL ) ) {
			$userdata = get_user_by( 'email', $email_or_id );
		} else {
			return; // email_or_id is neither a valid ID or email address
		}
		if( !$userdata) {
			return;
		}
		$email = $userdata->user_email;
		$fname = $userdata->first_name;
		$lname = $userdata->last_name;

		// make sure email is not temp
		if ( ! trim( $email ) || preg_match( '/^temp_[0-9a-f]+/i', $email ) ) {
			return;
		}

		// make sure levels is an array
		if ( ! is_array( $levels ) ) {
			$levels = array( $levels );
		}

		if ( ! $interface ) {
			$interface = new ConvertKit_Interface();
		}

		foreach ( $levels as $level_id ) {
			$interface->process( $email, $fname, $lname, $level_id, $action );
		}
	}

	static function _interface() {
		static $interface;
		if ( ! $interface ) {
			$interface = new ConvertKit_Interface();
		}
		return $interface;
	}

}


class ConvertKit_Interface {
	private $api_secret = '';
	private $wlm;
	private $ar;

	function __construct() {
		global $WishListMemberInstance;
		$this->wlm        = $WishListMemberInstance;
		$this->ar         = ( new \WishListMember\Autoresponder( 'convertkit' ) )->settings;
		$this->api_secret = $this->ar['ckapi'];
	}

	function cksdk() {
		static $cksdk;
		if ( ! $cksdk ) {
			$cksdk = new \cksdk( $this->api_secret );
		}
		return $cksdk;
	}

	function process( $email, $fname, $lname, $level_id, $action ) {
		$add    = $this->ar['list_actions'][ $level_id ][ $action ]['add'] ?: array();
		$remove = $this->ar['list_actions'][ $level_id ][ $action ]['remove'] ?: array();

		if ( $add ) {
			$this->subscribe( $add, $email, $fname, $lname );
		}
		if ( $remove ) {
			$this->unsubscribe( $email );
		}
	}

	function subscribe( $formid, $email, $fname, $lname ) {
		$ck = $this->cksdk();
		if ( $ck->last_error != '' ) {
			return $ck->last_error;
		}
		$args                     = array(
			'email'      => $email,
			'first_name' => $fname,
			'fields'     => array( 'last_name' => $lname ),
		);

		// If the $formid is in array (saw these in some cases from clients) then let's get the first value.
		if(is_array($formid)) {
			$formid = $formid[0];
		}

		$f                        = $ck->form_subscribe( $formid, $args );
		if ( ! $f ) {
			return $ck->last_error;
		}
		return true;
	}

	function unsubscribe( $email ) {
		$ck = $this->cksdk();
		if ( $ck->last_error != '' ) {
			return $ck->last_error;
		}
		$f = $ck->form_unsubscribe( $email );
		if ( ! $f ) {
			return $ck->last_error;
		}
		return true;
	}
	/* End of Functions */
}
