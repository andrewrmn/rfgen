<?php

class SiteOrigin_Premium_WooCommerce_Template_Single_Add_To_Cart extends WP_Widget {

	private $button_text;

	public function __construct() {
		parent::__construct(
			'so-wc-template-single-add-to-cart',
			__( 'Product "Add to cart" button', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the product Add to cart button.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( function_exists( 'woocommerce_template_single_add_to_cart' ) ) {
			if ( isset( $instance['add_to_cart_single'] ) ) {
				$this->button_text = $instance['add_to_cart_single'];
			}

			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'button_text' ) );
			woocommerce_template_single_add_to_cart();
			remove_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'button_text' ) );
		}
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$add_to_cart_single = ! empty( $instance['add_to_cart_single'] ) ? $instance['add_to_cart_single'] : __( 'Add to cart', 'siteorigin-premium' );
		$field_id = $this->get_field_id( 'add_to_cart_single' );
		$field_name = $this->get_field_name( 'add_to_cart_single' );
		?>
		<div class="so-wc-widget-form-input">
			<label for="<?php esc_attr_e( $field_id ); ?>">
				<?php esc_html_e( 'Single product button text', 'siteorigin-premium' ); ?>
			</label>
			<input
				type="text"
				id="<?php esc_attr_e( $field_id ); ?>"
				name="<?php esc_attr_e( $field_name ); ?>"
				value="<?php esc_attr_e( $add_to_cart_single ); ?>"/>
		</div>
		<?php
	}

	public function button_text( $text ) {
		global $product;

		if ( $product->get_type() == 'external' ) {
			return $text;
		}

		return ! empty( $this->button_text ) ? $this->button_text : $text;
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Template_Single_Add_To_Cart' );
