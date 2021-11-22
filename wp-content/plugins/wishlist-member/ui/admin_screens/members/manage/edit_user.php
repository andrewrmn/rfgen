<?php
	global $wp_roles;
	$roles = $wp_roles->roles;

	$wpm_useraddress = $profileuser->wpm_useraddress;
	$wpm_useraddress = is_array( $wpm_useraddress ) ? $wpm_useraddress : array();

	$registered = date('F d, Y h:ia', $this->UserRegistered($profileuser));
	$registered = $this->FormatDate( $registered );
	$regip = $profileuser->wpm_registration_ip;

	//fix issue when no login record shows date in 1970/1969
	if (( (int) $profileuser->wpm_login_date + $this->GMT) > 0) {
		$lastlogin = gmdate("F d, Y h:ia", (int) $profileuser->wpm_login_date);
		$lastlogin = $this->FormatDate( $lastlogin );
	} else {
		$lastlogin = __("No login record yet.", 'wishlist-member');
	}

	$loginip = __("No login record yet.", 'wishlist-member');
	if ( !empty( $profileuser->wpm_login_ip ) && $profileuser->wpm_login_ip ) {
		$loginip = $profileuser->wpm_login_ip;
	}

	$today_loggedin_ips = (array) $profileuser->wpm_login_counter;
	$today = date('Ymd');
	foreach ((array) $today_loggedin_ips AS $k => $v) {
		if ($v != $today) unset($today_loggedin_ips[$k]);
	}


	$user_roles = array_keys( $profileuser->caps );

	$level_history = $wlUser->get_history( 'level' );
	$ppp_history = $wlUser->get_history( 'ppp' );
	$scheduled_level_actions = $this->get_user_scheduled_level_actions($profileuser->ID);

	$login_history = $wlUser->get_history( 'login' );
	$rss_history = $wlUser->get_history( 'rss' );
?>
<div role="tabpanel" class="tab-pane active" id="member-info">
	<input type="hidden" name="userid" value="<?php echo $profileuser->ID; ?>" />
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group">
	    		<label for=""><?php _e('First Name', 'wishlist-member'); ?></label>
				<input type="text" class="form-control" value="<?php echo $profileuser->first_name; ?>" name="first_name"  placeholder="<?php _e('First Name', 'wishlist-member'); ?>" />
	    	</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group">
	    		<label for=""><?php _e('Last Name', 'wishlist-member'); ?></label>
				<input type="text" class="form-control" value="<?php echo $profileuser->last_name; ?>" name="last_name"  placeholder="<?php _e('Last Name', 'wishlist-member'); ?>" />
	    	</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group">
	    		<label for=""><?php _e('Email', 'wishlist-member'); ?></label>
				<input type="text" class="form-control" value="<?php echo $profileuser->user_email; ?>" name="user_email"  placeholder="<?php _e('Email', 'wishlist-member'); ?>" required="true"/>
	    	</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group">
	    		<label for=""><?php _e('Email Broadcasts', 'wishlist-member'); ?></label>
				<?php $checked = $profileuser->wlm_unsubscribe ? "" : "checked='checked'"; ?>
				<div class="form-check -with-tooltip">
					<label for="wlm_unsubscribe" class="cb-container">
						<input class="form-check-input" type="checkbox" id="wlm_unsubscribe" name="wlm_unsubscribe" uncheck_value="1" value="0" <?php echo $checked; ?>/>
						<span class="marker checkmark"></span>
					  	<span class="text-content"><?php _e( 'Subscribed', 'wishlist-member' ); ?></span>
					</label>
				</div>
	    	</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group">
	    		<label for=""><?php _e('Display name publicly as', 'wishlist-member'); ?></label>
				<select style="width: 100%" name="display_name" class="form-control wlm-levels">
					<?php $selected = $profileuser->first_name == $profileuser->display_name ?  $selected = 'selected="selected"' : ''; ?>
		     		<option value="<?php echo $profileuser->first_name; ?>" <?php echo $selected; ?> ><?php echo $profileuser->first_name; ?></option>
		     		<?php $selected = $profileuser->user_login == $profileuser->display_name ?  $selected = 'selected="selected"' : ''; ?>
		     		<option value="<?php echo $profileuser->user_login; ?>" <?php echo $selected; ?> ><?php echo $profileuser->user_login; ?></option>
		     		<?php $selected = $profileuser->last_name == $profileuser->display_name ?  $selected = 'selected="selected"' : ''; ?>
		     		<option value="<?php echo $profileuser->last_name; ?>" <?php echo $selected; ?> ><?php echo $profileuser->last_name; ?></option>
		     		<?php $fl =  $profileuser->first_name ." " .$profileuser->last_name; ?>
		     		<?php $selected = $fl == $profileuser->display_name ?  $selected = 'selected="selected"' : ''; ?>
		     		<option value="<?php echo $fl; ?>" <?php echo $selected; ?> ><?php echo $fl; ?></option>
		     		<?php $lf =  $profileuser->last_name ." " .$profileuser->first_name; ?>
		     		<?php $selected = $lf == $profileuser->display_name ?  $selected = 'selected="selected"' : ''; ?>
		     		<option value="<?php echo $lf; ?>" <?php echo $selected; ?> ><?php echo $lf; ?></option>
				</select>
	    	</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group membership-level-select">
	    		<label for="">
	    			<?php _e('Role', 'wishlist-member'); ?>
	    			<?php $this->tooltip(__('The WordPress Role for each Level is set in the Levels section. Each new member will be assigned that set WordPress Role by default when they join the Level. <br> <br> The WordPress Role for an individual Member can be changed here on a per Member basis.', 'wishlist-member'), 'lg'); ?>
	    		</label>
				<select style="width: 100%" name="role" class="form-control wlm-levels" <?php echo $profileuser->ID == get_current_user_id() ? 'disabled' : ''; ?>>
			     	<?php foreach( $roles as $rk => $role ) : ?>
			     		<?php $selected = in_array( $rk, $user_roles ) ?  $selected = 'selected="selected"' : ''; ?>
			     		<option value="<?php echo $rk; ?>" <?php echo $selected; ?> ><?php echo $role['name']; ?></option>
			     	<?php endforeach; ?>
				</select>
	    	</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group">
	    		<label for=""><?php _e('Username', 'wishlist-member'); ?></label>
				<input type="text" class="form-control" value="<?php echo $profileuser->user_login; ?>" disabled="true" />
	    	</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group mb-3">
				<label for=""><?php _e('Password', 'wishlist-member'); ?></label>
		    	<div class="form-inline input-group -form-tight" style="display: none">
		    		<div class="form-control -pw-form-control">
		    			<input type="text" class="form-control password-field" value="" required="true" autocomplete="new-password" />
		    			<span class="form-control input-group-addon pass-status text-center"></span>
		    		</div>
		    		<div class="input-group-append">
		    			<button class="btn -default generate-password">
		    				<?php _e('Generate', 'wishlist-member'); ?>
		    			</button>
		    		</div>
		    		<div class="input-group-append">
						<button class="btn -bare hide-password-field">
							<?php _e('Cancel', 'wishlist-member'); ?>
						</button>
		    		</div>
		    	</div>
				<button type="button" class="btn btn-success form-control -condensed show-password-field" >
					<?php _e('Change Password', 'wishlist-member'); ?>
				</button>
				<br>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-6 text-center">
			<a href="#" class="btn -outline -no-icon -condensed resend-reset-link-btn btn-block" data-user-login="<?php echo $profileuser->user_login; ?>">
				<span class="text"><?php _e('Send Reset Password Link to Member', 'wishlist-member'); ?></span>
			</a>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-6 text-center">
			<?php
				$sessions = WP_Session_Tokens::get_instance($profileuser->ID);
				$sessions = $sessions->get_all();
			?>
			<?php if ( count( $sessions ) > 0 ) : ?>
				<a href="#" class="btn -outline -condensed -no-icon logout-everywhere-btn btn-block" data-user-id="<?php echo $profileuser->ID; ?>"><span class="text"><?php _e('Log Out Everywhere', 'wishlist-member'); ?></span></a>
			<?php else: ?>
				<a href="#" class="btn -outline -condensed -no-icon -disabled disabled" disabled="true" style="width: 80%;"><span class="text"><?php _e('Log Out Everywhere', 'wishlist-member'); ?></span></a>
			<?php  endif; ?>
		</div>
	</div>
</div>
<div role="tabpanel" class="tab-pane" id="member-address">
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
	    		<label for=""><?php _e( 'Company', 'wishlist-member' ); ?></label>
				<input type="text" class="form-control" value="<?php echo stripslashes($wpm_useraddress['company']); ?>" name="wpm_useraddress[company]"  placeholder="Company" />
	    	</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="form-group no-margin">
	    		<label for="">Street</label>
				<input type="text" class="form-control" value="<?php echo stripslashes($wpm_useraddress['address1']); ?>" name="wpm_useraddress[address1]"  placeholder="Street Address" />
	    	</div>
			<div class="form-group">
	    		<label class="d-md-none d-sm-none" for="">&nbsp;</label>
				<input type="text" class="form-control" value="<?php echo stripslashes($wpm_useraddress['address2']); ?>" name="wpm_useraddress[address2]"  placeholder="Street Address 2" />
	    	</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group">
	    		<label for="">City/Town</label>
				<input type="text" class="form-control" value="<?php echo stripslashes($wpm_useraddress['city']); ?>" name="wpm_useraddress[city]"  placeholder="City/Town" />
	    	</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'State/Province', 'wishlist-member' ); ?></label>
				<input type="text" class="form-control" value="<?php echo stripslashes($wpm_useraddress['state']); ?>" name="wpm_useraddress[state]"  placeholder="State/Province" />
	    	</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group mb-sm-1">
	    		<label for="">Country</label>
	    		<select class="form-control wlm-select-country" name="wpm_useraddress[country]" style="width: 100%;">
					<?php foreach ((array) $this->Countries() AS $country) : ?>
						<?php
							$country_val = $country == "Select Country" ? "" : $country;
							$selected = "";
							if ( isset( $profileuser->wpm_useraddress['country'] ) ) {
								$selected = $country_val == $profileuser->wpm_useraddress['country'] ? ' selected="selected" ' : '';
							}
						?>
						<option value="<?php echo $country_val; ?>" <?php echo $selected; ?>><?php echo $country; ?></option>
					<?php endforeach; ?>
	    		</select>
	    	</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="form-group mb-sm-1">
	    		<label for="">Zip/Postal Code</label>
				<input type="text" class="form-control" value="<?php echo stripslashes($wpm_useraddress['zip']); ?>" name="wpm_useraddress[zip]"  placeholder="Zip/Postal Code" />
	    	</div>
		</div>
	</div>
</div>
<div role="tabpanel" class="tab-pane" id="member-level">
	<div class="horizontal-tabs">
		<div class="row no-gutters">
			<div class="col-12 col-md-auto">
				<!-- Nav tabs -->
				<div class="horizontal-tabs-sidebar" style="min-width: 100px;">
					<ul class="nav nav-tabs -h-tabs flex-column" role="tablist">
							<li role="presentation" class="nav-item">
								<a href="#manage-edit-levels" class="nav-link pp-nav-link active" aria-controls="manage-edit-levels" role="tab" data-type="manage-edit-levels" data-title="Levels" data-toggle="tab">Levels</a>
							</li>
							<?php if ( count($scheduled_level_actions) > 0 ) : ?>
								<li role="presentation" class="nav-item">
									<a href="#manage-edit-scheduled" class="nav-link pp-nav-link" aria-controls="manage-edit-scheduled" role="tab" data-type="manage-edit-scheduled" data-title="Scheduled Actions" data-toggle="tab">Scheduled</a>
								</li>
							<?php endif; ?>
							<li role="presentation" class="nav-item">
								<a href="#manage-edit-history" class="nav-link pp-nav-link" aria-controls="manage-edit-history" role="tab" data-type="manage-edit-history" data-title="Levels History" data-toggle="tab">History</a>
							</li>
					</ul>
				</div>
			</div>
			<div class="col">
				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="manage-edit-levels">
							<div class="row">
								<div class="col-md-5 mr-0 pr-0">
									<div class="form-group mb-3">
										<select class="form-control wlm-levels add-wlm-levels" id="" style="width: 100%">
											<option value="">- Select a Level -</option>
											<?php foreach ( $wpm_levels as $key => $value ) : ?>
												<?php $disabled = in_array( $key, $mlevels ) ? "disabled='disabled'" : ""; ?>
												<option value="<?php echo $key; ?>" <?php echo $disabled; ?>><?php echo $value['name']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="ml-2 pl-2" style="width: 36%">
									<div class="form-group mb-3">
										<select class="form-control wlm-levels add-wlm-levels-email" id="" style="width: 100%">
											<option value="sendlevel">Use Level Notification Settings</option>
											<option value="send">Send Email Notification</option>
											<option value="dontsend">Do NOT Send Email Notification</option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group mb-3">
										<a href="#" class="btn -primary -condensed add-userlevel-btn" user-id="<?php echo $profileuser->ID; ?>">
											<i class="wlm-icons">add</i>
											<span class="text"><?php _e( 'Add Level', 'wishlist-member' ); ?></span>
										</a>
									</div>
								</div>
							</div>
							<div class="table-wrapper table-responsive -with-input">
								<table class="table table-striped table-condensed table-fixed">
									<thead>
								 		<tr class="d-flex">
								 			<th class="col-4"><?php _e('Level Name', 'wishlist-member'); ?></th>
								 			<th class="col-3">
								 				<?php _e('Transaction ID', 'wishlist-member'); ?>
											 	<?php $this->tooltip(__('A Transaction ID is assigned to a Member during account creation/registration. <br><br>The format of the Transaction ID will be based on the Integrated Shopping Cart. In many cases, the Transaction ID ties the member to a payment in the payment provider.<br><br>If no integration with a ShoppingCart is set up, the Transaction ID format will be the standard WishList Member format. Example: WL-0-0000000000', 'wishlist-member'), 'lg'); ?>
								 			</th>
								 			<th colspan="2" class="col-5"><?php _e('Registration Date', 'wishlist-member'); ?></th>
								 		</tr>
									</thead>
									<tbody class="user-level-holder" style="max-height: 300px">
										<?php $xlevels = preg_grep( '/^\d+$/', $mlevels ); ?>
										<?php if ( count( $xlevels ) > 0 ): ?>
									 		<?php foreach( $wpm_levels as $levelid => $level ): ?>
									 			<?php if ( in_array( $levelid, $xlevels ) ) : ?>
									 				<?php $level_cancelled = $this->LevelCancelled( $levelid, $profileuser->ID ); ?>
											 		<tr class="d-flex button-hover">
											 			<td class="col-4 pt-3">
											 				<span class="table-td">
											 					<span class="levelname-holder">
													 				<?php
													 					if ( !$level_cancelled ) echo $level['name'];
													 					else echo "<strike>{$level['name']}</strike>";
													 				?>
													 			</span>
												 				<?php
																	$lvl_parent = $this->LevelParent($levelid,$profileuser->ID);
																	$lvl_parent = $lvl_parent && isset($wpm_levels[$lvl_parent]) ? $wpm_levels[$lvl_parent]["name"] : "";
												 				?>
												 				<?php if ( $lvl_parent ) : ?>
												 					<i class="wlm-icons pull-right" title="Parent Level: <?php echo $lvl_parent; ?>">person</i>
												 				<?php endif; ?>
											 				</span>
											 			</td>
											 			<td class="col-3">
											 				<?php $txnid = $this->GetMembershipLevelsTxnID($profileuser->ID, $levelid); ?>
											 				<input type="text" class="form-control" value="<?php echo $txnid; ?>" name="txnid[<?php echo $levelid; ?>]"  placeholder="<?php _e('Transaction ID', 'wishlist-member'); ?>" />
											 			</td>
											 			<td class="col-3">
											 				<?php
											 					$reg_date = gmdate('m/d/Y h:i:s a', $this->UserLevelTimestamp($profileuser->ID, $levelid) + $this->GMT );
											 					$reg_date = $reg_date ? $reg_date : "";
											 				?>
											 				<input id="DateRangePicker" type="text" class="form-control wlm-datetimepicker" value="<?php echo $reg_date; ?>" name="lvltime[<?php echo $levelid; ?>]"  placeholder="<?php _e('Registration Date', 'wishlist-member'); ?>" />
											 			</td>
											 			<td class="col-2">
															<div class="btn-group-action pull-right pt-1">
																<a href="#" user-id="<?php echo $profileuser->ID; ?>" level-id="<?php echo $levelid; ?>" level-name="<?php echo $level['name']; ?>" class="btn cancel-level-btn -del-btn <?php if ( $level_cancelled ) echo 'd-none'; ?>" title="Cancel from Level"><span class="wlm-icons md-24 -icon-only">close</span></a>
																<a href="#" user-id="<?php echo $profileuser->ID; ?>" level-id="<?php echo $levelid; ?>" level-name="<?php echo $level['name']; ?>" class="btn uncancel-level-btn -del-btn <?php if ( !$level_cancelled ) echo 'd-none'; ?>" title="Uncancel from Level"><span class="wlm-icons md-24 -icon-only">replay</span></a>
																<a href="#" user-id="<?php echo $profileuser->ID; ?>" level-id="<?php echo $levelid; ?>" class="btn remove-level-btn -del-btn" title="Remove from Level"><span class="wlm-icons md-24 -icon-only">delete</span></a>
															</div>
											 			</td>
											 		</tr>
										 		<?php endif; ?>
									 		<?php endforeach; ?>
									 	<?php else: ?>
									 		<tr class="tr-none"><td class="text-center" colspan="4">No membership levels</td></tr>
									 	<?php endif; ?>
							 		</tbody>
							 	</table>
							</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="manage-edit-history">
							<div class="table-wrapper table-responsive -with-input">
								<table class="table table-striped table-condensed table-fixed">
									<thead>
								 		<tr class="d-flex">
								 			<th class="col-3"><?php _e('Action', 'wishlist-member'); ?></th>
								 			<th class="col-5"><?php _e('Levels', 'wishlist-member'); ?></th>
								 			<th class="col-4"><?php _e('Date', 'wishlist-member'); ?></th>
								 		</tr>
									</thead>
									<tbody class="user-level-holder" style="max-height: 350px">
										<?php if ( count( $level_history ) > 0 ): ?>
									 		<?php foreach( $level_history as $i => $history ): ?>
									 			<?php
									 				$meta_value = isset($history->log_value) ? wlm_maybe_unserialize($history->log_value) : [];
									 				$history_timestamp = "-";
									 				if ( isset( $history->date_added ) ) {
									 					$history_timestamp = $this->FormatDate( $history->date_added );
									 				}
									 			?>
										 		<tr class="d-flex">
										 			<td class="col-3"><?php echo isset($history->log_key) ? ucwords($history->log_key) : "-"; ?></td>
										 			<td class="col-5"><?php echo isset($meta_value["level_names"]) ? $meta_value["level_names"] : "-"; ?></td>
										 			<td class="col-4"><?php echo $history_timestamp; ?></td>
										 		</tr>
										 	<?php endforeach; ?>
									 	<?php else: ?>
									 		<tr class="tr-none"><td class="text-center" colspan="3">No membership level history</td></tr>
									 	<?php endif; ?>
							 		</tbody>
							 	</table>
							</div>
					</div>
					<?php if ( count($scheduled_level_actions) > 0 ) : ?>
						<div role="tabpanel" class="tab-pane" id="manage-edit-scheduled">
								 	<div class="table-wrapper table-responsive -with-input mt-2">
										<table class="table table-striped table-condensed table-fixed">
											<thead>
										 		<tr class="d-flex">
										 			<th class="col-8"><?php _e('Scheduled Actions', 'wishlist-member'); ?></th>
										 			<th class="col-4"><?php _e('Date', 'wishlist-member'); ?></th>
										 		</tr>
											</thead>
											<tbody style="max-height: 350px">
											 		<?php foreach( $scheduled_level_actions as $i => $action ): ?>
											 			<?php
															$meta_value = isset($action["meta_value"]) ? wlm_maybe_unserialize($action["meta_value"]) : [];
											 				$action_timestamp = "-";
											 				if ( isset( $meta_value["action_timestamp"] ) ) {
											 					$action_timestamp = date_i18n( get_option( 'date_format' ) ." " .get_option( 'time_format' ), $meta_value["action_timestamp"] );
											 				}
											 				$action_details = isset($meta_value["action_details"]) ? $meta_value["action_details"] : [];
											 				$methods = array("add"=>"Add to","move"=>"Move to","cancel"=>"Cancel from","remove"=>"Remove from");
											 				$action_method = isset($methods[$action_details["level_action_method"]]) ? $methods[$action_details["level_action_method"]] : " - ";

															$levels = $action_details["action_levels"];
															foreach ( $levels as $key => $lvl ) {
																if ( isset($wpm_levels[$lvl]["name"]) ) {
																	$levels[$key] = $wpm_levels[$lvl]["name"];
																} else {
																	unset($levels[$key]);
																}
															}
											 				$text = $action_method ." " .implode(", ", $levels);
											 			?>
												 		<tr class="d-flex button-hover">
												 			<td class="col-6"><?php echo $text; ?></td>
												 			<td class="col-4"><?php echo $action_timestamp; ?></td>
												 			<td class="col-2">
																<div class="btn-group-action pull-right">
																	<a href="#" user-id="<?php echo $profileuser->ID; ?>" meta-key="<?php echo $action['meta_key']; ?>" class="btn remove-usermeta-btn -del-btn" title="Remove Schedule"><span class="wlm-icons md-24 -icon-only">delete</span></a>
																</div>
												 			</td>
												 		</tr>
												 	<?php endforeach; ?>
									 		</tbody>
									 	</table>
									</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

</div>
<div role="tabpanel" class="tab-pane" id="pay-per-posts">
	<?php
		$args = array(
			// 'public'                => true,
		 //    'exclude_from_search'   => false,
		    '_builtin'              => false
		);
		$post_types = get_post_types($args,'objects');
		$enabled_types = (array) $this->GetOption('protected_custom_post_types');

		$ptypes = array("post"=>"Posts","page"=>"Pages");
		foreach ( $post_types as $key => $value ) {
			if ( in_array( $value->name, $enabled_types ) ) {
				$ptypes[$value->name] = $value->label;
			}
		}

		$ppp = $this->GetUser_PayPerPost($profileuser->ID,true);
		$user_posts = array("post"=>array(0),"page"=>array(0));
		foreach ( $ppp as $key => $value ) {
			$user_posts[$value->type][] = $value->content_id;
		}
	?>
	<div class="row ppp-add-form">
		<div class="col-lg-8 col-md-8 col-sm-7">
			<div class="form-group -ppp">
				<select class="form-control wlm-select wlm-payperposts" name="wlm_payperposts" style="width: 100%">
				</select>
			</div>
		</div>
		<div class="col-lg-4 col-md-8 col-sm-5">
			<div class="form-group">
				<a href="#" class="btn -primary -condensed add-ppp-btn" data-type="">
					<i class="wlm-icons">add</i>
					<span class="text"><?php _e( 'Add Pay Per Post', 'wishlist-member' ); ?></span>
				</a>
			</div>
		</div>
	</div>
	<div class="horizontal-tabs">
		<div class="row no-gutters">
			<div class="col-12 col-md-auto">
				<!-- Nav tabs -->
				<div class="horizontal-tabs-sidebar">
					<ul class="nav nav-tabs -h-tabs flex-column" role="tablist">
						<?php foreach ( $ptypes as $ptype => $title ) : ?>
							<li role="presentation" class="nav-item">
								<a href="#<?php echo $ptype; ?>" class="nav-link pp-nav-link <?php echo $ptype == 'post' ? 'active' : ''; ?>" aria-controls="<?php echo $ptype; ?>" role="tab" data-type="<?php echo $ptype; ?>" data-title="<?php echo $title; ?>" data-toggle="tab"><?php echo $title; ?></a>
							</li>
						<?php endforeach; ?>
						<li role="presentation" class="nav-item">
							<a href="#ppphistory" class="nav-link pp-nav-link" aria-controls="ppphistory" role="tab" data-type="ppphistory" data-title="Pay Per Post History" data-toggle="tab">Pay Per Post History</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col">
				<!-- Tab panes -->
				<div class="tab-content">
					<?php foreach ( $ptypes as $ptype => $title ) : ?>
						<?php
							$p = array();
							if ( isset( $user_posts[$ptype] ) && count( $user_posts[$ptype] ) ) {
								$p = new WP_Query( array( 'post_type'=>$ptype,'post__in'=>$user_posts[$ptype],'nopaging'=>true ) );
								$p = $p->posts;
							}
						?>
						<div role="tabpanel" class="tab-pane <?php echo $ptype == 'post' ? 'active' : ''; ?>" id="<?php echo $ptype; ?>">
							<!-- <div class="table-wrapper table-responsive" style="overflow: scroll; max-height: 247px;">
								<table class="table table-condensed table-striped"> -->
							<div class="table-wrapper table-responsive -with-input">
								<table class="table table-striped table-condensed table-fixed">
									<tbody class="<?php echo $ptype; ?>-holder" style="max-height: 300px">
										<?php if ( count($p) > 0 ) : ?>
									 		<?php foreach( $p as $post ): ?>
										 		<tr class="d-flex button-hover">
										 			<td class="col-10"><?php echo $post->post_title; ?></td>
										 			<td class="col-2">
														<div class="btn-group-action pull-right">
															<a href="#" post-id="<?php echo $post->ID; ?>" class="btn remove-ppp-btn -del-btn"><span class="wlm-icons md-18 -icon-only">delete</span></a>
														</div>
										 			</td>
										 		</tr>
									 		<?php endforeach; ?>
									 	<?php else: ?>
									 			<tr class="tr-none"><td class="text-center" colspan="2">- <?php _e( 'None Assigned -', 'wishlist-member' ); ?></td></tr>
								 		<?php endif; ?>
							 		</tbody>
							 	</table>
							</div>
						</div>
					<?php endforeach; ?>

						<div role="tabpanel" class="tab-pane ppphistory" id="ppphistory">
							<div class="table-wrapper table-responsive -with-input mt-2">
								<table class="table table-striped table-condensed table-fixed">
									<thead>
								 		<tr class="d-flex">
								 			<th class="col-2"><?php _e('Action', 'wishlist-member'); ?></th>
								 			<th class="col-6"><?php _e('Title', 'wishlist-member'); ?></th>
								 			<th class="col-4"><?php _e('Date', 'wishlist-member'); ?></th>
								 		</tr>
									</thead>
									<tbody class="user-level-holder" style="max-height: 350px">
										<?php if ( count( $ppp_history ) > 0 ): ?>
									 		<?php foreach( $ppp_history as $i => $history ): ?>
									 			<?php
									 				$meta_value = isset($history->log_value) ? wlm_maybe_unserialize($history->log_value) : [];
									 				$history_timestamp = "-";
									 				if ( isset( $history->date_added ) ) {
									 					$history_timestamp = $this->FormatDate( $history->date_added );
									 				}
									 				$pid = isset($meta_value["post"]) ? $meta_value["post"] : "0";
													$p = get_post( $pid );
													if ( $p ) {
														$title = "{$p->post_title} ({$p->post_type})";
													} else {
														$title = "Invalid Post ({$pid})";
													}
									 			?>
										 		<tr class="d-flex">
										 			<td class="col-2"><?php echo isset($history->log_key) ? ucwords($history->log_key) : "-"; ?></td>
										 			<td class="col-6"><?php echo $title; ?></td>
										 			<td class="col-4"><?php echo $history_timestamp; ?></td>
										 		</tr>
										 	<?php endforeach; ?>
									 	<?php else: ?>
									 		<tr class="tr-none"><td class="text-center" colspan="3">No pay per post history</td></tr>
									 	<?php endif; ?>
							 		</tbody>
							 	</table>
							</div>
						</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div role="tabpanel" class="tab-pane" id="member-advance">
	<?php
		$blacklist_email = $this->GetOption('blacklist_email');
		$blacklist_ip	 = $this->GetOption('blacklist_ip');
	?>
	<div class="row">
		<div class="col pr-0">
			 <div class="form-group -url-group">
				<label class="form-label" for=""><?php _e( 'WishList Member RSS Feed URL:', 'wishlist-member' ); ?></label>
				<input type="text" value="<?php echo $profileuser->wlm_feed_url; ?>" id="wlm_feed_url" class="form-control copyable" readonly="readonly" tooltip_size="md" data-lpignore="true">
			</div>
		</div>
		<div class="col-auto">
			<label>&nbsp;</label>
			<button type="button" class="btn btn-success form-control -condensed" id="reset-rss-feed">
				<?php _e( 'Change Feed URL', 'wishlist-member' ); ?>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			<label for="">Date Registered:</label>
			<span class="form-control" readonly><?php echo $registered; ?></span>
		</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6">
			<div class="form-group">
				<label for="">Last Login Date:</label>
				<span class="form-control" readonly><?php echo $lastlogin; ?></span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-7">
			<div class="row no-gutters">
				<div class="col-md-12">
					<div class="form-group">
						<label for="">Registered Email</label>
						<?php
							$t = strpos( $blacklist_email, $profileuser->user_email ) === false ? "Add to Blacklist" : "Remove from Blacklist";
							$i = strpos( $blacklist_email, $profileuser->user_email ) === false ? "add_circle_outline" : "remove_circle_outline";
						?>
						<a href="#" class="btn -condensed -bare -mini pull-right add-blacklist blacklist-email">
							<i class="wlm-icons md-18"><?php echo $i; ?></i>
							<small><?php echo $t; ?></small>
							<input type="hidden" name="blacklist_email" value="<?php echo $profileuser->user_email; ?>" />
							<?php if ( strpos( $blacklist_email, $profileuser->user_email ) === false ): ?>
								<input type="hidden" name="add_blacklist" value="1" />
							<?php endif; ?>
						</a>
						<span class="form-control email_address" title="<?php echo strpos( $blacklist_email, $profileuser->user_email ) !== false ? 'Blacklisted' : ''; ?>" readonly>
							<?php
								$mail_parts = explode("@", $profileuser->user_email );
								if ( strlen( $mail_parts[0] ) >= 15 || strlen( $mail_parts[1] ) >= 20 ) {
									if ( strlen( $mail_parts[0] ) >= 15 ) {
										$mail_local = str_split($mail_parts[0],15);
										$mail_local = $mail_local[0] ."...";
									} else {
										$mail_local = $mail_parts[0];
									}
									if ( strlen( $mail_parts[1] ) >= 20 ) {
										$mail_domain = str_split($mail_parts[1],12);
										$mail_domain = $mail_domain[0] ."...com";
									} else {
										$mail_domain = $mail_parts[1];
									}
									echo "<span title='{$profileuser->user_email}' style='cursor: default;'>";
										echo $mail_local ."@" .$mail_domain;
									echo "</span>";
								} else {
									echo $profileuser->user_email;
								}
							?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6">
			<div class="row no-gutters">
				<div class="col-md-12">
					<div class="form-group">
						<label for="">Registered IP</label>
						<?php if ( !empty($regip) && $regip != "127.0.0.1"  ): ?>
							<?php
								$t = strpos( $blacklist_ip, $regip ) === false ? "Add to Blacklist" : "Remove from Blacklist";
								$i = strpos( $blacklist_ip, $regip ) === false ? "add_circle_outline" : "remove_circle_outline";
							?>
							<a href="#" class="btn -condensed -bare -mini pull-right add-blacklist blacklist-ip">
								<i class="wlm-icons md-18"><?php echo $i; ?></i>
								<small><?php echo $t; ?></small>
								<input type="hidden" name="blacklist_ip" value="<?php echo $regip; ?>" />
								<?php if ( strpos( $blacklist_ip, $regip ) === false ): ?>
									<input type="hidden" name="add_blacklist" value="1" />
								<?php endif; ?>
							</a>
						<?php endif; ?>
						<span class="form-control ip_address" title="<?php echo strpos( $blacklist_ip, $regip ) !== false ? 'Blacklisted' : ''; ?>" readonly>
						 	<?php echo $regip; ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-5">
			<div class="row no-gutters">
				<div class="col-md-12">
					<div class="form-group">
						<label for="">Last Login IP</label>
						<?php if ( !empty($loginip) && $loginip != "127.0.0.1" && $loginip != "No login record yet."  ): ?>
							<?php
								$t = strpos( $blacklist_ip, $loginip ) === false ? "Add to Blacklist" : "Remove from Blacklist";
								$i = strpos( $blacklist_ip, $loginip ) === false ? "add_circle_outline" : "remove_circle_outline";
							?>
							<a href="#" class="btn -condensed -bare -mini pull-right add-blacklist blacklist-ip">
								<i class="wlm-icons md-18"><?php echo $i; ?></i>
								<small><?php echo $t; ?></small>
								<input type="hidden" name="blacklist_ip" value="<?php echo $loginip; ?>" />
								<?php if ( strpos( $blacklist_ip, $loginip ) === false ): ?>
									<input type="hidden" name="add_blacklist" value="1" />
								<?php endif; ?>
							</a>
						<?php endif; ?>
						<span class="form-control ip_address" title="<?php echo strpos( $blacklist_ip, $loginip ) !== false ? 'Blacklisted' : ''; ?>" readonly>
						 	<?php echo $loginip; ?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-5 col-sm-5">
			<div class="form-group">
				<label for="">Login Limit:</label>
				<div class="input-group">
				 	<input type="number" class="form-control" min="-1" value="<?php echo $profileuser->wpm_login_limit; ?>" name="wpm_login_limit" />
				 	<div class="input-group-append">
				 		<div class="input-group-text"><?php _e( 'IPs per day', 'wishlist-member' ); ?></div>
						<?php $this->tooltip(__('The default Login Limit for all Members is set in the Advanced Options > Logins section.<br><br> The Login Limit for an individual Member can be changed here on a per Member basis. <br><br> This setting controls the total number of IPs a Member can login with each day.<br><br>Note: Special Values can be used.<br><br>0 or Blank: Use the Default Login Limit set in Advanced Options > Logins section for the Member.<br><br>-1: No Login Limit for the Member.', 'wishlist-member'), 'lg'); ?>
				 	</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ( count( $today_loggedin_ips ) > 0 ) : ?>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="row no-gutters">
					<div class="col-md-12">
						<div class="form-group">
							<label for="">IPs Logged In Today</label>
							<a href="#" class="btn -condensed -bare -mini pull-right reset-limit-counter" user-id="<?php echo $profileuser->ID; ?>">
								<i class="wlm-icons md-18">refresh</i>
								<small>Reset IP Limit Counter</small>
							</a>
							<span class="form-control" readonly>
							 	<?php echo implode(', ', array_keys((array) $today_loggedin_ips)); ?>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
<div role="tabpanel" class="tab-pane" id="data-privacy">
	<?php if ( $this->GetOption( 'privacy_require_tos_on_registration' ) ) : ?>
		<div class="row">
				<div class="col-md-6">
					<div class="switch">
						<div class="switch-left">
							<div class="form-group">
								<label class="switch-light switch-wlm" onclick="">
									<?php $checked = $this->Get_UserMeta( $profileuser->ID, 'wlm_tos_accepted' ) ? 'checked="checked"' : ''; ?>
									<input type="checkbox" id="wlm_tos_accepted-id1535116617944" value="1" class="notification-switch form-check-input is-toggle-switch" name="wlm_tos_accepted" uncheck_value="0" <?php echo $checked; ?> data-lpignore="true">
									<span>
										<span><i class="wlm-icons md-18 ico-check">check</i></span>
										<span><i class="wlm-icons md-18 ico-close">close</i></span>
										<a></a>
									</span>
								</label>
							</div>
						</div>
						<div class="switch-body">
							<label for="wlm_tos_accepted-id1535116617944">
								<span class="title-label"><?php _e( 'Terms of Service Accepted', 'wishlist-member' ); ?></span>
							</label>
						</div>
					</div>
					<input type="hidden" name="userid" value="<?php echo $profileuser->ID; ?>" data-lpignore="true">
					<input type="hidden" name="action" value="admin_actions" data-lpignore="true">
					<input type="hidden" name="WishListMemberAction" value="save_user_meta" data-lpignore="true">
				</div>
		</div>
	<?php endif; ?>
	<?php if( $this->GetOption( 'privacy_enable_consent_to_market' ) ) : ?>
		<div class="row">
				<div class="col-md-6">
					<div class="switch">
						<div class="switch-left">
							<div class="form-group">
								<label class="switch-light switch-wlm" onclick="">
									<?php $checked = $this->Get_UserMeta( $profileuser->ID, 'wlm_consent_to_market' ) ? 'checked="checked"' : ''; ?>
									<input type="checkbox" id="wlm_consent_to_market-id1535116617944" value="1" class="notification-switch form-check-input is-toggle-switch" name="wlm_consent_to_market" uncheck_value="0" <?php echo $checked; ?> data-lpignore="true">
									<span>
										<span><i class="wlm-icons md-18 ico-check">check</i></span>
										<span><i class="wlm-icons md-18 ico-close">close</i></span>
										<a></a>
									</span>
								</label>
							</div>
						</div>
						<div class="switch-body">
							<label for="wlm_consent_to_market-id1535116617944">
								<span class="title-label"><?php _e( 'Consent for Additional Marketing', 'wishlist-member' ); ?></span>
							</label>
						</div>
					</div>
					<input type="hidden" name="userid" value="<?php echo $profileuser->ID; ?>" data-lpignore="true">
					<input type="hidden" name="action" value="admin_actions" data-lpignore="true">
					<input type="hidden" name="WishListMemberAction" value="save_user_meta" data-lpignore="true">
				</div>
		</div>
	<?php endif; ?>
</div>
<div role="tabpanel" class="tab-pane" id="member-other">
	<p><?php _e( 'Any information collected in Custom Registration Forms will be displayed below.', 'wishlist-member' ); ?></p>
	<?php
		$custom_fields = $this->get_user_custom_fields( $profileuser->ID );
		$custom_fields = is_array($custom_fields) ? $custom_fields : [];
	?>
	<?php $cnt = 1; ?>
	<?php foreach ( $custom_fields as $key => $value ) : ?>
		<?php if( $cnt == 1 ) echo '<div class="row">'; ?>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<?php //var_dump( $value ); ?>
				<?php if ( $value['type'] == "radio" ): ?>
					<div class="form-group">
			    		<label for=""><?php echo $value['label']; ?></label>
			    		<?php $chk_cnt = 1; ?>
						<?php foreach ( $value['options'] as $key => $v ) : ?>
							<?php if( $chk_cnt == 1 ) echo '<div class="radio row mb-2">'; ?>
							<?php if ( !trim($v["text"]) ) continue; ?>
							<div class="col-md-auto col-sm-auto col-xs-auto">
			    				<?php $checked = $v['checked'] ?  $selected = 'checked="checked"' : ''; ?>
								<div class="form-check -with-tooltip">
									<label class="cb-container">
										<input type="radio" value="<?php echo $v['value']; ?>" name="customfields[<?php echo $value['attributes']['name']; ?>]" <?php echo $checked; ?> />
										<span class="marker btn-radio"></span>
									  	<span class="text-content"><?php echo $v["text"]; ?></span>
									</label>
								</div>
							</div>
							<?php if( $chk_cnt == 2 ) { echo '</div>'; $chk_cnt = 1; } else { $chk_cnt++; } ?>
						<?php endforeach; ?>
						<?php if( $chk_cnt % 2 == 0 ) { echo '</div>'; } ?>
			    	</div>
				<?php elseif ( $value['type'] == "checkbox" ): ?>
					<div class="form-group">
			    		<label for=""><?php echo $value['label']; ?></label>
			    		<?php $chk_cnt = 1; ?>
						<?php foreach ( $value['options'] as $key => $v ) : ?>
							<?php if( $chk_cnt == 1 ) echo '<div class="row mb-2">'; ?>
							<?php if ( !trim($v["text"]) ) continue; ?>
							<div class="col-md-6 col-sm-6 col-xs-6">
			    				<?php $checked = $v['checked'] ?  $selected = 'checked="checked"' : ''; ?>
								<div class="form-check -with-tooltip">
									<label class="cb-container">
										<input name="customfields[<?php echo $value['attributes']['name']; ?>][]" value="<?php echo $v['value']; ?>" type="checkbox" <?php echo $checked; ?> />
										<span class="marker checkmark"></span>
									  	<span class="text-content"><?php echo $v["text"]; ?></span>
									</label>
								</div>
							</div>
							<?php if( $chk_cnt == 2 ) { echo '</div>'; $chk_cnt = 1; } else { $chk_cnt++; } ?>
						<?php endforeach; ?>
						<?php if( $chk_cnt % 2 == 0 ) { echo '</div>'; } ?>
					</div>
				<?php elseif ( $value['type'] == "textarea" ): ?>
					<div class="form-group">
			    		<label for=""><?php echo $value['label']; ?></label>
			    		<textarea class="form-control" name="customfields[<?php echo $value['attributes']['name']; ?>]"><?php echo stripslashes($value['attributes']['value']); ?></textarea>
			    	</div>
				<?php elseif ( $value['type'] == "select" ): ?>
					<div class="form-group">
			    		<label for=""><?php echo $value['label']; ?></label>
			    		<select name="customfields[<?php echo $value['attributes']['name']; ?>]" class="form-control wlm-levels" style="width: 100%;">
				    		<?php foreach ( $value['options'] as $key => $v ) : ?>
				    				<?php $selected = $v['selected'] ?  $selected = 'selected="selected"' : ''; ?>
						     		<option value="<?php echo $v['value']; ?>" <?php echo $selected; ?> ><?php echo $v['text']; ?></option>
							<?php endforeach; ?>
			    		</select>
			    	</div>
				<?php else: ?>
					<div class="form-group">
			    		<label for=""><?php echo $value['label']; ?><?php echo $value['type'] == "hidden" ? " (Hidden Field)" : ""; ?></label>
						<input type="text" class="form-control" value="<?php echo stripslashes($value['attributes']['value']); ?>" name="customfields[<?php echo $value['attributes']['name']; ?>]" />
			    	</div>
			    <?php endif; ?>
			</div>
		<?php if( $cnt == 2 ) { echo '</div>'; $cnt = 1; } else { $cnt++; } ?>
	<?php endforeach; ?>
	<?php if( $cnt % 2 == 0 ) { echo '</div>'; } ?>
</div>
<div role="tabpanel" class="tab-pane" id="member-history">
	<div class="horizontal-tabs">
		<div class="row no-gutters">
			<div class="col-12 col-md-auto">
				<!-- Nav tabs -->
				<div class="horizontal-tabs-sidebar">
					<ul class="nav nav-tabs -h-tabs flex-column" role="tablist">
						<li role="presentation" class="nav-item">
							<a href="#loginhistory" class="nav-link active" aria-controls="loginhistory" role="tab" data-type="loginhistory" data-title="Login History" data-toggle="tab">Login</a>
						</li>
						<li role="presentation" class="nav-item">
							<a href="#rsshistory" class="nav-link" aria-controls="rsshistory" role="tab" data-type="rsshistory" data-title="RSS Feeds History" data-toggle="tab">RSS</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col">
				<!-- Tab panes -->
				<div class="tab-content">
						<div role="tabpanel" class="tab-pane active loginhistory" id="loginhistory">
							<div class="table-wrapper table-responsive -with-input mt-2">
								<table class="table table-striped table-condensed table-fixed">
									<thead>
								 		<tr class="d-flex">
								 			<th class="col-2"><?php _e('Status', 'wishlist-member'); ?></th>
								 			<th class="col-6"><?php _e('IP', 'wishlist-member'); ?></th>
								 			<th class="col-4"><?php _e('Date', 'wishlist-member'); ?></th>
								 		</tr>
									</thead>
									<tbody class="user-level-holder" style="max-height: 400px">
										<?php if ( count( $login_history ) > 0 ): ?>
									 		<?php foreach( $login_history as $i => $history ): ?>
									 			<?php
									 				$meta_value = isset($history->log_value) ? wlm_maybe_unserialize($history->log_value) : [];
									 				$history_timestamp = "-";
									 				if ( isset( $history->date_added ) ) {
									 					$history_timestamp = $this->FormatDate( $history->date_added );
									 				}
									 			?>
										 		<tr class="d-flex">
										 			<td class="col-2"><?php echo isset($history->log_key) ? ucwords($history->log_key) : "-"; ?></td>
										 			<td class="col-6"><?php echo isset($meta_value['ip']) && !empty($meta_value['ip']) ? $meta_value['ip'] : "-"; ?></td>
										 			<td class="col-4"><?php echo $history_timestamp; ?></td>
										 		</tr>
										 	<?php endforeach; ?>
									 	<?php else: ?>
									 		<tr class="tr-none"><td class="text-center" colspan="3">No login history</td></tr>
									 	<?php endif; ?>
							 		</tbody>
							 	</table>
							</div>
						</div>

						<div role="tabpanel" class="tab-pane rsshistory" id="rsshistory">
							<div class="table-wrapper table-responsive -with-input mt-2">
								<table class="table table-striped table-condensed table-fixed">
									<thead>
								 		<tr class="d-flex">
								 			<th class="col-2"><?php _e('Status', 'wishlist-member'); ?></th>
								 			<th class="col-6"><?php _e('IP', 'wishlist-member'); ?></th>
								 			<th class="col-4"><?php _e('Date', 'wishlist-member'); ?></th>
								 		</tr>
									</thead>
									<tbody class="user-level-holder" style="max-height: 400px">
										<?php if ( count( $rss_history ) > 0 ): ?>
									 		<?php foreach( $rss_history as $i => $history ): ?>
									 			<?php
									 				$meta_value = isset($history->log_value) ? wlm_maybe_unserialize($history->log_value) : [];
									 				$history_timestamp = "-";
									 				if ( isset( $history->date_added ) ) {
									 					$history_timestamp = $this->FormatDate( $history->date_added );
									 				}
									 			?>
										 		<tr class="d-flex">
										 			<td class="col-2"><?php echo isset($history->log_key) ? ucwords($history->log_key) : "-"; ?></td>
										 			<td class="col-6"><?php echo isset($meta_value['ip']) && !empty($meta_value['ip']) ? $meta_value['ip'] : "-"; ?></td>
										 			<td class="col-4"><?php echo $history_timestamp; ?></td>
										 		</tr>
										 	<?php endforeach; ?>
									 	<?php else: ?>
									 		<tr class="tr-none"><td class="text-center" colspan="3">No login history</td></tr>
									 	<?php endif; ?>
							 		</tbody>
							 	</table>
							</div>
						</div>
				</div>
			</div>
		</div>
	</div>
</div>