<?php // initialization

if ( ! class_exists('WLM_Drip_Api') ) {
	require_once( $this->pluginDir . '/extlib/wlm_drip/Drip_API.class.php' );
}

if(!class_exists('WLM3_drip_Hooks')) {
	class WLM3_drip_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action('wp_ajax_wlm3_drip_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
				'campaigns' => array()
			);

			$transient_name = 'wlmdrip_' . md5($apitoken);
			$ar = $this->wlm->GetOption('Autoresponders');

			if($save) {
				$ar['drip']['apitoken'] = $apitoken;
				$this->wlm->SaveOption('Autoresponders', $ar);
				delete_transient($transient_name);
			} else {
				$transient_result = get_transient($transient_name);
				if($transient_result) {
					$transient_result['cached'] = 1;
					wp_die(json_encode($transient_result));
				}
			}

			// connect and get info
			try {
				$api = new WLM_Drip_Api ($apitoken);

				if($api) {
					$accounts = $api->get_accounts();
					if($api->get_error_code()) {
						$data['message'] = $api->get_error_message();
					} else {
						foreach($accounts AS $account) {
							$campaigns = $api->get_campaigns( array( 'account_id'=>$account['id'] ) );
							foreach($campaigns AS $campaign) {
								$campaign = array(
									'value' => sprintf('%s-%s', $account['id'], $campaign['id']),
									'text' => sprintf('%s - %s', $account['name'], $campaign['name']),
								);
								$data['campaigns'][] = $campaign;
							}
						}
						$data['status'] = true;
					}
				} else {
					$data['message'] = 'Invalid API Token';
				}

			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			set_transient($transient_name, $data, 60 * 15);
			wp_die(json_encode($data));
		}
	}
	new WLM3_drip_Hooks;
}