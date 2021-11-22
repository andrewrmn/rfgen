<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class SiteOrigin_Premium_Recommended_Plugins
 *
 *  This class loads TGM Plugin Action into memory and recommends our plugins if they aren't installed.
 *
 */
class SiteOrigin_Premium_Recommended_Plugins {

	function __construct() {

		if ( apply_filters( 'siteorigin_premium_use_tgmpa', true ) ) {
			require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';
			add_action( 'tgmpa_register', array( $this, 'check_for_recommended_plugins' ) );
		}
	}

	public function check_for_recommended_plugins() {

		// Array of recommended plugins for TGMPA
		$plugins = array(
			array(
				'name' => 'SiteOrigin Widgets Bundle',
				'slug' => 'so-widgets-bundle',
				'required'  => false,
			),

			array(
				'name' => 'SiteOrigin Page Builder',
				'slug' => 'siteorigin-panels',
				'required'  => false,
			),

			array(
				'name' => 'SiteOrigin CSS',
				'slug' => 'so-css',
				'required'  => false,
			),
		);

		// Array of configuration settings for TGMPA
		$config = array(
			'id'           => 'siteorigin-premium-recommended-plugins',
			'menu'         => 'siteorigin-premium-install-recommended',
			'parent_slug'  => 'siteorigin-premium-addons',
			'capability'   => 'manage_options',
			'strings'     => array(
				'notice_can_install_recommended'  => _n_noop(
					/* translators: 1: plugin name(s). */
					'SiteOrigin Premium recommends the following plugin: %1$s.',
					'SiteOrigin Premium recommends the following plugins: %1$s.',
					'siteorigin-premium'
				),
				'notice_ask_to_update'            => _n_noop(
					/* translators: 1: plugin name(s). */
					'The following plugin needs to be updated to its latest version to ensure maximum compatibility with SiteOrigin Premium: %1$s.',
					'The following plugins need to be updated to their latest version to ensure maximum compatibility with SiteOrigin Premium: %1$s.',
					'siteorigin-premium'
				),
			),
		);

		tgmpa( $plugins, $config );
	}

	static function single(){
		static $single;
		return empty( $single ) ? $single = new self() : $single;
	}

}
