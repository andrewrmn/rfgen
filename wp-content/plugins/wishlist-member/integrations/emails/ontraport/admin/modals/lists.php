<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="ontraport-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="ontraport-lists-modal-<?php echo $level->id; ?>"
	data-label="ontraport-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Enable', 'wishlist-member' ); ?>',
					name  : 'addenabled[<?php echo $level->id; ?>]',
					value : 'yes',
					uncheck_value : '',
					type  : 'checkbox',
					column : 'col-12',
					'data-mirror-value' : '#ontraport-enable-<?php echo $level->id; ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Tags', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'ontraport-tags',
					style : 'width: 100%',
					name : 'tags[<?php echo $level->id; ?>][]',
					multiple : 'multiple',
					column : 'col-12',
					'data-mirror-value' : '#ontraport-tags-<?php echo $level->id; ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Sequences', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'ontraport-sequences',
					style : 'width: 100%',
					name : 'sequences[<?php echo $level->id; ?>][]',
					multiple : 'multiple',
					column : 'col-12',
					'data-mirror-value' : '#ontraport-sequences-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
