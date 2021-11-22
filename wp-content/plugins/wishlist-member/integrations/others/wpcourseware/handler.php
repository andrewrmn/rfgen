<?php
/*
 * WP Courseware Integration File
 * WP Courseware Site: http://http://wpcourseware.com/
 * Original Integration Author : Fel Jun Palawan
 * Version: $Id$
 */
if ( !class_exists('WLM_OTHER_INTEGRATION_WPCOURSEWARE') ) {

	class WLM_OTHER_INTEGRATION_WPCOURSEWARE {
		private $settings = [];
		public  $plugin_active = false;

		function __construct() {
			global $WishListMemberInstance;
			$data = $WishListMemberInstance->GetOption('wpcourseware_settings');
			$this->settings = is_array($data) ? $data : [];

			//check if WPCourseware LMS is active
			$active_plugins  = wlm_get_active_plugins();
			if ( in_array( 'WP Courseware', $active_plugins ) || isset($active_plugins['wp-courseware/wp-courseware.php']) ) {
				$this->plugin_active = true;
			}
			//check if WPCourseware Add on is active
			if ( in_array( 'WP Courseware - Wishlist Member Add On', $active_plugins ) || isset($active_plugins['wishlist-member-addon-for-wp-courseware/wp-courseware-wishlist-member.php']) ) {
				$this->plugin_active = false;
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

				add_action( 'wpcw_enroll_user', array( $this, 'CourseEnrolledHook' ), 99, 2 );
				add_action( 'wpcw_unenroll_user', array( $this, 'CourseUnEnrolledHook' ), 99, 2 );
				add_action( 'wpcw_user_completed_course', array( $this, 'CourseCompletedHook' ), 99, 3 );
			}
	    }

			/**
			 * Enrolls existing members of a level to courses
			 * Action: `wishlistmember_save_other_provider`
			 * @param  array $data Save data. Expects 'enroll-existing-members' and 'wpcourseware_settings[level][{level_id}]' in $data
			 */
			function enroll_existing_members( $data ) {
				// get courses to enroll to
				$enroll = wlm_arrval( $data, 'enroll-existing-members' );
				// get membership level
				$level = key( wlm_arrval( $data, 'wpcourseware_settings', 'level' ) );
				if( !is_array( $enroll ) || !$enroll || !$level ) {
					// $enroll and $level are both required
					return;
				}
				// add members of $level to the courses in $enroll
				foreach( (array) wlm_arrval( wlmapi_get_level_members( $level ), 'members', 'member' ) AS $member ) {
					$uid = wlm_arrval( $member, 'id' );
					$this->DoLevelHook( $uid, $enroll, array() );
				}
			}

	    function CourseCompletedHook( $user_id, $unit_id, $parent_data ) {
	    	global $WishListMemberInstance;
	    	$course_id = isset($parent_data->parent_course_id) ? $parent_data->parent_course_id : false;
	    	$wpuser = $user_id;
	    	if ( !$wpuser || !$course_id ) return;

	    	$action = "complete";
			$settings = isset($this->settings['course'][$course_id][$action]) ? $this->settings['course'][$course_id][$action] : [];
			$this->DoCourseHook( $wpuser, $course_id, $action, $settings );
	    }

	    function CourseUnEnrolledHook( $user_id, $courses_removed ) {
	    	global $WishListMemberInstance;
	    	$action = "remove";
	    	foreach ( (array) $courses_removed as $c ) {
				$settings = isset($this->settings['course'][$c][$action]) ? $this->settings['course'][$c][$action] : [];
				$this->DoCourseHook( $user_id, $c, $action, $settings );
	    	}
	    }

	    function CourseEnrolledHook( $user_id, $courses_enrolled ) {
	    	global $WishListMemberInstance;
	    	$action = "add";
	    	foreach ( $courses_enrolled as $c ) {
				$settings = isset($this->settings['course'][$c][$action]) ? $this->settings['course'][$c][$action] : [];
				$this->DoCourseHook( $user_id, $c, $action, $settings );
	    	}
	    }

	    private function DoCourseHook( $wpuser, $course_id, $action, $settings ) {
	    	global $WishListMemberInstance;

			$added_levels = isset($settings['add_level']) ? $settings['add_level'] : [];
			$cancelled_levels = isset($settings['cancel_level']) ? $settings['cancel_level'] : [];
			$removed_levels = isset($settings['remove_level']) ? $settings['remove_level'] : [];

			$current_user_mlevels = $WishListMemberInstance->GetMembershipLevels( $wpuser );
			$wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');

			$action = strtoupper(substr( $action, 0, 1));
			$txnid = "WPCourseware-{$action}{$course_id}-";
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

	    private function DoLevelHook( $uid, $apply_course, $remove_course ) {
			if ( function_exists('WPCW_courses_syncUserAccess') ) {
				if ( count( $apply_course ) > 0 ||  count( $remove_course ) > 0 ) {
					$user_courses = WPCW_users_getUserCourseList($uid);
					$current_courses = [];
					foreach ( $user_courses as $c ) {
						$current_courses[] = $c->course_id;
					}
					$current_courses = array_merge( $current_courses, $apply_course ); //add courses
					$current_courses = array_diff( $current_courses, $remove_course ); //remove courses
					$current_courses = array_unique($current_courses);
					WPCW_courses_syncUserAccess( $uid, $current_courses, 'sync');
				}
			}
	    }

		function ConfirmApproveLevelsTagsHook( $uid=null, $levels=null ) {
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if ( strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false ) return;

			$levels = is_array($levels) ? $levels : (array) $levels;
			$level_unconfirmed = $WishListMemberInstance->LevelUnConfirmed($levels[0], $uid);
			$level_for_approval = $WishListMemberInstance->LevelForApproval($levels[0], $uid);

			$settings = isset($this->settings['level'][$levels[0]]['add']) ? $this->settings['level'][$levels[0]]['add'] : [];
			$apply_course = isset($settings['apply_course']) ? $settings['apply_course'] : [];
			$remove_course = isset($settings['remove_course']) ? $settings['remove_course'] : [];

			if ( !$level_unconfirmed && !$level_for_approval ) {
				$this->DoLevelHook($uid, $apply_course, $remove_course);
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

			if ( !$level_unconfirmed && !$level_for_approval ) {
				$this->DoLevelHook($uid, $apply_course, $remove_course);
			}
		}

		//WHEN ADDED TO LEVELS
		function AddUserTagsHook($uid, $addlevels = ''){
			global $WishListMemberInstance;
			$user = get_userdata( $uid );
			if ( !$user ) return;
			if(strpos($user->user_email,"temp_") !== false && strlen($user->user_email) == 37 && strpos($user->user_email,"@") === false) return;

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
			$apply_course = isset($settings['apply_course']) ? $settings['apply_course'] : [];
			$remove_course = isset($settings['remove_course']) ? $settings['remove_course'] : [];
			if ( !$level_unconfirmed && !$level_for_approval ) {
				$this->DoLevelHook($uid, $apply_course, $remove_course);
			} else if ( isset( $_POST['SendMail'] ) ) {
				$this->DoLevelHook($uid, $apply_course, $remove_course);
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
				$this->DoLevelHook($uid, $apply_course, $remove_course);
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
				$this->DoLevelHook($uid, $apply_course, $remove_course);
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
				$this->DoLevelHook($uid, $apply_course, $remove_course);
			}
		}
	}
}

$WLMWPCoursewareInstance = new WLM_OTHER_INTEGRATION_WPCOURSEWARE();
$WLMWPCoursewareInstance->load_hooks();