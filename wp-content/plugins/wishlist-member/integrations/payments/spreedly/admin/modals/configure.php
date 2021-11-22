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
					label : '<?php _e( 'Short Site Name:', 'wishlist-member' ); ?>',
					name : 'spreedlyname',
					column : 'col-md-12',
					tooltip : '<?php _e( 'Short Site Name is located in your Pin Payments account under <em>Pin Payments Site Configuration > Short Site Name</em>', 'wishlist-member' ); ?>',
					tooltip_size : 'lg',
				}
			</template>	
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'API Authentication Token', 'wishlist-member' ); ?>',
					name : 'spreedlytoken',
					column : 'col-md-12',
					tooltip : '<?php _e( 'API Authentication Token is located in youor Pin Payments account under <em>Pin Payments Site Configuration > API Authentication Token</em>', 'wishlist-member' ); ?>',
					tooltip_size : 'lg',
				}
			</template>
			<!-- <div class="col-md-2">
				<label>&nbsp;</label>
				<a class="btn btn-block -default -condensed -no-icon save-keys"><span class="-processing"><?php _e( 'Processing...', 'wishlist-member' ); ?></span><span class="-connected"><?php _e( 'Disconnect', 'wishlist-member' ); ?></span><span class="-disconnected"><?php _e( 'Connect', 'wishlist-member' ); ?></span></a>
			</div> -->
		</div>
	</div>
</div>