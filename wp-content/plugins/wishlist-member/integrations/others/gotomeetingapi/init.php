<?php // initialization
if(!class_exists('WLM3_GoToMeetingAPI_Hooks')) {
	class WLM3_GoToMeetingAPI_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action('wp_ajax_wlm3_gotomeetingapi_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract($_POST['data']['webinar']['gotomeetingapi']);
			$data = array(
				'status' => false,
				'message' => '',
			);

			$save = wlm_arrval($_POST['data'], 'save');

			$obj = new \WishListMember\Webinars\GTMAPI_OAuth_En();
			$oauth = new \WishListMember\Webinars\GTMAPI_OAuth($obj);

			$authorizationcode = trim($authorizationcode);

			$webinar = $this->wlm->GetOption('webinar');
			$transient_name = 'wlmgtmapi_' . md5(serialize($_POST['data']));
			$webinar = $this->wlm->GetOption('webinar');

			if($save && $webinar['gotomeetingapi']['authorizationcode'] != $authorizationcode) {
				$webinar['gotomeetingapi'] = array_merge($webinar['gotomeetingapi'], $_POST['data']['webinar']['gotomeetingapi']);
				$webinar['gotomeetingapi']['accesstoken'] = '';
				$webinar['gotomeetingapi']['organizerkey'] = '';
				$webinar['gotomeetingapi']['refreshtoken'] = '';

				$this->wlm->SaveOption('webinar', $webinar);
			} else {
				$transient_result = get_transient($transient_name);
				if($transient_result) {
					$transient_result['cached'] = 1;
					wp_send_json($transient_result);
				}

				if($webinar['gotomeetingapi'])
					extract($webinar['gotomeetingapi']);
				$save = false;
			}

			try {

				$authorizationcode = trim($authorizationcode);

				if(empty($authorizationcode)) {
					throw new Exception('Authorization Code Required');
				}

				$save = wlm_arrval($_POST['data'], 'save');
				if($save) {
					$oauth_data = $oauth->getAccessTokenv2($authorizationcode);

					if(!empty($oauth_data->error)) {
						throw new Exception('Invalid Authorization Code');
					}

					$webinar['gotomeetingapi']['accesstoken'] = $oauth_data['access_token'];
					$webinar['gotomeetingapi']['organizerkey'] = $oauth_data['organizer_key'];
					$webinar['gotomeetingapi']['authorizationcode'] = $authorizationcode;
					$webinar['gotomeetingapi']['refreshtoken'] = $oauth_data['refresh_token'];

					$this->wlm->SaveOption('webinar', $webinar);
				} else {
					$gtm_api = new \WishListMember\Webinars\GoToWebinarAPIIntegration();
					$gtm_api->refreshtoken();

					$webinar = $this->wlm->GetOption('webinar');
				}
				extract($webinar['gotomeetingapi']);
				
				$obj->setAccessToken($accesstoken);
				$obj->setOrganizerKey($organizerkey);


				$data['webinars'] = (array) $oauth->getWebinars();
				if($oauth->hasApiError()) {
					throw new Exception('Invalid Authorization Code');
				}

				foreach($data['webinars'] AS &$webinar) {
					$webinar = array(
						'id' => sprintf('%s---%s', $webinar->webinarKey, $webinar->subject),
						'value' => sprintf('%s---%s', $webinar->webinarKey, $webinar->subject),
						'name' => $webinar->subject,
						'text' => $webinar->subject,
					);
				}
				unset($webinar);
				$data['status'] = true;

			} catch (Exception $e) {
				$data['message'] = $e->getMessage();
			}

			set_transient($transient_name, $data, 60 * 15);
			wp_send_json($data);
		}
	}
	new WLM3_GoToMeetingAPI_Hooks;
}