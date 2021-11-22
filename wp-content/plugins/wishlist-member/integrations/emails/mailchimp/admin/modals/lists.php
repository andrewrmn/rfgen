<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="mailchimp-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="mailchimp-lists-modal-<?php echo $level->id; ?>"
	data-label="mailchimp-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'mailchimp-lists-select',
					style : 'width: 100%',
					name : 'mcID[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#mailchimp-list-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'A Member will be added to the selected Email List and Groups when they join the Membership Level.', 'wishlist-member' ); ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Interest Groups', 'wishlist-member' ); ?>',
					type : 'select',
					style : 'width: 100%',
					name : 'mcGping[<?php echo $level->id; ?>][]',
					column : 'col-12 interest-group',
					multiple : 'multiple',
					'data-mirror-value' : '#mailchimp-interest-<?php echo $level->id; ?>',
				}
			</template>
		</div>
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Action if Removed from Level', 'wishlist-member' ); ?>',
					type  : 'select',
					class : 'mailchimp-actions-select',
					name  : 'mcOnRemCan[<?php echo $level->id; ?>]',
					options : [
						{ value : '', text : 'Do Nothing' },
						{ value : 'unsub', text : 'Unsubscribe from List' },
						{ value : 'move', text : 'Move to Group' },
						{ value : 'add', text : 'Add to Group' }
					],
					column : 'col-12',
					style : 'width: 100%',
					'data-mirror-value' : '#mailchimp-remove-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'An action can be set to occur if a member is removed from a Membership Level.', 'wishlist-member' ); ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Interest Groups', 'wishlist-member' ); ?>',
					type : 'select',
					style : 'width: 100%',
					name : 'mcRCGping[<?php echo $level->id; ?>][]',
					column : 'col-12 interest-group',
					multiple : 'multiple',
					'data-mirror-value' : '#mailchimp-interestr-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
