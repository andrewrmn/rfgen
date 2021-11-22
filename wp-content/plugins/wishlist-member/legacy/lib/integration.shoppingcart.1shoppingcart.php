<?php

/*
 * 1ShoppingCart Shopping Cart Integration Functions
 * Original Author : Mike Lopez
 * Version: $Id: integration.shoppingcart.1shoppingcart.php 3716 2017-12-09 14:37:01Z mike $
 */

//information below is now loaded in integration.shoppingcarts.php
//$__classname__ = 'WLM_INTEGRATION_1SHOPPINGCART';
//$__optionname__ = 'scthankyou';
//$__methodname__ = 'OneShoppingCart';

if (!class_exists('WLM_INTEGRATION_1SHOPPINGCART')) {

	class WLM_INTEGRATION_1SHOPPINGCART {

		function OneShoppingCart($that) {
			global $wlm_1sc_status_map;
			if (in_array(strtolower(trim(wlm_arrval($_POST,'status'))), array('accepted', 'approved', 'authorized', 'pending'))) { //accept even PENDING, let checkstatus handle it later
				if (!trim(wlm_arrval($_POST,'name')))
					$_POST['name'] = 'Firstname Lastname';
				$name = explode(' ', $_POST['name']);
				$_POST['lastname'] = array_pop($name);
				$_POST['firstname'] = implode(' ', $name);
				$_POST['action'] = 'wpm_register';
				$_POST['wpm_id'] = $_POST['sku1'];
				$_POST['username'] = $_POST['email1'];
				$orig_email = $_POST['email'] = $_POST['email1'];
				$_POST['password1'] = $_POST['password2'] = $that->PassGen();

				$address = array();
				$address['company'] = $_POST['shipCompany'];
				$address['address1'] = $_POST['shipAddress1'];
				$address['address2'] = $_POST['shipAddress2'];
				$address['city'] = $_POST['shipCity'];
				$address['state'] = $_POST['shipState'];
				$address['zip'] = $_POST['shipZip'];
				$address['country'] = $_POST['shipCountry'];

				$_POST['sctxnid'] = '1SC-'.$_POST['orderID'];

				$_POST['wpm_useraddress'] = $address;


				//cache the order
				$onescmerchantid = trim($that->GetOption('onescmerchantid'));
				$onescapikey = trim($that->GetOption('onescapikey'));
				if ($onescmerchantid && $onescapikey) {
					require_once($that->pluginDir . '/extlib/OneShopAPI.php');
					require_once($that->pluginDir . '/extlib/WLMOneShopAPI.php');
					$api = new WLMOneShopAPI($onescmerchantid, $onescapikey, 'https://www.mcssl.com');
					$order = $api->get_order_by_id($_POST['orderID'], true, true);
				}

				// support 1SC upsells
				if (trim($that->GetOption('onesc_include_upsells'))) {
					if (count($order['upsells'])) {

						// Added this so that we can also put the ORDER ID of the upsell order as TXN ID's on the upsell levels
						foreach($order['upsells'] as $ord) {
							$order_upsells[] = $ord['sku'] . "\t" . '1SC-'.$ord['id'];
						}

						$_POST['additional_levels'] = $order_upsells;
					}
				}

				$that->ShoppingCartRegistration();
			} else {
				// instant notification
				$onescmerchantid = trim($that->GetOption('onescmerchantid'));
				$onescapikey = trim($that->GetOption('onescapikey'));

				if ($onescmerchantid && $onescapikey) {
					$raw_post_data = file_get_contents('php://input');
					require_once($that->pluginDir . '/extlib/OneShopAPI.php');
					$API = new OneShopAPI($that->GetOption('onescmerchantid'), $that->GetOption('onescapikey'), 'https://www.mcssl.com');

					$requestBodyXML = new DOMDocument();

					if($raw_post_data!=''){
						if ($requestBodyXML->loadXML($raw_post_data) == true )  {
							$notificationType = $requestBodyXML->documentElement->nodeName;

							$recurring = false;
							switch (strtolower($notificationType)) {
								case "neworder":
									$tokenNode = $requestBodyXML->getElementsByTagName('Token')->item(0)->nodeValue;  
									$apiResult = $API->GetOrderById($tokenNode);
									$apiResultXML = new DOMDocument();
									if ($apiResultXML->loadXML($apiResult) == true) {

										$apiSuccess = $apiResultXML->getElementsByTagName('Response')->item(0)->getAttribute('success');

										if ($apiSuccess == 'true') {
											$orderXML = &$apiResultXML;
											$order_id = $orderXML->getElementsByTagName('OrderId')->item(0)->nodeValue;

											$recur_order_id = $orderXML->getElementsByTagName('RecurringOrderId')->item(0)->nodeValue;
											// if recurring id has value then skip
											if(is_numeric(($recur_order_id)))
												exit;

											$onescmerchantid = trim($that->GetOption('onescmerchantid'));
											$onescapikey = trim($that->GetOption('onescapikey'));
											require_once($that->pluginDir . '/extlib/OneShopAPI.php');
											require_once($that->pluginDir . '/extlib/WLMOneShopAPI.php');
											$api = new WLMOneShopAPI($onescmerchantid, $onescapikey, 'https://www.mcssl.com');

											// Get Order details to get the client ID
											$order = $api->get_order_by_id($order_id, true, true);

											// Check if the SKU matches any of the levels, if it is then add the orderID to the queue
											$is_sku_valid = false;
											$levels = $that->GetOption('wpm_levels');

											foreach($levels as $key => $level) {
												if($key == $order['sku'])
													$is_sku_valid = true;
											}
											
											if(!$is_sku_valid)
												exit;

											$WishlistAPIQueueInstance = new WishlistAPIQueue;
											$qname = "1sc_neworder_" .time();
											$data = $order_id;
											$WishlistAPIQueueInstance->add_queue($qname,$data,"For Queueing");
										}
									}
									// No need to go on with the rest of the script so just terminate it...
									exit;

									break;
								case 'orderstatuschange':
									$recurring = false;
									$apiResult = $API->GetOrderById($requestBodyXML->getElementsByTagName('Id')->item(0)->nodeValue);
									break;
								case 'recurringorderstatuschange':
									$recurring = true;
									$apiResult = $API->GetRecurringOrderById($requestBodyXML->getElementsByTagName('Id')->item(0)->nodeValue);
									break;

								default:
									# May have other types of notifications in the future
									break;
							}

							$apiResultXML = new DOMDocument();
							if ($apiResultXML->loadXML($apiResult) == true) {
								# Check if the API returned an error
								$apiSuccess = $apiResultXML->getElementsByTagName('Response')->item(0)->getAttribute('success');
								if ($apiSuccess == 'true') {

									$orderXML = &$apiResultXML;

									if($recurring) {
										$group = 'recurring';
										$status = strtolower($orderXML->getElementsByTagName('Status')->item(0)->nodeValue);
									} else {
										$group = 'onetime';
										$status = strtolower($orderXML->getElementsByTagName('OrderStatusType')->item(0)->nodeValue);
									}
									$_POST['sctxnid'] = '1SC-'.$orderXML->getElementsByTagName('OrderId')->item(0)->nodeValue;

									// Search first if there's a user for the transaction ID..
									// If there's none then add -R as our cron adds -R to recurring transaction ID's
                                    $user = $that->GetUserIDFromTxnID($_POST['sctxnid']);
                                    if (!$user) {
                                        $_POST['sctxnid'] = $_POST['sctxnid'].'-R';
                                    }

									switch($wlm_1sc_status_map[$group][$status]) {
										case 'activate':
											$that->ShoppingCartReactivate();

											if($recurring) {
												// Add hook for Shoppingcart reactivate so that other plugins can hook into this
												$_POST['sc_type'] = '1ShoppingCart';
												do_action('wlm_shoppingcart_rebill', $_POST);
											}
											break;
										case 'deactivate':
											$that->ShoppingCartDeactivate();
											break;
										default:
											// do nothing
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
