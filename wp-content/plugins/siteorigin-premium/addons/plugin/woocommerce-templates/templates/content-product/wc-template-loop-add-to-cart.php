<?php

class SiteOrigin_Premium_WooCommerce_Template_Loop_Add_To_Cart extends WP_Widget {

	private $button_text;

	public function __construct() {
		parent::__construct(
			'so-wc-template-loop-add-to-cart',
			__( 'Product loop "Add to cart"', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the product add to cart button.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		global $product;

		echo $args['before_widget'];
		if ( function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
			// Only override the button text if we have a setting for the product type
			if ( isset( $instance[ 'add_to_cart_' . $product->get_type() ] ) ) {
				$this->button_text = $instance[ 'add_to_cart_' . $product->get_type() ];
			}
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'button_text' ) );
			woocommerce_template_loop_add_to_cart();
			remove_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'button_text' ) );
		}
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$this->output_field( $instance, 'simple' );
		$this->output_field( $instance, 'variable', 'Select options' );
		$this->output_field( $instance, 'grouped', 'View options' );
	}

	private function output_field( $instance, $field, $fallback = false ) {
		if ( ! $fallback ) {
			$fallback = __( 'Add to cart', 'siteorigin-premium' );
		}

		$field_value = ! empty( $instance[ 'add_to_cart_' . $field ] ) ? $instance[ 'add_to_cart_' . $field ] : $fallback;
		$field_id = $this->get_field_id( 'add_to_cart_' . $field );
		$field_name = $this->get_field_name( 'add_to_cart_' . $field );
		?>
		<div class="so-wc-widget-form-input">
			<label for="<?php esc_attr_e( $field_id ); ?>">
				<?php esc_html_e( sprintf( __( '%s product button text', 'siteorigin-premium' ), ucfirst( $field ) ) ); ?>
			</label>
			<input
				type="text"
				id="<?php esc_attr_e( $field_id ); ?>"
				name="<?php esc_attr_e( $field_name ); ?>"
				value="<?php esc_attr_e( $field_value ); ?>"/>
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

register_widget( 'SiteOrigin_Premium_WooCommerce_Template_Loop_Add_To_Cart' );
