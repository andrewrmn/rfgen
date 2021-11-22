<?php
$wpm_levels = $this->GetOption('wpm_levels');
$nonmembers_cnt = $this->NonMemberCount();
$levels = array("NONMEMBERS"=>array("name"=>"Non-Members","count"=>$nonmembers_cnt)) + $wpm_levels;
$total_cnt = count( $levels );

$howmany = $this->GetOption( 'massmoveadd_pagination' );
if (is_numeric(wlm_arrval($_GET, 'howmany')) || !$howmany || wlm_arrval($_GET, 'howmany') == "Show All") {
	$howmany = wlm_arrval($_GET, 'howmany');
	if ( !$howmany ) $howmany = $this->pagination_items[1];
	if ( !in_array( $howmany, $this->pagination_items ) ) $howmany = $this->pagination_items[1];
	//we only save if not show all
	if ( $howmany != "Show All" ) $this->SaveOption('massmoveadd_pagination', $howmany);
}
$howmany = $howmany == "Show All" ? 999999999 : $howmany;

$offset = $_GET['offset'] - 1;
if ( $offset < 0 ) $offset = 0;
$offset = $offset * $howmany;
$membership_levels = array_slice( $levels, $offset, $howmany, true );
$current_page = $offset / $howmany + 1;
$offset += 1;
$total_pages = ceil( $total_cnt / $howmany);

$form_action = "?page={$this->MenuID}&wl=" .( isset( $_GET['wl'] ) ? $_GET['wl'] : "members/mass_move_add" );
?>
	<div class="page-header">
		<div class="row">
			<div class="col-md-9 col-sm-9 col-xs-8">
				<h2 class="page-title">
					<?php _e( 'Mass Move/Add Members', 'wishlist-member' ); ?>
				</h2>
			</div>
			<div class="col-md-3 col-sm-3 col-xs-4">
				<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
			</div>
		</div>
	</div>
	<div class="row">
<!-- 		<div class="col-md-8">
			<p>All Members within a Membership Level can be Moved/Added to a selected Membership Level using the Move/Add method below. <?php $this->tooltip(__('todo', 'wishlist-member')); ?></p>
		</div> -->
		<?php if ( $total_cnt && $total_cnt > $this->pagination_items[0] ) : ?>
			<div class="col-md-12">
				<div class="pagination -minimal pull-right">
					<div class="count pull-left">
						<div role="presentation" class="dropdown page-rows">
							<a href="#" class="dropdown-toggle" id="drop-page" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								<?php echo $offset; ?> - <?php echo ($howmany * $current_page) > $total_cnt ? $total_cnt : $howmany * $current_page; ?>
							</a> of <?php echo $total_cnt; ?>
							<div class="dropdown-menu" id="menu1" aria-labelledby="drop-page">
								<?php foreach ( $this->pagination_items as $key => $value) : ?>
									<a class="dropdown-item" target="_parent" href="<?php echo $form_action ."&howmany=" .$value; ?>"><?php echo $value; ?></a>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<?php if ( $howmany <= $total_cnt ) : ?>
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
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="table-wrapper table-responsive">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th style="width:20px"></th>
							<th style="width: 30%"><?php _e( 'From Membership Level', 'wishlist-member' ); ?></th>
							<th style="width: 10%" class="text-center"><?php _e( 'Members', 'wishlist-member' ); ?></th>
							<th style="width: 30%" class="text-center"><?php _e( 'To Membership Level', 'wishlist-member' ); ?></th>
							<th style="width: 30%" class="text-center"><?php _e( 'Actions', 'wishlist-member' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $membership_levels as $lvlid => $lvl ) : ?>
							<?php
								$lvl["count"] = $lvl["count"] ? $lvl["count"] : 0;
							?>
							<tr class="level-holder-<?php echo $lvlid; ?>">
								<td style="width:20px"></td>
								<td class="level-name"><?php echo  $lvl["name"]; ?></td>
								<td class="text-center level-count"><?php echo  $lvl["count"]; ?></td>
								<td class="text-center">
									<div class="form-group" <?php echo $lvl["count"] ? "" : "style='visibility:hidden;'" ?>>
										<label class="sr-only" for=""><?php _e( 'Member Role', 'wishlist-member' ); ?></label>
										<select class="form-control wlm-select move-to-level">
											<option value="">- Select a Level -</option>
											<?php foreach ( $wpm_levels as $key => $value ) : ?>
												<?php if ( $lvlid != $key ): ?>
													<option value="<?php echo $key; ?>"><?php echo  $value["name"]; ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
										</select>
									</div>
								</td>
								<td class="text-center">
									<div class="text-left" style="width: 190px ; margin: 0px auto 0px auto;">
										<?php $disabled = $lvl["count"] ? false : true; ?>
										<?php if ( !$disabled ) : ?>
											<a href="#" class="btn -success -condensed moveadd-members" data-action="add" data-levelid="<?php echo $lvlid; ?>">
												<i class="wlm-icons">add</i>
												<span><?php _e( 'Add', 'wishlist-member' ); ?></span>
											</a>
										<?php endif; ?>
										&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ( !$disabled && $lvlid != "NONMEMBERS" ) : ?>
											<a href="#" class="btn -info -condensed moveadd-members" data-action="move" data-levelid="<?php echo $lvlid; ?>">
												<i class="wlm-icons">swap_horiz</i>
												<span><?php _e( 'Move', 'wishlist-member' ); ?></span>
											</a>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

<!-- Modal -->
<div id="massmove-modal" data-id="massmove-modal" data-label="massmove_modal_label" data-title="Mass Move/Add Members" data-classes="modal-md" style="display:none">
	<div class="body">
		<h5 class="text-center message"><?php _e( '** Mass Move/Add Message goes here **', 'wishlist-member' ); ?></h5>
		<h5 class="text-danger text-center operation-warning"></h5>
		<h5 class="text-center message2"><?php _e( '** Mass Move/Add Message goes here **', 'wishlist-member' ); ?></h5>
		<div class="row progress-holder" style="display: none;">
			<div class="col-md-12">
				<div class="export-progress">
					<div class="progress">
						<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0" ></div>
					</div>
					<h5 class="text-center operation-msg"></h5>
					<div class="text-center mb-0 operation-loading"><img class="wlm3-modal-loader-overlay-balls" src="<?php echo $this->pluginURL3; ?>/ui/images/wlm-loader03.gif" /></div>
				</div>
				<br />
			</div>
		</div>
	</div>
	<div class="footer">
		<button type="button" class="btn -bare cancel-button" data-dismiss="modal"><?php _e( 'Cancel', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><span>Yes</span></button>
	</div>
</div>