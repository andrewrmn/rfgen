<?php
/*
 * Elementor Integration File
 * Elementor Site: http://learndash.com/
 * Original Integration Author : Fel Jun Palawan
 * Version: $Id$
 */
if ( !class_exists('WLM_OTHER_INTEGRATION_ELEMENTOR') ) {

	class WLM_OTHER_INTEGRATION_ELEMENTOR {
		private $settings = [];
		public  $plugin_active = false;

		function __construct() {
			//check if Elementor LMS is active
			$active_plugins  = wlm_get_active_plugins();
			if ( in_array( 'Elementor', $active_plugins ) || isset($active_plugins['elementor/elementor.php']) || is_plugin_active('elementor/elementor.php') ) {
				$this->plugin_active = true;
			}
		}

	    function load_hooks() {
			if ( $this->plugin_active ) {
				add_action( 'elementor/element/before_section_start', array( $this, 'add_wlm_section_options' ), 9, 3 );
				add_filter( 'elementor/frontend/builder_content_data', array( $this, 'builder_content_data' ), 9, 1 );
			}
	    }

	function builder_content_data( $data ) {
		static $current_user;
		static $user_levels;
		if ( !is_array( $data ) ) return $data;
		if ( !function_exists('wlmapi_get_member_levels') ) return $data;

		if ( is_null($current_user) ) {
			$current_user = wp_get_current_user();
		}

		if ( isset( $current_user->ID ) && $current_user->ID ) {
			if ( is_null( $user_levels ) ) {
					$user_levels = wlmapi_get_member_levels( $current_user->ID );
					$user_levels = is_array( $user_levels ) ? array_keys($user_levels) : [];
					foreach ( $user_levels as $key => $level_id ){						
					    if ( wishlistmember_instance()->LevelUnConfirmed( $level_id, $current_user->ID ) ) {
        					unset( $user_levels[$key] );
					    }
					}
			}
		}
		if( !is_array( $user_levels ) ) {
			$user_levels = array();
		}

		foreach ( $data as $key => $value ) {
			if ( $value['elType'] == 'section' ) {
				$condition_list = ['all','nonmembers','logged_in','ina','notin'];
				$condition_type = isset( $value['settings']['wlm_level_condition'] ) ? $value['settings']['wlm_level_condition'] : 'all';
				$condition_type = !in_array( $condition_type, $condition_list ) ? 'all' : $condition_type;

				$section_level = isset( $value['settings']['wlm_level'] ) ? $value['settings']['wlm_level'] : [];

				$display = true;
				if ( $condition_type == 'nonmembers' ) {
					// nonmembers means not logged-in
					$display = empty( $current_user->ID );
				} elseif ( $condition_type == 'logged_in' ) {
					// logged-in
					$display = !empty( $current_user->ID );
				} elseif ( $condition_type == 'ina' ) {
					// member logged-in and in a level
					if( empty( $current_user->ID ) ) {
						$display = false;
					} else {
						$section_level = !is_array($section_level) ? (array) $section_level : $section_level;
						$in_levels = array_intersect($section_level,$user_levels);
						if ( !empty( $section_level ) && count($in_levels) <= 0 ) {
							$display = false;
						}
					}
				} elseif ( $condition_type == 'notin' ) {
					// member logged-in and not in levels
					if( empty( $current_user->ID ) ) {
						$display = false;
					} else {
						$section_level = !is_array($section_level) ? (array) $section_level : $section_level;
						$in_levels = array_intersect($section_level,$user_levels);
						if ( ( !empty( $section_level ) && count($in_levels) >= 1 ) ) {
							$display = false;
						}
					}
				}

				if ( $display === false ) {
					unset($data[$key]);
				} else {
					$data[$key]['elements'] = $this->builder_content_data( $value['elements'] );
				}
			} else {
				$data[$key]['elements'] = $this->builder_content_data( $value['elements'] );
			}
		}
		return $data;
	}

	function add_wlm_section_options( $element, $section_id, $args ) {
		if ( 'section' === $element->get_name() && 'section_custom_css_pro' === $section_id && function_exists('wlmapi_get_levels') ) {

			$levels = wlmapi_get_levels();
			$levels = isset( $levels['levels']['level'] ) ? $levels['levels']['level'] : [];

			$level_options = [];
			foreach ( $levels as $key => $value ) {
				$level_options[$value['id']] = $value['name'];
			}

		   	$element->start_controls_section(
		   		'custom_section',
		   		[
		   			'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
		   			'label' => __( 'WishList Member', 'wishlist-member-elements' ),
		   		]
		   	);
			$element->add_control(
				'wlm_level_condition',
				[
					'label'   		=> __( 'Show this section to:', 'wishlist-member-elements' ),
					'label_block'	=> true,
					'type'    		=> \Elementor\Controls_Manager::SELECT,
					'default' 		=> 'all',
					'options' => [
						'all'        => __( 'Everybody', 'wishlist-member' ),
						'nonmembers' => __( 'Not Logged-in', 'wishlist-member' ),
						'logged_in'  => __( 'Logged-in', 'wishlist-member' ),
						'ina'        => __( 'Members in Membership Level(s)', 'wishlist-member' ),
						'notin'      => __( 'Members not in Membership Level(s)', 'wishlist-member' ),
					],
				]
			);
			$element->add_control(
				'wlm_level',
				[
					'label' => __( 'Membership Level(s)', 'wishlist-member-elements' ),
					'show_label' => false,
					'label_block'	=> true,
					'type' => \Elementor\Controls_Manager::SELECT2,
					'multiple' => true,
					'options' 		=> $level_options,
					'condition' 	=> [
						'wlm_level_condition' => ['ina','notin'],
					],
				]
			);

		   	$element->end_controls_section();
		}
	}
	}
}

$WLMElementorInstance = new WLM_OTHER_INTEGRATION_ELEMENTOR();
$WLMElementorInstance->load_hooks();