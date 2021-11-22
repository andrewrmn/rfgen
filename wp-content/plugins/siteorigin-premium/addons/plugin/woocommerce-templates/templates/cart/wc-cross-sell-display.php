<?php

class SiteOrigin_Premium_WooCommerce_Cross_Sell_Display extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-cross-sell-display',
			__( 'Cart cross sell display', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the cart cross-sell products.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( function_exists( 'woocommerce_cross_sell_display' ) ) {
			woocommerce_cross_sell_display();
		}
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Cross_Sell_Display' );
