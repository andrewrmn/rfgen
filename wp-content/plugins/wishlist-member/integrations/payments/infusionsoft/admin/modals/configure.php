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
					label : '<?php _e( 'App Name', 'wishlist-member' ); ?>',
					name : 'ismachine',
					column : 'col-md-12',
					addon_right : '.infusionsoft.com',
					tooltip : '<?php _e( 'Example: <em>appname</em>.infusionsoft.com', 'wishlist-member' ); ?>',
					tooltip_size : 'md',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Encrypted Key', 'wishlist-member' ); ?>',
					name : 'isapikey',
					column : 'col-md-12',
					tooltip : '<?php _e( 'This key is located in Infusionsoft under <em>Admin > Settings > Application</em>', 'wishlist-member' ); ?>',
					tooltip_size : 'md',
				}
			</template>
			<!-- <div class="col-md-2">
				<label>&nbsp;</label>
				<a class="btn btn-block -default -condensed -no-icon save-keys"><span class="-processing"><?php _e( 'Processing...', 'wishlist-member' ); ?></span><span class="-connected"><?php _e( 'Disconnect', 'wishlist-member' ); ?></span><span class="-disconnected"><?php _e( 'Connect', 'wishlist-member' ); ?></span></a>
			</div> -->
		</div>
	</div>
</div>