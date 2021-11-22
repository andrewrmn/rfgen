<?php
/* Other Integration : BuddyBoss */
include_once 'admin/init.php';

$groups = [];
$member_types = [];
$active_plugins  = wlm_get_active_plugins();

if ( in_array( 'BuddyBoss Platform', $active_plugins ) || isset($active_plugins['buddyboss-platform/bp-loader.php']) || is_plugin_active('buddyboss-platform/bp-loader.php') ) {
	if ( bp_is_active( 'groups' ) ) {
		$g = BP_Groups_Group::get( array( 'type'=>'alphabetical', 'per_page'=>9999 ));
		$g = isset( $g['groups'] ) ? $g['groups'] : [];
		foreach ( $g as $key => $value ) {
			$groups[$value->id] = ['id'=>$value->id ,'title'=>$value->name];
		}
	}

	$is_member_type_enabled = bp_member_type_enable_disable();
	if ( $is_member_type_enabled ) {
		$the_posts = new WP_Query(array( 'post_type' => bp_get_member_type_post_type(), 'post_status' =>  "publish",'nopaging'=>true));
		if ( count($the_posts->posts) ) {
			foreach ( $the_posts->posts as $key => $c ) {
				$member_types[$c->post_name] = ['id'=>$c->post_name ,'title'=>$c->post_title];
			}
		}
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
	'group' => 'Group Actions',
);
if ( $is_member_type_enabled ) {
	$tabs['type'] = "Profile Type Actions";
}
$tabs['settings'] = "Settings";

$active_tab = 'level';
$api_not_required = array();

$wlm_bb_group_default = $this->GetOption('wlm_bb_group_default');
$wlm_bb_ptype_default = $this->GetOption('wlm_bb_ptype_default');
?>
<div class="row">
	<div class="col plugin-status pt-2">
		<div class="text-warning"><p><em></em></p></div>
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
