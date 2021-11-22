<?php
/*
Plugin Name: SiteOrigin Mirror Widgets
Description: Create a widget once, use it everywhere. Update it and the changes reflect in all instances of the widget.
Version: 1.0.0
Author: SiteOrigin
Author URI: https://siteorigin.com
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Documentation: https://siteorigin.com/premium-documentation/plugin-addons/mirror-widgets/
Tags: Page Builder, Widgets Bundle
Requires: siteorigin-panels, so-widgets-bundle
*/

/**
 * Class SiteOrigin_Premium_Plugin_Mirror_Widgets
 */
class SiteOrigin_Premium_Plugin_Mirror_Widgets {
	
	const POST_TYPE = 'so_mirror_widget';
	
	function __construct() {
		if ( !( class_exists( 'SiteOrigin_Widgets_Bundle' ) && class_exists( 'SiteOrigin_Panels' ) ) ) {
			return;
		}

		add_action( 'init', array( $this, 'register_post_type' ) );

		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'add_shortcode_column' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'add_shortcode_column_content' ), 10, 2 );
		add_action( 'add_meta_boxes_' . self::POST_TYPE, array( $this, 'add_shortcode_meta_box' ) );

		add_shortcode( 'mirror_widget', array( $this, 'do_shortcode' ) );

		add_filter( 'siteorigin_panels_settings', array( $this, 'enable_page_builder' ) );
		add_filter( 'siteorigin_premium_addon_section_link-plugin/mirror-widgets', array( $this, 'section_link' ) );
		
		add_filter( 'so_panels_show_add_new_dropdown_for_type', array( $this, 'hide_add_new_dropdown_for_mirror_widget' ), 10, 2 );
		add_filter( 'so_panels_show_classic_admin_notice', array( $this, 'hide_classic_admin_notice_for_mirror_widget' ) );
		
		add_filter( 'siteorigin_panels_builder_supports', array( $this, 'builder_supports' ), 10, 3 );

		add_filter( 'siteorigin_widgets_widget_folders', array( $this, 'add_mirror_widget' ) );
		add_action( 'after_setup_theme', array( $this, 'activate_mirror_widget' ), 12 );

		add_filter( 'siteorigin_panels_widgets', array( $this, 'remove_mirror_widget' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		add_action( 'admin_print_scripts-post-new.php', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_print_scripts-post.php', array( $this, 'enqueue_admin_scripts' ) );
	}
	
	/**
	 * Get the single instance
	 *
	 * @return SiteOrigin_Premium_Plugin_Mirror_Widgets
	 */
	static function single() {
		static $single;
		return empty( $single ) ? $single = new self() : $single;
	}
	
	/**
	 * Register the Mirror Widget post type.
	 */
	function register_post_type() {
		register_post_type( self::POST_TYPE, array(
			'labels' => array(
				'singular_name' => __( 'Mirror Widget', 'siteorigin-premium' ),
				'name' => __( 'Mirror Widgets', 'siteorigin-premium' ),
			),
			'description' => __( 'A widget which can be used anywhere, with all changes to the widget reflected wherever the widget is used.', 'siteorigin-premium' ),
			'public' => true,
			'publicly_queryable' => isset( $_GET['_panelsnonce'] ),
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_in_admin_bar' => false,
			'show_in_rest' => false,
			'menu_icon' => SiteOrigin_Premium::dir_url( __FILE__ ) . './assets/menu-icon.svg',
			'supports' => array( 'title', 'editor', 'revisions' ),
		) );
	}

	/**
	 * Add Shortcode Column to Mirror Widget Post Type.
	 */
	function add_shortcode_column( $columns ) {
		$columns['so_mirror_widget_shortcode'] = 'Shortcode';

		// The date column should be the last column.
		unset( $columns['date'] );
		$columns['date'] = 'Date';

		return $columns;
	}

	function add_shortcode_column_content( $column, $post_id ) {
		if ( $column == 'so_mirror_widget_shortcode' ) {
			?>
			<input type="text" onfocus="this.select()" value='[mirror_widget id="<?php echo intval( $post_id ); ?>"]' readonly>
			<?php
		}
	}

	/**
	 * Add Shortcode metabox to Mirror Widget Post Type.
	 */
	public function add_shortcode_meta_box( $post ) {
		add_meta_box(
			'so-wc-mirror-widgets-shortcode',
			__( 'Mirror Widget Shortcode', 'siteorigin-premium' ),
			array( $this, 'render_shortcode_post_meta_box' ),
			self::POST_TYPE,
			'side',
			'default'
		);
	}


	public function render_shortcode_post_meta_box( $post, $metabox ) {
		?>
		<input type="text" onfocus="this.select()" value='[mirror_widget id="<?php echo intval( $post->ID ); ?>"]' readonly>
		<?php
	}

	/**
	 * Handle displaying the [mirror_widget] shortcode.
	 */
	public function do_shortcode( $atts ) {
		if (
			! empty( $atts['id'] ) &&
			is_numeric( $atts['id'] ) &&
			get_post_meta( $atts['id'], 'panels_data', true )
		) {
			echo SiteOrigin_Panels::renderer()->render( $atts['id'] );
		} else {
			 _e( 'Error: Invalid Mirror Widget ID.', 'siteorigin-premium' );
		}
	}

	/**
	 * Enable Page Builder for mirror widgets.
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	function enable_page_builder( $settings ) {
		if( empty( $settings['post-types'] ) ) {
			$settings['post-types'] = array();
		}
		
		$settings['post-types'] = array_unique(
			array_merge( $settings['post-types'], array( self::POST_TYPE ) )
		);
		
		return $settings;
	}

	function section_link() {
		return array(
			'label' => esc_attr( 'Manage Mirror Widgets', 'siteorigin-premium' ),
			'url'   => admin_url( 'edit.php?post_type=so_mirror_widget' )
		);
	}
	
	/**
	 * Hide the 'Add new' dropdown when creating a new Mirror Widget. Mirror Widgets require the clasic Page Builder
	 * interface.
	 *
	 * @param $show
	 * @param $post_type
	 *
	 * @return bool
	 */
	function hide_add_new_dropdown_for_mirror_widget( $show, $post_type ) {
		return $post_type != self::POST_TYPE && $show;
	}
	
	/**
	 * Hide the 'Add new' dropdown when creating a new Mirror Widget. Mirror Widgets require the clasic Page Builder
	 * interface.
	 *
	 * @param $show
	 * @param $post_type
	 *
	 * @return bool
	 */
	function hide_classic_admin_notice_for_mirror_widget( $show ) {
		global $typenow;
		return $typenow != self::POST_TYPE && $show;
	}
	
	
	/**
	 * The Mirror Widget uses a stripped down version of Page Builder which only allows adding a single widget.
	 *
	 *
	 * @param $supports
	 * @param $post
	 * @param $panels_data
	 *
	 * @return array
	 */
	function builder_supports( $supports, $post ) {
		
		if( $post->post_type == self::POST_TYPE ) {
			
			$supports = array(
				'editRow' => true,
				'deleteRow' => true,
				'moveRow' => true,
				
				'addWidget' => true,
				'editWidget' => true,
				'deleteWidget' => true,
				'moveWidget' => true,
				
				'prebuilt' => true,
				'history' => true,
				'liveEditor' => true,
				'revertToEditor' => false,
			);
		}
		
		return $supports;
	}

	public static function get_mirror_widget_posts() {
		return get_posts(
			array(
				'numberposts' => -1,
				'post_type' => self::POST_TYPE,
			)
		);
	}
	
	public static function get_mirror_widget_names() {
		$mirror_posts = self::get_mirror_widget_posts();
		$mirror_widgets = array();
		foreach ( $mirror_posts as $mirror_widget ) {

			if ( empty( $mirror_widget->post_title ) ) {
				$name = $mirror_widget->post_type . '_' . $mirror_widget->post_name;
			} else {
				$name = $mirror_widget->post_title;
			}

			$mirror_widgets[ $mirror_widget->post_name ] = $name;
		}

		return $mirror_widgets;
	}

	public static function render_mirror_widget( $mirror_widget_name, $is_preview ) {
		$mirror_posts = self::get_mirror_widget_posts();

		foreach ( $mirror_posts as $mirror_post ) {
			if ( $mirror_post->post_name == $mirror_widget_name ) {
				$panels_data = get_post_meta( $mirror_post->ID, 'panels_data', true );
			}
		}

		if ( ! empty( $panels_data ) ) {
			$renderer = SiteOrigin_Panels::renderer();
			return $renderer->render( 'w' . uniqid(), true, $panels_data, $layout_data, $is_preview );
		}

		return '';
	}

	public function add_mirror_widget( $folders ) {
		$folders[] = plugin_dir_path( __FILE__ ) . 'inc/';

		return $folders;
	}

	public function activate_mirror_widget() {
		if ( class_exists( 'SiteOrigin_Widgets_Bundle' ) ) {
			$so_widgets_bundle = SiteOrigin_Widgets_Bundle::single();
			$so_widgets_bundle->activate_widget( 'mirror-widget' );
		}
	}

	public function remove_mirror_widget( $widgets ) {
		global $pagenow, $typenow;
		$is_mirror_post_type = ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && $typenow == self::POST_TYPE;
		if ( $is_mirror_post_type && ! empty( $widgets['SiteOrigin_Premium_Widget_Mirror_Widget'] ) ) {
			unset( $widgets['SiteOrigin_Premium_Widget_Mirror_Widget'] );
		}
		return $widgets;
	}

	public function enqueue_admin_scripts() {

		if ( ! is_admin()) {
			return;
		}

		wp_enqueue_style(
			'so-mirror-widget-admin',
			plugin_dir_url(__FILE__) . 'css/so-mirror-widgets.css'
		);
	}
}
