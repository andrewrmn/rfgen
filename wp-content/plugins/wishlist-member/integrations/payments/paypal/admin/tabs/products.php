<div class="table-wrapper -no-shadow">
	<table class="table table-striped" id="paypalps-products" style="border-top: none">
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

<div id="paypalps-products-edit"></div>
<script type="text/template" id="paypalps-products-template">
	<% _.each(data, function(product, id) { %>
	<% if(!('name' in product)) return; %>
	<% if('new_product' in product) return; %>
	<tr class="button-hover" data-id="<%= id %>">
		<td><%= product.name %></td>
		<td><%= all_levels_flat[product.sku] ? all_levels_flat[product.sku].name : '' %></td>
		<td class="text-right">
			<a href="" class="wlm-popover clipboard tight btn wlm-icons md-24 -icon-only -link-btn" title="Copy Product Payment Link" alt="Click for Product Payment Link" data-text="<%= WLM3ThirdPartyIntegration.paypal.ppthankyou_url %>?pid=<%= id %>"><span>link</span></a>
			<a href="" class="wlm-popover clipboard paypal-copy-form tight btn wlm-icons md-24 -icon-only -code-btn" title="Copy Product Payment Form" alt="Click for Product Payment Form" data-id="<%= id %>"><span>code</span></a>
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
<script type="text/template" id="paypalps-products-edit-template">
<% _.each(data, function(product, id) { %>
<div
	id="paypalps_edit_product_<%= id %>-template" 
	data-id="paypalps_edit_product_<%= id %>"
	data-label="paypalps_edit_product_<%= id %>"
	data-title="Editing <%= product.name %>"
	data-show-default-footer="1"
	data-classes="modal-lg paypalps-edit-product"
	data-process="modal"
	style="display:none">
	<div class="body">
		<input type="hidden" name="paypalpsproducts[<%= id %>][id]" value="<%= id %>">
		<div class="paypalps-product-form">
			<div class="row">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Product Name', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][name]',
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
						name : 'paypalpsproducts[<%= id %>][sku]',
						column : 'col-6',
					}
				</template>
			</div>
			<div class="row">
				<div class="col-3 pt-1" style="white-space: nowrap;">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'One-Time Payment', 'wishlist-member' ); ?>',
							name : 'paypalpsproducts[<%= id %>][recurring]',
							value : 0,
							type : 'radio',
							class : '-paypal-recurring-toggle',
						}
					</template>
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Subscription', 'wishlist-member' ); ?>',
							name : 'paypalpsproducts[<%= id %>][recurring]',
							value : 1,
							type : 'radio',
							class : '-paypal-recurring-toggle',
						}
					</template>
				</div>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Amount', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][amount]',
						column : 'col-3 -paypalps-onetime',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Amount', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][recur_amount]',
						column : 'col-3 -paypalps-recurring',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Currency', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][currency]',
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
						name : 'paypalpsproducts[<%= id %>][recur_billing_frequency]',
						column : 'offset-3 col-1 no-padding-right -paypalps-recurring',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '&nbsp;', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][recur_billing_period]',
						type : 'select',
						style : 'width: 100%',
						options : paypal_common.pp_billing_cycle,
						column : 'col-2 no-padding-left -paypalps-recurring',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Stop After', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][recur_billing_cycles]',
						type : 'select',
						style : 'width: 100%',
						options : paypal_common.pp_stop_after,
						column : 'col-3 -paypalps-recurring',
					}
				</template>
			</div>
			<div class="row">
				<div class="col-3 pt-1 -paypalps-recurring">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( '1st Trial Period', 'wishlist-member' ); ?>',
							name : 'paypalpsproducts[<%= id %>][trial]',
							value : 1,
							uncheck_value : 0,
							type : 'checkbox',
							class : '-paypal-trial1-toggle',
						}
					</template>
				</div>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '1st Trial Amount', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][trial_amount]',
						column : 'col-3 -paypalps-trial',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '<span style="white-space: nowrap;">1st Trial Duration</span>', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][trial_recur_billing_frequency]',
						column : 'col-1 no-padding-right -paypalps-trial',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '&nbsp;', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][trial_recur_billing_period]',
						type : 'select',
						style : 'width: 100%',
						options : paypal_common.pp_billing_cycle,
						column : 'col-2 no-padding-left -paypalps-trial',
					}
				</template>
			</div>
			<div class="row">
				<div class="col-3 pt-1 -paypalps-trial">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( '2nd Trial Period', 'wishlist-member' ); ?>',
							name : 'paypalpsproducts[<%= id %>][trial2]',
							value : 1,
							uncheck_value : 0,
							type : 'checkbox',
							class : '-paypal-trial2-toggle',
						}
					</template>
				</div>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '2nd Trial Amount', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][trial2_amount]',
						column : 'col-3 -paypalps-trial2',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '<span style="white-space: nowrap;">2nd Trial Duration</span>', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][trial2_recur_billing_frequency]',
						column : 'col-1 no-padding-right -paypalps-trial2',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( '&nbsp;', 'wishlist-member' ); ?>',
						name : 'paypalpsproducts[<%= id %>][trial2_recur_billing_period]',
						type : 'select',
						style : 'width: 100%',
						options : paypal_common.pp_billing_cycle,
						column : 'col-2 no-padding-left -paypalps-trial2',
					}
				</template>
			</div>
		</div>
	</div>
</div>
<% }) %>
</script>