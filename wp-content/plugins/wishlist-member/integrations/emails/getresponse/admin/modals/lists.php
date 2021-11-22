<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="getresponselegacy-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="getresponselegacy-lists-modal-<?php echo $level->id; ?>"
	data-label="getresponselegacy-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Autoresponder Email', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'email[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#getresponselegacy-email-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'Create an auto responder email list in GetResponse and paste that email address into the Autoresponse Email field.', 'wishlist-member' ); ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe Email', 'wishlist-member' ); ?>',
					type  : 'text',
					name  : 'remove[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#getresponselegacy-remove-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'Create an Unsubscribe email list in GetResponse and paste that email address into the Autoresponse Email field.', 'wishlist-member' ); ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
