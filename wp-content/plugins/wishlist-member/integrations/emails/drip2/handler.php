<?php

namespace WishListMember\Autoresponders;

if ( ! class_exists( '\WLM_Drip_Api' ) ) {
	global $WishListMemberInstance;
	include_once $WishListMemberInstance->pluginDir . '/extlib/wlm_drip/Drip_API.class.php';
}

class Drip2 {
	static function __callStatic( $name, $args ) {
		$interface = self::_interface();
		if ( $interface->api() ) {
			call_user_func_array( array( $interface, $name ), $args );
		}
	}

	static function _interface() {
		static $interface;
		if ( ! $interface ) {
			$interface = new Drip2_Interface();
		}
		return $interface;
	}
}

class Drip2_Interface {
	private $drip2_settings = '';
	private $drip_api       = null;

	function __construct() {
		global $WishListMemberInstance;
		// make sure that WLM active and infusiosnsoft connection is set
		if ( isset( $WishListMemberInstance ) && class_exists( '\WLM_Drip_Api' ) ) {

			$this->drip2_settings = ( new \WishListMember\Autoresponder( 'drip2' ) )->settings;

			if ( empty( $this->drip2_settings ) ) {
				$this->drip2_settings = $WishListMemberInstance->GetOption( 'drip2_settings' );
			}

			// initilize drip api connection
			if ( $this->drip2_settings && isset( $this->drip2_settings['apitoken'] ) && ! empty( $this->drip2_settings['apitoken'] ) ) {
				$this->drip_api = new \WLM_Drip_Api( $this->drip2_settings['apitoken'] );
			}
		}
	}

	function api() {
		return $this->drip_api;
	}

	function fetch_subscriber( $account_id, $email ) {
		$params = array(
			'account_id' => $account_id,
			'email'      => $email,
		);
		try {
			$ret = $this->drip_api->fetch_subscriber( $params );
		} catch ( \Exception $e ) {
			$ret = false;
		}
		return $ret;
	}

	function create_or_update_subscriber( $account_id, $data ) {
		$params                  = array();
		$params['account_id']    = $account_id;
		$params['email']         = $data['user_email'];
		$params['custom_fields'] = array(
			'name'       => "{$data['first_name']} {$data['last_name']}",
			'first_name' => $data['first_name'],
			'last_name'  => $data['last_name'],
			'username'   => $data['username'],
		);
		try {
			$ret = $this->drip_api->create_or_update_subscriber( $params );
			if ( $ret === false ) {
				return array(
					'errstr' => 'Error:' . $this->drip_api->get_error_message(),
					'errno'  => $this->drip_api->get_error_code(),
				);
			}
		} catch ( \Exception $e ) {
			return array(
				'errstr' => 'Error:' . $e->getMessage(),
				'errno'  => 1,
			);
		}
		return true;
	}

	function tag_untag_subscriber( $account_id, $email, $tags, $apply = true ) {
		if ( count( $tags ) <= 0 ) {
			return true;
		}
		$errstr = '';
		$errno  = 0;
		foreach ( $tags as $tag ) {
			$tag = trim($tag);
			if(!$tag) {
				continue;
			}
			try {
				if ( $apply ) {
					$ret = $this->drip_api->tag_subscriber(
						array(
							'account_id' => $account_id,
							'email'      => $email,
							'tag'        => $tag,
						)
					);
				} else {
					$ret = $this->drip_api->untag_subscriber(
						array(
							'account_id' => $account_id,
							'email'      => $email,
							'tag'        => $tag,
						)
					);
				}
				if ( $ret !== true ) {
						$errstr = $this->drip_api->get_error_message();
						$errno  = $this->drip_api->get_error_code();
						break;
				}
			} catch ( \Exception $e ) {
				$errstr = $e->getMessage();
				$errno  = 1;
				break;
			}
		}
		if ( ! empty( $errstr ) ) {
			return array(
				'errstr' => $errstr,
				'errno'  => $errno,
			);
		}
		return true;
	}

	function create_event( $account_id, $email, $action, $levelid ) {
		global $WishListMemberInstance;
		$events = array(
			'add'    => 'Added to level',
			'cancel' => 'Cancelled from level',
			'rereg'  => 'Re-registered to level',
			'remove' => 'Removed from level',
		);
		if ( ! isset( $events[ $action ] ) ) {
			return array(
				'errstr' => 'Invalid action for event',
				'errno'  => 1,
			);
		}
		$wpm_levels = $WishListMemberInstance->GetOption( 'wpm_levels' );
		$level_name = isset( $wpm_levels[ $levelid ] ) ? $wpm_levels[ $levelid ]['name'] : 'InvalidLevel';
		$params     = array(
			'account_id' => $account_id,
			'email'      => $email,
			'action'     => $events[ $action ],
			'properties' => array(
				'source'     => 'wishlist_member',
				'level_id'   => $levelid,
				'level_name' => $level_name,
			),

		);
		$errstr = '';
		$errno  = 0;
		try {
			$ret = $this->drip_api->record_event( $params );
			if ( $ret !== true ) {
				$errstr = $this->drip_api->get_error_message();
				$errno  = $this->drip_api->get_error_code();
			}
		} catch ( \Exception $e ) {
			$errstr = $e->getMessage();
			$errno  = 1;
		}
		if ( ! empty( $errstr ) ) {
			return array(
				'errstr' => $errstr,
				'errno'  => $errno,
			);
		}
		return true;
	}

	function UpdateProfile( $user_id, $old_data ) {
		$user_data = get_userdata( $user_id );
		if ( ! $user_data ) {
			return;
		}
		$settings = $this->drip2_settings;
		unset( $settings['apitoken'] );

		$account_id = isset( $settings['account'] ) ? $settings['account'] : false;
		$account_id = ! empty( $account_id ) ? $account_id : false;
		if ( ! $account_id ) {
			return;
		}

		$sub = $this->fetch_subscriber( $account_id, $user_data->user_email );
		if ( $sub ) {
			$email_changed = false;
			$cfields       = array();
			$param         = array();
			if ( isset( $sub['custom_fields']['first_name'] ) ) {
				if ( $sub['custom_fields']['first_name'] != $user_data->first_name ) {
					$cfields['first_name'] = $user_data->first_name;
				}
			} else {
				$cfields['first_name'] = $user_data->first_name;
			}
			if ( isset( $sub['custom_fields']['last_name'] ) ) {
				if ( $sub['custom_fields']['last_name'] != $user_data->last_name ) {
					$cfields['last_name'] = $user_data->last_name;
				}
			} else {
				$cfields['last_name'] = $user_data->last_name;
			}
			if ( isset( $sub['custom_fields']['name'] ) ) {
				if ( $sub['custom_fields']['name'] != "{$user_data->first_name} {$user_data->last_name}" ) {
					$cfields['name'] = "{$user_data->first_name} {$user_data->last_name}";
				}
			} else {
				$cfields['name'] = "{$user_data->first_name} {$user_data->last_name}";
			}
			if ( isset( $sub['custom_fields']['username'] ) ) {
				if ( $sub['custom_fields']['username'] != $user_data->user_login ) {
					$cfields['username'] = $user_data->user_login;
				}
			} else {
				$cfields['username'] = $user_data->user_login;
			}

			if ( $old_data->user_email != $user_data->user_email ) {
				$email_changed = true;
			}

			if ( count( $cfields ) > 0 || $email_changed ) {
				$params['account_id'] = $account_id;
				$params['email']      = $old_data->user_email;
				if ( $email_changed ) {
					$params['new_email'] = $user_data->user_email;
				}
				if ( count( $cfields ) > 0 ) {
					$params['custom_fields'] = $cfields;
				}
				$msg = '';
				try {
					$ret = $this->drip_api->create_or_update_subscriber( $params );
					if ( $ret === false ) {
						$msg = 'Drip2Error:' . $this->drip_api->get_error_code() . '-' . $this->drip_api->get_error_message();
					}
				} catch ( \Exception $e ) {
					$msg = 'Drip2Error:' . $e->getMessage();
				}
				if ( ! empty( $msg ) ) {
					trigger_error( $msg );
				}
			}
		}
	}

	function processTags( $levels, $action, $data ) {
		global $WishListMemberInstance;
		if ( ! isset( $WishListMemberInstance ) ) {
			return array(
				'errstr' => 'WishList Member instance not found.',
				'errno'  => 1,
			);
		}
		if ( ! $this->drip_api ) {
			return array(
				'errstr' => 'Unable to process tags. No API Connection.',
				'errno'  => 1,
			);
		}
		$levels = (array) $levels;
		if ( count( $levels ) <= 0 ) {
			return array(
				'errstr' => 'No Levels Found',
				'errno'  => 1,
			);// no levels, no need to continue
		}
		if ( ! isset( $data['user_email'] ) || empty( $data['user_email'] ) ) {
			return array(
				'errstr' => 'Email address not found',
				'errno'  => 1,
			);
		}
		if ( ! in_array( $action, array( 'add', 'cancel', 'rereg', 'remove' ) ) ) {
			return array(
				'errstr' => 'Invalid action',
				'errno'  => 1,
			);
		}

		$account_id = isset( $this->drip2_settings['account'] ) ? $this->drip2_settings['account'] : false;
		$account_id = ! empty( $account_id ) ? $account_id : false;
		if ( ! $account_id ) {
			return array(
				'errstr' => 'No account configured',
				'errno'  => 1,
			);
		}

		$record_checked = false; // marker
		// add the tags for each level
		foreach ( (array) $levels as $level ) {

			$apply_tags   = isset( $this->drip2_settings[ $level ][ $action ]['apply_tag'] ) ? $this->drip2_settings[ $level ][ $action ]['apply_tag'] : false;
			$apply_tags   = ! empty( $apply_tags ) ? $apply_tags : false;
			$remove_tag   = isset( $this->drip2_settings[ $level ][ $action ]['remove_tag'] ) ? $this->drip2_settings[ $level ][ $action ]['remove_tag'] : false;
			$remove_tag   = ! empty( $remove_tag ) ? $remove_tag : false;
			$record_event = isset( $this->drip2_settings[ $level ][ $action ]['record_event'] ) ? $this->drip2_settings[ $level ][ $action ]['record_event'] : false;
			if ( ! $apply_tags && ! $remove_tag && ! $record_event ) {
				continue; // skip the rest of the loop
			}

			if ( ! $record_checked ) { // lets check the email if its in drip
				$x = $this->fetch_subscriber( $account_id, $data['user_email'] );
				if ( ! $x ) {
					// if email has no record, try and create one
					$x = $this->create_or_update_subscriber( $account_id, $data );
					if ( $x !== true ) {
						return $x; // an error occured when adding record to drip
					}
				}
				$record_checked = true;
			}

			// now we can add or remove tags to record
			if ( $apply_tags ) {
				$ret = $this->tag_untag_subscriber( $account_id, $data['user_email'], $apply_tags );
				if ( $ret !== true ) {
					return $ret;
				}
			}

			if ( $remove_tag ) {
				$ret = $this->tag_untag_subscriber( $account_id, $data['user_email'], $remove_tag, false );
				if ( $ret !== true ) {
					return $ret;
				}
			}

			if ( $record_event ) {
				$ret = $this->create_event( $account_id, $data['user_email'], $action, $level );
				if ( $ret !== true ) {
					return $ret;
				}
			}
		}

		return true; // success
	}

	function AddQueue( $data, $process = true ) {
		$WishlistAPIQueueInstance = new \WishlistAPIQueue();
		$qname                    = 'drip2ar' . time();
		$data                     = wlm_maybe_serialize( $data );
		$WishlistAPIQueueInstance->add_queue( $qname, $data, 'For Queueing' );
		if ( $process ) {
			$this->ProcessQueue();
		}
	}

	function ProcessQueue( $recnum = 10, $tries = 5 ) {
		if ( ! $this->drip_api ) {
			return;
		}
		$WishlistAPIQueueInstance = new \WishlistAPIQueue();
		$last_process             = get_option( 'WLM_AUTORESPONDER_DRIP2API_LastProcess' );
		$current_time             = time();
		$tries                    = $tries > 1 ? (int) $tries : 5;
		$error                    = false;
		// lets process every 10 seconds
		if ( ! $last_process || ( $current_time - $last_process ) > 10 ) {
			$queues = $WishlistAPIQueueInstance->get_queue( 'drip2ar', $recnum, $tries, 'tries,name' );
			foreach ( $queues as $queue ) {
				$data = wlm_maybe_unserialize( $queue->value );
				if ( $data['action'] == 'new' ) {
					$res = $this->NewUserTagsHook( $data['uid'], $data['levels'], $data['data'] );
				} elseif ( $data['action'] == 'add' ) {
					$res = $this->AddUserTagsHook( $data['uid'], $data['levels'], $data['data'] );
				} elseif ( $data['action'] == 'remove' ) {
					$res = $this->RemoveUserTagsHook( $data['uid'], $data['levels'], $data['data'] );
				} elseif ( $data['action'] == 'cancel' ) {
					$res = $this->CancelUserTagsHook( $data['uid'], $data['levels'], $data['data'] );
				} elseif ( $data['action'] == 'rereg' ) {
					$res = $this->ReregUserTagsHook( $data['uid'], $data['levels'], $data['data'] );
				} elseif ( $data['action'] == 'delete' ) {
					$res = $this->DeleteUserTagsHook( $data['uid'], $data['levels'], $data['data'] );
				}

				if ( isset( $res['errstr'] ) ) {
					$res['error'] = strip_tags( $res['errstr'] );
					$res['error'] = str_replace( array( "\n", "\t", "\r" ), '', $res['error'] );
					$res['errno'] = isset( $res['errno'] ) ? $res['errno'] : 1;
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
					update_option( 'WLM_AUTORESPONDER_DRIP2API_LastProcess', $current_time );
				} else {
					add_option( 'WLM_AUTORESPONDER_DRIP2API_LastProcess', $current_time );
				}
			}
		}
	}

	// FOR NEW USERS
	function NewUserTagsHookQueue( $uid = null, $udata = null ) {
		// Part of the Fix for issue where Add To levels aren't being processed.
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		// Don't add the data into the queue if it's from a temp account
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$udata['first_name'] = $user->first_name;
		$udata['last_name']  = $user->last_name;
		$udata['user_email'] = $user->user_email;
		$udata['username']   = $user->user_login;
		$data                = array(
			'uid'    => $uid,
			'action' => 'new',
			'levels' => (array) $udata['wpm_id'],
			'data'   => $udata,
		);
		$this->AddQueue( $data );
	}

	function NewUserTagsHook( $uid, $levels, $data ) {
		$tempacct = $data['email'] == 'temp_' . md5( $data['orig_email'] );
		if ( $tempacct ) {
			return; // if temp account used by sc, do not process
		}
		return $this->processTags( $levels, 'add', $data );
	}

	// WHEN ADDED TO LEVELS
	function AddUserTagsHookQueue( $uid, $addlevels = '' ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}

		$udata               = array();
		$udata['first_name'] = $user->first_name;
		$udata['last_name']  = $user->last_name;
		$udata['user_email'] = $user->user_email;
		$udata['username']   = $user->user_login;
		$data                = array(
			'uid'    => $uid,
			'action' => 'add',
			'levels' => $addlevels,
			'data'   => $udata,
		);
		// Fix for issue where Add To levels aren't being processed.
		// If the data is from a temp account then add it to the queue API and don't process it for now.
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			$this->AddQueue( $data, 0 );
		} elseif ( isset( $_POST['SendMail'] ) ) {
			// This elseif condition fixes the issue where members who are added via
			// WLM API aren't being processed by the Infusionsoft Autoresponder Integration.
			$this->AddQueue( $data, 0 );
		} else {
			$this->AddQueue( $data );
		}
	}

	function AddUserTagsHook( $uid, $levels, $data ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		// make sure that info are updated
		$data['first_name'] = $user->first_name;
		$data['last_name']  = $user->last_name;
		$data['user_email'] = $user->user_email;
		$data['username']   = $user->user_login;
		$levels             = (array) $levels;
		return $this->processTags( $levels, 'add', $data );
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
			'uid'    => $uid,
			'action' => 'remove',
			'levels' => $levels,
			'data'   => array(),
		);
		$this->AddQueue( $data );
	}

	function RemoveUserTagsHook( $uid, $levels, $data ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$data['first_name'] = $user->first_name;
		$data['last_name']  = $user->last_name;
		$data['user_email'] = $user->user_email;
		$data['username']   = $user->user_login;
		$levels             = (array) $levels;
		return $this->processTags( $levels, 'remove', $data );
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
			'uid'    => $uid,
			'action' => 'cancel',
			'levels' => $levels,
			'data'   => array(),
		);
		$this->AddQueue( $data );
	}

	function CancelUserTagsHook( $uid, $levels, $data ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$data['first_name'] = $user->first_name;
		$data['last_name']  = $user->last_name;
		$data['user_email'] = $user->user_email;
		$data['username']   = $user->user_login;
		$levels             = (array) $levels;
		return $this->processTags( $levels, 'cancel', $data );
	}

	// WHEN REREGISTERED FROM LEVELS
	function ReregUserTagsHookQueue( $uid, $levels = '' ) {
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

		$data = array(
			'uid'    => $uid,
			'action' => 'rereg',
			'levels' => $levels,
			'data'   => array(),
		);
		$this->AddQueue( $data );
	}

	function ReregUserTagsHook( $uid, $levels, $data ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$data['first_name'] = $user->first_name;
		$data['last_name']  = $user->last_name;
		$data['user_email'] = $user->user_email;
		$data['username']   = $user->user_login;
		$levels             = (array) $levels;
		return $this->processTags( $levels, 'rereg', $data );
	}

	// WHEN DELETED FROM LEVELS
	function DeleteUserHookQueue( $uid ) {
		if ( ! $this->drip_api ) {
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

		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}

		$udata               = array();
		$udata['first_name'] = $user->first_name;
		$udata['last_name']  = $user->last_name;
		$udata['user_email'] = $user->user_email;
		$udata['username']   = $user->user_login;
		$data                = array(
			'uid'    => $uid,
			'action' => 'delete',
			'levels' => $levels,
			'data'   => $udata,
		);
		$this->AddQueue( $data );
		return;
	}

	function DeleteUserTagsHook( $uid, $levels, $data ) {
		$user = get_userdata( $uid );
		if ( ! $user ) {
			return;
		}
		if ( strpos( $user->user_email, 'temp_' ) !== false && strlen( $user->user_email ) == 37 && strpos( $user->user_email, '@' ) === false ) {
			return;
		}

		$levels = (array) $levels;
		return $this->processTags( $levels, 'remove', $data );
	}

}
