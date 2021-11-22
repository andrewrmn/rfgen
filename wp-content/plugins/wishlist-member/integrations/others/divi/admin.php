<?php
$go = include_once 'admin/init.php';
if ( $go === false ) {
	return;
}

$tabs             = array(
	'tutorial' => 'Tutorial',
);
$active_tab       = '';
$api_not_required = array( 'tutorial' );

echo '<ul class="nav nav-tabs">';
foreach ( $tabs as $k => $v ) {
	$active       = $active_tab == $k ? 'active' : '';
	$api_required = in_array( $k, $api_not_required ) ? '' : 'api-required';
	printf( '<li class="%s %s nav-item"><a class="nav-link" data-toggle="tab" href="#%s_%s">%s</a></li>', $active, $api_required, $config['id'], $k, $v );
}
echo '</ul>';

printf( '<p>%s</p>', __( 'Configuration is not required within WishList Member for this integration. The integration is either enabled or disabled.', 'wishlist-member' ) );
printf('<p>%s</p>', __( 'While the integration is enabled, WishList Member will add a setting within Divi related to the display of Divi Sections and Rows.', 'wishlist-member' ) );
printf( '<p>%s</p>', __( 'The setting is available in the Advanced Options tab of all Sections and Rows within Divi.', 'wishlist-member' ) );
