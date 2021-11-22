<?php
/* Other Integration : FluentCRM */
require_once 'admin/init.php';

$fluentcrm_settings = isset( $ar_data[ $config['id'] ]['fluentcrm_settings'] ) ? $ar_data[ $config['id'] ]['fluentcrm_settings'] : array();
$lists              = array();
$tags               = array();
$active_plugins     = wlm_get_active_plugins();

if ( in_array( 'FluentCRM - Marketing Automation For WordPress', $active_plugins ) || isset( $active_plugins['fluent-crm/fluent-crm.php'] ) || is_plugin_active( 'fluent-crm/fluent-crm.php' ) ) {

	$listApi  = FluentCrmApi( 'lists' );
	$allLists = $listApi->all();
	foreach ( $allLists as $key => $value ) {
		$lists[ $value->id ] = array(
			'id'    => $value->id,
			'title' => $value->title,
		);
	}

	$tagApi  = FluentCrmApi( 'tags' );
	$allTags = $tagApi->all();
	foreach ( $allTags as $key => $value ) {
		$tags[ $value->id ] = array(
			'id'    => $value->id,
			'title' => $value->title,
		);
	}
}

?>
<div class="row">
	<div class="col plugin-status pt-2">
		<div class="text-warning"><p><em></em></p></div>
	</div>
</div>

<?php
$tabs = array(
	'level' => __( 'Membership Level Actions', 'wishlist-member' ),
	'list'  => __( 'List Actions', 'wishlist-member' ),
	'tag'   => __( 'Tag Actions', 'wishlist-member' ),
);

$active_tab       = 'level';
$api_not_required = array();

echo '<ul class="nav nav-tabs">';
foreach ( $tabs as $k => $v ) {
	$active       = $active_tab == $k ? 'active' : '';
	$api_required = in_array( $k, $api_not_required ) ? '' : 'api-required';
	printf( '<li class="%s %s nav-item"><a class="nav-link" data-toggle="tab" href="#%s_%s">%s</a></li>', $active, $api_required, $config['id'], $k, $v );
}
echo '</ul>';

echo '<div class="tab-content">';
foreach ( $tabs as $k => $v ) {
	$active       = $active_tab == $k ? 'active in' : '';
	$api_required = in_array( $k, $api_not_required ) ? '' : 'api-required';
	printf( '<div id="%s_%s" class="tab-pane %s %s">', $config['id'], $k, $api_required, $active );
	include_once 'admin/tabs/' . $k . '.php';
	echo '</div>';
}
echo '</div>';

printf( '<div data-script="%s"></div>', plugin_dir_url( __FILE__ ) . 'assets/admin.js' );
printf( '<div data-style="%s"></div>', plugin_dir_url( __FILE__ ) . 'assets/admin.css' );
?>
