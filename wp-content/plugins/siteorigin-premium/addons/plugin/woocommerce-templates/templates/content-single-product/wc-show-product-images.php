<?php

class SiteOrigin_Premium_WooCommerce_Show_Product_Images extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-show-product-images',
			__( 'Product images', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the product image gallery. Choose from several options.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['gallery_type'] ) ) {
			$gallery_type = $instance['gallery_type'];
			if ( empty( preg_match('/zoom/', $gallery_type ) ) ) {
				wp_dequeue_script( 'zoom' );
			}
			if ( empty( preg_match( '/lightbox/', $gallery_type ) ) ) {
				wp_dequeue_script( 'photoswipe-ui-default' );
				wp_dequeue_style( 'photoswipe-ui-default' );
				remove_action( 'wp_footer', 'woocommerce_photoswipe' );
			}
		}
		$sale_flash_enabled = isset( $instance['enable_sale_flash'] ) ? ! empty( $instance['enable_sale_flash'] ) : true;
		if ( function_exists( 'woocommerce_show_product_sale_flash' ) ) {
			if ( $sale_flash_enabled ) {
				?><div style="position: relative;"><?php
				woocommerce_show_product_sale_flash();
			}
		}
		if ( function_exists( 'woocommerce_show_product_images' ) ) {
			woocommerce_show_product_images();
		}
		if ( $sale_flash_enabled ) {
			?></div><?php
		}
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$gallery_type = !empty( $instance['gallery_type'] ) ? $instance['gallery_type'] : 'slider-lightbox-zoom';
		$gallery_options = array(
			'slider' => __( 'Slider', 'siteorigin-premium' ),
			'slider-lightbox' => __( 'Slider with lightbox', 'siteorigin-premium' ),
			'slider-zoom' => __( 'Slider with zoom', 'siteorigin-premium' ),
			'slider-lightbox-zoom' => __( 'Slider with lightbox and zoom', 'siteorigin-premium' ),
		);
		$gallery_id = $this->get_field_id( 'gallery_type' );
		$gallery_name = $this->get_field_name( 'gallery_type' );
		?>
		<div class="so-wc-widget-form-input">
			<label for="<?php echo esc_attr( $gallery_id )?>"><?php esc_html_e( 'Gallery type', 'siteorigin-premium' ) ?></label>
			<select
				id="<?php echo esc_attr( $gallery_id )?>"
				name="<?php echo esc_attr( $gallery_name ) ?>">
				<?php foreach( $gallery_options as $val => $label ) : ?>
					<option value="<?php echo esc_attr( $val ) ?>" <?php selected($val, $gallery_type) ?>><?php echo esc_html( $label ) ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
		$sale_flash_enabled = isset( $instance['enable_sale_flash']) ? ! empty( $instance['enable_sale_flash']) : true;
		$sale_flash_id = $this->get_field_id( 'enable_sale_flash' );
		$sale_flash_name = $this->get_field_name( 'enable_sale_flash' );
		?>
		<div class="so-wc-widget-form-input">
			<input
				type="checkbox"
				id="<?php echo esc_attr( $sale_flash_id ) ?>"
				name="<?php echo esc_attr( $sale_flash_name ) ?>"
				<?php checked( ! empty( $sale_flash_enabled ) )?>>
			<label for="<?php echo esc_attr( $sale_flash_id ) ?>">
				<?php esc_html_e( 'Enable sale sticker', 'siteorigin-premium' ) ?>
			</label>
		</div>
		<?php
	}

	public function update( $new_instance, $old_instance ) {

		if ( ! isset( $new_instance['enable_sale_flash'] ) ) {
			$new_instance['enable_sale_flash'] = empty( $old_instance['enable_sale_flash'] );
		}

		$gallery_options = array( 'slider', 'slider-lightbox', 'slider-zoom', 'slider-lightbox-zoom' );

		if ( ! isset( $new_instance['gallery_type'] ) || !in_array( $new_instance['gallery_type'], $gallery_options ) ) {
			$new_instance['gallery_type'] = 'slider-lightbox-zoom';
		}
		return $new_instance;
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Show_Product_Images' );
