<?php
/* Email Integration : Drip */
include_once 'admin/init.php';

$tabs = array(
	'settings' => 'Settings',
	'lists' => 'Campaigns',
	'tutorial' => 'Tutorial',
);
$active_tab = 'settings';
$api_not_required = array('settings','tutorial');

printf('<div class="form-text text-danger help-block"><p class="mb-0">%s</p></div>', 'This Drip integration is now deprecated. It is strongly recommended to use the updated Drip integration. <a href="?page=WishListMember&wl=setup/integrations/email_provider/drip2">Click here to do this now.</a>');

echo '<ul class="nav nav-tabs">';
foreach($tabs AS $k => $v) {
	$active = $active_tab == $k ? 'active' : '';
	$api_required = in_array($k, $api_not_required) ? '' : 'api-required';
	printf('<li class="%s %s nav-item"><a data-toggle="tab" class="nav-link" href="#%s_%s">%s</a></li>', $active, $api_required, $config['id'], $k, $v);
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
echo '<input type="hidden" name="action" value="admin_actions" />';
echo '<input type="hidden" name="WishListMemberAction" value="save_autoresponder" />';
printf ('<input type="hidden" name="autoresponder_id" value="%s" />', $config['id']);

echo '</div>';

printf('<div data-script="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.js');
