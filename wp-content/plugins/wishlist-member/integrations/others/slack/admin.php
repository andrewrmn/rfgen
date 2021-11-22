<?php
include_once 'admin/init.php';

$tabs = array(
	'settings' => 'Settings',
	'levels' => 'Levels',
	'tutorial' => 'Tutorial',
);
$active_tab = 'settings';

echo '<ul class="nav nav-tabs">';
foreach($tabs AS $k => $v) {
	$active = $active_tab == $k ? 'active' : '';
	printf('<li class="%s nav-item"><a class="nav-link" data-toggle="tab" href="#%s_%s">%s</a></li>', $active, $config['id'], $k, $v);
}
echo '</ul>';
echo '<div class="tab-content">';
foreach($tabs AS $k => $v) {
	$active = $active_tab == $k ? 'active in' : '';
	printf('<div id="%s_%s" class="tab-pane %s">', $config['id'], $k, $active);
	include_once 'admin/tabs/' . $k . '.php';
	echo '</div>';
}
echo '<input type="hidden" name="action" value="admin_actions" />';
echo '<input type="hidden" name="WishListMemberAction" value="save" />';

echo '</div>';

printf('<div data-script="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.js');
printf('<div data-style="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.css');
