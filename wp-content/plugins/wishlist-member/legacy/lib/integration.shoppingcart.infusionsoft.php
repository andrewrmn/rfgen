<?php

/*
 * InfusionSoft Shopping Cart Integration Functions
 * Original Author : Mike Lopez
 * Version: $Id: integration.shoppingcart.infusionsoft.php 7502 2021-01-12 16:42:09Z mike $
 */
//this line is already set on integration.shoppingcarts.php
// $__classname__ = 'WLM_INTEGRATION_INFUSIONSOFT';
// $__optionname__ = 'isthankyou';
// $__methodname__ = 'InfusionSoft';

if (!class_exists('WLM_INTEGRATION_INFUSIONSOFT')) {

	class WLM_INTEGRATION_INFUSIONSOFT {

		private $wlm          = NULL;
		private $machine_name = "";
		private $api_key      = "";
		private $ifsdk        = NULL;
		private $log          = false;
		private $debug        = false;
		private $force        = false;
		private $invmarker    = 'InfusionSoft';

		function __construct() {
			global $WishListMemberInstance;

			//make sure that WLM active and infusiosnsoft connection is set
			//WLM_Infusionsoft file is included in init file of this integration
			if ( ! isset( $WishListMemberInstance ) || ! class_exists( 'WLM_Infusionsoft' ) ) return;

			$this->wlm          = $WishListMemberInstance;
			$this->machine_name = $this->wlm->GetOption('ismachine');
			$this->api_key      = $this->wlm->GetOption('isapikey');
			$this->log          = $this->wlm->GetOption('isenable_log');
			$this->machine_name = $this->machine_name ? $this->machine_name : "";
			$this->api_key      = $this->api_key ? $this->api_key : "";
			//check if debugging is ON
			$this->debug = isset( $_GET['debug'] ) ? true : false;
			$this->force = isset( $_GET['force'] ) ? true : false;

			$apilogfile = false;
			if ( $this->log ) {
                $date_now    = date('m-d-Y');
                $apilogfile  = $this->wlm->pluginDir . "/ifs_logs_{$date_now}.csv";
			}

			if ( $this->api_key && $this->machine_name ) {
				$this->ifsdk = new WLM_Infusionsoft( $this->machine_name, $this->api_key, $apilogfile );
			}
		}

		//this is the function that is being called by the Thank You URL
		function infusionsoft( $that ) {
			if ( ! $this->ifsdk || ! $this->ifsdk->is_api_connected() ) {
				if ( $this->debug ) {
					_e( "Unable to establish Infusionsoft API connection. Please check your Infusionsoft App Name and API Key.", "wishlist-member" );
					die();
				} else {
					return false;
				}
			}

			$action = isset( $_GET['iscron'] ) ? $_GET['iscron'] : "";
			$action = $action == "1" ? "iscron" : "";
			$action = isset( $_POST['contactId'] ) ? "http-post" : $action;

			switch ( $action ) {
				case 'http-post':
					$this->process_http_post();
					break;
				case 'iscron':
					$this->process_cron();
					break;
				default:
					$this->process_registration();
					break;
			}
		}

		private function process_http_post() {
			$contactid = $_POST['contactId'];
			$add_level = isset($_POST['add']) ? $_POST['add'] : false;
			$remove_level = isset($_POST['remove']) ? $_POST['remove'] : false;
			$cancel_level = isset($_POST['cancel']) ? $_POST['cancel'] : false;
			$debug = isset($_GET['debug']) ? true : false;

			//if none of these are present, we stop
			if( ! $add_level && ! $remove_level && ! $cancel_level ) {
				if ( $debug ) {
					echo "No action found. <br />";
				}
				exit;
			}
			//check if contact exist in infusionsoft
			$contact = $this->ifsdk->get_contact_details( $contactid );
			if ( ! $contact) {
				if ( $this->debug ) {
					_e( "No Contact found.", "wishlist-member" );
				}
				die();
			}
			usleep(1000000);
			$uname = isset($_POST['WLMUserName']) && $_POST['WLMUserName'] != "" ? $_POST['WLMUserName'] : $contact['Email'];
			$pword = isset($_POST['WLMPassWord']) && $_POST['WLMPassWord'] != "" ? $_POST['WLMPassWord'] : $this->wlm->PassGen();
			$regemail = isset($_POST['WLMRegEmail']) && strtolower($_POST['WLMRegEmail']) == "no" ? false : true;
			$sequential = isset($_POST['WLMSequential']) && strtolower($_POST['WLMSequential']) == "no" ? false : true;
			//first we get check if this user exist using txnid
			$wpm_user =  $this->wlm->GetUserIDFromTxnID("IFContact-{$contactid}");
			$new_user = false;

			//if not, check if it exist using the email address
			if ( ! $wpm_user ) {
				if ( $this->debug ) {
					_e( "No User associated with this Contact.<br />Checking for contact email if matches found on user. <br />", 'wishlist-member');
				}

				if ( function_exists('get_user_by') ) {
					$wpm_user = get_user_by( 'email', $contact["Email"] );
					$wpm_user = $wpm_user ? $wpm_user->ID : false;
				} else {
					$wpm_user = email_exists( $contact["Email"] );
				}
			}

			//if not, check if it exist using the username
			if ( ! $wpm_user ) {
				if ( $this->debug ) {
					_e( "Checking for username if matches found on username. <br />", 'wishlist-member');
				}
				if ( function_exists('get_user_by') ) {
					$wpm_user = get_user_by( 'login', $uname );
					$wpm_user = $wpm_user ? $wpm_user->ID : $wpm_user;
				}
			}

			//if the user does not exist yet and its adding to level
			//lets create a new user using api
			if ( ! $wpm_user && $add_level ) {
				if ( $this->debug ) {
					_e( "No user found. Creating user. (Available if add is present) <br />", 'wishlist-member');
				}

				//if username was not specified, lets create using level setting format
				if ( !isset($_POST['WLMUserName']) || empty($_POST['WLMUserName']) ) {
					if ( $this->debug ) {
						echo "Generating username!<br />";
					}
					//make sure that the function exists
					if ( function_exists('wlm_generate_username') ) {
						if ( $this->debug ) {
							echo "...Checking Level Settings if username format is specified.<br />";
						}
						$lvls = array_intersect( \WishListMember\Level::get_all_levels(), explode( ',', $add_level ) );
						foreach( $lvls AS $lvl ) {
							$lvl = new \WishListMember\Level( $lvl );
							if( $lvl->autocreate_account_enable ) {
								$registration_level = $lvl;
								break;
							}
						}
					}
					if ( $registration_level && $registration_level->ID && $registration_level->autocreate_account_enable ) {
						if ( $this->debug ) {
							echo "...Generating username using Level Setting format.<br />";
						}
						$userdata = [
							'email' => $contact['Email'],
							'first_name' => $contact['FirstName'],
							'last_name'  => $contact['LastName'],
						];

						// grab the username format from level settings
						$username_format = trim( $registration_level->autocreate_account_username ) ?: $WishListMemberInstance->level_defaults['autocreate_account_username'];
						$uname = wlm_generate_username( $userdata, $username_format );
						if ( $uname === false ) {
							if ( $this->debug ) {
								echo "...**Unable to generate using Level Setting format.**<br />";
							}
							$uname = "";
						}
					}
					//still empty?
					if ( empty( $uname ) ) {
						if ( $this->debug ) {
							echo "...Using Contact Username or Email for username.<br />";
						}
						$uname = isset( $contact['Username'] ) && !empty( $contact['Username'] ) ?  trim( $contact['Username'] ) : $uname;
						$uname = empty( $uname ) ?  $contact['Email'] : $uname;
					}
				}

				// prepare data
				$data = array();
				$data['last_name'] = $contact['LastName'];
				$data['first_name'] = $contact['FirstName'];
				$data['user_login'] = $uname;
				$data['user_email'] = $contact['Email'];
				$data['user_pass'] = $pword;
				$data['display_name'] ="{$contact['FirstName']} {$contact['LastName']}";
				$data['Sequential'] = $sequential;
				$address['address1'] = $contact['StreetAddress1'];
				$address['address2'] = $contact['StreetAddress2'];
				$address['city'] = $contact['City'];
				$address['state'] = $contact['State'];
				$address['zip'] = $contact['PostalCode'];
				$address['country'] = $contact['Country'];
				$data["SendMail"] = $regemail;
				$data["Levels"] = explode(",", $add_level); //add the level here
				$wpm_errmsg = '';

				if ( function_exists("wlmapi_add_member") ) {
					if ( $debug ) { echo "Adding using WLM internal function.<br />"; }
					$ret = wlmapi_add_member( $data );
				} else {
					if ( $debug ) { echo "Adding sing WLM API Call.<br />"; }
					$wlm_api_key = $this->wlm->GetOption("WLMAPIKey");
					$wlm_site_url = home_url('/');
					$wlm_apiclass = new wlmapiclass($wlm_site_url,$wlm_api_key);
					$wlm_apiclass->return_format = "php";
					$ret = unserialize( $wlm_apiclass->post( "/members", $data ) );
				}

				if ( $ret["success"] && isset( $ret["member"][0]["ID"] ) ) {
					$wpm_user = $ret["member"][0]["ID"];
				} else {
					if ( $this->debug ) {
						_e( " Adding User Failed. Returns the following:", 'wishlist-member');
					}
				}

				if ( $this->debug ) {
					echo "<pre>";
					var_dump($ret);
					echo "</pre><br />";
				}
				$new_user = true; //this is new user
			}

			//assign infusiom contact id if none is assigned to this user
			if ( $wpm_user ) {
				$ifcontact = $this->wlm->Get_UserMeta( $wpm_user, "wlminfusionsoft_contactid" );
				if ( ! $ifcontact ) {
					if ( $this->debug ) {
						_e( "Updating Contact ID for user.<br />", 'wishlist-member');
					}
					$this->wlm->Update_UserMeta( $wpm_user, "wlminfusionsoft_contactid", $contactid );
				}
			}

			$current_user_mlevels = $this->wlm->GetMembershipLevels( $wpm_user );
			$wpm_levels = $this->wlm->GetOption('wpm_levels');

			if ( $this->debug ) {
				_e( "Performing operations. Please wait..<br />", 'wishlist-member');
			}

			//add
			if ( $wpm_user && $add_level ) {
				$user_mlevels = $current_user_mlevels;
				$add_level_arr = explode(",", $add_level);
				if ( in_array( "all", $add_level_arr ) ) {
					$add_level_arr = array_merge( $add_level_arr, array_keys( $wpm_levels ) );
					$add_level_arr = array_unique( $add_level_arr );
				}
				if ( ! $new_user ) {
					if ( $this->debug ) {
						_e( "Adding Levels.<br />", 'wishlist-member');
					}
					foreach ( $add_level_arr as $id=>$add_level ) {
						if ( isset( $wpm_levels[$add_level] ) ) { //check if valid level
							if ( ! in_array( $add_level, $user_mlevels ) ) {
								$user_mlevels[] = $add_level;
								$this->wlm->SetMembershipLevels( $wpm_user, $user_mlevels );
								$this->wlm->SetMembershipLevelTxnID( $wpm_user, $add_level, "IFContact-{$contactid}" );
							}else{
								//just uncancel the user
								$ret = $this->wlm->LevelCancelled( $add_level, $wpm_user, false );
							}
						} elseif ( strrpos( $add_level, "payperpost" ) !== false ) {
							$this->wlm->SetPayPerPost( $wpm_user, $add_level );
						}
					}
					if ( $this->debug ) {
						$cnt = count($add_level_arr);
						_e( "{$cnt} Levels Added.<br />", 'wishlist-member');
					}
				} else {
					if ( $this->debug ) {
						_e( "Updating Level Transaction ID.<br />", 'wishlist-member');
					}
					foreach( $add_level_arr as $id=>$add_level ) {
						if ( isset( $wpm_levels[$add_level] ) ) { //check if valid level
							$this->wlm->SetMembershipLevelTxnID( $wpm_user, $add_level, "IFContact-{$contactid}" );
						}
					}
				}
			}

			//cancel
			if ( $wpm_user && $cancel_level ) {
				if ( $this->debug ) {
					_e( "Cancelling Levels.<br />", 'wishlist-member');
				}
				$user_mlevels = $current_user_mlevels;
				$cancel_level_arr = explode( ",", $cancel_level );
				if ( in_array( "all", $cancel_level_arr ) ) {
					$cancel_level_arr = array_merge( $cancel_level_arr, array_keys( $wpm_levels ) );
					$cancel_level_arr = array_unique( $cancel_level_arr );
				}

				foreach ( $cancel_level_arr as $id=>$cancel_level ) {
					if ( isset( $wpm_levels[$cancel_level] ) ) { //check if valid level
						if ( in_array( $cancel_level, $user_mlevels ) ) {
							$ret = $this->wlm->LevelCancelled( $cancel_level, $wpm_user, true );
						}
					}
				}

				if ( $this->debug ) {
					$cnt = count( $cancel_level_arr );
					_e( "{$cnt} Levels Cancelled.<br />", 'wishlist-member');
				}
			}
			//remove
			if ( $wpm_user && $remove_level ) {
				if ( $this->debug ) {
					_e( "Removing Levels.<br />", 'wishlist-member');
				}
				$user_mlevels = $current_user_mlevels;
				$remove_level_arr = explode( ",", $remove_level );
				if ( in_array( "all", $remove_level_arr ) ) {
					$remove_level_arr = array_merge( $remove_level_arr, array_keys( $wpm_levels ) );
					$remove_level_arr = array_unique( $remove_level_arr );
				}

				foreach ( $remove_level_arr as $id=>$remove_level ) {
					$arr_index = array_search( $remove_level, $user_mlevels );
					if ( $arr_index !== false ) {
						unset( $user_mlevels[$arr_index] );
					} elseif ( strrpos( $remove_level, "payperpost" ) !== false ) {
						list( $marker, $pid ) = explode( "-", $remove_level );
						$post_type = get_post_type( $pid );
						$this->wlm->RemovePostUsers( $post_type, $pid, $wpm_user );
					}
				}
				$this->wlm->SetMembershipLevels( $wpm_user, $user_mlevels );

				if ( $debug ) {
					echo count( $remove_level_arr ) ." Levels Removed.<br />";
				}
			}
			if ( $this->debug ) {
				_e( "Done.<br />", 'wishlist-member');
			}
			usleep(1000000);
			exit;
		}

		private function process_cron() {
			$wlm_infusionsoft_init = new WLM_INTEGRATION_INFUSIONSOFT_INIT();
			$ret     = $wlm_infusionsoft_init->sync_ifs( $this->debug, $this->force );
			$end     = isset( $ret['end'] ) ? $ret['end'] : "-unknown-";
			$message = isset( $ret['message'] ) ? $ret['message'] : "empty";
			$count   = isset( $ret['count'] ) ? $ret['count'] : 0;
			echo "<br />{$end} {$message} ({$count} records)";
			die();
		}

		private function process_registration() {
			$wlm_infusionsoft_init = new WLM_INTEGRATION_INFUSIONSOFT_INIT();
			//get the productid to be used for free trial subscriptions, if present
			$SubscriptionPlanProductId = isset( $_GET['SubscriptionPlanProductId'] ) ? $_GET['SubscriptionPlanProductId'] : false;
			//get the subscription id, if subscription
			$SubscriptionId = isset( $_GET['SubscriptionId'] ) ? $_GET['SubscriptionId'] : "00";
			//determine if FREE TRIALS
			$isTrial = isset( $_GET['SubscriptionPlanWait'] ) ? true : false;

			$job     = false;
			$orderid = "";
			//now, lets check the orderid if passed
			if ( isset( $_GET['orderId'] ) && $_GET['orderId'] ) {
				$orderid = (int) trim( $_GET['orderId'] );
				//retrieve Job of the OrderID passed
				$job = $this->ifsdk->get_orderid_job($_GET['orderId'], $con, $key);
			}

			//if job(OrderID) does not exist, end
			if ( ! $job ) {
				if ( $this->debug ) {
					_e( "Invalid OrderID passed.('{$orderid}')", "wishlist-member" );
					die();
				} else { return; }
			}

			//get the job's contact details
			$contactid = $job['ContactId'];
			$contact = $this->ifsdk->get_contact_details( $contactid );
			if ( ! $contact ) {
				if ( $this->debug ) {
					_e( "Invalid Contact.('{$contactid}')", "wishlist-member" );
					die();
				} else { return; }
			}

			//retrieve invoice using our job Id
			$invoice = $this->ifsdk->get_jobid_invoice( $job['Id'] );
			if ( ! $invoice ){
				if ( $this->debug ) {
					_e( "No Invoice found for this order.({$job['Id']})", "wishlist-member" );
					die();
				} else { return; }
			}

			//if its a subscription plan with free trial
			//populate the ProductSold field of invoice
			if ( $SubscriptionPlanProductId && $isTrial ) {
				$invoice['ProductSold'] = (int) $SubscriptionPlanProductId; //set the product id to SubscriptionPlanProductId, they have the same value
			}

			//set the $invoice Subscription Id
			$invoice['SubscriptionId'] = $SubscriptionId;

			//process the invoice and get its status
			$invoice = $wlm_infusionsoft_init->get_invoice_status( $invoice );

			// fetch Sku for the product of the invoice
			// product id is used to search for the sku
			// we loop through each product sold and break the loop if we find a sku that matches a WishList Member level ID
			$wpm_levels = $this->wlm->GetOption('wpm_levels');
			foreach ( explode( ',', $invoice['ProductSold'] ) AS $psold ) {

				$product = $this->ifsdk->get_product_sku( $psold );

				$sku = $product && isset( $product['Sku'] ) ? $product['Sku'] : "";
				$sku = $this->wlm->IsPPPLevel( $sku ) || isset( $wpm_levels[ $sku ] ) ? $sku : false;
				if ( $sku ) {
					if ( ! $invoice['Sku'] ) {
						$invoice['Sku'] = $sku;
					} else {
						$_POST['additional_levels'][] = $sku;
					}
				}
			}

			//if no product sku then lets end here
			if ( ! isset($invoice['Sku'] ) || $invoice['Sku'] == "" || empty( $invoice['Sku'] ) ) {
				if ( $this->debug ) {
					_e( "Invalid Product SKU.({$job['Id']})", "wishlist-member" );
					die();
				} else { return; }
			}

			// if we're active, then good.
			if ( $invoice['Status'] != 'active' ){
				if ( $this->debug ) {
					echo "Inactive Invoice.({$invoice['Id']})<br />";
					die();
				} else { return; }
			}

			// prepare data
			$_POST['lastname']  = $contact['LastName'];
			$_POST['firstname'] = $contact['FirstName'];
			$_POST['action']    = 'wpm_register';
			$_POST['wpm_id']    = $invoice['Sku'];
			$_POST['username']  = $contact['Email'];
			$_POST['email']     = $contact['Email'];
			$_POST['password1'] = $_POST['password2'] = $this->wlm->PassGen();
			$_POST['sctxnid']   = "{$this->invmarker}-" . $invoice['Id'] . "-{$SubscriptionId}";

			//prepare the address fields using info from shopping cart
			$address['company']  = $contact['Company'];
			$address['address1'] = $contact['StreetAddress1'];
			$address['address2'] = $contact['StreetAddress2'];
			$address['city']     = $contact['City'];
			$address['state']    = $contact['State'];
			$address['zip']      = $contact['PostalCode'];
			$address['country']  = $contact['Country'];

			$_POST['wpm_useraddress'] = $address;

			if ( $this->debug ) {
				echo "Integration is working fine.<br />";
				echo "<pre>";
					var_dump($_POST);
				echo "</pre>";
				die();
			}
			// do registration
			$this->wlm->ShoppingCartRegistration();
		}
	}
}
