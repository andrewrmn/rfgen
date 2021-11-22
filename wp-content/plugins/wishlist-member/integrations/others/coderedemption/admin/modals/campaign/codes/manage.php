<div role="tabpanel" class="tab-pane" id="coderedemption-campaign-modal-codes-manage">
  <div class="row">
    <template class="wlm3-form-group">
      {
        type: 'text',
        id: 'coderedemption-code-search',
        column: 'col',
        placeholder: '<?php _e( 'Full / Partial Code', 'wishlist-member' ); ?>',
      }
    </template>
    <template class="wlm3-form-group">
      {
        type: 'select',
        id: 'coderedemption-code-search-status',
        column: 'col-auto px-0',
        style: 'width: 120px',
        options: [
          {value: '', text: '<?php _e( 'All', 'wishlist-member' ); ?>'},
          {value: '0', text: '<?php _e( 'Available', 'wishlist-member' ); ?>'},
          {value: '1', text: '<?php _e( 'Redeemed', 'wishlist-member' ); ?>'},
          {value: '2', text: '<?php _e( 'Cancelled', 'wishlist-member' ); ?>'},
        ]
      }
    </template>
    <div class="col-auto">
      <button type="button" class="btn -default -condensed" id="coderedemption-code-search-button"><?php _e( 'Search', 'wishlist-member' ); ?></button>
    </div>
  </div>
  <div id="coderedemption-code-search-results-wrapper" class="table-wrapper -no-shadow">
    <table id="coderedemption-code-search-results" class="table table-striped table-small table-borderless">
      <colgroup>
        <col>
        <col width="120">
        <col width="80">
      </colgroup>
      <tbody></tbody>
      <thead>
        <tr>
          <th><?php _e( 'Code', 'wishlist-member' ); ?></th>
          <th><?php _e( 'Status', 'wishlist-member' ); ?></th>
          <th></th>
        </tr>
      </thead>
    </table>
  </div>
  <p id="coderedemption-code-search-results-summary"></p>
</div>
