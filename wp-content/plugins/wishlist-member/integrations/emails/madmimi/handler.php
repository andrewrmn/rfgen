<?php

namespace WishListMember\Autoresponders;

if ( ! class_exists( '\WPMadMimi' ) ) {
	global $WishListMemberInstance;
	require_once $WishListMemberInstance->pluginDir . '/extlib/madmimi/madmimi.php';
}

class MadMimi {
	static function user_registered( $user_id, $data ) {
		self::added_to_level( $user_id, array( $data['wpm_id'] ) );
	}

	static function added_to_level( $user_id, $level_id ) {
		$level_id = wlm_remove_inactive_levels( $user_id, $level_id );
		self::pre_process( $user_id, $level_id, 'added' );
	}

	static function removed_from_level( $user_id, $level_id ) {
		self::pre_process( $user_id, $level_id, 'removed' );
	}

	static function uncancelled_from_level( $user_id, $levels ) {
		self::pre_process( $user_id, $levels, 'uncancelled' );
	}

	static function cancelled_from_level( $user_id, $levels ) {
		self::pre_process( $user_id, $levels, 'cancelled' );
	}

	static function pre_process( $email_or_id, $levels, $action ) {
		// get email address
		if ( is_numeric( $email_or_id ) ) {
			$userdata = get_userdata( $email_or_id );
		} elseif ( filter_var( $email_or_id, FILTER_VALIDATE_EMAIL ) ) {
			$userdata = get_user_by( 'email', $email_or_id );
		} else {
			return; // email_or_id is neither a valid ID or email address
		}
		if ( ! $userdata ) {
			return; // invalid user_id
		}

		// make sure email is not temp
		if ( ! trim( $userdata->user_email ) || preg_match( '/^temp_[0-9a-f]+/i', $userdata->user_email ) ) {
			return;
		}

		// make sure levels is an array
		if ( ! is_array( $levels ) ) {
			$levels = array( $levels );
		}

		foreach ( $levels as $level_id ) {
			self::process( $userdata, $level_id, $action );
		}
	}

	static function process( $userdata, $level_id, $action ) {
		static $ar;
		if ( ! $ar ) {
			$ar = ( new \WishListMember\Autoresponder( 'madmimi' ) )->settings;
		}

		$add    = $ar['list_actions'][ $level_id ][ $action ]['add'] ?: array();
		$remove = $ar['list_actions'][ $level_id ][ $action ]['remove'] ?: array();

		$username = $ar['username'];
		$api_key  = $ar['api_key'];

		$mmm = new \WPMadmimi( $username, $api_key );

		try {
			if ( $remove ) {
				$mmm->remove_from_lists( $remove, $userdata->user_email );
			}
			if ( $add ) {
				$mmm->add_to_lists( $add, $userdata->user_email, $userdata->first_name, $userdata->last_name );
			}
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
		}

	}
}
