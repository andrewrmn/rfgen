<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="callloop-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="callloop-lists-modal-<?php echo $level->id; ?>"
	data-label="callloop-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Call Loop List URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'callloop_settings[URL][<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#callloop-lists-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'Copy the list URL from Call Loop and paste it into the corresponding field with no extra spaces.', 'wishlist-member' ); ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe if Removed from Level', 'wishlist-member' ); ?>',
					name  : 'callloop_settings[callloopUnsub][<?php echo $level->id; ?>]',
					value : '1',
					uncheck_value : '',
					type  : 'checkbox',
					column : 'col-12',
					'data-mirror-value' : '#callloop-unsubscribe-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
