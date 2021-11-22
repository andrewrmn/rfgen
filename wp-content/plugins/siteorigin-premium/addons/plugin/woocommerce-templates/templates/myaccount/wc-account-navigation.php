<?php

class SiteOrigin_Premium_WooCommerce_Account_Navigation extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-account-navigation',
			__( 'Account navigation', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the account navigation links.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		do_action( 'woocommerce_account_navigation' );
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Account_Navigation' );

