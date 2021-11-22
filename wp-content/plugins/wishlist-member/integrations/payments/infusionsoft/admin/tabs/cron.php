<div class="row">
	<div class="col-md-12">
		<p><?php _e( 'WishList Member uses built-in', 'wishlist-member' ); ?> <a href="https://codex.wordpress.org/Function_Reference/wp_schedule_event" target="_blank">WordPress Cron</a> <?php _e( 'to sync member\'s membership level status with its corresponding Infusionsoft transaction twice a day.', 'wishlist-member' ); ?></p>
		<p><?php _e( 'In case your site is having issues with WordPress Cron or you want to sync in different and regular interval, you can setup your server cron job using details below.', 'wishlist-member' ); ?></p>		
		<p><?php _e( 'Settings:', 'wishlist-member' ); ?></p>
		<p><code>0 0,12 * * *</code></p>
		<p><?php _e( 'Command:', 'wishlist-member' ); ?></p>
		<p><code>/usr/bin/wget -O - -q -t 1 <?php echo $this->make_thankyou_url( $data->isthankyou ) ; ?>?iscron=1</code></p>
		<p><?php _e( 'Copy the line above and paste it into the command line of your Cron job.', 'wishlist-member' ); ?></p>
		<p><?php _e( 'Note: If the above command doesn\'t work, please try the following instead:', 'wishlist-member' ); ?></p>
		<p><code>/usr/bin/GET -d <?php echo $this->make_thankyou_url( $data->isthankyou ); ?>?iscron=1</code></p>
	</div>
</div>