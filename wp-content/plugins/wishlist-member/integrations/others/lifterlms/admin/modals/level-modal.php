

<?php
foreach($wpm_levels AS $level_id => $level) :
?>
<div
	data-process="modal"
	id="lifterlms-levels-<?php echo $level_id; ?>-template" 
	data-id="lifterlms-levels-<?php echo $level_id; ?>"
	data-label="lifterlms-levels-<?php echo $level_id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Actions for <?php echo $level['name']; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-lifterlms-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<?php foreach ($level_actions as $key => $value) : ?>
						<li class="<?php echo $key=='add' ? 'active':''; ?> nav-item"><a class="nav-link" data-toggle="tab" href="#lifterlms-when-<?php echo $key; ?>-<?php echo $level_id; ?>">When <?php echo $value; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php foreach ($level_actions as $key => $value) : ?>
				<div class="row tab-pane <?php echo $key=='add' ? 'active in':''; ?> px-2" id="lifterlms-when-<?php echo $key; ?>-<?php echo $level_id; ?>">
					<div class="horizontal-tabs">
						<div class="row no-gutters">
							<div class="col-12 col-md-auto">
								<!-- Nav tabs -->
								<div class="horizontal-tabs-sidebar">
									<ul class="nav nav-tabs -h-tabs flex-column" role="tablist">
										<li role="presentation" class="nav-item">
											<a href="#<?php echo $level_id; ?>-<?php echo $key; ?>-lifterlms-course" class="nav-link pp-nav-link active" aria-controls="course" role="tab" data-type="course" data-title="Course Actions" data-toggle="tab">Course</a>
											<a href="#<?php echo $level_id; ?>-<?php echo $key; ?>-lifterlms-membership" class="nav-link pp-nav-link" aria-controls="membership" role="tab" data-type="membership" data-title="Lifter Membership Actions" data-toggle="tab">Lifter Membership</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="col">
								<!-- Tab panes -->
								<div class="tab-content">
										<div role="tabpanel" class="tab-pane active" id="<?php echo $level_id; ?>-<?php echo $key; ?>-lifterlms-course">
											<div class="col-md-12">
												<div class="form-group">
													<label><?php _e( 'Enroll in Course', 'wishlist-member' ); ?></label>
													<select class="wlm-select lifterlms-courses-select <?php echo $key; ?> apply-course" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="lifterlms_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][apply_course][]"></select>
												</div>
											</div>
											<?php if( $key == 'add' ) : ?>
											<div class="col-md-12 add-checkboxes form-group d-none"><div><label><?php _e( 'Enroll existing members:', 'wishlist-member' ) ?></label></div></div>
											<?php endif; ?>
											<div class="col-md-12">
												<div class="form-group">
													<label><?php _e( 'Remove from Course', 'wishlist-member' ); ?></label>
													<select class="wlm-select lifterlms-courses-select <?php echo $key; ?> remove-course" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="lifterlms_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][remove_course][]"></select>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane" id="<?php echo $level_id; ?>-<?php echo $key; ?>-lifterlms-membership">
											<div class="col-md-12">
												<div class="form-group">
													<label><?php _e( 'Add to Lifter Membership', 'wishlist-member' ); ?></label>
													<select class="wlm-select lifterlms-memberships-select" multiple="multiple" data-placeholder="Select Memberships..." style="width:100%" name="lifterlms_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][apply_membership][]"></select>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label><?php _e( 'Remove from Lifter Membership', 'wishlist-member' ); ?></label>
													<select class="wlm-select lifterlms-memberships-select" multiple="multiple" data-placeholder="Select Memberships..." style="width:100%" name="lifterlms_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][remove_membership][]"></select>
												</div>
											</div>
										</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php
endforeach;
?>

