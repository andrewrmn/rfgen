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
				<p><?php _e( 'Copy the API URL and API Key from the Account > Integrations & API > API section of GetResponse and paste them into the appropriate fields.', 'wishlist-member' ); ?></p>
				<?php  if ( isset($data['api_url']) && strpos( $data['api_url'], 'api2') !== false ) : ?>
					<p class="text-danger"><?php _e( 'You are using an old version of GetResponse API. Please change your API URL to "https://api.getresponse.com/v3"', 'wishlist-member' ); ?></p>
				<?php endif; ?>
			</div>
			<template class="wlm3-form-group">{label : '<?php _e( 'API URL', 'wishlist-member' ); ?>', type : 'text', name : 'api_url', column : 'col-md-12'}</template>
			<template class="wlm3-form-group">{label : '<?php _e( 'API Key', 'wishlist-member' ); ?>', type : 'text', name : 'apikey', column : 'col-md-12'}</template>
		</div>
	</div>
</div>