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
			<div class="col-md-12">
				<p><?php _e( 'API Credentials are located in Ontraport under Administration Settings', 'wishlist-member' ); ?></p>
			</div>
			<template class="wlm3-form-group">{label : '<?php _e( 'App ID', 'wishlist-member' ); ?>', type : 'text', name : 'app_id', column : 'col-md-12'}</template>
			<template class="wlm3-form-group">{label : '<?php _e( 'API Key', 'wishlist-member' ); ?>', type : 'text', name : 'api_key', column : 'col-md-12'}</template>
			<!-- <div class="col-md-2">
				<label>&nbsp;</label>
				<a class="btn btn-block -default -condensed -no-icon save-keys"><span class="-processing"><?php _e( 'Processing...', 'wishlist-member' ); ?></span><span class="-connected"><?php _e( 'Disconnect', 'wishlist-member' ); ?></span><span class="-disconnected"><?php _e( 'Connect', 'wishlist-member' ); ?></span></a>
			</div> -->
		</div>
	</div>
</div>