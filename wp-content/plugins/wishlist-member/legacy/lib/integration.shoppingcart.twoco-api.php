<?php

/**
* 2Checkout Payment API references:
* Library - https://github.com/2Checkout/php-examples 
* Documentation - https://www.2checkout.com/documentation/payment-api/create-sale
*/

if (!class_exists('WLM_INTEGRATION_TWOCO_API')) {

	class WLM_INTEGRATION_TWOCO_API {
		var $wlm;
		var $twoco_ws;

		protected $sandbox = true;
		public function __construct() {
			add_action('admin_notices', array($this, 'notices'));

			global $WishListMemberInstance;
			$settings = $WishListMemberInstance->GetOption('twocheckoutapisettings');
		}
		public function twoco_api_process($wlm) {

			$this->wlm = $wlm;
			$action = trim(strtolower($_REQUEST['regform_action']));
			$valid_actions = array('charge', 'sync', 'update_payment', 'cancel', 'invoices', 'invoice','migrate');
			// if (!in_array($action, $valid_actions)) {
			// 	_e("Permission Denied", "wishlist-member");
			// 	die();
			// }
			// if (($action != 'sync' && $action != 'migrate') && !wp_verify_nonce($_REQUEST['nonce'], "eway-do-$action")) {
			// 	_e("Permission Denied", "wishlist-member");
			// 	die();
			// }
			switch ($action) {
				case 'charge':
					# code...
					$this->charge($_POST);
					break;
				case 'failed':
					throw new Exception("There was an error processing your Credit Card.");
					break;
				default:
					# code...
					break;
			}
		}
		public function charge($data = array()) {

			$settings = $this->wlm->GetOption('twocheckoutapisettings');

			try {
				$last_name = $data['last_name'];
				$first_name = $data['first_name'];

				if($data['charge_type'] == 'new') {
					if (empty($last_name) || empty($first_name) || empty($data['email'])) {
						throw new Exception("All fields are required");
					}
				}

				if(empty($data['cc_number']) || empty($data['cc_expmonth']) || empty($data['cc_expyear'])) {
					throw new Exception("All fields are required");
				}

				$_POST['level'] = $data['sku'];
				$_POST['lastname'] = $last_name;
				$_POST['firstname'] = $first_name;
				$_POST['action'] = 'wpm_register';
				$_POST['wpm_id'] = $data['sku'];
				$_POST['username'] = $data['email'];
				$_POST['email'] = $data['email'];
				$_POST['password1'] = $_POST['password2'] = $this->wlm->PassGen();
				
				$_POST['token'] = $_POST['token'];
				if($data['charge_type'] == 'new') {
					$this->charge_new($_POST);
				} else {
					$this->charge_existing($_POST);
				}
			} catch (Exception $e) {
				$this->fail(array(
					'msg' 	=> $e->getMessage(),
					'sku'	=> $data['sku']
				));
			}
		}
	

		public function add_to_level($user_id, $level_id, $txn_id) {
			$user = new \WishListMember\User($user_id);
			$levels = $user->Levels;

			$remaining_levels = array($level_id);
			foreach($levels as $i => $l) {
				$remaining_levels[] = $i;
			}

			$this->wlm->SetMembershipLevels($user_id, $remaining_levels);
			if($this->wlm->IsPPPLevel($level_id)) {
				list($tmp, $content_id) = explode('-', $level_id);
				$this->wlm->AddUserPostTransactionID($user_id, $content_id, $txn_id);
				$this->wlm->AddUserPostTimestamp($user_id, $content_id);
			} else {
				$this->wlm->SetMembershipLevelTxnID($user_id,  $level_id, $txn_id);
			}
		}
		public function charge_existing($data) {
			try {

				global $current_user;
				
				$cust_id = $current_user->ID;

				$txn_id  = $this->charge_customer($cust_id, $data, $data);

				//add user to level and redirect to the after reg url
				$this->add_to_level($current_user->ID, $data['sku'], $txn_id);
				$url = $this->wlm->GetAfterRegRedirect($data['sku']);
				wp_redirect($url);
				die();
			} catch (Exception $e) {
				$this->fail(array(
					'msg' 	=> $e->getMessage(),
					'sku'	=> $data['wpm_id']
				));
			}
		}
		
		private function charge_customer($cust_id, $cc_data, $var) {

			$wpm_levels 	= $this->wlm->GetOption('wpm_levels');

			$twocheckoutapisettings       = $this->wlm->GetOption('twocheckoutapisettings');
			$level_settings = $twocheckoutapisettings['connections'][$var['wpm_id']];

			$price = $level_settings['rebill_init_amount'];
			$startup_fee = '';
			
			if($level_settings['subscription'] == 1) {

				// If it's rebill type then set the price as the rebill amount
				$price = $level_settings['rebill_recur_amount'];

				// also set the start up fee
				$startup_fee = $level_settings['rebill_init_amount'];

				switch ($level_settings['rebill_interval_type']) {
					case 1:
						$interval = $level_settings['rebill_interval'] . ' Days';
						break;
					case 2:
						$interval = $level_settings['rebill_interval'] . ' Week';
						break;
					case 3:
						$interval = $level_settings['rebill_interval'] . ' Month';
						break;
					case 4:
						$interval = $level_settings['rebill_interval'] . ' Year';
						break;
				}
			}

			if ( extension_loaded( 'curl' ) && ! class_exists( 'Twocheckout', false ) ) {
				include_once $this->wlm->pluginDir . '/extlib/wlm_twoco_api/lib/Twocheckout.php';
			}

			$private_key = $twocheckoutapisettings['twocheckoutapi_private_key'];
			$seller_id = $twocheckoutapisettings['twocheckoutapi_seller_id'];

			Twocheckout::privateKey($private_key); 
			Twocheckout::sellerId($seller_id); 
			if($twocheckoutapisettings['twocheckoutapi_sandbox']) {
				Twocheckout::sandbox(true);
			} else {
				Twocheckout::sandbox(false);
			}

			try {
			    $charge = Twocheckout_Charge::auth(array(
			        "merchantOrderId" => $_POST['sku'],
			        "token"      => $var['token'],
			        "currency" => empty($twocheckoutapisettings['currency'])? 'USD' : $twocheckoutapisettings['currency'],
					"lineItems" => array( 0 =>
						array(
						"name" => $var['wpm_id'],
						"price" => $price,
						"type" => "product",
						"quantity" => "1",
						"productId" => $_POST['sku'],
						"recurrence" => $interval,
						"startupFee" => "",
						"duration" => ""
						),
					),
					"billingAddr" => array(
						"name" => $var['firstname'] . " " . $var['lastname'],
						"addrLine1" => 'NA',
						"city" => 'NA',
						"state" => 'NA',
						"zipCode" => 'NA',
						"country" => 'NA',
						"email" => $var['email'],
						"phoneNumber" => 'NA'
					)
			    ));

			    if ($charge['response']['responseCode'] == 'APPROVED') {
			       	return $result['response']['transactionId'];
			    } else {
					throw new Exception("There was an error processing your request, Please try again.");
				}

			} catch (Twocheckout_Error $e) {
			    throw new Exception($e->getMessage());
			}

		}
		
		public function charge_new($data) {
			try {
				//create the customer
				$txn_id  = $this->charge_customer('', $data, $data);
				
				$_POST['sctxnid'] = $txn_id;
				$this->wlm->ShoppingCartRegistration(true, false);

				$user = get_user_by('login', 'temp_' . md5($data['email']));
				$url = $this->wlm->GetContinueRegistrationURL($data['email']);
				wp_redirect($url);
				die();
			} catch (Exception $e) {

				$this->fail(array(
					'msg' 	=> $e->getMessage(),
					'sku'	=> $data['wpm_id']
				));
			}

		}
		
		private function create_customer($cust) {
			//create the cust
			$data['customerFirstName']  = $cust['first_name'];
			$data['customerLastName']   = $cust['last_name'];
			$data['customerEmail']      = $cust['email'];

			if(strtolower($res['CreateRebillCustomerResult']['Result']) !== 'success') {
				throw new Exception("Could not create customer");
			}
			return $res['CreateRebillCustomerResult']['RebillCustomerID'];
		}
		
		public function fail($data) {
			$uri = $_REQUEST['redirect_to'];
			if (stripos($uri, '?') !== false) {
				$uri .= "&status=fail&reason=" . preg_replace('/\s+/', '+', $data['msg']);
			} else {
				$uri .= "?&status=fail&reason=" . preg_replace('/\s+/', '+', $data['msg']);
			}

			$uri .= "#regform-" . $data['sku'];
			wp_redirect($uri);
			die();
		}
	}



}
