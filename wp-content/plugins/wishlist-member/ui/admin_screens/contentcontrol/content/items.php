<?php
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

	$ancestor = get_post_ancestors( $item->ID );

	$content_schedules = [];
	if ( $scheduler ) {
		$content_sched = $scheduler->GetContentSched($item->ID);
		foreach ( $content_sched as $sched ) {
			$content_schedules[] = ["type"=>"schedule","value"=>$sched];
		}
	}

	if ( $archiver ) {
		$content_sched = $archiver->GetPostExpiryDate($item->ID);
		foreach ( $content_sched as $sched ) {
			$content_schedules[] = ["type"=>"archive","value"=>$sched];
		}
	}

	if ( $manager ) {
		$sched_type = ["move","repost","set"];
		foreach ( $sched_type as $key => $t ) {
			$content_sched = $manager->GetPostManagerDate( $t ,$item->ID );
			foreach ( (array) $content_sched as $key => $value ) {
				$content_schedules[] = ["type"=>$t,"value"=>$value];
			}
		}
	}
?>

	<tr class="user-details-tr user-details-tr-<?php echo $item->ID; ?>">
		<td class="text-center">
			<div class="form-check -table-check-body">
				<input value="<?php echo $item->ID; ?>" type="checkbox" class="chk-contentid form-check-input" title="<?php echo $item->ID; ?>">
				<label class="form-check-label d-none" for=""></label>
			</div>
		</td>
		<td>
			<span title="<?php echo $protection_title[$protection]; ?>" class="pull-left text-muted">
				<i class="wlm-icons md-24"><?php echo $protection; ?></i>
			</span>
			<?php echo $is_heirarchical ? str_repeat( '&mdash; ', count($ancestor) ) : ""; ?>
			<div class="d-inline-block" style="max-width: calc(100% - 25px);">
				<?php echo $item->post_title; ?>
			</div>
		</td>
		<?php if ( $support_categories ): ?>
			<td class="text-center">
				<?php echo implode(", ", $post_categories); ?>
			</td>
		<?php endif; ?>
		<td class="text-center">
			<?php echo  date( get_option( 'date_format' ) ,strtotime( $item->post_date ) ) ?>
		</td>
		<td class="text-center">
			<div class="btn-group-action">
				<a href="<?php echo get_permalink($item->ID); ?>" target="_blank" title="View Content" class="btn wlm-icons md-24 -icon-only"><span>remove_red_eye</span></a>
			</div>
		</td>
	</tr>
	<?php if( count($content_schedules) > 0 ) : ?>
	<tr class="level-details-tr level-details-tr-<?php echo $item->ID; ?>">
		<td colspan="<?php echo $support_categories ? '5' : '4';  ?>" class="with-table">
			<table class="table -inner-table wlm-user-levels wlm-user-levels-<?php echo $item->ID; ?>">
				<!-- Toggle -->
				<thead class="py-0">
					<tr class="more-details -top-level">
						<th style="width: 93px;"></th>
						<th style="width: 20%;" class="pt-0">
							<a class="level-collapse py-0 <?php echo count($content_schedules) ? '' : 'd-none'; ?>" href="#" data-target=".collapse<?php echo $item->ID; ?>" data-userid="<?php echo $item->ID; ?>">
								<span class="collapse<?php echo $item->ID; ?> level-arrows -down wlm-icons <?php echo $icollapse1; ?>">arrow_drop_down</span>
								<span class="collapse<?php echo $item->ID; ?> level-arrows -right wlm-icons <?php echo $icollapse2; ?>">arrow_right</span>
								<span class="collapse<?php echo $item->ID; ?> levelheader text <?php echo $icollapse1; ?>"><?php _e('Scheduled', 'wishlist-member'); ?></span>
								<span class="collapse<?php echo $item->ID; ?> thelevels <?php echo $icollapse2; ?> text"><?php _e('Scheduled', 'wishlist-member'); ?></span>
							</a>
						</th>
						<th class="p-0">
						</th>
					</tr>
				</thead>
				<!-- Levels -->
				<tbody class="inner-tbody <?php echo $icollapse1; ?> collapse<?php echo $item->ID; ?>" data-userid="<?php echo $item->ID; ?>">
					<?php foreach ( $content_schedules as $sched): ?>
						<?php $v = $sched['value']; ?>
						<tr class="more-details wlm-user-level-details">
							<?php if ( $sched["type"] == "schedule" ):  ?>
								<td class="table-form-container text-center pr-sm-0 pl-sm-0 level-tools-sm">
									<div class="btn-group-action">
										<a href="#" title="Edit" operation="scheduler" postid="<?php echo $item->ID; ?>" schedid="<?php echo $v->id; ?>" levelid="<?php echo $v->mlevel; ?>" numdays="<?php echo $v->num_days; ?>" hidedays="<?php echo $v->hide_days; ?>" class="wlm-icons md-24 update-sched-btn d-lg-inline d-md-inline">edit</a>
										<a href="#" title="Remove" operation="remove_scheduler" postid="<?php echo $item->ID; ?>" levelid="<?php echo $v->mlevel; ?>" class="wlm-icons md-24 remove-sched-btn d-lg-inline d-md-inline">delete</a>
									</div>
								</td>
								<td style="padding-left: 9px">
									<span class='wlm-level-name' style="vertical-align: middle;">
										<?php echo $wpm_levels[$v->mlevel]['name']; ?>
									</span>
								</td>
								<td>
									<span class='wlm-level-show-after' style="vertical-align: middle;">
										<?php _e( 'Show content after', 'wishlist-member' ); ?> <strong><?php echo $v->num_days; ?></strong> <?php $v->num_days > 1 ? _e( 'days', 'wishlist-member' ) : _e( 'day', 'wishlist-member' ); ?>
										<?php if ( $v->hide_days > 0 ) : ?>
											<?php _e( ' for ', 'wishlist-member' ); ?> <strong><?php echo $v->hide_days; ?></strong> <?php $v->hide_days > 1 ? _e( 'days', 'wishlist-member' ) : _e( 'day', 'wishlist-member' ); ?>
										<?php endif; ?>
									</span>
								</td>
							<?php elseif( $sched["type"] == "archive" ):  ?>
								<td class="table-form-container text-center pr-sm-0 pl-sm-0 level-tools-sm">
									<div class="btn-group-action">
										<a href="#" title="Edit" operation="archiver" postid="<?php echo $item->ID; ?>" schedid="<?php echo $v->id; ?>" levelid="<?php echo $v->mlevel; ?>" scheddate="<?php echo date_i18n( "m/d/Y h:i a", strtotime($v->exp_date)); ?>" class="wlm-icons md-24 update-sched-btn d-lg-inline d-md-inline">edit</a>
										<a href="#" title="Remove" operation="remove_archiver" postid="<?php echo $item->ID; ?>" levelid="<?php echo $v->mlevel; ?>" class="wlm-icons md-24 remove-sched-btn d-lg-inline d-md-inline">delete</a>
									</div>
								</td>
								<td style="padding-left: 9px">
									<span class='wlm-level-name' style="vertical-align: middle;">
										<?php echo $wpm_levels[$v->mlevel]['name']; ?>
									</span>
								</td>
								<td>
									<span class='wlm-level-show-after' style="vertical-align: middle;">
										<?php _e( 'Archive content on ', 'wishlist-member' ); ?> <strong><?php echo $that->FormatDate( $v->exp_date, 0 ); ?></strong>
									</span>
								</td>
							<?php elseif( $sched["type"] == "move" || $sched["type"] == "repost" || $sched["type"] == "set" ):  ?>
								<td class="table-form-container text-center pr-sm-0 pl-sm-0 level-tools-sm">
									<div class="btn-group-action">
										<?php if ( $sched["type"] == "move" ) : ?>
											<a href="#" title="Edit" operation="manager" action="<?php echo $v->action; ?>" postid="<?php echo $item->ID; ?>" schedid="<?php echo $v->id; ?>" cat="<?php echo $v->categories; ?>" scheddate="<?php echo date_i18n( "m/d/Y h:i a", strtotime($v->due_date)); ?>" class="wlm-icons md-24 update-sched-btn d-lg-inline d-md-inline">edit</a>
										<?php endif; ?>
										<?php if ( $sched["type"] == "repost" ) : ?>
											<a href="#" title="Edit" operation="manager" action="<?php echo $sched["type"]; ?>" postid="<?php echo $item->ID; ?>" schedid="<?php echo $v->id; ?>" repnum="<?php echo $v->rep_num; ?>" repby="<?php echo $v->rep_by; ?>" repend="<?php echo $v->rep_end; ?>" scheddate="<?php echo date_i18n( "m/d/Y h:i a", strtotime($v->due_date)); ?>" class="wlm-icons md-24 update-sched-btn d-lg-inline d-md-inline">edit</a>
										<?php endif; ?>
										<?php if ( $sched["type"] == "set" ) : ?>
											<a href="#" title="Edit" operation="manager" action="<?php echo $sched["type"]; ?>" postid="<?php echo $item->ID; ?>" schedid="<?php echo $v->id; ?>" status="<?php echo $v->status; ?>" scheddate="<?php echo date_i18n( "m/d/Y h:i a", strtotime($v->due_date)); ?>" class="wlm-icons md-24 update-sched-btn d-lg-inline d-md-inline">edit</a>
										<?php endif; ?>
										<a href="#" title="Remove" operation="remove_manager" action="<?php echo $sched["type"]; ?>" postid="<?php echo $item->ID; ?>" schedid="<?php echo $v->id; ?>" scheddate="<?php echo date_i18n( "m/d/Y h:i a", strtotime($v->due_date)); ?>" class="wlm-icons md-24 remove-sched-btn d-lg-inline d-md-inline">delete</a>
									</div>
								</td>
								<td style="padding-left: 9px"> - </td>
								<td>
									<span class='wlm-level-name' style="vertical-align: middle;">
										<?php
											$str = "";
											switch ($sched["type"]) {
												case 'move':
													if ( $v->action == "move" ) $str = "Move to ";
													else $str = "Add to ";
	                                                $cat = explode('#',$v->categories);
	                                                $t = [];
	                                                foreach((array)$cat AS $cati=>$c) {
	                                                    $category = get_term_by('id', $c, 'category');
	                                                    $t[] = $category->name;
	                                                }
	                                                $str .= implode(",", $t);
	                                                $str .= " on <strong>" .$that->FormatDate( $v->due_date, 0 ) ."</strong>";
													break;
												case 'repost':
													$str = "Repost";
													$str .= " on <strong>" .$that->FormatDate( $v->due_date, 0 ) ."</strong>.";
	                                                if ( $v->rep_num > 0 ) {
	                                                    $every = $v->rep_num > 1 ? array('day'=>'Days','month'=>'Months','year'=>'Years') : array('day'=>'Day','month'=>'Month','year'=>'Year');
	                                                    $str .= ' Repeat every <strong>'.$v->rep_num .' ' .$every[$v->rep_by] ."</strong>.";
	                                                       $d1 = date_parse($v->due_date);
	                                                       if ( $v->rep_by == 'day' ) {
	                                                            $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$v->rep_num),$d1['year']);
	                                                       } else if ( $v->rep_by == 'month' ) {
	                                                            $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],($d1['month']+$v->rep_num),$d1['day'],$d1['year']);
	                                                       } else if ( $v->rep_by == 'year' ) {
	                                                           $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],($d1['year']+$v->rep_num));
	                                                       } else {
	                                                           $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$v->rep_num),$d1['year']);
	                                                       }

	                                                       if ( $v->rep_end > 0 ) {
	                                                            $str .= " Next due date is on <strong>" .$that->FormatDate( date('Y-m-d H:i:s',$new_bue_date), 0 ) ."</strong> (" .($v->rep_end -1) ." repetition" .(($v->rep_end -1) > 1 ? "s":"") ." left)";
	                                                       } else {
	                                                           $str.=' No repetition limit.';
	                                                       }
	                                                }
													break;
												case 'set':
													$stats = array('publish'=>'Published','pending'=>'Pending Review','draft'=>'Draft','trash'=>'Trash');
													$str = "Set content status to " .$stats[$v->status];
													$str .= " on <strong>" .$that->FormatDate( $v->due_date, 0 ) ."</strong>.";
													break;
											}
											echo $str;
										?>
									</span>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</td>
	</tr>
	<?php endif; ?>