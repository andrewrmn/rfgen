<?php $wpm_levels = $this->GetOption('wpm_levels'); ?>
<div class="wizard-form start">
	<div class="content-wrapper -no-header">
		<div class="header">
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-3 order-sm-0">
					<div class="logo-container text-center text-sm-left">
						<img src="<?php echo $this->pluginURL3; ?>/ui/images/wlm-logo-small.png" class="" alt="">
					</div>
				</div>
				<div class="col-xs-6 col-sm-12 col-md-6 order-sm-2 order-md-1 text-sm-center text-md-left">
					<h2><?php _e( 'Getting Started Wizard', 'wishlist-member' ); ?></h2>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-3 col-sm-4 order-sm-1">
					<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<p class="sub-title"><?php _e( 'Your site already has at least one membership level. Click on the level name below in order to modify it. You can also add another level.', 'wishlist-member' ); ?></p>
				<a href="#" data-screen="start" next-screen="step-1" class="btn -success next-btn">
					<i class="wlm-icons">add</i>
					<span><?php _e( 'Add Level', 'wishlist-member' ); ?></span>
				</a>
				<?php if ( count($wpm_levels) > 0 ): ?>
					<br><br><br>
					<div class="table-wrapper table-responsive">
						<table class="table table-striped table-condensed table-fixed">
							<thead>
								<tr class="d-flex">
									<th class="col-12"><?php _e( 'Level Name', 'wishlist-member' ); ?></th>
								</tr>
							</thead>
							<tbody style="max-height:208px">
								<?php foreach ( $wpm_levels as $level_id => $level ) : ?>
									<tr class="d-flex">
										<td class="col-12">
											<a href="#" data-screen="start" next-screen="step-1" class="next-btn"><?php echo $level['name']; ?>
												<input type='hidden' name='levelid' value='<?php echo $level_id; ?>' />
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( !$this->GetOption('wizard_ran') ) : ?>
			<div class="panel-footer -content-footer text-center">
				<a href="#" class="btn -outline -bare isexit" data-screen="thanks"><?php _e( 'Exit Wizard', 'wishlist-member' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</div>