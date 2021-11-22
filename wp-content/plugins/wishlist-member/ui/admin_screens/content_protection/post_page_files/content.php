<?php
$perpage = $this->GetOption('content-tab-perpage');
if (is_numeric(wlm_arrval($_GET, 'perpage')) || !$perpage || wlm_arrval($_GET, 'perpage') == "Show All") {
	$perpage = wlm_arrval($_GET, 'perpage');
	if ( !$perpage ) $perpage = $this->pagination_items[1];
	if ( !in_array( $perpage, $this->pagination_items ) ) $perpage = $this->pagination_items[1];
	//we only save if not show all
	if ( $perpage != "Show All" ) $this->SaveOption('content-tab-perpage', $perpage);
}
$perpage = $perpage == "Show All" ? 999999999 : $perpage;

$s_status = wlm_arrval($_GET, 's_status') . '';
$post_status = array('publish', 'pending', 'draft', 'future', 'private');
if ( $content_type == 'attachment') {
	$post_status[] = 'inherit';
	$s_status = ( $s_status == 'publish' ) ? $s_status : 'inherit';
}
if( $s_status && in_array( $s_status, $post_status ) ) {
	$post_status = array( $s_status );
}
$s_level = trim(wlm_arrval($_GET,'s_level'));
$s_currentpage = isset( $_GET['paged'] ) ? $_GET['paged'] - 1 : -1;
if ( $s_currentpage < 0 ) $s_currentpage = 0;
$s_offset = $s_currentpage * $perpage;
$s_currentpage = $s_offset / $perpage + 1;

$exclude_pages = $this->ExcludePages(array());

$args = array( 'post_type' =>  $content_type, 'post__not_in' => $exclude_pages );
$args['post_status'] = $post_status;
if( $s_level ) {
	$args['post__in'] = $this->GetMembershipContent( $content_type, $s_level);
	$args['post__in'][] = 0;
}
$args['offset'] = $s_offset;

$args['orderby'] = trim( wlm_arrval( $_GET, 'orderby' ) ) ?: 'post_title';
$args['order'] = trim( wlm_arrval( $_GET, 'order' ) ) ?: 'asc';

$sort_name = 'desc';
$sort_name_icon = '';
if( $args['orderby'] == 'post_title' ) {
	$sort_name = strtolower( $args['order'] ) == 'desc' ? 'asc' : 'desc';
	$sort_name_icon = $sort_name == 'desc' ? 'arrow_drop_up' : 'arrow_drop_down';
}

$sort_date = 'desc';
$sort_date_icon = '';
if( $args['orderby'] == 'post_date' ) {
	$sort_date = strtolower( $args['order'] ) == 'desc' ? 'asc' : 'desc';
	$sort_date_icon = $sort_date == 'desc' ? 'arrow_drop_up' : 'arrow_drop_down';
}

$args['posts_per_page'] = $perpage;
if ( isset( $_REQUEST['wlm_post_search_term'] ) ) {
	$args['s'] = $_REQUEST['wlm_post_search_term'];
}

//used for $page_href below
$url_keys = array_intersect_key( $_GET, array(
	"wlm_post_search_term"=>"",
	"s_level"=>"",
	"s_status"=>"",
	"orderby"=>"",
	"order"=>"",
));
$url_keys = array_filter($url_keys, 'strlen');


$is_custom_posttype = !in_array( $content_type,  array('page', 'attachment', 'post') );
//check if content is heirarchical, including custom post type
$is_heirarchical = $content_type == 'page' ? true : false;
$is_heirarchical = $is_custom_posttype ? is_post_type_hierarchical( $content_type) : $is_heirarchical;

$post_children = array();
$post_parents = array();
$postids = array();
if ( $is_heirarchical && !count($url_keys) ) {
	$args['sort_order'] = $args['order'];
	$args['sort_column'] = $args['orderby'];
	$args['exclude'] = implode(",", $exclude_pages );

	$pages_list = get_pages($args);
	$total_items = count($pages_list);
	$content_items = array_slice($pages_list, $s_offset, $perpage );

	//lets do it heirarchically
	if ( wlm_arrval( $_GET, 'orderby' ) == NULL ) {
		//get all the parents
		foreach ($content_items as $key => $value ) {
			if ( $value->post_parent ) $post_parents[] = $value->post_parent;
			$postids[] = $value->ID;
		}

		//check the parent if present, if not, add it
		foreach ( $post_parents as $parent_id ) {
			if ( !in_array( $parent_id, $postids ) ) {
				array_unshift( $content_items, get_post( $parent_id ) );
				$postids[] = $parent_id;
			}
		}

		//get all the children
		foreach ( $content_items as $key => $value ) {
			if ( $value->post_parent ) {
				$post_children[$value->post_parent][] = $value;
				unset($content_items[$key]); //remove the children from list, they be added seperately
			}
		}
	}
} else { // for non heirarchical post types
	$the_posts = new WP_Query($args);
	$content_items = $the_posts->posts;
	$total_items = $the_posts->found_posts;
	$is_heirarchical = count($url_keys);
}


$total_pages = ceil( $total_items / $perpage);
$s_offset += 1;

// Get Membership Levels
$wpm_levels = $this->GetOption('wpm_levels');
$page_href = "?page={$this->MenuID}&wl=" .( isset( $_GET['wl'] ) ? $_GET['wl'] : "content_protection/{$content_type}/content" );
$page_href .= "&" .build_query( $url_keys );


function display_items( $that, $item, $post_children, $content_type, $content_comment, $checkbox_check, $is_heirarchical) {
	if ( isset($post_children[$item->ID]) ) {
		include( $that->pluginDir3 ."/ui/admin_screens/content_protection/post_page_files/content-item.php");
		foreach ( $post_children[$item->ID] as $key => $value ) {
			display_items( $that, $value, $post_children, $content_type, $content_comment, $checkbox_check, $is_heirarchical);
		}
	} else {
		include( $that->pluginDir3 ."/ui/admin_screens/content_protection/post_page_files/content-item.php");
	}
}
?>

<?php if( $custom_post_type && !$content_comment ) : ?>
	<div class="header-tools" style="border: none">
		<div class="row">
			<div class="col-md-6">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Enable protection', 'wishlist-member' ); ?>',
						name  : '<?php echo $custom_post_type; ?>',
						value : '1',
						checked_value : '<?php echo $enabled_custom_post_types; ?>',
						uncheck_value : '0',
						class : 'wlm_toggle-switch enable-custom-post-type',
						type  : 'checkbox',
					}
				</template>
				<input type="hidden" name="action" value="admin_actions" />
				<input type="hidden" name="WishListMemberAction" value="enable_custom_post_types" />
			</div>
		</div>
	</div>
<?php  endif; ?>
<?php if ( $custom_post_type && !$enabled_custom_post_types ) return; ?>

<div class="header-tools -no-border no-padding">
	<div class="row">
		<div class="col-sm-12 col-md-4 col-lg-4 mb-sm-1">
			<div class="form-group">
				<label class="sr-only" for=""><?php _e( 'Actions', 'wishlist-member' ); ?></label>
				<select class="form-control wlm-select blk-actions" name="" id="" style="width: 100%">
					<option value="">- Select an Action -</option>
					<option value="protection">Edit Content Protection Status</option>
					<option value="add_level">Add Level(s) to Content</option>
					<option value="remove_level">Remove Level(s) from Content</option>
					<?php if ( !$content_comment && $content_type != 'attachment'  ): ?>
						<option value="ppp">Edit Per Member Access</option>
						<option value="addpppusers">Add Pay Per Post Members</option>
						<option value="removepppusers">Remove Pay Per Post Members</option>
					<?php endif; ?>
				</select>
			</div>
		</div>
		<div id="AdvancedSearchForm" class="search-bar col-sm-12 col-lg-<?php echo $content_type == 'attachment' ? '6' : '8';  ?> col-md-<?php echo $content_type == 'attachment' ? '6' : '8';  ?>">
			<form method="get" target="_parent" id="search-form" action="?<?php echo $this->QueryString(); ?>">
				<?php
					//lets add the querystring in hidden fields
					//this is needed since we are passing form tru GET
					$retain_keys = array("page", "wl");
					foreach( $_GET as $key=>$content){
						if ( in_array( $key, $retain_keys ) ) echo "<input type='hidden' name='$key' value='$content' />";
					}
				?>
				<div class="input-group">
					<input type="text" class="form-control" placeholder="Search Text" name="wlm_post_search_term" value="<?php echo esc_attr(stripslashes(wlm_arrval($_GET, 'wlm_post_search_term'))) ?>">
					<div class="input-group-append" style="width: 120px">
						<select class="form-control wlm-select" name="s_level">
							<option value="">- <?php _e( 'All Levels -', 'wishlist-member' ); ?></option>
							<?php foreach ( $wpm_levels as $key => $value ) : ?>
								<option value="<?php echo $key; ?>" <?php if (wlm_arrval($_GET, 's_level') == $key) echo " selected='true'"; ?>><?php echo $value['name'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<?php
						$post_statuses = array( "publish" => 'Published', "future" => 'Scheduled', "draft" => 'Draft', "pending" => 'Pending', "private" => 'Private');
					?>
					<div class="input-group-append" style="width: 120px">
						<select class="form-control wlm-select" name="s_status" id="">
							<option value="">- <?php _e( 'All Statuses -', 'wishlist-member' ); ?></option>
							<?php foreach ( $post_statuses as $key => $value ) : ?>
								<option value="<?php echo $key; ?>" <?php if (wlm_arrval($_GET, 's_status') == $key) echo " selected='true'"; ?>><?php echo $value ?></option>
							<?php endforeach; ?>
						</select>				
					</div>

					<div class="input-group-append">
						<button class="btn -default -icon search-btn btn-block">
							<i class="wlm-icons">search</i>
						</button>						
					</div>									
				</div>
			</form>
		</div>
		<?php if ( $content_type == 'attachment'  ): ?>
			<div class="col-md-2 col-lg-2 mt-sm-2 mt-lg-0 mt-md-0">
				<a href="#" class="btn -primary -condensed settings-btn">
					<i class="wlm-icons">settings</i>
					<span><?php _e( 'Settings', 'wishlist-member' ); ?></span>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<ul class="list-inline list-legend pull-left">
			<li class="list-inline-item"><?php _e( 'Regular text = Protected Level', 'wishlist-member' ); ?></li>
			<li class="list-inline-item"><em>Italics</em> <?php _e( 'text = Level has access to all posts.', 'wishlist-member' ); ?></li>
			<li class="list-inline-item"><strike>Strikethrough</strike> <?php _e( 'text = Level added but post is unprotected.', 'wishlist-member' ); ?></li>
		</ul>
		<div class="pagination pull-right mt-3">
			<?php if ( $total_items && $total_items > $this->pagination_items[0] ) : ?>
					<?php if ( $perpage <= $total_items ) : ?>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="text-muted pr-2">
									<div role="presentation" class="dropdown mt-9px">
										<a href="#" class="dropdown-toggle" id="drop-page" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
											<?php echo number_format($s_offset, 0, '.', ','); ?> - <?php echo ($perpage * $s_currentpage) > $total_items ? number_format($total_items, 0, '.', ',') : number_format( $perpage * $s_currentpage , 0, '.', ','); ?>
										</a> of <?php echo number_format($total_items, 0, '.', ','); ?>
										<ul class="dropdown-menu" id="menu1" aria-labelledby="drop-page">
											<?php foreach ( $this->pagination_items as $key => $value) : ?>
												<a class="dropdown-item" target="_parent" href="<?php echo $page_href ."&perpage=" .$value; ?>"><?php echo $value; ?></a>
											<?php endforeach; ?>
										</ul>
									</div>
								</span>
								<?php if ( $s_currentpage > 1 ) : ?>
									<a target="_parent" href="<?php echo $page_href ."&paged=1"; ?>" class="mt-6px"><i class="wlm-icons md-26">first_page</i></a>
								<?php else: ?>
									<a class="mt-6px text-muted disabled" disabled='disabled'><i class="wlm-icons md-26">first_page</i></a>
								<?php endif; ?>

								<?php
									if ( $s_currentpage <= 1 ) $previous_link = $page_href ."&paged=" .$total_pages;
									else $previous_link = $page_href ."&paged=" .($s_currentpage-1);
								?>
								<a target="_parent" href="<?php echo $previous_link; ?>" class="mt-6px"><i class="wlm-icons md-26">keyboard_arrow_left</i></a>
							</div>
							<input type="text" value="<?php echo $s_currentpage; ?>" class="form-control text-center pagination-pagenum" data-pages="<?php echo $total_pages; ?>" data-link="<?php echo $page_href ."&paged="; ?>" data-lpignore="true">
							<div class="input-group-append">
								<span class="mt-9px"> of <?php echo $total_pages; ?></span>
								<?php
									if ( $s_currentpage < $total_pages ) $next_link = $page_href ."&paged=" .($s_currentpage+1);
									else $next_link = $page_href ."&paged=1";
								?>
								<a target="_parent" href="<?php echo $next_link; ?>" class="mt-6px"><i class="wlm-icons md-26">keyboard_arrow_right</i></a>

								<?php if ( $s_currentpage < $total_pages ) : ?>
									<a target="_parent" href="<?php echo $page_href ."&paged=" .$total_pages; ?>" class="mt-6px"><i class="wlm-icons md-26">last_page</i></a>
								<?php else: ?>
									<a class="mt-6px text-muted disabled" disabled='disabled'><i class="wlm-icons md-26">last_page</i></a>
								<?php endif; ?>
							</div>
						</div>
					<?php else: ?>
							<div style="width: auto" class="input-group pull-right">
								<div class="input-group-prepend">
									<span class="text-muted pr-2">
										<div role="presentation" class="dropdown mt-9px">
											<a href="#" class="dropdown-toggle" id="drop-page" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
												<?php echo number_format($s_offset, 0, '.', ','); ?> - <?php echo ($perpage * $s_currentpage) > $total_items ? number_format($total_items, 0, '.', ',') : number_format( $perpage * $s_currentpage , 0, '.', ','); ?>
											</a> of <?php echo number_format($total_items, 0, '.', ','); ?>
											<ul class="dropdown-menu" id="menu1" aria-labelledby="drop-page">
												<?php foreach ( $this->pagination_items as $key => $value) : ?>
													<a class="dropdown-item" target="_parent" href="<?php echo $page_href ."&perpage=" .$value; ?>"><?php echo $value; ?></a>
												<?php endforeach; ?>
											</ul>
										</div>
									</span>
								</div>
							</div>
					<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="table-wrapper -special table-responsive -cp-table">
			<table class="table table-condensed">
				<thead>
					<tr class="button-hover">
						<th style="width: 40px" class="text-center">
							<div class="form-check -for-tables">
								<input value="" type="checkbox" class="chk-all form-check-input">
								<label for="" class="form-check-label d-none"></label>
							</div>
						</th>
						<th>
							<a href="<?php echo add_query_arg( ['orderby' => 'post_title', 'order' => $sort_name], admin_url( 'admin.php' . $page_href) ); ?>"><?php _e('Name', 'wishlist-member'); ?><span class="wlm-icons"><?php echo $sort_name_icon; ?></span></a>
						</th>
						<?php if ( !$content_comment ): ?>
							<th><?php _e( 'Status', 'wishlist-member' ); ?></th>
						<?php endif; ?>
						<?php if ( !$content_comment && $content_type != 'attachment' ): ?>
							<th style="width: 120px" class="text-center"><?php _e( 'Per Member Access', 'wishlist-member' ); ?></th>
							<th style="width: 100px" class="text-center"><?php _e( 'Post Members', 'wishlist-member' ); ?></th>
							<?php if ( $content_type != 'page' ): ?>
								<th class="text-center" style="width: 20%"><?php _e( 'Categories', 'wishlist-member' ); ?></th>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ( $content_type == 'attachment' || $content_type == 'page' ): ?>
							<th style="width: 120px;"><?php _e( 'Parent', 'wishlist-member' ); ?></th>
						<?php endif; ?>
						<th style="width: 100px" class="text-center"><a href="<?php echo add_query_arg( ['orderby' => 'post_date', 'order' => $sort_date], admin_url( 'admin.php' . $page_href ) ); ?>"><?php _e( 'Date', 'wishlist-member' ); ?><span class="wlm-icons"><?php echo $sort_date_icon; ?></span></a></th>
						<?php if ( $content_type != 'attachment' ): ?>
							<th style="width: 100px" class="text-center"></th>
						<?php endif; ?>
					</tr>
				</thead>
				<?php foreach ($content_items as $item ) : ?>
					<?php display_items($this, $item, $post_children, $content_type, $content_comment, $checkbox_check, $is_heirarchical ); ?>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>

<div class="pagination pull-right">
	<?php if ( $total_items && $total_items > $this->pagination_items[0] ) : ?>
			<?php if ( $perpage <= $total_items ) : ?>
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="text-muted pr-2">
							<div role="presentation" class="dropdown mt-9px">
								<a href="#" class="dropdown-toggle" id="drop-page" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
									<?php echo number_format($s_offset, 0, '.', ','); ?> - <?php echo ($perpage * $s_currentpage) > $total_items ? number_format($total_items, 0, '.', ',') : number_format( $perpage * $s_currentpage , 0, '.', ','); ?>
								</a> of <?php echo number_format($total_items, 0, '.', ','); ?>
								<ul class="dropdown-menu" id="menu1" aria-labelledby="drop-page">
									<?php foreach ( $this->pagination_items as $key => $value) : ?>
										<a class="dropdown-item" target="_parent" href="<?php echo $page_href ."&perpage=" .$value; ?>"><?php echo $value; ?></a>
									<?php endforeach; ?>
								</ul>
							</div>
						</span>
						<?php if ( $s_currentpage > 1 ) : ?>
							<a target="_parent" href="<?php echo $page_href ."&paged=1"; ?>" class="mt-6px"><i class="wlm-icons md-26">first_page</i></a>
						<?php else: ?>
							<a class="mt-6px text-muted disabled" disabled='disabled'><i class="wlm-icons md-26">first_page</i></a>
						<?php endif; ?>

						<?php
							if ( $s_currentpage <= 1 ) $previous_link = $page_href ."&paged=" .$total_pages;
							else $previous_link = $page_href ."&paged=" .($s_currentpage-1);
						?>
						<a target="_parent" href="<?php echo $previous_link; ?>" class="mt-6px"><i class="wlm-icons md-26">keyboard_arrow_left</i></a>
					</div>
					<input type="text" value="<?php echo $s_currentpage; ?>" class="form-control text-center pagination-pagenum" data-pages="<?php echo $total_pages; ?>" data-link="<?php echo $page_href ."&paged="; ?>" data-lpignore="true">
					<div class="input-group-append">
						<span class="mt-9px"> of <?php echo $total_pages; ?></span>
						<?php
							if ( $s_currentpage < $total_pages ) $next_link = $page_href ."&paged=" .($s_currentpage+1);
							else $next_link = $page_href ."&paged=1";
						?>
						<a target="_parent" href="<?php echo $next_link; ?>" class="mt-6px"><i class="wlm-icons md-26">keyboard_arrow_right</i></a>

						<?php if ( $s_currentpage < $total_pages ) : ?>
							<a target="_parent" href="<?php echo $page_href ."&paged=" .$total_pages; ?>" class="mt-6px"><i class="wlm-icons md-26">last_page</i></a>
						<?php else: ?>
							<a class="mt-6px text-muted disabled" disabled='disabled'><i class="wlm-icons md-26">last_page</i></a>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
					<div style="width: auto" class="input-group pull-right">
						<div class="input-group-prepend">
							<span class="text-muted pr-2">
								<div role="presentation" class="dropdown mt-9px">
									<a href="#" class="dropdown-toggle" id="drop-page" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
										<?php echo number_format($s_offset, 0, '.', ','); ?> - <?php echo ($perpage * $s_currentpage) > $total_items ? number_format($total_items, 0, '.', ',') : number_format( $perpage * $s_currentpage , 0, '.', ','); ?>
									</a> of <?php echo number_format($total_items, 0, '.', ','); ?>
									<ul class="dropdown-menu" id="menu1" aria-labelledby="drop-page">
										<?php foreach ( $this->pagination_items as $key => $value) : ?>
											<a class="dropdown-item" target="_parent" href="<?php echo $page_href ."&perpage=" .$value; ?>"><?php echo $value; ?></a>
										<?php endforeach; ?>
									</ul>
								</div>
							</span>
						</div>
					</div>
			<?php endif; ?>
	<?php endif; ?>
</div>

<!-- Modal -->
<div id="protection-modal" data-id="protection-modal" data-label="protection_modal_label" data-title="Edit Protection Status" data-classes="modal-sm" style="display:none">
	<div class="body">
		<div class="form-group">
			<label for=""><?php _e( 'Protection Status', 'wishlist-member' ); ?></label>
			<select class="form-control wlm-levels wlm-protection" name="protection" style="width: 100%" required>
				<option>Unprotected</option>
				<option>Protected</option>
				<option>Inherited</option>
			</select>
		</div>
		<?php if ( $content_comment ) : ?>
			<input type="hidden" name="content_comment" value="1" />
		<?php endif; ?>
		<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
		<input type="hidden" name="contentids" value="" />
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><i class="wlm-icons">lock</i>  <span>Update Protection</span></button>
	</div>
</div>

<div id="add-level-modal" data-id="add-level-modal" data-label="add_level_modal_label" data-title="Add Levels" data-classes="modal-sm" style="display:none">
	<div class="body">
		<div class="form-group membership-level-select">
			<label for="">Membership Levels</label>
			<select class="form-control wlm-levels" multiple="multiple" name="wlm_levels[]" id="" style="width: 100%" data-placeholder="Select Membership Levels" required>
				<?php foreach ( $wpm_levels as $key => $value ) : ?>
					<?php $disabled = isset($value[ $allprotection ]) && !empty($value[ $allprotection ]) ? "disabled='disabled'" : ""; ?>
					<option value="<?php echo $key; ?>" <?php echo $disabled; ?>><?php echo $value['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php if ( $content_comment ) : ?>
			<input type="hidden" name="content_comment" value="1" />
		<?php endif; ?>
		<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
		<input type="hidden" name="contentids" value="" />
		<input type="hidden" name="level_action" value="add" />
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><i class="wlm-icons">add_circle_outline</i> <span>Add Level</span></button>
	</div>
</div>

<div id="remove-level-modal" data-id="remove-level-modal" data-label="remove_level_modal_label" data-title="Remove Levels" data-classes="modal-sm" style="display:none">
	<div class="body">
		<div class="form-group">
			<label for="">Membership Levels</label>
			<select class="form-control wlm-levels" multiple="multiple" name="wlm_levels[]" id="" style="width: 100%" data-placeholder="Select Membership Levels" required>
				<?php foreach ( $wpm_levels as $key => $value ) : ?>
					<?php $disabled = isset($value[ $allprotection ]) && !empty($value[ $allprotection ]) ? "disabled='disabled'" : ""; ?>
					<option value="<?php echo $key; ?>" <?php echo $disabled; ?>><?php echo $value['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php if ( $content_comment ) : ?>
			<input type="hidden" name="content_comment" value="1" />
		<?php endif; ?>
		<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
		<input type="hidden" name="contentids" value="" />
		<input type="hidden" name="level_action" value="remove" />
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><i class="wlm-icons">remove_circle_outline</i> <span>Remove Level</span></button>
	</div>
</div>

<div id="ppp-modal" data-id="ppp-modal" data-label="ppp_modal_label" data-title="Edit Per Member Access" data-classes="modal-sm" style="display:none">
	<div class="body">
		<div class="form-group">
			<label for="">Access</label>
			<select class="form-control wlm-levels wlm-useraccess" name="useraccess" style="width: 100%" required>
				<option selected><?php _e( 'Disabled', 'wishlist-member' ); ?></option>
				<option><?php _e( 'Free', 'wishlist-member' ); ?></option>
				<option><?php _e( 'Paid', 'wishlist-member' ); ?></option>
			</select>
		</div>
		<?php if ( $content_comment ) : ?>
			<input type="hidden" name="content_comment" value="1" />
		<?php endif; ?>
		<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
		<input type="hidden" name="contentids" value="" />
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><i class="wlm-icons">person</i>  <span><?php _e( 'Update Member Access', 'wishlist-member' ); ?></span></button>
	</div>
</div>

<div id="edit-modal" data-id="edit-modal" data-label="edit_modal_label" data-title="Edit Content Protection" data-classes="modal-lg" style="display:none">
	<div class="body">
		<div class="edit-content">
		</div>
		<?php if ( $content_comment ) : ?>
			<input type="hidden" name="content_comment" value="1" />
		<?php endif; ?>
		<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
		<input type="hidden" name="contentids" value="" />
		<input type="hidden" name="checkbox_check" value="0" />
		<input type="hidden" name="level_action" value="set" />
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
		<!-- <button type="button" class="btn -primary save-button"><i class="wlm-icons">save</i>  <span>Save</span></button> -->
	</div>
</div>

<div id="ppp-user-modal" data-id="ppp-user-modal" data-label="ppp-user_modal_label" data-title="Add Member to Pay Per Post" data-classes="modal-md" style="display:none">
	<div class="body">
		<div class="form-group">
			<label for="">Select a Member</label>
			<select class="form-control wlm-payperpost-users" name="wlm_payperpost_users" style="width: 100%">
			</select>
		</div>
		<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
		<input type="hidden" name="contentids" value="" />
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
		<input type="hidden" name="operation" value="" />
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><i class="wlm-icons">add_circle_outline</i><span><?php _e( 'Add Member', 'wishlist-member' ); ?></span></button>
	</div>
</div>