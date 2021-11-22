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
					label : '<?php _e( 'Recurly Plan', 'wishlist-member' ); ?>',
					type : 'select',
					name : 'recurlyconnections[<?php echo $level->id; ?>][plan]',
					style : 'width: 100%',
					'data-mirror-value' : '#recurly-product-plan-<?php echo $level->id; ?>',
					'data-placeholder' : '<?php _e( 'Choose a Recurly Plan', 'wishlist-member' ); ?>',
					options : WLM3ThirdPartyIntegration.recurly.plan_options,
					column : 'col-12',
					'data-allow-clear' : 'true',
					class : 'recurlyplans',
				}
			</template>
		</div>
	</div>
</div>
<?php
	endforeach;
endforeach;
?>