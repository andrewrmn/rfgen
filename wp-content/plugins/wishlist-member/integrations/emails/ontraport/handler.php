<?php

namespace WishListMember\Autoresponders;

class Ontraport {
	static function subscribe( $email, $level_id ) {
		self::process( $email, $level_id );
	}

	static function unsubscribe( $email, $level_id ) {
		self::process( $email, $level_id, true );
	}

	static function process( $email, $level_id, $unsub = false ) {
		global $WishListMemberInstance;
		$ar = ( new \WishListMember\Autoresponder( 'ontraport' ) )->settings;

		if ( ! $unsub ) {

			if ( $ar['addenabled'][$level_id] == 'yes' ) {

				$fname = ( ! empty( $WishListMemberInstance->OrigPost['firstname'] ) ) ? $WishListMemberInstance->OrigPost['firstname'] : $WishListMemberInstance->ARSender['first_name'];
				$lname = ( ! empty( $WishListMemberInstance->OrigPost['lastname'] ) ) ? $WishListMemberInstance->OrigPost['lastname'] : $WishListMemberInstance->ARSender['last_name'];
				$email = ( ! empty( $WishListMemberInstance->OrigPost['email'] ) ) ? $WishListMemberInstance->OrigPost['email'] : $WishListMemberInstance->ARSender['email'];

				// Set format to add tags
				$tags = '*/*';
				foreach ( (array) $ar['tags'][$level_id] as $tag ) {
					$tags .= $tag . '*/*';
				}

				// Set format for sequences
				$sequences = '*/*';
				foreach ( (array) $ar['sequences'][$level_id] as $sequence ) {
					$sequences .= $sequence . '*/*';
				}

				// Set the request type and construct the POST request
				$postdata = array(
					'firstname'      => $fname,
					'lastname'       => $lname,
					'email'          => $email,
					'contact_cat'    => $tags,
					'updateSequence' => $sequences,
				);

				wp_remote_post(
					'https://api.ontraport.com/1/Contacts/saveorupdate',
					array(
						'body'     => $postdata,
						'blocking' => false,
						'headers'  => array(
							'Content-Type' => 'application/x-www-form-urlencoded',
							'Api-Key'      => $ar['api_key'],
							'Api-Appid'    => $ar['app_id'],
						),
					)
				);
			} else {
				// If unsub Do nothing, for now.
			}
		}
	}
}
