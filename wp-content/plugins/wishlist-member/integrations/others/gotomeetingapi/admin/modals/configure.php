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
					label : '<?php _e( 'Authorization Code', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'webinar[gotomeetingapi][authorizationcode]',
					column : 'col-md-12',
					help_block : '<a href="<?php echo $oauth->getApiAuthorizationUrl(); ?>" target="_blank">Click here to obtain an authorization code</a>'
				}
			</template>
			<input type="hidden" name="webinar[gotomeetingapi][accesstoken]">
			<input type="hidden" name="webinar[gotomeetingapi][organizerkey]">
		</div>
	</div>
</div>