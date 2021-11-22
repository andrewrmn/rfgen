<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="evergreen-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="evergreen-lists-modal-<?php echo $level->id; ?>"
	data-label="evergreen-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Auto Registration Link', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'webinar[evergreen][<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#evergreen-lists-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
