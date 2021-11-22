<?php // initialization
if ( ! class_exists( 'WLM3_Moosend_Hooks' ) ) {
	class WLM3_Moosend_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_moosend_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data = array(
				'status'  => false,
				'message' => '',
			);

			$transient_name = 'wlmmoosend_' . md5( $api_url . $api_key );
			if ( $save ) {
				$ar = $this->wlm->GetOption( 'Autoresponders' );

				$ar['moosend']['api_key'] = $api_key;
				$this->wlm->SaveOption( 'Autoresponders', $ar );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_die( json_encode( $transient_result ) );
				}
			}

			$response = wp_remote_get(
				sprintf( 'https://api.moosend.com/v3/lists.json?apikey=%s&WithStatistics=false&PageSize=1000', $api_key )
			);

			$body = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $body ) && empty( $body->Error ) ) {
				$data['status'] = true;
				$data['lists']  = $body->Context->MailingLists;
			} else {
				$data['message'] = 'Invalid API Key';
			}

			set_transient( $transient_name, $data, 60 * 15 );
			wp_die( json_encode( $data ) );
		}
	}
	new WLM3_Moosend_Hooks();
}
