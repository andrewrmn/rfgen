<?php
foreach($courses AS $course_id => $course) :
?>
<div
	data-process="modal"
	id="wpcourseware-course-<?php echo $course_id; ?>-template" 
	data-id="wpcourseware-course-<?php echo $course_id; ?>"
	data-label="wpcourseware-course-<?php echo $course_id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Actions for <?php echo $course["title"]; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-wpcourseware-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#wpcourseware-course-add-<?php echo $course_id; ?>">When Added to a Course</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#wpcourseware-course-complete-<?php echo $course_id; ?>">When Course is Completed</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#wpcourseware-course-remove-<?php echo $course_id; ?>">When Removed from a Course</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php $c_actions = ['add','complete','remove']; ?>
			<?php foreach ( $c_actions as $action ) : ?>
				<div class="row tab-pane <?php echo $action == 'add' ? 'active in' : ''; ?>" id="wpcourseware-course-<?php echo $action; ?>-<?php echo $course_id; ?>">
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Add to Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select wpcourseware-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="wpcourseware_settings[course][<?php echo $course_id; ?>][<?php echo $action; ?>][add_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Cancel from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select wpcourseware-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="wpcourseware_settings[course][<?php echo $course_id; ?>][<?php echo $action; ?>][cancel_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Remove from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select wpcourseware-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="wpcourseware_settings[course][<?php echo $course_id; ?>][<?php echo $action; ?>][remove_level][]"></select>
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
