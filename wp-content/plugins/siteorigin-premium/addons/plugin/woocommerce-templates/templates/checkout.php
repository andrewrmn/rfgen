<?php
defined( 'ABSPATH' ) || exit;

/**
 * @var WC_Checkout $checkout
 */

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

// If the user has created and enabled a Checkout Form Page Builder layout we load and render it here.

$so_wc_templates = get_option( 'so-wc-templates' );
$template_data = $so_wc_templates[ 'checkout' ];

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
	<?php
		if ( ! empty( $template_data['post_id'] ) ) {
			// Don't call `woocommerce_output_all_notices` here, as they should already be hooked into the above
			// `woocommerce_before_checkout_form` action.
			SiteOrigin_Premium_Plugin_WooCommerce_Templates::single()->before_template_render();
			echo SiteOrigin_Panels_Renderer::single()->render( $template_data['post_id'] );
			SiteOrigin_Premium_Plugin_WooCommerce_Templates::single()->after_template_render();
		}
	?>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
