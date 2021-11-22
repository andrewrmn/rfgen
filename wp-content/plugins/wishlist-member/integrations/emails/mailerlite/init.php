<?php // initialization
if ( ! class_exists( 'WLM3_MailerLite_Hooks' ) ) {
	class WLM3_MailerLite_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_mailerlite_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data = array(
				'status'  => false,
				'message' => '',
			);

			$transient_name = 'wlmmailerlite_' . md5( $$api_key );
			if ( $save || true ) {
				$ar = $this->wlm->GetOption( 'Autoresponders' );

				$ar['mailerlite']['api_key'] = $api_key;
				$this->wlm->SaveOption( 'Autoresponders', $ar );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_die( json_encode( $transient_result ) );
				}
			}

			$response = wp_remote_get(
				sprintf( 'https://api.mailerlite.com/api/v2/groups' ),
				array(
					'headers'    => array(
						'X-MailerLite-ApiKey' => $api_key,
					),
					'user-agent' => 'WishList Member/' . $this->wlm->Version,
				)
			);

			$body = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $body ) && empty( $body->error ) ) {
				$data['status'] = true;
				$data['lists']  = $body;
			} else {
				$data['message'] = $body->error->message;
			}

			set_transient( $transient_name, $data, 60 * 15 );
			wp_die( json_encode( $data ) );
		}
	}
	new WLM3_MailerLite_Hooks();
}
