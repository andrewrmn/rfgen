<?php
/*
Plugin Name: SiteOrigin Image Overlay
Description: Add a beautiful and customizable text overlay with animations to your images.
Version: 1.0.0
Author: SiteOrigin
Author URI: https://siteorigin.com
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Documentation: https://siteorigin.com/premium-documentation/plugin-addons/image-overlay/
Tags: Widgets Bundle
Requires: so-widgets-bundle/image, so-widgets-bundle/image-grid, so-widgets-bundle/masonry
*/

class SiteOrigin_Premium_Plugin_Image_Overlay {

	const SO_IMAGE_ID_BASE = 'sow-image';
	const SO_IMAGE_GRID_ID_BASE = 'sow-image-grid';
	const SO_SIMPLE_MASONRY_ID_BASE = 'sow-simple-masonry';

	function __construct() {
		$widget_ids = array(
			self::SO_IMAGE_ID_BASE,
			self::SO_IMAGE_GRID_ID_BASE,
			self::SO_SIMPLE_MASONRY_ID_BASE,
		);

		foreach ( $widget_ids as $widget_id ) {
			add_filter( 'siteorigin_widgets_form_options_' . $widget_id, array( $this, 'admin_form_options' ) );
			add_filter( 'siteorigin_widgets_wrapper_data_' . $widget_id, array( $this, 'add_wrapper_data' ), 10, 3 );
			add_action( 'siteorigin_widgets_enqueue_frontend_scripts_' . $widget_id, array( $this, 'enqueue_overlay_scripts' ), 10, 2 );
		}

		add_action( 'init' , array( $this, 'register_overlay_scripts' ) );
	}

	public static function single() {
		static $single;

		return empty( $single ) ? $single = new self() : $single;
	}

	public function get_settings_form() {

		$overlay_presets = $this->get_presets();

		return new SiteOrigin_Premium_Form(
			'so-addon-overlay-settings',
			array(
				'enabled_for_widgets' => array(
					'type' => 'checkboxes',
					'label' => __( 'Enabled SiteOrigin widgets', 'siteorigin-premium' ),
					'default' => array( 'sow-image', 'sow-image-grid', 'sow-simple-masonry' ),
					'options' => array(
						'sow-image' => __( 'Image widget', 'siteorigin-premium' ),
						'sow-image-grid' => __( 'Image Grid widget', 'siteorigin-premium' ),
						'sow-simple-masonry' => __( 'Simple Masonry widget', 'siteorigin-premium' ),
					),
				),
				'theme' => array(
					'type' => 'presets',
					'label' => __( 'Preset themes', 'siteorigin-premium' ),
					'options' => $overlay_presets,
				),
				'show_delay' => array(
					'type' => 'number',
					'label' => __( 'Delay before showing overlay (in milliseconds)', 'siteorigin-premium' ),
					'default' => 300,
				),
				'overlay_color' => array(
					'type' => 'color',
					'label' => __( 'Overlay color', 'siteorigin-premium' ),
					'default' => '#000000',
				),
				'overlay_opacity' => array(
					'type' => 'slider',
					'label' => __( 'Overlay opacity', 'siteorigin-premium' ),
					'min' => 0,
					'max' => 1,
					'step' => 0.01,
					'default' => 0.8,
				),
				'overlay_position' => array(
					'type' => 'select',
					'label' => __( 'Overlay position', 'siteorigin-premium' ),
					'default' => 'bottom',
					'options' => array(
						'top' => __( 'Top', 'siteorigin-premium' ),
						'right' => __( 'Right', 'siteorigin-premium' ),
						'bottom' => __( 'Bottom', 'siteorigin-premium' ),
						'left' => __( 'Left', 'siteorigin-premium' ),
					)
				),
				'overlay_size' => array(
					'type' => 'slider',
					'label' => __( 'Overlay size', 'siteorigin-premium' ),
					'description' => __( 'The size of the overlay as a fraction of the image\'s size.', 'siteorigin-premium' ),
					'min' => 0,
					'max' => 1,
					'step' => 0.01,
					'default' => 0.3,
				),
				'desktop_show_trigger' => array(
					'type' => 'radio',
					'label' => __( 'Desktop device trigger', 'siteorigin-premium' ),
					'default' => 'hover',
					'options' => array(
						'hover' => __( 'Show on hover', 'siteorigin-premium' ),
						'always' => __( 'Always show', 'siteorigin-premium' ),
					),
					'description' => __( 'When to display the overlay on desktop.', 'siteorigin-premium' ),
				),
				'touch_show_trigger' => array(
					'type' => 'radio',
					'label' => __( 'Touch device show trigger', 'siteorigin-premium' ),
					'default' => 'touch',
					'options' => array(
						'touch' => __( 'Show on touch', 'siteorigin-premium' ),
						'always' => __( 'Always show', 'siteorigin-premium' ),
					),
					'description' => __( 'When to display the overlay on touch devices.', 'siteorigin-premium' ),
				),
				'responsive_breakpoint' => array(
					'type'        => 'measurement',
					'label'       => __( 'Touch device width', 'siteorigin-premium' ),
					'default'     => '1024px',
					'description' => __( 'Device width in pixels. Controls when the touch show trigger takes effect.', 'siteorigin-premium' ),
				),
				'overlay_animation' => array(
					'type' => 'select',
					'label' => __( 'Overlay animation', 'siteorigin-premium' ),
					'options' => array(
						'' => __( 'None', 'siteorigin-premium' ),
						'fade' => __( 'Fade', 'siteorigin-premium' ),
						'slide' => __( 'Slide', 'siteorigin-premium' ),
						'drop' => __( 'Drop', 'siteorigin-premium' ),
					),
					'default' => 'fade',
				),
				'overlay_animation_onscreen' => array(
					'type' => 'checkbox',
					'label' => __( 'Animate overlay when image enters screen', 'siteorigin-premium' ),
					'description' => __( 'Requires the applicable device trigger to be set to Always show.', 'siteorigin-premium' ),
					'default' => false,
				),
				'font_family' => array(
					'type' => 'font',
					'label' => __( 'Font family', 'siteorigin-premium' ),
				),
				'text_size' => array(
					'type' => 'measurement',
					'label' => __( 'Text size', 'siteorigin-premium' ),
					'default' => '15px',
				),
				'text_color' => array(
					'type' => 'color',
					'label' => __( 'Text color', 'siteorigin-premium' ),
					'default' => '#FFFFFF',
				),
				'text_padding' => array(
					'type' => 'multi-measurement',
					'label' => __( 'Text padding', 'siteorigin-premium' ),
					'autofill' => true,
					'default' => '22px 22px 22px 22px',
					'measurements' => array(
						'top' => __( 'Top', 'siteorigin-premium' ),
						'right' => __( 'Right', 'siteorigin-premium' ),
						'bottom' => __( 'Bottom', 'siteorigin-premium' ),
						'left' => __( 'Left', 'siteorigin-premium' ),
					),
				),
				'text_position' => array(
					'type' => 'select',
					'label' => __( 'Text position', 'siteorigin-premium' ),
					'options' => array(
						'top' => __( 'Top', 'siteorigin-premium' ),
						'bottom' => __( 'Bottom', 'siteorigin-premium' ),
						'middle' => __( 'Middle', 'siteorigin-premium' ),
					),
					'default' => 'middle',
				),
				'text_align' => array(
					'type' => 'select',
					'label' => __( 'Text alignment', 'siteorigin-premium' ),
					'options' => array(
						'left' => __( 'Left', 'siteorigin-premium' ),
						'right' => __( 'Right', 'siteorigin-premium' ),
						'center' => __( 'Center', 'siteorigin-premium' ),
					),
					'default' => 'left',
				),
				'text_animation' => array(
					'type' => 'select',
					'label' => __( 'Text animation', 'siteorigin-premium' ),
					'options' => array(
						'' => __( 'None', 'siteorigin-premium' ),
						'fade' => __( 'Fade', 'siteorigin-premium' ),
						'slide_left' => __( 'Slide Left', 'siteorigin-premium' ),
						'slide_right' => __( 'Slide Right', 'siteorigin-premium' ),
						'slide_up' => __( 'Slide Up', 'siteorigin-premium' ),
						'slide_down' => __( 'Slide Down', 'siteorigin-premium' ),
						'drop' => __( 'Drop', 'siteorigin-premium' ),
					),
					'description' => __( 'This is a secondary animation which will be triggered after the overlay animation finishes playing.', 'siteorigin-premium' )
				),
			)
		);
	}

	public function get_presets() {
		$overlay_presets = json_decode(
			file_get_contents( plugin_dir_path( __FILE__ ) . 'data/presets.json' ),
			true
		);

		return $overlay_presets;
	}

	public function admin_form_options( $form_options ) {
		if ( empty( $form_options ) ) {
			return $form_options;
		}

		$form_options['overlay'] = array(
			'type' => 'section',
			'label' => __( 'Image Overlay', 'siteorigin-premium' ),
			'hide' => true,
			'fields' => array(
				'is_enabled' => array(
					'type' => 'select',
					'label' => __( 'Enable Image Overlay', 'siteorigin-premium' ),
					'default' => 'global',
					'options' => array(
						'global' => __( 'Use global setting', 'siteorigin-premium' ),
						'enabled' => __( 'Enabled', 'siteorigin-premium' ),
						'disabled' => __( 'Disabled', 'siteorigin-premium' ),
					),
					'state_emitter' => array(
						'callback' => 'select',
						'args' => array( 'is_enabled' ),
					),
				),
				'theme' => array(
					'type' => 'select',
					'label' => __( 'Overlay theme', 'siteorigin-premium' ),
					'default' => 'global',
					'description' => __( 'This will override some of the global Overlay settings.', 'siteorigin-premium' ),
					'options' => array(
						'global' => __( 'Use global setting', 'siteorigin-premium' ),
						'light' => __( 'Light', 'siteorigin-premium' ),
						'dark' => __( 'Dark', 'siteorigin-premium' ),
					),
					'state_handler' => array(
						'is_enabled[disabled]' => array( 'slideUp' ),
						'_else[is_enabled]' => array( 'slideDown' ),
					),
				),
			),
		);

		return $form_options;
	}

	public function add_wrapper_data( $data, $instance, $widget ) {

		$overlay_enabled = $this->overlay_enabled_for_instance( $instance, $widget );

		if ( empty( $overlay_enabled ) ) {
			return $data;
		}

		$data['overlay-enabled'] = $overlay_enabled;

		$overlay_settings = $this->get_overlay_settings();

		if ( ! empty( $instance['overlay'] ) && ! empty( $instance['overlay']['theme'] ) ) {
			$overlay_presets = $this->get_presets();
			if ( ! empty( $overlay_presets[ $instance['overlay']['theme'] ] ) ) {
				$preset = $overlay_presets[ $instance['overlay']['theme'] ];
				$overlay_settings = array_merge( $overlay_settings, $preset['values'] );
			}
		}

		// Font family and weight
		if ( ! empty( $overlay_settings['font_family'] ) ) {
			$font = siteorigin_widget_get_font( $overlay_settings['font_family'] );
			$overlay_settings['font'] = $font;
		}

		if ( ! empty( $overlay_settings['overlay_animation_onscreen'] ) ) {
			$overlay_settings['widget_id'] = $instance['_sow_form_id'];
		}

		$data['overlay-settings'] = json_encode( $overlay_settings );
		$data = apply_filters( 'siteorigin_premium_image_overlay_wrapper_data', $data, $instance, $widget );

		return $data;
	}

	public function register_overlay_scripts() {
		wp_register_script(
			'so-premium-anime',
			plugin_dir_url( __FILE__ ) . 'js/lib/anime' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
			array(),
			SITEORIGIN_PREMIUM_VERSION
		);

		wp_register_script(
			'so-premium-image-overlay',
			plugin_dir_url( __FILE__ ) . 'js/so-premium-image-overlay' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
			array( 'jquery', 'so-premium-anime' ),
			SITEORIGIN_PREMIUM_VERSION
		);

		wp_register_style(
			'so-premium-image-overlay',
			plugin_dir_url( __FILE__ ) . 'css/so-premium-image-overlay.css',
			array(),
			SITEORIGIN_PREMIUM_VERSION
		);
	}

	public function enqueue_overlay_scripts( $instance, $widget ) {

		if ( $this->overlay_enabled_for_instance( $instance, $widget ) ) {

			wp_enqueue_script( 'so-premium-image-overlay' );
			wp_enqueue_style( 'so-premium-image-overlay' );

			$overlay_settings = $this->get_overlay_settings();
			if ( ! empty( $overlay_settings['overlay_animation_onscreen'] ) ) {
				wp_enqueue_script( 'on-screen' );
			}
		}
	}

	private function overlay_enabled_for_instance( $instance, $widget ) {

		$overlay_settings = $this->get_overlay_settings();

		$overlay_global_is_enabled = in_array( $widget->id_base, $overlay_settings['enabled_for_widgets'] );

		$overlay_instance_is_enabled = ( empty( $instance['overlay'] ) || empty( $instance['overlay']['is_enabled'] ) ) ?
			'global' :
			$instance['overlay']['is_enabled'];

		return $overlay_instance_is_enabled == 'enabled' ||
			( $overlay_instance_is_enabled == 'global' && ! empty( $overlay_global_is_enabled ) );
	}

	private function get_overlay_settings() {
		$premium_options = SiteOrigin_Premium_Options::single();

		return $premium_options->get_settings( 'plugin/image-overlay' );
	}
}
