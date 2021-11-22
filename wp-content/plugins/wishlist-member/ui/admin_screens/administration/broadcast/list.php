<?php $no_canspam = !$complete_canspam ? "no-can-spam" : ""; ?>
<div class="broadcast-list-holder <?php echo $no_canspam; ?>">
	<div class="page-header">
		<div class="row">
			<div class="col-md-9 col-sm-9 col-xs-7">
				<h2 class="page-title">
					<?php _e( 'Email Broadcasts', 'wishlist-member' ); ?>
					<a href="#" class="btn -primary -icon-only -success -rounded create-broadcast-btn">
						<i class="wlm-icons">add</i>
					</a>
				</h2>
			</div>
			<div class="col-md-3 col-sm-3 col-xs-5">
				<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
			</div>
		</div>
	</div>
	<div class="header-tools">
		<div class="row">
			<div class="col-md-8 col-sm-12 col-xs-12">
				<a href="#" class="btn -primary pull-left -condensed update-canspam-btn">
					<i class="wlm-icons">settings</i>
					<span class="v-align-0"><?php _e( 'Settings', 'wishlist-member' ); ?></span>
				</a>
				<p class="mb-0 mr-2 ml-2 pull-left">
					<?php _e( 'Emails In Queue:', 'wishlist-member' ); ?> <strong class="emails-in-queue-cnt"><?php echo $email_queue_count <= 0 ? '0' : $email_queue_count; ?></strong>
					&nbsp;&nbsp;&nbsp;&nbsp;<span class="send-queue-status text-muted <?php echo $email_queue_count <= 0 ? 'd-none' : '' ?>">Sending emails in queue...</span>
					<br />
					Last Queued Email Sent:
					<strong>
						<?php $Queue_Sent = $this->GetOption('WLM_Last_Queue_Sent'); ?>
						<?php if ( $Queue_Sent != '' ) : ?>
							<?php echo date( get_option( 'date_format' ) ." " .get_option('time_format'),strtotime( $Queue_Sent ) ); ?>
						<?php else : ?>
							No Email Sent
						<?php endif; ?>
					</strong>
				</p>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-4">

			</div>
		</div>
	</div>
	<?php if ( $emails_count && $emails_count > $this->pagination_items[0] ) : ?>
			<div class="col-md-12">
				<div class="pagination pull-right">
					<div class="count pull-left">
						<div role="presentation" class="dropdown page-rows">
							<a href="#" class="dropdown-toggle" id="drop-page" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								<?php echo $offset; ?> - <?php echo ($perpage * $current_page) > $emails_count ? $emails_count : $perpage * $current_page; ?>
							</a> of <?php echo $emails_count; ?>
							<ul class="dropdown-menu" id="menu1" aria-labelledby="drop-page">
								<?php foreach ( $this->pagination_items as $value ) : ?>
									<li><a target="_parent" href="<?php echo $form_action ."&howmany=" .$value; ?>"><?php echo $value; ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<?php if ( $perpage <= $emails_count ) : ?>
						<div class="arrows pull-right">
							<?php
							if ( $current_page <= 1 ) $previous_link = $form_action ."&offset=" .$total_pages;
							else $previous_link = $form_action ."&offset=" .($current_page-1);
							?>
							<a target="_parent" href="<?php echo $previous_link; ?>" class="wlm-icons">keyboard_arrow_left</a>
							<?php
							if ( $current_page < $total_pages ) $next_link = $form_action ."&offset=" .($current_page+1);
							else $next_link = $form_action ."&offset=1";
							?>
							<a target="_parent" href="<?php echo $next_link; ?>" class="wlm-icons">keyboard_arrow_right</a>
						</div>
					<?php endif; ?>
				</div>
				<br class="d-none d-sm-block d-md-none">
				<br class="d-none d-sm-block d-md-none">
				<br class="d-none d-sm-block d-md-none">
			</div>
	<?php endif; ?>
	<div class="table-wrapper table-responsive">
		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th width="20%" class="text-center">Date</th>
					<th width="25%">Subject</th>
					<th width="9%" class="text-center">Sent As</th>
					<th width="20%">Sent To</th>
					<th width="8%" class="text-center">Recipients</th>
					<th width="8%" class="text-center">Status</th>
					<th width="10%" class="text-center">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $emails_count ) : ?>
					<?php foreach ($broadcast_emails AS $broadcast): ?>
						<?php
							$tqcount = $this->GetEmailBroadcastQueue($broadcast->id,false,true,0,true);
						?>
						<tr class="button-hover tr-<?php echo $broadcast->id; ?>">
							<td class="text-center"><?php echo date( get_option( 'date_format' ) ." " .get_option('time_format'),strtotime( $broadcast->date_added) ); ?></td>
							<td>
								<a href="#" class="duplicate-broadcast-btn" data-id="<?php echo $broadcast->id; ?>">
								<?php
									if ( strlen( $broadcast->subject ) >= 30 ) {
										$str = str_split($broadcast->subject,30);
										$str = $str[0] ."...";
										echo "<span title='{$broadcast->subject}'>";
										echo $str;
										echo "</span>";
									} else {
										echo  $broadcast->subject;
									}
								?>
								</a>
							</td>
							<td class="text-center"><?php echo strtoupper($broadcast->sent_as); ?></td>
							<td>
								<?php
									$lvl_id = explode('#', $broadcast->mlevel);
									$em = "";
									foreach ((array) $lvl_id AS $id => $level) {
										if (isset($wpm_levels[$level])) {
											$em .= $wpm_levels[$level]["name"] . ", ";
										}else if(strpos($level,"SaveSearch") !== false || strpos($level,"SavedSearch") !== false){
											$em .= $level . ", ";
										}
									}
									$em = substr( $em, 0, -2 );
									if ( strlen( $em ) >= 30 ) {
										$str = str_split($em,30);
										$str = $str[0] ."...";
										echo "<span title='{$em}'>";
										echo $str;
										echo "</span>";
									} else {
										echo  $em;
									}
								?>
							</td>
							<td class="text-center email-recipients email-recipients-<?php echo $broadcast->id; ?>">
								<?php $email_queue = $this->GetFailedQueue( $broadcast->id ); ?>
								<a href="#" title="Check Status" class="check-status-btn" data-id="<?php echo $broadcast->id; ?>"><?php echo $broadcast->total_queued; ?></a>
								<?php if ( count( $email_queue ) > 0 ): ?>
									<span class="wlm-icons text-danger md-18 -icon-only">error_outline</span>
								<?php else: ?>
									<span style="display:none;" class="wlm-icons text-danger md-18 -icon-only">error_outline</span>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<?php if ( $tqcount <= 0 && $broadcast->status != 'Queueing') : ?>
									<span class="broadcast-status"><?php _e( 'DONE', 'wishlist-member' ); ?></span>
								<?php else : ?>
									<span class="broadcast-status"><?php echo strtoupper($broadcast->status); ?></span>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<div class="btn-group-action">
									<?php if ( $tqcount <= 0 && $broadcast->status != 'Queueing') : ?>
										<a href="#" title="Queue" data-id="<?php echo $broadcast->id; ?>" data-status="Queued" class="btn broadcast-queued-btn d-none"><span class="wlm-icons md-24 -icon-only">send</span></a>
										<a href="#" title="Pause" data-id="<?php echo $broadcast->id; ?>" data-status="Paused" class="btn broadcast-paused-btn d-none"><span class="wlm-icons md-24 -icon-only">pause</span></a>
										<a href="#" title="Delete" data-id="<?php echo $broadcast->id; ?>" data-status="Delete" class="btn broadcast-delete-btn -del-btn"><span class="wlm-icons md-24 -icon-only">delete</span></a>
									<?php elseif ( $broadcast->status == "Paused" ) : ?>
										<a href="#" title="Queue" data-id="<?php echo $broadcast->id; ?>" data-status="Queued" class="btn broadcast-queued-btn"><span class="wlm-icons md-24 -icon-only">send</span></a>
										<a href="#" title="Pause" data-id="<?php echo $broadcast->id; ?>" data-status="Paused" class="btn broadcast-paused-btn d-none"><span class="wlm-icons md-24 -icon-only">pause</span></a>
										<a href="#" title="Delete" data-id="<?php echo $broadcast->id; ?>" data-status="Delete" class="btn broadcast-delete-btn -del-btn"><span class="wlm-icons md-24 -icon-only">delete</span></a>
									<?php elseif ( $broadcast->status == "Queued" ) : ?>
										<a href="#" title="Queue" data-id="<?php echo $broadcast->id; ?>" data-status="Queued" class="btn broadcast-queued-btn d-none"><span class="wlm-icons md-24 -icon-only">send</span></a>
										<a href="#" title="Pause" data-id="<?php echo $broadcast->id; ?>" data-status="Paused" class="btn broadcast-paused-btn"><span class="wlm-icons md-24 -icon-only">pause</span></a>
										<a href="#" title="Delete" data-id="<?php echo $broadcast->id; ?>" data-status="Delete" class="btn broadcast-delete-btn d-none -del-btn"><span class="wlm-icons md-24 -icon-only">delete</span></a>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="7" class="text-center">There are no previous broadcasts.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>