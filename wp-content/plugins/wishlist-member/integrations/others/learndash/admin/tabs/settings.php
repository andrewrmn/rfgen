<div class="row">
	<div class="col-sm-8 col-md-6 col-xxl-4 col-xxxl-3">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Enable default settings for new groups', 'wishlist-member' ); ?>',
				name  : 'wlm_ld_group_default',
				value : '1',
				checked_value : '<?php echo $wlm_ld_group_default; ?>',
				uncheck_value : '0',
				class : 'wlm_toggle-switch notification-switch',
				type  : 'toggle-adjacent-disable',
			}
		</template>
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="save" />
	</div>
	<div class="col">
		<button href="#" data-toggle="modal" data-target="#learndash-group-default" id="wlm_ld_group_default_btn" class="btn -primary -condensed edit-notification <?php echo $wlm_ld_group_default && $wlm_ld_group_default == "1" ? "" : "-disable"  ?>">
			<i class="wlm-icons">settings</i>
			<span><?php _e( 'Configure', 'wishlist-member' ); ?></span>
		</button>
	</div>
</div>


<div
	data-process="modal"
	id="learndash-group-default-template"
	data-id="learndash-group-default"
	data-label="learndash-group-default"
	data-title="Default Group Actions"
	data-show-default-footer="1"
	data-classes="modal-lg modal-learndash-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#learndash-group-add-default">When Added to a Group</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#learndash-group-remove-default">When Removed from a Group</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php $c_actions = ['add','remove']; ?>
			<?php foreach ( $c_actions as $action ) : ?>
				<div class="row tab-pane <?php echo $action == 'add' ? 'active in' : ''; ?>" id="learndash-group-<?php echo $action; ?>-default">
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Add to Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select learndash-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="learndash_settings[group][default][<?php echo $action; ?>][add_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Cancel from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select learndash-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="learndash_settings[group][default][<?php echo $action; ?>][cancel_level][]"></select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?php _e( 'Remove from Level', 'wishlist-member' ); ?></label>
							<select class="wlm-select learndash-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="learndash_settings[group][default][<?php echo $action; ?>][remove_level][]"></select>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>