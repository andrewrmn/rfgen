<?php // initialization
if(!class_exists('WLM3_Drip2_Hooks')) {
	class WLM3_Drip2_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action('wp_ajax_wlm3_drip2_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
				'accounts' => array(),
				'tags' => array()
			);

			$transient_name = 'wlmdrip2_' . md5($apitoken);
			$ar = $this->wlm->GetOption('Autoresponders');

			if($save) {
				$ar['drip2']['apitoken'] = $apitoken;
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
				$api = \WishListMember\Autoresponders\Drip2::_interface()->api();

				if($api) {
					$accounts = $api->get_accounts();
					if($api->get_error_code()) {
						$data['message'] = $api->get_error_message();
					} else {
						foreach($accounts AS $account) {
							$data['accounts'][$account['id']] = array(
								'value' => $account['id'],
								'text' => $account['name']
							);
						}
						$data['status'] = true;
					}

					if(is_array($data['accounts']) && $data['accounts']) {
						$selected_account = $ar['drip2']['account'];
						if(empty($selected_account)) {
							$selected_account = $data['accounts'][0]['value'];
							$ar['drip2']['account'] = $selected_account;
							$this->wlm->SaveOption('Autoresponders', $ar);
						}
						foreach($data['accounts'] AS $account) {
							$tags = $api->get_tags( array( 'account_id'=>$account['value'] ) );
							$data['tags'][$account['value']] = array();
							foreach($tags AS $tag) {
								$tag = array(
									'value' => $tag,
									'text' => $tag,
								);
								$data['tags'][$account['value']][] = $tag;
							}
						}
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
	new WLM3_Drip2_Hooks;
}