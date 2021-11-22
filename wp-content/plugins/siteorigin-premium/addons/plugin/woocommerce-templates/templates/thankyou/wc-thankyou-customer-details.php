<?php

class SiteOrigin_Premium_WooCommerce_Thankyou_Customer_Details extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'wc-thankyou-customer-details',
			__( 'Thank you customer details', 'siteorigin-premium' ),
			array(
				'description' => __( 'Displays customer details.', 'siteorigin-premium' ),
			),
			array()
		);
	}
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$order = get_query_var( 'siteorigin-premium-wctb-order' );
		if ( ! empty( $order ) && is_user_logged_in() && $order->get_user_id() === get_current_user_id() ) {
			wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
		}

		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Thankyou_Customer_Details' );

