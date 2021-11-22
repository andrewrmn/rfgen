<?php
/**
 * Handler for BuddyBoss integration
 * Author: Fel Jun Palawan <feljun@wishlistproducts.com>
 */

if ( ! class_exists( 'WLM_OTHER_INTEGRATION_BUDDYBOSS' ) ) {
	class WLM_OTHER_INTEGRATION_BUDDYBOSS {
		private $settings = [];
		public  $plugin_active = false;

		function __construct() {
			global $WishListMemberInstance;

			$data = $WishListMemberInstance->GetOption('buddyboss_settings');
			$this->settings = is_array($data) ? $data : [];
			$this->settings['group_default'] = $WishListMemberInstance->GetOption('wlm_bb_group_default');
			$this->settings['ptype_default'] = $WishListMemberInstance->GetOption('wlm_bb_ptype_default');

			//check if BuddyBoss is active
			$active_plugins  = wlm_get_active_plugins();
			if ( in_array( 'BuddyBoss Platform', $active_plugins ) || isset($active_plugins['buddyboss-platform/bp-loader.php']) || is_plugin_active('buddyboss-platform/bp-loader.php') ) {
				$this->plugin_active = true;
			}

			$this->load_hooks();
		}

	    function load_hooks() {
			if ( $this->plugin_active ) {
				add_action( 'wishlistmember_user_registered', array( $this, 'NewUserTagsHook' ), 99, 2 );
				add_action( 'wishlistmember_add_user_levels', array( $this, 'AddUserTagsHook' ),10,3);

				add_action('wishlistmember_confirm_user_levels', array($this, 'ConfirmApproveLevelsTagsHook'),99,2);
				add_action('wishlistmember_approve_user_levels', array($this, 'ConfirmApproveLevelsTagsHook'),99,2);

				add_action( 'wishlistmember_pre_remove_user_levels', array( $this, 'RemoveUserTagsHook' ), 99, 2 );
				add_action( 'wishlistmember_cancel_user_levels', array( $this, 'CancelUserTagsHook' ), 99, 2 );
				add_action( 'wishlistmember_uncancel_user_levels', array( $this, 'ReregUserTagsHook' ), 99, 2 );

				add_action( 'groups_leave_group', array( $this, 'GroupRemovedHook' ), 99, 2 );
				add_action( 'groups_accept_invite', array( $this, 'GroupAddedHook' ), 99, 3 );

				add_action( 'bp_set_member_type', array( $this, 'TypeAddedHook' ), 99, 3 );
				add_action( 'bp_remove_member_type', array( $this, 'TypeRemovedHook' ), 99, 2 );

				if ( isset($this->settings['group_default']) && $this->settings['group_default'] ) {
					add_action( 'groups_group_after_save',  array( $this, 'SaveGroupDefaultActions' ), 1, 1 );
				}

				if ( isset($this->settings['ptype_default']) && $this->settings['ptype_default'] && bp_member_type_enable_disable() ) {
					add_action( 'save_post_bp-member-type',  array( $this, 'SavePTypeDefaultActions' ), 10, 3 );
				}
			}
	    }

	    function SaveGroupDefaultActions( $obj_group ) {
	    	global $WishListMemberInstance, $wpdb;
	    	// the hook groups_group_after_save has no way of determining if we are saving new group or updating an existing one
	    	// so we check the last insert id
	    	if ( $obj_group->id != $wpdb->insert_id ) return;
	    	$post_id = $obj_group->id;

	    	$data = $WishListMemberInstance->GetOption('buddyboss_settings');
	    	if ( !isset($data['group']['default']) ) return;
	    	$data['group'][$post_id] = $data['group']['default'];
	    	$WishListMemberInstance->SaveOption('buddyboss_settings', $data);
	    }

	    function SavePTypeDefaultActions( $post_id, $post, $update ) {
	    	global $WishListMemberInstance;
	    	//if revision or update, disregard
	    	if ( $update || wp_is_post_revision( $post_id ) ) return;

	    	$data = $WishListMemberInstance->GetOption('buddyboss_settings');
	    	if ( !isset($data['ptype']['default']) ) return;
	    	// we use post id here, we will convert it later to post name
	    	// we convert it for  backward compatibility since
	    	// we use post name when we first release the integration
	    	$data['type'][$post_id] = $data['ptype']['default'];
	    	$WishListMemberInstance->SaveOption('buddyboss_settings', $data);
	    }

	    function TypeAddedHook( $user_id, $member_type, $append ) {
	    	global $WishListMemberInstance;
	    	$action = "add";
			$settings = isset($this->settings['type'][$member_type][$action]) ? $this->settings['type'][$member_type][$action] : [];
			$this->DoCourseHook( $user_id, $member_type, $action, $settings, true );
	    }

	    function TypeRemovedHook( $user_id, $member_type ) {
	    	global $WishListMemberInstance;
	    	$action = "remove";
			$settings = isset($this->settings['type'][$member_type][$action]) ? $this->settings['type'][$member_type][$action] : [];
			$this->DoCourseHook( $user_id, $member_type, $action, $settings, true );
	    }

	    function GroupAddedHook( $user_id, $group_id, $inviter_id ) {
	    	global $WishListMemberInstance;
	    	$action = "add";
			$settings = isset($this->settings['group'][$group_id][$action]) ? $this->settings['group'][$group_id][$action] : [];
			$this->DoCourseHook( $user_id, $group_id, $action, $settings, false );
	    }

	    function GroupRemovedHook( $group_id, $user_id ) {
	    	global $WishListMemberInstance;
	    	$action = "remove";
			$settings = isset($this->settings['group'][$group_id][$action]) ? $this->settings['group'][$group_id][$action] : [];
			$this->DoCourseHook( $user_id, $group_id, $action, $settings, false );
	    }

	    private function DoCourseHook( $wpuser, $hook_id, $action, $settings, $is_type = true ) {
	    	global $WishListMemberInstance;

			$added_levels = isset($settings['add_level']) ? $settings['add_level'] : [];
			$cancelled_levels = isset($settings['cancel_level']) ? $settings['cancel_level'] : [];
			$removed_levels = isset($settings['remove_level']) ? $settings['remove_level'] : [];

			$current_user_mlevels = $WishListMemberInstance->GetMembershipLevels( $wpuser );
			$wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');

			$prefix = $is_type ? "T" : "G";

			$action = strtoupper(substr( $action, 0, 1));
			$txnid = "BBOSS-{$action}-{$prefix}{$hook_id}-";
			//add to level
			if ( count( $added_levels ) > 0 ) {
				$user_mlevels = $current_user_mlevels;
				$add_level_arr = $added_levels;
				foreach ( $add_level_arr as $id => $add_level ) {
					if ( !isset( $wpm_levels[ $add_level ] ) ) continue;//check if valid level
					if( ! in_array( $add_level, $user_mlevels ) ) {
						$user_mlevels[] = $add_level;
						$new_levels[] = $add_level; //record the new level
						$WishListMemberInstance->SetMembershipLevels( $wpuser, $user_mlevels );
						$WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $add_level, "{$txnid}".time() );//update txnid
					}else{
						//For cancelled members
						$cancelled      = $WishListMemberInstance->LevelCancelled( $add_level, $wpuser );
						$resetcancelled = true; //lets make sure that old versions without this settings still works
						if ( isset( $wpm_levels[ $add_level ]['uncancelonregistration'] ) ) {
							$resetcancelled = $wpm_levels[ $add_level ]['uncancelonregistration'] == 1;
						}
						if ( $cancelled && $resetcancelled ) {
							$ret = $WishListMemberInstance->LevelCancelled( $add_level, $wpuser, false );
							$WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $add_level, "{$txnid}".time() );//update txnid
						}

						//For Expired Members
						$expired      = $WishListMemberInstance->LevelExpired( $add_level, $wpuser );
						$resetexpired = $wpm_levels[ $add_level ]['registrationdatereset'] == 1;
						if ( $expired && $resetexpired ) {
							$WishListMemberInstance->UserLevelTimestamp( $wpuser, $add_level, time() );
							$WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $add_level, "{$txnid}".time() );//update txnid
						} else {
							//if levels has expiration and allow reregistration for active members
							$levelexpires 	  = isset( $wpm_levels[ $add_level ]['expire'] ) ? (int)$wpm_levels[ $add_level ]['expire'] : false;
							$levelexpires_cal = isset( $wpm_levels[ $add_level ]['calendar'] ) ? $wpm_levels[ $add_level ]['calendar'] : false;
							$resetactive = $wpm_levels[ $add_level ]['registrationdateresetactive'] == 1;
							if ( $levelexpires && $resetactive ) {
								//get the registration date before it gets updated because we will use it later
								$levelexpire_regdate = $WishListMemberInstance->Get_UserLevelMeta( $wpuser, $add_level, 'registration_date');

								$levelexpires_cal = in_array( $levelexpires_cal, array('Days', 'Weeks', 'Months', 'Years') ) ? $levelexpires_cal : false;
								if ( $levelexpires_cal && $levelexpire_regdate ) {
									list( $xdate, $xfraction ) = explode('#', $levelexpire_regdate );
									list( $xyear, $xmonth, $xday, $xhour, $xminute, $xsecond ) = preg_split('/[- :]/', $xdate );
									if ( $levelexpires_cal == "Days" ) $xday = $levelexpires + $xday;
									if ( $levelexpires_cal == "Weeks" ) $xday = ($levelexpires * 7) + $xday;
									if ( $levelexpires_cal == "Months" ) $xmonth = $levelexpires + $xmonth;
									if ( $levelexpires_cal == "Years" ) $xyear = $levelexpires + $xyear;
									$WishListMemberInstance->UserLevelTimestamp( $wpuser, $add_level, mktime( $xhour, $xminute, $xsecond, $xmonth, $xday, $xyear ) );
									$WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $add_level, "{$txnid}".time() );//update txnid
								}
							}
						}
					}
				}
				//refresh for possible new levels
				$current_user_mlevels = $WishListMemberInstance->GetMembershipLevels( $wpuser );
			}
			//cancel from level
			if ( count( $cancelled_levels ) > 0  ) {
				$user_mlevels = $current_user_mlevels;
				foreach ( $cancelled_levels as $id => $cancel_level ) {
					if ( !isset( $wpm_levels[ $cancel_level ] ) ) continue;//check if valid level
					if( in_array( $cancel_level, $user_mlevels ) ) {
						$ret = $WishListMemberInstance->LevelCancelled( $cancel_level, $wpuser, true );
						// $WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $cancel_level, "{$txnid}".time() );//update txnid
					}
				}
			}
			//remove from level
			if ( count( $removed_levels ) > 0  ) {
				$user_mlevels = $current_user_mlevels;
				foreach ( $removed_levels as $id => $remove_level ) {
					$arr_index = array_search( $remove_level, $user_mlevels );
					if ( $arr_index !== false ) {
						unset( $user_mlevels[ $arr_index ] );
					}
				}
				$WishListMemberInstance->SetMembershipLevels( $wpuser, $user_mlevels );
				$WishListMemberInstance->SyncMembership(true);
			}
	    }

		function ConfirmApproveLevelsTagsHook( $uid=null, $levels=null ) {
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if ( strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false ) return;

			$is_member_type_enabled = bp_member_type_enable_disable();
			$is_groups_component_enabled = bp_is_active( 'groups' );
			if ( !$is_groups_component_enabled && !$is_member_type_enabled ) return;

			$levels = is_array($levels) ? $levels : (array) $levels;
			$level_unconfirmed = $WishListMemberInstance->LevelUnConfirmed($levels[0], $uid);
			$level_for_approval = $WishListMemberInstance->LevelForApproval($levels[0], $uid);

			$settings = isset($this->settings['level'][$levels[0]]['add']) ? $this->settings['level'][$levels[0]]['add'] : [];
			$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
			$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];
			$apply_type = isset($settings['apply_type']) ? $settings['apply_type'] : [];
			$remove_type = isset($settings['remove_type']) ? $settings['remove_type'] : [];

			if ( !$level_unconfirmed && !$level_for_approval ) {
				if ( $is_groups_component_enabled ) {
					foreach ( $apply_group as $group_id ) {
						groups_join_group($group_id, $uid);
					}
					foreach ( $remove_group as $group_id ) {
						groups_leave_group( $group_id, $uid );
					}
				}

				if ( $is_member_type_enabled ) {
					foreach ( $apply_type as $type_id ) {
						bp_set_member_type( $uid, $type_id, true );
					}
					foreach ( $remove_type as $type_id ) {
						bp_remove_member_type( $uid, $type_id );
					}
				}
			}
		}

		//FOR NEW USERS
		function NewUserTagsHook($uid=null,$udata=null){
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if(strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false) return;

			$is_member_type_enabled = bp_member_type_enable_disable();
			$is_groups_component_enabled = bp_is_active( 'groups' );
			if ( !$is_groups_component_enabled && !$is_member_type_enabled ) return;

			$level_unconfirmed = $WishListMemberInstance->LevelUnConfirmed($udata['wpm_id'], $uid);
			$level_for_approval = $WishListMemberInstance->LevelForApproval($udata['wpm_id'], $uid);

			$settings = isset($this->settings['level'][$udata['wpm_id']]['add']) ? $this->settings['level'][$udata['wpm_id']]['add'] : [];
			$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
			$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];
			$apply_type = isset($settings['apply_type']) ? $settings['apply_type'] : [];
			$remove_type = isset($settings['remove_type']) ? $settings['remove_type'] : [];

			if ( !$level_unconfirmed && !$level_for_approval ) {
				if ( $is_groups_component_enabled ) {
					foreach ( $apply_group as $group_id ) {
						groups_join_group($group_id, $uid);
					}
					foreach ( $remove_group as $group_id ) {
						groups_leave_group( $group_id, $uid );
					}
				}

				if ( $is_member_type_enabled ) {
					foreach ( $apply_type as $type_id ) {
						bp_set_member_type( $uid, $type_id, true );
					}
					foreach ( $remove_type as $type_id ) {
						bp_remove_member_type( $uid, $type_id );
					}
				}
			}
		}

		//WHEN ADDED TO LEVELS
		function AddUserTagsHook($uid, $addlevels = ''){
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if(strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false) return;

			$is_member_type_enabled = bp_member_type_enable_disable();
			$is_groups_component_enabled = bp_is_active( 'groups' );
			if ( !$is_groups_component_enabled && !$is_member_type_enabled ) return;

			$level_added = reset($addlevels); //get the first element
			// If from registration then don't don't process if the $addlevels is
			// the same level the user registered to. This is already processed by NewUserTagsQueue func.
			if(isset($_POST['action']) && $_POST['action'] == 'wpm_register') {
				if ( $_POST['wpm_id'] == $level_added ) {
					return;
				}
			}

			$level_unconfirmed = $WishListMemberInstance->LevelUnConfirmed($level_added, $uid);
			$level_for_approval = $WishListMemberInstance->LevelForApproval($level_added, $uid);

			$settings = isset($this->settings['level'][$level_added]['add']) ? $this->settings['level'][$level_added]['add'] : [];
			$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
			$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];
			$apply_type = isset($settings['apply_type']) ? $settings['apply_type'] : [];
			$remove_type = isset($settings['remove_type']) ? $settings['remove_type'] : [];

			if ( !$level_unconfirmed && !$level_for_approval ) {
				if ( $is_groups_component_enabled ) {
					foreach ( $apply_group as $group_id ) {
						groups_join_group($group_id, $uid);
					}
					foreach ( $remove_group as $group_id ) {
						groups_leave_group( $group_id, $uid );
					}
				}

				if ( $is_member_type_enabled ) {
					foreach ( $apply_type as $type_id ) {
						bp_set_member_type( $uid, $type_id, true );
					}
					foreach ( $remove_type as $type_id ) {
						bp_remove_member_type( $uid, $type_id );
					}
				}
			} else if ( isset( $_POST['SendMail'] ) ) {
				// This elseif condition fixes the issue where members who are added via
				// WLM API aren't being processed
				if ( $is_groups_component_enabled ) {
					foreach ( $apply_group as $group_id ) {
						groups_join_group($group_id, $uid);
					}
					foreach ( $remove_group as $group_id ) {
						groups_leave_group( $group_id, $uid );
					}
				}

				if ( $is_member_type_enabled ) {
					foreach ( $apply_type as $type_id ) {
						bp_set_member_type( $uid, $type_id, true );
					}
					foreach ( $remove_type as $type_id ) {
						bp_remove_member_type( $uid, $type_id );
					}
				}
			}
		}

		//WHEN REREGISTERED FROM LEVELS
		function ReregUserTagsHook( $uid, $levels = '' ){
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if(strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false) return;

			$is_member_type_enabled = bp_member_type_enable_disable();
			$is_groups_component_enabled = bp_is_active( 'groups' );
			if ( !$is_groups_component_enabled && !$is_member_type_enabled ) return;

			//lets check for PPPosts
			$levels = (array) $levels;
			foreach ( $levels as $key => $level ) {
				if ( strrpos( $level,"U-" ) !== false ) {
    				unset( $levels[$key] );
    			}
			}
			if ( count( $levels ) <= 0 ) return;

			foreach ( $levels as $level ) {
				$settings = isset($this->settings['level'][$level]['rereg']) ? $this->settings['level'][$level]['rereg'] : [];
				$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
				$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];
				$apply_type = isset($settings['apply_type']) ? $settings['apply_type'] : [];
				$remove_type = isset($settings['remove_type']) ? $settings['remove_type'] : [];

				if ( $is_groups_component_enabled ) {
					foreach ( $apply_group as $group_id ) {
						groups_join_group($group_id, $uid);
					}
					foreach ( $remove_group as $group_id ) {
						groups_leave_group( $group_id, $uid );
					}
				}

				if ( $is_member_type_enabled ) {
					foreach ( $apply_type as $type_id ) {
						bp_set_member_type( $uid, $type_id, true );
					}
					foreach ( $remove_type as $type_id ) {
						bp_remove_member_type( $uid, $type_id );
					}
				}
			}
		}

		//WHEN REMOVED FROM LEVELS
		function RemoveUserTagsHook($uid, $removedlevels = ''){
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if(strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false) return;

			$is_member_type_enabled = bp_member_type_enable_disable();
			$is_groups_component_enabled = bp_is_active( 'groups' );
			if ( !$is_groups_component_enabled && !$is_member_type_enabled ) return;

			//lets check for PPPosts
			$levels = (array) $removedlevels;
			foreach ( $levels as $key => $level ) {
				if ( strrpos( $level,"U-" ) !== false ) {
    				unset( $levels[$key] );
    			}
			}
			if ( count( $levels ) <= 0 ) return;

			foreach ( $levels as $level ) {
				$settings = isset($this->settings['level'][$level]['remove']) ? $this->settings['level'][$level]['remove'] : [];
				$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
				$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];
				$apply_type = isset($settings['apply_type']) ? $settings['apply_type'] : [];
				$remove_type = isset($settings['remove_type']) ? $settings['remove_type'] : [];

				if ( $is_groups_component_enabled ) {
					foreach ( $apply_group as $group_id ) {
						groups_join_group($group_id, $uid);
					}
					foreach ( $remove_group as $group_id ) {
						groups_leave_group( $group_id, $uid );
					}
				}

				if ( $is_member_type_enabled ) {
					foreach ( $apply_type as $type_id ) {
						bp_set_member_type( $uid, $type_id, true );
					}
					foreach ( $remove_type as $type_id ) {
						bp_remove_member_type( $uid, $type_id );
					}
				}
			}
		}

		//WHEN CANCELLED FROM LEVELS
		function CancelUserTagsHook($uid, $cancellevels = ''){
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if(strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false) return;

			$is_member_type_enabled = bp_member_type_enable_disable();
			$is_groups_component_enabled = bp_is_active( 'groups' );
			if ( !$is_groups_component_enabled && !$is_member_type_enabled ) return;

			//lets check for PPPosts
			$levels = (array) $cancellevels;
			foreach ( $levels as $key => $level ) {
				if ( strrpos( $level,"U-" ) !== false ) {
    				unset( $levels[$key] );
    			}
			}
			if ( count( $levels ) <= 0 ) return;

			foreach ( $levels as $level ) {
				$settings = isset($this->settings['level'][$level]['cancel']) ? $this->settings['level'][$level]['cancel'] : [];
				$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
				$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];
				$apply_type = isset($settings['apply_type']) ? $settings['apply_type'] : [];
				$remove_type = isset($settings['remove_type']) ? $settings['remove_type'] : [];

				if ( $is_groups_component_enabled ) {
					foreach ( $apply_group as $group_id ) {
						groups_join_group($group_id, $uid);
					}
					foreach ( $remove_group as $group_id ) {
						groups_leave_group( $group_id, $uid );
					}
				}

				if ( $is_member_type_enabled ) {
					foreach ( $apply_type as $type_id ) {
						bp_set_member_type( $uid, $type_id, true );
					}
					foreach ( $remove_type as $type_id ) {
						bp_remove_member_type( $uid, $type_id );
					}
				}
			}
		}

	}
	new WLM_OTHER_INTEGRATION_BUDDYBOSS();
}
