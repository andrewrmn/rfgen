<?php
/*
Widget Name: Product data tabs
Description: Tabs displaying the product description, additional information and reviews.
Author: SiteOrigin
Author URI: https://siteorigin.com
Documentation: https://siteorigin.com/premium-documentation/plugin-addons/woocommerce-templates/
*/

if ( ! class_exists( 'SiteOrigin_Widget' ) ) {
	return;
}

class SiteOrigin_Premium_WooCommerce_Output_Product_Data_Tabs extends SiteOrigin_Widget {
	private $changes = array();

	public function __construct() {
		parent::__construct(
			'so-wc-output-product-data-tabs',
			__( 'Product data tabs', 'siteorigin-premium' ),
			array(
				'description' => __( 'Tabs displaying the product description, additional information and reviews.', 'siteorigin-premium' ),
				'has_preview' => false,
			),
			array(),
			array(
				'tabs' => array(
					'label' => __( 'Tabs', 'siteorigin-premium' ),
					'type' => 'section',
					'fields' => array(
						'description' => array(
							'label' => __( 'Description', 'siteorigin-premium' ),
							'type' => 'section',
							'hide' => true,
							'fields' => array(
								'label' => array(
									'type' => 'text',
									'label' => __( 'Label', 'siteorigin-premium' ),
									'description' => __( 'If empty, the default label will be used.', 'siteorigin-premium' ),
								),
								'status' => array(
									'type' => 'checkbox',
									'label' => __( 'Enabled', 'siteorigin-premium' ),
									'default' => true,
								),
								'order' => array(
									'type' => 'number',
									'label' => __( 'Order', 'siteorigin-premium' ),
									'default' => 10,
								),
							),
						),
						'additional_information' => array(
							'label' => __( 'Additional', 'siteorigin-premium' ),
							'type' => 'section',
							'hide' => true,
							'fields' => array(
								'label' => array(
									'type' => 'text',
									'label' => __( 'Label', 'siteorigin-premium' ),
									'description' => __( 'Leaving this field empty will result in the default label being used.', 'siteorigin-premium' ),
								),
								'status' => array(
									'type' => 'checkbox',
									'label' => __( 'Enabled', 'siteorigin-premium' ),
									'default' => true,
								),
								'order' => array(
									'type' => 'number',
									'label' => __( 'Order', 'siteorigin-premium' ),
									'default' => 20,
								),
							),
						),
						'reviews' => array(
							'label' => __( 'Reviews', 'siteorigin-premium' ),
							'type' => 'section',
							'hide' => true,
							'fields' => array(
								'label' => array(
									'type' => 'text',
									'label' => __( 'Label', 'siteorigin-premium' ),
									'description' => __( 'Leaving this field empty will result in the default label being used.', 'siteorigin-premium' ),
								),
								'status' => array(
									'type' => 'checkbox',
									'label' => __( 'Enabled', 'siteorigin-premium' ),
									'default' => true,
								),
								'order' => array(
									'type' => 'number',
									'label' => __( 'Order', 'siteorigin-premium' ),
									'default' => 30,
								),
							),
						),
					),
				),
			),
			plugin_dir_path(__FILE__)
		);

	}

	private function prepare_changes( $tabs ) {
		if ( ! empty( $tabs ) ) {
			unset( $tabs['so_field_container_state'] );
			foreach ( $tabs as $name => $settings ) {
				if ( ! $settings['status'] ) {
					$this->changes['remove'][ $name ] = true;
					continue;
				}

				if ( ! empty( $settings['label'] ) ) {
					$this->changes['rename'][ $name ] = $settings['label'];
				}

				$this->changes['order'][ $name ] = $settings['order'];		
			}
		}
	}

	public function add_changes( $tabs ) {
		if ( ! empty( $this->changes ) ) {
			foreach ( $tabs as $name => $data ) {
				if ( isset( $this->changes['remove'] ) && isset( $this->changes['remove'][ $name ] ) ) {
					$this->changes['remove'][ $name ] = $tabs[ $name ];
					unset( $tabs[ $name ] );
					continue;
				}

				if ( isset( $this->changes['rename'] ) && isset( $this->changes['rename'][ $name ] ) ) {
					$old_title = $tabs[ $name ]['title'];
					$tabs[ $name ]['title'] = $this->changes['rename'][ $name ];
					$this->changes['rename'][ $name ] = $old_title;
				}

				$old_priority = $tabs[ $name ]['priority'];
				$tabs[ $name ]['priority'] = $this->changes['order'][ $name ];
				$this->changes['order'][ $name ] = $old_priority;
			}
		}

		return $tabs;
	}

	public function remove_changes( $tabs ) {
		if ( ! empty( $this->changes ) ) {
			foreach ( $this->changes['remove'] as $name => $data ) {
				if ( isset( $this->changes['remove'] ) && isset( $this->changes['remove'][ $name ] ) ) {
					$tabs[ $name ] = $this->changes['remove'][ $name ];
				}
			}

			foreach ( $tabs as $name => $data ) {
				if ( isset( $this->changes['rename'] ) && isset( $this->changes['rename'][ $name ] ) ) {
					$tabs[ $name ]['title'] = $this->changes['rename'][ $name ];
				}

				$tabs[ $name ]['priority'] = $this->changes['order'][ $name ];
			}
		}

		return $tabs;
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( function_exists( 'woocommerce_output_product_data_tabs' ) ) {
			if ( empty( $instance['tabs'] ) ) {
				woocommerce_output_product_data_tabs();
			} else {
				$this->prepare_changes( $instance['tabs'] );
				add_filter( 'woocommerce_product_tabs', array( $this, 'add_changes' ) );
				woocommerce_output_product_data_tabs();
				add_filter( 'woocommerce_product_tabs', array( $this, 'remove_changes' ) );
			}
		}

		echo $args['after_widget'];
	}

}

register_widget( 'SiteOrigin_Premium_WooCommerce_Output_Product_Data_Tabs' );
