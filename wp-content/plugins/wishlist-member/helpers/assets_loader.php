<?php
/**
 * This script handles the loading of CSS and JS files for WishList Member
 */

error_reporting( 0 );

define( 'PLUGIN_DIR', dirname( __DIR__ ) );

$wlm_build_number = '7524';

if ( preg_match( '/{' . 'GLOBALREV}/', $wlm_build_number ) ) {
	$wlm_build_number = '';
}
if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) === md5( $wlm_build_number . $_SERVER['REQUEST_URI'] ) ) {
	http_response_code( 304 );
	exit;
}

$styles  = array(
	array(
		'/assets/css/wordpress-overrides.css',
		'/assets/css/bootstrap.min.css',
		'/assets/css/animate.min.css',
		'/assets/css/select2.min.css',
		'/assets/css/select2-bootstrap.min.css',
		'/assets/css/toggle-switch-px.css',
		'/assets/css/daterangepicker.css',
		'/assets/css/jquery.minicolors.css',
		'/assets/css/source-sans.css',
		'/ui/stylesheets/main.css',
	),
);
$scripts = array(
	array(
		'/assets/js/jquery.min.js',
		'/assets/js/jquery-ui.min.js',
		'/assets/js/underscore-min.js',
		'/assets/js/underscore.string.min.js',
		'/assets/js/backbone-min.js',
		'/assets/js/tinymce/tinymce.min.js',
	),
	array(
		'/assets/js/popper.min.js',
		'/assets/js/bootstrap.min.js',
		'/assets/js/select2.min.js',
		'/assets/js/moment.min.js',
		'/assets/js/daterangepicker.js',
		'/assets/js/jquery.minicolors.min.js',
		'/assets/js/clipboard.min.js',
		'/assets/js/wlm.js',
		'/assets/js/main.js',
	),
);

if ( empty( $asset_type ) || ! in_array( $asset_type, array( 'css', 'js' ) ) ) {
	return;
}

$asset_index = (int) $asset_index;

$output = '';

// Combine Files
switch ( $asset_type ) {
	case 'css':
		$fs = $styles;
		break;
	default:
		$fs = $scripts;
}
$fs = (array) $fs[ $asset_index ];
foreach ( $fs as $f ) {
	if ( file_exists( PLUGIN_DIR . $f ) ) {
		$output .= '/* [' . $f . "] */\n";
		$output .= file_get_contents( PLUGIN_DIR . $f );
	}
	$output .= "\n";
}

if ( $asset_type == 'js' && $asset_index == 0 ) {
	// we use $ for jQuery
	$output .= 'var $ = jQuery.noConflict();';
}
$output = trim( $output );

// Content Type
$ct = $asset_type == 'css' ? 'text/css' : 'application/javascript';
header( 'Content-type: ' . $ct . '; charset=UTF-8' );

if ( ! $output ) {
	exit;
}

// caching headers
$seconds_to_cache = 3153600; // one year
$ts               = gmdate( 'D, d M Y H:i:s', time() + $seconds_to_cache ) . ' GMT';
if ( $wlm_build_number ) {
	header( 'Etag: ' . md5( $wlm_build_number . $_SERVER['REQUEST_URI'] ) );
}
header( 'Expires: ' . $ts );
header( 'Pragma: cache' );
header( 'Cache-Control: public, max-age=' . $seconds_to_cache );

$output = "/* WishList Member */\n" . $output;
