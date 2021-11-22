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
					name : 'ewaysettings[connections][<?php echo $level->id; ?>][rebill_init_amount]',
					'data-mirror-value' : '#eway-products-amount-<?php echo $level->id; ?>',
					type : 'text',
					column : 'col-6',
					class : '-amount',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Recurring', 'wishlist-member' ); ?>',
					name : 'ewaysettings[connections][<?php echo $level->id; ?>][subscription]',
					'data-mirror-value' : '#eway-products-recur-<?php echo $level->id; ?>',
					type : 'checkbox',
					column : 'col-12',
					value : '1',
					uncheck_value : '',
					'data-target' : '.eway-recurring-<?php echo $level->id; ?>',
					class : 'eway-recurring-toggle',
				}
			</template>
		</div>
		<div class="row eway-recurring-<?php echo $level->id; ?>">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Recurring Amount', 'wishlist-member' ); ?>',
					name : 'ewaysettings[connections][<?php echo $level->id; ?>][rebill_recur_amount]',
					'data-mirror-value' : '#eway-products-recuramount-<?php echo $level->id; ?>',
					type : 'text',
					column : 'col-6',
					class : '-amount',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( '<span style="white-space: nowrap">Interval</span>', 'wishlist-member' ); ?>',
					type : 'select',
					name : 'ewaysettings[connections][<?php echo $level->id; ?>][rebill_interval]',
					'data-mirror-value' : '#eway-products-interval-<?php echo $level->id; ?>',
					options : WLM3ThirdPartyIntegration['eway'].rebill_interval,
					style : 'width: 100%',
					column : 'col-2 pr-0',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( '&nbsp;', 'wishlist-member' ); ?>',
					type : 'select',
					name : 'ewaysettings[connections][<?php echo $level->id; ?>][rebill_interval_type]',
					'data-mirror-value' : '#eway-products-intervaltype-<?php echo $level->id; ?>',
					options : WLM3ThirdPartyIntegration['eway'].rebill_interval_type,
					style : 'width: 100%',
					column : 'col-4 pl-0',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Last Rebill Date', 'wishlist-member' ); ?>',
					name : 'ewaysettings[connections][<?php echo $level->id; ?>][rebill_end_date]',
					'data-mirror-value' : '#eway-products-rebillend-<?php echo $level->id; ?>',
					type : 'text',
					column : 'col-6',
					class : 'wlm-datetimepicker',
				}
			</template>
		</div>
	</div>
</div>
<?php
	endforeach;
endforeach;
?>