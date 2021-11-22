<?php
$wpm_levels = $this->GetOption( 'wpm_levels' );
$total_cnt  = count( $wpm_levels );
$this->SortLevels( $wpm_levels, 'a', 'levelOrder' );

$keys = array_flip( array (
	'name',
	'upgradeAfter',
	'upgradeAfterPeriod',
	'upgradeEmailNotification',
	'upgradeMethod',
	'upgradeOnDate',
	'upgradeSchedule',
	'upgradeTo',
	) );
foreach( $wpm_levels AS &$level ) {
	$level = array_intersect_key( $level, $keys );
}
$this->set_timezone_to_wp();

$howmany = $this->GetOption( 'sequential_pagination' );
if (is_numeric(wlm_arrval($_GET, 'howmany')) || !$howmany || wlm_arrval($_GET, 'howmany') == "Show All") {
	$howmany = wlm_arrval($_GET, 'howmany');
	if ( !$howmany ) $howmany = $this->pagination_items[1];
	if ( !in_array( $howmany, $this->pagination_items ) ) $howmany = $this->pagination_items[1];
	//we only save if not show all
	if ( $howmany != "Show All" ) $this->SaveOption('sequential_pagination', $howmany);
}
$howmany = $howmany == "Show All" ? 999999999 : $howmany;

$offset = $_GET['offset'] - 1;
if ( $offset < 0 ) {
	$offset = 0;
}
$offset            = $offset * $howmany;
$membership_levels = array_slice( $wpm_levels, $offset, $howmany, true );
$current_page      = $offset / $howmany + 1;
$offset           += 1;
$total_pages       = ceil( $total_cnt / $howmany );
$form_action       = "?page={$this->MenuID}&wl=" . ( isset( $_GET['wl'] ) ? $_GET['wl'] : 'members/sequential' );
?>
<div class="page-header">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-8">
			<h2 class="page-title"><?php _e( 'Sequential Upgrade', 'wishlist-member' ); ?></h2>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-4">
			<?php require $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
	</div>
</div>
<?php if ( $total_cnt && $total_cnt > $this->pagination_items[0] ) : ?>
<div class="row">
	<div class="col-md-12">
		<div class="pagination -minimal pull-right">
			<div class="count pull-left">
				<div role="presentation" class="dropdown page-rows">
					<a href="#" class="dropdown-toggle" id="drop-page" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<?php echo $offset; ?> - <?php echo ( $howmany * $current_page ) > $total_cnt ? $total_cnt : $howmany * $current_page; ?>
					</a> of <?php echo $total_cnt; ?>
					<ul class="dropdown-menu" id="menu1" aria-labelledby="drop-page">
						<?php foreach ( $this->pagination_items as $key => $value ) : ?>
						<a class="dropdown-item" target="_parent" href="<?php echo $form_action . '&howmany=' . $value; ?>"><?php echo $value; ?></a>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php if ( $howmany <= $total_cnt ) : ?>
			<div class="arrows pull-right">
				<?php
				if ( $current_page <= 1 ) {
					$previous_link = $form_action . '&offset=' . $total_pages;
				} else {
					$previous_link = $form_action . '&offset=' . ( $current_page - 1 );
				}
				?>
				<a target="_parent" href="<?php echo $previous_link; ?>" >
					<i class="wlm-icons md-26">keyboard_arrow_left</i>
				</a>
				<?php
				if ( $current_page < $total_pages ) {
					$next_link = $form_action . '&offset=' . ( $current_page + 1 );
				} else {
					$next_link = $form_action . '&offset=1';
				}
				?>
				<a target="_parent" href="<?php echo $next_link; ?>">
					<i class="wlm-icons md-26">keyboard_arrow_right</i>
				</a>
			</div>
			<?php endif; ?>
		</div>
		<br class="d-none d-sm-block d-md-none">
		<br class="d-none d-sm-block d-md-none">
		<br class="d-none d-sm-block d-md-none">
	</div>
</div>
<?php endif; ?>
<div class="row">
	<div class="col-md-12">
		<p><em><?php _e( 'WordPress Time:', 'wishlist-member' ); ?>
			<?php printf( '%s %s %s', date( get_option( 'date_format' ) ), date( get_option( 'time_format' ) ), $this->get_wp_tzstring( true ) ); ?>
		</em></p>
		<div class="table-wrapper table-responsive">
			<table class="table table-striped table-condensed">
				<colgroup>
					<col width="33%">
					<col>
					<col width="20">
				</colgroup>
				<thead>
					<tr>
						<th><?php _e( 'Membership Level', 'wishlist-member' ); ?></th>
						<th><?php _e('Upgrade', 'wishlist-member') ; ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody id="sequential-list"></tbody>
			</table>
		</div>
	</div>

</div>
<div class="content-wrapper -no-background">
	<h4><?php _e( 'Advanced Settings', 'wishlist-member' ); ?></h4>
	<div class="row">
		<div class="col-md-12">
			<p><?php _e( 'Sequential Upgrades are automatically triggered when a member signs in to their account. If you would like to set your system to trigger upgrades without requiring a member to sign in, you must create a Cron Job on your server.', 'wishlist-member' ); ?></p>
			<p><a href="?page=WishListMember&wl=advanced_settings/cron_jobs" target="_blank"><?php _e( 'Click Here', 'wishlist-member' ); ?></a> for instructions on how to set-up a Cron Job for WishList Member.</p>
		</div>
	</div>
</div>

<script type="text/template" id="wlm-sequential-list-template">
<% _.each(data.levels, function(lvl, lvlid) { %>
	<%
		// reset upgrade info if upgradeTo is not a valid level
		if( lvl.upgradeTo in data.wpm_levels === false ) {
			lvl.upgradeMethod = '';
			lvl.upgradeTo = null;
			lvl.upgradeSchedule = null;
			lvl.upgradeOnDate = null;
			lvl.upgradeAfter = null;
			lvl.upgradeAfterPeriod = null;
		}
		
		// set upgradeOnDate to today if it is empty
		if(!lvl.upgradeOnDate) {
			lvl.upgradeOnDate = Date.now() / 1000;
		}
	%>
	<tr class="tr tr-<%- lvlid %> button-hover" data-level-id="<%- lvlid %>" data-level-name="<%- lvl.name %>">
		<td>
			<span class="text"><a href="#sequential-upgrade" data-toggle="modal"><%- lvl.name %></a></span>
		</td>
		<td>
			<div class="seq-values">
				<input type="hidden" name="upgradeMethod" value="<%- String(lvl.upgradeMethod).replace( /(\d+|undefined)/, '' ) || 'inactive' %>">
				<input type="hidden" name="upgradeTo" value="<%- lvl.upgradeTo %>">
				<input type="hidden" name="upgradeSchedule" value="<%- lvl.upgradeSchedule %>">
				<input type="hidden" name="sched_toggle" value="<%- lvl.upgradeSchedule %>">
				<input type="hidden" name="upgradeAfter" value="<%- lvl.upgradeAfter %>">
				<input type="hidden" name="upgradeAfterPeriod" value="<%- lvl.upgradeAfterPeriod %>">
				<input type="hidden" name="upgradeOnDate" value="<%- isNaN(lvl.upgradeOnDate) ? lvl.upgradeOnDate : moment(lvl.upgradeOnDate * 1000).format(WLM3VARS.js_datetime_format) %>">
				<input type="hidden" name="upgradeEmailNotification" value="<%- lvl.upgradeEmailNotification || '' %>">
			</div>
			<%
				var method;
				var configured = true;
				switch ( lvl.upgradeMethod ) {
					case 'MOVE':
						method = wlm.translate( 'Move to <strong>%s</strong>' ).replace('%s', data.wpm_levels[ lvl.upgradeTo ].name );
						break;
					case 'ADD':
						method = wlm.translate( 'Add to <strong>%s</strong>' ).replace('%s', data.wpm_levels[ lvl.upgradeTo ].name );
						break;
					case 'REMOVE':
						method = wlm.translate( 'Remove from <strong>%s</strong>' ).replace('%s', data.wpm_levels[ lvlid ].name );
						break;
					default:
						configured = false;
						method = wlm.translate( 'None' );
				}

				var schedule = '';
				if( configured ) {
					switch ( lvl.upgradeSchedule ) {
						case 'ondate':
							schedule = wlm.translate( 'on %s').replace('%s', isNaN(lvl.upgradeOnDate) ? lvl.upgradeOnDate : moment(lvl.upgradeOnDate * 1000).format(WLM3VARS.js_datetime_format) );
							break;
						default:
							schedule = lvl.upgradeAfter;
							switch ( lvl.upgradeAfterPeriod ) {
								case 'years':
									schedule = ( schedule == 1 ? wlm.translate('after %d Year') : wlm.translate('after %d Years') ).replace('%d', schedule );
									break;
								case 'months':
									schedule = ( schedule == 1 ? wlm.translate('after %d Month') : wlm.translate('after %d Months') ).replace('%d', schedule );
									break;
								case 'weeks':
									schedule = ( schedule == 1 ? wlm.translate('after %d Week') : wlm.translate('after %d Weeks') ).replace('%d', schedule );
									break;
								default:
									schedule = ( schedule == 1 ? wlm.translate('after %d Day') : wlm.translate('after %d Days') ).replace('%d', schedule );
									break;
							}
							break;
					}
				}
			%>
			<%= method + ' ' + schedule %>
			</td>
		<td>
			<div class="btn-group-action">
				<a href="#sequential-upgrade" data-toggle="modal" title="Edit Sequential Upgrade" class="btn -icon-only -edit-btn" target="_parent">
					<i class="wlm-icons md-24">edit</i>
				</a>
			</div>
		</td>
	</tr>
<% }); %>
</script>

<script type="text/javascript">
	function sequential_list(membership_levels) {
		if(!this.levels) {
			this.levels = <?php echo json_encode( $membership_levels ) ; ?>;
		}
		if(membership_levels) {
			this.levels = $.extend(true, levels, membership_levels);
		}
		var data = {
			levels : this.levels,
			wpm_levels : <?php echo json_encode( $wpm_levels ) ; ?>,
		}
		var html = _.template($('script#wlm-sequential-list-template').html(), {variable: 'data'})(data);
		$('#sequential-list').html(html.trim());
	}

</script>

<?php require_once 'sequential/edit.php'; ?>

<style type="text/css">
	tr.schedule-ondate .schedule-after-holder,
	tr.schedule-after .schedule-ondate-holder,
	tr.method-inactive .schedule-type-holder,
	tr.method-inactive .schedule-ondate-holder,
	tr.method-inactive .schedule-after-holder,
	tr.method-inactive .remove-from-holder,
	tr.method-add .remove-from-holder,
	tr.method-move .remove-from-holder {
		display: none;
	}
	tr.method-inactive .upgrade-to-holder,
	tr.method-remove .upgrade-to-holder {
		visibility: hidden;
		height: 0;
	}
</style>
