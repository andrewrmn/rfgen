<?php
/* Email Integration : Generic2 */
include_once 'admin/init.php';

$tabs = array(
	'lists' => 'Mailing Lists',
	'tutorial' => 'Tutorial',
);
$active_tab = 'lists';

echo '<ul class="nav nav-tabs">';
foreach($tabs AS $k => $v) {
	$active = $active_tab == $k ? 'active' : '';
	printf('<li class="%s nav-item"><a data-toggle="tab" class="nav-link" href="#%s_%s">%s</a></li>', $active, $config['id'], $k, $v);
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
echo '<input type="hidden" name="WishListMemberAction" value="save_autoresponder" />';
printf ('<input type="hidden" name="autoresponder_id" value="%s" />', $config['id']);

echo '</div>';
