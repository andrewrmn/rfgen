<?php

class SiteOrigin_Premium_WooCommerce_Thankyou_Order_Details_Table extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'wc-thankyou-order-details-table',
			__( 'Thank you order details', 'siteorigin-premium' ),
			array(
				'description' => __( 'Displays a table of order details.', 'siteorigin-premium' ),
			),
			array()
		);
	}
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$order = get_query_var( 'siteorigin-premium-wctb-order' );
		if ( ! empty( $order ) && function_exists( 'woocommerce_order_details_table' ) ) {
			$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
			$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );

			?>
			<section class="woocommerce-order-details">
				<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

				<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'siteorigin-premium' ); ?></h2>

				<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

					<thead>
						<tr>
							<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'siteorigin-premium' ); ?></th>
							<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'siteorigin-premium' ); ?></th>
						</tr>
					</thead>

					<tbody>
						<?php
						do_action( 'woocommerce_order_details_before_order_table_items', $order );

						foreach ( $order_items as $item_id => $item ) {
							$product = $item->get_product();

							wc_get_template(
								'order/order-details-item.php',
								array(
									'order'              => $order,
									'item_id'            => $item_id,
									'item'               => $item,
									'show_purchase_note' => $show_purchase_note,
									'purchase_note'      => $product ? $product->get_purchase_note() : '',
									'product'            => $product,
								)
							);
						}

						do_action( 'woocommerce_order_details_after_order_table_items', $order );
						?>
					</tbody>

					<tfoot>
						<?php
						foreach ( $order->get_order_item_totals() as $key => $total ) {
							?>
								<tr>
									<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
									<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
								</tr>
								<?php
						}
						?>
						<?php if ( $order->get_customer_note() ) : ?>
							<tr>
								<th><?php esc_html_e( 'Note:', 'siteorigin-premium' ); ?></th>
								<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
							</tr>
						<?php endif; ?>
					</tfoot>
				</table>

				<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
			</section>

			<?php
			/**
			 * Action hook fired after the order details.
			 *
			 * @since 4.4.0
			 * @param WC_Order $order Order data.
			 */
			do_action( 'woocommerce_after_order_details', $order );
		}
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Thankyou_Order_Details_Table' );

