<?php
namespace WishListMember\Webinars;

class EverGreenIntegration {
	var $slug = 'evergreen';
	public function __construct() {
		// hook to our subscribe function
		add_action( 'wishlistmember_webinar_subscribe', array( $this, 'subscribe' ) );
	}

	/**
	 * Action: wishlistmember_webinar_subscribe
	 * Subscribes a user to a webinar
	 * @param array $data 
	 */
	public function subscribe( $data ) {

		global $WishListMemberInstance;
		$webinars = $WishListMemberInstance->GetOption( 'webinar' );
		$settings = $webinars[ $this->slug ];
		$settings = $settings[ $data['level'] ];
		if ( empty( $settings ) ) {
			return;
		}

		$url      = $settings;
		$urlparts = parse_url( $url );
		parse_str( $urlparts['query'], $args );
		$args['name']  = sprintf( '%s %s', $data['first_name'], $data['last_name'] );
		$args['email'] = $data['email'];

		// subscribe to next day
		$args['date']     = date( 'Y-m-d', time() + ( 3600 * 24 ) );
		$args['timezone'] = 'UTC';

		$query             = http_build_query( $args );
		$urlparts['query'] = $query;

		$url = sprintf( '%s://%s%s?%s', $urlparts['scheme'], $urlparts['host'], $urlparts['path'], $urlparts['query'] );
		$WishListMemberInstance->ReadURL( $url );
	}
}

new EverGreenIntegration();
