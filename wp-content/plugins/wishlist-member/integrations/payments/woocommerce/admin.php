<?php
/* Payment Integration : WooCommerce */
if ( ! class_exists( 'WooCommerce' ) ) {
	   printf( '<div><p>This integration requires the <a href="%s" target="_blank">WooCommerce</a> plugin.</p></div>', $config['link'] );
	   return;
}

require_once 'admin/init.php';

$tabs       = array(
	'products'      => 'Products',
	// 'cancellations' => 'Cancellations',
	'tutorial'      => 'Tutorial',
);
$active_tab = 'products';

echo '<ul class="nav nav-tabs">';
foreach ( $tabs as $k => $v ) {
	$active = $active_tab == $k ? 'active' : '';
	printf( '<li class="%s nav-item"><a class="nav-link" data-toggle="tab" href="#%s_%s">%s</a></li>', $active, $config['id'], $k, $v );
}
echo '</ul>';
echo '<div class="tab-content">';
foreach ( $tabs as $k => $v ) {
	$active = $active_tab == $k ? 'active in' : '';
	printf( '<div id="%s_%s" class="tab-pane %s">', $config['id'], $k, $active );
	include_once 'admin/tabs/' . $k . '.php';
	echo '</div>';
}
echo '</div>';

printf( '<div data-script="%s"></div>', plugin_dir_url( __FILE__ ) . 'assets/admin.js' );
printf( '<div data-style="%s"></div>', plugin_dir_url( __FILE__ ) . 'assets/admin.css' );
