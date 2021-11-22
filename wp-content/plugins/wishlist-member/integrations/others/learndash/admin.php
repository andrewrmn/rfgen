<?php
$go = include_once 'admin/init.php';
if($go === false) return;

$the_posts = new WP_Query(array( 'post_type' =>  'sfwd-courses','nopaging'=>true));
$courses = [];
if ( count($the_posts->posts) ) {
	foreach ( $the_posts->posts as $key => $c ) {
		$courses[$c->ID] = ['id'=>$c->ID ,'title'=>$c->post_title];
	}
}

$the_groups = new WP_Query(array( 'post_type' =>  'groups','nopaging'=>true));
$groups = [];
if ( count($the_groups->posts) ) {
	foreach ( $the_groups->posts as $key => $c ) {
		$groups[$c->ID] = ['id'=>$c->ID ,'title'=>$c->post_title];
	}
}


$level_actions = array(
	"add" => "Added",
	"cancel" => "Cancelled",
	"remove" => "Removed",
	"rereg" => "Re-Registered",
);

$tabs = array(
	'level' => 'Membership Level Actions',
	'course' => 'Course Actions',
	'group' => 'Group Actions',
	'settings' => 'Settings',
);
$active_tab = 'level';
$api_not_required = array();

$wlm_ld_group_default = $this->GetOption('wlm_ld_group_default');
?>
<div class="row">
	<div class="col plugin-status pt-2">
		<div class="text-warning"></div>
	</div>
</div>


<?php
echo '<ul class="nav nav-tabs">';
foreach ( $tabs AS $k => $v ) {
	$active = $active_tab == $k ? 'active' : '';
	$api_required = in_array($k, $api_not_required) ? '' : 'api-required';
	printf('<li class="%s %s nav-item"><a class="nav-link" data-toggle="tab" href="#%s_%s">%s</a></li>', $active, $api_required, $config['id'], $k, $v);
}
echo '</ul>';

echo '<div class="tab-content">';
foreach( $tabs AS $k => $v ) {
	$active = $active_tab == $k ? 'active in' : '';
	$api_required = in_array($k, $api_not_required) ? '' : 'api-required';
	printf('<div id="%s_%s" class="tab-pane %s %s">', $config['id'], $k, $api_required, $active);
	include_once 'admin/tabs/' . $k . '.php';
	echo '</div>';
}
echo '</div>';

printf('<div data-script="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.js');
?>
