<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="gotomeeting-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="gotomeeting-lists-modal-<?php echo $level->id; ?>"
	data-label="gotomeeting-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'GoToWebinar &reg; Registration URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'webinar[gotomeeting][<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#gotomeeting-lists-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
