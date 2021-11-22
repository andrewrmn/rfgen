<div class="row">
	<template class="wlm3-form-group">
		{
			type : 'url',
			name : 'slack_settings[webhook_url]',
			column : 'col-md-8 pr-0',
			class : 'applycancel',
			label : '<?php _e( 'Default Webhook URL', 'wishlist-member' ); ?> (<a href="https://slack.com/apps/A0F7XDUAZ-incoming-webhooks" target="_blank">Incoming Webooks App</a>)',
			placeholder : '<?php _e( 'https://', 'wishlist-member' ); ?>',
		}
	</template>
	<div class="col-auto pr-0">
			<label>&nbsp;</label>
			<button class="btn d-block -default -condensed slack-test-webhook"><?php _e( 'Test', 'wishlist-member' ); ?></button>
	</div>
</div>
<div class="row">
	<template class="wlm3-form-group">
		{
			type : 'text',
			name : 'slack_settings[username]',
			column : 'col-md-4',
			class : 'applycancel',
			label : '<?php _e( 'Custom Name', 'wishlist-member' ); ?>',
			placeholder : '<?php _e( 'WishList Member (Default)', 'wishlist-member' ); ?>',
		}
	</template>
</div>