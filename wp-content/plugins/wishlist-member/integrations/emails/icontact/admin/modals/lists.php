<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="icontact-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="icontact-lists-modal-<?php echo $level->id; ?>"
	data-label="icontact-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Contact List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'icontact-lists',
					style : 'width: 100%',
					name : 'icID[<?php echo $level->id; ?>]',
					multiple : 'multiple',
					column : 'col-12',
					'data-mirror-value' : '#icontact-lists-<?php echo $level->id; ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Log Unsubscribes', 'wishlist-member' ); ?>',
					name  : 'iclog[<?php echo $level->id; ?>]',
					value : '1',
					uncheck_value : '',
					type  : 'checkbox',
					column : 'col-12',
					'data-mirror-value' : '#icontact-unsubscribe-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
