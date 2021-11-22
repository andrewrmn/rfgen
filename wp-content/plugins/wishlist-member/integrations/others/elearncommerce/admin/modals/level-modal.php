<?php
foreach($wpm_levels AS $level_id => $level) :
?>
<div
	data-process="modal"
	id="elearncommerce-levels-<?php echo $level_id; ?>-template" 
	data-id="elearncommerce-levels-<?php echo $level_id; ?>"
	data-label="elearncommerce-levels-<?php echo $level_id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Actions for <?php echo $level['name']; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-elearncommerce-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<?php foreach ($level_actions as $key => $value) : ?>
						<li class="<?php echo $key=='add' ? 'active':''; ?> nav-item"><a class="nav-link" data-toggle="tab" href="#elearncommerce-when-<?php echo $key; ?>-<?php echo $level_id; ?>">When <?php echo $value; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php foreach ($level_actions as $key => $value) : ?>
				<div class="row tab-pane <?php echo $key=='add' ? 'active in':''; ?> px-2" id="elearncommerce-when-<?php echo $key; ?>-<?php echo $level_id; ?>">
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Start a Course', 'wishlist-member' ); ?></label>
							<select class="wlm-select elearncommerce-courses-select <?php echo $key; ?> apply-course" multiple="multiple" data-placeholder="Select Courses..." style="width:100%" name="elearncommerce_settings[level][<?php echo $level_id; ?>][<?php echo $key; ?>][apply_course][]"></select>
						</div>
					</div>
					<?php if( $key == 'add' ) : ?>
					<div class="col-md-12 add-checkboxes form-group d-none"><div><label><?php _e( 'Enroll existing members:', 'wishlist-member' ) ?></label></div></div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php
endforeach;
?>
