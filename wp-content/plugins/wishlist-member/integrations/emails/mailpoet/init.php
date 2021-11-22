<?php // initialization
if ( ! class_exists( 'WLM3_MailPoet_Hooks' ) ) {
	class WLM3_MailPoet_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_mailpoet_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data = array(
				'status'  => false,
				'message' => '',
			);

			$transient_name = 'wlmmailpoet_' . md5( $personal_access_token );
			if ( $save ) {
				$ar = $this->wlm->GetOption( 'Autoresponders' );

				$ar['mailpoet']['personal_access_token'] = $personal_access_token;
				$this->wlm->SaveOption( 'Autoresponders', $ar );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_send_json( $transient_result );
				}
			}

			$response = wp_remote_get(
				'https://api.mailpoet.com/lists',
				array(
					'headers' => array(
						'Authorization' => 'Bearer ' . $personal_access_token,
					),
				)
			);

			$body = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $body ) ) {
				$data['status'] = true;
				$data['lists']  = $body;
			} else {
				$data['message'] = 'Invalid Personal Access Token';
			}

			set_transient( $transient_name, $data, 60 * 15 );
			wp_send_json( $data );
		}
	}
	new WLM3_MailPoet_Hooks();
}
