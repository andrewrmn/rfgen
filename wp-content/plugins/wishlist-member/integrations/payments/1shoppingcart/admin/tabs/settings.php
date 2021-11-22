
<form>
	<div class="row">
		<div class="col-md-12 mb-4"><?php echo $config_button; ?></div>
	</div>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Thank You URL / API Notification URL', 'wishlist-member' ); ?>',
				name : 'scthankyou',
				addon_left : '<?php echo $wpm_scregister; ?>',
				addon_right : '.PHP',
				column : 'col-md-auto',
				class : 'text-center -url',
				group_class : '-url-group mb-1',
				tooltip : '<?php _e( 'The end string of the displayed Post URL can be edited if desired. Note that this Post URL must be copied and pasted exactly without any spaces before or after it.', 'wishlist-member' ); ?>',
				tooltip_size : 'lg',
			}
		</template>
	</div>
	<div class="row">
		<div class="col-md-12 text-muted">
			<p><?php _e( 'Set the Thank You URL in the 1ShoppingCart account or the Thank You URL for each product to the this URL.', 'wishlist-member' ); ?><br>
			<?php _e( 'Also add this URL as a Notification URL in 1ShoppingCart under My Account &gt; API &gt; API Settings.', 'wishlist-member' ); ?></p>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</form>
