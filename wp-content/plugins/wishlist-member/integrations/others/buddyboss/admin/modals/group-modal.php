<?php
foreach($groups AS $group_id => $group) :
?>
<div
	data-process="modal"
	id="buddyboss-group-<?php echo $group_id; ?>-template" 
	data-id="buddyboss-group-<?php echo $group_id; ?>"
	data-label="buddyboss-group-<?php echo $group_id; ?>"
	data-title="Editing Group Actions for <strong><?php echo $group["title"]; ?></strong>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-buddyboss-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#buddyboss-group-add-<?php echo $group_id; ?>">When Added to this Group</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#buddyboss-group-remove-<?php echo $group_id; ?>">When Removed from this Group</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php $c_actions = ['add','remove']; ?>
			<?php foreach ( $c_actions as $action ) : ?>
				<div class="row tab-pane <?php echo $action == 'add' ? 'active in' : ''; ?>" id="buddyboss-group-<?php echo $action; ?>-<?php echo $group_id; ?>">
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Add to Level', 'wishlist-member' ); ?></label>
							<select class="buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[group][<?php echo $group_id; ?>][<?php echo $action; ?>][add_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Cancel from Level', 'wishlist-member' ); ?></label>
							<select class="buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[group][<?php echo $group_id; ?>][<?php echo $action; ?>][cancel_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Remove from Level', 'wishlist-member' ); ?></label>
							<select class="buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[group][<?php echo $group_id; ?>][<?php echo $action; ?>][remove_level][]"></select>
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
