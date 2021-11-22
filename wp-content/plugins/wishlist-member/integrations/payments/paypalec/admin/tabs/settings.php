<form>
	<div class="row">
		<?php echo $pp_upgrade_instructions; ?>
		<div class="col-auto mb-4"><?php echo $config_button; ?></div>
	</div>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Instant Payment Notification URL', 'wishlist-member' ); ?>',
				column : 'col-md-6',
				class : 'copyable',
				readonly : 'readonly',
				value : '<?php echo add_query_arg( 'action', 'ipn', $data->paypalecthankyou_url ); ?>',
				help_block : '<?php _e( 'Set this as the Instant Payment Notification URL in PayPal by updating your settings under My Profile > Selling Tools > Instant Payment Notifications', 'wishlist-member' ); ?>'
			}
		</template>
	</div>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Cancellation URL', 'wishlist-member' ); ?>',
				name : 'paypalec_cancel_url',
				column : 'col-md-6',
				class : 'applycancel',
				tooltip : '<?php _e( 'The URL a member will be redirected to if they cancel their purchase on the PayPal Checkout Page.<br><br>The member will be redirected to the home page by default if no URL is set here.', 'wishlist-member' ); ?>',
				tooltip_size : 'lg',
			}
		</template>
	</div>
	<input type="hidden" class="-url" name="paypalecthankyou" />
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</form>