<?php

class SiteOrigin_Premium_WooCommerce_Template_Loop_Product_Thumbnail extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-template-loop-product-thumbnail',
			__( 'Product loop thumbnail', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the product thumbnail image.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		$sale_flash_enabled = isset( $instance['enable_sale_flash'] ) ? ! empty( $instance['enable_sale_flash'] ) : true;
		if ( function_exists( 'woocommerce_show_product_loop_sale_flash' ) ) {
			if ( $sale_flash_enabled ) {
				?><div style="position: relative;"><?php
				woocommerce_show_product_loop_sale_flash();
			}
		}
		if ( function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
			woocommerce_template_loop_product_link_open();
			woocommerce_template_loop_product_thumbnail();
			woocommerce_template_loop_product_link_close();
		}
		if ( $sale_flash_enabled ) {
			?></div><?php
		}
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$sale_flash_enabled = isset( $instance['enable_sale_flash']) ? ! empty( $instance['enable_sale_flash']) : true;
		$field_id = $this->get_field_id( 'enable_sale_flash' );
		$field_name = $this->get_field_name( 'enable_sale_flash' );
		?>
		<div class="so-wc-widget-form-input">
			<input
				type="checkbox"
				id="<?php echo esc_attr( $field_id ) ?>"
				name="<?php echo esc_attr( $field_name ) ?>"
				<?php checked( ! empty( $sale_flash_enabled ) )?>/>
			<label for="<?php echo esc_attr( $field_id ) ?>">
				<?php esc_html_e( 'Enable sale sticker', 'siteorigin-premium' ) ?>
			</label>
		</div>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		if ( ! isset( $new_instance['enable_sale_flash'] ) ) {
			$new_instance['enable_sale_flash'] = empty( $old_instance['enable_sale_flash'] );
		}
		return $new_instance;
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Template_Loop_Product_Thumbnail' );
