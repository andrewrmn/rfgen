<?php

class SiteOrigin_Premium_Options {

	private $messages;

	function __construct(){
		add_action( 'admin_menu', array($this, 'add_admin_page'), 9 );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );
		add_action( 'load-siteorigin_page_siteorigin-premium-license', array( $this, 'save_premium_license' ) );

		add_action( 'wp_ajax_so_premium_change_status', array( $this, 'change_status_action' ) );
		
		add_action( 'wp_ajax_so_premium_addon_settings_form', array( $this, 'get_addon_settings_form' ) );
		add_action( 'wp_ajax_so_premium_addon_settings_save', array( $this, 'addon_settings_save' ) );

		$this->messages = array();
	}

	static function single(){
		static $single;
		return empty( $single ) ? $single = new self() : $single;
	}

	/**
	 * Add the options page
	 */
	function add_admin_page(){

		if ( empty ( $GLOBALS['admin_page_hooks']['siteorigin'] ) ) {
			add_menu_page(
				__( 'SiteOrigin', 'siteorigin-premium' ),
				__( 'SiteOrigin', 'siteorigin-premium' ),
				'manage_options',
				'siteorigin',
				'',
				SiteOrigin_Premium::dir_url( __FILE__ ) . '../img/menu-icon.svg',
				66
			);
		}

		add_submenu_page(
			'siteorigin',
			__('Premium Addons', 'siteorigin-premium'),
			__('Premium Addons', 'siteorigin-premium'),
			'manage_options',
			'siteorigin-premium-addons',
			array( $this, 'render_addons_page' )
		);

		add_submenu_page(
			'siteorigin',
			__('Premium License', 'siteorigin-premium'),
			__('Premium License', 'siteorigin-premium'),
			'manage_options',
			'siteorigin-premium-license',
			array( $this, 'render_license_page' )
		);

		// Prevent empty "SiteOrigin" menu item by removing main menu page.
		// This has to be done after submenu items are added, or the entire menu will disappear.
		remove_submenu_page( 'siteorigin', 'siteorigin' );
	}

	/**
	 * Enqueue the admin scripts for the premium settings page
	 *
	 * @param $prefix
	 */
	function enqueue_admin_scripts( $prefix ){
		wp_enqueue_script( 'siteorigin-premium-trunk-animation', SiteOrigin_Premium::dir_url( __FILE__ ) . 'js/trunk-animation' . SiteOrigin_Premium::$js_suffix . '.js', array( 'jquery' ), SITEORIGIN_PREMIUM_VERSION );

		if( $prefix == 'siteorigin_page_siteorigin-premium-license' || $prefix == 'siteorigin_page_siteorigin-premium-addons' ) {
			wp_enqueue_style( 'siteorigin-premium-admin', SiteOrigin_Premium::dir_url(__FILE__) . 'css/admin.css', array(), SITEORIGIN_PREMIUM_VERSION );
		}

		if( $prefix == 'siteorigin_page_siteorigin-premium-addons' ) {
			wp_enqueue_script( 'vimeo', 'https://player.vimeo.com/api/player.js', array(), '2.7.0' );
			wp_enqueue_script( 'siteorigin-premium-trianglify', SiteOrigin_Premium::dir_url(__FILE__) . 'js/trianglify' . SiteOrigin_Premium::$js_suffix . '.js', array( 'jquery' ), SITEORIGIN_PREMIUM_VERSION );
			wp_enqueue_script( 'siteorigin-premium-addons', SiteOrigin_Premium::dir_url(__FILE__) . 'js/addons' . SiteOrigin_Premium::$js_suffix . '.js', array( 'jquery' ), SITEORIGIN_PREMIUM_VERSION );

			wp_localize_script(
				'siteorigin-premium-addons',
				'soPremiumAddons',
				array(
					'settingsForm' => array(
						'error' => __( 'Error loading form! Please try again later.', 'siteorigin-premium' ),
					),
				)
			);
		}
	}

	/**
	 * Save the premium license
	 */
	function save_premium_license(){
		// Save the settings
		if( ! empty( $_POST ) && check_admin_referer( 'save_siteorigin_premium' ) ) {
			// Save the settings
			$settings_raw = !empty($_POST['siteorigin_premium']) ? $_POST['siteorigin_premium'] : array();
			$license_key = !empty( $settings_raw['key'] ) ? sanitize_text_field( $settings_raw['key'] ) : '';

			$license = new SiteOrigin_Premium_License( $license_key );
			// This should check the license key validity
			if( ! empty( $license_key ) ) {
				// Check if the user has submitted a partially hidden license key.
				if ( strpos( $license_key, '***' ) !== false ) {
					return;
				}

				$status = $license->check_license_key();
				
				$notices = SiteOrigin_Premium_Admin_Notices::single();
				$notices->clear_notices();
				if ( $notices->has_notice( $status ) ) {
					$notices->activate_notice( $status );
				}
			}
			else {
				update_option( 'siteorigin_premium_license_status', '' );
			}

			if( ! empty( $_POST['activate'] ) ) {
				$activate_status = $license->activate_license();

				if( $activate_status == SiteOrigin_Premium_License::STATUS_INACTIVE ) {
					$this->messages[] = array(
						'type' => 'error',
						'message' => __( 'There was a problem trying to activate your license.', 'siteorigin-premium' ),
					);
				}
				else if( $activate_status == SiteOrigin_Premium_License::STATUS_INVALID ) {
					$this->messages[] = array(
						'type' => 'error',
						'message' => __( 'Your license couldn\'t be activated because the key is not valid.', 'siteorigin-premium' ),
					);
				}
				else if( $activate_status == 'no_activations_left' ) {
					$this->messages[] = array(
						'type' => 'error',
						'message' => __( 'License could not be activated. It already in use on too many sites.', 'siteorigin-premium' ) .
									 ' ' .
									 sprintf(
									 	__('Manage licenses on the <a href="%s" target="_blank" rel="noopener noreferrer">SiteOrigin Dashboard</a>.', 'siteorigin-premium'),
									    'https://siteorigin.com/dashboard/?dashboard_tab=order_history'
									 ),
					);
				}
				else {
					$this->messages[] = array(
						'type' => 'updated',
						'message' => __( 'Your license has been activated.', 'siteorigin-premium' ),
					);
				}
			}

			// Store all the options
			update_option( 'siteorigin_premium_key', $license_key );

			if( get_option( 'siteorigin_premium_key' ) != $license_key ) {
				update_option( 'siteorigin_premium_license_status', '' );
			}
		}
	}

	/**
	 * Render the options page
	 */
	function render_license_page(){
		$key = get_option( 'siteorigin_premium_key', '' );
		$license = new SiteOrigin_Premium_License( $key );
		$license_active = $license->is_active();

		// Don't show the full valid license key.
		if ( $license_active ) {
			$cut_off = strlen( $key ) - 4;
			$key = substr_replace( $key, str_repeat( '*', $cut_off ), 0, $cut_off );
		}

		include SiteOrigin_Premium::dir_path(__FILE__) . 'tpl/license-page.php';
	}

	function render_addons_page(){
		// Include all the addons
		$addons = array(
			'plugin' => array(),
			'theme' => array(),
		);

		$filter = empty( $_GET['filter'] ) ? '' : $_GET['filter'];

		$default_headers = array(
			'Name' => 'Plugin Name',
			'Description' => 'Description',
			'Documentation' => 'Documentation',
			'Tags' => 'Tags',
			'Video' => 'Video',
		);

		foreach( $addons as $section => $section_addons ) {

			$folder = SiteOrigin_Premium::dir_path( __FILE__ ) . '../addons/' . $section . '/';

			foreach( glob( $folder . '*/*.php' ) as $filename ) {
				$p = pathinfo( $filename );
				$addon_id = $section . '/' . $p['filename'];
				$theme_support_id = $p['filename'];

				if( $section == 'theme' && ! current_theme_supports( 'siteorigin-premium-' . $theme_support_id ) ) {
					// These theme doesn't support this feature.
					continue;
				}
				
				$data = get_file_data( $filename, $default_headers );
				
				// Need to explicitly call `translate` for file headers.
				foreach ( array( 'Name', 'Description', 'Tags' ) as $field ) {
					$data[ $field ] = translate( $data[ $field ], 'siteorigin-premium' );
				}
				
				$data['ID'] = $addon_id;
				$data['File'] = $filename;
				$data['Type'] = 'plugin';

				// Remove SiteOrigin from the start of the addon name
				$data['Name'] = str_replace( 'SiteOrigin ', '', $data['Name'] );

				if( $section == 'plugin' ) {
					$data['CanEnable'] = true;
					$data['Active'] = SiteOrigin_Premium::single()->is_addon_active( $addon_id );
				}
				else {

					$theme_supports = get_theme_support( 'siteorigin-premium-' . $theme_support_id );

					if( !empty( $theme_supports ) ) {
						$support = current( get_theme_support( 'siteorigin-premium-' . $theme_support_id ) );
						$data['Active'] = !empty( $support['enabled'] );

						// We can enable/disable this addon if the theme mod is known.
						if( !empty( $support['theme_mod'] ) || !empty( $support['siteorigin_setting'] ) ) {
							$data['CanEnable'] = true;
						}
					}
					else {
						$data['Active'] = false;
						$data['CanEnable'] = false;
					}
				}
				
				/** @var SiteOrigin_Premium_Form $settings_form */
				$settings_form = null;
				$addon = SiteOrigin_Premium::single()->load_addon( $addon_id );
				if ( ! empty( $addon ) && method_exists( $addon, 'get_settings_form' ) ) {
					$settings_form = $addon->get_settings_form();
				}
				if ( ! empty( $settings_form ) ) {
					$form_url         = add_query_arg(
						array(
							'id'     => $addon_id,
							'action' => 'so_premium_addon_settings_form',
						),
						admin_url( 'admin-ajax.php' )
					);
					$data['form_url'] = wp_nonce_url( $form_url, 'display-addon-settings-form' );
					
					// Enqueue scripts and styles for the form fields.
					ob_start();
					$settings_form->form( array() );
					ob_get_clean();
				}
				$data['has_settings'] = ! empty( $settings_form );

				$addons[$section][$addon_id] = $data;
			}
		}

		$action_url = add_query_arg( array(
			'action' => 'so_premium_change_status',
		), admin_url( 'admin-ajax.php' ) );

		$action_url = wp_nonce_url( $action_url, 'change_status' );

		include SiteOrigin_Premium::dir_path(__FILE__) . 'tpl/addons-page.php';
	}
	
	function get_addon_settings_form() {
		if( ! wp_verify_nonce($_GET['_wpnonce'], 'display-addon-settings-form') ) {
			exit();
		}
		
		if( ! current_user_can( 'manage_options' ) ) {
			exit();
		}
		
		$addon_id = empty( $_GET['id'] ) ? false : $_GET['id'];
		
		if( ! SiteOrigin_Premium::single()->is_addon_active( $addon_id ) ) {
			exit();
		}
		
		$action_url = admin_url( 'admin-ajax.php' );
		$action_url = add_query_arg( array(
			'id' => $addon_id,
			'action' => 'so_premium_addon_settings_save',
		), $action_url );
		$action_url = wp_nonce_url( $action_url, 'save-premium-addon-settings' );
		
		$value = $this->get_settings( $addon_id );
		
		/** @var SiteOrigin_Premium_Form $settings_form */
		$settings_form = null;
		$addon = SiteOrigin_Premium::single()->load_addon( $addon_id );
		if ( ! empty( $addon ) && method_exists( $addon, 'get_settings_form' ) ) {
			
			$settings_form = $addon->get_settings_form();
			?>
			<form method="post" action="<?php echo esc_url( $action_url ) ?>" target="so-premium-addon-settings-save">
				<?php $settings_form->form( $value ) ?>
			</form>
			<?php
		}
		
		exit();
	}
	
	function addon_settings_save() {
		if( ! wp_verify_nonce( $_GET['_wpnonce'], 'save-premium-addon-settings' ) ) {
			exit();
		}
		
		if( ! current_user_can( 'manage_options' ) ) {
			exit();
		}
		
		$addon_id = empty( $_GET['id'] ) ? false : $_GET['id'];
		/** @var SiteOrigin_Premium_Form $settings_form */
		$settings_form = null;
		$addon = SiteOrigin_Premium::single()->load_addon( $addon_id );
		if ( empty( $addon ) || ! method_exists( $addon, 'get_settings_form' ) ) {
			exit();
		}
		$settings_form = $addon->get_settings_form();
		
		$new_form_values = array_values( $_POST );
		$old_form_values = $this->get_settings( $addon_id );
		
		$new_form_values = $settings_form->update( array_shift( $new_form_values ), $old_form_values );
		
		unset( $new_form_values['_sow_form_id'] );
		unset( $new_form_values['_sow_form_timestamp'] );
		update_option( 'so_premium_addon_settings[' . $addon_id . ']', $new_form_values );
	}
	
	function get_settings( $addon_id ) {
		$values = get_option( 'so_premium_addon_settings[' . $addon_id . ']', array() );
		
		/** @var SiteOrigin_Premium_Form $settings_form */
		$settings_form = null;
		$addon = SiteOrigin_Premium::single()->load_addon( $addon_id );
		if ( ! empty( $addon ) && method_exists( $addon, 'get_settings_form' ) ) {
			$settings_form = $addon->get_settings_form();
			// Add in the defaults
			$values = $settings_form->add_defaults( $settings_form->form_options(), $values );
		}
		
		return $values;
	}

	function display_key_message( $license_active ){
		
		if( ! $license_active ) {
			$this->messages[] = array(
				'type' => 'error',
				'message' => __( "You're using SiteOrigin Premium in development mode. Add and activate your license key to change to production mode.", 'siteorigin-premium' ) .
			                 ' ' .
			                 __( "Development mode uses slower raw files, but they're ideal when you're still developing a site.", 'siteorigin-premium' )
			);
		}

		if( empty($this->messages) ) return;

		foreach( $this->messages as $message ) {
			?>
			<div class="<?php echo $message['type'] ?>">
				<p>
					<?php echo $message['message'] ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Change the status of an addon
	 */
	function change_status_action(){
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'change_status' ) ) {
			exit();
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			exit();
		}

		if( !isset( $_POST['id'] ) || !isset( $_POST['section'] ) || !isset( $_POST['status'] ) ) exit();

		$id = stripslashes( $_POST['id'] );
		$section = stripslashes( $_POST['section'] );
		$status = (bool) stripslashes( $_POST['status'] );

		list( $addon_section, $addon_id ) = explode( '/', $id, 2 );

		// The section should be the same from both places
		if( $section !== $addon_section ) exit();

		if( $section == 'theme' ) {
			// This needs to be changed via the theme mod
			$support = current( get_theme_support( 'siteorigin-premium-' . $addon_id ) );
			if( !empty( $support['theme_mod'] ) ) {
				$theme_mod = $support['theme_mod'];
				if( $theme_mod[0] == '!' ) {
					// The ! means we want the mod to be the opposite
					set_theme_mod( substr( $theme_mod, 1 ), ! $status );
				} else {
					set_theme_mod( $theme_mod, $status );
				}
			}
			else if( !empty( $support['siteorigin_setting'] ) || class_exists('SiteOrigin_Settings') ) {
				$setting_key = $support['siteorigin_setting'];
				$settings = SiteOrigin_Settings::single();
				if( $setting_key[0] == '!' ) {
					// The ! means we want the mod to be the opposite
					$settings->set( substr( $setting_key, 1 ), ! $status );
				} else {
					$settings->set( $setting_key, $status );
				}
			}

		} else {
			SiteOrigin_Premium::single()->set_addon_active( $id, $status );
			
			// Check if we need to activate the required plugins/widgets
			if($status && !empty($_POST['activate_required'])) {
				$data = get_file_data(
					SiteOrigin_Premium::dir_path( __FILE__ ) . '../addons/' . $addon_section . '/' . $addon_id . '/' . $addon_id . '.php',
					array(
						'Requires' => 'Requires'
					)
				);
				if( !empty($data['Requires']) ){
					$requires = explode(',', $data['Requires']);
					foreach(array_map('trim', $requires) as $r){
						@ list($plugin, $widget) = explode('/', $r);
						
						if( ! is_plugin_active($plugin . '/' . $plugin . '.php') && current_user_can( 'activate_plugins' ) ){
							activate_plugin($plugin . '/' . $plugin . '.php');
						}
						
						if(
							$plugin == 'so-widgets-bundle' &&
							!empty($widget) &&
							current_user_can( apply_filters( 'siteorigin_widgets_admin_menu_capability', 'manage_options' ) ) &&
							class_exists('SiteOrigin_Widgets_Bundle')
						) {
							SiteOrigin_Widgets_Bundle::single()->activate_widget($widget);
						}
					}
				}
			}
		}

		header( 'content-type: application/json' );

		SiteOrigin_Premium::single()->load_addon( $addon_section . '/' . $addon_id );
		$submenu_links = apply_filters( 'siteorigin_premium_addon_submenu_links-' . $addon_section . '/' . $addon_id, array() );

		if( $status ) {
			// This has been activated
			$action_links = apply_filters( 'siteorigin_premium_addon_action_links-' . $addon_section . '/' . $addon_id, array() );

			echo json_encode( array(
				'status' => 'enabled',
				'action_links' => array_values( $action_links ),
				'submenu_links' => array_values( $submenu_links )
			) );
		}
		else {
			// This has been deactivated
			echo json_encode( array(
				'status' => 'disabled',
				'submenu_links' => array_values( $submenu_links )
			) );
		}

		exit();
	}

}

SiteOrigin_Premium_Options::single();
