<div class="row">
	<div class="col-md-12">
		<label><?php _e( 'Optional Cron Job Settings', 'wishlist-member' ); ?></label>
	</div>
	<div class="col-md-8">
			<p><?php _e( 'WishList Member uses built-in', 'wishlist-member' ); ?> <a href="https://codex.wordpress.org/Function_Reference/wp_schedule_event" target="_blank">WordPress Cron</a> <?php _e( 'to sync member\'s membership level status with its corresponding Authorize.net ARB transactions twice a day.', 'wishlist-member' ); ?></p>
			<p><?php _e( 'In case your site is having issues with WordPress Cron or you want to sync in different and regular interval, you can setup your server cron job using details below.', 'wishlist-member' ); ?></p>

			<p><?php _e( 'Settings:', 'wishlist-member' ); ?></p>
			<ul>
				<code>0 0,12 * * *</code>
			</ul>

			<p><?php _e( 'Command:', 'wishlist-member' ); ?></p>
			<ul>
				<code>/usr/bin/wget -O - -q -t 1 <?php echo $data->anetarbthankyou_url; ?>?action=sync-arb</code>
			</ul>

			<p>
				<em>Copy the line above and paste it into the command line of your Cron job.<br>
				<?php _e( 'Note: If the above command doesn\'t work, please try the following instead:', 'wishlist-member' ); ?></em>
			</p>
			<ul>
				<code>/usr/bin/GET -d <?php echo $data->anetarbthankyou_url; ?>?action=sync-arb</code>
			</ul>
    	</div>
</div>