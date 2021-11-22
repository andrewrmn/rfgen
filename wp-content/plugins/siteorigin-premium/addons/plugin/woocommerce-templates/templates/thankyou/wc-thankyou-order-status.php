<?php

class SiteOrigin_Premium_WooCommerce_Thankyou_Order_Status extends SiteOrigin_Widget {

	public function __construct() {
		parent::__construct(
			'wc-thankyou-order-status',
			__( 'Thank you order status', 'siteorigin-premium' ),
			array(
				'description' => __( 'Displays order status.', 'siteorigin-premium' ),
				'has_preview' => false,
			),
			array(),
			array(
				'success' => array(
					'type'   => 'section',
					'label'  => __( 'Success' , 'siteorigin-premium' ),
					'hide'   => true,
					'fields' => array(
						'message' => array(
							'type'    => 'tinymce',
							'label'   => __( 'Message', 'siteorigin-premium' ),
							'default' => __( 'Thank you. Your order has been received.', 'siteorigin-premium' ),
						),
					),
				),
				'failed' => array(
					'type'   => 'section',
					'label'  => __( 'Failed' , 'siteorigin-premium' ),
					'hide'   => true,
					'fields' => array(
						'message' => array(
							'type'    => 'tinymce',
							'label'   => __( 'Message', 'siteorigin-premium' ),
							'default' => __( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'siteorigin-premium' ),
						),
					),
				),
			)
		);
	}
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$order = get_query_var( 'siteorigin-premium-wctb-order' );
		if ( ! empty( $order ) ) {
			if ( $order->has_status( 'failed' ) ) {
				if ( ! empty( $instance['failed']['message'] ) ) :
					?>
					<div class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
						<?php echo $instance['failed']['message']; ?>
					</div>
				<?php endif; ?>

				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'siteorigin-premium' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'siteorigin-premium' ); ?></a>
					<?php endif; ?>
				</p>
				<?php
			} elseif ( ! empty( $instance['success']['message'] ) ) {
				?>
				<div class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
					<?php echo apply_filters( 'woocommerce_thankyou_order_received_text', $instance['success']['message'], $order ); ?>
				</div>
				<?php
			}
		}
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Thankyou_Order_Status' );
