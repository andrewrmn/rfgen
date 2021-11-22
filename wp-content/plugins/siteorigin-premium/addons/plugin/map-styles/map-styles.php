<?php
/*
Plugin Name: SiteOrigin Map Styles
Description: Adds a curated list of predefined map styles to the SiteOrigin Google Maps widget as well as additional consent prompt styling options.
Version: 1.0.0
Author: SiteOrigin
Author URI: https://siteorigin.com
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Documentation: https://siteorigin.com/premium-documentation/plugin-addons/map-styles/
Tags: Widgets Bundle
Requires: so-widgets-bundle/google-map
*/

class SiteOrigin_Premium_Plugin_Map_Styles {
	
	function __construct() {
		add_action( 'init', array( $this, 'init_addon' ) );
	}
	
	static function single() {
		static $single;
		return empty( $single ) ? $single = new self() : $single;
	}
	
	/**
	 * Do any required intialization methods.
	 */
	public function init_addon() {
		$this->add_filters();
	}
	
	/**
	 * Add filters for modifying various widget related properties and configuration.
	 */
	public function add_filters() {
		if ( class_exists( 'SiteOrigin_Widget_GoogleMap_Widget' ) ) {
			add_filter( 'siteorigin_widgets_form_options_sow-google-map', array( $this, 'admin_form_options' ) );
			add_filter( 'siteorigin_widgets_google_maps_widget_styles', array( $this, 'add_premium_styles' ), 10, 2 );
			add_filter( 'siteorigin_widgets_settings_form_sow-google-map', array( $this, 'add_widget_global_settings' ), 10, 2 );
			add_filter( 'siteorigin_widgets_template_variables_sow-google-map', array( $this, 'add_template_variables' ), 10, 2 );
			add_filter( 'siteorigin_widgets_less_variables_sow-google-map', array( $this, 'add_less_variables' ), 10, 2 );
			add_action( 'siteorigin_widgets_enqueue_frontend_scripts_sow-google-map', array( $this, 'enqueue_front_end_scripts' ), 10, 2 );
		}
	}
	
	/**
	 * Filters the admin form for the maps widget to add Premium fields.
	 *
	 * @param $form_options array The Google Maps Widget's form options.
	 * @param $widget SiteOrigin_Widget_GoogleMap_Widget The widget object.
	 *
	 * @return mixed The updated form options array containing the new and modified fields.
	 */
	public function admin_form_options( $form_options ) {

		if ( empty( $form_options ) ) {
			return $form_options;
		}
		
		if ( ! function_exists( 'list_files' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		siteorigin_widgets_array_insert(
			$form_options,
			'settings',
			array(
				'center_user_location' => array(
					'type' => 'checkbox',
					'label' => __( "Center on user's location", 'siteorigin-premium' ),
					'default' => false,
					'description' => __( 'The user will be prompted to accept before centering. The Map center location will be used as a fallback. Requires HTTPS.', 'siteorigin-premium' ),
				),
			)
		);
		
		if ( ! WP_Filesystem() ) {
			return $form_options;
		}

		siteorigin_widgets_array_insert(
			$form_options['styles']['fields']['style_method']['options'],
			'custom',
			array(
				'premium' => __( 'Premium Styles', 'siteorigin-premium' )
			)
		);
		
		$map_styles_dir = plugin_dir_path( __FILE__ ) . "data/";
		$files = list_files( realpath( $map_styles_dir ), 1 );
		
		$premium_map_styles = array();
		foreach ( $files as $file ) {
			
			$path_info = pathinfo( $file );
			$mime_type = '';
			if ( function_exists( 'mime_content_type' ) ) {
				// get file mime type
				$mime_type = mime_content_type( $file );
				
			} else {
				// If `mime_content_type` isn't available, just check file extension.
				// Allow files with `.json` or common image extension.
				$allowed_types = array( 'json', 'jpg', 'jpeg', 'gif', 'png' );
				if ( ! empty( $path_info['ext'] ) && in_array( $path_info['ext'], $allowed_types ) ) {
					$mime_type = $path_info['ext'] == 'json' ? 'text/' : 'image/';
				}
			}
			
			$valid_type = strpos( $mime_type, 'text/' ) === 0 || strpos( $mime_type, 'image/' ) === 0;
			if ( empty( $mime_type ) || empty( $valid_type ) ) {
				continue;
			}
			
			// get file contents
			$file_contents = file_get_contents( $file );
			
			// skip if file_get_contents fails
			if ( $file_contents === false ) {
				continue;
			}
			
			$filename = $path_info['filename'];
			
			if ( empty( $premium_map_styles[ $filename ] ) ) {
				$premium_map_styles[ $filename ] = array();
				$premium_map_styles[ $filename ]['label'] = implode( ' ', array_map( 'ucfirst', explode( '_', $filename ) ) );
			}
			
			if ( strpos( $mime_type, 'image/' ) === 0 ) {
				$premium_map_styles[ $filename ]['image'] = plugin_dir_url( __FILE__ ) . 'data/' . $path_info['basename'];
			}
		}
		
		
		siteorigin_widgets_array_insert(
			$form_options['styles']['fields'],
			'raw_json_map_styles',
			array(
				'premium_map_style' => array(
					'type' => 'image-radio',
					'layout' => 'horizontal',
					'label' => __( 'Premium styles', 'siteorigin-premium' ),
					'options' => $premium_map_styles,
					'default' => 'silver',
					'state_handler' => array(
						'style_method[premium]' => array('show'),
						'_else[style_method]' => array('hide'),
					),
					'description' => sprintf(
						__( 'Imports map styles created using the %sGoogle Maps Platform Styling Wizard%s and %sSnazzy Maps%s.', 'siteorigin-premium' ),
						'<a href="https://mapstyle.withgoogle.com/" target="_blank" rel="noopener noreferrer">',
						'</a>',
						'<a href="https://snazzymaps.com/" target="_blank" rel="noopener noreferrer">',
						'</a>'
					),
				),
			)
		);
		
		return $form_options;
	}
	
	public function add_premium_styles( $styles, $instance ) {
		$style_config = $instance['styles'];
		
		if ( $style_config['style_method'] === 'premium' &&  ! empty( $style_config['premium_map_style'] ) ) {
			
			$premium_style = $style_config['premium_map_style'];
			
			$premium_styles_string = file_get_contents( plugin_dir_path( __FILE__ ) . "data/$premium_style.json" );
			
			if ( ! empty( $premium_styles_string ) ) {
				$styles['styles'] = json_decode( $premium_styles_string, true );
				if ( empty( $style_config['styled_map_name'] ) ) {
					$styles['map_name'] = implode( ' ', array_map( 'ucfirst', explode( '_', $premium_style ) ) );
				}
			}
		}
		return $styles;
	}

	public function add_widget_global_settings( $form_options, $instance ) {
		if ( empty( $form_options ) ) {
			return $form_options;
		}

		if ( ! isset( $form_options['map_consent_design'] ) ) {
			$form_options['map_consent_design'] = array(
				'type' => 'section',
				'label' => __( 'Consent prompt design', 'siteorigin-premium' ),
				'hide' => true,
				'fields' => array(),
			);
		}

		$form_options['map_consent_design']['fields']['background'] = array(
			'type' => 'section',
			'label' => __( 'Background', 'siteorigin-premium' ),
			'hide' => true,
			'fields' => array(
				'color' => array(
					'type' => 'color',
					'label' => __( 'Consent prompt background color', 'siteorigin-premium' ),
					'default' => '#000',
				),
				'image' => array(
					'type' => 'media',
					'label' => __( 'Consent prompt background image', 'siteorigin-premium' ),
					'library' => 'image',
					'fallback' => true,
				),
			),
		);

		$form_options['map_consent_design']['fields']['text'] = array(
			'type' => 'section',
			'label' => __( 'Text', 'siteorigin-premium' ),
			'hide' => true,
			'fields' => array(
				'color' => array(
					'type' => 'color',
					'label' => __( 'Consent prompt text color', 'siteorigin-premium' ),
					'default' => '#fff',
				),
				'link' => array(
					'type' => 'color',
					'label' => __( 'Consent prompt link color', 'siteorigin-premium' ),
					'default' => '#41a9d5',
				),
				'link_hover' => array(
					'type' => 'color',
					'label' => __( 'Consent prompt link color hover', 'siteorigin-premium' ),
					'default' => '#298fba',
				),
			),
		);



		// Remove and re-add Responsive Breakpoint so that it's the last setting. 
		$breakpoint = $form_options['responsive_breakpoint'];
		unset( $form_options['responsive_breakpoint'] );
		$form_options['responsive_breakpoint'] = $breakpoint;

		return $form_options;
	}

	public function add_template_variables( $template_variables, $instance ) {
		$maps_widget = new SiteOrigin_Widget_GoogleMap_Widget();
		$global_settings = $maps_widget->get_global_settings();

		if (
			! empty( $global_settings['map_consent'] ) &&
			! empty( $global_settings['map_consent_design']['background'] )
		) {
			$consent_background_image = siteorigin_widgets_get_attachment_image_src(
				$global_settings['map_consent_design']['background']['image'],
				'full',
				$global_settings['map_consent_design']['background']['image_fallback']
			);

			if ( ! empty( $consent_background_image ) ) {
				$template_variables['consent_background_image'] = $consent_background_image[0];
			}
		}

		if ( isset( $template_variables['map_data'] ) ) {
			$template_variables['map_data']['center_user_location'] = ! empty( $instance['center_user_location'] );
		}

		return $template_variables;
	}

	public function add_less_variables( $less_variables, $instance ) {
		$maps_widget = new SiteOrigin_Widget_GoogleMap_Widget();
		$global_settings = $maps_widget->get_global_settings();

		if ( ! empty( $global_settings['map_consent'] ) && ! empty( $global_settings['map_consent_design'] ) ) {
			$map_content_settings = array(
				'background',
				'text',
			);

			foreach ( $map_content_settings as $setting ) {
				if ( ! empty( $global_settings['map_consent_design'][ $setting ] ) && is_array( $global_settings['map_consent_design'][ $setting ] ) ) {

					foreach ( $global_settings['map_consent_design'][ $setting ] as $style => $value ) {
						if ( ! empty( $value ) ) {

							$less_variables[ 'map_consent_notice_' . $setting . '_' . $style ] = $value;
						}
					}
				}
			}
		}

		return $less_variables;
	}

	public function enqueue_front_end_scripts( $instance, $widget ) {
		if ( ! empty( $instance['center_user_location'] ) ) {
			wp_enqueue_script( 'siteorigin-premium-map-user-location' );
		}
	}

}
