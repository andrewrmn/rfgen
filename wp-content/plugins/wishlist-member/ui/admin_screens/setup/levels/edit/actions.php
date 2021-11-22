<div role="tabpanel" class="tab-pane" id="" data-id="levels_actions">
			<p class="float-left"><em>WordPress Time:
				<?php printf('%s %s %s', date( get_option( 'date_format' ) ), date( get_option( 'time_format' ) ), $this->get_wp_tzstring( true ) ); ?>
			</em></p>
	<p class="text-right">
		<button data-toggle="modal" data-target="#level-actions" href="" class="btn -success -condensed">
			<i class="wlm-icons">add</i>
			<span><?php _e( 'Add Action', 'wishlist-member' ); ?></span>
		</button>
	</p>
	<div class="content-wrapper">
			<div class="table-wrapper table-responsive mb-0">
				<table class="table table-striped table-condensed" id="table-level-actions">
					<colgroup>
						<col>
						<col width="20%">
						<col width="100">
					</colgroup>
					<thead>
						<tr>
							<th class="action-table-title"><?php _e('', 'wishlist-member'); ?></th>
							<th><?php _e('Schedule', 'wishlist-member'); ?></th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
	</div>
</div>
