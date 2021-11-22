<?php // initialization
if(!class_exists('WLM3_ActiveCampaign_Hooks')) {
	class WLM3_ActiveCampaign_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action('wp_ajax_wlm3_activecampaign_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'wlmactvcmpn_' . md5($api_url . $api_key);
			if($save) {
				$ar = $this->wlm->GetOption('Autoresponders');
				$ar['activecampaign']['api_url'] = $api_url;
				$ar['activecampaign']['api_key'] = $api_key;
				$this->wlm->SaveOption('Autoresponders', $ar);
			} else {
				$transient_result = get_transient($transient_name);
				if($transient_result) {
					$transient_result['cached'] = 1;
					wp_send_json($transient_result);
				}
			}

			try {
				require_once ($this->wlm->pluginDir . '/extlib/active-campaign/active-campaign.php');
				$api = new WpActiveCampaign($api_url, $api_key);

				$lists = $api->get_lists();
				foreach($lists AS &$list) {
					$list->text = $list->name;
					$list->value = $list->id;
				}
				unset($list);
				$data['lists'] = $lists;

				$data['status'] = true;
				$data['message'] = 'Connected';
			} catch (Exception $e) {
				$data['message'] = $e->getMessage();
				$data['lists'] = array();
			}

			set_transient($transient_name, $data, 60 * 15);
			wp_send_json($data);
		}
	}
	new WLM3_ActiveCampaign_Hooks;
}