<?php
/**
 * Handles the polling of the 1ShoppingCart API
 * to check the status of 1ShoppingCart orders
 * and set level status accordingly
 */

$GLOBALS['wlm_1sc_status_map'] = array(
	'onetime' => array (
		'approved'                   => 'activate', // 
		'accepted'                   => 'activate', // A properly processed order
		'pending'                    => 'activate', // A status for redirect based processors like PayPal where we have to wait for an IPN response
		'authorize'                  => 'activate', // The customer has sufficient funds for the order, but it has not been “captured”. Means the charge hasn’t been completed.
		'authorized'                 => 'activate', // The customer has sufficient funds for the order, but it has not been “captured”. Means the charge hasn’t been completed.
		'declined'                   => 'deactivate', // A proper declined order
		'voided'                     => 'deactivate', // An order that was refunded before capture, meaning it is void and never actually processed
		'cancelled'                  => 'deactivate', // This is an Authorization that has been cancelled rather than captured.
		'refunded'                   => 'deactivate', // An order that was refunded after capture
		'refundedfull'               => 'deactivate', // An order that was refunded after capture
		'refunded - partial'         => 'deactivate', // A partially refunded order
		'refundedpartial'            => 'deactivate', // A partially refunded order
		'refunded partial (offline)' => 'deactivate', // An order that was partially refunded, however, it was not passed to a gateway or payment processor
		'refundedpartialoffline'     => 'deactivate', // An order that was partially refunded, however, it was not passed to a gateway or payment processor
		'refunded (offline)'         => 'deactivate', // An order that is marked as refunded, but it was not passed to a gateway or payment processor.
		'refundedoffline'            => 'deactivate', // An order that is marked as refunded, but it was not passed to a gateway or payment processor.
		'refundedfulloffline'        => 'deactivate', // An order that is marked as refunded, but it was not passed to a gateway or payment processor.
		'archived'                   => 'ignore', // The order has been hidden in our UI unless specifically searched for using “archived”
		'unknown'                    => 'ignore', // Cases where we could not determine success from the payment processor
		'error'                      => 'ignore', // Typically a communication error or bad gateway configuration
	),
	'recurring' => array (
		'active'    => 'activate', // An event that is up to date on charges and has remaining billing cycles
		'overdue'   => 'activate', // An event that is currently re-trying and has remaining billing cycles (payments are in arrears)
		'failed'    => 'deactivate', // This event has exceeded max attempts, and is now stopped
		'cancelled' => 'deactivate', // And event that has been manually terminated; may or may not have remaining billing cycles.
		'completed' => 'ignore', // An event that has charged all applicable charges and has completed the set cycle
		'paused'    => 'ignore', // An event that has been manually put on hold but is not completed or failed
	),
);

class WLM_INTEGRATION_1SHOPPINGCART_INIT {
	var $api;

	/**
	 * Constructor
	 */
	function __construct() {
		global $WishListMemberInstance;

		if ( isset( $WishListMemberInstance ) ) {
			// get 1sc api information
			$onescmerchantid = trim( $WishListMemberInstance->GetOption( 'onescmerchantid' ) );
			$onescapikey = trim( $WishListMemberInstance->GetOption( 'onescapikey' ) );
		}

		// bail if there is no or incomplete api information
		if ( !$onescmerchantid || !$onescapikey ) {
			return;
		}

		// load required libs
		require_once $WishListMemberInstance->pluginDir . '/extlib/OneShopAPI.php';
		require_once $WishListMemberInstance->pluginDir . '/extlib/WLMOneShopAPI.php';

		// initialize api
		$this->api = new WLMOneShopAPI( $onescmerchantid, $onescapikey, 'https://www.mcssl.com' );

		$this->merchantid = $onescmerchantid;
		$this->apikey = $onescapikey;

		// get order details
		if ( !wp_next_scheduled( 'wishlistmember_1shoppingcart_check_order_status' ) ) {
			// wp_schedule_event( time(), 'everyfifteenminutes', 'wishlistmember_1shoppingcart_check_order_status' );
			wp_schedule_event( time(), 'twicedaily', 'wishlistmember_1shoppingcart_check_order_status' );
		}

		// add action for our crons
		add_action( 'wishlistmember_1shoppingcart_check_order_status', array( $this, 'CheckOrderStatusType' ) );
	}

	/**
	 * Simple 1SC Get API
	 *
	 * @param string  $request Request being made. Ex: ORDERS/LIST
	 * @param array   $params  Optional parameters to pass
	 * @param integer $limit   RecordSets to retrieve. Default is 1. Set to "0" to get all RecordSets
	 * @return array array of XML Records returned
	 */
	function SimpleAPI( $request, $params = array(), $limit = 1 ) {
		$request = trim( preg_replace( array( '#^/#', '#/$#' ), '', $request ) );
		$pattern = 'https://mcssl.com/API/%d/%s?key=%s';

		if ( empty( $this->merchantid ) || empty( $this->apikey ) || empty( $request ) ) {
			return '';
		}

		if ( !empty( $params ) ) {
			$params = '&' . http_build_query( $params );
		} else {
			$params = '';
		}

		$results = array();
		$read = 1;

		$base_url = sprintf( $pattern, $this->merchantid, $request, $this->apikey );

		while ( $read ) {
			$read = 0;
			$url = $base_url . $params;
			$result = wp_remote_retrieve_body( wp_remote_get( $url ) );
			if ( $result ) {
				$results[] = $result;
				if ( preg_match( '#<nextrecordset>(.+?)</nextrecordset>#im', $result, $matches ) ) {
					if ( preg_match_all( '/<([^\s]+?)>(.+?)</im', $matches[1], $matches ) ) {
						$params = '&' . http_build_query( array_combine( $matches[1], $matches[2] ) );
						$read = 1;
					}
				}
			}

			$limit--;

			if ( empty( $limit ) ) {
				$read = 0;
			}

		}

		return $results;
	}

	/**
	 * Check if returned XML's response is successful
	 * @param string $result XML
	 * @return boolean
	 */
	function SimpleAPIResultSuccessful($result) {
		return (bool) preg_match('#<response[^>]+?success="true"[^>]*?>#im', $result);
	}

	/**
	 * Update status of 1ShoppingCart orders
	 * At the moment, here's the status of how things are:
	 * - One time orders need to be polled for checking
	 * - Recurring orders make use of 1ShoppingCart's instant notification so no polling is needed for that
	 *
	 * Furthermore, we added a way to 
	 */
	function CheckOrderStatusType( ) {
		global $wpdb, $WishListMemberInstance, $wlm_1sc_status_map, $wlm_no_cartintegrationterminate;
		if ( get_transient( 'running-1sc-' . __FUNCTION__ ) ) {
			// return;
		}
		wlm_set_time_limit( DAY_IN_SECONDS / 2 );
		set_transient( 'running-1sc-' . __FUNCTION__, 1, DAY_IN_SECONDS / 2 );

		$reset = get_option('wlm-reset-1sc');
		if($reset) {
			delete_option( 'wlm-reset-1sc' );
			$reset = strtoupper($reset);
			if(in_array($reset, array('O','R'))) {
				$wpdb->query("UPDATE `{$WishListMemberInstance->Tables->userlevel_options}` SET `option_value`=SUBSTRING_INDEX(`option_value`,'-',2) WHERE `option_name`='transaction_id' AND `option_value` REGEXP '^1SC-[0-9]+-{$reset}$'");
			}
		}

		$transaction_ids = get_transient( 'wlm-1sc-xqueue' );
		if(empty($transaction_ids)) {
			// update old 1SC transaction IDs (xxx) to 1SC-xxx
			$wpdb->query("UPDATE `{$WishListMemberInstance->Tables->userlevel_options}` SET `option_value`=CONCAT('1SC-',`option_value`) WHERE `option_name` = 'transaction_id' AND `option_value` REGEXP '^[0-9]+$'");
			// get orders from transaction IDs
			$transaction_ids = $wpdb->get_col( "SELECT DISTINCT `option_value` FROM `{$WishListMemberInstance->Tables->userlevel_options}` WHERE `option_name` = 'transaction_id' AND `option_value` REGEXP '^1SC-[0-9]+.*$' ORDER BY `option_value` ASC" );
			set_transient( 'wlm-1sc-xqueue', $transaction_ids, DAY_IN_SECONDS * 3 );
		}

		$counter = 5;

		while ( $transaction_id = array_shift($transaction_ids) ) {
			$wlm_no_cartintegrationterminate = true;
			$counter--;
			$origtxnid = $transaction_id;
			$transaction_id = explode('-', $transaction_id);
			$transaction_type = strtoupper($transaction_id[2]);
			$transaction_id = $transaction_id[1];
			$transaction_type_needs_update = false;
			if(!in_array($transaction_type, array('O', 'R'))) {
				$transaction_type = '';
				$transaction_type_needs_update = true;
			}

			if('R' == $transaction_type) continue; // do not poll info for orders flagged as recurring

			$result = $this->SimpleAPI('/ORDERS/' . $transaction_id);
			if(!$this->SimpleAPIResultSuccessful($result[0])) continue;

			if(!$transaction_type) { // transaction type unknown, let's check
				$recurring = (bool) preg_match('#<isrecurring>true</isrecurring>#im', $result[0]); // looks like it's recurring
				if($recurring) {
					$orig_result = $result; // save original result (first order)

					// grab client ID
					if(!preg_match('#>(\d+)?</clientid>#im', $result[0], $match)) continue; // get client id

					// pull recurring orders filtered by Client ID
					// * I wish there's a way to just filter recurring orders by Order ID *
					$result = $this->SimpleAPI('/RecurringOrders/List', array('LimitClient' => $match[1]), 100);
					if(!$this->SimpleAPIResultSuccessful($result[0])) continue;

					preg_match_all('#>(\d+)?</recurringorder>#im', $result[0], $matches);
					$recurring_events = $matches[1];
					rsort($recurring_events);
					$found = false;

					// note, this is expensive as we have to loop through all recurring orders for the client and not the order id
					while($recurring_event = array_shift($recurring_events)) { // look for matching recurring event for order
						$result = $this->SimpleAPI('/RecurringOrders/' . $recurring_event);
						if(!$this->SimpleAPIResultSuccessful($result[0])) continue;

						$string = sprintf('#>%d</orderid>#im', $transaction_id);
						if(preg_match($string, $result[0])) {
							$found = true;
							break; // found matching recurring event for order
						}
					}
					if(!$found) {
						// no matching recurring event found so we return the original order's result and set recurring to false
						$result = $orig_result;
						$recurring = false;
					}
					$transaction_type = $recurring ? 'R' : ''; // we've found a recurring event so mark this as recurring, otherwise do not mark it as anything so we can check again later
				} else {
					$transaction_type = 'O';
				}

			}

			$recurring = ('R' == $transaction_type);

			if('' != $transaction_type && $transaction_type_needs_update) {
				$transaction_id = sprintf('1SC-%d-%s', $transaction_id, $transaction_type);
				$query = $wpdb->prepare("UPDATE `{$WishListMemberInstance->Tables->userlevel_options}` SET `option_value`=%s WHERE `option_name` = 'transaction_id' AND `option_value`=%s", $transaction_id, $origtxnid);
				$wpdb->query($query);
				$origtxnid = $transaction_id;
			}

			$field = $recurring ? 'status' : 'orderstatustype';
			$map_index = $recurring ? 'recurring' : 'onetime';

			if(preg_match( '#<'.$field.'>(.+)?</'.$field.'>#im', $result[0], $match )) {
				$orderstatustype = strtolower($match[1]);
				$_POST['sctxnid'] = $origtxnid;
				switch($wlm_1sc_status_map[$map_index][$orderstatustype]) {
					case 'activate':
						$WishListMemberInstance->ShoppingCartReactivate();
						break;
					case 'deactivate':
						$WishListMemberInstance->ShoppingCartDeactivate();
						break;
					default:
						// do nothing
				}
			}

			if(!$counter) {
				$counter = 5;
				set_transient( 'wlm-1sc-xqueue', $transaction_ids, DAY_IN_SECONDS * 3 );
			}
		}

		if($transaction_ids) {
			set_transient( 'wlm-1sc-xqueue', $transaction_ids, DAY_IN_SECONDS * 3 );
		} else {
			delete_transient( 'wlm-1sc-xqueue' );
		}

		delete_transient( 'running-1sc-' . __FUNCTION__ );
	}

	// Function to process new order ids queued via 1SC's API Notification
	// This will create an incomplete registration for users who immediately closed the browser
	// after payment and wasn't redirected to the WLM reg page.
	function ProcessOneSCQueueForIncompleteReg( $recnum = 10) {

		global $WishListMemberInstance;
		$WishlistAPIQueueInstance = new WishlistAPIQueue;

		$queues = $WishlistAPIQueueInstance->get_queue("1sc_neworder_",$recnum,$tries,"tries,name");
		foreach($queues as $queue){

			$order_id = $queue->value;
			
			//Check the transaction ID in 4 formats (XXXXXXX, 1SC-XXXXXXX, 1SC-XXXXXXX-R, 1SC-XXXXXXX-O)

			// XXXXXXX format
			$_POST['sctxnid'] = $order_id;

			$user = $WishListMemberInstance->GetUserIDFromTxnID($_POST['sctxnid']);

			// 1SC-XXXXXXX format
			if (!$user) 
		       $_POST['sctxnid'] = '1SC-'.$order_id;

			$user = $WishListMemberInstance->GetUserIDFromTxnID($_POST['sctxnid']);

			// 1SC-XXXXXXX-R format
			if (!$user) 
		        $_POST['sctxnid'] = '1SC-'.$order_id.'-R';

		    $user = $WishListMemberInstance->GetUserIDFromTxnID($_POST['sctxnid']);

		    // 1SC-XXXXXXX-O format
			if (!$user) 
		        $_POST['sctxnid'] = '1SC-'.$order_id.'-O';

		    $user = $WishListMemberInstance->GetUserIDFromTxnID($_POST['sctxnid']);

		   	// DELETE queue entry before processing.
		   	$WishlistAPIQueueInstance->delete_queue($queue->ID);

		    // If no User yet then we create an incomplete registration for the user
		    if (!$user) {

	    		// Also check if the ORDER ID is in the User Meta as additional_levels, if it's there then don't continue
	    		// as this means the order is already in the addtional_levels user_meta of the user.
				global $wpdb, $WishListMemberInstance;
	  		 	$table = $WishListMemberInstance->Tables->user_options;
	  		 	$opt_value = $order_id;
	  		 	$opt_name = 'additional_levels';
	   			$row =  $wpdb->get_row($q = $wpdb->prepare("SELECT `user_id` FROM `{$table}` WHERE  `option_value` LIKE '%$opt_value%' AND `option_name`='$opt_name'", $opt_value, $opt_name));
	   			if (is_object($row))
	   				continue;

		    	$onescmerchantid = trim($WishListMemberInstance->GetOption('onescmerchantid'));
				$onescapikey = trim($WishListMemberInstance->GetOption('onescapikey'));
				require_once($WishListMemberInstance->pluginDir . '/extlib/OneShopAPI.php');
				require_once($WishListMemberInstance->pluginDir . '/extlib/WLMOneShopAPI.php');

				$api = new WLMOneShopAPI($onescmerchantid, $onescapikey, 'https://www.mcssl.com');
				$api2 = new OneShopAPI($onescmerchantid, $onescapikey, 'https://www.mcssl.com');

				// Get Order details to get the client ID
				$order = $api->get_order_by_id($order_id, true, true);

				// Check if the SKU matches any of the levels, if it is then add the orderID to the queue
				$is_sku_valid = false;
				$levels = $WishListMemberInstance->GetOption('wpm_levels');

				foreach($levels as $key => $level) {
					if($key == $order['sku'])
						$is_sku_valid = true;
				}
				
				if(!$is_sku_valid)
					continue;

				// support 1SC upsells
				if (trim($WishListMemberInstance->GetOption('onesc_include_upsells'))) {
					if (count($order['upsells'])) {
						// Added this so that we can also put the ORDER ID of the upsell order as TXN ID's on the upsell levels
						foreach($order['upsells'] as $ord) {
							$order_upsells[] = $ord['sku'] . "\t" . $ord['id'];
						}
						$_POST['additional_levels'] = $order_upsells;
					}
				}

				// Get the fname, lname and email via client_id
				$client_id = $order['client_id'];
				$client = $api2->GetClientById($client_id);


				if( ini_get('allow_url_fopen') ) {
					$xml = simplexml_load_string($client);

					$client_email = (string) $xml->ClientInfo->Email;
					$client_firstname = (string) $xml->ClientInfo->FirstName;
					$client_lastname = (string) $xml->ClientInfo->LastName;

					$_POST['lastname'] = $client_lastname;
					$_POST['firstname'] = $client_firstname;
					$_POST['action'] = 'wpm_register';
					$_POST['wpm_id'] = $order['sku']; // Get SKU from orderID
					$_POST['username'] = $client_email;
					$orig_email = $_POST['email'] = $client_email;
					$_POST['password1'] = $_POST['password2'] = $WishListMemberInstance->PassGen();

					$_POST['sctxnid'] = '1SC-'.$order_id;

					// ==================================================================================================================
					// Check if the order is an upsell of an existing level of the user...
					$member = wlmapi_get_member_by('user_email', $client_email);
					$continue_creating_incomplete_reg = true;

					// If client email already exist then check for the member's current levels to see if any has a 1SC TXN ID format
					if($member) {

						// CHECK IF THE order_id IS AN UPSELL OF A PREVIOUSLY BOUGHT LEVEL
						// If it's an upsell then we add the level to the user instead of creating an extra incomplete registration

						// 1. Get user's current levels based on client email
						$ulevels = wlmapi_get_member_levels($member['members']['member'][0]['id']);
						foreach($ulevels as $ulevel){

							// 2. Check if any of the levels has a 1SC transaction ID format
							$pos = strpos($ulevel->TxnID, '1SC-');
							if ($pos !== false) {
								$lvl_txn_id = explode('-', $ulevel->TxnID);
								$lvl_txn_id = $lvl_txn_id[1];
								
								$api4 = new WLMOneShopAPI($onescmerchantid, $onescapikey, 'https://www.mcssl.com');

								$order4 = $api4->get_order_by_id($lvl_txn_id, true, true);
								foreach($order4['upsells'] as $upsell4) {

									// if $order_id mathes any of the upsells then just add the level to the matching member.
									if($upsell4['id'] == $order_id) {
									  	$args = array(
									        'Users' => array($member['members']['member'][0]['id']),
										    'Pending' => false,
										    'TxnID' => $_POST['sctxnid']
									    );
									    wlmapi_add_member_to_level($_POST['wpm_id'], $args);

									    $continue_creating_incomplete_reg = false;
									} 
								}
							}
						}
					} 

					if(!$continue_creating_incomplete_reg) {

						// Don't run code that creates incomplete reg
						continue;
					}
					// ==================================================================================================================

					$WishListMemberInstance->ShoppingCartRegistration(null, false);
				}
		    }
		}


	}
}

// load the thing
new WLM_INTEGRATION_1SHOPPINGCART_INIT();

if( isset( $_GET['wlm_1sc_reset'])  && is_admin() ) {
	add_option( 'wlm-reset-1sc', $_GET['wlm_1sc_reset'] );
	do_action( 'wishlistmember_1shoppingcart_check_order_status' );
}