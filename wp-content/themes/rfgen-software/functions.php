<?php
/**
 * Twenty Nineteen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */
/**
 * Twenty Nineteen only works in WordPress 4.7 or later.
 */

// update_option( 'siteurl', 'http://local.rfgen' );
// update_option( 'home', 'http://local.rfgen' );


if (version_compare($GLOBALS['wp_version'], '4.7', '<')) {
    require get_template_directory() . '/inc/back-compat.php';
    return;
}

if (!function_exists('twentynineteen_setup')) :

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function twentynineteen_setup() {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Twenty Nineteen, use a find and replace
         * to change 'twentynineteen' to the name of your theme in all the template files.
         */
        load_theme_textdomain('twentynineteen', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(1568, 9999);

        // This theme uses wp_nav_menu() in two locations.
        register_nav_menus(
                array(
                    'primary' => __('Primary', 'twentynineteen'),
                //'footer' => __( 'Footer Menu', 'twentynineteen' ),
                //'social' => __( 'Social Links Menu', 'twentynineteen' ),
                )
        );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
                'html5',
                array(
                    'search-form',
                    'comment-form',
                    'comment-list',
                    'gallery',
                    'caption',
                    'script',
                    'style',
                    'navigation-widgets',
                )
        );

        /**
         * Add support for core custom logo.
         *
         * @link https://codex.wordpress.org/Theme_Logo
         */
        add_theme_support(
                'custom-logo',
                array(
                    'height' => 190,
                    'width' => 190,
                    'flex-width' => false,
                    'flex-height' => false,
                )
        );

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        // Add support for Block Styles.
        add_theme_support('wp-block-styles');

        // Add support for full and wide align images.
        add_theme_support('align-wide');

        // Add support for editor styles.
        add_theme_support('editor-styles');

        // Enqueue editor styles.
        //add_editor_style( 'style-editor.css' );
        // Add custom editor font sizes.
        add_theme_support(
                'editor-font-sizes',
                array(
                    array(
                        'name' => __('Small', 'twentynineteen'),
                        'shortName' => __('S', 'twentynineteen'),
                        'size' => 19.5,
                        'slug' => 'small',
                    ),
                    array(
                        'name' => __('Normal', 'twentynineteen'),
                        'shortName' => __('M', 'twentynineteen'),
                        'size' => 22,
                        'slug' => 'normal',
                    ),
                    array(
                        'name' => __('Large', 'twentynineteen'),
                        'shortName' => __('L', 'twentynineteen'),
                        'size' => 36.5,
                        'slug' => 'large',
                    ),
                    array(
                        'name' => __('Huge', 'twentynineteen'),
                        'shortName' => __('XL', 'twentynineteen'),
                        'size' => 49.5,
                        'slug' => 'huge',
                    ),
                )
        );

        // Editor color palette.
        add_theme_support(
                'editor-color-palette',
                array(
                    array(
                        'name' => 'default' === get_theme_mod('primary_color') ? __('Blue', 'twentynineteen') : null,
                        'slug' => 'primary',
                        'color' => twentynineteen_hsl_hex('default' === get_theme_mod('primary_color') ? 199 : get_theme_mod('primary_color_hue', 199), 100, 33),
                    ),
                    array(
                        'name' => 'default' === get_theme_mod('primary_color') ? __('Dark Blue', 'twentynineteen') : null,
                        'slug' => 'secondary',
                        'color' => twentynineteen_hsl_hex('default' === get_theme_mod('primary_color') ? 199 : get_theme_mod('primary_color_hue', 199), 100, 23),
                    ),
                    array(
                        'name' => __('Dark Gray', 'twentynineteen'),
                        'slug' => 'dark-gray',
                        'color' => '#111',
                    ),
                    array(
                        'name' => __('Light Gray', 'twentynineteen'),
                        'slug' => 'light-gray',
                        'color' => '#767676',
                    ),
                    array(
                        'name' => __('White', 'twentynineteen'),
                        'slug' => 'white',
                        'color' => '#FFF',
                    ),
                )
        );

        // Add support for responsive embedded content.
        add_theme_support('responsive-embeds');

        // Add support for custom line height.
        add_theme_support('custom-line-height');
    }

endif;
add_action('after_setup_theme', 'twentynineteen_setup');

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function twentynineteen_widgets_init() {

    register_sidebar(
            array(
                'name' => __('Blog Sidebar', 'twentynineteen'),
                'id' => 'sidebar-1',
                'description' => __('Add widgets here to appear in your footer.', 'twentynineteen'),
                'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
                'after_widget' => '</div></div>',
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>',
            )
    );


    register_sidebar(
            array(
                'name' => __('Footer Widget 1', 'twentynineteen'),
                'id' => 'footer-1',
                'description' => __('Add widgets here to appear in your footer.', 'twentynineteen'),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h6 class="widget-title">',
                'after_title' => '</h6>',
            )
    );


	register_sidebar(
		array(
			'name'          => __( 'Footer Widget 2', 'twentynineteen' ),
			'id'            => 'footer-2',
			'description'   => __( 'Add widgets here to appear in your footer.', 'twentynineteen' ),
			'before_widget' => '<div id="%2$s" class="widget %2$s">',
			'after_widget'  => '</div></div></div>',
			'before_title'  => '<a data-toggle="collapse" href="#collapseMenu-1" role="button" aria-expanded="false" aria-controls="%1$s">',
			'after_title'   => '</a><div id="collapseMenu-1" class="widget %2$s collapse"><div class="card card-body">',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Widget 3', 'twentynineteen' ),
			'id'            => 'footer-3',
			'description'   => __( 'Add widgets here to appear in your footer.', 'twentynineteen' ),
			'before_widget' => '<div id="%2$s" class="widget %2$s">',
			'after_widget'  => '</div></div></div>',
			'before_title'  => '<a data-toggle="collapse" href="#collapseMenu-2" role="button" aria-expanded="false" aria-controls="%1$s">',
			'after_title'   => '</a><div id="collapseMenu-2" class="widget %2$s collapse"><div class="card card-body">',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Widget 4', 'twentynineteen' ),
			'id'            => 'footer-4',
			'description'   => __( 'Add widgets here to appear in your footer.', 'twentynineteen' ),
			'before_widget' => '<div id="%2$s" class="widget %2$s">',
			'after_widget'  => '</div></div></div>',
			'before_title'  => '<a data-toggle="collapse" href="#collapseMenu-3" role="button" aria-expanded="false" aria-controls="%1$s">',
			'after_title'   => '</a><div id="collapseMenu-3" class="widget %2$s collapse"><div class="card card-body">',
		)
	);

	register_sidebar(
            array(
                'name' => __('Footer Widget 5', 'twentynineteen'),
                'id' => 'footer-5',
                'description' => __('Add widgets here to appear in your footer.', 'twentynineteen'),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h6 class="widget-title d-none">',
                'after_title' => '</h6>',
            )
    );

}

add_action('widgets_init', 'twentynineteen_widgets_init');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width Content width.
 */
function twentynineteen_content_width() {
    // This variable is intended to be overruled from themes.
    // Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    $GLOBALS['content_width'] = apply_filters('twentynineteen_content_width', 640);
}

add_action('after_setup_theme', 'twentynineteen_content_width', 0);

/**
 * Enqueue scripts and styles.
 */
function twentynineteen_scripts() {
	wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-block-style' );

    wp_enqueue_style('twentynineteen-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));

    wp_style_add_data('twentynineteen-style', 'rtl', 'replace');

    wp_enqueue_style('twentynineteen-style-new', get_template_directory_uri() . '/style-new.css', array('twentynineteen-style'), '1.1');
    wp_enqueue_style('twentynineteen-font-awesome', get_template_directory_uri() . '/css/all.css', array('twentynineteen-style'), '1.1');
    wp_enqueue_style('twentynineteen-style-theme', get_template_directory_uri() . '/css/style-theme.css', array('twentynineteen-style'), '1.1');
    wp_enqueue_style('twentynineteen-fancybox', get_template_directory_uri() . '/css/jquery.fancybox.min.css', array('twentynineteen-style'), '1.1');
    wp_enqueue_style('twentynineteen-bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array('twentynineteen-style'), '1.1');
	wp_enqueue_style('twentynineteen-responsive-new', get_template_directory_uri() . '/css/responsive-new.css', array('twentynineteen-style'), '1.1');
    wp_enqueue_style('twentynineteen-responsive', get_template_directory_uri() . '/css/responsive.css', array('twentynineteen-style'), '1.1');
    wp_enqueue_style('twentynineteen-waitMe', get_template_directory_uri() . '/css/waitMe.min.css', array('twentynineteen-style'), '1.1');



    if (has_nav_menu('menu-1')) {
        wp_enqueue_script('twentynineteen-priority-menu', get_theme_file_uri('/js/priority-menu.js'), array(), '20181214', true);
        wp_enqueue_script('twentynineteen-touch-navigation', get_theme_file_uri('/js/touch-keyboard-navigation.js'), array(), '20181231', true);
    }

    wp_enqueue_style('twentynineteen-print-style', get_template_directory_uri() . '/print.css', array(), wp_get_theme()->get('Version'), 'print');

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }


	   wp_deregister_script('jquery');
    wp_enqueue_script('jquery', get_theme_file_uri('/js/jquery-min.js'), array(), '1.1', true);




    wp_enqueue_script('twentynineteen-popper', get_theme_file_uri('/js/popper.min.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-bootstrap', get_theme_file_uri('/js/bootstrap.min.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-ie10-viewport', get_theme_file_uri('/js/ie10-viewport-bug-workaround.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-ie-emulation', get_theme_file_uri('/js/ie-emulation-modes-warning.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-custom', get_theme_file_uri('/js/custom.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-fancybox', get_theme_file_uri('/js/jquery.fancybox.min.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-owl-carousel', get_theme_file_uri('/js/owl.carousel.js'), array('jquery'), '1.1', true);
    wp_enqueue_script('twentynineteen-easing', get_theme_file_uri('/js/jquery.easing.min.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-stellarnav', get_theme_file_uri('/js/stellarnav.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-accordion', get_theme_file_uri('/js/accordion.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-jquery-nav', get_theme_file_uri('/js/jquery.nav.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-matchHeight', get_theme_file_uri('/js/jquery.matchHeight-min.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-waitMe', get_theme_file_uri('/js/waitMe.min.js'), array(), '1.1', true);
    wp_enqueue_script('twentynineteen-loadmore', get_theme_file_uri('/js/loadmore.js'), array(), '1.1', true);
  	wp_enqueue_script('twentynineteen-owl', get_theme_file_uri('/js/owl.carousel.js'), array(), '1.1', true);
  	wp_enqueue_script('twentynineteen-waypoints', get_theme_file_uri('/js/jquery.waypoints.min.js'), array(), '1.1', true);
  	wp_enqueue_script('twentynineteen-countup', get_theme_file_uri('/js/jquery.countup.js'), array(), '1.1', true);
  	wp_enqueue_script('twentynineteen-jquery-mobile-mega', get_theme_file_uri('/js/jquery-mobile-mega-menu-min.js'), array(), '1.1', true);

    wp_enqueue_style('rfgen-css', get_template_directory_uri() . '/build/main.css', array('twentynineteen-style'), '1.1');
    wp_enqueue_script( 'rfgen-js', get_template_directory_uri() . '/build/main.js', array('jquery'), '1.0.0', true );

}

add_action('wp_enqueue_scripts', 'twentynineteen_scripts');

/**
 * Fix skip link focus in IE11.
 *
 * This does not enqueue the script because it is tiny and because it is only for IE11,
 * thus it does not warrant having an entire dedicated blocking script being loaded.
 *
 * @link https://git.io/vWdr2
 */
function twentynineteen_skip_link_focus_fix() {
    // The following is minified via `terser --compress --mangle -- js/skip-link-focus-fix.js`.
    ?>
    <script>
        /(trident|msie)/i.test(navigator.userAgent) && document.getElementById && window.addEventListener && window.addEventListener("hashchange", function () {
            var t, e = location.hash.substring(1);
            /^[A-z0-9_-]+$/.test(e) && (t = document.getElementById(e)) && (/^(?:a|select|input|button|textarea)$/i.test(t.tagName) || (t.tabIndex = -1), t.focus())
        }, !1);
    </script>
    <?php
}

add_action('wp_print_footer_scripts', 'twentynineteen_skip_link_focus_fix');

/**
 * Enqueue supplemental block editor styles.
 */
function twentynineteen_editor_customizer_styles() {

    wp_enqueue_style('twentynineteen-editor-customizer-styles', get_theme_file_uri('/style-editor-customizer.css'), false, '1.1', 'all');

    if ('custom' === get_theme_mod('primary_color')) {
        // Include color patterns.
        require_once get_parent_theme_file_path('/inc/color-patterns.php');
        wp_add_inline_style('twentynineteen-editor-customizer-styles', twentynineteen_custom_colors_css());
    }
}

add_action('enqueue_block_editor_assets', 'twentynineteen_editor_customizer_styles');

/**
 * Display custom color CSS in customizer and on frontend.
 */
function twentynineteen_colors_css_wrap() {

    // Only include custom colors in customizer or frontend.
    if ((!is_customize_preview() && 'default' === get_theme_mod('primary_color', 'default') ) || is_admin()) {
        return;
    }

    require_once get_parent_theme_file_path('/inc/color-patterns.php');

    $primary_color = 199;
    if ('default' !== get_theme_mod('primary_color', 'default')) {
        $primary_color = get_theme_mod('primary_color_hue', 199);
    }
    ?>

    <style type="text/css" id="custom-theme-colors" <?php echo is_customize_preview() ? 'data-hue="' . absint($primary_color) . '"' : ''; ?>>
        <?php echo twentynineteen_custom_colors_css(); ?>
    </style>
    <?php
}

add_action('wp_head', 'twentynineteen_colors_css_wrap');

/**
 * SVG Icons class.
 */
require get_template_directory() . '/classes/class-twentynineteen-svg-icons.php';

/**
 * Custom Comment Walker template.
 */
require get_template_directory() . '/classes/class-twentynineteen-walker-comment.php';

/**
 * Common theme functions.
 */
require get_template_directory() . '/inc/helper-functions.php';

/**
 * SVG Icons related functions.
 */
require get_template_directory() . '/inc/icon-functions.php';

/**
 * Enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom template tags for the theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Theme Customizer.
 */
require get_template_directory() . '/inc/theme-customizer.php';

/**
 * Block Patterns.
 */
require get_template_directory() . '/inc/block-patterns.php';

function crunchify_disable_comment_url($fields) {
    unset($fields['url']);
    return $fields;
}

add_filter('comment_form_default_fields', 'crunchify_disable_comment_url');

function wpb_move_comment_field_to_bottom($fields) {
    $comment_field = $fields['comment'];
    unset($fields['comment']);
    $fields['comment'] = $comment_field;
    return $fields;
}

add_filter('comment_form_fields', 'wpb_move_comment_field_to_bottom');

// Changing excerpt length
function new_excerpt_length($length) {
    return 20;
}

add_filter('excerpt_length', 'new_excerpt_length');

// Changing excerpt more
function new_excerpt_more($more) {
    return '...';
}

add_filter('excerpt_more', 'new_excerpt_more');

function disable_create_newpost() {
    global $wp_post_types;
    $wp_post_types['sitesettings']->cap->create_posts = 'do_not_allow';
    remove_post_type_support('sitesettings', 'title');
    remove_post_type_support('sitesettings', 'editor');
}

add_action('init', 'disable_create_newpost');

add_filter('post_row_actions', 'remove_row_actions', 10, 1);

function remove_row_actions($actions) {
    if (get_post_type() === 'sitesettings') {
        //  unset($actions['edit']);
        unset($actions['view']);
        unset($actions['trash']);
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}

/*
 * Filter Media Taxonomy
 */

function filter_media() {
	if(isset($_POST['term_id'])){
		$term_id = explode(',', $_POST['term_id']);
		$tax_query = array(array('taxonomy' => 'media_category', 'field' => 'term_id', 'terms' => $term_id));
	}
	$off = (isset($_POST['offset']) && $_POST['offset'] > 0) ? $_POST['offset'] : 0;
	$posts_per_page = 12;
    $ajaxposts = new WP_Query([
        'post_type' => 'media',
		'posts_per_page' => $posts_per_page,
		'offset' => $off,
        'tax_query' => isset($tax_query) ? $tax_query : NULL,
		'orderby' => 'publish_date',
		'order' => 'DESC', 'meta_query' => array(
    array(
      'key' => 'show_in_front_page',
      'value' => '1',
      'compare' => '==' // not really needed, this is the default
    )
  )
    ]);
    $response = '';

	if ($ajaxposts->have_posts()) {
		$post_count = count($ajaxposts->posts);
		ob_start();
		while ($ajaxposts->have_posts()) : $ajaxposts->the_post();
			$response .= get_template_part('media/content', 'mediatax');
		endwhile;
		$response = ob_get_contents();
		ob_end_clean();
    } else {
		$post_count = 0;
		$response = "<li class='col-md-12'>Sorry, we couldn't find what you were looking for, you may find one of these articles pertinent.</li>";
    }

    echo json_encode(array('rows' => $post_count, 'blogs' => $response));
    exit;
}

add_action('wp_ajax_filter_media', 'filter_media');
add_action('wp_ajax_nopriv_filter_media', 'filter_media');

/*
 * Media List Search
 */
add_action('wp_ajax_media_search', 'media_search');
add_action('wp_ajax_nopriv_media_search', 'media_search');

function media_search() {

    $search_key = $_POST['search_key'];
	$off = (isset($_POST['offset']) && $_POST['offset'] > 0) ? $_POST['offset'] : 0;
	$posts_per_page = 12;
    $ajaxposts = new WP_Query([
        'post_type' => 'media',
		'posts_per_page' => $posts_per_page,
		'offset' => $off,
        'posts_per_page' => -1,
        's' => $search_key,
		'orderby' => 'publish_date',
		'order' => 'DESC', 'meta_query' => array(
    array(
      'key' => 'show_in_front_page',
      'value' => '1',
      'compare' => '==' // not really needed, this is the default
    )
  )
    ]);

    $response = '';

    if ($ajaxposts->have_posts()) {
		$post_count = count($ajaxposts->posts);
		ob_start();
		while ($ajaxposts->have_posts()) : $ajaxposts->the_post();
			$response .= get_template_part('media/content', 'mediatax');
		endwhile;
		$response = ob_get_contents();
		ob_end_clean();
    } else {
		$post_count = 0;
		$response = "<li class='col-md-12'>Sorry, we couldn't find what you were looking for.</li>";
    }

    echo json_encode(array('rows' => $post_count, 'blogs' => $response));
    exit;
}

/*
 * Filter Post Taxonomy
 */
add_action('wp_ajax_filter_blog', 'filter_blog');
add_action('wp_ajax_nopriv_filter_blog', 'filter_blog');

function filter_blog() {
	$_none = array(array('key' => 'hide_form_list', 'value' => true, 'compare' => '!='));
    $term_id = array_filter($_POST['term_id']);
    $search_key = $_POST['search_key'];
    $off = (isset($_POST['offset']) && $_POST['offset'] > 0) ? $_POST['offset'] : 0;
    $posts_per_page = ($off > 0) ? 6 : 9;
    if (!empty($term_id)):
        $tax_ar = array();
        foreach ($term_id as $tm) {
            $tax_ar[] = array('taxonomy' => 'category', 'field' => 'term_id', 'terms' => $tm);
        }
        $post_ar = array(
            'post_type' => 'post',
			'meta_query' => $_none,
            'posts_per_page' => $posts_per_page,
            'offset' => $off,
            'tax_query' => array($tax_ar),
            'orderby' => 'publish_date',
            'order' => 'DESC'
        );
        if (!empty($search_key)) {
            $post_ar['s'] = $search_key;
        }
        $ajaxposts = new WP_Query($post_ar);
    else:
        $post_ar = array(
            'post_type' => 'post',
			'meta_query' => $_none,
            'posts_per_page' => $posts_per_page,
            'offset' => $off,
            'orderby' => 'publish_date',
            'order' => 'DESC');
        if (!empty($search_key)) {
            $post_ar['s'] = $search_key;
        }
        $ajaxposts = new WP_Query($post_ar);
    endif;
//echo "Last SQL-Query: {$ajaxposts->request}";
    $response = '';
    $tops = 0;
    if ($ajaxposts->have_posts()) {
        $post_count = count($ajaxposts->posts);
        ob_start();
        while ($ajaxposts->have_posts()) : $ajaxposts->the_post();
            $response .= get_template_part('content', 'blogs');
        endwhile;
        $response = ob_get_contents();
        ob_end_clean();
    } else {
        if (!empty($term_id)) {
            $post_ar = array(
                'post_type' => 'post',
				'meta_query' => $_none,
                'posts_per_page' => $posts_per_page,
                'offset' => $off,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => $term_id
                    )),
                'orderby' => 'publish_date',
                'order' => 'DESC');
            if (!empty($search_key)) {
                $post_ar['s'] = $search_key;
            }
            if (isset($_POST['exlude']) && !empty($_POST['exlude'])) {
                $post_ar['post__not_in'] = explode(',', $_POST['exlude']);
            }
            $ajaxposts = new WP_Query($post_ar);
            if ($ajaxposts->have_posts()) {
                $post_count = count($ajaxposts->posts);
                ob_start();
                while ($ajaxposts->have_posts()) : $ajaxposts->the_post();
                    $response .= get_template_part('content', 'blogs');
                endwhile;
                $response = ob_get_contents();
                ob_end_clean();
            }
        } else {
            $tops = 1;
            $post_count = 0;
            $response = "<li class='col-md-12'>Sorry, we couldn't find what you were looking for.</li>";
        }
    }

    echo json_encode(array('rows' => $post_count, 'blogs' => $response, 'tops' => $tops));
    exit;
}
/*
* Blog Post slug modify
*/
	function golden_oak_web_design_blog_generate_rewrite_rules( $wp_rewrite ) {
  $new_rules = array(
    '(([^/]+/)*blog)/page/?([0-9]{1,})/?$' => 'index.php?pagename=$matches[1]&paged=$matches[3]',
    'blog/([^/]+)/?$' => 'index.php?post_type=post&name=$matches[1]',
    'blog/[^/]+/attachment/([^/]+)/?$' => 'index.php?post_type=post&attachment=$matches[1]',
    'blog/[^/]+/attachment/([^/]+)/trackback/?$' => 'index.php?post_type=post&attachment=$matches[1]&tb=1',
    'blog/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&attachment=$matches[1]&feed=$matches[2]',
    'blog/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&attachment=$matches[1]&feed=$matches[2]',
    'blog/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$' => 'index.php?post_type=post&attachment=$matches[1]&cpage=$matches[2]',
    'blog/[^/]+/attachment/([^/]+)/embed/?$' => 'index.php?post_type=post&attachment=$matches[1]&embed=true',
    'blog/[^/]+/embed/([^/]+)/?$' => 'index.php?post_type=post&attachment=$matches[1]&embed=true',
    'blog/([^/]+)/embed/?$' => 'index.php?post_type=post&name=$matches[1]&embed=true',
    'blog/[^/]+/([^/]+)/embed/?$' => 'index.php?post_type=post&attachment=$matches[1]&embed=true',
    'blog/([^/]+)/trackback/?$' => 'index.php?post_type=post&name=$matches[1]&tb=1',
    'blog/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&name=$matches[1]&feed=$matches[2]',
    'blog/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&name=$matches[1]&feed=$matches[2]',
    'blog/page/([0-9]{1,})/?$' => 'index.php?post_type=post&paged=$matches[1]',
    'blog/[^/]+/page/?([0-9]{1,})/?$' => 'index.php?post_type=post&name=$matches[1]&paged=$matches[2]',
    'blog/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?post_type=post&name=$matches[1]&paged=$matches[2]',
    'blog/([^/]+)/comment-page-([0-9]{1,})/?$' => 'index.php?post_type=post&name=$matches[1]&cpage=$matches[2]',
    'blog/([^/]+)(/[0-9]+)?/?$' => 'index.php?post_type=post&name=$matches[1]&page=$matches[2]',
    'blog/[^/]+/([^/]+)/?$' => 'index.php?post_type=post&attachment=$matches[1]',
    'blog/[^/]+/([^/]+)/trackback/?$' => 'index.php?post_type=post&attachment=$matches[1]&tb=1',
    'blog/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&attachment=$matches[1]&feed=$matches[2]',
    'blog/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&attachment=$matches[1]&feed=$matches[2]',
    'blog/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$' => 'index.php?post_type=post&attachment=$matches[1]&cpage=$matches[2]',
  );
  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
add_action( 'generate_rewrite_rules', 'golden_oak_web_design_blog_generate_rewrite_rules' );

function golden_oak_web_design_update_post_link( $post_link, $id = 0 ) {
  $post = get_post( $id );
  if( is_object( $post ) && $post->post_type == 'post' ) {
    return home_url( '/blog/' . $post->post_name );
  }
  return $post_link;
}
add_filter( 'post_link', 'golden_oak_web_design_update_post_link', 1, 3 );

function rfgen_project_rewrite_rule() {
	add_rewrite_rule( 'about/rfgen-leadership-team/([^/]+)', 'index.php?author_a=$matches[1]', 'top' );
	add_rewrite_rule( 'about/content-contributors/([^/]+)', 'index.php?author_b=$matches[1]', 'top' );
	add_rewrite_rule( 'about/consulting-services/enterprise-mobility-experts/([^/]+)', 'index.php?author_c=$matches[1]', 'top' );
	add_rewrite_rule( 'lp/([^/]+)/2$', 'index.php?node=$matches[1]', 'top' );
	add_rewrite_rule( 'research-library/([^/]+)/([^/]+)/([^/]+)$', 'index.php?cat_a=$matches[1]&cat_b=$matches[2]&post_slug=$matches[3]', 'top' );
	//add_rewrite_rule( 'research-library/([^/]+)/([^/]+)/([^/]+)/([^/]+)$', 'index.php?cat_b=$matches[2]&cat_c=$matches[3]&child=$matches[4]', 'top' );
}

add_action( 'init', 'rfgen_project_rewrite_rule' );

function rfgen_register_query_var( $vars ) {
    $vars[] = 'author_a';
	$vars[] = 'author_b';
	$vars[] = 'author_c';
	$vars[] = 'node';
	$vars[] = 'cat_a';
	$vars[] = 'cat_b';
	$vars[] = 'cat_c';
	//$vars[] = 'child';
	$vars[] = 'post_slug';
    return $vars;
}

add_filter( 'query_vars', 'rfgen_register_query_var' );

function wpdocs_load_page_template( $template ) {
	if ( !empty(get_query_var( 'author_a' )) || !empty(get_query_var( 'author_b' )) || !empty(get_query_var( 'author_c' )) ) {
		$template = get_template_directory() . '/team-template.php';
	}
	if(!empty(get_query_var( 'node' ))){
		$template = get_template_directory() . '/page-templates/sales-promotional-custom.php';
	}
	if(!empty(get_query_var( 'post_slug' ))){
		$sub_cat = get_term_by('slug', get_query_var( 'cat_b' ), 'media_category');
		$template = get_template_directory() . '/custom-media.php';
	}
	/*if(!empty(get_query_var( 'child' ))){
		$sub_cat = get_term_by('slug', get_query_var( 'cat_b' ), 'media_category');
		if(isset($sub_cat->term_id) && !empty($sub_cat->parent)){
			$template = get_template_directory() . '/custom-media-child.php';
		}
	}*/
    return $template;
}
add_filter( 'template_include', 'wpdocs_load_page_template' );

function seota_custom_dynamic_page_title($title) {
    // Return a custom document title for
    // the boat details custom page template
	global $wpdb;
	$pg_title = ' - RFgen';
	if ( !empty(get_query_var( 'author_a' )) || !empty(get_query_var( 'author_b' )) || !empty(get_query_var( 'author_c' )) ) {
		if(!empty(get_query_var( 'author_a' ))){
			$pg_title = ucwords(str_replace('-', ' ', get_query_var( 'author_a' ))) . $pg_title;
		}
		elseif(!empty(get_query_var( 'author_b' ))){
			$pg_title = ucwords(str_replace('-', ' ', get_query_var( 'author_b' ))) . $pg_title;
		}
		else{
			$pg_title = ucwords(str_replace('-', ' ', get_query_var( 'author_c' ))) . $pg_title;
		}
		return $pg_title;
	}
	elseif(!empty(get_query_var( 'node' ))){
		$pg_title = ucwords(str_replace('-', ' ', get_query_var( 'node' ))) . $pg_title;
		return $pg_title;
	}
	elseif(!empty(get_query_var( 'post_slug' ))){
		$sub_cat = get_term_by('slug', get_query_var( 'cat_b' ), 'media_category');
		$post_parent = 0;
		if(isset($sub_cat) || empty($sub_cat)){
			$arg_s = array('name' => get_query_var( 'cat_b' ), 'post_type' => 'media', 'post_status' => 'publish', 'numberposts' => 1);
			$top_posts = get_posts($arg_s);
			$args = array();
			if(isset($top_posts[0]->ID) && $top_posts[0]->ID > 0){
				$post_parent = $top_posts[0]->ID;
			}
		}
		$arg_s = array('name' => get_query_var( 'post_slug' ), 'post_type'   => 'media', 'post_parent' => $post_parent, 'post_status' => 'publish', 'numberposts' => 1);
		$my_posts = get_posts($arg_s);
		if(isset($my_posts[0]->ID) && $my_posts[0]->ID > 0){
			return $my_posts[0]->post_title . $pg_title;
		}
	}
	/*elseif(!empty(get_query_var( 'child' ))){
		$sub_cat = get_term_by('slug', get_query_var( 'cat_b' ), 'media_category');
		if(isset($sub_cat->term_id) && !empty($sub_cat->parent)){
			$post_parent = 0;
			if(!empty(get_query_var( 'cat_c' ))){
				$arg_s = array('name' => get_query_var( 'cat_c' ), 'post_type'   => 'media', 'post_status' => 'publish', 'numberposts' => 1);
				$top_posts = get_posts($arg_s);
				$args = array();
				if(isset($top_posts[0]->ID) && $top_posts[0]->ID > 0){
					$post_parent = $top_posts[0]->ID;
				}
			}
			$arg_s = array('name' => get_query_var( 'child' ), 'post_type'   => 'media', 'post_parent' => $post_parent, 'post_status' => 'publish', 'numberposts' => 1);
			$my_posts = get_posts($arg_s);
			if(isset($my_posts[0]->ID) && $my_posts[0]->ID > 0){
				return $my_posts[0]->post_title . $pg_title;
			}
		}
	}*/

	return $title;

}
add_filter( 'wpseo_title', 'seota_custom_dynamic_page_title' );
add_filter( 'wpseo_opengraph_title', 'seota_custom_dynamic_page_title' );

function seota_custom_dynamic_page_description($meta_desc) {
    // Return a custom document description for
    // the boat details custom page template
	if(!empty(get_query_var( 'post_slug' ))){
		$sub_cat = get_term_by('slug', get_query_var( 'cat_b' ), 'media_category');
		$post_parent = 0;
		if(isset($sub_cat) || empty($sub_cat)){
			$arg_s = array('name' => get_query_var( 'cat_b' ), 'post_type' => 'media', 'post_status' => 'publish', 'numberposts' => 1);
			$top_posts = get_posts($arg_s);
			$args = array();
			if(isset($top_posts[0]->ID) && $top_posts[0]->ID > 0){
				$post_parent = $top_posts[0]->ID;
			}
		}
		$arg_s = array('name' => get_query_var( 'post_slug' ), 'post_type'   => 'media', 'post_parent' => $post_parent, 'post_status' => 'publish', 'numberposts' => 1);
		$my_posts = get_posts($arg_s);
		if(isset($my_posts[0]->ID) && $my_posts[0]->ID > 0){
			return get_post_meta($my_posts[0]->ID, '_yoast_wpseo_metadesc', true);
		}
	}
	/*elseif(!empty(get_query_var( 'child' ))){
		$sub_cat = get_term_by('slug', get_query_var( 'cat_b' ), 'media_category');
		if(isset($sub_cat->term_id) && !empty($sub_cat->parent)){
			$post_parent = 0;
			if(!empty(get_query_var( 'cat_c' ))){
				$arg_s = array('name' => get_query_var( 'cat_c' ), 'post_type'   => 'media', 'post_status' => 'publish', 'numberposts' => 1);
				$top_posts = get_posts($arg_s);
				$args = array();
				if(isset($top_posts[0]->ID) && $top_posts[0]->ID > 0){
					$post_parent = $top_posts[0]->ID;
				}
			}
			$arg_s = array('name' => get_query_var( 'child' ), 'post_type'   => 'media', 'post_parent' => $post_parent, 'post_status' => 'publish', 'numberposts' => 1);
			$my_posts = get_posts($arg_s);
			if(isset($my_posts[0]->ID) && $my_posts[0]->ID > 0){
				return get_post_meta($my_posts[0]->ID, '_yoast_wpseo_metadesc', true);
			}
		}
	}*/

	return $meta_desc;

}
add_filter( 'wpseo_metadesc', 'seota_custom_dynamic_page_description' );
add_filter( 'wpseo_opengraph_desc', 'seota_custom_dynamic_page_description' );

add_filter( 'wpseo_opengraph_url', 'change_og_url' );

function change_og_url( $url ) {
	if ( !empty(get_query_var( 'author_a' )) || !empty(get_query_var( 'author_b' )) || !empty(get_query_var( 'author_c' )) ) {
		$segments = explode('about/', $_SERVER['REQUEST_URI']);
		if(isset($segments[1])){
			return site_url() . '/about/' . $segments[1];
		}
	}
	elseif(!empty(get_query_var( 'node' ))){
		$segments = explode('lp/', $_SERVER['REQUEST_URI']);
		if(isset($segments[1])){
			return site_url() . '/lp/' . $segments[1];
		}
	}
	elseif(!empty(get_query_var( 'post_slug' ))){
		$segments = explode('research-library/', $_SERVER['REQUEST_URI']);
		if(isset($segments[1])){
			return site_url() . '/research-library/' . $segments[1];
		}
	}
	/*elseif(!empty(get_query_var( 'child' ))){
		$segments = explode('research-library/', $_SERVER['REQUEST_URI']);
		$sub_cat = get_term_by('slug', get_query_var( 'cat_b' ), 'media_category');
		if(isset($sub_cat->term_id) && !empty($sub_cat->parent)){
			if(isset($segments[1])){
				return site_url() . '/research-library/' . $segments[1];
			}
		}
	}*/
	return $url;
}

function acf_load_cta_field_choices( $field ) {
    // reset choices
    $field['choices'] = array('hide' => "- Hide CTA -");
    // get the textarea value from options page without any formatting
	$get_cta = get_posts(array('numberposts' => -1, 'post_type' => 'cta-library', 'orderby' => 'title', 'order' => 'ASC'));
    // loop through array and add to field 'choices'
    if( !empty($get_cta) ) {

        foreach( $get_cta as $choice ) {

            $field['choices'][ $choice->ID ] = $choice->post_title;

        }
    }
    // return the field
    return $field;
}

add_filter('acf/load_field/name=select_cta', 'acf_load_cta_field_choices');
add_filter('acf/load_field/name=bottom_cta', 'acf_load_cta_field_choices');

/*
 * Product Download Version Filter
 */
add_action('wp_ajax_filter_version', 'filter_version');
add_action('wp_ajax_nopriv_filter_version', 'filter_version');

function filter_version() {

    $versions = new WP_Query(array(
        'post_type' => 'product_download',
        'post__in' => array($_POST['post_id'])
    ));
    $relfeature = '';
    while ($versions->have_posts()) : $versions->the_post();

        $reldate = '<h4>' . get_field('release_label') . '</h4><p>Release Date: ' . get_field('release_date') . '</p>';

        while (have_rows('product_features')): the_row();
            $relfeature .= '<div class="row"><div class="col-sm-6 col-12 align-self-center"><div class="title"><span class="icon">' . get_sub_field('feature_icon') . '</span>' . get_sub_field('feature_label') . '</div></div>';
            $count = count(get_sub_field('feature_link'));
            while (have_rows('feature_link')): the_row();
                if ($count == 1):
                    $relfeature .= '<div class="col-sm-6 col-12 align-self-center"><a class="btn btn-link btn-block" href="' . get_sub_field('button_link') . '">' . get_sub_field('button_label') . '</a></div>';
                else:
                    $relfeature .= '<div class="col-sm-3 col-6 align-self-center"><a class="btn btn-link btn-block" href="' . get_sub_field('button_link') . '">' . get_sub_field('button_label') . '</a></div>';
                endif;
            endwhile;
            $relfeature .= '</div>';
        endwhile;

        $relnote = get_field('release_notes');

    endwhile;
    wp_reset_query();

    echo json_encode(array('reldate' => $reldate, 'relfeature' => $relfeature, 'relnote' => $relnote));
    die;
}

/*
 * Admin bar disable for specific user role - 24-06-2021
 */
function check_user_role($roles, $user_id = null) {
    if ($user_id) $user = get_userdata($user_id);
    else $user = wp_get_current_user();
    if (empty($user)) return false;

    foreach ($user->roles as $role) {
        if (in_array($role, $roles)) {
            return true;
        }
    }
    return false;
}

// show admin bar only for admins and editors
if (!check_user_role(array('administrator','editor'))) {
  add_filter('show_admin_bar', '__return_false');
}


function ns_img_unautop($custom_content) {
   $custom_content = preg_replace('/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s', '\1', $custom_content);
   return $custom_content;
}
add_filter( 'acf_the_content', 'ns_img_unautop', 30 );
