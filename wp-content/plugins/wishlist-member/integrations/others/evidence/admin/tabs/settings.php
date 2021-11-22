<div class="row">
	<template class="wlm3-form-group">
		{
			type : 'url',
			name : 'evidence_settings[webhook_url]',
			column : 'col-md-8 pr-0',
			class : 'applycancel',
			label : '<?php _e( 'Default Webhook URL', 'wishlist-member' ); ?>',
			placeholder : '<?php _e( 'https://', 'wishlist-member' ); ?>',
		}
	</template>
	<div class="col-auto pr-0">
			<label>&nbsp;</label>
			<button class="btn d-block -default -condensed evidence-test-webhook"><?php _e( 'Test', 'wishlist-member' ); ?></button>
	</div>
</div>