<?php

namespace WishListMember\Autoresponders;

class Drip {
	static function subscribe( $email, $level_id ) {
		self::process( $email, $level_id, true );
	}

	static function unsubscribe( $email, $level_id ) {
		self::process( $email, $level_id, false );
	}

	static function process( $email, $level_id, $subscribe ) {
		global $WishListMemberInstance;
		$ar = ( new \WishListMember\Autoresponder( 'drip' ) )->settings;

		$token = trim( $ar['apitoken'] );
		if ( empty( $token ) ) {
			return;
		}

		require_once $WishListMemberInstance->pluginDir . '/extlib/wlm_drip/Drip_API.class.php';
		$drip_api = new \WLM_Drip_Api( $token );

		if ( $ar['campaign'][ $level_id ] ) {
			list($account_id, $campaign_id) = explode( '-', $ar['campaign'][ $level_id ] );
			$params                         = array(
				'account_id'  => $account_id,
				'campaign_id' => $campaign_id,
				'email'       => $email,
			);
			if ( $subscribe ) {
				$params['double_optin']  = (bool) $ar['double'][ $level_id ];
				$name                    = $WishListMemberInstance->ARSender['name'];
				list($fname, $lname)     = explode( ' ', $name, 2 );
				$params['custom_fields'] = array(
					'name'       => $name,
					'first_name' => $fname,
					'last_name'  => $lname,
				);
				$drip_api->subscribe_subscriber( $params );
			} else {
				if ( $ar['unsub'][ $level_id ] ) {
					$drip_api->unsubscribe_subscriber( $params );
				}
			}
		}
	}
}
