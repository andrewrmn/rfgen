<?php
foreach($member_types AS $type_id => $type ) :
?>
<div
	data-process="modal"
	id="buddyboss-types-<?php echo $type_id; ?>-template" 
	data-id="buddyboss-types-<?php echo $type_id; ?>"
	data-label="buddyboss-types-<?php echo $type_id; ?>"
	data-title="Editing Profile Type Actions for <strong><?php echo $type["title"]; ?></strong>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-buddyboss-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#buddyboss-type-add-<?php echo $type_id; ?>">When Added to this Profile Type</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#buddyboss-type-remove-<?php echo $type_id; ?>">When Removed from this Profile Type</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php $c_actions = ['add','remove']; ?>
			<?php foreach ( $c_actions as $action ) : ?>
				<div class="row tab-pane <?php echo $action == 'add' ? 'active in' : ''; ?>" id="buddyboss-type-<?php echo $action; ?>-<?php echo $type_id; ?>">
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Add to Level', 'wishlist-member' ); ?></label>
							<select class="buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[type][<?php echo $type_id; ?>][<?php echo $action; ?>][add_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Cancel from Level', 'wishlist-member' ); ?></label>
							<select class="buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[type][<?php echo $type_id; ?>][<?php echo $action; ?>][cancel_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Remove from Level', 'wishlist-member' ); ?></label>
							<select class="buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[type][<?php echo $type_id; ?>][<?php echo $action; ?>][remove_level][]"></select>
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
