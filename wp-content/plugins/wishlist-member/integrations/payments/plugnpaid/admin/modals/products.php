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
		<div class="row plugnpaid-products-<?php echo $level->id; ?>">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'plug&paid Plan', 'wishlist-member' ); ?>',
					type : 'select',
					name : 'plugnpaid_products[<?php echo $level->id; ?>]',
					style : 'width: 100%',
					'data-placeholder' : '<?php _e( 'Choose a plug&paid Product', 'wishlist-member' ); ?>',
					'data-allow-clear' : 'true',
					options : WLM3ThirdPartyIntegration.plugnpaid.products_options,
					column : 'col-12',
				}
			</template>
		</div>
	</div>
</div>
<?php
	endforeach;
endforeach;
?>