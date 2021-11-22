<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="easywebinar-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="easywebinar-lists-modal-<?php echo $level->id; ?>"
	data-label="easywebinar-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Webinar', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'easywebinar-webinars',
					style : 'width: 100%',
					name : 'webinar[easywebinar][<?php echo $level->id; ?>]',
					column : 'col-12',
					options : WLM3ThirdPartyIntegration.easywebinar.webinar.easywebinar.webinar_options,
					'data-placeholder' : '<?php _e( 'Select a Webinar', 'wishlist-member' ); ?>',
					'data-allow-clear' : 'true',
					'data-mirror-value' : '#easywebinar-lists-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
