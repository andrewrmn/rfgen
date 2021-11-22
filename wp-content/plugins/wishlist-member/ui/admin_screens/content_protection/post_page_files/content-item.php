<?php
	if ( !$content_comment ) {
		$content_lvls = $that->GetContentLevels( $item->post_type, $item->ID, true, false, $immutable );
	} else {
		$content_lvls = $that->GetContentLevels( '~COMMENT', $item->ID, true, false, $immutable );
	}

	$checkbox_check = isset( $checkbox_check ) ? $checkbox_check : false; //not used anymore, used to check the checkbox whenever something is change on the record.

	$protection_title = array(
		"lock" => "Protected",
		"lock_open" => "Unprotected",
		"inherit" => "Inherited",
		"inherit_unprotected" => "Inherited",
	);
	if ( !$content_comment ) {
		$protect_inherit = $that->SpecialContentLevel( $item->ID, 'Inherit' );
		$protection = $that->Protect( $item->ID ) ? "lock" : "lock_open";
	} else {
		$protect_inherit = $that->SpecialContentLevel( $item->ID, 'Inherit', null, '~COMMENT' );
		$protection = $that->SpecialContentLevel( $item->ID, 'Protection', null, '~COMMENT' ) ? "lock" : "lock_open";
	}
	if ( $protect_inherit ) {
		if ( $protection == "lock" ) $protection = "inherit";
		else $protection = "inherit_unprotected";
	}

	$post_categories = array();
	if ( !$content_comment ) {
		$cats = wp_get_post_categories( $item->ID );
		foreach ( $cats as $c ){
		    $cat = get_category( $c );
		    $post_categories[$c] = $cat->name;
		}
	}

	$taxonomy_names = get_taxonomies( array( '_builtin' => false ), 'names' );
	array_unshift( $taxonomy_names, 'category' );
	$taxonomies     = wp_get_object_terms( $item->ID, $taxonomy_names, array( 'fields' => 'ids' ) );
	$protected_taxonomies = array();
	if(!is_wp_error($taxonomies) AND !empty($taxonomies)) {
		foreach($taxonomies AS $taxonomy) {
			if( $that->CatProtected( $taxonomy ) ) {
				$protected_taxonomies[] = $taxonomy;
			}
		}
	}
	$ancestor = get_post_ancestors( $item->ID );


	$allprotection = $content_type == '~COMMENT' ? "allcomments" : "dummy";
	$allprotection = $content_type == 'post' ? "allposts" : $allprotection;
	$allprotection = $content_type == 'page' ? "allpages" : $allprotection;
?>
<tbody class="outer-tbody button-hover content-tbody-<?php echo $item->ID; ?>">
	<tr class="content-tr content-tr-<?php echo $item->ID; ?>">
		<td class="text-center">
			<div class="form-check -for-tables">
				<input value="<?php echo $item->ID; ?>" type="checkbox" class="chk-contentid form-check-input" title="<?php echo $item->ID; ?>">
				<label class="form-check-label d-none" for=""></label>
			</div>
		</td>
		<td>
			<a href="#" title="<?php echo $protection_title[$protection]; ?>" class="toggle-content-protection pull-left" content_type="<?php echo $content_type; ?>" contentids="<?php echo $item->ID; ?>" content_comment="<?php echo $content_comment ? '1' : '0'; ?>">
				<i class="wlm-icons md-24"><?php echo $protection; ?></i>
			</a>
			<?php echo $is_heirarchical ? str_repeat( '&mdash; ', count($ancestor) ) : ""; ?>
			<div class="d-inline-block" style="max-width: calc(100% - 25px);">
				<?php if ( $content_type == 'attachment' ): ?>
					<?php echo $item->post_title; ?><br>
					<em><?php echo basename( get_attached_file( $item->ID ) ); ?></em>
				<?php else: ?>
					<a href="#" data-contenttype="<?php echo $content_comment ? "comment" : $content_type; ?>"  data-contentid="<?php echo $item->ID; ?>" class="edit-btn">
						<?php echo $item->post_title; ?>
					</a>
				<?php endif; ?>
			</div>
		</td>
		<?php if ( !$content_comment ): ?>
		<td>
			<?php
				$protection_status = "";
				if ( $protect_inherit ) {
					$protection_status = $protection_title[$protection];
					if ( $protection == "inherit" ) {
						$protection_status .= " - Protected";
					} else {
						$protection_status .= " - Unprotected";
					}

					$prot_cat = array();
					if($protected_taxonomies) {
						foreach($protected_taxonomies AS $id) {
							$t = get_term( $id );
							$prot_cat[] = $t->name;
						}
					} else {
						foreach($ancestor AS $id) {
							$prot_cat[] = get_the_title( $id );
						}
					}
					$prot_cat = count( $prot_cat ) > 0 ? " from " .implode(", ", $prot_cat ) : "";
					$protection_status .= $prot_cat;
				}
				if ( !empty( $protection_status ) ) {
					echo "<span title='{$protection_status}'>{$protection_title[$protection]}</span>";
				} else {
					echo $protection_title[$protection];
				}
			?>
		</td>
		<?php endif; ?>
		<?php if ( !$content_comment && $content_type != 'attachment' ): ?>
			<td class="text-center">
				<?php
					$ppost_status = $that->PayPerPost(  $item->ID ) ? "Paid" : "Disabled";
					$ppost_status = $that->Free_PayPerPost( $item->ID ) ? "Free" : $ppost_status;
				?>
				<?php echo $ppost_status; ?>
			</td>
			<td class="text-center ppp-user-count-holder"><?php echo $that->count_post_users( $item->ID, $item->post_type ); ?></td>
			<?php if ( $content_type != 'page' ): ?>
				<td class="text-center">
					<?php echo implode(", ", $post_categories); ?>
				</td>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( $content_type == 'attachment' || $content_type == 'page' ): ?>
			<td>
				<?php
					// $p = get_post( $item->post_parent );
					// if ( $p ) echo $p->post_title;
					$content_parent = array();
					foreach($ancestor AS $id) {
						$content_parent[] = get_the_title( $id );
					}
					$content_parent = implode(" > ", $content_parent );
				?>
				<?php if( $content_parent ) : ?>
					<span class='wlm-content-parent d-block text-truncate' title='<?php echo $content_parent; ?>' style='cursor: default; max-width: 120px;'>
						<?php echo $content_parent; ?>
					</span>
				<?php endif; ?>
			</td>
		<?php endif; ?>
		<td class="text-center"><?php echo  date( get_option( 'date_format' ) ,strtotime( $item->post_date ) ) ?></td>
		<?php if ( $content_type != 'attachment' ): ?>
			<td class="text-center">
				<div class="btn-group-action">
					<a href="<?php echo get_permalink($item->ID); ?>" target="_blank" title="View Content" class="btn wlm-icons md-24 -icon-only"><span>remove_red_eye</span></a>
					<a href="#" data-contenttype="<?php echo $content_comment ? "comment" : $content_type; ?>"  data-contentid="<?php echo $item->ID; ?>" class="btn wlm-icons md-24 -icon-only edit-btn"><span><?php _e( 'edit', 'wishlist-member' ); ?></span></a>
				</div>
			</td>
		<?php endif; ?>
	</tr>
	<tr>
		<?php
			$colspan = 7;
			$colspan = $content_comment ? 3 : $colspan;
			$colspan = $content_type == 'attachment' ? 4 : $colspan;
		?>
		<td>&nbsp;</td>
		<td colspan="<?php echo $colspan; ?>">
			<?php
				$wpm_levels = $that->GetOption('wpm_levels');
				$content_mylvls = array();
				foreach ( $content_lvls as $key => $value ) {
					if ( !isset($wpm_levels[$key][$allprotection]) || empty($wpm_levels[$key][$allprotection]) ) {
						$content_mylvls[$key] = trim($value);
						unset($content_lvls[$key]);
					}
				}
				$levels = "";
				if ( count( $content_lvls ) ) {
					$ct = $content_comment ? "comment" : $content_type;
					$title = "This level has access to all {$ct}s";
					$levels = "<em class='text-muted' title='{$title}' style='cursor: default;'>" .implode("</em>, <em class='text-muted' title='{$title}' style='cursor: default;'>", $content_lvls ) ."</em>";
				}
				if ( count( $content_mylvls ) ) {
					$levels  = !empty( $levels ) ? $levels .", " : $levels;
					if ( $protection == "lock_open" ) {
						$levels .= "<span>" .implode("</span>, <span>", $content_mylvls ) ."</span>";
					} else {
						$levels .= implode(", ", $content_mylvls );
					}
				}
				$levels = empty( $levels ) ? "<span class='text-muted'>(No Membership Levels Assigned)</span>" : $levels;
			?>
			<div class="overflow-ellipsis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span class="wlm-icons text-muted -o-3" title="Membership Levels" style="cursor: default;">levels_icon</span>
				<?php $protection == "lock_open" || $protection == "inherit_unprotected" ? "text-decoration: line-through;" : ""; ?>
				<span style="vertical-align: middle;" >
					<?php echo $levels; ?>
				</span>
			</div>
		</td>
	</tr>
</tbody>