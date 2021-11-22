<?php
/*
 * Stripe Shopping Cart Integration Functions
 * Original Author : Erwin Atuli
 * Version: $Id: integration.shoppingcart.stripe.php 7502 2021-01-12 16:42:09Z mike $
 */

// $__classname__ = 'WLM_INTEGRATION_STRIPE';
// $__optionname__ = 'stripethankyou';
// $__methodname__ = 'stripe';

if ( ! class_exists( 'WLM_INTEGRATION_STRIPE' ) ) {

	class WLM_INTEGRATION_STRIPE {
		var $wlm;
		public function __construct() {
			$sc = new WLM_Stripe_ShortCodes();
			add_action( 'admin_notices', array( $this, 'notices' ) );
		}
		public function stripe( $wlm ) {
			$this->wlm     = $wlm;
			$action        = trim( strtolower( $_REQUEST['stripe_action'] ) );
			$valid_actions = array( 'charge', 'sync', 'update_payment', 'cancel', 'invoices', 'invoice', 'migrate', 'check_coupon', 'get_coupon', 'sca_redirect' );
			if ( ! in_array( $action, $valid_actions ) ) {
				_e( 'Permission Denied', 'wishlist-member' );
				die();
			}
			if ( ( $action != 'sync' && $action != 'migrate' ) && ! wp_verify_nonce( $_REQUEST['nonce'], "stripe-do-$action" ) ) {
				_e( 'Permission Denied', 'wishlist-member' );
				die();
			}
			switch ( $action ) {
				case 'migrate':
					$this->migrate();
					break;
				case 'charge':
					// code...
					$this->charge( $_POST );
					break;
				case 'sync':
					$this->sync( $_POST );
					break;
				case 'update_payment':
					$this->update_payment( $_POST );
					break;
				case 'cancel':
					$this->cancel( $_POST );
					break;
				case 'invoices':
					$this->invoices( $_POST );
					break;
				case 'invoice':
					$this->invoice( $_POST );
					break;
				case 'check_coupon':
					$this->check_coupon( $_POST );
					break;
				case 'get_coupon':
					$this->get_coupon( $_POST );
					break;
				case 'sca_redirect':
					$this->handle_sca_redirect( $_REQUEST );
					break;
				default:
					// code...
					break;
			}
		}
		public function check_coupon( $data = array() ) {
			$stripeapikey = $this->wlm->GetOption( 'stripeapikey' );
			WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

			try {
				$coupon = WLMStripe\Coupon::retrieve( $data['coupon'] );
				if( $coupon->valid == 1) { 
					echo json_encode( true );
				} else {
					echo json_encode( false );
				}
			} catch ( Exception $e ) {
				echo json_encode( false );
			}

			die();
		}
		public function get_coupon( $data = array() ) {
			$stripeapikey = $this->wlm->GetOption( 'stripeapikey' );
			WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

			try {
				$coupon  = WLMStripe\Coupon::retrieve( $data['coupon'] );
				$coupons = array(
					'c_type'   => $coupon->amount_off ? 'amount_off' : 'percent_off',
					'c_amount' => $coupon->amount_off ? $coupon->amount_off : $coupon->percent_off,
				);

				echo json_encode( $coupons );
			} catch ( Exception $e ) {
				echo json_encode( '' );
			}

			die();
		}
		public function migrate() {
			$users = get_users();
			echo sprintf( "migrating %s stripe users<br/>\n", count( $users ) );

			$live = $_GET['live'];
			foreach ( $users as $u ) {
				$cust_id = $this->wlm->Get_UserMeta( $u->ID, 'custom_stripe_cust_id' );

				echo sprintf( 'migrating user %s with stripe_cust_id: <br/>', $u->ID, $cust_id );
				if ( $live || ! empty( $cust_id ) ) {
					$this->wlm->Update_UserMeta( $u->ID, 'stripe_cust_id', $cust_id );
				}
			}
		}
		public function cancel( $data = array() ) {
			global $current_user;
			if ( empty( $current_user->ID ) ) {
				return;
			}
			$stripeapikey   = $this->wlm->GetOption( 'stripeapikey' );
			$stripe_cust_id = $this->wlm->Get_UserMeta( $current_user->ID, 'stripe_cust_id' );
			$stripesettings = $this->wlm->GetOption( 'stripesettings' );
			$connections    = $this->wlm->GetOption( 'stripeconnections' );
			WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

			try {
				// also handle onetime payments
				// $this->wlm->ShoppingCartDeactivate();
				$stripe_level_settings = $connections[ wlm_arrval( $_POST, 'wlm_level' ) ];
				if ( ! empty( $stripe_level_settings['subscription'] ) ) {
					$cust          = WLMStripe\Customer::retrieve( $stripe_cust_id );
					if ( !$cust->subscriptions ) {						
						$cust = WLMStripe\Customer::retrieve(
							array(
							    'id'        => $stripe_cust_id,
							    'expand'    => array( 'subscriptions' ),
							)
					 	);					 	
					}
					$at_period_end = false;
					if ( ! empty( $stripesettings['endsubscriptiontiming'] ) && $stripesettings['endsubscriptiontiming'] == 'periodend' ) {
						$at_period_end = true;
					}
					// Check if customer has more than 1 subscription, if so then get the
					// subscription ID and only cancel the subscription that matches the STRIPE PLAN
					// passed in the $_POST data
					if ( count( $cust->subscriptions->data ) > 1 ) {
						list($c_id, $plan_id) = explode( '-', $data['txn_id'] );
						foreach ( $cust->subscriptions->data as $d ) {
							if ( $d->plan->id == $plan_id ) {
								$sub_id = $d->id;

								if ( $at_period_end ) {
									$update = WLMStripe\Subscription::update(
										$sub_id,
										array(
											'cancel_at_period_end' => $at_period_end,
										)
									);
								} else {
									$subscription = WLMStripe\Subscription::retrieve( $sub_id );
									$subscription->cancel();
								}
							}
						}
					} else {
						if ( $at_period_end ) {
							$sub_id = $cust->subscriptions->data[0]->id;
							$update = WLMStripe\Subscription::update(
								$sub_id,
								array(
									'cancel_at_period_end' => $at_period_end,
								)
							);
						} else {
							$sub_id = $cust->subscriptions->data[0]->id;

							$subscription = WLMStripe\Subscription::retrieve( $sub_id );
							$subscription->cancel();
						}
					}
				} else {
					$_POST['sctxnid'] = $_REQUEST['txn_id'];
					$this->wlm->ShoppingCartDeactivate();
				}
				$status = 'ok';
			} catch ( Exception $e ) {
				$status = 'fail&err=' . $e->getMessage();
			}
			$uri = $data['redirect_to'];
			if ( ! empty( $stripesettings['cancelredirect'] ) ) {
				$uri = get_permalink( $stripesettings['cancelredirect'] );
			}
			if ( stripos( $uri, '?' ) !== false ) {
				$uri .= "&status=$status";
			} else {
				$uri .= "?&status=$status";
			}
			wp_redirect( $uri );
			die();
		}

		public function update_payment( $data = array() ) {
			$stripeapikey = $this->wlm->GetOption( 'stripeapikey' );
			WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

			try {
				global $current_user;
				if ( empty( $current_user->ID ) ) {
					throw new Exception( __( 'An error occured while processing the request, Please try again', 'wishlist-member' ) );
				}
				$cust_id = $this->wlm->Get_UserMeta( $current_user->ID, 'stripe_cust_id' );
				if ( empty( $cust_id ) ) {
					// user is a member but not linked
					// try to create this user in stripe
					$cust_details = array(
						'name' => sprintf( '%s %s', $current_user->first_name, $current_user->last_name ),
						'description' => sprintf( '%s %s', $current_user->first_name, $current_user->last_name ),
						'email'       => $current_user->user_email,
					);
					$cust         = WLMStripe\Customer::create( $cust_details );

					$payment_method = WLMStripe\PaymentMethod::create(
						array(
							'type' => 'card',
							'card' => array(
								'token' => $data['stripeToken'],
							),
						)
					);

					$payment_method = WLMStripe\PaymentMethod::retrieve( $payment_method->id );
					$payment_method->attach( array( 'customer' => $cust->id ) );

					$cust->invoice_settings->default_payment_method = $payment_method->id;
					$cust->save();

					$this->wlm->Update_UserMeta( $current_user->ID, 'stripe_cust_id', $cust->id );
				} else {
					$cust = WLMStripe\Customer::retrieve( $cust_id );

					$payment_method = WLMStripe\PaymentMethod::create(
						array(
							'type' => 'card',
							'card' => array(
								'token' => $data['stripeToken'],
							),
						)
					);

					$payment_method = WLMStripe\PaymentMethod::retrieve( $payment_method->id );
					$payment_method->attach( array( 'customer' => $cust->id ) );

					$cust->invoice_settings->default_payment_method = $payment_method->id;
					$cust->save();
				}
				$status = 'ok';
			} catch ( Exception $e ) {
				$err    = preg_replace( '/\s+/', '+', $e->getMessage() );
				$status = 'fail&err=' . $err;
			}

			$uri = $data['redirect_to'];
			if ( stripos( $uri, '?' ) !== false ) {
				$uri .= "&status=$status";
			} else {
				$uri .= "?&status=$status";
			}
			wp_redirect( $uri );
			die();
		}

		public function sync( $data = array() ) {
			$this->wlm->SyncMembership();
			$obj    = json_decode( file_get_contents( 'php://input' ) );
			$id     = null;
			$action = null;
			WLMStripe\WLM_Stripe::setApiKey( $this->wlm->GetOption( 'stripeapikey' ) );

			// If $obj is empty then just return, otherwise it will show errors when viewed in browser
			if ( empty( $obj ) ) {
				die( "\n" );
			}

			// Means this came from a test web hook URL
			// Skip sync process to avoid 500 internal server error as
			// the Sync process will throw errors
			if ( $obj->id == 'evt_00000000000000' ) {
				die( "\n" );
			}

			// Request for the stripe event object to
			// make sure this is a legit stripe notification
			$obj = WLMStripe\Event::retrieve( $obj->id );

			switch ( $obj->type ) {
				// do not handler creates anymore
				// case 'customer.subscription.created':
				// $cust_id = $obj->data->object->customer;
				// $plan_id = $obj->data->object->plan->id;
				// $id = $cust_id . "-" . $plan_id;
				// $action = 'move';
				// break;
				case 'customer.subscription.deleted':
					$cust_id = $obj->data->object->customer;
					$plan_id = $obj->data->object->plan->id;
					$id      = $cust_id . '-' . $plan_id;
					$action  = 'deactivate';
					break;

				case 'customer.subscription.created':
				case 'customer.subscription.updated':
					$cust_id = $obj->data->object->customer;
					$plan_id = $obj->data->object->plan->id;
					$id      = $cust_id . '-' . $plan_id;

					switch ( $obj->data->object->status ) {
						case 'trialing':
						case 'past_due':
							$action = 'reactivate';
							break;
						case 'active':
							$action = 'reactivate';
							if ( ! empty( $obj->data->previous_attributes->plan->id ) ) {
								// we are changing subscriptions
								$prev_id = sprintf( '%s-%s', $cust_id, $obj->data->previous_attributes->plan->id );
								$action  = 'move';
							}
							break;
						case 'unpaid':
						case 'cancelled':
						default:
							$action = 'deactivate';
							break;
					}

					// This is an active subscription
					// reactivate? No need
					break;
				case 'invoice.payment_failed':
					// no need, we'll also be able to catch this under charge_failed
					break;

				case 'customer.deleted':
					$cust_id = $obj->data->object->id;
					$user_id = $this->wlm->Get_UserID_From_UserMeta( 'stripe_cust_id', $cust_id );
					$levels  = $this->wlm->GetMembershipLevels( $user_id, null, true, null, true );
					if ( empty( $levels ) ) {
						die( "\n" );
					}
					$id     = $this->wlm->GetMembershipLevelsTxnID( $user_id, $levels[0] );
					$action = 'deactivate';
					break;
				case 'charge.refunded':
					$id     = $obj->data->object->id;
					$action = 'deactivate';
					break;
				case 'charge.failed':
					// no need to handle as failed charges are handled
					// in the merchant site
					// $cust_id = $obj->data->object->customer;
					// $customer = WLMStripe\Customer::retrieve($cust_id);
					// if (empty($customer->plan)) {
					// return;
					// }
					// $id = sprintf("%s-%s", $cust_id, $customer->plan->id);
					// $action = 'deactivate';
					//
					break;
			}

			$_POST['sctxnid'] = $id;
			switch ( $action ) {
				case 'deactivate':
					echo 'info(deact): deactivating subscription: ' . $id;
					$_POST['sctxnid'] = $id;
					$this->wlm->ShoppingCartDeactivate();
					break;
				case 'reactivate':
					echo 'info(react): reactivating subscription: ' . $id;
					$_POST['sctxnid'] = $id;

					$_POST['sc_type'] = 'Stripe';
					do_action( 'wlm_shoppingcart_rebill', $_POST );

					$this->wlm->ShoppingCartReactivate();

					break;
				case 'move':
					// activate the new one
					$connections = $this->wlm->GetOption( 'stripeconnections' );

					// get the correct level
					$wpm_level      = $this->stripe_plan_id_to_sku( $connections, $obj->data->object->plan->id );
					$prev_wpm_level = $this->stripe_plan_id_to_sku( $connections, $obj->data->previous_attributes->plan->id );

					// get the correct user
					$user_id = $this->wlm->Get_UserID_From_UserMeta( 'stripe_cust_id', $cust_id );

					if ( ! empty( $wpm_level ) && ! empty( $user_id ) ) {
						// remove from previous level
						$current_levels = $this->wlm->GetMembershipLevels( $user_id, null, null, true );
						$levels         = array_diff( $current_levels, array( $prev_wpm_level ) );
						echo 'removing from ' . $prev_wpm_level;
						$this->wlm->SetMembershipLevels( $user_id, $levels );

						echo "info(move): moving user:$user_id to new subscription:$wpm_level with tid:$id";
						$this->add_to_level( $user_id, $wpm_level, $id );
					}
					break;
			}
			die( "\n" );
		}
		public function stripe_plan_id_to_sku( $connections, $id ) {
			foreach ( $connections as $c ) {
				if ( $c['plan'] == $id ) {
					return $c['sku'];
				}
			}
		}
		public function add_to_level( $user_id, $level_id, $txn_id ) {
			$user = new \WishListMember\User( $user_id );

			 $wpm_levels = $this->wlm->GetOption( 'wpm_levels' );

			 $this->wlm->SetMembershipLevels( $user_id, array( $level_id ), null, null, null, null, null, null, true );

			// Send email notifications
			if($wpm_levels[$level_id]['newuser_notification_user'] == 1) {
				$email_macros = array(
					'[password]' => '********',
					'[memberlevel]' => $wpm_levels[$level_id]['name'],
				);
				$this->wlm->email_template_level = $level_id;
				$this->wlm->send_email_template( 'registration', $user_id, $email_macros );
			}

			if($wpm_levels[$level_id]['newuser_notification_admin'] == 1) {
				$this->wlm->email_template_level = $level_id;
				$this->wlm->send_email_template( 'admin_new_member_notice', $user_id, $email_macros, $this->wlm->GetOption( 'email_sender_address' ) );
		    }

			if ( isset( $wpm_levels[ $level_id ]['registrationdatereset'] ) ) {
				$timestamp = time();
				$this->wlm->UserLevelTimestamp( $user_id, $level_id, $timestamp );
			}

			if ( $this->wlm->IsPPPLevel( $level_id ) ) {
				list($tmp, $content_id) = explode( '-', $level_id );
				$this->wlm->AddUserPostTransactionID( $user_id, $content_id, $txn_id );

				if ( empty( $timestamp ) ) {
					$timestamp = time();
				}

				$this->wlm->AddUserPostTimestamp( $user_id, $content_id, $timestamp );
			} else {
				$this->wlm->SetMembershipLevelTxnID( $user_id, $level_id, $txn_id );
			}
		}
		public function charge_existing( $data ) {

			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			$connections    = $this->wlm->GetOption( 'stripeconnections' );
			$stripesettings = $this->wlm->GetOption( 'stripesettings' );
			$stripe_plan    = $connections[ $data['wpm_id'] ]['plan'];
			$settings       = $connections[ $data['wpm_id'] ];

			WLMStripe\WLM_Stripe::setApiVersion( '2019-08-14' );

			try {

				global $current_user;
				$stripe_cust_id = $this->wlm->Get_UserMeta( $current_user->ID, 'stripe_cust_id' );

				if ( $data['subscription'] ) {

					// since 3.6 change the plan to customer-selected plan if there is one
					$stripe_plan = $this->choose_plan( $stripe_plan, $connections, $data );

					if ( ! empty( $stripe_cust_id ) ) {
						$cust = WLMStripe\Customer::retrieve( $stripe_cust_id );

						$stripe_cust_payment_method_id = $cust->invoice_settings->default_payment_method;

						// If customer has Stripe Customer ID but doesn't have a payment Method ID (they might have bought using // token before) then create a payment method ID using the new card they used on purchase and attach it.
						if ( empty( $stripe_cust_payment_method_id ) ) {
							$payment_method = WLMStripe\PaymentMethod::create(
								array(
									'type' => 'card',
									'card' => array(
										'token' => $data['stripeToken'],
									),
								)
							);
						} else {
							$payment_method = WLMStripe\PaymentMethod::retrieve( $stripe_cust_payment_method_id );
						}

						$payment_method = WLMStripe\PaymentMethod::retrieve( $payment_method->id );
						$payment_method->attach( array( 'customer' => $cust->id ) );

						$cust->invoice_settings->default_payment_method = $payment_method->id;
						$cust->save();

						if ( empty( $payment_method->id ) ) {
							throw new Exception( 'Could not verify credit card information' );
						}
					} else {
						if ( empty( $data['stripeToken'] ) ) {
							throw new Exception( 'Could not verify credit card information' );
						}
						$cust_details = array(
							'name' => sprintf( '%s %s', $data['firstname'], $data['lastname'] ),
							'description' => sprintf( '%s %s', $data['firstname'], $data['lastname'] ),
							'email'       => $data['email'],
						);
						$cust         = WLMStripe\Customer::create( $cust_details );

						$payment_method = WLMStripe\PaymentMethod::create(
							array(
								'type' => 'card',
								'card' => array(
									'token' => $data['stripeToken'],
								),
							)
						);

						$payment_method = WLMStripe\PaymentMethod::retrieve( $payment_method->id );
						$payment_method->attach( array( 'customer' => $cust->id ) );

						$cust->invoice_settings->default_payment_method = $payment_method->id;
						$cust->save();

						$this->wlm->Update_UserMeta( $current_user->ID, 'stripe_cust_id', $cust->id );
					}

					$prorate = true;
					if ( ! empty( $stripesettings['prorate'] ) && $stripesettings['prorate'] == 'no' ) {
						$prorate = false;
					}

					if ( ! empty( $stripe_plan ) ) {
						foreach ( $cust->subscriptions->data as $sub ) {
							if ( $sub->plan->id == $stripe_plan ) {
								throw new Exception( __( 'Cannot purchase an active plan', 'wishlist-member' ) );
							}
						}
					}

					if ( empty( $data['coupon'] ) ) {
						unset( $params['coupon'] );
					}

					$txn_id = $this->charge_plan( $stripe_plan, $cust, $prorate, $data, 'charge_existing', $current_user->ID );

				} else {

					if ( ! empty( $stripe_cust_id ) ) {
						$cust = WLMStripe\Customer::retrieve( $stripe_cust_id );

						$stripe_cust_payment_method_id = $cust->invoice_settings->default_payment_method;

						// If customer has Stripe Customer ID but doesn't have a payment Method ID (they might have bought using // token before) then create a payment method ID using the new card they used on purchase and attach it.
						if ( empty( $stripe_cust_payment_method_id ) ) {
							$payment_method = WLMStripe\PaymentMethod::create(
								array(
									'type' => 'card',
									'card' => array(
										'token' => $data['stripeToken'],
									),
								)
							);
						} else {
							$payment_method = WLMStripe\PaymentMethod::retrieve( $stripe_cust_payment_method_id );
						}

						$payment_method = WLMStripe\PaymentMethod::retrieve( $payment_method->id );
						$payment_method->attach( array( 'customer' => $cust->id ) );

						$cust->invoice_settings->default_payment_method = $payment_method->id;
						$cust->save();

						if ( empty( $payment_method->id ) ) {
							throw new Exception( 'Could not verify credit card information' );
						}
					} else {
						// Create USER
						$cust = WLMStripe\Customer::create(
							array(
								'name' => sprintf( '%s %s', $data['firstname'], $data['lastname'] ),
								'description' => sprintf( '%s %s', $data['firstname'], $data['lastname'] ),
								'email'       => $data['email'],
							)
						);

						$this->wlm->Update_UserMeta( $current_user->ID, 'stripe_cust_id', $cust->id );

						// Instead of directly using tokens to charge we now create a payment method using Stripe Tokens and then
						// attach it to customer for future charges. This is a bit different from Stripe tokens.
						$payment_method = WLMStripe\PaymentMethod::create(
							array(
								'type' => 'card',
								'card' => array(
									'token' => $data['stripeToken'],
								),
							)
						);
					}

					$currency = empty( $stripesettings['currency'] ) ? 'USD' : $stripesettings['currency'];

					// override amount and currency if set in shortcode
					$currency = isset( $data['stripe_currency'] ) ? strtoupper( $data['stripe_currency'] ) : $currency;
					$amt      = isset( $data['stripe_amount'] ) ? (float) $data['stripe_amount'] : $settings['amount'];
					$amt      = number_format( $amt * 100, 0, '.', '' );

					$level      = wlmapi_get_level( $data['wpm_id'] );
					$level_name = $level['level']['name'];

					if ( empty( $level_name ) ) {
						$ppp_level  = $WishListMemberInstance->IsPPPLevel( $data['sku'] );
						$level_name = $ppp_level->post_title;
					}

					// Create the PaymentIntent
					$intent = WLMStripe\PaymentIntent::create(
						array(
							'customer'            => $cust->id,
							'payment_method'      => $payment_method->id,
							'amount'              => $amt,
							'currency'            => $currency,
							'confirmation_method' => 'automatic',
							'confirm'             => true,
							'description'         => sprintf( '%s - One Time Payment', $level_name ),
						)
					);

					$txn_id = $intent->charges->data[0]->id;

					// If payment requires AUTH then let process_sca_auth handle it.
					if ( $intent->status == 'requires_action' && $intent->next_action->type == 'use_stripe_sdk' ) {
						$this->process_sca_auth( $data, $intent->client_secret, $cust->id, '', $intent->id, 'charge_existing', $current_user->ID );
					}
				}

				// add user to level and redirect to the after reg url
				$this->add_to_level( $current_user->ID, $data['sku'], $txn_id );
				$url = $this->wlm->GetAfterRegRedirect( $data['sku'] );
				wp_redirect( $url );
				die();
			} catch ( Exception $e ) {
				$this->fail(
					array(
						'msg' => $e->getMessage(),
						'sku' => $data['wpm_id'],
					)
				);
			}
		}
		public function charge_new( $data ) {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			$connections    = $this->wlm->GetOption( 'stripeconnections' );
			$stripesettings = $this->wlm->GetOption( 'stripesettings' );
			$stripe_plan    = $connections[ $data['wpm_id'] ]['plan'];
			$settings       = $connections[ $data['wpm_id'] ];
			WLMStripe\WLM_Stripe::setApiVersion( '2019-08-14' );

			try {
				if ( $data['subscription'] ) {

					// since 3.6 change the plan to customer-selected plan if there is one
					$stripe_plan = $this->choose_plan( $stripe_plan, $connections, $data );

					// Create USER
					$cust = WLMStripe\Customer::create(
						array(
							'name' => sprintf( '%s %s', $data['firstname'], $data['lastname'] ),
							'description' => sprintf( '%s %s', $data['firstname'], $data['lastname'] ),
							'email'       => $data['email'],
						)
					);

					$prorate = true;
					if ( ! empty( $stripesettings['prorate'] ) && $stripesettings['prorate'] == 'no' ) {
						$prorate = false;
					}

					if ( empty( $data['coupon'] ) ) {
						unset( $params['coupon'] );
					}

					// Instead of directly using tokens to charge we now create a payment method using Stripe Tokens and then
					// attach it to customer for future charges. This is a bit different from Stripe tokens.
					$payment_method = WLMStripe\PaymentMethod::create(
						array(
							'type' => 'card',
							'card' => array(
								'token' => $data['stripeToken'],
							),
						)
					);

					$payment_method = WLMStripe\PaymentMethod::retrieve( $payment_method->id );
					$payment_method->attach( array( 'customer' => $cust->id ) );

					$cust->invoice_settings->default_payment_method = $payment_method->id;
					$cust->save();

					$txn_id = $this->charge_plan( $stripe_plan, $cust, $prorate, $data, 'charge_new' );
					
				} else {
					$currency = empty( $stripesettings['currency'] ) ? 'USD' : $stripesettings['currency'];

					// override amount and currency if set in shortcode
					$currency = isset( $data['stripe_currency'] ) ? strtoupper( $data['stripe_currency'] ) : $currency;
					$amt      = isset( $data['stripe_amount'] ) ? (float) $data['stripe_amount'] : $settings['amount'];
					$amt      = number_format( $amt * 100, 0, '.', '' );

					// Create USER
					$cust = WLMStripe\Customer::create(
						array(
							'name' => sprintf( '%s %s', $data['firstname'], $data['lastname'] ),
							'description' => sprintf( '%s %s', $data['firstname'], $data['lastname'] ),
							'email'       => $data['email'],
						)
					);

					// Instead of directly using tokens to charge we now create a payment method using Stripe Tokens and then
					// attach it to customer for future charges. This is a bit different from Stripe tokens.
					$payment_method = WLMStripe\PaymentMethod::create(
						array(
							'type' => 'card',
							'card' => array(
								'token' => $data['stripeToken'],
							),
						)
					);

					$payment_method = WLMStripe\PaymentMethod::retrieve( $payment_method->id );
					$payment_method->attach( array( 'customer' => $cust->id ) );

					$cust->invoice_settings->default_payment_method = $payment_method->id;
					$cust->save();

					// Get the level name as using $settings['membershiplevel'] may cause issues if the admin changes the name of // the membership level.
					$level      = wlmapi_get_level( $data['wpm_id'] );
					$level_name = $level['level']['name'];

					// Create the PaymentIntent
					$intent = WLMStripe\PaymentIntent::create(
						array(
							'customer'            => $cust->id,
							'payment_method'      => $payment_method->id,
							'amount'              => $amt,
							'currency'            => $currency,
							'confirmation_method' => 'automatic',
							'confirm'             => true,
							'description'         => sprintf( '%s - One Time Payment', $level_name ),
						)
					);

					$txn_id = $intent->charges->data[0]->id;

					// If payment requires AUTH then let process_sca_auth handle it.
					if ( $intent->status == 'requires_action' && $intent->next_action->type == 'use_stripe_sdk' ) {
						$this->process_sca_auth( $data, $intent->client_secret, $cust->id, '', $intent->id, 'charge_new' );
					}
				}

				$_POST['sctxnid'] = $txn_id;
				$this->wlm->ShoppingCartRegistration( true, false );

				$user = get_user_by( 'login', 'temp_' . md5( $data['email'] ) );
				$this->wlm->Update_UserMeta( $user->ID, 'stripe_cust_id', $cust->id );
				$this->wlm->Update_UserMeta( $user->ID, 'stripe_payment_method_id', $payment_method->id );
				$url = $this->wlm->GetContinueRegistrationURL( $data['email'] );
				wp_redirect( $url );
				die();
			} catch ( Exception $e ) {

				if ( $subs->latest_invoice->payment_intent->status == 'requires_action' ) {
					$this->fail(
						array(
							'msg'             => $e->getMessage(),
							'sku'             => $data['wpm_id'],
							'p_intent_secret' => $subs->latest_invoice->payment_intent->client_secret,
							'cus_id'          => $subs->latest_invoice->customer,
						)
					);
				} else {
						$cust->delete();

						$this->fail(
							array(
								'msg' => $e->getMessage(),
								'sku' => $data['wpm_id'],
							)
						);
				}
			}

		}

		public function charge_plan( $stripe_plan, $cust, $prorate, $data, $sca_charge_type, $cuid = '' ) {
			$plan = WLMStripe\Price::retrieve( $stripe_plan );
			if( $plan->recurring ) {
				// recurring plan
				$subs = WLMStripe\Subscription::create(
					array(
						'customer'        => $cust->id,
						'prorate'         => $prorate,
						'coupon'          => $data['coupon'],
						'trial_from_plan' => true,
						'items'           => array(
							array(
								'plan' => $stripe_plan,
							),
						),
						'expand'          => array( 'latest_invoice.payment_intent' ),
					)
				);
				$latest_invoice = $subs->latest_invoice;
			} else {
				// one time payment plan
				$discount = null;
				if( $data['coupon'] ) {
					$discount = $cust->discount;
					$cust->coupon = $data['coupon'];
					$cust->save();
				}
				$invitem = WLMStripe\InvoiceItem::create(
					array(
							'customer' => $cust->id,
							'price'    => $stripe_plan,
					)
				);
				$latest_invoice = WLMStripe\Invoice::create(
					array(
						'customer' => $cust->id,
					)
				);
				$latest_invoice->pay();
			}
			
			if ( $latest_invoice->payment_intent->charges->data[0]->status == 'failed' ) {
				throw new Exception($latest_invoice->payment_intent->charges->data[0]->failure_message);
			}
			
			if ( $latest_invoice->payment_intent->status == 'requires_action' ) {
				// If card needs authentication then let's initiate SCA popup
				$this->process_sca_auth( $data, $latest_invoice->payment_intent->client_secret, $cust->id, $stripe_plan, '', $sca_charge_type, $cuid );
			}

			if( $data['coupon'] ) {
				$cust->coupon = $discount ? $discount->coupon->id : null;
				$cust->save();
			}

			$txn_id = sprintf( '%s-%s', $cust->id, $stripe_plan );
			return $txn_id;
		}
		/**
		 * Process payments that needs SCA authentication
		 * in a form of a pop up modal which Stripe handles via Stripe JS.
		 *
		 * @param array  $data array of data needed to create temp accounts for users
		 * @param string $payment_intent_secret - Needed to trigger the SCA pop up modal
		 * @param string $cust_id - Customer ID created in STripe
		 * @param string $stripe_plan - ID of the Stripe Plan
		 * @param string $payment_intent - Payment Intent ID needed to get the charges->id in function handle_sca_redirect()
		 * @param string $charge_type - (charge_new, charge_existing)
		 * @param int    $user_id - User's WordPress USER ID
		 */
		function process_sca_auth( $data, $payment_intent_secret, $cust_id, $stripe_plan = '', $payment_intent = '', $charge_type = '', $user_id = '' ) {

			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			$stripepublishablekey = trim( $this->wlm->GetOption( 'stripepublishablekey' ) );

			// Build the Success Redirect
			$sca_redirect_nonce = wp_create_nonce( 'stripe-do-sca_redirect' );
			$stripethankyou     = $this->wlm->GetOption( 'stripethankyou' );
			$stripethankyou_url = $this->wlm->make_thankyou_url( $stripethankyou );
			$sca_params         = '?stripe_action=sca_redirect&cus_id=' . $cust_id . '&sku=' . $data['wpm_id'] . '&plan_id=' . $stripe_plan . '&fn=' . $_POST['firstname'] . '&ln=' . $_POST['lastname'] . '&p_intent=' . $payment_intent . '&charge_type=' . $charge_type . '&u_id=' . $user_id . '&nonce=' . $sca_redirect_nonce;
			$success_redirect   = $stripethankyou_url . $sca_params;

			// Build the error redirect URL so that we can redirect and tell them in case SCA
			// Authentication Failed
			$error_redirect = $_REQUEST['redirect_to'];

			if ( stripos( $error_redirect, '?' ) !== false ) {
				$error_redirect .= '&status=fail&reason=' . preg_replace( '/\s+/', '+', 'Failed to complete the Strong Customer Authentication. The payment was not processed.' );
			} else {
				$error_redirect .= '?&status=fail&reason=' . preg_replace( '/\s+/', '+', 'Failed to complete the Strong Customer Authentication. The payment was not be processed.' );
			}
			$error_redirect .= '#regform-' . $data['sku'];

			?>
			<script src="https://js.stripe.com/v3/"></script>
			<script type="text/javascript">

				var stripe = Stripe('<?php echo $stripepublishablekey; ?>');

				var paymentIntentSecret = "<?php echo $payment_intent_secret; ?>";

					stripe.handleCardPayment(paymentIntentSecret).then(function(result) {
					  if (result.error) {
						  window.location.replace('<?php echo $error_redirect; ?>');
					  } else {
						window.location.replace('<?php echo $success_redirect; ?>');
					  }
					});
			</script>
			<?php
			$animation_image = $WishListMemberInstance->pluginURL . '/images/loadingAnimation.gif';
			$notify_msg      = __( 'The payment will not be processed and services will not be provisioned until authentication is completed. Please complete the authentication.', 'wishlist-member' );
			echo '<br><br><br><center>' . $notify_msg . '<br><br><img src="' . $animation_image . '"</center>';
			die();
		}

		/**
		 * This handles creating temp account, redirecting users to reg page and adding them to levels
		 *
		 * @param array $data array of data from $_GET
		 */
		public function handle_sca_redirect( $data ) {

			$stripeapikey = $this->wlm->GetOption( 'stripeapikey' );
			WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

			$cust = WLMStripe\Customer::retrieve( $data['cus_id'] );

			if ( ! empty( $data['plan_id'] ) ) {
				$txn_id = sprintf( '%s-%s', $cust->id, $data['plan_id'] );
			} else {
				// If it's one time then we get the charges ID from payment intent created for the customer
				$payment_intent_id = $data['p_intent'];
				$intent            = WLMStripe\PaymentIntent::retrieve( $payment_intent_id );
				$txn_id            = $intent->charges->data[0]->id;
			}

			if ( $data['charge_type'] == 'charge_new' ) {

				$_POST['sctxnid']          = $txn_id;
				$_POST['stripe_wlm_level'] = $data['sku'];
				$_POST['lastname']         = $data['ln'];
				$_POST['firstname']        = $data['fn'];
				$_POST['action']           = 'wpm_register';
				$_POST['wpm_id']           = $data['sku'];
				$_POST['username']         = $cust->email;
				$_POST['email']            = $cust->email;
				$_POST['password1']        = $_POST['password2'] = $this->wlm->PassGen();

				$this->wlm->ShoppingCartRegistration( true, false );
				$user = get_user_by( 'login', 'temp_' . md5( $cust->email ) );
				$this->wlm->Update_UserMeta( $user->ID, 'stripe_cust_id', $cust->id );

				// If p_intent is present then this is one time which uses PaymentMethod to make charges.
				// Let's save the payment method ID to the user ID.
				if ( ! empty( $data['p_intent'] ) ) {
					$this->wlm->Update_UserMeta( $user->ID, 'stripe_payment_method_id', $intent->payment_method );
				}

				$url = $this->wlm->GetContinueRegistrationURL( $cust->email );

			} elseif ( $data['charge_type'] == 'charge_existing' ) {
				// add user to level and redirect to the after reg url
				$this->add_to_level( $data['u_id'], $data['sku'], $txn_id );
				$url = $this->wlm->GetAfterRegRedirect( $data['sku'] );
			}
			wp_redirect( $url );
			die();
		}

		public function fail( $data ) {
			$uri = $_REQUEST['redirect_to'];
			if ( stripos( $uri, '?' ) !== false ) {
				$uri .= '&status=fail&reason=' . preg_replace( '/\s+/', '+', $data['msg'] );
			} else {
				$uri .= '?&status=fail&reason=' . preg_replace( '/\s+/', '+', $data['msg'] );
			}
			$uri .= '#regform-' . $data['sku'];
			// error_log($uri);
			wp_redirect( $uri, 307 );

			die();
		}

		public function charge( $data = array() ) {
			$stripeconnections = $this->wlm->GetOption( 'stripeconnections' );
			$stripeapikey      = $this->wlm->GetOption( 'stripeapikey' );
			$settings          = $stripeconnections[ $data['sku'] ];
			WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

			try {
				$btn_hash        = isset( $data['btn_hash'] ) ? $data['btn_hash'] : false;
				$custom_amount   = isset( $data['custom_amount'] ) ? $data['custom_amount'] : false;
				$custom_currency = isset( $data['custom_currency'] ) ? $data['custom_currency'] : false;
				if ( $custom_amount !== false || $custom_currency !== false ) {
					if ( ! wp_verify_nonce( $btn_hash, "{$stripeapikey}-{$custom_amount}-{$custom_currency}" ) ) {
						throw new Exception( 'Your request is invalid or expired. Please try again.' );
					}
				}

				$last_name  = $data['last_name'];
				$first_name = $data['first_name'];
				if ( $charge_type == 'new' ) {
					if ( empty( $last_name ) || empty( $first_name ) || empty( $data['email'] ) ) {
						throw new Exception( 'All fields are required' );
					}

					if ( empty( $data['stripeToken'] ) ) {
						throw new Exception( 'Payment Processing Failed' );
					}
				}

				$_POST['stripe_wlm_level'] = $data['sku'];
				$_POST['lastname']         = $last_name;
				$_POST['firstname']        = $first_name;
				$_POST['action']           = 'wpm_register';
				$_POST['wpm_id']           = $data['sku'];
				$_POST['username']         = $data['email'];
				$_POST['email']            = $data['email'];
				$_POST['password1']        = $_POST['password2'] = $this->wlm->PassGen();

				// lets add custom currency and amount
				if ( $custom_amount ) {
					$_POST['stripe_amount'] = $custom_amount;
				}
				if ( $custom_currency ) {
					$_POST['stripe_currency'] = trim( $custom_currency );
				}

				if ( $data['charge_type'] == 'new' ) {
					$this->charge_new( $_POST );
				} else {
					$this->charge_existing( $_POST );
				}
			} catch ( Exception $e ) {
				$this->fail(
					array(
						'msg' => $e->getMessage(),
						'sku' => $data['sku'],
					)
				);
			}
		}

		// following functions are used to query invoices
		// and returns content ready for display for member profile
		public function invoice( $data ) {
			global $current_user;
			if ( empty( $current_user->ID ) ) {
				return;
			}

			try {
				$stripeapikey = $this->wlm->GetOption( 'stripeapikey' );
				WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

				$inv  = WLMStripe\Invoice::retrieve( $data['txn_id'] );
				$cust = WLMStripe\Customer::retrieve( $inv['customer'] );
				include $this->get_view_path( 'invoice_details' );
				die();
			} catch ( Exception $e ) {

			}
		}

		public function invoices( $data ) {
			global $WishListMemberInstance;
			global $current_user;
			if ( empty( $current_user->ID ) ) {
				return;
			}
			$cust_id = $this->wlm->Get_UserMeta( $current_user->ID, 'stripe_cust_id' );
			try {
				$stripeapikey = $this->wlm->GetOption( 'stripeapikey' );
				$txns         = $this->wlm->GetMembershipLevelsTxnIDs( $current_user->ID );
				WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

				$inv      = WLMStripe\Invoice::all(
					array(
						'count'    => 100,
						'customer' => $cust_id,
					)
				);
				$invoices = array();
				if ( ! empty( $inv['data'] ) ) {
					$invoices = array_merge( $invoices, $inv['data'] );
				}
				// try to get manual charges
				// $manual_charges = WLMStripe\Charge::all(array("count" => 100, 'customer' => $cust_id));
				// $invoices = array_merge($invoices, $inv['data']);
				// var_dump($manual_charges);

				include $this->get_view_path( 'invoice_list' );
				die();
			} catch ( Exception $e ) {
				_e( '<p>No invoices found for this user</p>', 'wishlist-member' );
				die();
			}
		}
		public function get_view_path( $handle ) {
			global $WishListMemberInstance;
			return sprintf( $WishListMemberInstance->pluginDir . '/extlib/wlm_stripe/%s.php', $handle );
		}

		/**
		 * @since 3.6
		 * Replace $stripe_plan with the customer-selected payment plan if the latter is valid
		 *
		 * @param  string $stripe_plan   The original payment plan
		 * @param  array  $connections   Configured stripe connections
		 * @param  array  $data          Post data
		 * @return string                Customer-selected payment plan if valid, otherwise return $stripe_plan
		 */
		private function choose_plan( $stripe_plan, $connections, $data ) {
			/**
			 * since 3.6 Check if customer chose a plan from our payment form and if it is
			 * not the same as $stripe_plan then check if it's any of the configured plans
			 * and if so then change $stripe_plan to $selected_plan
			 */
			$selected_plan = wlm_arrval( $data, 'stripe_plan' );
			if ( $selected_plan && $selected_plan != $stripe_plan ) {
				$plans = json_decode( stripslashes( wlm_arrval( $connections[ $data['wpm_id'] ], 'plans' ) ) );
				if ( $plans && is_array( $plans ) && in_array( $selected_plan, $plans ) ) {
					$stripe_plan = $selected_plan;
				}
			}
			return $stripe_plan;
		}
	}

}
