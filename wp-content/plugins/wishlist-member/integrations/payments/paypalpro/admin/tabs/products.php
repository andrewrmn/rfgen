<div class="table-wrapper -no-shadow">
	<table class="table table-striped" id="paypalpro-products" style="border-top: none">
		<tbody></tbody>
		<thead>
			<tr>
				<th width="25%"><?php _e( 'Product Name', 'wishlist-member' ); ?></th>
				<th><?php _e( 'Access', 'wishlist-member' ); ?></th>
				<th width="80px"></th>
				<th width="80px" class="text-center"><?php _e( 'Subscription', 'wishlist-member' ); ?></th>
				<th width="80px" class="text-center"><?php _e( 'Currency', 'wishlist-member' ); ?></th>
				<th width="80px" class="text-center"><?php _e( 'Amount', 'wishlist-member' ); ?></th>
				<th width="80px"></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td class="pt-3" colspan="7">
					<p><?php _e( 'No products found.', 'wishlist-member' ); ?></p>
				</td>
			</tr>
			<tr>
				<td class="pt-3 text-center" colspan="7">
					<a href="#" class="btn -success -add-btn -condensed">
						<i class="wlm-icons">add</i>
						<span><?php _e( 'Add New Product', 'wishlist-member' ); ?></span>
					</a>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<div class="notice notice-warning"><p><?php _e( 'The purchase button for the membership level needs to be inserted into a WordPress page/post by using the blue WishList Member code inserter located on the page/post editor.', 'wishlist-member' ); ?></p></div>

<div id="paypalpro-products-edit"></div>
<script type="text/template" id="paypalpro-products-template">
	<% _.each(data, function(product, id) { %>
	<% if(!('name' in product)) return; %>
	<% if('new_product' in product) return; %>
	<tr class="button-hover" data-id="<%= id %>">
		<td><%= product.name %></td>
		<td><%= all_levels_flat[product.sku] ? all_levels_flat[product.sku].name : '' %></td>
		<td class="text-right">
			<a href="" class="wlm-popover clipboard tight btn wlm-icons md-24 -icon-only -link-btn" title="Copy Product Payment Link" alt="Click for Product Payment Link" data-text="<%= WLM3VARS.blogurl %>?pppro=<%= id %>"><span>link</span></a>
		</td>
		<td class="text-center"><%= product.recurring == 1 ? 'YES' : 'NO' %></td>
		<td class="text-center"><%= product.currency %></td>
		<td class="text-center"><%= Number(product.recurring == 1 ? product.recur_amount : product.amount).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) %></td>
		<td class="text-right">
			<div class="btn-group-action">
				<a href="#" class="btn wlm-icons md-24 -icon-only -edit-btn"><span>edit</span></a>
				<a href="#" class="btn wlm-icons md-24 -icon-only -del-btn"><span>delete</span></a></div>
		</td>
	</tr>
	<% }) %>
</script>
<script type="text/template" id="paypalpro-products-edit-template">
<% _.each(data, function(product, id) { %>
<div
	id="paypalpro_edit_product_<%= id %>-template" 
	data-id="paypalpro_edit_product_<%= id %>"
	data-label="paypalpro_edit_product_<%= id %>"
	data-title="Editing <%= product.name %>"
	data-show-default-footer="1"
	data-classes="modal-lg paypalpro-edit-product"
	data-process="modal"
	style="display:none">
	<div class="body">
		<input type="hidden" name="paypalproproducts[<%= id %>][id]" value="<%= id %>">
		<div class="paypalpro-product-form">
			<div class="row">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Product Name', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][name]',
						column : 'col-6',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Access', 'wishlist-member' ); ?>',
						type : 'select',
						style : 'width: 100%',
						options : paypal_common.levels_select_group,
						'data-placeholder' : '<?php _e( 'Select a Level', 'wishlist-member' ); ?>',
						grouped : true,
						name : 'paypalproproducts[<%= id %>][sku]',
						column : 'col-6',
					}
				</template>
			</div>
			<div class="row">
				<div class="col-3 pt-1" style="white-space: nowrap;">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'One-Time Payment', 'wishlist-member' ); ?>',
							name : 'paypalproproducts[<%= id %>][recurring]',
							value : 0,
							type : 'radio',
							class : '-paypal-recurring-toggle',
						}
					</template>
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Subscription', 'wishlist-member' ); ?>',
							name : 'paypalproproducts[<%= id %>][recurring]',
							value : 1,
							type : 'radio',
							class : '-paypal-recurring-toggle',
						}
					</template>
				</div>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Amount', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][amount]',
						column : 'col-3 -paypalpro-onetime',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Amount', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][recur_amount]',
						column : 'col-3 -paypalpro-recurring',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Currency', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][currency]',
						type : 'select',
						style : 'width: 100%',
						options : paypal_common.pp_currencies,
						column : 'col-3',
					}
				</template>
			</div>
			<div class="row">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '<span style="white-space: nowrap;">Billing Cycle</span>', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][recur_billing_frequency]',
						column : 'offset-3 col-1 no-padding-right -paypalpro-recurring',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '&nbsp;', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][recur_billing_period]',
						type : 'select',
						style : 'width: 100%',
						options : paypal_common.pp_billing_cycle,
						column : 'col-2 no-padding-left -paypalpro-recurring',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Stop After', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][recur_billing_cycles]',
						type : 'select',
						style : 'width: 100%',
						options : paypal_common.pp_stop_after,
						column : 'col-3 -paypalpro-recurring',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '<span style="white-space:nowrap">Max Failed Attempts</span>', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][max_failed_payments]',
						options : paypal_common.pp_billing_cycle,
						column : 'col-3 -paypalpro-recurring',
					}
				</template>
			</div>
			<div class="row">
				<div class="col-3 pt-1 -paypalpro-recurring">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Trial Period', 'wishlist-member' ); ?>',
							name : 'paypalproproducts[<%= id %>][trial]',
							value : 1,
							uncheck_value : 0,
							type : 'checkbox',
							class : '-paypal-trial1-toggle',
						}
					</template>
				</div>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Trial Amount', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][trial_amount]',
						column : 'col-3 -paypalpro-trial',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '<span style="white-space: nowrap;">Trial Duration</span>', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][trial_recur_billing_frequency]',
						column : 'col-1 no-padding-right -paypalpro-trial',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '&nbsp;', 'wishlist-member' ); ?>',
						name : 'paypalproproducts[<%= id %>][trial_recur_billing_period]',
						type : 'select',
						style : 'width: 100%',
						options : paypal_common.pp_billing_cycle,
						column : 'col-2 no-padding-left -paypalpro-trial',
					}
				</template>
			</div>
		</div>
	</div>
</div>
<% }) %>
</script>