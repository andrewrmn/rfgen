<?php

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

?><li <?php wc_product_class( '', $product ); ?>><?php
// If the user has created and enabled a Product Archive Page Builder layout we load and render it here.

$so_wc_templates = get_option( 'so-wc-templates' );
$template_data = $so_wc_templates[ 'content-product' ];
if ( ! empty( $_GET['siteorigin_premium_template_preview'] ) ) {
	$template_post_id = $_POST['preview_template_post_id'];
} else {
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	if ( ! empty( $term ) && is_object( $term ) ) {
		$template_post_id = get_option( "_term_type_{$term->taxonomy}_{$term->term_id}" );
	}
}

if ( empty( $template_post_id ) && ! empty( $template_data['post_id'] ) ) {
	$template_post_id = $template_data['post_id'];
}

if ( ! empty( $template_post_id ) ) {
	echo SiteOrigin_Panels_Renderer::single()->render( $template_post_id );
}

?></li>
