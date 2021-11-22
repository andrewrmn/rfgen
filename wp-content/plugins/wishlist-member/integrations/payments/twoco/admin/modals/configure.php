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
					label : '<?php _e( 'Merchant Code', 'wishlist-member' ); ?>',
					name : 'twocovendorid',
					column : 'col-md-12',
					tooltip : '<?php _e( 'The Merchant Code is the id given to you from 2Checkout. The id must be exact with no spaces in front or back.', 'wishlist-member' ); ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Secret Key', 'wishlist-member' ); ?>',
					name : 'twocosecret',
					column : 'col-md-12',
					tooltip : '<?php _e( 'The Secret Key can be edited if desired. Note that this Secret Key must be copied and pasted exactly without any spaces before or after it.', 'wishlist-member' ); ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Demo Mode', 'wishlist-member' ); ?>',
					name : 'twocodemo',
					value : 1,
					uncheck_value : 0,
					type : 'checkbox',
					column : 'col-md-12',
				}
			</template>
		</div>
	</div>
</div>