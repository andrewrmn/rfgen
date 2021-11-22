<div class="row">
	<div class="col-auto mb-4"><?php echo $config_button; ?></div>
	<?php echo $api_status_markup; ?>		
</div>
<div class="row">
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'Subscribers Changed Notification URL', 'wishlist-member' ); ?>',
			readonly : 'readonly',
			column : 'col-md-12',
			class : 'copyable',
			value : WLM3ThirdPartyIntegration['spreedly'].spreedlythankyou_url,
			tooltip : '<?php _e( 'Subscribers Changed Notification URL is located in your Pin Payments account under <em>Site Configuration &gt; Subscribers Changed Notification URL</em>', 'wishlist-member' ); ?>',
			tooltip_size : 'lg',
			help_block : '<?php _e( 'Set the Subscribers Changed Notification URL in Pin Payments to this URL', 'wishlist-member' ); ?>'
		}
	</template>	
</div>