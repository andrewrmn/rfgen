<?php
// load membership levels
$wpm_levels = $this->GetOption( 'wpm_levels' );

$level_edit_tabs = apply_filters( 'wishlistmember_level_edit_tabs', array() ) ?: array(); 

if( $x = wlm_arrval( $_GET, 'howmany' ) ) {
	$this->SaveOption( 'levels-pagination-size', $x );
}
$pagination_size = $this->GetOption( 'levels-pagination-size' ) ?: 25;

$pagination = new \WishListMember\Pagination( count( $wpm_levels ), $pagination_size, wlm_arrval( $_GET, 'offset' ), 'offset', sprintf( '%s&search=%s&filter=%s', admin_url( 'admin.php?page=' . $this->MenuID . '&wl=setup/levels/' ), wlm_arrval( $_GET, 'search' ), wlm_arrval( $_GET, 'filter' ) ), $this->pagination_items );

$stats = array();
$members_link = 'admin.php?page=WishListMember&wl=members/manage';

// load roles
$roles = $GLOBALS['wp_roles']->roles;
$caps = array();
foreach ( (array) $roles as $key => $role ) {
	if ( (isset($role['capabilities']['level_10']) && $role['capabilities']['level_10'] ) || (isset($role['capabilities']['level_9']) && $role['capabilities']['level_9']) || (isset($role['capabilities']['level_8']) && $role['capabilities']['level_8']) ) {
		unset( $roles[ $key ] );
	} else {
		list($roles[ $key ]) = explode( '|', $role['name'] );
		$caps[ $key ] = count( $role['capabilities'] );
	}
}
array_multisort( $caps, SORT_ASC, $roles );
// supply options for role select
$js_roles = array();
foreach ( $roles as $k => $v ) {
	$js_roles[] = array(
		'value' => $k,
		'text' => $v,
	);
}
$js_roles = json_encode( $js_roles );
echo "\n<script type='text/javascript'>\nvar js_roles = $js_roles;\n</script>\n";
// supply options for add to and remove from select
$js_levels = array();
foreach ( $wpm_levels as $k => $v ) {
	$js_levels[] = array(
		'value' => $k,
		'text' => $v['name'],
		'id' => $k,
		'name' => $v['name'],
	);
	$wpm_levels[ $k ]['id'] = $k;

	foreach(array('removeFromLevel', 'addToLevel', 'cancelFromLevel','cancel_removeFromLevel', 'cancel_addToLevel', 'cancel_cancelFromLevel','remove_removeFromLevel', 'remove_addToLevel', 'remove_cancelFromLevel') AS $option) {
		$wpm_levels[$k][$option] = wlm_arrval( $wpm_levels, $k, $option ) ?: array();
	}
}

$shortcodes = $this->wlmshortcode->shortcodes;
$wlm_shortcodes = array(
	array('name' => 'Merge Codes', 'options' => array(array('value' => '', 'text' => ''))),
);
for( $i = 0; $i < count($shortcodes); $i+=3 ) {
	$wlm_shortcodes[0]['options'][] = array('value' => sprintf('[%s]', $shortcodes[$i][0]), 'text' => $shortcodes[$i + 1]);
}

$custom_user_data = $this->wlmshortcode->custom_user_data;
if($custom_user_data) {
	$wlm_shortcodes[] = array('name' => 'Custom Registration Fields', 'options' => array());
	foreach($custom_user_data AS $c) {
		$wlm_shortcodes[0]['options'][] = array('value' => sprintf('[wlm_custom %s]', $c), 'text' => $c);
	}
}

$wlm_sender_default = [ 'name' => $this->GetOption( 'email_sender_name' ), 'email' => $this->GetOption( 'email_sender_address' ) ];

printf( "\n<script type='text/javascript'>\nvar pagination = %s;\nvar js_levels = %s;\nvar wpm_levels = %s;\nvar level_stats = %s;\nvar members_link = %s;\nvar wlm_shortcodes = %s;\nvar wlm_sender_default = %s;\nvar wlm_level_edit_tabs = %s\n</script>\n", json_encode( $pagination ), json_encode( $js_levels ), json_encode( $wpm_levels ), json_encode( $stats ), json_encode( $members_link ), json_encode( $wlm_shortcodes ), json_encode( $wlm_sender_default ), json_encode( array_keys( $level_edit_tabs ) ) );

$registration_forms = $this->GetCustomRegForms();
foreach($registration_forms AS $id => &$f) {
	$f = array('value' => $id, 'text' => $f->option_value['form_name']);
}
unset($f);
array_unshift($registration_forms, array('value' => '', 'text' => 'Default Registration Form'));
printf( "<script type='text/javascript'>\nwpm_regforms = %s\nwpm_regform_defaults = %s\n</script>\n", json_encode( array_values( $registration_forms ) ), json_encode( preg_replace('/<script.+?<\/script>/i', '', $this->get_legacy_registration_form( '-----dummy-----', '', true ) ) ) );

// supply options for after reg and after login select
$pages = get_pages( 'exclude=' . implode( ',', $this->ExcludePages( array(), true ) ) );
$js_pages = array(
	array(
		'value' => '',
		'text' => 'WordPress Home Page',
	),
);
if ( $pages ) {
	foreach ( $pages as $page ) {
		$js_pages[] = array(
			'value' => $page->ID,
			'text' => $page->post_title,
		);
	}
}
$js_pages = json_encode( $js_pages );

$recaptcha_settings = json_encode(array(
	'recaptcha_public_key' => $this->GetOption('recaptcha_public_key') ?: '',
	'recaptcha_private_key' => $this->GetOption('recaptcha_private_key') ?: '',
));
echo <<<SCRIPT

<script type='text/javascript'>
	var js_pages = $js_pages;
	var recaptcha_settings = $recaptcha_settings;
</script>

<script type='text/javascript' src='{$this->pluginURL3}/assets/js/codemirror.js'></script>
<script type='text/javascript' src='{$this->pluginURL3}/assets/js/codemirror/mode-css.js'></script>
<script type='text/javascript' src='{$this->pluginURL3}/assets/js/codemirror/mode-xml.js'></script>
<script type='text/javascript' src='{$this->pluginURL3}/assets/js/codemirror/mode-javascript.js'></script>
<script type='text/javascript' src='{$this->pluginURL3}/assets/js/codemirror/mode-htmlmixed.js'></script>

<link rel="stylesheet" href="{$this->pluginURL3}/assets/css/codemirror.css">

SCRIPT;
$modal_footer = <<<STRING
	<button class="btn -bare modal-cancel">
		<span>Close</span>
	</button>
	<button class="modal-save-and-continue btn -primary">
		<i class="wlm-icons">save</i>
		<span>Save</span>
	</button>
	&nbsp;
	<button class="modal-save-and-close btn -success">
		<i class="wlm-icons">save</i>
		<span>Save &amp; Close</span>
	</button>
STRING;

$tab_footer = <<<STRING
	<button href="#" class="btn -primary done">
		<i class="wlm-icons">levels_icon</i>
		<span>Return to Levels</span>
	</button>
STRING;

include_once 'levels/list.php';
include_once 'levels/edit.php';
?>
<!-- Modal 01 -->

<!-- Modal 02 (Email Notifications) -->

<style type="text/css">
	#email-notification-settings .modal-body .-holder {
		display: none;
	}
	#email-notification-settings .modal-body.cancel .-holder.cancel,
	#email-notification-settings .modal-body.uncancel .-holder.uncancel,
	#email-notification-settings .modal-body.newuser .-holder.newuser,
	#email-notification-settings .modal-body.requireemailconfirmation .-holder.requireemailconfirmation,
	#email-notification-settings .modal-body.requireadminapproval-free .-holder.requireadminapproval-free,
	#email-notification-settings .modal-body.requireadminapproval-paid .-holder.requireadminapproval-paid,
	#email-notification-settings .modal-body.incomplete .-holder.incomplete,
	#email-notification-settings .modal-body.expiring .-holder.expiring {
		display: block;
	}

	#custom-redirects .modal-body .-holder {
		display: none;
	}
	#custom-redirects .modal-body.afterreg-redirect .-holder.afterreg-redirect,
	#custom-redirects .modal-body.login-redirect .-holder.login-redirect,
	#custom-redirects .modal-body.logout-redirect .-holder.logout-redirect {
		display: block;
	}

	.shortcode_inserter {
		margin: 0;
		padding: 0;
		min-height: auto;
	}

	.CodeMirror { border: 1px solid #ddd; }
	.CodeMirror pre { padding-left: 8px; line-height: 1.25; }

</style>
