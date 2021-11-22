<div role="tabpanel" class="tab-pane active" id="ppps_access">
	<div class="content-wrapper">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Enable Pay Per Post for this content', 'wishlist-member' ); ?>',
					name  : 'is_ppp',
					value : '1',
					uncheck_value : '',
					checked_value : <?php echo $data['is_ppp']; ?>,
					type  : 'toggle-adjacent',
					column : 'col-md-12',
					class : 'ppp-toggle'
				}
			</template>
			<div class="col-md-12" <?php if(!$data['is_ppp']) echo 'style="display: none;"'; ?>>
				<div class="row">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Allow Free Registration for this content', 'wishlist-member' ); ?>',
							name  : 'free_ppp',
							value : '1',
							uncheck_value : '',
							type  : 'toggle-adjacent',
							column : 'col-md-12',
							class : 'ppp-toggle'
						}
					</template>
					<div class="col-md-6" <?php if(!$data['free_ppp']) echo 'style="display: none;"'; ?>>
						<template class="wlm3-form-group">
							{
								label : '<?php _e('Free Registration URL', 'wishlist-member'); ?>',
								value : '<?php echo WLMREGISTERURL . '/payperpost/' . $post->ID; ?>',
								class : 'copyable'
							}
						</template>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="panel-footer -content-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<?php echo $tab_footer; ?>
				</div>
			</div>
		</div>
	</div>
</div>
