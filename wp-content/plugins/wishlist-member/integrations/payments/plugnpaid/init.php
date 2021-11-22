<?php // initialization

if ( ! class_exists( 'WLM3_PlugNPaid_Hooks' ) ) {
	class WLM3_PlugNPaid_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_plugnpaid_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data = array(
				'status'  => false,
				'message' => '',
			);

			$transient_name = 'wlm_plugnpaid_' . md5( $api_url . $api_key );
			if ( $save ) {
				$this->wlm->SaveOption( 'plugnpaidapikey', $plugnpaidapikey );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_send_json( $transient_result );
				}
			}

			// get products from plug&paid
			$result = Requests::request(
				'https://api.plugnpaid.com/v1/products/list',
				array(
					'Content-Type' => 'application/json',
					'Referer' => '', // has to be explicitly set to blank for this to work
				),
				json_encode( array( 'token' => $plugnpaidapikey ) ),
				Requests::POST,
				array(
					'useragent' => 'WishList Member/' . $this->wlm->Version,
				)
			);

			$response = json_decode( $result->body );

			if ( ! $response || $response->status < 0 ) {
				$data['message'] = $response->error;
			} else {
				$data['data']['products']         = $response->products;
				$data['data']['products_options'] = array(
					'(empty)' => array(
						'id'   => '',
						'text' => '',
					),
				);
				foreach ( $response->products as $product ) {
					$data['data']['products_options'][ $product->id ] = array(
						'id'   => $product->id,
						'text' => $product->name,
					);
				}
				$data['status'] = 1;
			}

			set_transient( $transient_name, $data, 60 * 15 );

			wp_die( json_encode( $data ) );
		}
	}
	new WLM3_PlugNPaid_Hooks();
}
