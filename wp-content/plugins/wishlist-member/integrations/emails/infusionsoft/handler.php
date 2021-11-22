<?php

namespace WishListMember\Autoresponders;

if ( ! class_exists( '\WLM_Infusionsoft' ) ) {
	global $WishListMemberInstance;
	include_once $WishListMemberInstance->pluginDir . '/extlib/wlm-infusionsoft.php';
}

class Infusionsoft {

	static function subscribe( $email, $level_id ) {
		self::process( $email, $level_id );
	}

	static function unsubscribe( $email, $level_id ) {
		self::process( $email, $level_id, true );
	}

	static function process( $email, $level_id, $unsub = false ) {
		global $WishListMemberInstance;
		$ar    = ( new \WishListMember\Autoresponder( 'infusionsoft' ) )->settings;
		$ifsdk = false;
		// make sure WishListMemberInstance WLM active and infusiosnsoft connection is set
		if ( isset( $WishListMemberInstance ) && class_exists( '\WLM_Infusionsoft' ) ) {
			$machine_name = $WishListMemberInstance->GetOption( 'auto_ismachine' );
			$api_key      = $WishListMemberInstance->GetOption( 'auto_isapikey' );
			$log          = $WishListMemberInstance->GetOption( 'auto_isenable_log' );
			$machine_name = $machine_name ? $machine_name : '';
			$api_key      = $api_key ? $api_key : '';

			$apilogfile = false;
			if ( $log ) {
				$date_now   = date( 'm-d-Y' );
				$apilogfile = $WishListMemberInstance->pluginDir . "/ifs_logs_{$date_now}.csv";
			}

			if ( $api_key && $machine_name ) {
				$ifsdk = new \WLM_Infusionsoft( $machine_name, $api_key, $apilogfile );
			}
		}

		if ( ! $ifsdk ) {
			return;
		}

		$campid  = $ar['isCID'][ $level_id ]; // get the campaign ID of the Membership Level
		$isUnsub = ( $ar['isUnsub'][ $level_id ] == 1 ? true : false ); // check if we will unsubscribe or not

		if ( $campid ) {

			list( $fName, $lName ) = explode( ' ', $WishListMemberInstance->ARSender['name'], 2 ); // split the name into First and Last Name
			$email                 = $WishListMemberInstance->ARSender['email'];

			$contactid = $ifsdk->get_contactid_by_email( $email );

			if ( $unsub ) { // if the Unsubscribe
				// if email is found, remove it from campaign and if it will be unsubscribe once remove from level
				if ( $contactid && $isUnsub ) {
					$res = $ifsdk->remove_followup_sequence( $contactid, $campid );
				}
			} else { // else Subscribe
				// if email is existing, assign it to the campaign
				if ( $contactid ) {
					// optin email first
					$ifsdk->optin_contact_email( $email );
					$res = $ifsdk->assign_followup_sequence( $contactid, $campid );
				} else {
					// if email is new, assign it to the add it to the database
					$carray    = array(
						'Email'     => $email,
						'FirstName' => $fName,
						'LastName'  => $lName,
					);
					$contactid = $ifsdk->create_contact( $carray );
					// if successfully addded, opt-in the contact
					if ( $contactid ) {
						$ifsdk->optin_contact_email( $email );
						$res = $ifsdk->assign_followup_sequence( $contactid, $campid );
					}
				}
			}
		}
	}

	static function __callStatic( $name, $args ) {
		static $ifs;
		global $WishListMemberInstance;

		if(!$ifs) {
			$ifs = new Infusionsoft_Tags_Handler();
		}

		// if the method exists then call it
		if ( $ifs->ifsdk && method_exists( $ifs, $name ) ) {
			return call_user_func_array( array( $ifs, $name ), $args );
		}
	}
}


class Infusionsoft_Tags_Handler {
	private $machine_name = '';
	private $api_key      = '';
	public $ifsdk         = null;
	private $log          = false;

	function __construct() {
		global $WishListMemberInstance;
		// make sure that WLM active and infusiosnsoft connection is set
		if ( isset( $WishListMemberInstance ) && class_exists( '\WLM_Infusionsoft' ) ) {
			$this->machine_name = $WishListMemberInstance->GetOption( 'auto_ismachine' );
			$this->api_key      = $WishListMemberInstance->GetOption( 'auto_isapikey' );
			$this->log          = $WishListMemberInstance->GetOption( 'auto_isenable_log' );
			$this->machine_name = $this->machine_name ? $this->machine_name : '';
			$this->api_key      = $this->api_key ? $this->api_key : '';

			$apilogfile = false;
			if ( $this->log ) {
				$date_now   = date( 'm-d-Y' );
				$apilogfile = $WishListMemberInstance->pluginDir . "/ifs_logs_{$date_now}.csv";
			}

			if ( $this->api_key && $this->machine_name ) {
				$this->ifsdk = new \WLM_Infusionsoft( $this->machine_name, $this->api_key, $apilogfile );
			}
		}
	}

	function add_ifs_field( $custom_fields, $userid ) {
		global $WishListMemberInstance;

		if ( ! isset( $WishListMemberInstance ) ) {
			return $custom_fields;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return $custom_fields; }

		$contactid = $WishListMemberInstance->Get_UserMeta( $userid, 'wlminfusionsoft_contactid' );
		if ( !$contactid ) {
			$contactid = get_user_meta( $userid, 'wlifcon_contactid', true ); // wlmis contactid
		}
		$custom_fields['wlminfusionsoft_contactid'] = array(
			'type'       => 'text', // hidden, select, textarea, checkbox, etc
			'label'      => 'Infusionsoft Contact ID',
			// 'description' => 'Description',
			'attributes' => array(
				'type'  => 'text', // hidden, select, textarea, checkbox, etc
				'name'  => 'wlminfusionsoft_contactid', // same as index above
				// 'other attributes' => 'value',
				'value' => $contactid,
				// more attributes if needed
			),
		);
		return $custom_fields;
	}

	function save_ifs_field( $data ) {
		global $WishListMemberInstance;
		if ( ! isset( $WishListMemberInstance ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return; }
		if ( ! isset( $data['userid'] ) ) {
			return;
		}

		$user_custom_fields = isset( $data['customfields'] ) ? $data['customfields'] : array();
		if ( ! isset( $user_custom_fields['wlminfusionsoft_contactid'] ) ) {
			return;
		}
		$wlminfusionsoft_contactid = $user_custom_fields['wlminfusionsoft_contactid'] ? (int) trim( $user_custom_fields['wlminfusionsoft_contactid'] ) : '';

		$WishListMemberInstance->Update_UserMeta( $data['userid'], 'wlminfusionsoft_contactid', $wlminfusionsoft_contactid );
	}

	function ProfileForm( $user ) {
		global $WishListMemberInstance, $pagenow;
		if ( ! isset( $WishListMemberInstance ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return; }
		$user_id = $user;
		if ( is_object( $user ) ) {
			$user_id = $user->ID;
		}
		if ( $pagenow != 'profile.php' && $pagenow != 'user-edit.php' ) {
			return;
		}

		$contactid = $WishListMemberInstance->Get_UserMeta( $user_id, 'wlminfusionsoft_contactid' );
		if ( !$contactid ) {
			$contactid = get_user_meta( $user_id, 'wlifcon_contactid', true ); // wlmis contactid
		}
		echo '<h3>Infusionsoft Info</h3>';
		echo '<table class="form-table">';
		echo '<tbody>';
		echo '<tr>';
		echo '<th><label for="wlminfusionsoft_contactid">Infusionsoft Contact ID</label></th>';
		echo '<td>';
		echo '<input type="text" name="wlminfusionsoft_contactid" id="wlminfusionsoft_contactid" value="' . $contactid . '" class="regular-text">';
		echo '</td>';
		echo '</tr>';
		echo '</tbody>';
		echo '</table>';
	}

	function UpdateProfile( $user ) {
		global $WishListMemberInstance;
		if ( ! isset( $WishListMemberInstance ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return; }
		$user_id = $user;
		if ( is_object( $user ) ) {
			$user_id = $user->ID;
		}

		if ( isset( $_POST['wlminfusionsoft_contactid'] ) ) {
			$WishListMemberInstance->Update_UserMeta( $user_id, 'wlminfusionsoft_contactid', (int) trim( $_POST['wlminfusionsoft_contactid'] ) );
		}
	}

	function generateContactId( $uid ) {
		if ( ! $this->ifsdk || ! $this->ifsdk->is_api_connected() ) {
			return null;
		}

		global $WishListMemberInstance;
		$contactid = get_user_meta( $uid, 'wlifcon_contactid', true ); // wlmis contactid

		if ( ! $contactid ) {
			$user_info = get_userdata( $uid );
			if ( ! $user_info ) {
				return null;
			}

			$email = $user_info->user_email;
			if ( $email && filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				$contactid = $this->ifsdk->get_contactid_by_email( $email );
				if ( ! $contactid ) {
					$user      = array(
						'Email'     => $email,
						'FirstName' => $user_info->user_firstname,
						'LastName'  => $user_info->user_lastname,
					);
					$contactid = $this->ifsdk->create_contact( $user, 'Added Via WLM INF AR Integration API.' );
				}
			} else {
				return null;
			}
		}

		if ( $contactid ) {
			$WishListMemberInstance->Update_UserMeta( $uid, 'wlminfusionsoft_contactid', $contactid );
		} else {
			$contactid = false; // make sure that contactid is false
		}

		return $contactid;
	}

	function processTags( $levels, $action, $contactid = null, $uid = null ) {
		if ( ! $this->ifsdk || ! $this->ifsdk->is_api_connected() ) {
			return array(
				'errstr' => 'Unable to process tags. No API Connection.',
				'errno'  => 1,
			);
		}

		global $WishListMemberInstance;
					$ar = $WishListMemberInstance->GetOption( 'Autoresponders' );
		$this->settings = isset( $ar['infusionsoft'] ) ? $ar['infusionsoft'] : false;
		$levels         = (array) $levels;
		if ( count( $levels ) <= 0 ) {
			return array(
				'errstr' => 'No Levels Found',
				'errno'  => 1,
			);// no levels, no need to continue
		}

		if ( ! $contactid ) {
			$contactid = $WishListMemberInstance->Get_UserMeta( $uid, 'wlminfusionsoft_contactid' );
			// get the contactid if not set
			if ( ! $contactid ) {
				$contactid = $this->generateContactId( $uid );
				if ( $contactid === null ) {
					 return array(
						 'errstr' => 'Theres a problem with userid, email or api connection.',
						 'errno'  => 1,
					 );
				}
			}
		}

		if ( $contactid ) {

			if ( $action == 'new' || $action == 'add' ) {
				$istags_add_app = $this->settings['istags_add_app'];
				$istags_add_rem = $this->settings['istags_add_rem'];
			} elseif ( $action == 'remove' ) {
				$istags_add_app = $this->settings['istags_remove_app'];
				$istags_add_rem = $this->settings['istags_remove_rem'];
			} elseif ( $action == 'cancel' ) {
				$istags_add_app = $this->settings['istags_cancelled_app'];
				$istags_add_rem = $this->settings['istags_cancelled_rem'];
			} elseif ( $action == 'uncancel' ) {
				$istags_add_app = $this->settings['istags_uncancelled_app'];
				$istags_add_rem = $this->settings['istags_uncancelled_rem'];
			} elseif ( $action == 'delete' ) {
				$istags_add_app = $this->settings['istags_remove_app'];
				$istags_add_rem = $this->settings['istags_remove_rem'];
			}

			if ( $istags_add_app ) {
				$istags_add_app = wlm_maybe_unserialize( $istags_add_app );
			} else {
				$istags_add_app = array();
			}

			if ( $istags_add_rem ) {
				$istags_add_rem = wlm_maybe_unserialize( $istags_add_rem );
			} else {
				$istags_add_rem = array();
			}

			// add the tags for each level
			foreach ( (array) $levels as $level ) {
				// add the contact to a tag/group
				if ( isset( $istags_add_app[ $level ] ) ) {
					foreach ( $istags_add_app[ $level ] as $k => $val ) {
						$ret = $this->ifsdk->tag_contact( $contactid, $val );
						if ( isset( $ret['errno'] ) ) {
							return $ret;
						}
					}
				}

				// remove the contact from tag/group
				if ( isset( $istags_add_rem[ $level ] ) ) {
					foreach ( $istags_add_rem[ $level ] as $k => $val ) {
						$ret = $this->ifsdk->untag_contact( $contactid, $val );
						if ( isset( $ret['errno'] ) ) {
							return $ret;
						}
					}
				}
			}
		} else {
			return array(
				'errstr' => 'No Contact ID',
				'errno'  => 1,
			);
		}

		return true; // success
	}

	function ifarAddQueue( $data, $process = true ) {
		$WishlistAPIQueueInstance = new \WishlistAPIQueue();
		$qname                    = 'infusionsoftar_' . time();
		$data                     = wlm_maybe_serialize( $data );
		$WishlistAPIQueueInstance->add_queue( $qname, $data, 'For Queueing' );
		if ( $process ) {
			$this->ifarProcessQueue();
		}
	}

	function ifarProcessQueue( $recnum = 10, $tries = 5 ) {
		if ( ! $this->ifsdk || ! $this->ifsdk->is_api_connected() ) {
			return;
		}
		$WishlistAPIQueueInstance = new \WishlistAPIQueue();
		$last_process             = get_option( 'WLM_InfusionsoftARAPI_LastProcess' );
		$current_time             = time();
		$tries                    = $tries > 1 ? (int) $tries : 5;
		$error                    = false;
		// lets process every 10 seconds
		if ( ! $last_process || ( $current_time - $last_process ) > 10 ) {
			$queues = $WishlistAPIQueueInstance->get_queue( 'infusionsoftar', $recnum, $tries, 'tries,name' );
			foreach ( $queues as $queue ) {
				$data = wlm_maybe_unserialize( $queue->value );
				if ( $data['action'] == 'new' ) {
					$res = $this->NewUserTagsHook( $data['uid'], $data['data'] );
				} elseif ( $data['action'] == 'add' ) {
					$res = $this->AddUserTagsHook( $data['uid'], $data['addlevels'] );
				} elseif ( $data['action'] == 'remove' ) {
					$res = $this->RemoveUserTagsHook( $data['uid'], $data['removedlevels'] );
				} elseif ( $data['action'] == 'cancel' ) {
					$res = $this->CancelUserTagsHook( $data['uid'], $data['cancellevels'] );
				} elseif ( $data['action'] == 'uncancel' ) {
					$res = $this->UnCancelUserTagsHook( $data['uid'], $data['uncancellevels'] );
				} elseif ( $data['action'] == 'delete' ) {
					$res = $this->DeleteUserTagsHook( $data['contactid'], $data['levels'] );
				}

				if ( isset( $res['errstr'] ) ) {
					$res['error'] = strip_tags( $res['errstr'] );
					$res['error'] = str_replace( array( "\n", "\t", "\r" ), '', $res['error'] );
					$d            = array(
						'notes' => "{$res['errno']}:{$res['error']}",
						'tries' => $queue->tries + 1,
					);
					$WishlistAPIQueueInstance->update_queue( $queue->ID, $d );
					$error = true;
				} else {
					$WishlistAPIQueueInstance->delete_queue( $queue->ID );
					$error = false;
				}
			}
			// save the last processing time
			if ( $error ) {
				$current_time = time();
				if ( $last_process ) {
					update_option( 'WLM_InfusionsoftARAPI_LastProcess', $current_time );
				} else {
					add_option( 'WLM_InfusionsoftARAPI_LastProcess', $current_time );
				}
			}
		}
	}

	function ConfirmApproveLevelsTagsHook( $uid = null, $level = null ) {
		global $WishListMemberInstance;

		$user = get_userdata( $uid );

		$udata = array(
			'username'  => $user->user_login,
			'firstname' => $user->user_firstname,
			'lastname'  => $user->user_lastname,
			'email'     => $user->user_email,
			'wpm_id'    => $level[0],
		);

		$level_unconfirmed = false;
		if ( $WishListMemberInstance->LevelUnConfirmed( $level[0], $uid ) ) {
			$level_unconfirmed = true;
		}

		$level_for_approval = false;
		if ( $WishListMemberInstance->LevelForApproval( $level[0], $uid ) ) {
			$level_for_approval = true;
		}

		$data = array(
			'uid'    => $uid,
			'action' => 'new',
			'data'   => $udata,
		);

		if ( $level_unconfirmed || $level_for_approval ) {
			// Don't add the data into the queue if the level's status is not active
		} else {
			$this->ifarAddQueue( $data );
		}
	}

	// FOR NEW USERS
	function NewUserTagsHookQueue( $uid = null, $udata = null ) {

		global $WishListMemberInstance;

		$level_unconfirmed = false;
		if ( $WishListMemberInstance->LevelUnConfirmed( $udata['wpm_id'], $uid ) ) {
			$level_unconfirmed = true;
		}

		$level_for_approval = false;
		if ( $WishListMemberInstance->LevelForApproval( $udata['wpm_id'], $uid ) ) {
			$level_for_approval = true;
		}

		$data = array(
			'uid'    => $uid,
			'action' => 'new',
			'data'   => $udata,
		);

		// Part of the Fix for issue where Add To levels aren't being processed.
		$user = get_userdata( $uid );
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			// Don't add the data into the queue if it's from a temp account
		} elseif ( $level_unconfirmed || $level_for_approval ) {
			// Don't add the data into the queue if the level's status is not active
		} else {
			$this->ifarAddQueue( $data );
		}

	}

	function NewUserTagsHook( $uid = null, $data = null ) {
		$tempacct = $data['email'] == 'temp_' . md5( $data['orig_email'] );
		if ( $tempacct ) {
			return; // if temp account used by sc, do not process
		}
		$levels = (array) $data['wpm_id'];

		return $this->processTags( $levels, 'new', null, $uid );
	}

	// WHEN ADDED TO LEVELS
	function AddUserTagsHookQueue( $uid, $addlevels = '' ) {

		global $WishListMemberInstance;

		$level_unconfirmed = false;
		if ( $WishListMemberInstance->LevelUnConfirmed( $addlevels[0], $uid ) ) {
			$level_unconfirmed = true;
		}

		$level_for_approval = false;
		if ( $WishListMemberInstance->LevelForApproval( $addlevels[0], $uid ) ) {
			$level_for_approval = true;
		}

		$data = array(
			'uid'       => $uid,
			'action'    => 'add',
			'addlevels' => $addlevels,
		);

		// If from registration then don't don't process if the $addlevels is
		// the same level the user registered to. This is already processed by NewUserTagsQueue func.
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'wpm_register' ) {
			if ( $_POST['wpm_id'] == $addlevels[0] ) {
				return;
			}
		}

		// Fix for issue where Add To levels aren't being processed.
		// If the data is from a temp account then add it to the queue API and don't process it for now.
		$user = get_userdata( $uid );
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			$this->ifarAddQueue( $data, 0 );
		} elseif ( isset( $_POST['SendMail'] ) ) {
			// This elseif condition fixes the issue where members who are added via
			// WLM API aren't being processed by the Infusionsoft Autoresponder Integration.
			$this->ifarAddQueue( $data, 0 );
		} elseif ( $level_unconfirmed || $level_for_approval ) {
			// Don't add the data into the queue if the level's status is not active
		} else {
			$this->ifarAddQueue( $data );
		}
	}

	function AddUserTagsHook( $uid, $newlevels = '' ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return array(
				'errstr' => 'Invalid User ID.',
				'errno'  => 1,
			);
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$levels = (array) $newlevels;
		return $this->processTags( $levels, 'add', null, $uid );
	}

	// WHEN REMOVED FROM LEVELS
	function RemoveUserTagsHookQueue( $uid, $removedlevels = '' ) {
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

		$data = array(
			'uid'           => $uid,
			'action'        => 'remove',
			'removedlevels' => $removedlevels,
		);
		$this->ifarAddQueue( $data );
	}

	function RemoveUserTagsHook( $uid, $removedlevels = '' ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return array(
				'errstr' => 'Invalid User ID.',
				'errno'  => 1,
			);
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$levels = (array) $removedlevels;
		return $this->processTags( $levels, 'remove', null, $uid );
	}

	// WHEN CANCELLED FROM LEVELS
	function CancelUserTagsHookQueue( $uid, $cancellevels = '' ) {
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

		$data = array(
			'uid'          => $uid,
			'action'       => 'cancel',
			'cancellevels' => $levels,
		);
		$this->ifarAddQueue( $data );
	}

	function CancelUserTagsHook( $uid, $removedlevels = '' ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return array(
				'errstr' => 'Invalid User ID.',
				'errno'  => 1,
			);
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$levels = (array) $removedlevels;
		return $this->processTags( $levels, 'cancel', null, $uid );
	}

	// WHEN UNCANCELLED FROM LEVELS
	function UnCancelUserTagsHookQueue( $uid, $uncancellevels = '' ) {
		// lets check for PPPosts
		$levels = (array) $uncancellevels;
		foreach ( $levels as $key => $level ) {
			if ( strrpos( $level, 'U-' ) !== false ) {
				unset( $levels[ $key ] );
			}
		}
		if ( count( $levels ) <= 0 ) {
			return;
		}

		$data = array(
			'uid'          => $uid,
			'action'       => 'uncancel',
			'uncancellevels' => $levels,
		);
		$this->ifarAddQueue( $data );
	}

	function UnCancelUserTagsHook( $uid, $uncancellevels = '' ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return array(
				'errstr' => 'Invalid User ID.',
				'errno'  => 1,
			);
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$levels = (array) $uncancellevels;
		return $this->processTags( $levels, 'uncancel', null, $uid );
	}

	// WHEN DELETED FROM LEVELS
	function DeleteUserHookQueue( $uid ) {
		if ( ! $this->ifsdk || ! $this->ifsdk->is_api_connected() ) {
			return;
		}

		global $WishListMemberInstance;
		$levels = $WishListMemberInstance->GetMembershipLevels( $uid );
		foreach ( $levels as $key => $lvl ) {
			if ( strpos( $lvl, 'U-' ) !== false ) {
				unset( $levels[ $key ] );
			}
		}
		if ( ! is_array( $levels ) || count( $levels ) <= 0 ) {
			return; // lets return if no level was found
		}

		$contactid = $WishListMemberInstance->Get_UserMeta( $uid, 'wlminfusionsoft_contactid' );

		if ( ! $contactid ) { // if no contactid

			$user_info = get_userdata( $uid );
			if ( ! $user_info ) {
				return; // invalid user
			}
			$email = $user_info->user_email;

			if ( ! $contactid ) {
				if ( $email && filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					$contactid = $this->ifsdk->get_contactid_by_email( $email );
					// since we are deleting the user, we wont be adding it on IFS also
				}
			}
			if ( ! $contactid ) {
				$contactid = get_user_meta( $uid, 'wlifcon_contactid', true ); // wlmis contactid
			}
		}
		if ( ! $contactid ) {
			return; // lets return if no level was found
		}

		$data = array(
			'uid'       => $uid,
			'contactid' => $contactid,
			'action'    => 'delete',
			'levels'    => $levels,
		);

		$this->ifarAddQueue( $data );

		return;
	}

	function DeleteUserTagsHook( $contactid, $levels = array() ) {
		$levels = (array) $levels;
		return $this->processTags( $levels, 'remove', $contactid, null );
	}

}
