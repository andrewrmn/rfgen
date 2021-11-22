<?php

class SiteOrigin_Premium_WooCommerce_Shop_Product_Loop extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-shop-product-loop',
			__( 'Shop product loop', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the default product loop saved in the Product archive tab.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( woocommerce_product_loop() ) {

			/**
			 * Hook: woocommerce_before_shop_loop.
			 *
			 * @hooked woocommerce_output_all_notices - 10
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action( 'woocommerce_before_shop_loop' );

			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 *
					 * @hooked WC_Structured_Data::generate_product_data() - 10
					 */
					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action( 'woocommerce_no_products_found' );
		}
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Shop_Product_Loop' );
