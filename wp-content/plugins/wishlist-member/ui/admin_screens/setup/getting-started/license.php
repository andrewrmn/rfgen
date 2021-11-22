<div class="wizard-form -dark">
	<div class="content-wrapper -no-header level-data">
		<div class="row align-items-center">
			<div class="col-md-5">
				<div class="information text-center">
					<img src="<?php echo $this->pluginURL3; ?>/ui/images/wishlist-member-logo.png" class="mx-auto d-block" alt="">
				</div>
			</div>
			<div class="col-md-7">
				<div class="white-background">
					<div class="row">
						<div class="col-md-12">
							<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
						</div>
					</div>
					<h4 class="mb-3"><?php _e( 'Enter your WishList Products License Key to activate.', 'wishlist-member' ); ?></h4>
					<p><?php _e( 'Your WishList Products License Key was sent to you in an email after purchase. It can also be found in the Customer Center.', 'wishlist-member' ); ?></p>
					<p><?php _e( 'Don\'t have a License Key? <a href="https://member.wishlistproducts.com/" target="_blank">Get one here.</a>', 'wishlist-member' ); ?></p>
					<div class="form-group large-form">
						<label for=""><?php _e( 'License Key', 'wishlist-member' ); ?></label>
						<?php $license = $this->GetOption('LicenseKey'); ?>
						<input type="text" name="license" class="form-control input-lg mb-0" value="<?php echo $license ?>">
					</div>
					<div class="row">
						<div class="col-md-12 text-center">
							<a href="#" data-screen="license" next-screen="license-confirm" class="btn -primary -lg pull-left save-btn">
								<?php _e( 'Activate License', 'wishlist-member' ); ?>
								<i class="wlm-icons">arrow_forward</i>
							</a>
							<a href="#" class="btn -bare -lg pull-right skip-license">
								<?php _e( 'Skip', 'wishlist-member' ); ?>
							</a>
						</div>
						<div class="col-12 text-center">
							<span class="form-text text-danger help-block mt-3 d-none">
							</span>
						</div>
					</div>
				</div>
			</div>
			<br>
		</div>
	</div>
</div>