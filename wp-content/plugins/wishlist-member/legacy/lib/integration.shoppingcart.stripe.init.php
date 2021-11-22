<?php

if ( extension_loaded( 'curl' ) && function_exists( 'mb_detect_encoding' ) && ! class_exists( 'Stripe', false ) ) {
	include_once $this->pluginDir . '/extlib/Stripe/init.php';
}

if ( ! class_exists( 'WLM_Stripe_ShortCodes' ) ) {
	class WLM_Stripe_ShortCodes {
		public function __construct() {
			global $WishListMemberInstance;

			// Added this as some sites where mb_detect_encoding is disabled will
			// produce a fatal error because /extlib/Stripe/init.php wasn't loaded.
			if ( class_exists( 'WLMStripe\WLM_Stripe' ) ) {
				WLMStripe\WLM_Stripe::setAppInfo(
					'WishList Member',
					$WishListMemberInstance->Version,
					'http://member.wishlistproducts.com/',
					'pp_partner_FlHHCjuMMJYOXI'
				);
			}

			add_action( 'edit_user_profile', array( $this, 'profile_form' ) );
			add_action( 'show_user_profile', array( $this, 'profile_form' ) );
			add_action( 'profile_update', array( $this, 'update_profile' ), 9, 2 );

			add_action( 'wishlistmember_cancel_user_levels', array( $this, 'cancel_stripe_subs_via_hook' ), 99, 2 );

			add_filter( 'wishlist_member_user_custom_fields', array( $this, 'add_stripe_field' ), 99, 2 );
			add_filter( 'wishlistmember_post_update_user', array( $this, 'save_stripe_field' ), 99, 1 );

			add_action( 'admin_notices', array( $this, 'notices' ) );

			add_action( 'wp_footer', array( $this, 'footer' ) );

			$WishListMemberInstance->tinymce_lightbox_files[] = $this->get_view_path( 'tinymce_lightbox' );

			add_shortcode( 'wlm_stripe_btn', array( $this, 'wlm_stripe_btn' ) );
			add_shortcode( 'wlm_stripe_profile', array( $this, 'wlm_stripe_profile' ) );
			
			add_filter( 'wishlistmember_integration_shortcodes', function( $shortcodes ) {
				$levels = array();
				foreach( \WishListMember\Level::get_all_levels( true ) AS $level ) {
					$levels[$level->ID] = array( 'label' => $level->name );
				}
				$wlm_shortcodes = array();
				
				// paymemt buttons
				$str = __( ' Registration Button', 'wishlist-member' );
				foreach ( \WishListMember\Level::get_all_levels( true ) AS $l ) {
					$wlm_shortcodes[sprintf( 'wlm_stripe_btn sku=%s button_label="" pay_button_label="" coupon="1"', $l->ID, $l->name )] = array(
						'label' => $l->name . $str,
					);
				}

				// profile shortcode
				$wlm_shortcodes['wlm_stripe_profile'] = array(
					'label' => __( 'Profile Page', 'wishlist-member' ),
					'attributes' => array(
						'levels' => array(
							'columns' => 3,
							'type' => 'checkbox',
							'options' => array(
								'all' => array(
									'label' => __( 'Membership Levels', 'wishlist-member' ),
									'unchecked' => 'no',
								),
							),
							'default' => 'all',
						),
						'level-choices' => array(
							'columns' => 9,
							'type' => 'select-multiple',
							'separator' => ',',
							'options' => $levels,
							'placeholder' => __( 'All Levels', 'wishlist-member' ),
							'dependency' => '[name="levels"]:checked',
						),
						'include_posts' => array(
							'type' => 'checkbox',
							'options' => array(
								'yes' => array(
									'label' => __( 'Include Pay-Per-Posts', 'wishlist-member' ),
									'unchecked' => 'no',
								),
							),
							'default' => 'no,'
						)
					)
				);
				
				$shortcodes['Stripe Integration'] = $wlm_shortcodes;

				return $shortcodes;
			} );

		}

		/**
		 * Cancel the user's Stripe Subscription when their membership level gets cancelled in WLM
		 *
		 * @param array   $level_ids -  SKUs of the membership level
		 * @param integer $user_id - User ID of the member that was cancelled.
		 */
		public function cancel_stripe_subs_via_hook( $user_id, $level_ids ) {

			global $WishListMemberInstance;

			$stripeapikey   = $WishListMemberInstance->GetOption( 'stripeapikey' );
			$stripesettings = $WishListMemberInstance->GetOption( 'stripesettings' );
			$connections    = $WishListMemberInstance->GetOption( 'stripeconnections' );
			WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

			foreach ( $level_ids as $level_id ) {
				$stripe_cust_id = $WishListMemberInstance->Get_UserMeta( $user_id, 'stripe_cust_id' );

				if ( empty( $stripe_cust_id ) ) {
					continue;
				}

				try {
					$stripe_level_settings = $connections[ $level_id ];

					// If Level is not a Subscription skip it.
					if ( empty( $stripe_level_settings['subscription'] ) ) {
						continue;
					}

					if ( empty( $stripe_level_settings['cancel_subs_if_cancelled_in_wlm'] ) ) {
						continue;
					}

					$cust = WLMStripe\Customer::retrieve( $stripe_cust_id );
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

					$txn_id = $WishListMemberInstance->GetMembershipLevelsTxnID( $user_id, $level_id );

					// Check if customer has more than 1 subscription, if so then get the
					// subscription ID and only cancel the subscription that matches the STRIPE PLAN
					// passed in the $_POST data
					if ( count( $cust->subscriptions->data ) > 1 ) {
						list($c_id, $plan_id) = explode( '-', $txn_id );
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
							$sub_id       = $cust->subscriptions->data[0]->id;
							$subscription = WLMStripe\Subscription::retrieve( $sub_id );
							$subscription->cancel();
						}
					}
				} catch ( Exception $e ) {

				}
			}
		}

		public function get_view_path( $handle ) {
			global $WishListMemberInstance;
			return sprintf( $WishListMemberInstance->pluginDir . '/extlib/wlm_stripe/%s.php', $handle );
		}
		function add_stripe_field( $custom_fields, $userid ) {
			global $WishListMemberInstance;
			if ( ! isset( $WishListMemberInstance ) ) {
				return $custom_fields;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				return $custom_fields; }

			$stripeapikey         = trim( $WishListMemberInstance->GetOption( 'stripeapikey' ) );
			$stripepublishablekey = trim( $WishListMemberInstance->GetOption( 'stripepublishablekey' ) );
			if ( empty( $stripeapikey ) && empty( $stripeapikey ) ) {
				return $custom_fields;
			}

			$stripe_cust_id = $WishListMemberInstance->Get_UserMeta( $userid, 'stripe_cust_id' );

			$custom_fields['stripe_cust_id'] = array(
				'type'       => 'text', // hidden, select, textarea, checkbox, etc
				'label'      => 'Stripe Customer ID',
				// 'description' => 'Description',
				'attributes' => array(
					'type'  => 'text', // hidden, select, textarea, checkbox, etc
					'name'  => 'stripe_cust_id', // same as index above
					// 'other attributes' => 'value',
					'value' => $stripe_cust_id,
					// more attributes if needed
				),
			);
			return $custom_fields;
		}

		function save_stripe_field( $data ) {
			global $WishListMemberInstance;
			if ( ! isset( $WishListMemberInstance ) ) {
				return;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				return; }
			if ( ! isset( $data['userid'] ) ) {
				return;
			}

			$user_custom_fields = isset( $data['customfields'] ) ? $data['customfields'] : array();
			if ( ! isset( $user_custom_fields['stripe_cust_id'] ) ) {
				return;
			}
			$stripe_cust_id = $user_custom_fields['stripe_cust_id'] ? trim( $user_custom_fields['stripe_cust_id'] ) : '';

			$WishListMemberInstance->Update_UserMeta( $data['userid'], 'stripe_cust_id', $stripe_cust_id );
		}
		public function profile_form( $user ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$user_id = $user;
			if ( is_object( $user ) ) {
				$user_id = $user->ID;
			}

			global $WishListMemberInstance;
			global $pagenow;

			$stripeapikey         = trim( $WishListMemberInstance->GetOption( 'stripeapikey' ) );
			$stripepublishablekey = trim( $WishListMemberInstance->GetOption( 'stripepublishablekey' ) );

			if ( empty( $stripeapikey ) && empty( $stripeapikey ) ) {
				return;
			}

			if ( $pagenow == 'profile.php' || $pagenow == 'user-edit.php' ) {
				$stripe_cust_id = $WishListMemberInstance->Get_UserMeta( $user_id, 'stripe_cust_id' );
				include $this->get_view_path( 'stripe_user_profile' );
			}
		}
		public function update_profile( $user ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$user_id = $user;
			if ( is_object( $user ) ) {
				$user_id = $user->ID;
			}

			global $WishListMemberInstance;
			if ( isset( $_POST['stripe_cust_id'] ) ) {
				$WishListMemberInstance->Update_UserMeta( $user_id, 'stripe_cust_id', trim( $_POST['stripe_cust_id'] ) );
			}
		}
		public function notices() {
			if ( extension_loaded( 'curl' ) ) {
				return;
			}

			if ( $_GET['page'] == 'WishListMember' && $_GET['wl'] == 'integration' ) {
				$msg  = '<div class="error fade"><p>';
				$msg .= __( '<strong>WishList Member Notice:</strong> The <strong>Stripe</strong> integration will not work properly. Please enable <strong>Curl</strong>.', 'wishlist-member' );
				$msg .= '</p></div>';
				echo $msg;
			}
		}



		public function wlm_stripe_btn( $atts, $content ) {
			$form = new WLM_Stripe_Forms();
			return $form->generate_stripe_form( $atts, $content );
		}
		public function footer() {
			global $WishListMemberInstance;
			if ( isset( $WishListMemberInstance ) ) {
				$stripethankyou = $WishListMemberInstance->GetOption( 'stripethankyou' );
			}

			$stripethankyou_url = $WishListMemberInstance->make_thankyou_url( $stripethankyou );

			$wlmstripevars['cancelmessage']      = __( 'Are you sure you want to cancel your subscription?', 'wishlist-member' );
			$wlmstripevars['nonceinvoices']      = wp_create_nonce( 'stripe-do-invoices' );
			$wlmstripevars['nonceinvoicedetail'] = wp_create_nonce( 'stripe-do-invoice' );
			$wlmstripevars['noncecoupon']        = wp_create_nonce( 'stripe-do-check_coupon' );
			$wlmstripevars['noncecoupondetail']  = wp_create_nonce( 'stripe-do-get_coupon' );
			$wlmstripevars['stripethankyouurl']  = $stripethankyou_url;
			?>
			<script type="text/javascript">
				function get_stripe_vars() {
					return eval( '(' + '<?php echo json_encode( $wlmstripevars ); ?>' +')');
				}
			</script>
			<?php
		}

		public function wlm_stripe_profile( $atts ) {
			ob_start();
			global $WishListMemberInstance;
			global $current_user;

			$stripepublishablekey = trim( $WishListMemberInstance->GetOption( 'stripepublishablekey' ) );
			$stripethankyou       = $WishListMemberInstance->GetOption( 'stripethankyou' );
			$stripethankyou_url   = $WishListMemberInstance->make_thankyou_url( $stripethankyou );

			if ( empty( $current_user->ID ) ) {
				return null;
			}

			$default_atts = array(
				'levels'             => '',
				'include_posts'      => 'yes',
				'hide_cancel_button' => 'no',
			);
			$atts         = shortcode_atts( $default_atts, $atts );
			$mlevels      = $atts['levels'] == '' ? 'all' : $atts['levels'];
			$mlevels      = $mlevels != 'no' ? ( $mlevels != 'all' ? explode( ',', $mlevels ) : $mlevels ) : 'no';
			$ppost        = $atts['include_posts'] != 'no' ? 'yes' : 'no';

			wp_enqueue_style( 'wlm-stripe-profile-style', $WishListMemberInstance->pluginURL . '/extlib/wlm_stripe/css/stripe-profile.css', '', $WishListMemberInstance->Version );
			wp_enqueue_style( 'stripe-paymenttag-style', $WishListMemberInstance->pluginURL . '/extlib/wlm_stripe/css/stripe-paymenttag.css', '', $WishListMemberInstance->Version );
			wp_enqueue_script( 'stripe-paymenttag', $WishListMemberInstance->pluginURL . '/extlib/wlm_stripe/js/stripe-paymenttag.js', array( 'jquery' ), $WishListMemberInstance->Version, true );
			wp_enqueue_script( 'leanModal', $WishListMemberInstance->pluginURL . '/extlib/wlm_stripe/js/jquery.leanModal.min.js', array( 'jquery' ), $WishListMemberInstance->Version, true );
			wp_enqueue_script( 'wlm-stripe-profile', $WishListMemberInstance->pluginURL . '/extlib/wlm_stripe/js/stripe.wlmprofile.js', array( 'stripe-paymenttag', 'leanModal' ), $WishListMemberInstance->Version, true );

			$levels     = $WishListMemberInstance->GetMembershipLevels( $current_user->ID, null, null, null, true );
			$wpm_levels = $WishListMemberInstance->GetOption( 'wpm_levels' );
			$user_posts = $WishListMemberInstance->GetUser_PayPerPost( 'U-' . $current_user->ID );

			$stripeapikey = $WishListMemberInstance->GetOption( 'stripeapikey' );
			WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );

			$stripe_cust_id = $WishListMemberInstance->Get_UserMeta( $current_user->ID, 'stripe_cust_id' );
			if ( ! empty( $stripe_cust_id ) ) {
				try {
					$cust   = WLMStripe\Customer::retrieve( $stripe_cust_id );
					if ( !$cust->subscriptions ) {						
						$cust = WLMStripe\Customer::retrieve(
							array(
							    'id'        => $stripe_cust_id,
							    'expand'    => array( 'subscriptions' ),
							)
					 	);					 	
					}
				} catch ( Exception $e ) {
					echo '<span class="stripe-error">' . $e->getMessage() . '</span>';
				}
			}
			$txnids = array();

			if ( $mlevels != 'no' ) {
				foreach ( $wpm_levels as $id => $level ) {
					if ( $mlevels != 'all' && ! in_array( $id, (array) $mlevels ) ) {
						continue;
					}
					$txn = $WishListMemberInstance->GetMembershipLevelsTxnID( $current_user->ID, $id );
					if ( empty( $txn ) ) {
						continue;
					}

					$subs_end_msg         = __( 'Access to Level Ends: ', 'wishlist-member' );
					$payment_canceled_msg = __( 'Payment Subscription Cancelled:', 'wishlist-member' );

					if ( strpos( $txn, 'cus_' ) === false ) {
						$txnids[ $id ]['stripe_connected'] = false;
					}

					if ( ! empty( $cust ) ) {
						if ( count( $cust->subscriptions->data ) > 1 ) {
							list($c_id, $plan_id) = explode( '-', $txn, 2 );
							foreach ( $cust->subscriptions->data as $d ) {
								if ( $d->plan->id == $plan_id ) {
									$txnids[ $id ]['stripe_connected'] = true;
									if ( $d->cancel_at ) {
										$txnids[ $id ]['subs_cancelled'] = true;

										$subs_end_date         = date_i18n( get_option( 'date_format' ), $d->cancel_at + $WishListMemberInstance->GMT );
										$payment_canceled_date = date_i18n( get_option( 'date_format' ), $d->canceled_at + $WishListMemberInstance->GMT );

										$txnids[ $id ]['subs_cancelled_msg']  = $payment_canceled_msg . $payment_canceled_date . '<br>';
										$txnids[ $id ]['subs_cancelled_msg'] .= $subs_end_msg . $subs_end_date;
									}
								}
							}
						} else {
							// If subscriptions is empty then this might be a one time purchase
							if ( count( $cust->subscriptions->data ) ) {
								list($c_id, $plan_id) = explode( '-', $txn, 2 );

								if ( $cust->subscriptions->data[0]->plan->id == $plan_id ) {
									$txnids[ $id ]['stripe_connected'] = true;
								} else {
									$txnids[ $id ]['stripe_connected'] = false;
								}
							} else {
								$txnids[ $id ]['stripe_connected'] = false;
							}

							$sub_id = $cust->subscriptions->data[0]->cancel_at;
							if ( $cust->subscriptions->data[0]->cancel_at && $cust->subscriptions->data[0]->plan->id == $plan_id ) {
								$txnids[ $id ]['subs_cancelled'] = true;

								$subs_end_date         = date_i18n( get_option( 'date_format' ), $cust->subscriptions->data[0]->cancel_at + $WishListMemberInstance->GMT );
								$payment_canceled_date = date_i18n( get_option( 'date_format' ), $cust->subscriptions->data[0]->canceled_at + $WishListMemberInstance->GMT );

								$txnids[ $id ]['subs_cancelled_msg']  = $payment_canceled_msg . $payment_canceled_date . '<br>';
								$txnids[ $id ]['subs_cancelled_msg'] .= $subs_end_msg . $subs_end_date;
							}
						}
					}

					$txnids[ $id ]['hide_cancel_button'] = $atts['hide_cancel_button'];
					$txnids[ $id ]['txn']                = $txn;
					$txnids[ $id ]['level']              = $level;
					$txnids[ $id ]['level_id']           = $id;
					$txnids[ $id ]['type']               = 'membership';
					?>
			<script src="https://js.stripe.com/v3/"></script>
			<script type="text/javascript">
				
			var stripe = Stripe('<?php echo $stripepublishablekey; ?>');
			var stripe_profile_button_status=true;
			jQuery(function($) {
					<?php
					foreach ( $txnids as $txnid ) {
						$txn = str_replace( '-', '', $txnids[ $id ]['level_id'] );
						echo <<<str
				
					var profile_elements = stripe.elements();
					var style = {
					  base: {
					    color: '#32325d',
					    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
					    fontSmoothing: 'antialiased',
					    fontSize: '16px',
					    '::placeholder': {
					      color: '#aab7c4'
					    }
					  },
					  invalid: {
					    color: '#fa755a',
					    iconColor: '#fa755a'
					  }
					};

					var card$txn = profile_elements.create('card', {style: style});

					card$txn.mount('#profile-card-element-$txn');
					
					card$txn.addEventListener('change', function(event) {
					  var displayError = document.getElementById('profile-card-errors-$txn');
					  if (event.error) {
					    displayError.textContent = event.error.message;
					    displayError.style.display = "block"; 
					  } else {
					    displayError.textContent = '';
					    displayError.style.display = "none"; 
					  }
					});

					$("#profile-form-credit-$txn").click(function( event ) {

							var cardData = { 
								  name: "$current_user->display_name",
								  email: "$current_user->user_email"
							};

							stripe.createToken(card$txn, cardData).then(function(result) {
							    if (result.error) {
							      var errorElement = document.getElementById('profile-card-errors-$txn');
							      errorElement.textContent = result.error.message;
							     	ui.find('.profile-card-errors-$txn').html( '<p>' + result.error.message  + '</p>');
									event.preventDefault();
							    } else {
							      	var token = result.token.id;
									$("#profile-form-credit-$txn").append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
									if ( stripe_profile_button_status == true) {
										stripe_profile_button_status = false;
										$("#profile-form-credit-$txn").submit();
									}
							    }
						 	});
							return false;
					});
str;
					}
					?>
					}); 
					</script>
					<?php
				}
			}

			if ( $ppost == 'yes' ) {
				foreach ( $user_posts as $u ) {
					$p                         = get_post( $u->content_id );
					$id                        = 'payperpost-' . $u->content_id;
					$txn                       = $WishListMemberInstance->Get_ContentLevelMeta( 'U-' . $current_user->ID, $u->content_id, 'transaction_id' );
					$txnids[ $id ]['txn']      = $txn;
					$txnids[ $id ]['level_id'] = $id;
					$txnids[ $id ]['type']     = 'post';
					$txnids[ $id ]['level']    = array(
						'name' => $p->post_title,
					);
				}
			}

			$wlm_user = new \WishListMember\User( $current_user->ID );
			?>
			<?php if ( isset( $_GET['status'] ) ) : ?>
				<?php if ( wlm_arrval( $_GET, 'status' ) == 'ok' ) : ?>
					<p><span class="stripe-success"><?php _e( 'Profile Updated', 'wishlist-member' ); ?></span></p>
				<?php else : ?>
					<span class="stripe-error"><?php _e( 'Unable to update your profile, please try again', 'wishlist-member' ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
			<?php
			include $this->get_view_path( 'profile' );
			$str = ob_get_clean();
			$str = preg_replace( '/\s+/', ' ', $str );
			return $str;

		}
	}
}
if ( ! class_exists( 'WLM_Stripe_Forms' ) ) {

	class WLM_Stripe_Forms {
		protected $forms;
		public function get_view_path( $handle ) {
			global $WishListMemberInstance;
			return sprintf( $WishListMemberInstance->pluginDir . '/extlib/wlm_stripe/%s.php', $handle );
		}
		public function footer() {
			global $current_user;
			global $WishListMemberInstance;

			$stripepublishablekey = trim( $WishListMemberInstance->GetOption( 'stripepublishablekey' ) );
			$skus                 = array_keys( $this->forms );
			$stripe_cust_id       = $WishListMemberInstance->Get_UserMeta( $current_user->ID, 'stripe_cust_id' );

			if ( ! empty( $stripe_cust_id ) ) {
				try {
					WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );
					$cust = WLMStripe\Customer::retrieve( $stripe_cust_id );

					if ( ! empty( $cust ) ) {
						$data['sc_details']['stripe_payment_method_id'] = $cust->invoice_settings->default_payment_method;
					}
				} catch ( Exception $e ) {

				}
			}

			foreach ( $this->forms as $frm ) {
				echo $frm;
			}

			?>
<script type="text/javascript">
	
var stripe = Stripe('<?php echo $stripepublishablekey; ?>');
var stripe_payment_button_status=true;

jQuery(function($) {
			<?php

			foreach ( $skus as $sku ) {
				$unedited_sku = $sku;
				$sku          = str_replace( '-', '', $sku );
				echo <<<str

var elements = stripe.elements();
var style = {
  base: {
    color: '#32325d',
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSmoothing: 'antialiased',
    fontSize: '16px',
    '::placeholder': {
      color: '#aab7c4'
    }
  },
  invalid: {
    color: '#fa755a',
    iconColor: '#fa755a'
  }
};

var card$sku = elements.create('card', {style: style});

card$sku.mount('#card-element-$sku');

// Handle real-time validation errors from the card Element.
card$sku.addEventListener('change', function(event) {
  var displayError = document.getElementById('card-errors-$sku');
  if (event.error) {
    displayError.textContent = event.error.message;
    displayError.style.display = "block"; 
  } else {
    displayError.textContent = '';
    displayError.style.display = "none"; 
  }
});

str;

				if ( is_user_logged_in() ) {
					if ( ! empty( $cust->invoice_settings->default_payment_method ) ) {
						// User has Payment Method ID so skip all validation..
						echo <<<str
						$('#regform-$sku .regform-form').PopupRegForm({
							skip_all_validations: true
						});
str;
					} else {
						// User logged in doesn't have Stripe Payment Method ID therefore
						// Do card validation but skip fname, lname validation
						echo <<<str
						$('#regform-$unedited_sku .regform-form').PopupRegForm({
							validate_last_name: false,
							validate_first_name: false,
							validate_cvc: false,
							validate_exp: false,
							validate_ccnumber: false,
							on_validate_success: function(form, fields, ui) {
								ui.find('.regform-waiting').show();

								var cardData = { 
								  name: fields.first_name.val() + " " + fields.last_name.val(),
								  email: fields.email.val()
								}

								stripe.createToken(card$sku, cardData).then(function(result) {
								    if (result.error) {
								      // Inform the user if there was an error.
								      var errorElement = document.getElementById('card-errors-$sku');
								      errorElement.textContent = result.error.message;

								      	// From old
								     	ui.find('#card-errors-$sku').html( '<p>' + result.error.message  + '</p>').show();
										form.find('.regform-button').prop("disabled", false);
										form.find('.regform-waiting').hide();
								    } else {
								      	var token = result.token.id;
										// insert the token into the form so it gets submitted to the server
										form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");

										if ( stripe_payment_button_status == true) {
											stripe_payment_button_status = false;
											form.submit();
										}
								    }
							 	});
								return false;
							}
						});
str;
					}
				} else {
					// User is not logged in so do all validations.
					echo <<<str
					$('#regform-$unedited_sku .regform-form').PopupRegForm({
						validate_cvc: false,
						validate_exp: false,
						validate_ccnumber: false,
						on_validate_success: function(form, fields, ui) {
							ui.find('.regform-waiting').show();

							var cardData = { 
							  name: fields.first_name.val() + " " + fields.last_name.val(),
							  email: fields.email.val()
							}

							stripe.createToken(card$sku, cardData).then(function(result) {
							    if (result.error) {
							      // Inform the user if there was an error.
							      var errorElement = document.getElementById('card-errors-$sku');
							      errorElement.textContent = result.error.message;

							      	// From old
							     	ui.find('#card-errors-$sku').html( '<p>' + result.error.message  + '</p>').show();
									form.find('.regform-button').prop("disabled", false);
									form.find('.regform-waiting').hide();
							    } else {
							      	var token = result.token.id;
									// insert the token into the form so it gets submitted to the server
									form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");

									if ( stripe_payment_button_status == true) {
										stripe_payment_button_status = false;
										form.submit();
									}
							    }
						 	});
							return false;
						}
					});
str;
				}
			}
			?>
});
</script>
			<?php
		}
		public function load_popup() {
			global $WishListMemberInstance;
			wp_enqueue_script( 'wlm-jquery-fancybox' );
			wp_enqueue_style( 'wlm-jquery-fancybox' );

			wp_enqueue_script( 'wlm-popup-regform-stripev3' );
			wp_enqueue_style( 'wlm-popup-regform-style' );

		}

		public function generate_stripe_form( $atts, $content ) {
			global $WishListMemberInstance;
			$this->load_popup();
			add_action( 'wp_footer', array( $this, 'footer' ), 100 );

			global $current_user;
			extract(
				shortcode_atts(
					array(
						'sku'                  => null,
						'amount'               => 0,
						'currency'             => '',
						'coupon'               => 1,
						'showlogin'            => 1,
						'button_label'         => '',
						'pay_button_label'     => '',
						'hide_button_currency' => 0,
						'class'                => null,
					),
					$atts
				)
			);

			if ( empty( $sku ) ) {
				return null;
			}
			$amount   = $amount ? (float) $amount : 0;
			$currency = $currency ? $currency : '';
			$coupon   = (int) $coupon;
			$btn_hash = false;

			$stripeapikey       = $WishListMemberInstance->GetOption( 'stripeapikey' );
			$stripeconnections  = $WishListMemberInstance->GetOption( 'stripeconnections' );
			$stripethankyou     = $WishListMemberInstance->GetOption( 'stripethankyou' );
			$stripethankyou_url = $WishListMemberInstance->make_thankyou_url( $stripethankyou );
			$stripesettings     = $WishListMemberInstance->GetOption( 'stripesettings' );
			$wpm_levels         = $WishListMemberInstance->GetOption( 'wpm_levels' );
			$WishListMemberInstance->InjectPPPSettings( $wpm_levels );

			// settings
			$settings = $stripeconnections[ $sku ];
			$amt      = $settings['amount'];

			if ( empty( $currency ) ) {
				$cur = empty( $stripesettings['currency'] ) ? 'USD' : $stripesettings['currency'];
			} else {
				$cur = $currency;
			}

			if ( $settings['subscription'] ) {
				try {
					WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );
					$plan  = array( WLMStripe\Price::retrieve( $settings['plan'] ) );
					$amt   = number_format( $plan->amount / 100, 2, '.', '' );
					$plans = json_decode( stripslashes( wlm_arrval( $settings, 'plans' ) ) );
					if ( is_array( $plans ) ) {
						foreach ( $plans as $xplan ) {
							$xplan = trim( $xplan );
							if ( ! $xplan ) {
									  continue;
							}
							$plan[] = WLMStripe\Price::retrieve( $xplan );
						}
					}
				} catch ( Exception $e ) {
					$msg = __( 'Error %s', 'wishlist-member' );
					return sprintf( $msg, $e->getMessage() );
				}
			} else {
				// override by shorcode attribute
				if ( $amount || $currency ) {
					$btn_hash = true; // lets check if this need hash
				}
				$amt      = $amount ? $amount : $amt;
				$currency = $currency ? $currency : $cur;
				if ( $btn_hash ) {
					$btn_hash = "{$stripeapikey}-{$amt}-{$currency}";
				}
				$coupon = false; // disable coupon for one time payments.
			}

			$ppp_level  = $WishListMemberInstance->IsPPPLevel( $sku );
			$level_name = $wpm_levels[ $sku ]['name'];

			if ( $ppp_level ) {
				$level_name = $ppp_level->post_title;
			}

			$heading = empty( $stripesettings['formheading'] ) ? 'Register for %level' : $stripesettings['formheading'];
			$heading = str_replace( '%level', $level_name, $heading );

			if ( empty( $button_label ) ) {
				$btn_label = empty( $stripesettings['buttonlabel'] ) ? 'Join %level' : $stripesettings['buttonlabel'];
			} else {
				$btn_label = $button_label;
			}
			$btn_label = str_replace( '%level', $level_name, $btn_label );

			if ( empty( $pay_button_label ) ) {
				$panel_btn_label = empty( $stripesettings['panelbuttonlabel'] ) ? 'Pay' : $stripesettings['panelbuttonlabel'];
			} else {
				$panel_btn_label = $pay_button_label;
			}
			$panel_btn_label = stripslashes( str_replace( '%level', $level_name, $panel_btn_label ) );
			$logo            = $stripesettings['logo'];
			$logo            = str_replace( '%level', $level_name, $stripesettings['logo'] );
			$content         = trim( $content );
			ob_start();
			?>

			<?php if ( empty( $content ) ) : ?>
				<button class="regform-button go-regform <?php echo $class; ?>" name="go_regform" value="<?php echo $amt; ?>" style="width: auto" id="go-regform-<?php echo $sku; ?>" class="" href="#regform-<?php echo $sku; ?>"><?php echo stripslashes( $btn_label ); ?></button>
			<?php else : ?>
				<span>
					<a data-fancybox data-options='{"src" : "#regform-<?php echo $sku; ?>" }' href="javascript:;" id="go-regform-<?php echo $sku; ?>" name="href_go_regform" class="go-regform" ><?php echo $content; ?></a>
					<input type="hidden" class="go-regform-hidden" name="go-regform-hidden" value="<?php echo $amt; ?>">
				</span>
			<?php endif; ?>

			<?php $btn = ob_get_clean(); ?>

			<?php
			$additional_class = 'regform-stripe';
			if ( ! $coupon ) {
				$additional_class .= ' nocoupon';
			}
			if ( $hide_button_currency > 0 ) {
				$additional_class .= ' hide-button-currency';
			}

			// Data to be use to edit the pay button and the amount description
			$data['sc']         = 'stripe';
			$data['sc_details'] = array(
				'sku'             => $atts['sku'],
				'is_subscription' => $settings['subscription'],
				'amt'             => $amt,
				'currency'        => $cur,
				'plan_details'    => $plan,
				'panel_btn_label' => $panel_btn_label,
			);

			if ( ! is_user_logged_in() ) {
				$path = sprintf( $WishListMemberInstance->pluginDir . '/extlib/wlm_stripe/form_new_fields.php' );
				include $path;
				$this->forms[ $sku ] = wlm_build_payment_form( $data, $additional_class );
			} else {
				$stripe_cust_id = $WishListMemberInstance->Get_UserMeta( $current_user->ID, 'stripe_cust_id' );

				if ( ! empty( $stripe_cust_id ) ) {

					try {
						WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );
						$cust = WLMStripe\Customer::retrieve( $stripe_cust_id );

						if ( ! empty( $cust ) ) {
							$data['sc_details']['stripe_customer_id']       = $stripe_cust_id;
							$data['sc_details']['stripe_payment_method_id'] = $cust->invoice_settings->default_payment_method;

							$payment_method                          = WLMStripe\PaymentMethod::retrieve( $cust->invoice_settings->default_payment_method );
							$data['sc_details']['stripe_card_last4'] = $payment_method->card->last4;
							$data['sc_details']['stripe_card_brand'] = $payment_method->card->brand;

						}
					} catch ( Exception $e ) {

					}
				}

				$path = sprintf( $WishListMemberInstance->pluginDir . '/extlib/wlm_stripe/form_existing_fields.php' );
				include $path;
				$this->forms[ $sku ] = wlm_build_payment_form( $data, $additional_class );
			}
			return $btn;
		}

	}

}

$sc = new WLM_Stripe_ShortCodes();

?>
