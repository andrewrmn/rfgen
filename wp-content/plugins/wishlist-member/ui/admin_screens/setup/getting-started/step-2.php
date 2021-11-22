<div class="wizard-form step-2 d-none">
	<div class="row">
		<div class="col-md-8 col-sm-8 col-xs-8">
			<h3 class="title"><span class="number"><?php _e( '2', 'wishlist-member' ); ?></span> Level Requirements</h3>
		</div>
		<div class="col-md-4 col-sm-4 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="progress">
			  <div class="progress-bar -success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 40%;">
			  </div>
			</div>
		</div>
	</div>
	<div class="content-wrapper -no-header level-data">
		<div class="row">
			<div class="col-md-12">
				<h2><?php _e( 'Level Name:', 'wishlist-member' ); ?> <span class="level-name-holder"><?php echo $level_data["name"]; ?></span></h2>
				<div class="row">
					<div class="col-md-12">
						<p><?php _e( 'When members register for this level, they can be given immediate access or their status can be set to pending until certain criteria has been met.', 'wishlist-member' ); ?></p>
						<div class="row">
							<div class="col-md-12">
								<div class="form-check -with-tooltip">
									<label class="cb-container">Require Admin Approval
										<?php $requireadminapproval =  isset( $level_data["requireadminapproval"] ) ? $level_data["requireadminapproval"] : ''; ?>
										<input name="requireadminapproval" value="1" type="checkbox" <?php echo $requireadminapproval == "1" ? "checked='checked'" : '' ?> >
										<span class="checkmark"></span>
									</label>
								</div>
							</div>
							<div class="col-md-12 mt-2">
								<div class="form-check -with-tooltip">
									<label class="cb-container">Require Member to Confirm Email Address
										<?php $requireemailconfirmation =  isset( $level_data["requireemailconfirmation"] ) ? $level_data["requireemailconfirmation"] : ''; ?>
										<input name="requireemailconfirmation" value="1" type="checkbox" <?php echo $requireemailconfirmation == "1" ? "checked='checked'" : '' ?> >
										<span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<br>
						<p><?php _e( 'Members can be required to agree to certain Terms and Conditions of the site during registration. (Note: This can always be edited later.)', 'wishlist-member' ); ?></p>
						<div class="row">
							<div class="col-md-12" style="margin-bottom: 10px;">
								<div class="form-check -with-tooltip">
									<label class="cb-container">Require Terms and Conditions
										<?php $enable_tos =  isset( $level_data["enable_tos"] ) ? $level_data["enable_tos"] : ''; ?>
										<input type="checkbox" name="enable_tos" id="enable_tos" value="1" class="wlm_toggle-adjacent" uncheck_value="0" <?php echo $enable_tos == "1" ? "checked='checked'" : '' ?> >
										<span class="checkmark"></span>
									</label>
								</div>
							</div>
							<div class="col-md-12" <?php echo $enable_tos == "1" ? "" : "style='display:none;'"; ?> >
								<?php $tos = isset( $level_data["tos"] ) ? $level_data["tos"] : "I agree to the following Terms and Conditions."; ?>
								<textarea name="tos" id="" cols="60" rows="6" class="mb-3 w-100"><?php echo  $tos; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer -content-footer">
			<div class="row">
				<div class="col-sm-4 col-lg-3 col-md-3 order-sm-1 order-md-0">
					<div class="pull-left">
						<a href="#" class="btn -outline -bare isexit" data-screen="thanks"><?php _e( 'Exit Wizard', 'wishlist-member' ); ?></a>
					</div>
				</div>
				<div class="col-sm-12 col-md-4 col-lg-4 order-sm-0">
					<div class="indicator text-center">2/5</div>
				</div>
				<div class="col-sm-8 col-md-5 col-lg-5 order-sm-2">
					<div class="pull-right">
						<a href="#" class="btn -default next-btn isback" data-screen="step-2" next-screen="step-1">
							<i class="wlm-icons">arrow_back</i>
							<span><?php _e( 'Back', 'wishlist-member' ); ?></span>
						</a>
						<a href="#" class="btn -primary next-btn" data-screen="step-2" next-screen="step-3">
							<span><?php _e( 'Next', 'wishlist-member' ); ?></span>
							<i class="wlm-icons">arrow_forward</i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>