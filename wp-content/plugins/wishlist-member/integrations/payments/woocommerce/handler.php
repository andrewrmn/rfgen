<?php // integration handler
if ( ! class_exists( 'WLM_INTEGRATION_WOOCOMMERCE' ) ) {
	class WLM_INTEGRATION_WOOCOMMERCE {
		function __construct() {
			add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 3 );
			add_action( 'woocommerce_subscription_status_changed', array( $this, 'subscription_status_changed' ), 10, 3 );
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_metabox_tabs' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'product_metabox_panel' ) );
			add_action( 'save_post_product', array( $this, 'save_woocommerce_product' ) );
			if ( class_exists( 'WooCommerce' ) ) {
				add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
				add_action( 'untrashed_post', array( $this, 'untrash_post' ), 1000 );
				// woocommerce products metabox tab and panel
			}
		}

		/**
		 * Action: save_post_product
		 *
		 * @param integer $postID
		 */
		function save_woocommerce_product( $postID ) {
			global $WishListMemberInstance;
			if ( !( isset( $_POST['wishlist_member_woo_levels'] ) ) ) return;
			$wlmwoo = $WishListMemberInstance->GetOption( 'woocommerce_products' );
			if ( ! is_array( $wlmwoo ) ) {
				$wlmwoo = array();
			}
			$wlmwoo[ $postID ] = wlm_arrval( $_POST, 'wishlist_member_woo_levels' );
			$WishListMemberInstance->SaveOption( 'woocommerce_products', $wlmwoo );
		}

		/**
		 * Filter: woocommerce_product_data_tabs
		 * Add WishList Member tab to the WooCommerce Product Meta box
		 *
		 * @param array $tabs Array of tabs
		 * @return array
		 */
		function product_metabox_tabs( $tabs ) {
			$tabs['wishlist_member_woo'] = array(
				'label'    => __( 'WishList Member', 'woocommerce' ),
				'target'   => 'wishlist_member_woo',
				'class'    => array(),
				'priority' => 71,
			);
			return $tabs;
		}

		/**
		 * Action: woocommerce_product_data_panels
		 * Add WishList Member panel to the WooCommerce Product Meta Box
		 */
		function product_metabox_panel() {
			require_once __DIR__ . '/resources/woocommerce_products_panel.php';
		}

		/**
		 * Removes levels from a member if an order is trashed
		 *
		 * @param int $post_id
		 */
		function trash_post( $post_id ) {
			if ( ! function_exists( 'wc_get_order' ) ) {
				return;
			}
			$order = wc_get_order( $post_id );
			if ( ! $order ) {
				return;
			}
			$this->__remove_levels( $this->__generate_transaction_id( $order ) );
		}

		/**
		 * Restores an order from trash and updates levels accordingly
		 *
		 * @param int $post_id
		 */
		function untrash_post( $post_id ) {
			if ( ! function_exists( 'wc_get_order' ) ) {
				return;
			}
			$order = wc_get_order( $post_id );
			if ( ! $order ) {
				return;
			}
			$function = function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $order ) ? 'subscription_status_changed' : 'order_status_changed';
			call_user_func( array( $this, $function ), $post_id, 'trash', $order->get_status() );
		}

		/**
		 * Map subscription status to either activate, remove or deactivate
		 * Called by woocommerce_subscription_status_changed action
		 *
		 * @uses WLM_INTEGRATION_WOOCOMMERCE::__status_changed
		 *
		 * @param int    $order_id
		 * @param string $old_status
		 * @param string $new_status
		 */
		function subscription_status_changed( $order_id, $old_status, $new_status ) {
			switch ( $new_status ) {
				case 'active':
					$status = 'activate';
					break;
				case 'cancelled':
					$status = 'deactivate';
					break;
				case 'pending':
				case 'on-hold':
					$status = 'pending';
					break;
				case 'switched':
				case 'pending-cancel':
				case 'expired':
				default:
					$status = '';
			}
			if ( $status ) {
				$this->__status_changed( $order_id, $status );
			}
		}

		/**
		 * Map order status change to either activate, remove or deactivate
		 * Called by woocommerce_order_status_changed action
		 *
		 * @uses WLM_INTEGRATION_WOOCOMMERCE::__status_changed
		 *
		 * @param int    $order_id
		 * @param string $old_status
		 * @param string $new_status
		 */
		function order_status_changed( $order_id, $old_status, $new_status ) {
			switch ( $new_status ) {
				case 'processing':
				case 'completed':
					$status = 'activate';
					break;
				case 'cancelled':
				case 'refunded':
					$status = 'deactivate';
					break;
				case 'pending':
				case 'on-hold':
				case 'failed':
					$status = 'remove';
					break;
				default:
					$status = '';
			}
			if ( $status ) {
				$this->__status_changed( $order_id, $status );
			}
		}

		/**
		 * Updates a member's levels or their status
		 * Creates a new member if one doesn't exist yet
		 * Used info is gathered from the $order_id
		 *
		 * @param int    $order_id
		 * @param string $status
		 */
		private function __status_changed( $order_id, $status ) {
			global $WishListMemberInstance, $wlm_no_cartintegrationterminate;
			if ( ! function_exists( 'wc_get_order' ) ) {
				return;
			}
			$woocommerce_products = $WishListMemberInstance->GetOption( 'woocommerce_products' );
			$order                = wc_get_order( $order_id );
			if ( ! $order ) {
				return;
			}
			$txnid = $this->__generate_transaction_id( $order );

			switch ( $status ) {
				case 'activate':
					// take care adding of new customer and levels
					$user = $order->get_customer_id();
					if ( ! $user ) {
						$user = get_user_by_email( $order->get_billing_email() );
						if ( ! $user ) {
							$user = array(
								'first_name'       => $order->get_billing_first_name(),
								'last_name'        => $order->get_billing_last_name(),
								'user_email'       => $order->get_billing_email(),
								'user_login'       => $order->get_billing_email(),
								'user_pass'        => wlm_generate_password(),

								// address
								'company'          => $order->get_billing_company(),
								'address1'         => $order->get_billing_address_1(),
								'address2'         => $order->get_billing_address_2(),
								'city'             => $order->get_billing_city(),
								'state'            => $order->get_billing_state(),
								'zip'              => $order->get_billing_postcode(),
								'country'          => WC()->countries->countries[$order->get_billing_country()],

								'SendMailPerLevel' => 1,
							);
						} else {
							$user = $user->ID;
						}
					}
					$levels = array();
					foreach ( $order->get_items() as $item ) {
						$pid = $item->get_product()->id;
						if ( isset( $woocommerce_products[ $pid ] ) && is_array( $woocommerce_products[ $pid ] ) ) {
							$levels = array_merge( $levels, $woocommerce_products[ $pid ] );
						}
					}
					if ( $levels ) {
						$memlevels = array();
						if ( is_int( $user ) ) {
							$memlevels = $WishListMemberInstance->GetMembershipLevels( $user, false, true );
							foreach ((array) $levels AS $level) {
							    if ( wishlistmember_instance()->LevelForApproval( $level, $user ) )
								wishlistmember_instance()->LevelForApproval($level, $user, false);
							}
						}
						$levels = array_unique( $levels );
						foreach ( $levels as &$level ) {
							if ( is_int( $user ) ) {
								$registration_date= wishlistmember_instance()->Get_UserLevelMeta( $user, $level , 'registration_date');
							}else{
								$registration_date=null;
							}
							$level = in_array( $level, $memlevels ) ? false : array( $level, $txnid, $registration_date );
						}
						unset( $level );
						$levels = array( 'Levels' => array_diff( $levels, array( false ) ) );
						$uid    = 0;
						if ( is_array( $user ) ) {
							$result = wlmapi_add_member( $user + $levels );
							if ( $result['success'] && $result['member'][0]['ID'] ) {
								$uid = $result['member'][0]['ID'];
								if( !is_admin() ) {
									$WishListMemberInstance->WPMAutoLogin( $result['member'][0]['ID'] );
								}
							}
						} else {
							wlmapi_update_member( $user, array( 'SendMailPerLevel' => 1 ) + $levels );
							$uid = $user;
						}
						if ( $uid ) {
							// link order to user
							wc_update_new_customer_past_orders( $uid );
							// update billing and shipping meta
							$metas = get_post_meta( $order_id );
							foreach ( $metas as $key => $value ) {
								if ( preg_match( '/^_((billing|shipping)_.+)/', $key, $match ) ) {
									update_user_meta( $uid, $match[1], $value[0] );
								}
							}
						}
					}

					$old                             = $wlm_no_cartintegrationterminate;
					$wlm_no_cartintegrationterminate = true;
					$_POST['sctxnid']                = $txnid;

					$WishListMemberInstance->ShoppingCartReactivate();
					$wlm_no_cartintegrationterminate = $old;
					break;
				case 'deactivate':
					$old                             = $wlm_no_cartintegrationterminate;
					$wlm_no_cartintegrationterminate = true;
					$_POST['sctxnid']                = $txnid;

					$WishListMemberInstance->ShoppingCartDeactivate();
					$wlm_no_cartintegrationterminate = $old;
					break;
				 case 'pending';
					$user = wishlistmember_instance()->GetUserIDFromTxnID($txnid);
					if ( $user )
					    $user = wishlistmember_instance()->Get_UserData($user);
					if ( $user->ID ) {
						$levels = array_intersect(array_keys((array) wishlistmember_instance()->GetMembershipLevelsTxnIDs($user->ID, $txnid)), wishlistmember_instance()->GetMembershipLevels($user->ID));
						foreach ((array) $levels AS $level) {
						    wishlistmember_instance()->LevelForApproval($level, $user->ID, true);
						}
					}
					break;
				case 'remove':
					$this->__remove_levels( $txnid );
					break;
			}
		}

		/**
		 * Removes levels from a member based on transaction ID
		 *
		 * @param string $txnid
		 */
		private function __remove_levels( $txnid ) {
			global $WishListMemberInstance;
			$user_id = $WishListMemberInstance->GetUserIDFromTxnID( $txnid );
			if ( $user_id ) {
				$levels = $WishListMemberInstance->GetMembershipLevelsTxnIDs( $user_id, $txnid );
				if ( $levels ) {
					wlmapi_update_member( $user_id, array( 'RemoveLevels' => array_keys( $levels ) ) );
				}
			}

		}

		/**
		 * Generates transaction id from order WooCommerce object
		 *
		 * @param WC_Order $order
		 */
		private function __generate_transaction_id( $order ) {
			return 'WooCommerce#' . $order->get_parent_id() . '-' . $order->get_order_number();
		}
	}
	new WLM_INTEGRATION_WOOCOMMERCE();
}
