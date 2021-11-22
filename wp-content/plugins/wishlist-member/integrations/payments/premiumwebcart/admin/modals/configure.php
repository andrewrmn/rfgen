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
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Mechant ID', 'wishlist-member' ); ?>',
					name : 'pwcmerchantid',
					column : 'col-md-12',
					tooltip : '<?php _e( 'Merchant ID is located in the following section:<br><br>Account Settings > Current Status.', 'wishlist-member' ); ?>',
					tooltip_size: 'md',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'API Key', 'wishlist-member' ); ?>',
					name : 'pwcapikey',
					column : 'col-md-12',
					tooltip : '<?php _e( 'API Key is located in the following section:<br><br>Cart Settings > Advanced Integration > API Integration.', 'wishlist-member' ); ?>',
					tooltip_size: 'md',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Secret Word', 'wishlist-member' ); ?>',
					name : 'pwcsecret',
					column : 'col-md-12',
					tooltip : '<?php _e( 'The Secret Word is used to generate a hash key for security purposes.', 'wishlist-member' ); ?>',
					tooltip_size: 'md',
				}
			</template>
		</div>
	</div>
</div>