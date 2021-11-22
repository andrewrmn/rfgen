<div class="wizard-form step-1">
	<div class="row">
		<div class="col-md-8 col-sm-8 col-xs-8">
			<h3 class="title"><span class="number"><?php _e( '1', 'wishlist-member' ); ?></span> Level Setup</h3>
		</div>
		<div class="col-md-4 col-sm-4 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="progress">
			  <div class="progress-bar -success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
			  </div>
			</div>
		</div>
	</div>
	<?php
		$level_names = [];
		foreach ($wpm_levels as $key => $value) {
			if ( $level_data["name"] != $value['name']  ) $level_names[] = $value['name'];
		}
	?>
	<script type="text/javascript">
		var $levelnames = <?php echo json_encode($level_names); ?>
	</script>
	<div class="content-wrapper -no-header level-data">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group large-form">
							<label for="">Level Name</label>
							<input type="text" name="name" value="<?php echo isset( $level_data["name"] ) ? $level_data["name"] : ''; ?>" class="form-control input-lg level-name" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h4><?php _e( 'How long will members have access to this level?', 'wishlist-member' ); ?></h4>
						<div class="row">
							<?php $expire_option =  isset( $level_data["expire_option"] ) ? $level_data["expire_option"] : 0; ?>
							<!-- start: v4 -->
							<div class="col-md-4">
								<template class="wlm3-form-group">
									{
										label : '<?php _e( 'Ongoing', 'wishlist-member' ); ?>',
										name  : 'expire_option',
										id : 'AccessLength01',
										value : '0',
										checked_value : '<?php echo $expire_option; ?>',
										type  : 'radio',
										tooltip: '<?php _e( 'An ongoing membership will give access with no specific expiration. A member\\\'s access will be cancelled if a payment tied to an integration fails to process successfully.', 'wishlist-member' ); ?>',
										tooltip_size: 'md',
										column: 'mb-2',

									}
								</template>
								<template class="wlm3-form-group">
									{
										label : '<?php _e( 'Fixed Term', 'wishlist-member' ); ?>',
										name  : 'expire_option',
										id : 'AccessLength02',
										value : '1',
										checked_value : '<?php echo $expire_option; ?>',
										type  : 'radio',
										tooltip: '<?php _e( 'When using a fixed term expiration a level can be scheduled to automatically expire after a certain amount of time. It can be a specified number of Days, Weeks, Months or Years.', 'wishlist-member' ); ?>',
										tooltip_size: 'md',
										column: 'mb-2',

									}
								</template>
								<template class="wlm3-form-group">
									{
										label : '<?php _e( 'Specific Date', 'wishlist-member' ); ?>',
										name  : 'expire_option',
										id : 'AccessLength03',
										value : '2',
										checked_value : '<?php echo $expire_option; ?>',
										type  : 'radio',
										tooltip: '<?php _e( 'When using a Specific Date expiration a level can be scheduled to automatically expire on a specific date. A date is chosen from a calendar and all members will expire on the same date.', 'wishlist-member' ); ?>',
										tooltip_size: 'md',
										column: 'mb-2',

									}
								</template>
							</div>
							<!-- end: v4 -->
							<?php $exp_op_padding = $expire_option == 2 ? "35" : "15"; ?>
							<div class="col-md-3 expire_option" style="padding-top: <?php echo $exp_op_padding; ?>px;">
								<div class="form-inline -combo-form" <?php echo $expire_option != 1 ? "style='display:none;'" : ""; ?> >
									<div>
										<label class="sr-only" for="">Fixed Term</label>
										<div class="input-group">
											<?php $expire = isset( $level_data["expire"] ) && $expire_option == 1  ? $level_data["expire"] : $this->level_defaults['expire']; ?>
											<input type="number" style="width: 35%" name="expire" class="form-control text-center" value="<?php echo $expire; ?>">
											<?php $calendar = isset( $level_data["calendar"] ) && $expire_option == 1  ? $level_data["calendar"] : $this->level_defaults['calendar']; ?>
											<select class="form-control wlm-select" name="calendar" style="width: 65%;" tabindex="-1" aria-hidden="true">
												<option value="Days" <?php echo $calendar == "Days" ? "selected='selected'" : ""; ?> >Day(s)</option>
												<option value="Weeks" <?php echo $calendar == "Weeks" ? "selected='selected'" : ""; ?> >Week(s)</option>
												<option value="Months" <?php echo $calendar == "Months" ? "selected='selected'" : ""; ?> >Month(s)</option>
												<option value="Years" <?php echo $calendar == "Years" ? "selected='selected'" : ""; ?> >Year(s)</option>
											</select>
										</div>
									</div>
								</div>
								<div class="date-ranger" <?php echo $expire_option != 2 ? "style='display:none;'" : ""; ?>>
									<label class="sr-only" for="">Specific Date</label>
									<div class="date-ranger-container">
										<?php $expire_date = isset( $level_data["expire_date"] ) && $expire_option == 2  ? $level_data["expire_date"] : date(get_option('date_format')); ?>
										<input type="text" name="expire_date" class="form-control" placeholder="" value="<?php echo $expire_date; ?>">
										<i class="wlm-icons">date_range</i>
									</div>
								</div>
							</div>
						</div>
						<p class="mt-3"><?php _e( 'In some cases it may be best to have specific control over access to specific types of content. In other cases it may be convenient to automatically give access to certain types of content. ', 'wishlist-member' ); ?></p>
						<h4>
							<?php _e( 'Would you like to automatically give this level access to specific content?', 'wishlist-member' ); ?>
							<?php $this->tooltip(__('<p>Enabling any of these settings will provide access to all protected content for any member of this level. These settings are helpful when you have one level that should have access to everything.</p><p>These settings do not apply to unprotected content. All members and non-members already have access to all unprotected content.</p>', 'wishlist-member'), 'xxl'); ?>
						</h4>
						<div class="row">
							<div class="col-md-3">
								<div class="form-check -with-tooltip mt-2">
									<label class="cb-container">All Posts
										<?php $allposts =  isset( $level_data["allposts"] ) ? $level_data["allposts"] : ''; ?>
										<input name="allposts" value="on" type="checkbox" <?php echo $allposts == "on" ? "checked='checked'" : '' ?> >
										<span class="checkmark"></span>
									</label>
								</div>
							</div>
							<div class="col-md-9">
								<div class="form-check -with-tooltip mt-2">
									<label class="cb-container">All Categories
										<?php $allcategories =  isset( $level_data["allcategories"] ) ? $level_data["allcategories"] : ''; ?>
										<input name="allcategories" value="on" type="checkbox" <?php echo $allcategories == "on" ? "checked='checked'" : '' ?> >
										<span class="checkmark"></span>
									</label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-check -with-tooltip mt-2">
									<label class="cb-container">All Pages
										<?php $allpages =  isset( $level_data["allpages"] ) ? $level_data["allpages"] : ''; ?>
										<input name="allpages" value="on" type="checkbox" <?php echo $allpages == "on" ? "checked='checked'" : '' ?> >
										<span class="checkmark"></span>
									</label>
								</div>
							</div>
							<div class="col-md-9">
								<div class="form-check -with-tooltip mt-2">
									<label class="cb-container">All Comments
										<?php $allcomments =  isset( $level_data["allcomments"] ) ? $level_data["allcomments"] : ''; ?>
										<input name="allcomments" value="on" type="checkbox" <?php echo $allcomments == "on" ? "checked='checked'" : '' ?> >
										<span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<br>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer -content-footer">
			<div class="row">
				<div class="col-sm-4 col-md-3 col-lg-3 order-sm-1 order-md-0">
					<div class="pull-left">
						<a href="#" class="btn -outline -bare isexit mb-sm-0" data-screen="thanks"><?php _e( 'Exit Wizard', 'wishlist-member' ); ?></a>
					</div>
				</div>
				<div class="col-sm-12 col-md-4 col-lg-4 order-sm-0">
					<div class="indicator text-center">1/5</div>
				</div>
				<div class="col-sm-8 col-md-5 col-lg-5 order-sm-2">
					<div class="pull-right">
						<?php if ( count($wpm_levels) > 0 ): ?>
							<a href="#" class="btn -default next-btn isback" data-screen="step-1" next-screen="start">
								<i class="wlm-icons">arrow_back</i>
								<span><?php _e( 'Back', 'wishlist-member' ); ?></span>
							</a>
						<?php endif; ?>
						<a href="#" class="btn -primary next-btn" data-screen="step-1" next-screen="step-2">
							<span><?php _e( 'Next', 'wishlist-member' ); ?></span>
							<i class="wlm-icons">arrow_forward</i>
							<?php if ( $levelid ): ?>
								<input type='hidden' name='levelid' value='<?php echo $levelid; ?>' />
							<?php endif; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	include( $this->pluginDir3 ."/ui/admin_screens/setup/getting-started/step-2.php");
	include( $this->pluginDir3 ."/ui/admin_screens/setup/getting-started/step-3.php");
	include( $this->pluginDir3 ."/ui/admin_screens/setup/getting-started/step-4.php");
	include( $this->pluginDir3 ."/ui/admin_screens/setup/getting-started/step-5.php");
?>
