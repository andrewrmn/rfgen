<?php
/*
 * LearnDash Integration File
 * LearnDash Site: http://learndash.com/
 * Original Integration Author : Fel Jun Palawan
 * Version: $Id$
 */
if ( !class_exists('WLM_OTHER_INTEGRATION_LEARNDASH') ) {

	class WLM_OTHER_INTEGRATION_LEARNDASH {
		private $settings = [];
		public  $plugin_active = false;

		function __construct() {
			global $WishListMemberInstance;
			$data = $WishListMemberInstance->GetOption('learndash_settings');
			$this->settings = is_array($data) ? $data : [];
			$this->settings['group_default'] = $WishListMemberInstance->GetOption('wlm_ld_group_default');

			//check if LearnDash LMS is active
			$active_plugins  = wlm_get_active_plugins();
			if ( in_array( 'LearnDash LMS', $active_plugins ) || isset($active_plugins['sfwd-lms/sfwd_lms.php']) || is_plugin_active('sfwd-lms/sfwd_lms.php') ) {
				$this->plugin_active = function_exists('ld_update_course_access');
			}
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

				add_action ('wishlistmember_save_other_provider', array( $this, 'enroll_existing_members' ) );

				add_action( 'learndash_update_course_access', array( $this, 'CourseUpdatedHook' ), 99, 4 );
				add_action( 'learndash_course_completed', array( $this, 'CourseCompletedHook' ), 99, 1 );

				add_action( 'ld_removed_group_access', array( $this, 'GroupRemovedHook' ), 99, 2 );
				add_action( 'ld_added_group_access', array( $this, 'GroupAddedHook' ), 99, 2 );

				if ( isset($this->settings['group_default']) && $this->settings['group_default'] ) {
					add_action( 'save_post_groups',  array( $this, 'SaveGroupDefaultActions' ), 10, 3 );
				}
			}
	    }

			/**
			 * Enrolls existing members of a level to courses
			 * Action: `wishlistmember_save_other_provider`
			 * @param  array $data Save data. Expects 'enroll-existing-members' and 'learndash_settings[level][{level_id}]' in $data
			 */
			function enroll_existing_members( $data ) {
				// get courses to enroll to
				$enroll = wlm_arrval( $data, 'enroll-existing-members' );
				// get membership level
				$level = key( wlm_arrval( $data, 'learndash_settings', 'level' ) );
				if( !is_array( $enroll ) || !$enroll || !$level ) {
					// $enroll and $level are both required
					return;
				}
				// add members of $level to the courses in $enroll
				foreach( (array) wlm_arrval( wlmapi_get_level_members( $level ), 'members', 'member' ) AS $member ) {
					$uid = wlm_arrval( $member, 'id' );
					foreach( $enroll AS $course_id ) {
						ld_update_course_access( $uid, $course_id, false );
					}
				}
			}

	    function SaveGroupDefaultActions( $post_id, $post, $update ) {
	    	global $WishListMemberInstance;
	    	//if revision or update, disregard
	    	if ( $update || wp_is_post_revision( $post_id ) ) return;

	    	$data = $WishListMemberInstance->GetOption('learndash_settings');
	    	if ( !isset($data['group']['default']) ) return;
	    	$data['group'][$post_id] = $data['group']['default'];
	    	$WishListMemberInstance->SaveOption('learndash_settings', $data);
	    }

	    function GroupAddedHook( $user_id, $group_id ) {
	    	global $WishListMemberInstance;
	    	$action = "add";
			$settings = isset($this->settings['group'][$group_id][$action]) ? $this->settings['group'][$group_id][$action] : [];
			$this->DoCourseHook( $user_id, $group_id, $action, $settings, false );
	    }

	    function GroupRemovedHook( $user_id, $group_id ) {
	    	global $WishListMemberInstance;
	    	$action = "remove";
			$settings = isset($this->settings['group'][$group_id][$action]) ? $this->settings['group'][$group_id][$action] : [];
			$this->DoCourseHook( $user_id, $group_id, $action, $settings, false );
	    }

	    function CourseCompletedHook( $data ) {
	    	global $WishListMemberInstance;
	    	$user = isset($data['user']) ? $data['user'] : false;
	    	$course = isset($data['course']) ? $data['course'] : false;
	    	if ( !$user || !$course ) return;

	    	$wpuser = isset( $user->ID ) ? $user->ID : false;
	    	$course_id = isset( $course->ID ) ? $course->ID : false;
	    	if ( !$wpuser || !$course_id ) return;

	    	$action = "complete";
			$settings = isset($this->settings['course'][$course_id][$action]) ? $this->settings['course'][$course_id][$action] : [];
			$this->DoCourseHook( $wpuser, $course_id, $action, $settings );
	    }

	    function CourseUpdatedHook( $wpuser, $course_id, $access_list, $remove ) {
	    	global $WishListMemberInstance;
	    	$action = $remove ? "remove" : "add";
			$settings = isset($this->settings['course'][$course_id][$action]) ? $this->settings['course'][$course_id][$action] : [];
			$this->DoCourseHook( $wpuser, $course_id, $action, $settings );
	    }

	    private function DoCourseHook( $wpuser, $hook_id, $action, $settings, $is_course = true ) {
	    	global $WishListMemberInstance;

			$added_levels = isset($settings['add_level']) ? $settings['add_level'] : [];
			$cancelled_levels = isset($settings['cancel_level']) ? $settings['cancel_level'] : [];
			$removed_levels = isset($settings['remove_level']) ? $settings['remove_level'] : [];

			$current_user_mlevels = $WishListMemberInstance->GetMembershipLevels( $wpuser );
			$wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');

			$prefix = $is_course ? "C" : "G";

			$action = strtoupper(substr( $action, 0, 1));
			$txnid = "LearnDash-{$action}{$prefix}{$hook_id}-";
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

			$levels = is_array($levels) ? $levels : (array) $levels;

			foreach ( $levels as $key => $lvl ) {
				$level_unconfirmed = $WishListMemberInstance->LevelUnConfirmed($lvl, $uid);
				$level_for_approval = $WishListMemberInstance->LevelForApproval($lvl, $uid);

				$settings = isset($this->settings['level'][$lvl]['add']) ? $this->settings['level'][$lvl]['add'] : [];
				$apply_course = isset($settings['apply_course']) ? $settings['apply_course'] : [];
				$remove_course = isset($settings['remove_course']) ? $settings['remove_course'] : [];
				$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
				$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];

				if ( !$level_unconfirmed && !$level_for_approval ) {
					foreach ( $apply_course as $course_id ) {
						ld_update_course_access( $uid, $course_id, false );
					}
					foreach ( $remove_course as $course_id ) {
						ld_update_course_access( $uid, $course_id, true );
					}
					foreach ( $apply_group as $group_id ) {
						ld_update_group_access( $uid, $group_id, false );
					}
					foreach ( $remove_group as $group_id ) {
						ld_update_group_access( $uid, $group_id, true );
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

			$level_unconfirmed = $WishListMemberInstance->LevelUnConfirmed($udata['wpm_id'], $uid);
			$level_for_approval = $WishListMemberInstance->LevelForApproval($udata['wpm_id'], $uid);

			$settings = isset($this->settings['level'][$udata['wpm_id']]['add']) ? $this->settings['level'][$udata['wpm_id']]['add'] : [];
			$apply_course = isset($settings['apply_course']) ? $settings['apply_course'] : [];
			$remove_course = isset($settings['remove_course']) ? $settings['remove_course'] : [];
			$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
			$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];

			if ( !$level_unconfirmed && !$level_for_approval ) {
				foreach ( $apply_course as $course_id ) {
					ld_update_course_access( $uid, $course_id, false );
				}
				foreach ( $remove_course as $course_id ) {
					ld_update_course_access( $uid, $course_id, true );
				}
				foreach ( $apply_group as $group_id ) {
					ld_update_group_access( $uid, $group_id, false );
				}
				foreach ( $remove_group as $group_id ) {
					ld_update_group_access( $uid, $group_id, true );
				}
			}
		}

		//WHEN ADDED TO LEVELS
		function AddUserTagsHook($uid, $addlevels = ''){
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if(strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false) return;

			$addlevels = is_array($addlevels) ? $addlevels : (array) $addlevels;
			$level_added = $addlevels;
			$level_added = reset($level_added); //get the first element
			// If from registration then don't don't process if the $addlevels is
			// the same level the user registered to. This is already processed by NewUserTagsQueue func.
			if(isset($_POST['action']) && $_POST['action'] == 'wpm_register') {
				if ( $_POST['wpm_id'] == $level_added ) {
					return;
				}
			}

			foreach ( $addlevels as $key => $lvl ) {
				$level_unconfirmed = $WishListMemberInstance->LevelUnConfirmed($lvl, $uid);
				$level_for_approval = $WishListMemberInstance->LevelForApproval($lvl, $uid);

				$settings = isset($this->settings['level'][$lvl]['add']) ? $this->settings['level'][$lvl]['add'] : [];
				$apply_course = isset($settings['apply_course']) ? $settings['apply_course'] : [];
				$remove_course = isset($settings['remove_course']) ? $settings['remove_course'] : [];
				$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
				$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];

				if ( !$level_unconfirmed && !$level_for_approval ) {
					foreach ( $apply_course as $course_id ) {
						ld_update_course_access( $uid, $course_id, false );
					}
					foreach ( $remove_course as $course_id ) {
						ld_update_course_access( $uid, $course_id, true );
					}
					foreach ( $apply_group as $group_id ) {
						ld_update_group_access( $uid, $group_id, false );
					}
					foreach ( $remove_group as $group_id ) {
						ld_update_group_access( $uid, $group_id, true );
					}
				} else if ( isset( $_POST['SendMail'] ) ) {
					// This elseif condition fixes the issue where members who are added via
					// WLM API aren't being processed
					foreach ( $apply_course as $course_id ) {
						ld_update_course_access( $uid, $course_id, false );
					}
					foreach ( $remove_course as $course_id ) {
						ld_update_course_access( $uid, $course_id, true );
					}
					foreach ( $apply_group as $group_id ) {
						ld_update_group_access( $uid, $group_id, false );
					}
					foreach ( $remove_group as $group_id ) {
						ld_update_group_access( $uid, $group_id, true );
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
				$apply_course = isset($settings['apply_course']) ? $settings['apply_course'] : [];
				$remove_course = isset($settings['remove_course']) ? $settings['remove_course'] : [];
				$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
				$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];

				foreach ( $apply_course as $course_id ) {
					ld_update_course_access( $uid, $course_id, false );
				}
				foreach ( $remove_course as $course_id ) {
					ld_update_course_access( $uid, $course_id, true );
				}
				foreach ( $apply_group as $group_id ) {
					ld_update_group_access( $uid, $group_id, false );
				}
				foreach ( $remove_group as $group_id ) {
					ld_update_group_access( $uid, $group_id, true );
				}
			}
		}

		//WHEN REMOVED FROM LEVELS
		function RemoveUserTagsHook($uid, $removedlevels = ''){
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if(strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false) return;

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
				$apply_course = isset($settings['apply_course']) ? $settings['apply_course'] : [];
				$remove_course = isset($settings['remove_course']) ? $settings['remove_course'] : [];
				$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
				$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];

				foreach ( $apply_course as $course_id ) {
					ld_update_course_access( $uid, $course_id, false );
				}
				foreach ( $remove_course as $course_id ) {
					ld_update_course_access( $uid, $course_id, true );
				}
				foreach ( $apply_group as $group_id ) {
					ld_update_group_access( $uid, $group_id, false );
				}
				foreach ( $remove_group as $group_id ) {
					ld_update_group_access( $uid, $group_id, true );
				}
			}
		}

		//WHEN CANCELLED FROM LEVELS
		function CancelUserTagsHook($uid, $cancellevels = ''){
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if(strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false) return;

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
				$apply_course = isset($settings['apply_course']) ? $settings['apply_course'] : [];
				$remove_course = isset($settings['remove_course']) ? $settings['remove_course'] : [];
				$apply_group = isset($settings['apply_group']) ? $settings['apply_group'] : [];
				$remove_group = isset($settings['remove_group']) ? $settings['remove_group'] : [];

				foreach ( $apply_course as $course_id ) {
					ld_update_course_access( $uid, $course_id, false );
				}
				foreach ( $remove_course as $course_id ) {
					ld_update_course_access( $uid, $course_id, true );
				}
				foreach ( $apply_group as $group_id ) {
					ld_update_group_access( $uid, $group_id, false );
				}
				foreach ( $remove_group as $group_id ) {
					ld_update_group_access( $uid, $group_id, true );
				}
			}
		}
	}
}

$WLMLearnDashInstance = new WLM_OTHER_INTEGRATION_LEARNDASH();
$WLMLearnDashInstance->load_hooks();