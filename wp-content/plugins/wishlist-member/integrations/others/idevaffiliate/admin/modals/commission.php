<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="idevaffiliate-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="idevaffiliate-lists-modal-<?php echo $level->id; ?>"
	data-label="idevaffiliate-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Initial Price', 'wishlist-member' ); ?>',
					type : 'text',
					class : '-numeric',
					name : 'WLMiDev[wlm_idevamountfirst][<?php echo $level->id; ?>]',
					placeholder : '<?php _e( '0.00', 'wishlist-member' ); ?>',
					'data-mirror-value' : '#idev-values-initial-<?php echo $level->id; ?>',
					column : 'col-6',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Recurring Price', 'wishlist-member' ); ?>',
					type : 'text',
					class : '-numeric',
					name : 'WLMiDev[wlm_idevamountrecur][<?php echo $level->id; ?>]',
					placeholder : '<?php _e( '0.00', 'wishlist-member' ); ?>',
					'data-mirror-value' : '#idev-values-recur-<?php echo $level->id; ?>',
					column : 'col-6',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Fixed Commission', 'wishlist-member' ); ?>',
					type : 'checkbox',
					name : 'WLMiDev[wlm_idevspecificamount][<?php echo $level->id; ?>]',
					value : 'yes',
					uncheck_value : '',
					column : 'col-12',
					'data-level' : '<?php echo $level->id; ?>',
					'data-mirror-value' : '#idev-values-fixed-<?php echo $level->id; ?>',
					class : '-commission-type',
				}
			</template>
		</div>
		<div class="row -commission-fixed-<?php echo $level->id; ?>">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Initial Commission', 'wishlist-member' ); ?>',
					type : 'text',
					class : '-numeric',
					name : 'WLMiDev[wlm_idevamountpayment][<?php echo $level->id; ?>]',
					placeholder : '<?php _e( '0.00', 'wishlist-member' ); ?>',
					'data-mirror-value' : '#idev-values-initialc-<?php echo $level->id; ?>',
					column : 'col-6',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Recurring Commission', 'wishlist-member' ); ?>',
					type : 'text',
					class : '-numeric',
					name : 'WLMiDev[wlm_idevamountpaymentrecur][<?php echo $level->id; ?>]',
					placeholder : '<?php _e( '0.00', 'wishlist-member' ); ?>',
					'data-mirror-value' : '#idev-values-recurc-<?php echo $level->id; ?>',
					column : 'col-6',
				}
			</template>
		</div>
		<div class="row -commission-idev-<?php echo $level->id; ?>">
			<div class="col-6">
				<p><em>Payout levels set in iDevAffiliate</em></p>
			</div>
		</div>
	</div>
</div>
<?php
endforeach;
?>
