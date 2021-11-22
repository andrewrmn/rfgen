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
				<p><?php printf( __( 'Generate your SendFox Personal Access Token by going to %s and paste it below.', 'wishlist-member' ), '<a href="https://sendfox.com/account/oauth">https://sendfox.com/account/oauth</a>' ); ?></p>
			</div>
			<template class="wlm3-form-group">{label : '<?php _e( 'Personal Access Token', 'wishlist-member' ); ?>', type : 'textarea', name : 'personal_access_token', column : 'col-md-12'}</template>
		</div>
	</div>
</div>