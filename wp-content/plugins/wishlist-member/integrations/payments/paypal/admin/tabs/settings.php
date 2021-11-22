<form>
	<div class="row">
		<?php echo $pp_upgrade_instructions; ?>
		<div class="col-auto mb-4"><?php echo $config_button; ?></div>
		<div class="col-md-12">
			<h3 class="main-title"><?php _e( 'Enable Payment Data Transfer', 'wishlist-member' ); ?></h3>
			<p><?php _e( 'Locate your PDT Identity Token and set the following options in the My Account > My Selling Tools > Website Preferences section of PayPal:', 'wishlist-member' ); ?></p>
			<ul>
				<li><strong>Auto Return</strong>: <?php _e( 'On', 'wishlist-member' ); ?></li>
				<li><strong>Return URL</strong>: <?php _e( 'Any URL can be used but it cannot be left blank. The site homepage URL is recommend.', 'wishlist-member' ); ?></li>
				<li><strong>Payment Data Transfer</strong>: <?php _e( 'On', 'wishlist-member' ); ?></li>
			</ul>
			<h3 class="main-title"><?php _e( 'Enable Instant Payment Notifications', 'wishlist-member' ); ?></h3>
			<p><?php _e( 'Set the following options in the Profile > My Selling Tools > Instant Payment Notifications > Choose IPN Settings OR Edit Settings section of PayPal.', 'wishlist-member' ); ?></p>
			<ul>
				<li><strong>Notification URL</strong>: <?php _e( 'Any URL can be used but it cannot be left blank. The site homepage URL is recommend.', 'wishlist-member' ); ?></li>
				<li><strong>IPN Messages</strong>: <?php _e( 'Receive IPN messages (Enabled)', 'wishlist-member' ); ?></li>
			</ul>
		</div>
	</div>
	<input type="hidden" class="-url" name="ppthankyou" />
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</form>