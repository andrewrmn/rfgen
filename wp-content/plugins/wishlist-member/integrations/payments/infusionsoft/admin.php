<?php
/* Payment Integration : Infusionsoft */
include_once 'admin/init.php';

$tabs = array(
	'settings' => 'Settings',
	'products' => 'Products',
	'cron' => 'Cron Job',
	'tutorial' => 'Tutorial',

);
$active_tab = 'settings';
$api_not_required = array('settings','tutorial');
echo '<p><a href="http://wlplink.com/go/wlmis/29it" target="_blank">Learn more about our deeper integration with Infusionsoft</a></p>';
echo '<ul class="nav nav-tabs">';
foreach($tabs AS $k => $v) {
	$active = $active_tab == $k ? 'active' : '';
	$api_required = in_array($k, $api_not_required) ? '' : 'api-required';
	printf('<li class="%s %s nav-item"><a class="nav-link" data-toggle="tab" href="#%s_%s">%s</a></li>', $active, $api_required, $config['id'], $k, $v);
}
echo '</ul>';
echo '<div class="tab-content">';
foreach($tabs AS $k => $v) {
	$active = $active_tab == $k ? 'active in' : '';
	$api_required = in_array($k, $api_not_required) ? '' : 'api-required';
	printf('<div id="%s_%s" class="tab-pane %s %s">', $config['id'], $k, $api_required, $active);
	include_once 'admin/tabs/' . $k . '.php';
	echo '</div>';
}
echo '</div>';

printf('<div data-script="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.js');
printf('<div data-style="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.css');
