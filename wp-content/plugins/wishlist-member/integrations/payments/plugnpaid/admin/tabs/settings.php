<div class="row">
	<div class="col-auto mb-4"><?php echo $config_button; ?></div>
	<?php echo $api_status_markup; ?>		
</div>

<div class="row api-required">
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'Thank You URL / API Notification URL', 'wishlist-member' ); ?>',
			name : 'plugnpaidthankyou',
			addon_left : '<?php echo $wpm_scregister; ?>',
			addon_right : '<?php echo strpos( $wpm_scregister, '?' ) === false ? '?' : '&'; ?>plugnpaid_action=webhook',
			column : 'col-md-auto',
			class : 'text-center -url',
			group_class : '-url-group mb-1',
			help_block : '<?php _e( 'Copy and paste this URL into plug&paid at <a href="https://www.plugnpaid.com/app/settings/webhooks" target="_blank">https://www.plugnpaid.com/app/settings/webhooks</a>', 'wishlist-member' ); ?>',
		}
	</template>
`</div>

