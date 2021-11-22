<div class="row align-items-center no-gutter wizard-form -dark content-wrapper -congrats-gs">
	<div class="col-md-5">
		<div class="information text-center">
			<img src="<?php echo $this->pluginURL3; ?>/ui/images/wishlist-member-logo.png" class="img-fluid" alt="">
		</div>
	</div>
	<div class="col-md-7">
		<div class="white-background">
			<br><br>
			<h4><?php _e( 'Important Details:', 'wishlist-member' ); ?></h4>
			<?php if ( $data["payment_provider"] || $data["email_provider"] ): ?>
				<span class="form-text text-danger help-block">
					<p><?php _e( 'You have at least one 3rd Party Integration enabled. Further setup is required.', 'wishlist-member' ); ?></p>
					<p class="no-margin no-padding">
						Integrations can be configured in the Setup > <a target="_parent" href="?page=WishListMember&wl=setup/integrations/payment_provider"><?php _e( 'Integrations section', 'wishlist-member' ); ?></a>
					</p>
				</span>
			<?php endif; ?>
			<p><?php _e( 'The setup details you configured can be modified at any time by running the Getting Started Wizard again. Alternatively you can configure any of these settings individually throughout WishList Member.', 'wishlist-member' ); ?></p>
			<br><br>
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-6 text-center text-lg-right">
					<a href="#" class="btn -primary -lg -no-icon next-btn" data-screen="thanks" next-screen="start">
						<span><?php _e( 'Run the Wizard Again...', 'wishlist-member' ); ?></span>
					</a>						
				</div>
				<div class="col-sm-12 col-md-12 col-lg-2 text-center mt-2 mb-2">
					<span class="or"><?php _e( 'OR', 'wishlist-member' ); ?></span>
				</div>
				<div class="col-sm-12 col-md-12 col-lg-4 text-center text-lg-left">
					<a href="#" class="btn -success -lg -no-icon next-btn" data-screen="thanks" next-screen="home">
						<span><?php _e( 'Exit Wizard', 'wishlist-member' ); ?></span>
					</a>						
				</div>
			</div>
			<br><br>
		</div>
	</div>
</div>

