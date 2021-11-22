<?php
/*
Plugin Name: SiteOrigin Tooltip
Description: Enable a tooltip on various image widgets.
Version: 1.0.0
Author: SiteOrigin
Author URI: https://siteorigin.com
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Documentation: https://siteorigin.com/premium-documentation/plugin-addons/tooltip/
Tags: Widgets Bundle
Requires: so-widgets-bundle/image, so-widgets-bundle/image-grid, so-widgets-bundle/masonry
*/

class SiteOrigin_Premium_Plugin_Tooltip {

	const SO_IMAGE_ID_BASE = 'sow-image';
	const SO_IMAGE_GRID_ID_BASE = 'sow-image-grid';
	const SO_SIMPLE_MASONRY_ID_BASE = 'sow-simple-masonry';
	const SO_ICON_ID_BASE = 'sow-icon';
	const SO_PRICE_TABLE_ID_BASE = 'sow-price-table';
	const SO_FEATURES_ID_BASE = 'sow-features';

	function __construct() {
		$widget_ids = array(
			self::SO_IMAGE_ID_BASE,
			self::SO_IMAGE_GRID_ID_BASE,
			self::SO_SIMPLE_MASONRY_ID_BASE,
			self::SO_ICON_ID_BASE,
			self::SO_PRICE_TABLE_ID_BASE,
			self::SO_FEATURES_ID_BASE,
		);

		foreach ( $widget_ids as $widget_id ) {
			add_filter( 'siteorigin_widgets_form_options_' . $widget_id, array( $this, 'admin_form_options' ) );
			add_filter( 'siteorigin_widgets_wrapper_data_' . $widget_id, array( $this, 'add_wrapper_data' ), 10, 3 );
			add_filter( 'siteorigin_widgets_wrapper_classes_' . $widget_id, array( $this, 'add_wrapper_class' ), 10, 3 ); 
			add_action( 'siteorigin_widgets_enqueue_frontend_scripts_' . $widget_id, array( $this, 'enqueue_tooltip_scripts' ), 10, 2 );
		}

		add_action( 'init' , array( $this, 'register_tooltip_scripts' ) );
	}

	public static function single() {
		static $single;

		return empty( $single ) ? $single = new self() : $single;
	}

	public function get_settings_form() {
		return new SiteOrigin_Premium_Form(
			'so-addon-tooltip-settings',
			array(
				'enabled_for_widgets' => array(
					'type' => 'checkboxes',
					'label' => __( 'Enabled SiteOrigin widgets', 'siteorigin-premium' ),
					'default' => array( 'sow-image', 'sow-image-grid', 'sow-simple-masonry', 'sow-price-table' ),
					'options' => array(
						'sow-features' => __( 'Features widget', 'siteorigin-premium' ),
						'sow-icon' => __( 'Icon Widget', 'siteorigin-premium' ),
						'sow-image' => __( 'Image widget', 'siteorigin-premium' ),
						'sow-image-grid' => __( 'Image Grid widget', 'siteorigin-premium' ),
						'sow-price-table' => __( 'Price Table widget', 'siteorigin-premium' ),
						'sow-simple-masonry' => __( 'Simple Masonry widget', 'siteorigin-premium' ),
					),
				),
				'show_trigger' => array(
					'type' => 'radio',
					'label' => __( 'Show tooltip on', 'siteorigin-premium' ),
					'default' => 'mouseover',
					'options' => array(
						'mouseover' => __( 'Mouse over', 'siteorigin-premium' ),
						'click' => __( 'Click', 'siteorigin-premium' ),
					),
					'state_emitter' => array(
						'callback' => 'select',
						'args' => array( 'trigger' ),
					),
				),
				'show_delay' => array(
					'type' => 'number',
					'label' => __( 'Delay before showing tooltip (in milliseconds)', 'siteorigin-premium' ),
					'default' => 500,
					'state_handler' => array(
						'trigger[mouseover]' => array( 'slideDown' ),
						'_else[trigger]' => array( 'slideUp' ),
					),
				),
				'hide_trigger' => array(
					'type' => 'radio',
					'label' => __( 'Hide tooltip on', 'siteorigin-premium' ),
					'default' => 'mouseout',
					'options' => array(
						'mouseout' => __( 'Mouse out', 'siteorigin-premium' ),
						'click' => __( 'Click', 'siteorigin-premium' ),
					),
				),
				'position' => array(
					'type' => 'select',
					'label' => __( 'Tooltip position', 'siteorigin-premium' ),
					'default' => 'follow_cursor',
					'options' => array(
						'follow_cursor' => __( 'Follow cursor', 'siteorigin-premium' ),
						'center' => __( 'Center', 'siteorigin-premium' ),
						'top' => __( 'Top', 'siteorigin-premium' ),
						'bottom' => __( 'Bottom', 'siteorigin-premium' ),
					),
				),
				'theme' => array(
					'type' => 'radio',
					'label' => __( 'Theme', 'siteorigin-premium' ),
					'default' => 'light',
					'options' => array(
						'light' => __( 'Light', 'siteorigin-premium' ),
						'dark' => __( 'Dark', 'siteorigin-premium' ),
					),
				),
			)
		);
	}

	public function admin_form_options( $form_options ) {
		if ( empty( $form_options ) ) {
			return $form_options;
		}
		
		$form_options['tooltip'] = array(
			'type' => 'section',
			'label' => __( 'Tooltip', 'siteorigin-premium' ),
			'hide' => true,
			'fields' => array(
				'is_enabled' => array(
					'type' => 'select',
					'label' => __( 'Enable Tooltip', 'siteorigin-premium' ),
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
					'label' => __( 'Tooltip theme', 'siteorigin-premium' ),
					'default' => 'global',
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

		$tooltip_enabled = $this->tooltip_enabled_for_instance( $instance, $widget );

		if ( empty( $tooltip_enabled ) ) {
			return $data;
		}

		$data['tooltip-enabled'] = $tooltip_enabled;

		$tooltip_settings = $this->get_tooltip_settings();

		$tooltip_theme = $tooltip_settings['theme'];
		if ( ! ( empty( $instance['tooltip'] ) || empty( $instance['tooltip']['theme'] ) ) && $instance['tooltip']['theme'] != 'global' ) {
			$tooltip_theme = $instance['tooltip']['theme'];
		}

		$data['tooltip-theme'] = $tooltip_theme;

		return $data;
	}

	public function add_wrapper_class( $classes, $instance, $widget ) {
		if ( ! empty( $this->tooltip_enabled_for_instance( $instance, $widget ) ) ) {
			$classes[] = 'so-premium-tooltip-container'; 
		}

		return $classes;
	}

	public function register_tooltip_scripts() {
		wp_register_script(
			'so-premium-tooltip',
			plugin_dir_url( __FILE__ ) . 'js/so-premium-tooltip' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
			array( 'jquery' ),
			SITEORIGIN_PREMIUM_VERSION
		);
		
		wp_register_style(
			'so-premium-tooltip',
			plugin_dir_url( __FILE__ ) . 'css/so-premium-tooltip.css',
			array(),
			SITEORIGIN_PREMIUM_VERSION
		);
	}
	
	public function enqueue_tooltip_scripts( $instance, $widget ) {
		$tooltip_settings = $this->get_tooltip_settings();
		
		if (
			$this->tooltip_enabled_for_instance( $instance, $widget ) &&
			! wp_script_is( 'so-premium-tooltip' )
		) {
			wp_enqueue_script( 'so-premium-tooltip' );
			wp_localize_script(
				'so-premium-tooltip',
				'soPremiumTooltipOptions',
				$tooltip_settings
			);
			wp_enqueue_style( 'so-premium-tooltip' );
			// If SiteOrigin Page Builder is active, use its breakpoints. If not, use default breakpoint.
			$breakpoint = function_exists( 'siteorigin_panels_setting' ) ? siteorigin_panels_setting( 'tablet-width' ) : '1024';
			wp_add_inline_style(
				'so-premium-tooltip', 
				'@media (min-width: ' . $breakpoint . 'px) { .so-premium-tooltip { max-width: 30vw; } }'
			);
		}
	}
	
	private function tooltip_enabled_for_instance( $instance, $widget ) {

		$tooltip_settings = $this->get_tooltip_settings();

		$tooltip_global_is_enabled = in_array( $widget->id_base, $tooltip_settings['enabled_for_widgets'] );

		$tooltip_instance_is_enabled = ( empty( $instance['tooltip'] ) || empty( $instance['tooltip']['is_enabled'] ) ) ? 'global' : $instance['tooltip']['is_enabled'];

		return $tooltip_instance_is_enabled == 'enabled' ||
			( $tooltip_instance_is_enabled == 'global' && ! empty( $tooltip_global_is_enabled ) );
	}

	private function get_tooltip_settings() {
		$premium_options = SiteOrigin_Premium_Options::single();
		
		return $premium_options->get_settings( 'plugin/tooltip' );
	}
}
