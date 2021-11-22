<?php

include_once 'admin/init.php';

$error = array();
if ( ! function_exists( 'curl_init' ) ) {
	$error[] = __( 'CURL PHP extension.', 'wishlist-member' );
}
if ( ! function_exists( 'json_decode' ) ) {
	$error[] = __( 'JSON PHP extension.', 'wishlist-member' );
}
if ( ! function_exists( 'mb_detect_encoding' ) ) {
	$error[] = __( 'Multibyte String PHP extension.', 'wishlist-member' );
}

if ( count( $error ) ) {
	$error = '<li>' . implode( '</li><li>', $error ) . '</li>';
	$heading = __( 'Stripe requires the following PHP extensions:', 'wishlist-member' );
	printf( '<div class="form-text text-danger help-block"><p class="title">%s</p><br><ul>%s</ul><br><p>%s</p></div>', $heading, $error, __( 'Please ask your web hosting provider to enable it.','wishlist-member' ) );
	return;
}

$tabs = array(
	'settings' => 'Settings',
	'products' => 'Products',
	'tutorial' => 'Tutorial',
);
$active_tab = 'settings';
$api_not_required = array('settings', 'tutorial');

echo '<ul class="nav nav-tabs">';
foreach ( $tabs as $k => $v ) {
	$active = $active_tab == $k ? 'active' : '';
	$api_required = in_array($k, $api_not_required) ? '' : 'api-required';
	printf( '<li class="%s nav-item"><a class="nav-link %s " data-toggle="tab" href="#%s_%s">%s</a></li>', $api_required, $active, $config['id'], $k, $v );
}
echo '</ul>';
echo '<div class="tab-content">';
foreach ( $tabs as $k => $v ) {
	$active = $active_tab == $k ? 'active in' : '';
	$api_required = in_array($k, $api_not_required) ? '' : 'api-required';
	printf( '<div id="%s_%s" class="tab-pane %s %s">', $config['id'], $k, $active, $api_required );
	include_once 'admin/tabs/' . $k . '.php';
	echo '</div>';
}
echo '</div>';


printf('<div data-script="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.js');
printf('<div data-style="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.css');
