<?php
/* Payment Integration : 2Checkout */
include_once 'admin/init.php';

$tabs = array(
	'settings' => 'Settings',
	'products' => 'Products',
	'tutorial' => 'Tutorial',
);
$active_tab = 'settings';

printf('<div class="form-text text-danger help-block"><p class="mb-0">%s</p></div>', 'This 2Checkout integration is now deprecated. It is strongly recommended to use the updated 2Checkout integration. <a href="?page=WishListMember&wl=setup/integrations/payment_provider/twoco-api">Click here to do this now.</a>');

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
echo '</div>';
