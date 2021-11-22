<?php
foreach ( $all_levels as $levels ) :
	foreach ( $levels as $level ) :
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
		<input type="hidden" name="stripeconnections[<?php echo $level->id; ?>][sku]" value="<?php echo $level->id; ?>">
		<input type="hidden" name="stripeconnections[<?php echo $level->id; ?>][membershiplevel]" value="<?php echo $level->name; ?>">
		<div class="row mb-3">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Stripe Plan', 'wishlist-member' ); ?>',
					type : 'radio',
					name : 'stripeconnections[<?php echo $level->id; ?>][subscription]',
					value : 1,
					column : 'col-12',
					checked : 'checked',
					class : 'stripe-plan-toggle',
					'data-target' : '.stripe-plan-<?php echo $level->id; ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'One Time Payment (Custom Pricing)', 'wishlist-member' ); ?>',
					type : 'radio',
					name : 'stripeconnections[<?php echo $level->id; ?>][subscription]',
					value : 0,
					column : 'col-12',
					class : 'stripe-plan-toggle',
					'data-target' : '.stripe-onetime-<?php echo $level->id; ?>',
				}
			</template>
		</div>
		<div style="display:none;" class="row stripe-onetime-<?php echo $level->id; ?>">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Amount', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'stripeconnections[<?php echo $level->id; ?>][amount]',
					class : '-amount',
					placeholder : '<?php _e( 'Enter Amount', 'wishlist-member' ); ?>',
					column : 'col-12',
				}
			</template>
		</div>
		<div class="row stripe-plan-<?php echo $level->id; ?>">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Select Stripe Plan(s)', 'wishlist-member' ); ?>',
					type : 'select',
					multiple : 'multiple',
					class : 'stripe-products',
					name : 'stripeconnections[<?php echo $level->id; ?>][plan]',
					style : 'width: 100%',
					'data-placeholder' : '<?php _e( 'Choose a Stripe Plan', 'wishlist-member' ); ?>',
					'data-allow-clear' : 'true',
					options : WLM3ThirdPartyIntegration.stripe.plan_options,
					column : 'col-12',
				}
			</template>
			
			<!-- @since 3.6 Support for multiple plans in the same product. -->
			<input type="hidden" class="stripe-plan" name="stripeconnections[<?php echo $level->id; ?>][plan]">
			<input type="hidden" class="stripe-plans" name="stripeconnections[<?php echo $level->id; ?>][plans]">

			<template class="wlm3-form-group">
				{
					label : "<?php _e( 'Cancel the user\'s Stripe Subscription when the membership level is cancelled in WishList Member', 'wishlist-member' ); ?>",
					type : 'checkbox',
					name : 'stripeconnections[<?php echo $level->id; ?>][cancel_subs_if_cancelled_in_wlm]',
					value : 'yes',
					uncheck_value : '',
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
