<?php

class SiteOrigin_Premium_WooCommerce_Output_Additional_Information extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-output-additional-information',
			__( 'Product additional information', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the product weight, dimensions, and attributes, if available.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		global $product;
		echo $args['before_widget'];
		do_action( 'woocommerce_product_additional_information', $product );
		echo $args['after_widget'];
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Output_Additional_Information' );
