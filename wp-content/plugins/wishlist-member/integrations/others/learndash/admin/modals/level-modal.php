<?php
foreach($wpm_levels AS $level_id => $level) :
?>
<div
	data-process="modal"
	id="learndash-levels-<?php echo $level_id; ?>-template" 
	data-id="learndash-levels-<?php echo $level_id; ?>"
	data-label="learndash-levels-<?php echo $level_id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Actions for <?php echo $level['name']; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-learndash-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<?php foreach ($level_actions as $key => $value) : ?>
						<li class="<?php echo $key=='add' ? 'active':''; ?> nav-item"><a class="nav-link" data-toggle="tab" href="#learndash-when-<?php echo $key; ?>-<?php echo $level_id; ?>">When <?php echo $value; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php foreach ($level_actions as $key => $value) : ?>
				<div class="row tab-pane <?php echo $key=='add' ? 'active in':''; ?> px-2" id="learndash-when-<?php echo $key; ?>-<?php echo $level_id; ?>">
					<div class="horizontal-tabs">
						<div class="row no-gutters">
							<div class="col-12 col-md-auto">
								<!-- Nav tabs -->
								<div class="horizontal-tabs-sidebar">
									<ul class="nav nav-tabs -h-tabs flex-column" role="tablist">
										<li role="presentation" class="nav-item">
											<a href="#<?php echo $level_id; ?>-<?php echo $key; ?>-learndash-course" class="nav-link pp-nav-link active" aria-controls="course" role="tab" data-type="course" data-title="Course Actions" data-toggle="tab">Course</a>
											<a href="#<?php echo $level_id; ?>-<?php echo $key; ?>-learndash-group" class="nav-link pp-nav-link" aria-controls="group" role="tab" data-type="group" data-title="Group Actions" data-toggle="tab">Group</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="col">
								<!-- Tab panes -->
								<div class="tab-content">
										<div role="tabpanel" class="tab-pane active" id="<?php echo $level_id; ?>-<?php echo $key; ?>-learndash-course">
											<div class="col-md-12">
												<div class="form-group">
													<label><?php _e( 'Enroll in Course', 'wishlist-member' ); ?></label>
													<select class="learndash-courses-select <?php echo $key; ?> apply-course" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="learndash_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][apply_course][]"></select>
												</div>
											</div>
											<?php if( $key == 'add' ) : ?>
											<div class="col-md-12 add-checkboxes form-group d-none"><div><label><?php _e( 'Enroll existing members:', 'wishlist-member' ) ?></label></div></div>
											<?php endif; ?>
											<div class="col-md-12">
												<div class="form-group">
													<label><?php _e( 'Remove from Course', 'wishlist-member' ); ?></label>
													<select class="learndash-courses-select <?php echo $key; ?> remove-course" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="learndash_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][remove_course][]"></select>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane" id="<?php echo $level_id; ?>-<?php echo $key; ?>-learndash-group">
											<div class="col-md-12">
												<div class="form-group">
													<label><?php _e( 'Add to Group', 'wishlist-member' ); ?></label>
													<select class="learndash-groups-select" multiple="multiple" data-placeholder="Select Groups..." style="width:100%" name="learndash_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][apply_group][]"></select>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label><?php _e( 'Remove from Group', 'wishlist-member' ); ?></label>
													<select class="learndash-groups-select" multiple="multiple" data-placeholder="Select Groups..." style="width:100%" name="learndash_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][remove_group][]"></select>
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
