<?php

class SiteOrigin_Premium_WooCommerce_Cart_Empty_Return_To_Shop extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-cart-empty-return-to-shop',
			__( 'Cart empty - Return to shop', 'siteorigin-premium' ),
			array( 'description' => __( 'Display return to shop button.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
			<p class="return-to-shop">
				<a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
					<?php esc_html_e( 'Return to shop', 'woocommerce' ); ?>
				</a>
			</p>
		<?php endif;
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Cart_Empty_Return_To_Shop' );
