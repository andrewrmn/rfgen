<?php
foreach($wpm_levels AS $level_id => $level) :
?>
<div
	data-process="modal"
	id="wpcourseware-levels-<?php echo $level_id; ?>-template" 
	data-id="wpcourseware-levels-<?php echo $level_id; ?>"
	data-label="wpcourseware-levels-<?php echo $level_id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Actions for <?php echo $level['name']; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-wpcourseware-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#wpcourseware-when-added-<?php echo $level_id; ?>">When Added</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#wpcourseware-when-cancelled-<?php echo $level_id; ?>">When Cancelled</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#wpcourseware-when-reregistered-<?php echo $level_id; ?>">When Re-Registered</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#wpcourseware-when-removed-<?php echo $level_id; ?>">When Removed</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<div class="row tab-pane active in" id="wpcourseware-when-added-<?php echo $level_id; ?>">
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Enroll in Course', 'wishlist-member' ); ?></label>
						<select class="wlm-select wpcourseware-courses-select add apply-course" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="wpcourseware_settings[level][<?php echo $level_id; ?>][add][apply_course][]"></select>
					</div>
				</div>
				<div class="col-md-12 add-checkboxes form-group d-none"><div><label><?php _e( 'Enroll existing members:', 'wishlist-member' ) ?></label></div></div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Remove from Course', 'wishlist-member' ); ?></label>
						<select class="wlm-select wpcourseware-courses-select add remove-course" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="wpcourseware_settings[level][<?php echo $level_id; ?>][add][remove_course][]"></select>
					</div>
				</div>
			</div>
			<div class="row tab-pane" id="wpcourseware-when-cancelled-<?php echo $level_id; ?>">
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Enroll in Course', 'wishlist-member' ); ?></label>
						<select class="wlm-select wpcourseware-courses-select" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="wpcourseware_settings[level][<?php echo $level_id; ?>][cancel][apply_course][]"></select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Remove from Course', 'wishlist-member' ); ?></label>
						<select class="wlm-select wpcourseware-courses-select" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="wpcourseware_settings[level][<?php echo $level_id; ?>][cancel][remove_course][]"></select>
					</div>
				</div>
			</div>
			<div class="row tab-pane" id="wpcourseware-when-reregistered-<?php echo $level_id; ?>">
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Enroll in Course', 'wishlist-member' ); ?></label>
						<select class="wlm-select wpcourseware-courses-select" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="wpcourseware_settings[level][<?php echo $level_id; ?>][rereg][apply_course][]"></select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Remove from Course', 'wishlist-member' ); ?></label>
						<select class="wlm-select wpcourseware-courses-select" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="wpcourseware_settings[level][<?php echo $level_id; ?>][rereg][remove_course][]"></select>
					</div>
				</div>
			</div>
			<div class="row tab-pane" id="wpcourseware-when-removed-<?php echo $level_id; ?>">
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Enroll in Course', 'wishlist-member' ); ?></label>
						<select class="wlm-select wpcourseware-courses-select" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="wpcourseware_settings[level][<?php echo $level_id; ?>][remove][apply_course][]"></select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Remove from Course', 'wishlist-member' ); ?></label>
						<select class="wlm-select wpcourseware-courses-select" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="wpcourseware_settings[level][<?php echo $level_id; ?>][remove][remove_course][]"></select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
endforeach;
?>
