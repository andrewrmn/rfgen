<div class="wizard-form step-4 d-none">
	<div class="row">
		<div class="col-md-8 col-sm-8 col-xs-8">
			<h3 class="title"><span class="number"><?php _e( '4', 'wishlist-member' ); ?></span> Email Setup</h3>
		</div>
		<div class="col-md-4 col-sm-4 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="progress">
			  <div class="progress-bar -success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 80%;">
			  </div>
			</div>
		</div>
	</div>
	<div class="content-wrapper -no-header level-data">
		<div class="row">
			<div class="col-md-12">
				<h4 class="mb-4"><?php _e( 'WishList Member will send various email messages to members.', 'wishlist-member' ); ?></h4>
				<h4><?php _e( 'Messages will be sent from:', 'wishlist-member' ); ?></h4>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Name</label>
							<input type="text" name="email_sender_name" class="form-control" value="<?php echo $this->GetOption("email_sender_name"); ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?php _e( 'Email Address', 'wishlist-member' ); ?></label>
							<input type="text" name="email_sender_address" class="form-control" value="<?php echo $this->GetOption("email_sender_address"); ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer -content-footer">
			<div class="row">
				<div class="col-sm-4 col-md-3 col-lg-3 order-sm-1 order-md-0">
					<div class="pull-left">
						<a href="#" class="btn -outline -bare isexit" data-screen="thanks"><?php _e( 'Exit Wizard', 'wishlist-member' ); ?></a>
					</div>
				</div>
				<div class="col-sm-12 col-md-4 col-lg-4 order-sm-0">
					<div class="indicator text-center">4/5</div>
				</div>
				<div class="col-sm-8 col-md-5 col-lg-5 order-sm-2">
					<div class="pull-right">
						<a href="#" class="btn -default next-btn isback" data-screen="step-4" next-screen="step-3">
							<i class="wlm-icons">arrow_back</i>
							<span><?php _e( 'Back', 'wishlist-member' ); ?></span>
						</a>
						<a href="#" class="btn -primary next-btn" data-screen="step-4" next-screen="step-5">
							<span><?php _e( 'Next', 'wishlist-member' ); ?></span>
							<i class="wlm-icons">arrow_forward</i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>