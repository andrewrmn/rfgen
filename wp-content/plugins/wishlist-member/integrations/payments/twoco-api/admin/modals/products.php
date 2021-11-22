<?php
foreach($all_levels AS $levels):
	foreach($levels AS $level) :
		$level = (object) $level;
?>
<div
	data-process="modal"
	id="products-<?php echo $config['id']; ?>-<?php echo $level->id; ?>-template" 
	data-id="products-<?php echo $config['id']; ?>-<?php echo $level->id; ?>"
	data-label="products-<?php echo $config['id']; ?>-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Product for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Amount', 'wishlist-member' ); ?>',
					name : 'twocheckoutapisettings[connections][<?php echo $level->id; ?>][rebill_init_amount]',
					'data-mirror-value' : '#twocoapi-products-amount-<?php echo $level->id; ?>',
					type : 'text',
					column : 'col-6',
					class : '-amount',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Recurring', 'wishlist-member' ); ?>',
					name : 'twocheckoutapisettings[connections][<?php echo $level->id; ?>][subscription]',
					'data-mirror-value' : '#twocoapi-products-recur-<?php echo $level->id; ?>',
					type : 'checkbox',
					column : 'col-12',
					value : '1',
					uncheck_value : '',
					'data-target' : '.twoco-api-recurring-<?php echo $level->id; ?>',
					class : 'twoco-api-recurring-toggle',
				}
			</template>
		</div>
		<div class="row twoco-api-recurring-<?php echo $level->id; ?>">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Recurring Amount', 'wishlist-member' ); ?>',
					name : 'twocheckoutapisettings[connections][<?php echo $level->id; ?>][rebill_recur_amount]',
					'data-mirror-value' : '#twocoapi-products-recuramount-<?php echo $level->id; ?>',
					type : 'text',
					column : 'col-6',
					class : '-amount',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( '<span style="white-space: nowrap">Interval</span>', 'wishlist-member' ); ?>',
					type : 'select',
					name : 'twocheckoutapisettings[connections][<?php echo $level->id; ?>][rebill_interval]',
					'data-mirror-value' : '#twocoapi-products-interval-<?php echo $level->id; ?>',
					options : WLM3ThirdPartyIntegration['twoco-api'].rebill_interval,
					style : 'width: 100%',
					column : 'col-2 pr-0',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( '&nbsp;', 'wishlist-member' ); ?>',
					type : 'select',
					name : 'twocheckoutapisettings[connections][<?php echo $level->id; ?>][rebill_interval_type]',
					'data-mirror-value' : '#twocoapi-products-intervaltype-<?php echo $level->id; ?>',
					options : WLM3ThirdPartyIntegration['twoco-api'].rebill_interval_type,
					style : 'width: 100%',
					column : 'col-4 pl-0',
				}
			</template>
		</div>
	</div>
</div>
<?php
	endforeach;
endforeach;
?>