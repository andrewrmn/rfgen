<?php

defined( 'ABSPATH' ) || exit;

// If the user has created and enabled a Thank You Page Builder layout we load and render it here.

$so_wc_templates = get_option( 'so-wc-templates' );
$template_data = $so_wc_templates[ 'thankyou' ];

if ( empty( $template_data['post_id'] ) ) {
	return;
}

if ( empty( get_query_var( 'order-received' ) ) ) {
	// Unable to detect order id.
	return;
}

$order = false;

// Get the order.
$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( get_query_var( 'order-received' ) ) );
$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( wp_unslash( $_GET['key'] ) ) ); // WPCS: input var ok, CSRF ok.

if ( $order_id > 0 ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || ! hash_equals( $order->get_order_key(), $order_key ) ) {
		$order = false;
	}
}

if ( empty( $order ) ) {
	// Was not able to detect order.
	return;
}

// Store Order for later use by Thank You widgets.
set_query_var( 'siteorigin-premium-wctb-order', $order );
?>
<div class="woocommerce-order">
<?php
	// Don't call `woocommerce_output_all_notices` here, as it should already be hooked into the
	// `woocommerce_account_content` action called in the `wc-account-content` widget.
	SiteOrigin_Premium_Plugin_WooCommerce_Templates::single()->before_template_render();
	echo SiteOrigin_Panels_Renderer::single()->render( $template_data['post_id'] );
	SiteOrigin_Premium_Plugin_WooCommerce_Templates::single()->after_template_render();
	?>
</div>
