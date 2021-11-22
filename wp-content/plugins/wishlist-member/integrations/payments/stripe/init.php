<?php // initialization

if ( ! class_exists( 'WLM3_Stripe_Hooks' ) ) {
	class WLM3_Stripe_Hooks {
		const MAX_PLAN_COUNT = 999;
		const MAX_PROD_COUNT = 999;
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_stripe_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data = array(
				'status'  => false,
				'message' => '',
			);
			if ( ! empty( $stripeapikey ) ) {
				try {

					$status    = WLMStripe\WLM_Stripe::setApiKey( $stripeapikey );
					$plans     = WLMStripe\Price::all( array( 'count' => self::MAX_PLAN_COUNT ) );
					$_products = WLMStripe\Product::all( array( 'count' => self::MAX_PROD_COUNT ) );
					$products  = array();
					foreach ( $_products->data as $product ) {
						$products[ $product->id ] = $product->name;
					}

					$api_type        = strpos( $stripeapikey, 'test' ) === false ? 'LIVE' : 'TEST';
					$data['message'] = $api_type;
					$data['status']  = true;

					$data['data']['plan_options'] = array();
					foreach ( $plans->data as $plan ) {
						if($plan->recurring) {
							$interval = $plan->recurring->interval;
							if ( $plan->recurring->interval_count <> 1 ) {
								$interval = sprintf( '%d %ss', $plan->recurring->interval_count, $interval );
							}
						} else {
							$interval = __( 'One time', 'wishlist-member' );
						}
						$text = sprintf( '%s - %s (%s %s / %s)', $products[ $plan->product ], $plan->nickname ?: $plan->id, strtoupper( $plan->currency ), number_format( $plan->unit_amount / 100, 2, '.', ',' ), $interval );

						// @since 3.6 create optgroup for select2
						if ( ! isset( $data['data']['plan_options'][ $plan->product ] ) ) {
							$data['data']['plan_options'][ $plan->product ] = array(
								'text'     => $products[ $plan->product ],
								'children' => array(),
							);
						}
						// @since 3.6 add plans to correct group
						$data['data']['plan_options'][ $plan->product ]['children'][] = array(
							'value' => $plan->id,
							'id'    => $plan->id,
							'text'  => $text,
						);
					}
					// @since 3.6 remove keys from optgroup as select2 wants an array
					$data['data']['plan_options'] = array_values( $data['data']['plan_options'] );

					$data['data']['plans'] = $plans;

					if ( $save ) {
						$this->wlm->SaveOption( 'stripeapikey', $stripeapikey );
						$this->wlm->SaveOption( 'stripepublishablekey', $stripepublishablekey );
					}
				} catch ( Exception $e ) {
					$data['message'] = $e->getMessage();
				}
			} else {
				$data['message'] = 'No Stripe Secret Key';
			}
			wp_die( json_encode( $data ) );
		}
	}
	new WLM3_Stripe_Hooks();
}
