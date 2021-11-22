<div class="tab-pane" id="coderedemption-campaign-modal-settings">
  <div class="row">
    <template class="wlm3-form-group">
      {
        label : '<?php _e( 'Name', 'wishlist-member' ); ?>',
        type : 'text',
        name : 'name',
        column : 'col',
        placeholder: '<?php _e( 'Enter campaign name', 'wishlist-member' ) ?>'
      }
    </template>
    <template class="wlm3-form-group">
      {
        label : '<?php _e( 'Active', 'wishlist-member' ); ?>',
        type : 'toggle-switch',
        name : 'status',
        column : 'col-auto mt-4 pt-1',
        checked: true,
        value : 1,
        uncheck_value: 0,
      }
    </template>
    <template class="wlm3-form-group">
      {
        label : '<?php _e( 'Description', 'wishlist-member' ); ?>',
        type : 'textarea',
        name : 'description',
        column : 'col-12',
      }
    </template>
  </div>
  <div class="row edit-only">
    <div class="col">
      <p>
        <span class="coderedemption-code-total"></span> <a onclick="$('.generate-code').click()" href="#"><?php _e( 'Add Codes', 'wishlist-member' ); ?></a><br>
        <span class="coderedemption-code-stats"></span>
      </p>
    </div>
  </div>
</div>
