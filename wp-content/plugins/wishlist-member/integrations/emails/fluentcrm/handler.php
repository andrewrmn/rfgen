<?php
namespace WishListMember\Autoresponders;

class FluentCRM {
	static function __callStatic( $name, $args ) {
		$interface = self::_interface();
		if ( $interface->api() ) {
			call_user_func_array( array( $interface, $name ), $args );
		}
	}

	static function delete_tag_action() {
		$fluentcrm_settings = new \WishListMember\Autoresponder( 'fluentcrm' );
		try {
			unset( $fluentcrm_settings->settings['fluentcrm_settings']['tag'][ wlm_arrval( $_POST, 'tag_id' ) ] );
		} catch ( Exception $e ) {
		}
		$fluentcrm_settings->save_settings();
		wp_send_json_success();
	}

	static function _interface() {
		static $interface;
		if ( ! $interface ) {
			$interface = new FluentCRM_Interface();
		}
		return $interface;
	}
}

class FluentCRM_Interface {
	private $settings     = array();
	public $plugin_active = false;

	function __construct() {
		global $WishListMemberInstance;

		$data = ( new \WishListMember\Autoresponder( 'fluentcrm' ) )->settings ?: false;
		$data = isset( $data['fluentcrm_settings'] ) ? $data['fluentcrm_settings'] : array();
		// $WishListMemberInstance->GetOption('fluentcrm_settings');
		$this->settings = is_array( $data ) ? $data : array();

		// check if FluentCRM is active
		$active_plugins = wlm_get_active_plugins();
		if ( in_array( 'FluentCRM - Marketing Automation For WordPress', $active_plugins ) || isset( $active_plugins['fluent-crm/fluent-crm.php'] ) || is_plugin_active( 'fluent-crm/fluent-crm.php' ) ) {
			$this->plugin_active = true;
		}
	}

	function api() {
		return $this->plugin_active;
	}

	function TagsAddedHook( $attached_tagids, $subscriber ) {
		global $WishListMemberInstance;
		$action = 'add';
		$user   = get_user_by( 'email', $subscriber->email );
		if ( $user === false ) {
			return;
		}
		foreach ( $attached_tagids as $key => $value ) {
			$settings = isset( $this->settings['tag'][ $value ][ $action ] ) ? $this->settings['tag'][ $value ][ $action ] : array();
			$this->DoHook( $user->ID, $value, $action, $settings, false );
		}
	}

	function TagsRemovedHook( $detached_tagids, $subscriber ) {
		global $WishListMemberInstance;
		$action = 'remove';
		$user   = get_user_by( 'email', $subscriber->email );
		if ( $user === false ) {
			return;
		}
		foreach ( $detached_tagids as $key => $value ) {
			$settings = isset( $this->settings['tag'][ $value ][ $action ] ) ? $this->settings['tag'][ $value ][ $action ] : array();
			$this->DoHook( $user->ID, $value, $action, $settings, false );
		}
	}

	function ListsAddedHook( $attached_listids, $subscriber ) {
		global $WishListMemberInstance;
		$action = 'add';
		$user   = get_user_by( 'email', $subscriber->email );
		if ( $user === false ) {
			return;
		}
		foreach ( $attached_listids as $key => $value ) {
			$settings = isset( $this->settings['list'][ $value ][ $action ] ) ? $this->settings['list'][ $value ][ $action ] : array();
			$this->DoHook( $user->ID, $value, $action, $settings, false );
		}
	}

	function ListsRemovedHook( $detached_listids, $subscriber ) {
		global $WishListMemberInstance;
		$action = 'remove';
		$user   = get_user_by( 'email', $subscriber->email );
		if ( $user === false ) {
			return;
		}
		foreach ( $detached_listids as $key => $value ) {
			$settings = isset( $this->settings['list'][ $value ][ $action ] ) ? $this->settings['list'][ $value ][ $action ] : array();
			$this->DoHook( $user->ID, $value, $action, $settings, false );
		}
	}

	private function DoHook( $wpuser, $hook_id, $action, $settings, $is_list = true ) {
		global $WishListMemberInstance;

		$added_levels     = isset( $settings['add_level'] ) ? $settings['add_level'] : array();
		$cancelled_levels = isset( $settings['cancel_level'] ) ? $settings['cancel_level'] : array();
		$removed_levels   = isset( $settings['remove_level'] ) ? $settings['remove_level'] : array();

		$add_ppp    = isset( $settings['add_ppp'] ) ? $settings['add_ppp'] : array();
		$remove_ppp = isset( $settings['remove_ppp'] ) ? $settings['remove_ppp'] : array();

		if ( count( $added_levels ) <= 0 && count( $cancelled_levels ) <= 0 && count( $removed_levels ) <= 0 && count( $add_ppp ) <= 0 && count( $remove_ppp ) <= 0 ) {
			return;
		}

		$current_user_mlevels = $WishListMemberInstance->GetMembershipLevels( $wpuser );
		$wpm_levels           = $WishListMemberInstance->GetOption( 'wpm_levels' );

		$prefix = $is_list ? 'L' : 'T';

		$action = strtoupper( substr( $action, 0, 1 ) );
		$txnid  = "FLUENTCRM-{$action}-{$prefix}{$hook_id}-";

		// add to level
		if ( count( $added_levels ) > 0 ) {
			$user_mlevels  = $current_user_mlevels;
			$add_level_arr = $added_levels;
			foreach ( $add_level_arr as $id => $add_level ) {
				if ( ! isset( $wpm_levels[ $add_level ] ) ) {
					continue;// check if valid level
				}
				if ( ! in_array( $add_level, $user_mlevels ) ) {
					$user_mlevels[] = $add_level;
					$new_levels[]   = $add_level; // record the new level
					$WishListMemberInstance->SetMembershipLevels( $wpuser, $user_mlevels );
					$WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $add_level, "{$txnid}" . time() );// update txnid
				} else {
					// For cancelled members
					$cancelled      = $WishListMemberInstance->LevelCancelled( $add_level, $wpuser );
					$resetcancelled = true; // lets make sure that old versions without this settings still works
					if ( isset( $wpm_levels[ $add_level ]['uncancelonregistration'] ) ) {
						$resetcancelled = $wpm_levels[ $add_level ]['uncancelonregistration'] == 1;
					}
					if ( $cancelled && $resetcancelled ) {
						$ret = $WishListMemberInstance->LevelCancelled( $add_level, $wpuser, false );
						$WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $add_level, "{$txnid}" . time() );// update txnid
					}

					// For Expired Members
					$expired      = $WishListMemberInstance->LevelExpired( $add_level, $wpuser );
					$resetexpired = $wpm_levels[ $add_level ]['registrationdatereset'] == 1;
					if ( $expired && $resetexpired ) {
							$WishListMemberInstance->UserLevelTimestamp( $wpuser, $add_level, time() );
							$WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $add_level, "{$txnid}" . time() );// update txnid
					} else {
						// if levels has expiration and allow reregistration for active members
						$levelexpires     = isset( $wpm_levels[ $add_level ]['expire'] ) ? (int) $wpm_levels[ $add_level ]['expire'] : false;
						$levelexpires_cal = isset( $wpm_levels[ $add_level ]['calendar'] ) ? $wpm_levels[ $add_level ]['calendar'] : false;
						$resetactive      = $wpm_levels[ $add_level ]['registrationdateresetactive'] == 1;
						if ( $levelexpires && $resetactive ) {
							// get the registration date before it gets updated because we will use it later
							$levelexpire_regdate = $WishListMemberInstance->Get_UserLevelMeta( $wpuser, $add_level, 'registration_date' );

							$levelexpires_cal = in_array( $levelexpires_cal, array( 'Days', 'Weeks', 'Months', 'Years' ) ) ? $levelexpires_cal : false;
							if ( $levelexpires_cal && $levelexpire_regdate ) {
								list( $xdate, $xfraction )                                 = explode( '#', $levelexpire_regdate );
								list( $xyear, $xmonth, $xday, $xhour, $xminute, $xsecond ) = preg_split( '/[- :]/', $xdate );
								if ( $levelexpires_cal == 'Days' ) {
									$xday = $levelexpires + $xday;
								}
								if ( $levelexpires_cal == 'Weeks' ) {
									$xday = ( $levelexpires * 7 ) + $xday;
								}
								if ( $levelexpires_cal == 'Months' ) {
									$xmonth = $levelexpires + $xmonth;
								}
								if ( $levelexpires_cal == 'Years' ) {
									$xyear = $levelexpires + $xyear;
								}
								$WishListMemberInstance->UserLevelTimestamp( $wpuser, $add_level, mktime( $xhour, $xminute, $xsecond, $xmonth, $xday, $xyear ) );
								$WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $add_level, "{$txnid}" . time() );// update txnid
							}
						}
					}
				}
			}
			// refresh for possible new levels
			$current_user_mlevels = $WishListMemberInstance->GetMembershipLevels( $wpuser );
		}

		// cancel from level
		if ( count( $cancelled_levels ) > 0 ) {
			$user_mlevels = $current_user_mlevels;
			foreach ( $cancelled_levels as $id => $cancel_level ) {
				if ( ! isset( $wpm_levels[ $cancel_level ] ) ) {
					continue;// check if valid level
				}
				if ( in_array( $cancel_level, $user_mlevels ) ) {
					$ret = $WishListMemberInstance->LevelCancelled( $cancel_level, $wpuser, true );
					// $WishListMemberInstance->SetMembershipLevelTxnID( $wpuser, $cancel_level, "{$txnid}".time() );//update txnid
				}
			}
		}

		// remove from level
		if ( count( $removed_levels ) > 0 ) {
			$user_mlevels = $current_user_mlevels;
			foreach ( $removed_levels as $id => $remove_level ) {
				$arr_index = array_search( $remove_level, $user_mlevels );
				if ( $arr_index !== false ) {
					unset( $user_mlevels[ $arr_index ] );
				}
			}
			$WishListMemberInstance->SetMembershipLevels( $wpuser, $user_mlevels );
			$WishListMemberInstance->SyncMembership( true );
		}

		if ( count( $add_ppp ) > 0 ) {
			foreach ( $add_ppp as $key => $value ) {
				$post = get_post( $value, ARRAY_A );
				if ( $post ) {
					$WishListMemberInstance->AddPostUsers( $post['post_type'], $post['ID'], $wpuser );
				}
			}
		}

		if ( count( $remove_ppp ) > 0 ) {
			foreach ( $remove_ppp as $key => $value ) {
				$post = get_post( $value, ARRAY_A );
				if ( $post ) {
					$WishListMemberInstance->RemovePostUsers( $post['post_type'], $post['ID'], $wpuser );
				}
			}
		}
	}

	private function create_fluentcrm_contact( $data ) {
		$contactApi = FluentCrmApi( 'contacts' );
		$contact    = $contactApi->createOrUpdate( $data );
		// send a double opt-in email if the status is pending
		if ( $contact->status == 'pending' ) {
			$contact->sendDoubleOptinEmail();
		}
	}

	// FOR NEW USERS
	function NewUserTagsHook( $uid = null, $udata = null ) {
		global $WishListMemberInstance;
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$level_unconfirmed  = $WishListMemberInstance->LevelUnConfirmed( $udata['wpm_id'], $uid );
		$level_for_approval = $WishListMemberInstance->LevelForApproval( $udata['wpm_id'], $uid );

		$settings    = isset( $this->settings['level'][ $udata['wpm_id'] ]['add'] ) ? $this->settings['level'][ $udata['wpm_id'] ]['add'] : array();
		$apply_tag   = isset( $settings['apply_tag'] ) ? $settings['apply_tag'] : array();
		$remove_tag  = isset( $settings['remove_tag'] ) ? $settings['remove_tag'] : array();
		$apply_list  = isset( $settings['apply_list'] ) ? $settings['apply_list'] : array();
		$remove_list = isset( $settings['remove_list'] ) ? $settings['remove_list'] : array();

		if ( ! $level_unconfirmed && ! $level_for_approval ) {
			$contactApi = FluentCrmApi( 'contacts' );
			$contact    = $contactApi->getContact( $user->user_email );
			if ( ! $contact ) {
				$data = array(
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
					'email'      => $user->user_email, // requied
					'tags'       => $apply_tag, // tag ids as an array
					'lists'      => $apply_list, // list ids as an array
				);
				$this->create_fluentcrm_contact( $data );
			} else {
				$contact->detachTags( $remove_tag );
				$contact->detachLists( $remove_list );
				$contact->attachTags( $apply_tag );
				$contact->attachLists( $apply_list );
			}
		}
	}

	// WHEN ADDED TO LEVELS
	function AddUserTagsHook( $uid, $addlevels = '' ) {
		global $WishListMemberInstance;
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$level_added = reset( $addlevels ); // get the first element
		// If from registration then don't don't process if the $addlevels is
		// the same level the user registered to. This is already processed by NewUserTagsQueue func.
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'wpm_register' ) {
			if ( $_POST['wpm_id'] == $level_added ) {
				return;
			}
		}

		$level_unconfirmed  = $WishListMemberInstance->LevelUnConfirmed( $level_added, $uid );
		$level_for_approval = $WishListMemberInstance->LevelForApproval( $level_added, $uid );

		$settings    = isset( $this->settings['level'][ $level_added ]['add'] ) ? $this->settings['level'][ $level_added ]['add'] : array();
		$apply_tag   = isset( $settings['apply_tag'] ) ? $settings['apply_tag'] : array();
		$remove_tag  = isset( $settings['remove_tag'] ) ? $settings['remove_tag'] : array();
		$apply_list  = isset( $settings['apply_list'] ) ? $settings['apply_list'] : array();
		$remove_list = isset( $settings['remove_list'] ) ? $settings['remove_list'] : array();

		$contactApi = FluentCrmApi( 'contacts' );
		if ( ! $level_unconfirmed && ! $level_for_approval ) {
			$contact = $contactApi->getContact( $user->user_email );
			if ( ! $contact ) {
				$data = array(
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
					'email'      => $user->user_email, // requied
					'tags'       => $apply_tag, // tag ids as an array
					'lists'      => $apply_list, // list ids as an array
				);
				$this->create_fluentcrm_contact( $data );
			} else {
				$contact->detachTags( $remove_tag );
				$contact->detachLists( $remove_list );
				$contact->attachTags( $apply_tag );
				$contact->attachLists( $apply_list );
			}
		} elseif ( isset( $_POST['SendMail'] ) ) {
			$contact = $contactApi->getContact( $user->user_email );
			if ( ! $contact ) {
				$data = array(
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
					'email'      => $user->user_email, // requied
					'tags'       => $apply_tag, // tag ids as an array
					'lists'      => $apply_list, // list ids as an array
				);
				$this->create_fluentcrm_contact( $data );
			} else {
				$contact->detachTags( $remove_tag );
				$contact->detachLists( $remove_list );
				$contact->attachTags( $apply_tag );
				$contact->attachLists( $apply_list );
			}
		}
	}

	// FOR APPROVAL or CONFIRMATION
	function ConfirmApproveLevelsTagsHook( $uid = null, $levels = null ) {
		global $WishListMemberInstance;
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$levels             = is_array( $levels ) ? $levels : (array) $levels;
		$level_unconfirmed  = $WishListMemberInstance->LevelUnConfirmed( $levels[0], $uid );
		$level_for_approval = $WishListMemberInstance->LevelForApproval( $levels[0], $uid );

		$settings    = isset( $this->settings['level'][ $levels[0] ]['add'] ) ? $this->settings['level'][ $levels[0] ]['add'] : array();
		$apply_tag   = isset( $settings['apply_tag'] ) ? $settings['apply_tag'] : array();
		$remove_tag  = isset( $settings['remove_tag'] ) ? $settings['remove_tag'] : array();
		$apply_list  = isset( $settings['apply_list'] ) ? $settings['apply_list'] : array();
		$remove_list = isset( $settings['remove_list'] ) ? $settings['remove_list'] : array();

		if ( ! $level_unconfirmed && ! $level_for_approval ) {
			$contactApi = FluentCrmApi( 'contacts' );
			$contact    = $contactApi->getContact( $user->user_email );
			if ( ! $contact ) {
				$data = array(
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
					'email'      => $user->user_email, // requied
					'tags'       => $apply_tag, // tag ids as an array
					'lists'      => $apply_list, // list ids as an array
				);
				$this->create_fluentcrm_contact( $data );
			} else {
				$contact->detachTags( $remove_tag );
				$contact->detachLists( $remove_list );
				$contact->attachTags( $apply_tag );
				$contact->attachLists( $apply_list );
			}
		}
	}

	// WHEN REREGISTERED FROM LEVELS
	function ReregUserTagsHook( $uid, $levels = '' ) {
		global $WishListMemberInstance;
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		// lets check for PPPosts
		$levels = (array) $levels;
		foreach ( $levels as $key => $level ) {
			if ( strrpos( $level, 'U-' ) !== false ) {
				unset( $levels[ $key ] );
			}
		}
		if ( count( $levels ) <= 0 ) {
			return;
		}

		$contactApi = FluentCrmApi( 'contacts' );

		foreach ( $levels as $level ) {
			$settings    = isset( $this->settings['level'][ $level ]['rereg'] ) ? $this->settings['level'][ $level ]['rereg'] : array();
			$apply_tag   = isset( $settings['apply_tag'] ) ? $settings['apply_tag'] : array();
			$remove_tag  = isset( $settings['remove_tag'] ) ? $settings['remove_tag'] : array();
			$apply_list  = isset( $settings['apply_list'] ) ? $settings['apply_list'] : array();
			$remove_list = isset( $settings['remove_list'] ) ? $settings['remove_list'] : array();

			$contact = $contactApi->getContact( $user->user_email );
			if ( ! $contact ) {
				$data = array(
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
					'email'      => $user->user_email, // requied
					'tags'       => $apply_tag, // tag ids as an array
					'lists'      => $apply_list, // list ids as an array
				);
				$this->create_fluentcrm_contact( $data );
			} else {
				$contact->detachTags( $remove_tag );
				$contact->detachLists( $remove_list );
				$contact->attachTags( $apply_tag );
				$contact->attachLists( $apply_list );
			}
		}
	}

	// WHEN REMOVED FROM LEVELS
	function RemoveUserTagsHook( $uid, $removedlevels = '' ) {
		global $WishListMemberInstance;
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		// lets check for PPPosts
		$levels = (array) $removedlevels;
		foreach ( $levels as $key => $level ) {
			if ( strrpos( $level, 'U-' ) !== false ) {
				unset( $levels[ $key ] );
			}
		}
		if ( count( $levels ) <= 0 ) {
			return;
		}

		$contactApi = FluentCrmApi( 'contacts' );
		foreach ( $levels as $level ) {
			$settings    = isset( $this->settings['level'][ $level ]['remove'] ) ? $this->settings['level'][ $level ]['remove'] : array();
			$apply_tag   = isset( $settings['apply_tag'] ) ? $settings['apply_tag'] : array();
			$remove_tag  = isset( $settings['remove_tag'] ) ? $settings['remove_tag'] : array();
			$apply_list  = isset( $settings['apply_list'] ) ? $settings['apply_list'] : array();
			$remove_list = isset( $settings['remove_list'] ) ? $settings['remove_list'] : array();

			$contact = $contactApi->getContact( $user->user_email );
			if ( ! $contact ) {
				$data = array(
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
					'email'      => $user->user_email, // requied
					'tags'       => $apply_tag, // tag ids as an array
					'lists'      => $apply_list, // list ids as an array
				);
				$this->create_fluentcrm_contact( $data );
			} else {
				$contact->detachTags( $remove_tag );
				$contact->detachLists( $remove_list );
				$contact->attachTags( $apply_tag );
				$contact->attachLists( $apply_list );
			}
		}
	}

	// WHEN CANCELLED FROM LEVELS
	function CancelUserTagsHook( $uid, $cancellevels = '' ) {
		global $WishListMemberInstance;
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		// lets check for PPPosts
		$levels = (array) $cancellevels;
		foreach ( $levels as $key => $level ) {
			if ( strrpos( $level, 'U-' ) !== false ) {
				unset( $levels[ $key ] );
			}
		}
		if ( count( $levels ) <= 0 ) {
			return;
		}

		$contactApi = FluentCrmApi( 'contacts' );
		foreach ( $levels as $level ) {
			$settings    = isset( $this->settings['level'][ $level ]['cancel'] ) ? $this->settings['level'][ $level ]['cancel'] : array();
			$apply_tag   = isset( $settings['apply_tag'] ) ? $settings['apply_tag'] : array();
			$remove_tag  = isset( $settings['remove_tag'] ) ? $settings['remove_tag'] : array();
			$apply_list  = isset( $settings['apply_list'] ) ? $settings['apply_list'] : array();
			$remove_list = isset( $settings['remove_list'] ) ? $settings['remove_list'] : array();

			$contact = $contactApi->getContact( $user->user_email );
			if ( ! $contact ) {
				$data = array(
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
					'email'      => $user->user_email, // requied
					'tags'       => $apply_tag, // tag ids as an array
					'lists'      => $apply_list, // list ids as an array
				);
				$this->create_fluentcrm_contact( $data );
			} else {
				$contact->detachTags( $remove_tag );
				$contact->detachLists( $remove_list );
				$contact->attachTags( $apply_tag );
				$contact->attachLists( $apply_list );
			}
		}
	}
}

