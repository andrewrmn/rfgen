<?php

namespace WishListMember\Autoresponders;

if ( ! class_exists( '\WpActiveCampaign' ) ) {
	global $WishListMemberInstance;
	require_once $WishListMemberInstance->pluginDir . '/extlib/active-campaign/active-campaign.php';
}

class ActiveCampaign {
	static function subscribe( $email, $level_id ) {
		self::process( $email, $level_id );
	}

	static function unsubscribe( $email, $level_id ) {
		self::process( $email, $level_id, true );
	}

	static function process( $email, $level_id, $unsub = false ) {
		global $WishListMemberInstance;
		$ar   = ( new \WishListMember\Autoresponder( 'activecampaign' ) )->settings;
		$maps = $ar['maps'][ $level_id ];
		if ( empty( $maps ) ) {
			return;
		}

		$api_url = isset($ar['api_url']) ? trim($ar['api_url']) : "";
		$api_key = isset($ar['api_key']) ? trim($ar['api_key']) : "";

		if ( empty($api_key) || empty($api_url) ) return;

		$ac = new \WpActiveCampaign( $api_url, $api_key );

		try {
			if ( $unsub && isset($ar[ $level_id ]['autoremove']) && $ar[ $level_id ]['autoremove'] ) {
				$ac->remove_from_lists( $maps, $email );
			}
			if ( ! $unsub ) {
				if ( ! empty( $maps ) ) {
					$ac->add_to_lists(
						$maps,
						array(
							'first_name' => $WishListMemberInstance->ARSender['first_name'],
							'last_name'  => $WishListMemberInstance->ARSender['last_name'],
							'email'      => $WishListMemberInstance->ARSender['email'],
						)
					);
				}
			}
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
		}
	}
}
