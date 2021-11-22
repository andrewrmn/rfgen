<p>
	<?php _e( 'Create a product for each Membership Level or Pay Per Post using the assigned SKUs listed below.', 'wishlist-member' ); ?>
</p>
<div id="1shoppingcart-products-table"></div>
<script type="text/template" id="1shoppingcart-products-template">
	<h3 style="margin-bottom: 5px"><%= data.label %></h3>
	<div class="table-wrapper -no-shadow">
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th width="30%">SKU <?php $this->tooltip(__('Each Membership Level has a unique SKU that must be used for each product that needs to be created in the online payment option.', 'wishlist-member')); ?></th>
				</tr>
			</thead>
			<tbody>
				<% _.each(data.levels, function(level) { %>
				<tr>
					<td><%= level.name %></td>
					<td><%= level.id %></td>
				</tr>
				<% }); %>
			</tbody>
		</table>
	</div>
</script>

<script type="text/javascript">
	$('#1shoppingcart-products-table').empty();
	$.each(all_levels, function(k, v) {
		var data = {
			label : post_types[k].labels.name,
			levels : v
		}
		var tmpl = _.template($('script#1shoppingcart-products-template').html(), {variable: 'data'});
		var html = tmpl(data);
		$('#1shoppingcart-products-table').append(html);
	});
</script>