<?php // initialization

if(!class_exists('WLM3_Infusionsoft_Hooks')) {
	class WLM3_Infusionsoft_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			
			add_action('wp_ajax_wlm3_infusionsoft_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract ($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
			);
			if ( class_exists('WLM_Infusionsoft') ) {
				if($save) {
					$this->wlm->SaveOption('isapikey', $isapikey);
					$this->wlm->SaveOption('ismachine', $ismachine);
				} else {
					$transient_name = 'wlmis_' . md5($isapikey . $ismachine);
					$transient_result = get_transient($transient_name);
					if($transient_result) {
						$transient_result['cached'] = 1;
						wp_die(json_encode($transient_result));
					}					
				}
				if ( !$isapikey || !$ismachine) {
					$x = array();
					if(!$ismachine) $x[] = 'Machine name';
					if(!$isapikey) $x[] = 'Encrypted key';
					$data['message'] = implode(' and ', $x) . ' not provided';
				} else {
					$ifsdk = new WLM_Infusionsoft( $ismachine, $isapikey );
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
	new WLM3_Infusionsoft_Hooks;
}
