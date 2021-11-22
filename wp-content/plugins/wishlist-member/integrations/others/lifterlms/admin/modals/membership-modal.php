<?php
foreach($memberships AS $membership_id => $membership) :
?>
<div
	data-process="modal"
	id="lifterlms-membership-<?php echo $membership_id; ?>-template" 
	data-id="lifterlms-membership-<?php echo $membership_id; ?>"
	data-label="lifterlms-membership-<?php echo $membership_id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Actions for <?php echo $membership["title"]; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-lifterlms-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#lifterlms-membership-add-<?php echo $membership_id; ?>">When Added to a Lifter Membership</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#lifterlms-membership-remove-<?php echo $membership_id; ?>">When Removed from a Lifter Membership</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php $c_actions = ['add','remove']; ?>
			<?php foreach ( $c_actions as $action ) : ?>
				<div class="row tab-pane <?php echo $action == 'add' ? 'active in' : ''; ?>" id="lifterlms-membership-<?php echo $action; ?>-<?php echo $membership_id; ?>">
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Add to Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select lifterlms-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="lifterlms_settings[membership][<?php echo $membership_id; ?>][<?php echo $action; ?>][add_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Cancel from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select lifterlms-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="lifterlms_settings[membership][<?php echo $membership_id; ?>][<?php echo $action; ?>][cancel_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Remove from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select lifterlms-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="lifterlms_settings[membership][<?php echo $membership_id; ?>][<?php echo $action; ?>][remove_level][]"></select>
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
