<p><a href="#sendstudio-list-id" class="hide-show"><?php _e( 'How to get the List ID', 'wishlist-member' ); ?></a></p>
<ol class="d-none" id="sendstudio-list-id" style="list-style: decimal">
	<li><p><?php _e( 'Log in to the Interspire Email Marketer account', 'wishlist-member' ); ?></p></li>
	<li><p><?php _e( 'Navigate to the following section:', 'wishlist-member' ); ?><br>"Contact Lists Tab" > "View Contact Lists" and edit the contact list</p></li>
	<li><p>Copy the value of the "ID" parameter from the browser URL Example:<br><?php _e( 'http://www.yourdomain.com/path/to/IEM/installationadmin/index.php?Page=Lists&Action=Edit&id=', 'wishlist-member' ); ?><mark>1</mark><br>(The number 1 is the ID in this example)</p></li>
</ol>

<div id="sendstudio-lists-table" class="table-wrapper -no-shadow"></div>
<script type="text/template" id="sendstudio-lists-template">
	<table class="table table-striped">
		<colgroup>
			<col>
			<col width="1%">
		</colgroup>
		<thead>
			<tr>
				<th>Membership Level</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<% _.each(data.levels, function(level) { %>
			<tr class="button-hover">
				<td><a href="#" data-toggle="modal" data-target="#interspire-lists-modal-<%- level.id %>"><%= level.name %></a></td>
				<td class="text-right" style="vertical-align: middle">
					<div class="btn-group-action">
						<a href="#" data-toggle="modal" data-target="#interspire-lists-modal-<%- level.id %>" class="btn -tags-btn" title="Edit"><i class="wlm-icons md-24">edit</i></a>
					</div>
				</td>
			</tr>
			<% }); %>
		</tbody>
	</table>
</script>

<script type="text/javascript">
	$('#sendstudio-lists-table').empty();
	$.each(all_levels, function(k, v) {
		var data = {
			label : post_types[k].labels.name,
			levels : v
		}
		var tmpl = _.template($('script#sendstudio-lists-template').html(), {variable: 'data'});
		var html = tmpl(data);
		$('#sendstudio-lists-table').append(html);
		return false;
	});
</script>
