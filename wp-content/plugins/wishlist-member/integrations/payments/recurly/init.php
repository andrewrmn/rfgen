<?php

if ( ! class_exists( 'WLM3_Recurly_Hooks' ) ) {
	class WLM3_Recurly_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_recurly_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data = array(
				'status' => false,
				'message' => 'Disconnected',
			);

			$transient_name = 'recurly_' . md5( serialize( $recurlyapikey ) );
			if ( $save ) {
				$this->wlm->SaveOption( 'recurlyapikey', $recurlyapikey );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_send_json( $transient_result );
				}
			}

			if ( ! empty( $recurlyapikey ) ) {
				try {
					ini_set( 'display_errors', 0 );
					require_once( $this->wlm->pluginDir . '/extlib/WP_RecurlyClient.php' );
					$recurly = new WP_RecurlyClient( $recurlyapikey );

					$recurly->request();
					$error = $recurly->last_error();
					if ( $error ) {
						$data['message'] = $error['message'];
					} else {
						$plans = $recurly->get_plans();
						foreach ( $plans as &$plan ) {
							$plan = array(
								'text' => $plan['name'],
								'value' => $plan['plan_code'],
								'id' => $plan['plan_code'],
							);
						}
						unset( $plan );
						array_unshift(
							$plans, array(
								'text' => '',
								'value' => '',
							)
						);
						$data['plans'] = $plans;

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
	new WLM3_Recurly_Hooks();
}
