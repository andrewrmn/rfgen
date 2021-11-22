<p>
	<?php _e( 'Membership Levels can be assigned to your Forms by selecting a Form Name in the corresponding area below.', 'wishlist-member' ); ?>
</p>
<div id="convertkit-lists-table" class="table-wrapper"></div>
<script type="text/template" id="convertkit-lists-template">
	<table class="table table-striped">
		<colgroup>
			<col>
			<col width="1%">
		</colgroup>
		<thead>
			<tr>
				<th>Name</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<% _.each(data.levels, function(level) { %>
			<tr>
				<td><a href="#" data-toggle="modal" data-target="#convertkit-lists-modal-<%- level.id %>"><%= level.name %></a></td>
				<td class="text-right" style="vertical-align: middle">
					<div class="btn-group-action">
						<a href="#" data-toggle="modal" data-target="#convertkit-lists-modal-<%- level.id %>" class="btn -tags-btn" title="Edit"><i class="wlm-icons md-24">edit</i></a>
					</div>
				</td>
			</tr>
			<% }); %>
		</tbody>
	</table>
</script>

<script type="text/javascript">
	$('#convertkit-lists-table').empty();
	$.each(all_levels, function(k, v) {
		var data = {
			label : post_types[k].labels.name,
			levels : v
		}
		var tmpl = _.template($('script#convertkit-lists-template').html(), {variable: 'data'});
		var html = tmpl(data);
		$('#convertkit-lists-table').append(html);
		return false;
	});
</script>
