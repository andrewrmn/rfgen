<?php

class SiteOrigin_Premium_WooCommerce_Checkout_Customer_Details extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-checkout-details',
			__( 'Checkout customer details', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the customer details form.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		$checkout = WC()->checkout();
		if ( $checkout->get_checkout_fields() ) {
			do_action( 'woocommerce_checkout_before_customer_details' );
			?>
			<div class="col2-set" id="customer_details">
				<div class="col-1">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</div>

				<div class="col-2">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
			</div><?php
			do_action( 'woocommerce_checkout_after_customer_details' );
		}
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Checkout_Customer_Details' );

