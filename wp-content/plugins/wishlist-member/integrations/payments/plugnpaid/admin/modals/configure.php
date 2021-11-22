<div
	data-process="modal"
	id="configure-<?php echo $config['id']; ?>-template"
	data-id="configure-<?php echo $config['id']; ?>"
	data-label="configure-<?php echo $config['id']; ?>"
	data-title="<?php echo $config['name']; ?> Configuration"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<input type="hidden" class="-url" name="plugnpaidthankyou" />
		<div class="row">
			<?php echo $api_status_markup; ?>
		</div>
		<div class="row -integration-keys">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'API Token', 'wishlist-member' ); ?>',
					name : 'plugnpaidapikey',
					column : 'col-md-12',
				}
			</template>
		</div>
	</div>
</div>