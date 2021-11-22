<?php

class SiteOrigin_Premium_WooCommerce_Upsell_Display extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-upsell-display',
			__( 'Product upsell display', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the upsell products.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( function_exists( 'woocommerce_upsell_display' ) ) {
			woocommerce_upsell_display();
		}
		echo $args['after_widget'];
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Upsell_Display' );
