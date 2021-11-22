<?php

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

// If the user has created and enabled a Cart Page Builder layout we load and render it here.

$so_wc_templates = get_option( 'so-wc-templates' );
$template_data = $so_wc_templates[ 'cart' ];

if ( ! empty( $template_data['post_id'] ) ) {
	// Don't call `woocommerce_output_all_notices` here, as they should already be hooked into the above
	// `woocommerce_before_cart` action.
	SiteOrigin_Premium_Plugin_WooCommerce_Templates::single()->before_template_render();
	echo SiteOrigin_Panels_Renderer::single()->render( $template_data['post_id'] );
	SiteOrigin_Premium_Plugin_WooCommerce_Templates::single()->after_template_render();
}

do_action( 'woocommerce_after_cart' );
