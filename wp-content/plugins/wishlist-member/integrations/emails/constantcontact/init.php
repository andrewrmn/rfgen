<?php // initialization
require_once($this->legacy_wlm_dir . '/extlib/ConstantContact.php');

if(!class_exists('WLM3_ConstantContact_Hooks')) {
	class WLM3_ConstantContact_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action('wp_ajax_wlm3_constantcontact_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']); // ccusername, ccpassword
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'wlmcnstnt_' . md5($ccusername . $ccpassword);
			if($save) {
				$ar = $this->wlm->GetOption('Autoresponders');
				$ar['constantcontact']['ccusername'] = $ccusername;
				$ar['constantcontact']['ccpassword'] = $ccpassword;
				$this->wlm->SaveOption('Autoresponders', $ar);
			} else {
				$transient_result = get_transient($transient_name);
				if($transient_result) {
					$transient_result['cached'] = 1;
					wp_die(json_encode($transient_result));
				}
			}

			// test connection and get lists
			$err_msg = __('Cannot connect to Constant Contact.', 'wishlist-member');
			$cc_api = New ConstantContact($ccusername, $ccpassword);

			$data['status'] = true;
			if(is_object($cc_api)) {
				$lists = $cc_api->get_all_lists();
				$data['lists'] = $lists;
				$err_msg = '';
			}
			$code = wlm_arrval($cc_api, 'http_response_code');
			if($code && $code > 200) {
				$data['status'] = false;
				if(preg_match_all('/<h1>.+?<\/h1>/i', $cc_api->http_response_body, $match)) {
					$data['message'] = strip_tags(implode('. ', $match[0]));
				} else {
					$data['message'] = 'Error connecting to Constant Contact.';
				}
			}

			set_transient($transient_name, $data, 60 * 15);
			wp_die(json_encode($data));
		}
	}
	new WLM3_ConstantContact_Hooks;
}