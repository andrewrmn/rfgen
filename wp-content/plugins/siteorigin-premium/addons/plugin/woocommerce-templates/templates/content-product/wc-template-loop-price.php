<?php

class SiteOrigin_Premium_WooCommerce_Template_Loop_Price extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-template-loop-price',
			__( 'Product loop price', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the product price.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( function_exists( 'woocommerce_template_loop_price' ) ) {
			woocommerce_template_loop_price();
		}
		echo $args['after_widget'];
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Template_Loop_Price' );
