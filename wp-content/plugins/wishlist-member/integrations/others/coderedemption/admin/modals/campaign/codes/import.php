<div role="tabpanel" class="tab-pane" id="coderedemption-campaign-modal-codes-import">
  <div class="row">
    <template class="wlm3-form-group">
      {
        type: 'file',
        label: '<?php _e( 'CSV File', 'wishlist-member' ) ?>',
        column: 'col-12',
        id: 'coderedemption-code-import-file',
      }
    </template>
    <template class="wlm3-form-group">
      {
        type: 'select',
        id: 'coderedemption-code-import-option',
        label: '<?php _e( 'Import Option', 'wishlist-member' ) ?>',
        options: [
          {value: 'skip', text: '<?php _e( 'Do not import duplicate codes', 'wishlist-member' ); ?>'},
          {value: 'update', text: '<?php _e( 'Update status of duplicate codes', 'wishlist-member' ); ?>'},
          {value: 'replace', text: '<?php _e( 'Delete and replace all Codes', 'wishlist-member' ); ?>'},
        ],
        style: 'width: 100%',
        column: 'col-md-6'
      }
    </template>
    <div class="col-12 pt-0">
      <button id="coderedemption-code-import-button" type="button" class="btn -default -condensed" type="button"><?php _e( 'Import', 'wishlist-member' ) ?></button>
    </div>
  </div>
</div>
