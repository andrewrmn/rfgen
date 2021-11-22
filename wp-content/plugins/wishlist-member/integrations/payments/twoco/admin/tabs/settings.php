<form>
	<div class="row">
		<div class="col-md-12 mb-4"><?php echo $config_button; ?></div>
	</div>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Instant Notification URL', 'wishlist-member' ); ?>',
				name : 'twocothankyou',
				addon_left : '<?php echo $wpm_scregister; ?>',
				column : 'col-md-auto',
				class : 'text-center -url',
				group_class : '-url-group',
				help_block : '<?php _e( 'Use this URL as the Instant Notification URL in 2Checkout.', 'wishlist-member' ); ?>',
				tooltip : '<?php _e( 'The end string of the displayed Post URL can be edited if desired. Note that this Post URL must be copied and pasted exactly without any spaces before or after it.', 'wishlist-member' ); ?>',
				tooltip_size : 'lg',
			}
		</template>
	</div>
	<div class="row">
		<div class="col-md-12">
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</form>