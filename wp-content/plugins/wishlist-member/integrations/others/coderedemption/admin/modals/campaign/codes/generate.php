<div role="tabpanel" class="tab-pane" id="coderedemption-campaign-modal-codes-generate">
  <div class="row">
    <template class="wlm3-form-group">
      {
        type : 'select',
        label: '<?php _e( 'Format', 'wishlist-member' ); ?>',
        id : 'generate-code-format',
        column : 'col',
        style : 'width: 100%',
        options: [
          { value : 'uuid4', text : 'UUID v4 (<?php _e( 'Preferred', 'wishlist-member' ); ?>)' },
          { value : 'sha1', text : 'SHA-1' },
          { value : 'md5', text : 'MD5' },
          { value : 'random', text : '<?php _e( 'Random 32-character string', 'wishlist-member' ); ?>' },
        ]
      }
    </template>
    <template class="wlm3-form-group">
      {
        type: 'number',
        label: '<?php _e( 'Quantity', 'wishlist-member' ); ?>',
        id : 'generate-code-quantity',
        column : 'col-3 pl-0',
        value : 5000,
      }
    </template>
    <div class="col-auto pl-0">
      <label>&nbsp;</label><br>
      <button type="button" class="btn -default -condensed" id="generate-codes"><?php _e( 'Generate', 'wishlist-member' ); ?></button>
    </div>
  </div>
</div>
