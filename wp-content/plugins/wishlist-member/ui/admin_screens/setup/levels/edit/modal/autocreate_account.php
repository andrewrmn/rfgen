<div
	id="auto-create-accounts-for-integrations-modal"
	data-id="auto-create-accounts-for-integrations"
	data-label="auto-create-accounts-for-integrations"
	data-title="Auto-Create Accounts for Integrations"
	data-show-default-footer=""
	data-classes="modal-md"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label      : '<?php _e( 'Username Format', 'wishlist-member' ); ?>',
					type       : 'text',
					name       : 'autocreate_account_username',
					column     : 'col-12',
					help_block : 'Shortcodes: {name}, {fname}, {lname}, {email}, {rand_ltr 3}, {rand_num 3}, {rand_mix 3}'
				}
			</template>
		</div>
		<div class="row">
			<div class="col-auto pr-0">
				<div class="form-group">
					<template class="wlm3-form-group">
						{
							label         : '<?php _e( 'Enable Delay', 'wishlist-member' ); ?>',
							name          : 'autocreate_account_enable_delay',
							value         : '1',
							uncheck_value : '0',
							type          : 'toggle-adjacent',
						}
					</template>
				</div>
			</div>
			<div class="col">
				<div class="form-inline pull-left">
					<template class="wlm3-form-group">
						{
							type  : 'text',
							name  : 'autocreate_account_delay',
							style : 'width: 60px;',
							class : 'text-center',
						}
					</template>
					<template class="wlm3-form-group">
						{
							type    : 'select',
							name    : 'autocreate_account_delay_type',
							class   : 'text-center',
							style   : 'width: 100px;',
							options : [
								{text : 'Minute(s)', value : 1},
								{text : 'Hour(s)', value : 60}
							],
						}
					</template>
				</div>
			</div>
		</div>
	</div>
	<div class="footer">
		<?php echo $modal_footer; ?>
	</div>
</div>
