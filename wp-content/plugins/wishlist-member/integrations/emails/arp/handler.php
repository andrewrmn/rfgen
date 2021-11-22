<?php

namespace WishListMember\Autoresponders;

class ARP {

	static function subscribe( $email, $level_id ) {
		self::process( $email, $level_id, true );
	}

	static function unsubscribe( $email, $level_id ) {
		self::process( $email, $level_id, false );
	}

	static function process( $email, $level_id, $subscribe ) {
		global $WishListMemberInstance;
		$ar = ( new \WishListMember\Autoresponder( 'arp' ) )->settings;

		$postURL = $ar['arpurl'];
		$arUnsub = ( $ar['arUnsub'][ $level_id ] == 1 ? true : false );

		if ( $postURL && $ar['arID'][ $level_id ] ) {
			$emailAddress = $WishListMemberInstance->ARSender['email'];
			$fullName     = $WishListMemberInstance->ARSender['name'];

			$httpAgent = 'ARPAgent';
			$postData  = array(
				'id'                => $ar['arID'][ $level_id ],
				'full_name'         => $fullName,
				'split_name'        => $fullName,
				'email'             => $emailAddress,
				'subscription_type' => 'E',
			);
			if ( ! $subscribe ) {
				if ( $arUnsub ) {
					$postData['arp_action'] = 'UNS';
				} else {
					return;
				}
			}

			wp_remote_post(
				$postURL,
				array(
					'blocking'   => false,
					'user-agent' => $httpAgent,
					'body'       => $postData,
				)
			);
		}
	}
}

