<?php

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );
?>
	<header class="woocommerce-products-header">
		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
			<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
		<?php endif; ?>

		<?php do_action( 'woocommerce_archive_description' ); ?>
	</header>
<?php

// If the user has created and enabled a Shop Page Builder layout we load and render it here.

$so_wc_templates = get_option( 'so-wc-templates' );
$template_data = $so_wc_templates[ 'shop' ];

// Don't call `woocommerce_output_all_notices` here, as it should already be hooked into the
// `woocommerce_before_shop_loop` action called in the `wc-shop-product-loop` widget.
if ( ! empty( $template_data['post_id'] ) ) {
	SiteOrigin_Premium_Plugin_WooCommerce_Templates::single()->before_template_render();
	echo SiteOrigin_Panels_Renderer::single()->render( $template_data['post_id'] );
	SiteOrigin_Premium_Plugin_WooCommerce_Templates::single()->after_template_render();
}

do_action( 'woocommerce_after_main_content' );

do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
