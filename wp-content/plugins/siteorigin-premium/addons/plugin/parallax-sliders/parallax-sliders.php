<?php
/*
Plugin Name: SiteOrigin Parallax Sliders
Description: Adds parallax background option to slider widgets.
Version: 1.0.0
Author: SiteOrigin
Author URI: https://siteorigin.com
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Documentation: https://siteorigin.com/premium-documentation/plugin-addons/parallax-sliders/
Tags: Widgets Bundle
Video: 314963213
Requires: so-widgets-bundle/slider, so-widgets-bundle/layout-slider, so-widgets-bundle/hero
*/

class SiteOrigin_Premium_Plugin_Parallax_Sliders {

	function __construct(){
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );

		add_filter( 'siteorigin_widgets_form_options_sow-slider', array( $this, 'widget_forms' ), 10, 2 );
		add_filter( 'siteorigin_widgets_form_options_sow-hero', array( $this, 'widget_forms' ), 10, 2 );
		add_filter( 'siteorigin_widgets_form_options_sow-layout-slider', array( $this, 'widget_forms' ), 10, 2 );

		add_filter( 'siteorigin_widgets_slider_wrapper_attributes', array( $this, 'slider_wrapper_attributes' ), 10, 3 );
		add_filter( 'siteorigin_widgets_slider_overlay_attributes', array( $this, 'slider_overlay_attributes' ), 10, 3 );

		if ( $this->use_new_parallax() ) {
			add_action( 'siteorigin_widgets_slider_before_contents', array( $this, 'add_new_parallax_image' ) );
		}

		add_filter( 'siteorigin_widgets_less_sow-hero', array( $this, 'disable_fixed_slider_mobile' ), 10, 2 );
		add_filter( 'siteorigin_widgets_less_sow-layout-slider', array( $this, 'disable_fixed_slider_mobile' ), 10, 2 );
	}

	static function single(){
		static $single;
		return empty( $single ) ? $single = new self() : $single;
	}

	function use_new_parallax() {
		$parallax_status = true;

		// If Page Builder is active, use the parallax Type setting value as the Parallax status.
		if ( function_exists( 'siteorigin_panels_setting' ) && ! empty( siteorigin_panels_setting( 'parallax-type' ) ) ) {
			$parallax_status = siteorigin_panels_setting( 'parallax-type' ) == 'modern';
		}

		return apply_filters( 'siteorigin_parallax_sliders_use_new_parallax', $parallax_status );
	}

	function register_assets() {
		if ( $this->use_new_parallax() ) {
			if ( ! wp_script_is( 'simpleParallax', 'registered' ) ) {
				wp_register_script(
					'simpleParallax',
					SiteOrigin_Premium::dir_url( __FILE__ ) . 'js/simpleparallax' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
					array( 'jquery', 'siteorigin-parallax-slider-addon' ),
					'5.5.1'
				);

				wp_register_script(
					'siteorigin-parallax-slider-addon',
					SiteOrigin_Premium::dir_url( __FILE__ ) . 'js/parallax-sliders' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
					array(),
					SITEORIGIN_PREMIUM_VERSION
				);

				wp_localize_script(
					'simpleParallax',
					'parallaxStyles',
					apply_filters(
						'siteorigin_parallax_sliders_fallback_settings',
						array(
							'mobile-breakpoint'       => '780px',
							'disable-parallax-mobile' => false,
							'delay'                   => 0.4,
							'scale'                   => 1.1,
						)
					)
				);
			}
		} elseif ( ! wp_script_is( 'siteorigin-parallax', 'registered' ) ) {
			wp_register_script(
				'siteorigin-parallax',
				SiteOrigin_Premium::dir_url( __FILE__ ) . 'js/siteorigin-parallax' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
				array( 'jquery' ),
				SITEORIGIN_PREMIUM_VERSION
			);
		}
	}

	function slider_wrapper_attributes( $attributes, $frame, $background ){
		if ( empty( $background['image'] ) || ! isset( $background['image-sizing'] ) || $background['image-sizing'] == 'cover' ) {
			return $attributes;
		}
	
		if( isset( $background['opacity'] ) && $background['opacity'] != 1 ) {
			return $attributes;
		}

		if( $background['image-sizing'] == 'parallax' ){
			if ( $this->use_new_parallax() ) {
				unset( $attributes['style'] );
			} else {
				if ( empty( $background['image-width'] ) || empty( $background['image-height'] ) ) {
					return $attributes;
				}
				unset( $attributes['style'] );

				$attributes['data-siteorigin-parallax'] = json_encode( array(
					'backgroundUrl' => $background['image'],
					'backgroundSize' => array(
						$background['image-width'],
						$background['image-height'],
					),
					'backgroundSizing' => 'scaled',
				) );
				wp_enqueue_script( 'siteorigin-parallax' );
			}
		} elseif ( $background['image-sizing'] == 'fixed' ){
			$attributes['style'][] = 'background-size: cover';
			$attributes['style'][] = 'background-attachment: fixed';
		}

		return $attributes;
	}

	function slider_overlay_attributes( $attributes, $frame, $background ){
		if( empty( $background['image'] ) || ! isset( $background['opacity'] ) || $background['opacity'] == 1 ) {
			return $attributes;
		}
		
		if ( ! isset( $background['image-sizing'] ) || $background['image-sizing'] == 'cover' ) {
			return $attributes;
		}

		if( $background['image-sizing'] == 'parallax' ){
			if ( $this->use_new_parallax() ) {
				unset( $attributes['style'] );
			} else {
				if ( empty( $background['image-width'] ) || empty( $background['image-height'] ) ) {
					return $attributes;
				}
				unset( $attributes['style'] );

				$attributes['data-siteorigin-parallax'] = json_encode( array(
					'backgroundUrl' => $background['image'],
					'backgroundSize' => array(
						$background['image-width'],
						$background['image-height'],
					),
					'backgroundSizing' => 'scaled',
				) );
				wp_enqueue_script( 'siteorigin-parallax' );
			}
		} elseif ( $background['image-sizing'] == 'fixed' ){
			$attributes['style'][] = 'background-size: cover';
			$attributes['style'][] = 'background-attachment: fixed';
		}

		return $attributes;
	}

	function widget_forms( $form, $widget ){
		switch( get_class( $widget ) ) {
			case 'SiteOrigin_Widget_Hero_Widget':
			case 'SiteOrigin_Widget_LayoutSlider_Widget':
				if( isset( $form['frames']['fields']['background']['fields']['image_type']['options'] ) ) {
					$form['frames']['fields']['background']['fields']['image_type']['options']['parallax'] = __( 'Parallax', 'siteorigin-premium' );
					$form['frames']['fields']['background']['fields']['image_type']['options']['fixed'] = __( 'Fixed', 'siteorigin-premium' );
				}
				break;

			case 'SiteOrigin_Widget_Slider_Widget' :
				if( isset( $form['frames']['fields']['background_image_type']['options'] ) ) {
					$form['frames']['fields']['background_image_type']['options']['parallax'] = __( 'Parallax', 'siteorigin-premium' );
				}
				break;
		}

		return $form;
	}

	function add_new_parallax_image( $frame ) {
		// Is this the Slider widget without a foreground image?
		if ( ! empty( $frame['background_image_type'] ) && $frame['background_image_type'] == 'parallax' ) {
			// If the slider doesn't have a foreground, just enqueue simpleParallax.
			if ( empty( $frame['foreground_image'] ) && empty( $frame['foreground_image_fallback'] ) ) {
				wp_enqueue_script( 'simpleParallax' );
				return;
			}
			$url_field = $frame['background_image'];
			$url_fallback_field = $frame['background_image_fallback'];
			$opacity = null;
		} elseif ( isset( $frame['background'] ) && $frame['background']['image_type'] == 'parallax' ) {
			$url_field = $frame['background']['image'];
			$url_fallback_field = $frame['background']['image_fallback'];
			$opacity = 'style="opacity: ' . ( intval( $frame['background']['opacity'] ) / 100 ) . '"';
		} else {
			return;
		}

		$url = siteorigin_widgets_get_attachment_image_src( $url_field, 'full' )[0];
		if ( empty( $url ) && ! empty( $url_fallback_field ) ) {
			$url = $url_fallback_field;
		}

		if ( ! empty( $url ) ) {
			wp_enqueue_script( 'simpleParallax' );
			echo '<img src="' . esc_url( $url ) . '" data-siteorigin-parallax="true" '. $opacity . '>';
		}
	}

	// Disable fixed Sliders on mobile devices due to an issue on iOS.
	function disable_fixed_slider_mobile( $less, $instance ) {
		if ( ! empty( $instance['image_type'] ) && $instance['image_type'] == 'fixed' ) {
			$less .= '
				@media (max-width: @responsive_breakpoint) {
					.sow-slider-image-fixed {
						background-attachment: scroll !important;
					}
				}';
		} 
		return $less;
	}
}
