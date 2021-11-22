<?php

include_once 'admin/init.php';

$tabs = array(
	'api' => 'API',
	'products' => 'Products',
	'subscriptions' => 'Subscriptions',
	'tutorial' => 'Tutorial',
);
$active_tab = 'api';
$api_not_required = array('api', 'tutorial', 'products');

printf('<div class="form-text text-danger help-block"><p class="mb-0">%s</p></div>', 'The Pin Payments Subscriptions product this integration was based on was discontinued by Pin Payments. Unfortunately, we cannot guarantee this integration will continue to function. This integration has been set as legacy in WishList Member. We recommend using a different payment provider Integration option to ensure proper functionality.');

echo '<ul class="nav nav-tabs">';
foreach ( $tabs as $k => $v ) {
	$active = $active_tab == $k ? 'active' : '';
	$api_required = in_array($k, $api_not_required) ? '' : 'api-required';
	printf( '<li class="%s %s nav-item"><a class="nav-link" data-toggle="tab" href="#%s_%s">%s</a></li>', $active, $api_required, $config['id'], $k, $v );
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

echo '<input type="hidden" class="-url" name="spreedlythankyou_url" />';
echo '<input type="hidden" name="action" value="admin_actions" />';
echo '<input type="hidden" name="WishListMemberAction" value="save" />';

echo '</div>';

printf('<div data-script="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.js');