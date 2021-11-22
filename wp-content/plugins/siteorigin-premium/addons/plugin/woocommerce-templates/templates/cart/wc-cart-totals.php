<?php

class SiteOrigin_Premium_WooCommerce_Cart_Totals extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-cart-totals',
			__( 'Cart totals', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the cart totals and checkout button.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( function_exists( 'woocommerce_cart_totals' ) ) {
			woocommerce_cart_totals();
		}
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Cart_Totals' );
