<?php
if ( ! class_exists( 'SiteOrigin_Widget' ) ) {
	return;
}

class SiteOrigin_Premium_WooCommerce_Checkout_Order_Review extends SiteOrigin_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-checkout-order-review',
			__( 'Checkout order review', 'siteorigin-premium' ),
			array(
				'description' => __( 'Display the order review and place order button.', 'siteorigin-premium' ),
				'has_preview' => false,
			),
			array(),
			array(
				'thumbnails' => array(
					'type' => 'checkbox',
					'label' => __( 'Display product images', 'siteorigin-premium' ),
					'default' => false,
					'state_emitter' => array(
						'callback' => 'conditional',
						'args'     => array(
							'thumbnail_settings[show]: val',
							'thumbnail_settings[hide]: ! val'
						),
					),
				),
				'thumbnail_size' => array(
					'type'    => 'select',
					'label'   => __( 'Thumbnail image size', 'siteorigin-premium' ),
					'default' => 'small',
					'options' => array(
						'small'  => __( 'Small', 'siteorigin-premium' ),
						'medium' => __( 'Medium', 'siteorigin-premium' ),
						'large'  => __( 'Large', 'siteorigin-premium' ),
					),
					'state_handler' => array(
						'border[show]' => array( 'show' ),
						'border[hide]' => array( 'hide' ),
					),
				),
				'border_width' => array(
					'type'  => 'measurement',
					'label' => __( 'Border width', 'siteorigin-premium' ),
					'state_handler' => array(
						'border[show]' => array( 'show' ),
						'border[hide]' => array( 'hide' ),
					),
				),
				'border_color' => array(
					'type'  => 'color',
					'label' => __( 'Border color', 'siteorigin-premium' ),
					'state_handler' => array(
						'border[show]' => array( 'show' ),
						'border[hide]' => array( 'hide' ),
					),
				),
			)
		);

		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'ajax_setup_product_images' ) );
		add_filter( 'siteorigin_widgets_sanitize_instance_so-wc-checkout-order-review', array( $this, 'store_settings_on_save' ), 10, 3 );
	}

	// The WC checkout Ajax doesn't load the page content so we need to store the data for later use. 
	public function store_settings_on_save( $new_instance, $form_options, $widget ) {
		if ( ! empty( $new_instance['thumbnails'] ) ) {
			$settings = array(
				'thumbnail_size' => $new_instance['thumbnail_size'],
				'border_width'   => $new_instance['border_width'],
				'border_color'   => $new_instance['border_color'],

			);
			update_option( 'so_order_review_settings', $settings, false );
		} else {
			delete_option( 'so_order_review_settings' );
		}

		return $new_instance;
	}

	public function ajax_setup_product_images( $post_data ) {
		$thumbnail_settings = get_option( 'so_order_review_settings' );
		if ( ! empty( $thumbnail_settings ) ) {
			add_filter( 'woocommerce_cart_item_name', array( $this, 'add_product_images' ), 10, 3 );
		}
	}

	public function add_product_images( $product_name, $cart_item, $cart_item_key ) {
		// For consistency, we use the stored settings.
		$thumbnail_settings = get_option( 'so_order_review_settings' );

		if ( ! empty( $thumbnail_settings ) ) {
			switch ( $thumbnail_settings['thumbnail_size'] ) {
				case 'small':
					$image_size = array( 40, 40 );
					break;
				case 'medium':
					$image_size = array( 60, 60 );
					break;
				case 'large':
					$image_size = array( 80, 80 );
					break;
			}

			$product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $product->get_image( $image_size ), $cart_item, $cart_item_key );

			echo '<div class="siteorigin-premium-checkout-product-image" style="display: inline-block; line-height: 0; vertical-align: middle;';
			if ( ! empty( $thumbnail_settings['border_width'] ) && $thumbnail_settings['border_width'] > 0 ) {
				$border_color = ! empty( $thumbnail_settings['border_color'] ) ? $thumbnail_settings['border_color'] : '#000';
				echo 'border: ' . esc_attr( $thumbnail_settings['border_width'] ) . ' solid ' . esc_attr( $border_color );
			}
			echo '">' . $thumbnail . '</div>';

		}
	}

	public function widget( $args, $instance ) {
		if ( ! empty( $instance['thumbnails'] ) ) {
			add_filter( 'woocommerce_cart_item_name', array( $this, 'add_product_images' ), 10, 3 );
		}

		echo $args['before_widget'];
		do_action( 'woocommerce_checkout_before_order_review_heading' );
		?>
		<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>
		<?php
		do_action( 'woocommerce_checkout_after_order_review' );
		echo $args['after_widget'];

		if ( ! empty( $instance['thumbnails'] ) ) {
			remove_filter( 'woocommerce_cart_item_name', array( $this, 'add_product_images' ) );
		}
	}
}
register_widget( 'SiteOrigin_Premium_WooCommerce_Checkout_Order_Review' );
