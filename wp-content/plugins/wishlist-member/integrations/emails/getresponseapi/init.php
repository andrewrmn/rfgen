<?php // initialization
if(!class_exists('WLM3_GetResponseAPI_Hooks')) {
	class WLM3_GetResponseAPI_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action('wp_ajax_wlm3_getresponseAPI_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'wlmgrapi_' . md5($api_url . $apikey);
			if($save) {
				$ar = $this->wlm->GetOption('Autoresponders');
				$ar['getresponseAPI']['api_url'] = $api_url;
				$ar['getresponseAPI']['apikey'] = $apikey;
				$this->wlm->SaveOption('Autoresponders', $ar);
			} else {
				$transient_result = get_transient($transient_name);
				if($transient_result) {
					$transient_result['cached'] = 1;
					wp_die(json_encode($transient_result));
				}
			}

			if ( strpos($api_url, 'api2') !== false) {
				$array = array($apikey);
				$request = json_encode(array('method' => 'ping', 'params' => $array, 'id' => null));
				$res = wp_remote_post($api_url, array(
					'headers' => array('Content-type' => 'application/json'),
					'body' => $request
				));
				if(is_wp_error($res)) {
					$data['message'] = $res->get_error_message();
				} else {
					$res = json_decode($res['body']);
					if($res->result && !$res->error) {
						$data['status'] = true;
						$data['message'] = $res;
					} else {
						$data['message'] = $res->error->message;
					}
				}
			} else {
				require_once ($this->wlm->pluginDir . '/extlib/wlm-getresponse-v3.php');
				$api = new WLM_GETRESPONSE_V3($apikey,$api_url);
				$res = $api->ping();
				if ( isset($res->accountId) ) {
					$data['status'] = true;
					$data['message'] = $res;
				} else {
					$err = "";
					if ( isset($res->httpStatus) ) $err .= $res->httpStatus;
					if ( isset($res->code) ) $err .= ":" .$res->code;
					if ( isset($res->message) ) $err .= ":" .$res->message;
					$data['message'] = $err;
				}
			}
			set_transient($transient_name, $data, 60 * 15);
			wp_die(json_encode($data));
		}
	}
	new WLM3_GetResponseAPI_Hooks;
}