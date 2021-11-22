<?php

if ( ! class_exists( 'WLM3_Eway_Hooks' ) ) {
	class WLM3_Eway_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_eway_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			extract( $ewaysettings );
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'eway_' . md5( serialize( $ewaysettings ) );
			if ( $save ) {
				$settings = (array) $this->wlm->GetOption( 'ewaysettings' );
				$this->wlm->SaveOption( 'ewaysettings', array_merge( $settings, $ewaysettings ) );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_send_json( $transient_result );
				}
			}

			if ( ! empty( $eway_customer_id ) && ! empty( $eway_username ) && ! empty( $eway_password ) ) {
				try {
					// require_once $this->wlm->pluginDir . '/extlib/eway/EwayWebserviceClient.php';
					require_once $this->wlm->pluginDir . '/extlib/eway/Eway24WebserviceClient.php';

					$svc = new Eway24WebserviceClient( $eway_customer_id, $eway_username, $eway_password, ! empty( $eway_sandbox ) );
					$params = array(
						'ewayCustomerInvoiceRef' => '138433888562',
					);
					$res = $svc->call( 'Transaction24HourReportByInvoiceReference', $params );

					$result = $res['Transaction24HourReportByInvoiceReferenceResult'];
					if ( empty( $result['ewayTrxnStatus'] ) ) {
						if ( ! empty( $result['ewayTrxnError'] ) ) {
							$data['message'] = $result['ewayTrxnError'];
						}
					} else {
						$data['message'] = 'Connected';
						$data['status'] = true;
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
	new WLM3_Eway_Hooks();
}
