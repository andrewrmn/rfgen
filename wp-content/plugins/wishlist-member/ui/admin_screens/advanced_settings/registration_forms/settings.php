<div class="content-wrapper">
	<form action="">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Registration Form Style', 'wishlist-member' ); ?>',
					column: 'col-auto',
					name: 'FormVersion',
					options : [ {value : 'themestyled', text : '<?php _e( 'Theme Styled', 'wishlist-member' ); ?>'}, {value : 'improved', text : '<?php _e( 'WishList Member Styled', 'wishlist-member' ); ?>'}, {value : '', text : '<?php _e( 'Legacy', 'wishlist-member' ); ?>'} ],
					value: <?php echo json_encode( $this->GetOption('FormVersion') ); ?>,
					'data-initial': <?php echo json_encode( $this->GetOption('FormVersion') ); ?>,
					type: 'select',
					style : 'width: 200px;',
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
		<div class="row">
			<div class="col-md-3">
				<label><?php _e( 'Registration Session Timeout', 'wishlist-member' ); ?></label>
			</div>
		</div>
		<div class="row">
			<template class="wlm3-form-group">
				{
					addon_right : 'Seconds',
					column: 'col-auto',
					name: 'reg_cookie_timeout',
					tooltip: '<?php _e( 'This sets the length of time before the registration page session times out.', 'wishlist-member' ); ?>',
					group_class: 'reg-cookie-timeout',
					value: '<?php echo $initial = ((int) $this->GetOption('reg_cookie_timeout')) ?: 600; ?>',
					"data-initial": '<?php echo $initial; ?>',
					"data-default": '600',
					type: 'number',
					style: 'width: 5em;'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</form>
	<div class="row">
		<?php $option_val = $this->GetOption('enable_short_registration_links') ?>
		<div class="col-md-12">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Enable short Incomplete Registration links', 'wishlist-member' ); ?>',
					name  : 'enable_short_registration_links',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip : '<?php _e( 'If this is enabled then continue registration links are automatically shortened.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('redirect_existing_member') ?>
		<div class="col-md-12">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Redirect to Existing Member Registration', 'wishlist-member' ); ?>',
					name  : 'redirect_existing_member',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip : '<?php _e( 'Automatically redirect customer to existing member registration form if payment email is already in the database.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>	
</div>