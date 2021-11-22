<div
	id="sequential-upgrade-modal" 
	data-id="sequential-upgrade"
	data-label="sequential-upgrade"
	data-title="Edit Sequential Upgrade for <span class='level-name'></span>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="save_sequential" />
		<input type="hidden" name="level_id" value="" />
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Method', 'wishlist-member' ); ?>',
					type : 'select',
					name : 'upgradeMethod',
					id : 'seq-method',
					options : [
						{ value : 'inactive', text : '<?php _e( 'None', 'wishlist-member' ); ?>' },
						{ value : 'ADD', text : '<?php _e( 'Add', 'wishlist-member' ); ?>' },
						{ value : 'MOVE', text : '<?php _e( 'Move', 'wishlist-member' ); ?>' },
						{ value : 'REMOVE', text : '<?php _e( 'Remove', 'wishlist-member' ); ?>' },
					],
					column: 'col-4',
					style: 'width: 100%',
					group_class : 'mb-3',
				}
			</template>
			<?php
				$options = array();
			foreach ( $wpm_levels as $key => $value ) {
				$options[] = array(
					'value' => $key,
					'text'  => $value['name'],
				);
			}
				$options = json_encode( $options );
			?>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'To', 'wishlist-member' ); ?>',
					name : 'upgradeTo',
					type : 'select',
					options : <?php echo $options; ?>,
					column: 'col-8 -show-add -show-move',
					style: 'width: 100%',
					group_class : 'mb-3',
					id : 'seq_upgrade_to',
					'data-placeholder' : '<?php _e( 'Choose a Membership Level', 'wishlist-member' ); ?>',
				}
			</template>
			<div class="col-8 -show-remove">
				<div class="form-group mb-3">
					<label><?php _e( 'From', 'wishlist-member' ); ?></label>
					<span class="form-control disabled level-name"></span>
				</div>
			</div>
		</div>
		<div class="row -show-add -show-move -show-remove">			
			<div class="col-4 pr-0">
				<div class="switch-toggle switch-toggle-wlm -compressed" style="margin-top: 3px;">
					<input skip-save="1" class="toggle-radio toggle-radio-sched" name="sched_toggle" id="after" type="radio" value="after">
					<label for="after"><?php _e( 'After', 'wishlist-member' ); ?></label>
					<input skip-save="1" class="toggle-radio toggle-radio-sched" name="sched_toggle" id="on" type="radio" value="ondate">
					<label for="on"><?php _e( 'On', 'wishlist-member' ); ?></label>
					<a href="" class="btn btn-primary"></a>
				</div>
				<input type="hidden" id="upgrade-schedule" name="upgradeSchedule" value="">
			</div>
			<template class="wlm3-form-group">
				{
					type : 'number',
					name : 'upgradeAfter',
					column: 'col-3 -sched-after -sched-options'
				}
			</template>
			<template class="wlm3-form-group">
				{
					type : 'select',
					name : 'upgradeAfterPeriod',
					options : [
						{ value : '', text : '<?php _e( 'Day(s)', 'wishlist-member' ); ?>' },
						{ value : 'weeks', text : '<?php _e( 'Week(s)', 'wishlist-member' ); ?>' },
						{ value : 'months', text : '<?php _e( 'Month(s)', 'wishlist-member' ); ?>' },
						{ value : 'years', text : '<?php _e( 'Year(s)', 'wishlist-member' ); ?>' },
					],
					column: 'col-5 pl-md-0 -sched-after -sched-options',
					style: 'width: 100%',
				}
			</template>
			<template class=wlm3-form-group>
				{
					type : 'text',
					name : 'upgradeOnDate',
					class : 'wlm-datetimepicker',
					column : 'col-8 -sched-ondate -sched-options d-none'
				}
			</template>
		</div>
		<div class="row -show-add -show-move">			
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Email Notification', 'wishlist-member' ) ?>',
					type : 'select',
					name : 'upgradeEmailNotification',
					column : 'col-12',
					style : 'width: 100%',
					options : [
						{ value : '1', text : '<?php _e( 'Use Level Notification Settings', 'wishlist-member' ); ?>' },
						{ value : '2', text : '<?php _e( 'Send Email Notification', 'wishlist-member' ); ?>' },
						{ value : '', text : '<?php _e( 'Do NOT Send Email Notification', 'wishlist-member' ); ?>' },
					],
				}
			</template>
		</div>
	</div>
</div>

<style type="text/css">
	#select2-seq_upgrade_to-results [aria-disabled="true"] {
		display: none;
	}
</style>