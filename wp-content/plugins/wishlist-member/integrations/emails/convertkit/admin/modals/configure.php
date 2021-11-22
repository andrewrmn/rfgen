<div
	data-process="modal"
	id="configure-<?php echo $config['id']; ?>-template" 
	data-id="configure-<?php echo $config['id']; ?>"
	data-label="configure-<?php echo $config['id']; ?>"
	data-title="<?php echo $config['name']; ?> Configuration"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row -integration-keys">
			<?php echo $api_status_markup; ?>		
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'API Secret', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'ckapi',
					column : 'col-md-12',
					help_block : '<?php _e( 'Copy the <a href="https://app.convertkit.com/account/edit" target="_blank">API Secret</a> from the Account section of ConvertKit and paste it into the field', 'wishlist-member' ); ?>',
					tooltip : '<?php _e( 'Make sure to use the API Secret, not the API Key', 'wishlist-member' ); ?>'
				}
			</template>
			<!-- <div class="col-md-2">
				<label>&nbsp;</label>
				<a class="btn btn-block -default -condensed -no-icon save-keys"><span class="-processing"><?php _e( 'Processing...', 'wishlist-member' ); ?></span><span class="-connected"><?php _e( 'Disconnect', 'wishlist-member' ); ?></span><span class="-disconnected"><?php _e( 'Connect', 'wishlist-member' ); ?></span></a>
			</div> -->
		</div>
	</div>
</div>