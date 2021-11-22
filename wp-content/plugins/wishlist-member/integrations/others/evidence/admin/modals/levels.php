<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="evidence-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="evidence-lists-modal-<?php echo $level->id; ?>"
	data-label="evidence-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row mb-3">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Enable', 'wishlist-member' ); ?>',
					type : 'toggle-switch',
					name : 'evidence_settings[active][<?php echo $level->id; ?>]',
					value : '1',
					uncheck_value : '0',
					column : 'col-12',
				}
			</template>
		</div>
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Custom Text #1', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'evidence_settings[custom_text_1][<?php echo $level->id; ?>]',
					column : 'col-12',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Custom Text #2', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'evidence_settings[custom_text_2][<?php echo $level->id; ?>]',
					column : 'col-12',
				}
			</template>
		</div>
		<div class="row">
			<div class="col-12">
				<label><?php _e('Custom Webhook URL','wishlist-member'); ?></label>
			</div>
		</div>
		<div class="row">
			<template class="wlm3-form-group">
				{
					type : 'toggle-adjacent-disable',
					name : 'evidence_settings[custom_webhook_enabled][<?php echo $level->id; ?>]',
					value : '1',
					uncheck_value : '0',
					column : 'col-auto custom-webhook-toggle',
				}
			</template>
			<template class="wlm3-form-group">
				{
					type : 'text',
					name : 'evidence_settings[custom_webhook_url][<?php echo $level->id; ?>]',
					column : 'col px-0',
					placeholder : WLM3ThirdPartyIntegration.evidence.evidence_settings.webhook_url + ' (' + wlm.translate('Default') + ')',
				}
			</template>
			<div class="col-auto">
				<button class="btn -default -condensed evidence-test-webhook" data-level="<?php echo $level->id; ?>"><?php _e('Test','wishlist-member');?></button>
			</div>
		</div>
	</div>
</div>
<?php
endforeach;
?>
