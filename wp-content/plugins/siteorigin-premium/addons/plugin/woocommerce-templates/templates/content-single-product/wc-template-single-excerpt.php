<?php

class SiteOrigin_Premium_WooCommerce_Template_Single_Excerpt extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-template-single-excerpt',
			__( 'Product short description', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the product short description.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( function_exists( 'woocommerce_template_single_excerpt' ) ) {
			woocommerce_template_single_excerpt();
		}
		echo $args['after_widget'];
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Template_Single_Excerpt' );
