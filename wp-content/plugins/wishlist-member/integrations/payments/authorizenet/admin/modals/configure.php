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
					label : '<?php _e( 'API Login ID', 'wishlist-member' ); ?>',
					name : 'anloginid',
					column : 'col-md-12',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Trasaction Key', 'wishlist-member' ); ?>',
					name : 'antransid',
					column : 'col-md-12',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Signature Key', 'wishlist-member' ); ?>',
					name : 'anmd5hash',
					column : 'col-md-12',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Sandbox Testing', 'wishlist-member' ); ?>',
					name : 'anetsandbox',
					value : 1,
					uncheck_value : 0,
					type : 'checkbox',
					column : 'col-md-12',
				}
			</template>
		</div>
	</div>
</div>