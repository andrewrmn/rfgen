<div class="row">
	<div class="col-auto mb-4"><?php echo $config_button; ?></div>
	<?php echo $api_status_markup; ?>		
</div>

<div class="row api-required">
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'Web Hook', 'wishlist-member' ); ?>',
			readonly : 'readonly',
			column : 'col-auto',
			value : WLM3ThirdPartyIntegration.stripe.stripethankyou_url + '?stripe_action=sync',
			help_block : '<?php _e( 'Copy and paste this URL into Stripe at <a href="https://dashboard.stripe.com/account/webhooks" target="_blank">https://dashboard.stripe.com/account/webhooks</a>', 'wishlist-member' ); ?>',
		}
	</template>	
</div>

