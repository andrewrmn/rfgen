<?php

class SiteOrigin_Premium_WooCommerce_Template_Single_Rating extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-template-single-rating',
			__( 'Product rating', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the product rating.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( function_exists( 'woocommerce_template_single_rating' ) ) {
			woocommerce_template_single_rating();
		}
		echo $args['after_widget'];
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Template_Single_Rating' );
