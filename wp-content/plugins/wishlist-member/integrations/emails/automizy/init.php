<?php // initialization
if(!class_exists('WLM3_Automizy_Hooks')) {
	class WLM3_Automizy_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			add_action('wp_ajax_wlm3_automizy_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
				'lists' => array(),
				'tags' => array()
			);

			$transient_name = 'wlmautomizy_' . md5($api_key);
			$ar = $this->wlm->GetOption('Autoresponders');
			if( $save ) {
				$ar['automizy']['api_key'] = $api_key;
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
				$data["status"] = true;
				$api = \WishListMember\Autoresponders\Automizy::_interface()->api();
				if ( $api ) {
					$lists_ret = $api->get( 'smart-lists' );
					if ( !$api->is_success() ) {
						$data["message"] = $api->get_last_error();
						$data["status"] = false;
						$api = false;
					}
				}

				if ( $api ) {

					$rec_count = 100;
					$lists = $api->get('smart-lists', array("limit"=>$rec_count,"fields"=>"id,name,contactsCount") );
					if ( $api->is_success() ) {
						$start = ceil ( $lists['totalItems'] / $rec_count);
						$offset = 1;
						$lists = $lists['smartLists'];
						while ( $offset <= $start ) {
							$args = array( "limit" => $rec_count, "page" => $offset*$rec_count,"fields"=>"id,name,contactsCount" );
						    $lists2 = $api->get('smart-lists',$args);
						    if ( $lists2 ) $lists = array_merge_recursive( $lists, $lists2['smartLists'] );
						    $offset += 1;
						}

						foreach ( $lists as $key => $value ) {
							$data["lists"][$value['id']] = $value['name'];
						}
					}

					$rec_count = 100;
					$tags = $api->get('contacts/tag-manager', array("limit"=>$rec_count) );
					if ( $api->is_success() ) {
						$start = ceil ( $tags['totalItems'] / $rec_count);
						$offset = 1;
						$tags = $tags['contactTags'];
						while ( $offset <= $start ) {
							$args = array( "limit" => $rec_count, "page" => $offset*$rec_count );
						    $tags2 = $api->get('contacts/tag-manager',$args);
						    if ( $tags2 ) $tags = array_merge_recursive( $tags, $tags2['contactTags'] );
						    $offset += 1;
						}
						foreach ( $tags as $key => $value ) {
							$data["tags"][$value['name']] = $value['name'];
						}
					}

					if ( count($data["lists"]) <= 0 ) {
						$data["status"] = false;
						$data["message"] = "You have no List in your Automizy account";
					} else {
						$data["status"] = true;
					}
				}

			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			set_transient($transient_name, $data, 60 * 15);
			wp_die( json_encode($data) );
		}
	}
	new WLM3_Automizy_Hooks;
}