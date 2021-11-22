<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="aweber-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="aweber-lists-modal-<?php echo $level->id; ?>"
	data-label="aweber-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'AWeber List Name (ex: listname@aweber.com)', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'email[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#aweber-email-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'Copy the aWeber list name from aWeber and paste it into the corresponding field with no extra spaces.', 'wishlist-member' ); ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Safe Unsubscribe Email', 'wishlist-member' ); ?>',
					type  : 'text',
					name  : 'remove[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#aweber-remove-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'Create unsubscribe email address and paste into AWeber. Note that is can be any email address tied to your domain name.', 'wishlist-member' ); ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
