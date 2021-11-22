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
				<p><?php _e( 'API Credentials can be found within your MailChimp account at', 'wishlist-member' ); ?> <a href="http://admin.mailchimp.com/account/api/" target="_blank">http://admin.mailchimp.com/account/api/</a></p>
			</div>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'API Key', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'mcapi',
					column : 'col-12',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Disable Double Opt-in', 'wishlist-member' ); ?>',
					name  : 'optin',
					value : '1',
					uncheck_value : '0',
					type  : 'checkbox',
					column : 'col-12',
					tooltip : '<?php _e( 'Disabling Double Opt-in in Mailchimp might result to your account to be suspended if abused.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="api_v3" value="1">
		</div>
	</div>
</div>