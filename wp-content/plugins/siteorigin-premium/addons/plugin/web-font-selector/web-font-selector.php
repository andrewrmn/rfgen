<?php
/*
Plugin Name: SiteOrigin Web Font Selector
Description: Choose from hundreds of beautiful web fonts right in the visual editor.
Version: 1.0.0
Author: SiteOrigin
Author URI: https://siteorigin.com
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Documentation: https://siteorigin.com/premium-documentation/plugin-addons/web-font-selector/
Tags: CSS, Widgets Bundle
Video: 314963142
Requires: so-css, so-widgets-bundle/editor
*/

class SiteOrigin_Premium_Plugin_Web_Font_Selector {

	function __construct() {
		add_filter( 'siteorigin_css_property_controllers', array( $this, 'modify_font_controls' ) );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_socss_control_scripts'), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_end_scripts' ) );
		add_action( 'wp_enqueue_editor', array( $this, 'enqueue_tinymce_plugin_scripts' ) );
		// Specifically for Widgets Bundle previews.
		add_action( 'siteorigin_widgets_render_preview_sow-editor', array( $this, 'enqueue_front_end_scripts' ) );
		
		add_filter( 'mce_external_plugins', array( $this, 'add_font_selector_tinymce_plugin' ), 15 );
		add_filter( 'mce_buttons', array( $this, 'add_font_selector_tinymce_button' ), 15 );
	}

	static function single() {
		static $single;
		return empty( $single ) ? $single = new self() : $single;
	}
	
	static function get_font_modules() {
		
		static $font_modules;
		
		if ( empty( $font_modules ) ) {
			$font_modules = include dirname(__FILE__) . '/fonts/font_modules.php';
		}
		
		static $fonts;
		if ( empty ( $fonts ) ) {
			foreach ( $font_modules as $module_name => $module ) {
				$module['fonts'] = include dirname(__FILE__) . '/fonts/' . $module_name . '.php';
				$fonts[ $module_name ] = $module;
			}
		}
		
		return $fonts;
	}

	function modify_font_controls($controls) {
		
		$fonts = self::get_font_modules();

		$ctrls = $controls['text']['controllers'];

		foreach ( $ctrls as $key => $ctrl ) {
			if ( $ctrl['type'] == 'font_select' ) {
				$ctrl['args']['modules'] = $fonts;
				$ctrls[ $key ] = $ctrl;
			}
		}
		$controls['text']['controllers'] = $ctrls;

		return $controls;
	}
	
	function add_font_selector_tinymce_plugin( $plugins ) {
		$plugins['so-premium-font-selector'] = plugin_dir_url( __FILE__ ) . 'js/so-premium-tmce-fonts-plugin' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js';
		return $plugins;
	}
	
	function add_font_selector_tinymce_button( $buttons ) {
		$buttons[] = 'so-premium-font-selector';
		return $buttons;
	}
	
	function enqueue_tinymce_plugin_scripts() {

		$this->enqueue_front_end_scripts();
		$this->enqueue_web_font_selector();

		wp_localize_script(
			'siteorigin-premium-tinymce-font-selector-plugin',
			'soPremiumFonts',
			array(
				'font_modules' => self::get_font_modules(),
				'placeholder_text' => __( 'Select Web Font', 'siteorigin-premium' ),
			)
		);
	}

	function enqueue_socss_control_scripts( $page ) {
		if ( $page != 'appearance_page_so_custom_css' ) {
			return;
		}
		
		$this->enqueue_web_font_selector();

		wp_enqueue_script(
			'font-select-control',
			plugin_dir_url(__FILE__) . 'js/font-select-control' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
			array( 'siteorigin-custom-css' ),
			SITEORIGIN_PREMIUM_VERSION,
			true
		);
	}
	
	private function enqueue_web_font_selector() {
		
		// We'll use chosen for the font selector
		wp_enqueue_script(
			'siteorigin-premium-chosen',
			plugin_dir_url(__FILE__) . 'js/lib/chosen/chosen.jquery' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
			array('jquery'),
			'1.4.2'
		);
		wp_enqueue_style(
			'siteorigin-premium-chosen',
			plugin_dir_url(__FILE__) . 'js/lib/chosen/chosen' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.css',
			array(),
			'1.4.2'
		);
		
		wp_enqueue_style(
			'so-premium-tinymce-chosen',
			plugin_dir_url(__FILE__) . 'js/so-premium-tinymce-chosen.css',
			array(),
			SITEORIGIN_PREMIUM_VERSION
		);
		
		wp_enqueue_script( 'web-font-loader', '//ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js', array(), null, true );
		
		wp_enqueue_script(
			'web-font-selector',
			plugin_dir_url(__FILE__) . 'js/web-font-selector' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
			array( 'jquery', 'siteorigin-premium-chosen' ),
			SITEORIGIN_PREMIUM_VERSION,
			true
		);
	}
	
	public function enqueue_front_end_scripts() {
		wp_enqueue_script(
			'siteorigin-premium-web-font-importer',
			plugin_dir_url( __FILE__ ) . 'js/so-premium-tmce-fonts-importer' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
			array(),
			SITEORIGIN_PREMIUM_VERSION,
			true
		);
		
		wp_localize_script(
			'siteorigin-premium-web-font-importer',
			'soPremiumFonts',
			array(
				'font_modules' => self::get_font_modules(),
			)
		);
	}
}
