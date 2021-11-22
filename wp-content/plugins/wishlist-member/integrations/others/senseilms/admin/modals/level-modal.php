<?php
foreach($wpm_levels AS $level_id => $level) :
?>
<div
	data-process="modal"
	id="senseilms-levels-<?php echo $level_id; ?>-template" 
	data-id="senseilms-levels-<?php echo $level_id; ?>"
	data-label="senseilms-levels-<?php echo $level_id; ?>"
	data-title="Editing Level Actions for <strong><?php echo $level['name']; ?></strong>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-senseilms-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<?php foreach ($level_actions as $key => $value) : ?>
						<li class="<?php echo $key=='add' ? 'active':''; ?> nav-item"><a class="nav-link" data-toggle="tab" href="#senseilms-when-<?php echo $key; ?>-<?php echo $level_id; ?>">When <?php echo $value; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php foreach ($level_actions as $key => $value) : ?>
				<div class="row tab-pane <?php echo $key=='add' ? 'active in':''; ?> px-2" id="senseilms-when-<?php echo $key; ?>-<?php echo $level_id; ?>">
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Start a Course', 'wishlist-member' ); ?></label>
							<select class="wlm-select senseilms-courses-select <?php echo $key; ?> apply-course" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="senseilms_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][apply_course][]"></select>
						</div>
					</div>
					<?php if( $key == 'add' ) : ?>
					<div class="col-md-12 add-checkboxes form-group d-none"><div><label><?php _e( 'Enroll existing members:', 'wishlist-member' ) ?></label></div></div>
					<?php endif; ?>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Reset and Remove from Course', 'wishlist-member' ); ?></label>
							<select class="wlm-select senseilms-courses-select <?php echo $key; ?> remove-course" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="senseilms_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][remove_course][]"></select>
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
