  <?php while ( $level = array_shift( $levels ) ) : ?>
    <?php  if ( !$level->Name ) continue; ?>
    <tr class="more-details wlm-user-level-details" data-levelid="<?php echo $level->Level_ID; ?>">
      <td class="table-form-container text-center pr-sm-0 pl-sm-0 level-tools-sm">
        <div class="btn-group-action">
          <?php if ( $level->Timestamp !== false && !$level->Scheduled ) : ?>
            <a href="#" title="Move Level" data-userid="<?php echo $uid; ?>" data-lvlname="<?php echo $level->Name; ?>" data-levelid="<?php echo $level->Level_ID; ?>" class="wlm-icons md-24 move-level-btn d-lg-inline d-md-inline">swap_horiz</a>
            <a href="#" title="<?php echo $level->Cancelled ? "Uncancel from Level" : "Cancel from Level"; ?>" data-userid="<?php echo $uid; ?>" data-lvlname="<?php echo $level->Name; ?>" data-levelid="<?php echo $level->Level_ID; ?>" class="wlm-icons md-24 d-lg-inline d-md-inline <?php echo $level->Cancelled ? "uncancel" : "cancel"; ?>-level-btn"><?php echo $level->Cancelled ? "replay" : "close"; ?></a>
            <a href="#" title="Remove from Level" data-userid="<?php echo $uid; ?>" data-lvlname="<?php echo $level->Name; ?>" data-levelid="<?php echo $level->Level_ID; ?>" class="wlm-icons md-24 delete-level-btn d-lg-inline d-md-inline">remove_circle_outline</a>
          <?php endif; ?>
        </div>
      </td>
      <!-- <td style="padding-left: 34px"> -->
      <td style="padding-left: 9px">
          <?php if( $level->Expired ): ?><i class="wlm-icons md-24 level-icon color-orange" title="Expired" style="cursor: default;">timer_off</i><?php endif; ?>
          <?php if( $level->Cancelled ): ?><i class="wlm-icons md-24 level-icon color-red" title="Cancelled" style="cursor: default;">cancelled_icon</i><?php endif; ?>
          <?php if( $level->UnConfirmed ): ?><i class="wlm-icons md-24 level-icon color-blue02" title="Unconfirmed" style="cursor: default;">needs_confirm</i><?php endif; ?>
          <?php if( $level->Pending ): ?><i class="wlm-icons md-24 level-icon color-blue02" title="Needs Approval" style="cursor: default;">needs_approval</i><?php endif; ?>
          <?php if ( !$level->Expired && !$level->Cancelled && !$level->UnConfirmed && !$level->Pending ): ?>
            <?php if ( in_array( 'Scheduled', $level->Status ) ) : ?>
              <i class="wlm-icons md-24 level-icon color-blue02" title="Scheduled" style="cursor: default;">date_range</i>
            <?php else: ?>
              <i class="wlm-icons md-24 level-icon color-green" title="Active" style="cursor: default;">active_icon</i>
            <?php endif; ?>
          <?php endif; ?>
          &nbsp;
          <span class='wlm-level-name' style="vertical-align: middle;">
            <?php
              $lname = $level->Name;
              if($level->Expired || $level->Cancelled) {
                $lname = sprintf('<strike>%s</strike>', $lname);
              }
              if($level->UnConfirmed || $level->Pending) {
                $lname = sprintf('<em>%s</em>', $lname);
              }
              echo $lname;
            ?>
          </span>
      </td>
      <td>
          <span class='wlm-level-details'>
          <?php
              $this_status = array();
              if ( !in_array( 'Active', $level->Status ) && !in_array( 'Scheduled', $level->Status ) ) {
                $level_pending = array();
                $pending_statuses =  $level->Status;
                foreach ( $pending_statuses as $pending_status ) {
                  if ( $pending_status == 'For Approval' ) {
                    // Check if the reason for "For Approval" is due to a pending payment in Shopping Cart, 
                    // so far only Pin Payments and Paypal are the SC's that sends pending notifications.
                    if ( in_array( $level->Pending, array('Paypal Pending', 'Pin Payments Confirmation') ) ){
                      $level_pending[] = $pending_status .' ' .$level->Pending;
                    } else {
                      // $link = sprintf('<small><a href="#" data-schedule-type="approve" data-level-id="%s" data-userid="%d" class="wlm-icons md-24 text-success unschedule-level-btn">active_icon</a></small>', $level->Level_ID, $wlUser->ID );
                      // $level_pending[] = "{$link} {$pending_status}";
                      $level_pending[] = sprintf('<a href="#" title="Approve" data-schedule-type="approve" data-level-id="%s" data-userid="%d" class="scheduled-action-btn -approve unschedule-level-btn"><i class="wlm-icons md-20">check_circle</i></a> Needs Approval', $level->Level_ID, $wlUser->ID );
                    }
                  } else {
                    $status_date = "";
                    if ( $level->Expired ) {
                       $status_date = __(" on ", 'wishlist-member') .date_i18n( get_option( 'date_format' ), $level->ExpiryDate + $this->GMT);
                    } elseif ( $level->Cancelled ) {
                        $status_date = __(" on ", 'wishlist-member') .date_i18n( get_option( 'date_format' ), $level->CancelledDate + $this->GMT);
                    }
                    $level_pending[] = $pending_status .$status_date;
                  }
                }
                $this_status = $level_pending;
              }
              if ( in_array( 'Scheduled', $level->Status ) ) {
                $link = sprintf('<a href="#" title="Unschedule" data-schedule-type="%s" data-level-id="%s" data-userid="%d" class="scheduled-action-btn -remove unschedule-level-btn"><i class="wlm-icons md-20">remove_circle</i></a>', $level->ScheduleInfo['type'], $level->Level_ID, $wlUser->ID);
                $str_action = $level->ScheduleInfo['type'] == 'move' ? ucwords($level->ScheduleInfo['type']) ." from {$wpm_levels[$level->ScheduleInfo['level_from']]['name']}" : ucwords($level->ScheduleInfo['type']) ;
                $this_status[] = sprintf('<span>%s %s to %s on %s</span>', $link, $str_action, $level->Name, date_i18n( get_option( 'date_format' ), strtotime($level->ScheduleInfo['date']) + $this->GMT) );
              } else {
                if ( $level->SequentialCancelled && $level->Active ) {
                  echo '<span><em>Sequential Upgrade stopped</em></span>';
                }
                $more_schedules = array();
                $remove = $this->Get_UserLevelMeta( $wlUser->ID, $level->Level_ID, 'scheduled_remove' );
                //check if serialized data, added this part because the result has '";' added to it
                if ( @unserialize($remove) !== false ) {
                  $cnt = 0;
                  while ( !is_array($remove) && $cnt < 4 ) {
                    $remove = wlm_maybe_unserialize($remove);
                    $cnt++;
                  }
                }
                //end -> need to double check on this issue
                if ( $remove ) $more_schedules['remove'] = strtotime($remove['date']) + $this->GMT;
                if ( $level->CancelDate && !$level->Cancelled ) $more_schedules['cancel'] = $level->CancelDate;
                if ( $level->ExpiryDate && !$level->Expired ) $more_schedules['expire'] = $level->ExpiryDate;
                if ( $more_schedules ) asort( $more_schedules );
                foreach ( $more_schedules AS $key => $date ) {
                  $event = $event_reason = $link = '';
                  switch ( $key ) {
                    case 'remove':
                      $event = __('be Removed', 'wishlist-member');
                      $link = sprintf('<a href="#" title="Unschedule" data-schedule-type="remove" data-level-id="%s" data-userid="%d" class="scheduled-action-btn -remove unschedule-level-btn"><i class="wlm-icons md-20">remove_circle</i></a>', $level->Level_ID, $wlUser->ID);
                      // $level_pending[] = sprintf('<a href="#" data-schedule-type="approve" data-level-id="%s" data-userid="%d" class="btn -mini -success unschedule-level-btn"><span>Approve</span></a>', $level->Level_ID, $wlUser->ID );
                      break;
                    case 'cancel':
                      $event = __('be Cancelled', 'wishlist-member');
                      if( $level->CancelDateReason ) {
                        $event_reason = wishlistmember_instance()->tooltip(
                          wlm_arrval( $level, 'CancelDateReason', 'text' ), '', true,
                          array( 'icon-class' => 'md-20 align-bottom ml-1', 'icon' => wlm_arrval( $level, 'CancelDateReason', 'icon' ) ?: 'help' ) );
                      }
                      $link = sprintf('<a href="#" title="Unschedule" data-schedule-type="cancel" data-level-id="%s" data-userid="%d" class="scheduled-action-btn -remove unschedule-level-btn"><i class="wlm-icons md-20">remove_circle</i></a>', $level->Level_ID, $wlUser->ID);
                      break;
                    case 'expire':
                      $event = 'Expire';
                      break;
                  }
                  if ( $event ) {
                    $this_status[] = sprintf('<span>%s To %s on %s</span> %s', $link, $event, date_i18n( get_option( 'date_format' ), $date + $this->GMT ), $event_reason );
                  }
                }
              }
              if ( !empty( $this_status ) ) echo implode(', ', $this_status);
          ?>
          </span>
      </td>
      <td>
          <span class='wlm-level-date'>
            <?php echo  ( $level->Timestamp !== false && !$level->Scheduled )  ? date_i18n( get_option( 'date_format' ), $level->Timestamp + $this->GMT ) : ''; ?>
          </span>
      </td>
      <td></td>
    </tr>
  <?php endwhile; ?>
  <?php if( wlm_arrval( $wlUser, 'PayPerPosts', '_all_' ) ) : ?>
    <tr class="more-details wlm-user-level-details">
      <td class="table-form-container xtext-center xpr-sm-0 xpl-sm-0 xlevel-tools-sm">
      </td>
      <td style="padding-left: 9px">
        <a href="#" title="Edit Member" data-userid="1" data-tab-focus="#pay-per-posts,#ppphistory" class="edit-user-btn">
          <i class="wlm-icons md-24 level-icon" title="Pay Per Posts" style="cursor: default;">description</i>
          &nbsp;
          <span class="text-body"><?php _e( 'Pay Per Posts', 'wishlist-member' ); ?></span>
        </a>
      </td>
    </tr>
  <?php endif; ?>