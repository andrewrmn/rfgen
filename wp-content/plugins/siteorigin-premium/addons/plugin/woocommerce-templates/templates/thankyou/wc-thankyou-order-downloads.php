<?php

class SiteOrigin_Premium_WooCommerce_Thankyou_Order_Downloads extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'wc-thankyou-order-downloads',
			__( 'Thank you order downloads', 'siteorigin-premium' ),
			array(
				'description' => __( 'Displays order downloads.', 'siteorigin-premium' ),
			),
			array()
		);
	}
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$order = get_query_var( 'siteorigin-premium-wctb-order' );
		if ( ! empty( $order ) ) {
			$show_downloads = $order->has_downloadable_item() && $order->is_download_permitted();
			if ( $show_downloads ) {
				$downloads = $order->get_downloadable_items();
				wc_get_template(
					'order/order-downloads.php',
					array(
						'downloads'  => $downloads,
						'show_title' => true,
					)
				);
			}
		}
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Thankyou_Order_Downloads' );

