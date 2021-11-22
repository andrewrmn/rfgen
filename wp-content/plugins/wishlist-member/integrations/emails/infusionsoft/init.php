<?php // initialization

if(!class_exists('WLM3_Infusionsoft_AR_Hooks')) {
	if ( ! class_exists( '\WLM_Infusionsoft' ) ) {
		global $WishListMemberInstance;
		include_once $WishListMemberInstance->pluginDir . '/extlib/wlm-infusionsoft.php';
	}
	
	class WLM3_Infusionsoft_AR_Hooks {
		var $wlm;
		var $key = 'infusionsoft';
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			
			add_action('wp_ajax_wlm3_infusionsoft_ar_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract ($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
			);
			if ( class_exists('WLM_Infusionsoft') ) {
				$transient_name = 'wlmisar_' . md5($iskey . $ismname);
				if($save) {
					$ar = $this->wlm->GetOption('Autoresponders');
					$ar[$this->key]['iskey'] = $iskey;
					$ar[$this->key]['ismname'] = $ismname;
					$this->wlm->SaveOption('Autoresponders', $ar);
					//used by infusionsoft init
					$this->wlm->SaveOption('auto_ismachine', $ismname);
					$this->wlm->SaveOption('auto_isapikey', $iskey);
				} else {
					$transient_result = get_transient($transient_name);
					if($transient_result) {
						$transient_result['cached'] = 1;
						wp_die(json_encode($transient_result));
					}
				}
				if ( !$iskey || !$ismname) {
					$x = array();
					if(!$ismname) $x[] = 'Machine name';
					if(!$iskey) $x[] = 'Encrypted key';
					$data['message'] = implode(' and ', $x) . ' not provided';
				} else {
					$ifsdk = new WLM_Infusionsoft( $ismname, $iskey );
					if ( $ifsdk->is_api_connected() ) {
						$isTagsCategory = $ifsdk->get_tag_categories();
						$isTags         = $ifsdk->get_tags();
						$isTagsCategory = (array) $isTagsCategory;
						$isTagsCategory[0] = "- No Category -";
						asort($isTagsCategory);

						$data['tagscategory'] = $isTagsCategory;
						$data['tags'] = $isTags;

						$data['status'] = true;
						$data['message'] = 'OK';

						set_transient($transient_name, $data, 60 * 15);
					} else {
						$data['message'] = 'WishList Member could not establish a connection to Infusionsoft using the App Name and Encrypted Key that you entered. Please make sure that the information you entered are correct and Infusionsoft is not blocked by your server.';
					}
				}
			} else {
				$data['message'] = 'WLM_Infusionsoft not found';
			}
			wp_die(json_encode($data));
		}
	}
	new WLM3_Infusionsoft_AR_Hooks;
}
