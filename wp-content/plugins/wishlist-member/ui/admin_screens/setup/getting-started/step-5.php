<div class="wizard-form step-5 d-none">
	<div class="row">
		<div class="col-md-8 col-sm-8 col-xs-8">
			<h3 class="title"><span class="number"><?php _e( '5', 'wishlist-member' ); ?></span> Integrations</h3>
		</div>
		<div class="col-md-4 col-sm-4 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="progress">
			  <div class="progress-bar -success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
			  </div>
			</div>
		</div>
	</div>
	<?php
		$show_legacy = $this->GetOption('show_legacy_integrations');

		$payment_provider = array();
		$providers = glob($this->pluginDir3.'/integrations/payments/*', GLOB_ONLYDIR);
		foreach($providers AS $provider) {
			$config = include($provider . '/config.php');
			if ( !isset( $config['legacy'] ) || $show_legacy ) {
				$payment_provider[ $config['id'] ] = empty($config['nickname']) ? $config['name'] : $config['nickname'];
			}
		}
		asort($payment_provider, SORT_STRING | SORT_FLAG_CASE);

		$email_provider = array();
		$providers = glob($this->pluginDir3.'/integrations/emails/*', GLOB_ONLYDIR);
		foreach($providers AS $provider) {
			$config = include($provider . '/config.php');
			if ( !isset( $config['legacy'] ) || $show_legacy ) {
				$email_provider[ $config['id'] ] = empty($config['nickname']) ? $config['name'] : $config['nickname'];
			}
		}
		asort($email_provider, SORT_STRING | SORT_FLAG_CASE);
	?>
	<div class="content-wrapper -no-header wizard-integration-holder level-data">
		<div class="row">
			<div class="col-md-12 no-margin">
				<label for=""><?php _e( 'Would you like to accept payments using a 3rd party payment provider? If so, you can enable a Payment Provider integration by selecting it from the list.', 'wishlist-member' ); ?></label>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<select class="form-control integration-wlm-select wlm-select" name="payment_provider" style="width: 100%" data-placeholder="Select payment provider">
						<option></option>
						<!-- <option value="none"><?php _e( 'Select payment provider', 'wishlist-member' ); ?></option> -->
						<?php foreach ( $payment_provider as $id => $name ): ?>
							<option value="<?php echo $id; ?>" ><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col-md-8" style="display: none;">
				<p class="pt-2" style="opacity: 0.5; font-style: italic;">(<?php _e( 'Further setup will be required later)', 'wishlist-member' ); ?></p>
			</div>
			<div class="col-md-12 no-margin">
				<label for=""><?php _e( 'Would you like to add your members to a mailing list using a 3rd party email service? If so, you can enable an Email Provider integration by selecting it from the list.', 'wishlist-member' ); ?></label>
			</div>
			<div class="col-md-4 no-margin">
				<div class="form-group no-margin">
					<select class="form-control integration-wlm-select wlm-select" name="email_provider" style="width: 100%" data-placeholder="Select email provider">
							<option></option>
						<!-- <option value="none"><?php _e( 'Select email provider', 'wishlist-member' ); ?></option> -->
						<?php foreach ( $email_provider as $id => $name ): ?>
							<option value="<?php echo $id; ?>" ><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col-md-8" style="display: none;">
				<p class="pt-2" style="opacity: 0.5; font-style: italic;">(<?php _e( 'Further setup will be required later)', 'wishlist-member' ); ?></p>
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
					<div class="indicator text-center">5/5</div>
				</div>
				<div class="col-sm-8 col-md-5 col-lg-5 order-sm-2">
					<div class="pull-right">
						<a href="#" class="btn -default next-btn isback" data-screen="step-5" next-screen="step-4">
							<i class="wlm-icons">arrow_back</i>
							<span><?php _e( 'Back', 'wishlist-member' ); ?></span>
						</a>
						<a href="#" class="btn -success save-btn" data-screen="step-5" next-screen="congrats">
							<i class="wlm-icons">save</i>
							<span><?php _e( 'Save', 'wishlist-member' ); ?></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>