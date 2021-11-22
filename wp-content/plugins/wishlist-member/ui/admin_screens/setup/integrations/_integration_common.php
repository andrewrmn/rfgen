<?php

$integration_link = add_query_arg(['page' => $this->MenuID, 'wl' => $wl_path], admin_url('admin.php'));

$requested_integration = $virtual_path ? array_pop( $virtual_path ) : '*';

$wpm_levels = $this->GetOption('wpm_levels');
foreach($wpm_levels AS $id => &$level) {
	$level['id'] = $id;
}
unset($level);
$payperposts = $this->get_payperposts( array( 'post_title', 'post_type' ) );
$all_levels = array_merge(
	array(
		'__levels__' => $wpm_levels,
	), $payperposts
);

$post_types = get_post_types('', 'objects');
foreach( $post_types AS &$ptype) {
	$ptype = (object) array ( 'labels' => (object) array( 'name' => sprintf( 'Pay Per Post: %s', $ptype->labels->name ) ) );
}
unset($ptype);
$post_types['__levels__'] = (object) array('labels' => (object) array('name' => 'Membership Levels'));


$all_levels_select_options = array();
foreach($all_levels AS $type => $levels) {
	$options = array();
	foreach($levels AS $level) {
		$options[] = array('text' => $level['name'], 'name' => $level['name'], 'value' => $level['id'], 'id' => $level['id']);
	}
	if( $options ) {
		$all_levels_select_options[] = array('name' => $post_types[$type]->labels->name, 'text' => $post_types[$type]->labels->name, 'options' => $options, 'children' => $options);
	}
}

$wpm_scregister = $this->make_thankyou_url( '' );

echo "\n<script type='text/javascript'>\n";

echo "var wpm_scregister = '$wpm_scregister';\n";

$x = json_encode($all_levels);
$x = !empty($x) ? $x : "{}";
echo "var all_levels = $x;\n";

$x = json_encode($post_types);
$x = !empty($x) ? $x : "{}";
echo "var post_types = $x;\n";

$x = json_encode($all_levels_select_options);
$x = !empty($x) ? $x : "{}";
echo "var all_levels_select_options = $x;\n";

echo "</script>\n";

function thirdparty_integration_data($id, $data) {
	echo "<script type='text/javascript'>\n";
	if(empty($data)) $data = new stdClass;
	printf("WLM3ThirdPartyIntegration['%s'] = %s;\n", $id, json_encode($data));
	echo "</script>\n";
}

$show_legacy_integrations = $this->GetOption('show_legacy_integrations');

$tab_footer = <<<STRING
	<a href="$integration_link" class="btn -primary done -return-button">
		<i class="wlm-icons">date_range</i>
		<span>Return to Integrations</span>
	</a>
STRING;

$api_status_markup = <<<STRING
<div class="col api-status pt-2">
	<div class="text-warning"><p><em>Checking...</em></p></div>
</div>
STRING;

?>
<template id="thirdparty-provider-toggle">
	<label class="switch-light switch-wlm">
		<input type="checkbox" value="1" name="toggle-thirdparty-provider" skip-save="1">		
		<span>
			<span>
				<i class="wlm-icons md-18 ico-check">
				check</i>
			</span>
			<span>
				<i class="wlm-icons md-18 ico-close">
				close</i>
			</span>
			<a>
			</a>
		</span>
	</label>
</template>

<!-- This has to be here -->
<script type="text/javascript">
	var integration_save_validators = [];
	var integration_takeover_save = [];
	var integration_modal_save = [];
	var integration_before_open = {};
	var integration_after_open = {};
	var wlm3_integration_config;
	var WLM3ThirdPartyIntegration = {};
	var all_levels_flat = {};
	$.each(all_levels, function(a, b) {
		$.each(b, function(c, d) {
			d.__type__ = a;
			all_levels_flat[d.id] = d;
		});
	});
	var requested_integration = <?php echo json_encode($requested_integration); ?>;
	$(function() {
		$.getScript(WLM3VARS.pluginurl + '/ui/js/admin_js/setup/integrations/_integration_common.js?build=7524');
	});
</script>

<!-- todo: move this somewhere else -->
<style type="text/css">
	.-has-settings .close-buttons,
	.-no-settings .save-buttons {
		display: none;
	}

	.-is-active .-inactive,
	.-is-inactive .-active {
		display: none;
	}

	.thirdparty-provider-container {
		position: relative;
	}

	.thirdparty-provider-container .save-keys span {
		display: none;
	}
	.thirdparty-provider-container .save-keys.disabled .-processing,
	.thirdparty-provider-container:not(.api-fail) .save-keys:not(.disabled) .-connected,
	.thirdparty-provider-container.api-fail .save-keys:not(.disabled) .-disconnected {
		display: inline;
	}
	.thirdparty-provider-container.api-fail .api-required {
		display: none;
	}
	.thirdparty-provider-container .api-status p::before {
		content: "API Status : ";
	}

	#all-integrations-parent .collapsing {
		transition: none !important;
	}
	#all-integrations-parent .collapsing:not(.show) {
		display: none;
	}

	#all-integrations-parent td.text-center .switch {
		display: inline-block;
	}
	#all-integrations-parent td.text-center .switch-body {
		display: none;
	}
	.integration-video {
	  position: relative;
	  padding-bottom: 56.25%;
	  height: 0;
	  overflow: hidden;
  	}
  	.integration-video iframe {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
</style>