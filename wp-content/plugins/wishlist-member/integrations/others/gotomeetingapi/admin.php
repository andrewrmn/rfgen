<?php
$go = include_once 'admin/init.php';
if($go === false) return;

$tabs = array(
	'api' => 'API',
	'settings' => 'Settings',
	'tutorial' => 'Tutorial',
);
$active_tab = 'api';
$api_not_required = array('api','tutorial');

$x = wlm_arrval($data, 'authorizationcode');
if($x && strlen($x) <= 10) {
	echo '<div class="alert alert-warning"><p>Important: The previous Authentication method of GotoWebinar will be deprecated on August 14, 2018. This means that after the said date your GoToWebinar integration will stop working.</p><p>Please reauthenticate your GoToWebinar Integration by getting a new Authentication Code using <a target="_blank" href="'. $oauth->getApiAuthorizationUrl().'">this link </a> and paste it in the Authorization Code box below and then click the "Update Webinar Settings" button.</p></div>';
}

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
echo '<input type="hidden" name="action" value="admin_actions" />';
echo '<input type="hidden" name="WishListMemberAction" value="save_webinar" />';
printf ('<input type="hidden" name="webinar_id" value="%s" />', $config['id']);

echo '</div>';

printf('<div data-script="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.js');
