<style media="screen">
  /* code stack numbering */
  table#code-redemption-code-actions {
    counter-reset: rowNumber;
  }
  table#code-redemption-code-actions tbody tr:not(.ui-sortable-placeholder) td:first-child::before {
    counter-increment: rowNumber;
    content: counter(rowNumber);
  }
</style>
<div class="tab-pane" id="coderedemption-campaign-modal-actions">
  <div class="row">
    <div class="col-12">
      <p><?php _e( 'A campaign requires at least one action. Add additional actions to allow the redemption of multiple codes. The first code will redeem the first action in the stack. The second code will redeem the second action and so on. Drag and drop to reorder.', 'wishlist-member' ) ?></p>
    </div>
  </div>
  <div class="table-wrapper -no-shadow">
    <table id="code-redemption-code-actions" class="table table-striped">
      <colgroup>
        <col width="50">
        <col width="130">
        <col>
        <col width="70">
      </colgroup>
      <tbody id="actions-tbody">
        <tr class="button-hover">
          <td class="text-center"></td>
          <td>
            <template class="wlm3-form-group">
              {
                type: 'select',
                name: 'access[0][action]',
                class: 'access-action',
                options: [
                  {value : 'add', text : '<?php _e( 'Add', 'wishlist-member' ); ?>'},
                  {value : 'move', text : '<?php _e( 'Move', 'wishlist-member' ); ?>'},
                ],
                style: 'width: 100%',
              }
            </template>
          </td>
          <td>
            <template class="wlm3-form-group">
              {
                type: 'select',
                name: 'access[0][levels]',
                class: 'access-levels',
                id : 'coderedemption-access-levels',
                grouped: true,
                multiple: true,
                options: all_levels_select_options,
                style: 'width: 100%',
                'data-placeholder': '<?php _e( 'Select access...', 'wishlist-member' ); ?>'
              }
            </template>
          </td>
          <td>
            <div class="btn-group-action text-right">
              <a href="#" title="<?php _e( 'Delete Action', 'wishlist-member' ); ?>" class="btn -icon-only -action-del-btn"><i class="wlm-icons md-24" title="<?php _e( 'Delete Action', 'wishlist-member' ); ?>">delete</i></a>
              <a href="#" style="cursor: move" title="Move Membership Level" class="btn -icon-only handle ui-sortable-handle"><i class="wlm-icons md-24">swap_vert</i></a>
            </div>
          </td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th><?php _e( 'Stack', 'wishlist-member' ); ?></th>
          <th><?php _e( 'Action', 'wishlist-member' ); ?></th>
          <th><?php _e( 'Access', 'wishlist-member' ); ?></th>
          <th></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="10"><button class="btn -success -condensed" id="add-action"><i class="wlm-icons">add</i><?php _e( 'Add Action', 'wishlist-member' ); ?></button></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
