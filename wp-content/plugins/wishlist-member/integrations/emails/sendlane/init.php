<?php // initialization
if(!class_exists('WLM3_Sendlane_Hooks')) {
	class WLM3_Sendlane_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			add_action('wp_ajax_wlm3_sendlane_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
				'lists' => array(),
				'tags' => array()
			);

			$transient_name = 'wlmsendlane_' . md5($api_key);
			$ar = $this->wlm->GetOption('Autoresponders');
			if( $save ) {
				$ar['sendlane']['api_key'] = $api_key;
				$ar['sendlane']['api_hash'] = $api_hash;
				$ar['sendlane']['subdomain'] = $subdomain;
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
				$api = \WishListMember\Autoresponders\SendLane::_interface()->api();

				if($api) {
					$lists_ret = $api->post('lists');
					if ( isset( $lists_ret['error'] ) ) {
						$data["message"] = implode( ",", $lists_ret['error']);
						$data["status"] = false;
						$api = false;
					}
				}

				if ( $api ) {
					$record_limit = 20;
					//get the list
					$ret = array(1);$start = 1;
					while ( count($ret) > 0 ) {
						$ret = $api->post('lists', array("start"=>$start,"limit"=>$record_limit) );
						$ret  = isset( $ret['error'] ) ? array() : $ret;
						if ( count( $ret ) ) {
							foreach ( $ret as $key => $value ) {
								$data["lists"][$value['list_id']] = $value['list_name'];
							}
							//if result is less than $record_limit, no more records left
							if ( count( $ret ) < $record_limit ) $ret = array();
						}
						$start++;
					}

					//get the tags
					$ret = array(1);$start = 1;
					while ( count($ret) > 0 ) {
						$ret = $api->post('tags', array("start"=>$start,"limit"=>$record_limit) );
						$ret  = isset( $ret['error'] ) ? array() : $ret;
						if ( count( $ret ) ) {
							foreach ( $ret as $key => $value ) {
								$data["tags"][$value['tag_id']] = $value['tag_name'];
							}
							//if result is less than $record_limit, no more records left
							if ( count( $ret ) < $record_limit ) $ret = array();
						}
						$start++;
					}

					$data["status"] = true;
				}

			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			set_transient($transient_name, $data, 60 * 15);
			wp_die( json_encode($data) );
		}
	}
	new WLM3_Sendlane_Hooks;
}