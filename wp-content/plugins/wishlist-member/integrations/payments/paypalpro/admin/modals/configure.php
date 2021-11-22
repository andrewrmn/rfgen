<div
	data-process="modal"
	id="configure-<?php echo $config['id']; ?>-template" 
	data-id="configure-<?php echo $config['id']; ?>"
	data-label="configure-<?php echo $config['id']; ?>"
	data-title="<?php echo $config['name']; ?> Configuration"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<p><a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&amp;generic-flow=true" target="paypal-api-get-signature" onclick="window.open(this.href, 'paypal-api-get-signature', 'height=500,width=360')"><?php _e( 'Click here to get your live PayPal API credentials', 'wishlist-member' ); ?></a></p>
			</div>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'API Username', 'wishlist-member' ); ?>',
					name : 'paypalprosettings[live][api_username]',
					column : 'col-md-6',
				}
			</template>
			<template class="wlm3-form-group">
				{ 
					label : '<?php _e( 'API Password', 'wishlist-member' ); ?>',
					name : 'paypalprosettings[live][api_password]',
					column : 'col-md-6',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'API Signature', 'wishlist-member' ); ?>',
					name : 'paypalprosettings[live][api_signature]',
					column : 'col-md-12',
				}
			</template>
		</div>
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Enable Sandbox Testing', 'wishlist-member' ); ?>',
					name : 'paypalprosettings[sandbox_mode]',
					id : 'paypalpro-enable-sandbox',
					value : 1,
					uncheck_value : 0,
					type : 'checkbox',
					column : 'col-md-12 mb-2',
				}
			</template>
		</div>
		<div class="row" id="paypalpro-sandbox-settings" style="display:none">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Sandbox API Username', 'wishlist-member' ); ?>',
					name : 'paypalprosettings[sandbox][api_username]',
					column : 'col-md-6',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Sandbox API Password', 'wishlist-member' ); ?>',
					name : 'paypalprosettings[sandbox][api_password]',
					column : 'col-md-6',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Sandbox API Signature', 'wishlist-member' ); ?>',
					name : 'paypalprosettings[sandbox][api_signature]',
					column : 'col-md-12',
				}
			</template>
		</div>
	</div>
</div>