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
				<p><?php _e( 'Your API Settings is located in your Account &gt; Account Settings', 'wishlist-member' ); ?></p>
			</div>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'API Token', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'api_key',
					column : 'col-md-12',
				}
			</template>
		</div>
	</div>
</div>