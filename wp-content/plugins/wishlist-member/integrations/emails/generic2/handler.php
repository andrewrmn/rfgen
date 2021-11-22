<?php

namespace WishListMember\Autoresponders;

class Generic2 {

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
		static $ar;

		if ( empty( $ar ) ) {
			$ar = ( new \WishListMember\Autoresponder( 'generic2' ) )->settings;
		}

		// get email address
		if ( is_numeric( $email_or_id ) ) {
			$userdata = get_userdata( $email_or_id );
			if ( ! $userdata ) {
				return; // invalid user_id
			}
		} elseif ( filter_var( $email_or_id, FILTER_VALIDATE_EMAIL ) ) {
			$userdata = get_user_by( 'email', $email_or_id );
			if ( ! $userdata ) {
				return; // invalid user_id
			}
		} else {
			return; // email_or_id is neither a valid ID or email address
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

		foreach ( $levels as $level_id ) {
			$add    = $ar['list_actions'][ $level_id ][ $action ]['add'] ?: '';
			$remove = $ar['list_actions'][ $level_id ][ $action ]['remove'] ?: '';
			if ( $add ) {
				self::send_data( $add, $email, $fname, $lname, true );
			}
			if ( $remove ) {
				self::send_data( $remove, $email, $fname, $lname, false );
			}
		}
	}

	static function send_data( $post_url, $email, $fname, $lname, $subscribe ) {
		global $WishListMemberInstance;

		if ( $post_url ) {

			$httpAgent = 'WLM_GENERIC_AGENT';
			$postData  = array(
				'email_address' => $email,
				'first_name'    => $fname,
				'last_name'     => $lname,
			);
			if ( $subscribe ) {
				$postData['unsubscribe'] = 0;
			} else {
				$postData['unsubscribe'] = 1;
			}

			if ( $subscribe || isset( $postData['unsubscribe'] ) && ! $subscribe ) {
				wp_remote_post(
					$post_url,
					array(
						'blocking'   => false,
						'user-agent' => $httpAgent,
						'body'       => $postData,
					)
				);
			}
		}
	}
}

