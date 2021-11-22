<?php
/**
 * Handler for Slack integration
 * Author: Mike Lopez <mike@wishlistproducts.com>
 */

if ( ! class_exists( 'WLM_OTHER_INTEGRATION_TUTOR' ) ) {
	class WLM_OTHER_INTEGRATION_TUTOR {
		private $settings = [];
		public  $plugin_active = false;

		function __construct() {
			global $WishListMemberInstance;

			$data = $WishListMemberInstance->GetOption('tutorlms_settings');
			$this->settings = is_array($data) ? $data : [];

			//check if LearnDash LMS is active
			$active_plugins  = wlm_get_active_plugins();
			if ( in_array( 'TutorLMS', $active_plugins ) || isset($active_plugins['tutor/tutor.php']) || is_plugin_active('tutor/tutor.php') ) {
				$this->plugin_active = function_exists('tutor_utils');
			}

			$this->load_hooks();
			// hooks
			// var_dump( tutor_utils()->do_enroll() );
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

				add_action( 'tutor_after_enroll', array( $this, 'CoursEnrollHook' ), 99, 2 );
				add_action( 'tutor_course_complete_after', array( $this, 'CourseCompletedHook' ), 99, 2 );
			}
	    }
			
			/**
			 * Enrolls existing members of a level to courses
			 * Action: `wishlistmember_save_other_provider`
			 * @param  array $data Save data. Expects 'enroll-existing-members' and 'tutorlms_settings[level][{level_id}]' in $data
			 */
			function enroll_existing_members( $data ) {
				// get courses to enroll to
				$enroll = wlm_arrval( $data, 'enroll-existing-members' );
				// get membership level
				$level = key( wlm_arrval( $data, 'tutorlms_settings', 'level' ) );
				if( !is_array( $enroll ) || !$enroll || !$level ) {
					// $enroll and $level are both required
					return;
				}
				// add members of $level to the courses in $enroll
				foreach( (array) wlm_arrval( wlmapi_get_level_members( $level ), 'members', 'member' ) AS $member ) {
					$uid = wlm_arrval( $member, 'id' );
					foreach( $enroll AS $course_id ) {
						$order_id = "WLM-{$level}-{$uid}-{$course_id}";
						tutor_utils()->do_enroll( $course_id , $order_id, $uid );
					}
				}
			}

	    function CourseCompletedHook( $course_id ) {
	    	global $WishListMemberInstance;
	    	$action = "complete";
	    	//in tutor, each student enrolled in the course, have a child post of that course
	    	//we get the author of the post to get the student (user) id who enrolled on that course
	    	$user_id = get_current_user_id();
	    	if ( $user_id ) {
				$settings = isset($this->settings['course'][$course_id][$action]) ? $this->settings['course'][$course_id][$action] : [];
				$this->DoCourseHook( $user_id, $course_id, $action, $settings );
	    	}
	    }

	    function CoursEnrollHook( $course_id, $post_id ) {
	    	global $WishListMemberInstance;
	    	$action = "add";
	    	//in tutor, each student enrolled in the course, have a child post of that course
	    	//we get the author of the post to get the student (user) id who enrolled on that course
	    	$post   = get_post($post_id);
	    	if ( $post ) {
				$settings = isset($this->settings['course'][$course_id][$action]) ? $this->settings['course'][$course_id][$action] : [];
				$this->DoCourseHook( $post->post_author, $course_id, $action, $settings );
	    	}
	    }

	    private function DoCourseHook( $wpuser, $hook_id, $action, $settings ) {
	    	global $WishListMemberInstance;

			$added_levels = isset($settings['add_level']) ? $settings['add_level'] : [];
			$cancelled_levels = isset($settings['cancel_level']) ? $settings['cancel_level'] : [];
			$removed_levels = isset($settings['remove_level']) ? $settings['remove_level'] : [];

			$current_user_mlevels = $WishListMemberInstance->GetMembershipLevels( $wpuser );
			$wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');

			$action = strtoupper(substr( $action, 0, 1));
			$txnid = "Tutor-{$action}{$hook_id}-";
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
			$level_unconfirmed = $WishListMemberInstance->LevelUnConfirmed($levels[0], $uid);
			$level_for_approval = $WishListMemberInstance->LevelForApproval($levels[0], $uid);

			$settings = isset($this->settings['level'][$levels[0]]['add']) ? $this->settings['level'][$levels[0]]['add'] : [];
			$apply_course = isset($settings['apply_course']) ? $settings['apply_course'] : [];
			$remove_course = isset($settings['remove_course']) ? $settings['remove_course'] : [];

			if ( !$level_unconfirmed && !$level_for_approval ) {
				foreach ( $apply_course as $course_id ) {
					$order_id = "WLM-{$level[0]}-{$uid}-{$course_id}";
					tutor_utils()->do_enroll( $course_id , $order_id, $uid );
				}
				foreach ( $remove_course as $course_id ) {
					tutor_utils()->cancel_course_enrol( $course_id , $uid, 'delete' );
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

			if ( !$level_unconfirmed && !$level_for_approval ) {
				foreach ( $apply_course as $course_id ) {
					$order_id = "WLM-{$udata['wpm_id']}-{$uid}-{$course_id}";
					tutor_utils()->do_enroll($course_id , $order_id, $uid );
				}
				foreach ( $remove_course as $course_id ) {
					tutor_utils()->cancel_course_enrol( $course_id , $uid, 'delete' );
				}
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
				foreach ( $apply_course as $course_id ) {
					$order_id = "WLM-{$level_added}-{$uid}-{$course_id}";
					tutor_utils()->do_enroll($course_id , $order_id, $uid );
				}
				foreach ( $remove_course as $course_id ) {
					tutor_utils()->cancel_course_enrol( $course_id , $uid, 'delete' );
				}
			} else if ( isset( $_POST['SendMail'] ) ) {
				// This elseif condition fixes the issue where members who are added via
				// WLM API aren't being processed
				foreach ( $apply_course as $course_id ) {
					$order_id = "WLM-{$level_added}-{$uid}-{$course_id}";
					tutor_utils()->do_enroll($course_id , $order_id, $uid );
				}
				foreach ( $remove_course as $course_id ) {
					tutor_utils()->cancel_course_enrol( $course_id , $uid, 'delete' );
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

				foreach ( $apply_course as $course_id ) {
					$order_id = "WLM-{$level}-{$uid}-{$course_id}";
					tutor_utils()->do_enroll($course_id , $order_id, $uid );
				}
				foreach ( $remove_course as $course_id ) {
					tutor_utils()->cancel_course_enrol( $course_id , $uid, 'delete' );
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

				foreach ( $apply_course as $course_id ) {
					$order_id = "WLM-{$level}-{$uid}-{$course_id}";
					tutor_utils()->do_enroll($course_id , $order_id, $uid );
				}
				foreach ( $remove_course as $course_id ) {
					tutor_utils()->cancel_course_enrol( $course_id , $uid, 'delete' );
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

				foreach ( $apply_course as $course_id ) {
					$order_id = "WLM-{$level}-{$uid}-{$course_id}";
					tutor_utils()->do_enroll($course_id , $order_id, $uid );
				}

				foreach ( $remove_course as $course_id ) {
					tutor_utils()->cancel_course_enrol( $course_id , $uid, 'delete' );
				}
			}
		}

	}
	new WLM_OTHER_INTEGRATION_TUTOR();
}
