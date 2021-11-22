<?php

class SiteOrigin_Premium_WooCommerce_Output_Product_Reviews extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-output-product-reviews',
			__( 'Product reviews', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the product review form and reviews.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( comments_open() ) {
			comments_template();
		}
		echo $args['after_widget'];
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Output_Product_Reviews' );
