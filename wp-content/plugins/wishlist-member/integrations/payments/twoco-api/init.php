<?php

if ( ! class_exists( 'WLM3_2CheckOutAPI_Hooks' ) ) {
	class WLM3_2CheckOutAPI_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_twoco-api_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			extract( $twocheckoutapisettings );
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'twocoapi_' . md5( serialize( $twocheckoutapisettings ) );
			if ( $save ) {
				$settings = $this->wlm->GetOption( 'twocheckoutapisettings' );
				$this->wlm->SaveOption( 'twocheckoutapisettings', array_merge( $settings, $twocheckoutapisettings ) );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_send_json( $transient_result );
				}
			}
			if ( ! empty( $twocheckoutapi_seller_id ) && ! empty( $twocheckoutapi_publishable_key ) && ! empty( $twocheckoutapi_private_key ) ) {
				try {

					$url = sprintf( 'https://%s.2checkout.com/checkout/api/1/%s/rs/authService', empty( $twocheckoutapi_sandbox ) ? 'www' : 'sandbox', $twocheckoutapi_seller_id );

					$postdata = array(
						'sellerId' => $twocheckoutapi_seller_id,
						'privateKey' => $twocheckoutapi_private_key,
						'token' => '-',
					);

					$result = wp_remote_post(
						$url, array(
							'headers'   => array(
								'Content-Type' => 'application/json',
							),
							'body'      => json_encode( $postdata ),
							'method'    => 'POST',
						)
					);

					$result = json_decode( $result['body'] );

					if ( is_object( $result ) && isset( $result->exception ) && is_object( $result->exception ) ) {
						$code = wlm_arrval( $result->exception, 'errorCode' );
						if ( empty( $code ) || $code == '300' ) {
							$data['message'] = 'Unauthorized.';
						} else {
							$data['status'] = true;
							$data['message'] = 'Connected';
						}
					} else {
						$data['message'] = 'Connection Failed.';
					}
				} catch ( Exception $e ) {
					$data['message'] = $e->getMessage();
				}
			} else {
				$data['message'] = 'Disconnected.';
			}
			set_transient( $transient_name, $data, 60 * 15 );
			wp_send_json( $data );
		}
	}
	new WLM3_2CheckOutAPI_Hooks();
}
