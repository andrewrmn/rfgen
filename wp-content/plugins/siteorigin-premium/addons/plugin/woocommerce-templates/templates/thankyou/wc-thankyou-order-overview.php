<?php

class SiteOrigin_Premium_WooCommerce_Thankyou_Order_Overview extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'wc-thankyou-order-overview',
			__( 'Thank you order overview', 'siteorigin-premium' ),
			array(
				'description' => __( 'Displays an overview of the order.', 'siteorigin-premium' ),
			),
			array()
		);
	}
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$order = get_query_var( 'siteorigin-premium-wctb-order' );
		if ( ! empty( $order ) && ! $order->has_status( 'failed' ) ) :
			?>
			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

				<li class="woocommerce-order-overview__order order">
					<?php esc_html_e( 'Order number:', 'siteorigin-premium' ); ?>
					<strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<li class="woocommerce-order-overview__date date">
					<?php esc_html_e( 'Date:', 'siteorigin-premium' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php esc_html_e( 'Email:', 'siteorigin-premium' ); ?>
						<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</li>
				<?php endif; ?>

				<li class="woocommerce-order-overview__total total">
					<?php esc_html_e( 'Total:', 'siteorigin-premium' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php esc_html_e( 'Payment method:', 'siteorigin-premium' ); ?>
						<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>

			</ul>
			<?php
		endif;
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Thankyou_Order_Overview' );

