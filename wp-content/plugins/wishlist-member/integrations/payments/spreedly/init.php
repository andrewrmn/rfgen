<?php

if ( ! class_exists( 'WLM3_Spreedly_Hooks' ) ) {
	class WLM3_Spreedly_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			require_once($this->wlm->pluginDir . '/extlib/class.spreedly.inc');

			add_action( 'wp_ajax_wlm3_spreedly_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'spreedly_' . md5( serialize( $_POST ) );
			if ( $save ) {
				$this->wlm->SaveOption( 'spreedlyname', $spreedlyname );
				$this->wlm->SaveOption( 'spreedlytoken', $spreedlytoken );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_send_json( $transient_result );
				}
			}
			if ( ! empty( $spreedlyname ) && ! empty( $spreedlytoken ) ) {
				try {
					Spreedly::configure($spreedlyname, $spreedlytoken);
					$r = SpreedlySubscriptionPlan::get_all();
					if (isset($r['ErrorCode'])) {
						if ($r['ErrorCode'] == '401') {
							$data['message'] = 'Invalid Pin Payments API Credentials';
						} else {
							$data['message'] = $r['Response'];
						}
					} else {
						$data['status'] = true;
						$data['message'] = 'Connected';
						$data['subscriptions'] = $r;
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
	new WLM3_Spreedly_Hooks();
}
