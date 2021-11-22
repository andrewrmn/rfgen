<?php

class SiteOrigin_Premium_WooCommerce_Output_Related_Products extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-output-related-products',
			__( 'Related products', 'siteorigin-premium' ),
			array( 'description' => __( 'Display related products.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( function_exists( 'woocommerce_output_related_products' ) ) {
			woocommerce_output_related_products();
		}
		echo $args['after_widget'];
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Output_Related_Products' );
