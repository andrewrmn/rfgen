<div class="row">
	<div class="col-sm-8 col-md-6 col-xxl-4 col-xxxl-3">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Enable Default Settings for New Groups', 'wishlist-member' ); ?>',
				name  : 'wlm_bb_group_default',
				value : '1',
				checked_value : '<?php echo $wlm_bb_group_default; ?>',
				uncheck_value : '0',
				class : 'wlm_toggle-switch notification-switch',
				type  : 'toggle-adjacent-disable',
			}
		</template>
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="save" />
	</div>
	<div class="col">
		<button href="#" data-toggle="modal" data-target="#buddyboss-group-default" id="wlm_bb_group_default_btn" class="btn -primary -condensed edit-notification <?php echo $wlm_bb_group_default && $wlm_bb_group_default == "1" ? "" : "-disable"  ?>">
			<i class="wlm-icons">settings</i>
			<span><?php _e( 'Configure', 'wishlist-member' ); ?></span>
		</button>
	</div>
</div>
<div
	data-process="modal"
	id="buddyboss-group-default-template"
	data-id="buddyboss-group-default"
	data-label="buddyboss-group-default"
	data-title="Default Group Actions"
	data-show-default-footer="1"
	data-classes="modal-lg modal-buddyboss-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#buddyboss-group-add-default">When Added to a Group</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#buddyboss-group-remove-default">When Removed from a Group</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php $c_actions = ['add','remove']; ?>
			<?php foreach ( $c_actions as $action ) : ?>
				<div class="row tab-pane <?php echo $action == 'add' ? 'active in' : ''; ?>" id="buddyboss-group-<?php echo $action; ?>-default">
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Add to Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[group][default][<?php echo $action; ?>][add_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Cancel from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[group][default][<?php echo $action; ?>][cancel_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Remove from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[group][default][<?php echo $action; ?>][remove_level][]"></select>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<?php if ( $is_member_type_enabled ) : ?>
<div class="row">
	<div class="col-sm-8 col-md-6 col-xxl-4 col-xxxl-3">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Enable Default Settings for New Profile Type', 'wishlist-member' ); ?>',
				name  : 'wlm_bb_ptype_default',
				value : '1',
				checked_value : '<?php echo $wlm_bb_ptype_default; ?>',
				uncheck_value : '0',
				class : 'wlm_toggle-switch notification-switch',
				type  : 'toggle-adjacent-disable',
			}
		</template>
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="save" />
	</div>
	<div class="col">
		<button href="#" data-toggle="modal" data-target="#buddyboss-ptype-default" id="wlm_bb_ptype_default_btn" class="btn -primary -condensed edit-notification <?php echo $wlm_bb_ptype_default && $wlm_bb_ptype_default == "1" ? "" : "-disable"  ?>">
			<i class="wlm-icons">settings</i>
			<span><?php _e( 'Configure', 'wishlist-member' ); ?></span>
		</button>
	</div>
</div>

<div
	data-process="modal"
	id="buddyboss-ptype-default-template"
	data-id="buddyboss-ptype-default"
	data-label="buddyboss-ptype-default"
	data-title="Default Profile Type Actions"
	data-show-default-footer="1"
	data-classes="modal-lg modal-buddyboss-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#buddyboss-ptype-add-default">When Added to this Profile Type</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#buddyboss-ptype-remove-default">When Removed from this Profile Type</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php $c_actions = ['add','remove']; ?>
			<?php foreach ( $c_actions as $action ) : ?>
				<div class="row tab-pane <?php echo $action == 'add' ? 'active in' : ''; ?>" id="buddyboss-ptype-<?php echo $action; ?>-default">
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Add to Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[ptype][default][<?php echo $action; ?>][add_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Cancel from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[ptype][default][<?php echo $action; ?>][cancel_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Remove from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select buddyboss-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="buddyboss_settings[ptype][default][<?php echo $action; ?>][remove_level][]"></select>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php endif; ?>