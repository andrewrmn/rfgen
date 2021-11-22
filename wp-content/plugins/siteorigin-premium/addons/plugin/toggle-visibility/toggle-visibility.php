<?php
/*
Plugin Name: SiteOrigin Toggle Visibility
Description: Toggle the visibility of Page Builder rows and widgets based on device.
Version: 1.0.0
Author: SiteOrigin
Author URI: https://siteorigin.com
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Documentation: https://siteorigin.com/premium-documentation/plugin-addons/toggle-visibility
Tags: Page Builder
Requires: siteorigin-panels
*/

class SiteOrigin_Premium_Plugin_Toggle_Visibility {

	function __construct() {
		add_filter( 'siteorigin_panels_row_style_groups', array($this, 'style_group'), 10, 3 );
		add_filter( 'siteorigin_panels_row_style_fields', array( $this, 'style_fields' ), 10, 3 );
		add_filter( 'siteorigin_panels_widget_style_groups', array($this, 'style_group'), 10, 3 );
		add_filter( 'siteorigin_panels_widget_style_fields', array($this, 'style_fields'), 10, 3 );
		add_filter( 'siteorigin_panels_css_object', array( $this, 'attributes' ), 10, 4 );
	}

	public static function single() {
		static $single;

		return empty( $single ) ? $single = new self() : $single;
	}

	function style_group( $groups, $post_id, $args ) {
		$groups['toggle'] = array(
			'name' => __( 'Toggle Visibility', 'siteorigin-premium' ),
			'priority' => 30
		);

		return $groups;
	}

	function style_fields( $fields, $post_id, $args ) {
		if ( current_filter() == 'siteorigin_panels_row_style_fields' ) {
			$fields['disable_row'] = array(
				// Adding empty 'name' field to avoid 'Undefined index' notices in PB due to always expecting
				// name 'field' in siteorigin-panels\inc\styles-admin.php:L145
				'name' => '',
				'label' => __( 'Hide Row', 'siteorigin-premium' ),
				'type' => 'checkbox',
				'group' => 'toggle',
				'description' => __( 'Disable row on all devices.', 'siteorigin-premium' ),
				'priority' => 10,
			);
		} else {
			$fields['disable_widget'] = array(
				'name' => '',
				'label' => __( 'Hide Widget', 'siteorigin-premium' ),
				'type' => 'checkbox',
				'group' => 'toggle',
				'description' => __( 'Disable widget on all devices.', 'siteorigin-premium' ),
				'priority' => 10,
			);
		}

		$fields['disable_desktop'] = array(
			'name' => '',
			'label' => __( 'Hide on Desktop', 'siteorigin-premium' ),
			'type' => 'checkbox',
			'group' => 'toggle',
			'priority' => 29,
		);
		$fields['disable_tablet'] = array(
			'name' => '',
			'label' => __( 'Hide on Tablet', 'siteorigin-premium' ),
			'type' => 'checkbox',
			'group' => 'toggle',
			'priority' => 30,
		);
		$fields['disable_mobile'] = array(
			'name' => '',
			'label' => __( 'Hide on Mobile', 'siteorigin-premium' ),
			'type' => 'checkbox',
			'group' => 'toggle',
			'priority' => 40,
		);

		return $fields;
	}

	// Row Style Field

	// Visiability styles output 
	function attributes( $css, $panels_data, $post_id, $layout_data ) {
		$panels_tablet_width = siteorigin_panels_setting( 'tablet-width' );
		$panels_mobile_width = siteorigin_panels_setting( 'mobile-width' );
		$desktop_breakpoint = ( $panels_tablet_width === '' ? $panels_mobile_width : $panels_tablet_width ) + 1;
		$tablet_min_width = $panels_mobile_width + 1;

		foreach ( $layout_data as $ri => $row ) {
			// Check if row is disabled on all devices
			if ( ! empty( $row['style']['disable_row'] ) ) {
				$css->add_row_css( $post_id, $ri, null, array(
					'display' => 'none',
				) );
				continue; // No need to proceed
			}

			// Check if row is disabled on desktop
			if ( ! empty( $row['style']['disable_desktop'] ) ) {
				$css->add_row_css( $post_id, $ri, null, array(
					'display' => 'none',
				), ":$desktop_breakpoint" );
			}

			// Check if row is disabled on tablet
			if ( ! empty( $row['style']['disable_tablet'] ) && $panels_tablet_width > $panels_mobile_width ) {
				$css->add_row_css( $post_id, $ri, null, array(
					'display' => 'none',
				), "$panels_tablet_width:$tablet_min_width" );
			}

			// Check if row is disabled on mobile
			if ( ! empty( $row['style']['disable_mobile'] ) ) {
				$css->add_row_css( $post_id, $ri, null, array(
					'display' => 'none',
				), $panels_mobile_width );
			}

			foreach ( $row['cells'] as $ci => $cell ) {
				foreach ( $cell['widgets'] as $wi => $widget ) {
					// Check if the widget is disabled on all devices
					if ( ! empty( $widget['panels_info']['style']['disable_widget'] ) ) {
						$css->add_widget_css( $post_id, $ri, $ci, $wi, '', array(
							'display' => 'none',
						) );
						continue; // No need to proceed
					}

					// Check if widet is disabled on desktop
					if ( ! empty( $widget['panels_info']['style']['disable_desktop'] ) ) {
						$css->add_widget_css( $post_id, $ri, $ci, $wi, null, array(
							'display' => 'none',
						), ":$desktop_breakpoint" );
					}

					// Check if widget is disabled on tablet
					if ( ! empty( $widget['panels_info']['style']['disable_tablet'] ) && $panels_tablet_width > $panels_mobile_width ) {
						$css->add_widget_css( $post_id, $ri, $ci, $wi, null, array(
							'display' => 'none',
						), "$panels_tablet_width:$tablet_min_width" );
					}

					// Check if widget is disabled on mobile
					if ( ! empty( $widget['panels_info']['style']['disable_mobile'] ) ) {
						$css->add_widget_css( $post_id, $ri, $ci, $wi, null, array(
							'display' => 'none',
						), $panels_mobile_width );
					}
				}
			}
		}
		return $css;
	}
}
