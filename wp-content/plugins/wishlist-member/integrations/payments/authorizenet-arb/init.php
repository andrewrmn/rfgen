<?php // initialization

if ( ! class_exists( 'AuthnetARB' ) ) {
	include_once( $this->legacy_wlm_dir . '/extlib/wlm_authorizenet_arb/authnet_arb.php' );
}

if ( ! class_exists( 'WLM3_ANetARB_Hooks' ) ) {
	class WLM3_ANetARB_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_anetarb_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			extract( $anetarbsettings );
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'anetarb_' . md5( serialize( $anetarbsettings ) );
			if ( $save ) {
				$this->wlm->SaveOption( 'anetarbsettings', $anetarbsettings );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_send_json( $transient_result );
				}
			}
			if ( ! empty( $api_login_id ) && ! empty( $api_transaction_key ) ) {
				try {
					$test = ! empty( $sandbox_mode );

					$arb = new WLMAuthnet\AuthnetARB( $api_login_id, $api_transaction_key, $test );
					$arb->do_apicall( 'authenticateTestRequest', array() );

					$data['status'] = true;
					$data['message'] = 'Connected';
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
	new WLM3_ANetARB_Hooks();
}
