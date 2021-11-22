<?php // integration handler
if ( ! class_exists( 'WLM_INTEGRATION_PLUGNPAID' ) ) {
	/**
	 * Plug&Paid Integration Handler
	 */
	class WLM_INTEGRATION_PLUGNPAID {
		function __construct() {
			add_action( 'wishlistmember_paymentprovider_handler', array( $this, 'process_webhook' ) );
		}

		/**
		 * Action: wishlistmember_paymentprovider_handler
		 *
		 * @uses WLM_INTEGRATION_PLUGNPAID::get_products
		 * @uses WLM_INTEGRATION_PLUGNPAID::get_user
		 * @uses WLM_INTEGRATION_PLUGNPAID::add_user_to_levels
		 * @uses
		 *
		 * @param string $scuri The SCURI parsed by WishList Member
		 */
		public function process_webhook( $scuri ) {
			global $WishListMemberInstance;

			// check that the $scuri is ours
			if ( $WishListMemberInstance->GetOption( 'plugnpaidthankyou' ) != $scuri ) {
				return; // not ours, pass control back to WishList Member
			}

			// we only handle webhooks
			if ( 'webhook' != wlm_arrval( $_GET, 'plugnpaid_action' ) ) {
				exit; // terminate, nothing else needs to happen
			}

			$pdata = json_decode( $x = file_get_contents( 'php://input' ) );
			if ( ! $pdata ) {
				exit; // terminate, nothing else needs to happen
			}

			// get configured products
			$plugnpaid_products = $WishListMemberInstance->GetOption( 'plugnpaid_products' );

			// process the webhook
			switch ( wlm_arrval( $pdata, 'type' ) ) {
				// "create/update" types
				case 'new_simple_sale':
				case 'recurring_subscription_sale':
				case 'mark_paid':
					// only create/update the user if order status is paid
					if ( in_array( $pdata->data->order->status, array( 'paid', 'ordered' ) ) ) {
						// retrieve product IDs
						$products = $this->get_products( $pdata );
						// match products with levels
						$levels = array_unique( array_keys( array_intersect( $plugnpaid_products, $products ) ) );
						// retrieve the user and add levels to user if match is found
						$user = $this->get_user( $pdata );
						if ( $user ) {
							$this->add_user_to_levels( $user, $levels, $pdata->data->order->id );
						}
					}
					break;

				case 'recurring_subscription_cancelled':
				case 'order_declined':
				case 'refund':
					if ( $pdata->data->order->id ) {
						$_POST['sctxnid'] = $pdata->data->order->id;
						$WishListMemberInstance->ShoppingCartDeactivate();
					}
					break;
			}

			exit; // terminate, nothing else needs to happen
		}

		/**
		 * Retrieve products from $pdata
		 *
		 * @param object $pdata
		 * @return array array of product IDs
		 */
		private function get_products( $pdata ) {
			// return empty array if we can't find $pdata->data->order->products
			if ( empty( $pdata->data ) || empty( $pdata->data->order ) || empty( $pdata->data->order->products ) ) {
				return array();
			}

			$products = array();
			// add product IDs to $products
			foreach ( $pdata->data->order->products as $product ) {
				if ( empty( $product->id ) ) {
					continue;
				}
				$products[] = $product->id;
			}

			// return array of product IDs
			return $products;
		}

		/**
		 * Retrieve user from $pdata
		 * If the user's email already exists in the database then return the user's ID
		 * If the user's email does not exist then return array of user info
		 *
		 * @param object $pdata
		 * @return int|array
		 */
		private function get_user( $pdata ) {
			// return empty array if we can't find $pdata->data->order->customer
			if ( empty( $pdata->data ) || empty( $pdata->data->order ) || empty( $pdata->data->order->customer ) ) {
				return false;
			}

			$customer = $pdata->data->order->customer;

			// check if the email address is already in our database
			$user = get_user_by_email( $customer->email );
			if ( ! $user ) {
				// new user, create array compatible with wlm_insert_user
				list($fname, $lname) = explode( ' ', $customer->name, 2 );
				$user                = array(
					'first_name' => $fname,
					'last_name'  => $lname,
					'user_email' => $customer->email,
					'user_login' => $customer->email,
					'user_pass'  => wlm_generate_password(),
				);
			} else {
				// existing user, we just need the ID
				$user = $user->ID;
			}

			return $user;
		}

		/**
		 * Adds user to levels
		 *
		 * @uses wlmapi_add_member
		 * @uses wlmapi_update_member
		 * @uses WishListMemberPluginMethods::ShoppingCartReactivate
		 *
		 * @param int|array $user If int then $levels are added to the user. If not, then create the user and add levels to newly created user
		 * @param array     $levels Array of membership levels
		 * @param string    $txnid Transaction ID
		 */
		private function add_user_to_levels( $user, $levels, $txnid ) {
			global $WishListMemberInstance;

			$memlevels = array();
			// populate $memlevels if user already exists
			if ( is_int( $user ) ) {
				$memlevels = $WishListMemberInstance->GetMembershipLevels( $user, false, true );
			}

			// remove duplicate levels and set transaction ID for each level
			$levels = array_unique( $levels );
			foreach ( $levels as &$level ) {
				$level = in_array( $level, $memlevels ) ? false : array( $level, $txnid );
			}
			unset( $level );

			// cleanup. remove "false" entries
			$levels = array( 'Levels' => array_diff( $levels, array( false ) ) );

			// check if $user is array or not. Array means we create the user, otherwise update
			if ( is_array( $user ) ) {
				// create the user and add the levels
				wlmapi_add_member( $user + array( 'SendMailPerLevel' => 1 ) + $levels );
			} else {
				// update the user with new levels
				wlmapi_update_member( $user, array( 'SendMailPerLevel' => 1 ) + $levels );
			}

			// call legacy ShoppingCartReactivate for backwards compatibility
			$old                             = $wlm_no_cartintegrationterminate;
			$wlm_no_cartintegrationterminate = true;
			$_POST['sctxnid']                = $txnid;
			$WishListMemberInstance->ShoppingCartReactivate();
			$wlm_no_cartintegrationterminate = $old;
		}
	}

	// initialize everything
	new WLM_INTEGRATION_PLUGNPAID();
}
