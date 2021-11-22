<?php // initialization
if(!class_exists('WLM3_iContact_Hooks')) {
	class WLM3_iContact_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action('wp_ajax_wlm3_icontact_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'wlmicntct_' . md5($icusername . $icapipassword . $icapiid);
			$ar = $this->wlm->GetOption('Autoresponders');
			if($save) {
				$ar['icontact']['icusername'] = $icusername;
				$ar['icontact']['icapipassword'] = $icapipassword;
				$ar['icontact']['icapiid'] = $icapiid;
				$this->wlm->SaveOption('Autoresponders', $ar);
			} else {
				$transient_result = get_transient($transient_name);
				if($transient_result) {
					$transient_result['cached'] = 1;
					wp_send_json($transient_result);
				}
			}

			$headers = array('headers' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
				'API-Version' => '2.0',
				'API-AppId' => $icapiid,
				'API-Username' => $icusername,
				'API-Password' => $icapipassword,
			));
			$url = "https://app.icontact.com/icp/a/";

			$result = json_decode(wp_remote_retrieve_body(wp_remote_get($url, $headers)));
			if(isset($result->errors)) {
				$data['message'] = implode('<br>', $result->errors);
			} elseif(isset($result->accounts)) {
				$acct_id = $result->accounts[0]->accountId;
				$data['icaccountid'] = $acct_id;

				$ar['icontact']['icaccountid'] = $acct_id;
				$this->wlm->SaveOption('Autoresponders', $ar);

				$url = "https://app.icontact.com/icp/a/{$acct_id}/c";
				$result = json_decode(wp_remote_retrieve_body(wp_remote_get($url, $headers)));
				$folders = array();
				if(!empty($result->clientfolders)) {
					foreach((array) $result->clientfolders AS $clientfolder) {
						$folder = array('id' => $clientfolder->clientFolderId);
						$folder['name'] = $clientfolder->clientFolderId;
						if(!empty($clientfolder->name)) {
							$folder['name'] = $clientfolder->name;
						}
						$folder['text'] = $folder['name'];
						$folder['value'] = $folder['id'];
						$folders[] = $folder;
					}
				}
				$data['folders'] = $folders;

				if(!($ar['icontact']['icfolderid'] + 0)) {
					$ar['icontact']['icfolderid'] = $folders[0]['id'];
					$this->wlm->SaveOption('Autoresponders', $ar);
				}

				$data['icfolderid'] = $folder_id = $ar['icontact']['icfolderid'];

				$url = "https://app.icontact.com/icp/a/{$acct_id}/c/{$folder_id}/lists";
				$result = json_decode(wp_remote_retrieve_body(wp_remote_get($url, $headers)));

				$lists = array();
				if(!empty($result->lists)) {
					foreach((array) $result->lists AS $list) {
						$l = array('id' => $list->listId);
						$l['name'] = $list->listId;
						if(!empty($list->name)) {
							$l['name'] = $list->name;
						}
						$l['text'] = $l['name'];
						$l['value'] = $l['id'];
						$lists[] = $l;
					}
				}

				$data['lists'] = $lists;

				$data['status'] = true;
			} else {
				$data['message'] = 'An unknown error occured. Please double check your API credentials.';
			}
			$data['message'] = str_ireplace('api', 'API', $data['message']);

			set_transient($transient_name, $data, 60 * 15);
			wp_send_json($data);
		}
	}
	new WLM3_iContact_Hooks;
}