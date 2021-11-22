<?php // initialization
if(!class_exists('WLM3_AWeberAPI_Hooks')) {
	class WLM3_AWeberAPI_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action('wp_ajax_wlm3_aweberapi_test_keys', array($this, 'test_keys'));
			add_action('admin_init', array($this, 'save_key_callback'));
		}

		function connect($auth_key) {
			$integration = \WishListMember\Autoresponders\AweberAPI::_interface();
			$curl_exists = function_exists('curl_init');
			$access_tokens = array('', '');

			$data = $this->wlm->GetOption('Autoresponders');
			$msg = 'Disconnected';

			$result = false;
			$lists = array();

			// If curl is disabled, don't run Aweber API connection and return error msg
			if ( $curl_exists ) {
				// Try Connecting and if there's an error, catch it so that the page doesn't go blank
				try {
					$access_tokens = $integration->get_access_tokens();
					if ( !empty( $access_tokens ) ) $connected = true;

					// !connected but we have an auth key
					// let's try to connect one last time
					if ( !$connected && !empty( $auth_key ) ) {
						$access_tokens = $integration->renew_access_tokens();
						if ( !empty( $access_tokens ) ) {
							//save the new access tokens
							$data['aweberapi']['access_tokens'] = $access_tokens;
							$connected = true;
							$result = true;
						} else {
							$access_tokens = array('', '');
							$msg = __('Unable to connect to your Aweber account. Please check and make sure that the Authorization Key is correct.', 'wishlist-member');
						}
						$this->wlm->SaveOption('Autoresponders', $data);
					}

					if ( $connected ) {
						$lists = $integration->get_lists();
						// reformat
						$list_tmp = array();
						foreach ($lists as $item) {
							$list_tmp[$item['id']] = $item;
						}
						$lists = $list_tmp;
						$result = true;
						$msg = '';
					}
				} catch (Exception $e) {
					$msg = $e->getMessage();
				}
			} else {
				$msg = __('Aweber API integration needs the CURL enabled for it to work. Please contact your host and have them enable it on your server  to continue integrating with AWeber API.', 'wishlist-member');
			}

			return array(
				'status' => $result,
				'message' => $msg,
				'lists' => $lists,
				'data' => $data['aweberapi'],
			);

		}
		function test_keys($data = null, $return = false) {
			if(is_array($data)) {
				extract($data);
			} else {
				extract($_POST['data']);
			}
			$data = array(
				'status' => false,
				'message' => 'Disconnected',
			);

			$transient_name = 'wlmawbrapi_' . md5($auth_key);
						
			if($remove){
			    $ar = $this->wlm->GetOption('Autoresponders');
			    $ar['aweberapi']['auth_key'] = $auth_key;
			    unset($ar['aweberapi']['auth_key'] );
			    unset($ar['aweberapi']['access_tokens'] );
			    $this->wlm->SaveOption('Autoresponders', $ar);
			    delete_transient($transient_name);
			    wp_die(json_encode($data));
			}
			
			if($save) {
				$ar = $this->wlm->GetOption('Autoresponders');
				$ar['aweberapi']['auth_key'] = $auth_key;
				$this->wlm->SaveOption('Autoresponders', $ar);
			} else {
				$transient_result = get_transient($transient_name);
				if($transient_result) {
					$transient_result['cached'] = 1;
					wp_die(json_encode($transient_result));
				}
			}

			$data = $this->connect($auth_key);

			if($data['status']) {
				$data['data']['connected_auth_key'] = sprintf(
					'%s|%s',
					preg_replace('/\|$/', '', $data['data']['auth_key']),
					implode('|', $data['data']['access_tokens'])
				);
				$data['data']['auth_key'] = $data['data']['connected_auth_key'];
			}

			set_transient($transient_name, $data, 60 * 15);
			if($return) {
				return $data;
			}
			wp_die(json_encode($data));
		}
		function save_key_callback() {
			extract($_GET);	
			if(!empty($aweberapi_connect) && !empty($authorization_code)) {
				$data = array(
					'save' => 1,
					'auth_key' => $authorization_code
				);
				$result = $this->test_keys($data, true);
				$url = remove_query_arg(array('aweberapi_connect', 'authorization_code')) . '#aweberapi';
				wp_redirect($url);
				exit; 
			}
		}
	}
	new WLM3_AWeberAPI_Hooks;
}