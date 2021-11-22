<?php // initialization
if(!class_exists('WLM3_MadMimi_Hooks')) {
	class WLM3_MadMimi_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action('wp_ajax_wlm3_madmimi_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'wlmmdmimi_' . md5($username . $api_key);
			if($save) {
				$ar = $this->wlm->GetOption('Autoresponders');
				$ar['madmimi']['username'] = $username;
				$ar['madmimi']['api_key'] = $api_key;
				$this->wlm->SaveOption('Autoresponders', $ar);
			} else {
				$transient_result = get_transient($transient_name);
				if($transient_result) {
					$transient_result['cached'] = 1;
					wp_send_json($transient_result);
				}
			}

			try {
				require_once ($this->wlm->pluginDir . '/extlib/madmimi/madmimi.php');
				$api = new WPMadMimi($username, $api_key);
				$lists = $api->get_lists();

				if(!is_array($lists)) {
					throw new Exception("Invalid API Credentials", 1);
				}
				foreach($lists AS &$list) {
					$list->id = $list->name;
					$list->text = $list->name;
					$list->value = $list->id;
				}
				unset($list);

				$data['status'] = true;
				$data['lists'] = $lists;

			} catch (Exception $e) {
				$data['message'] = $e->getMessage();
			}

			set_transient($transient_name, $data, 60 * 15);
			wp_send_json($data);
		}
	}
	new WLM3_MadMimi_Hooks;
}