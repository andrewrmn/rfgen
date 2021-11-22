<?php

namespace WishListMember\Autoresponders;

if ( ! class_exists( '\WLM_Automizy' ) ) {
	global $WishListMemberInstance;
	require_once $WishListMemberInstance->pluginDir . '/extlib/wlm-automizy.php';
}

class Automizy {
	static function __callStatic( $name, $args ) {
		$interface = self::_interface();
		if ( $interface->api() ) {
			call_user_func_array( array( $interface, $name ), $args );
		}
	}

	static function _interface() {
		static $interface;
		if ( ! $interface ) {
			$interface = new Automizy_Interface();
		}
		return $interface;
	}
}

class Automizy_Interface {
	private $settings  = '';
	private $api_key   = null;

	function __construct() {
		global $WishListMemberInstance;
		$this->automizy_api = false;
		// make sure that WLM active and infusiosnsoft connection is set
		if ( isset( $WishListMemberInstance ) && class_exists( '\WLM_Automizy' ) ) {
			$this->settings = ( new \WishListMember\Autoresponder( 'automizy' ) )->settings ?: false;
			// initilize automizy api connection
			if ( $this->settings && ! empty( $this->settings['api_key'] ) ) {
				$this->automizy_api = new \WLM_Automizy( $this->settings['api_key']);
				$lists_ret	= $this->automizy_api->get( 'smart-lists' );
				if ( !$this->automizy_api->is_success() ) {
					$this->automizy_api = false;
				}
			}
		}
	}

	function api() {
		return $this->automizy_api;
	}

	function processTags( $levels, $action, $data ) {
		global $WishListMemberInstance;
		if ( ! isset( $WishListMemberInstance ) ) {
			return array(
				'errstr' => 'WishList Member instance not found.',
				'errno'  => 1,
			);
		}
		if ( ! $this->automizy_api ) {
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

		$errors = array();
		// add the tags for each level
		foreach ( (array) $levels as $level ) {
			$error = array();

			$apply_tags = isset( $this->settings[ $level ][ $action ]['apply_tag'] ) ? $this->settings[ $level ][ $action ]['apply_tag'] : false;
			$apply_tags = ! empty( $apply_tags ) ? $apply_tags : false;
			$remove_tag = isset( $this->settings[ $level ][ $action ]['remove_tag'] ) ? $this->settings[ $level ][ $action ]['remove_tag'] : false;
			$remove_tag = ! empty( $remove_tag ) ? $remove_tag : false;

			$list_add    = isset( $this->settings[ $level ][ $action ]['list_add'] ) ? $this->settings[ $level ][ $action ]['list_add'] : false;
			$list_add    = ! empty( $list_add ) ? $list_add : false;
			$list_remove = isset( $this->settings[ $level ][ $action ]['list_remove'] ) ? $this->settings[ $level ][ $action ]['list_remove'] : false;
			$list_remove = ! empty( $list_remove ) ? $list_remove : false;

			$c = $this->automizy_api->get("contacts/{$data['user_email']}");
			if ( $c ) {
				$tags = array_merge($c["tags"],$tags);
				$tags = array_unique($tags);

				$new_lists = [];
				foreach ($c["smartLists"] as $key => $value) {
					$new_lists[] =  $value["id"];
				}
				//lets remove first
				if ( $list_remove ) $new_lists = array_diff($new_lists, $list_remove );
				if ( $list_add ) $new_lists[] = $list_add;
				$new_lists = array_unique($new_lists);

				$params = [
					"customFields" => [
						"firstname" => $data['first_name'],
						"lastname"  => $data['last_name'],
					],
					"smartLists" => $new_lists,
				];
				if ( $remove_tag ) $params['removeTags'] = $remove_tag;
				if ( $apply_tags ) $params['addTags'] = $apply_tags;

				$c = $this->automizy_api->patch("contacts/{$data['user_email']}", $params );
			} else {
				$params = [
					"email" => $data['user_email'],
					"customFields" => [
						"firstname" => $data['first_name'],
						"lastname"  => $data['last_name'],
					],
					"tags" => $apply_tags,
				];
				$list_add = $list_add ? $list_add : 1;
				$c = $this->automizy_api->post("smart-lists/{$list_add}/contacts", $params );
			}

			if ( !$this->automizy_api->is_success() && !empty($this->automizy_api->get_last_error()) ) {
				$errors[ $level ] = $this->automizy_api->get_last_error();
			}
		}
		return count( $errors ) ? wlm_maybe_serialize( $errors ) : true; // success
	}

	function AddQueue( $data, $process = true ) {
		$WishlistAPIQueueInstance = new \WishlistAPIQueue();
		$qname                    = 'automizyar' . time();
		$data                     = wlm_maybe_serialize( $data );
		$WishlistAPIQueueInstance->add_queue( $qname, $data, 'For Queueing' );
		if ( $process ) {
			$this->ProcessQueue();
		}
	}

	function ProcessQueue( $recnum = 10, $tries = 3 ) {
		if ( ! $this->automizy_api ) {
			return;
		}
		$WishlistAPIQueueInstance = new \WishlistAPIQueue();
		$last_process             = get_option( 'WLM_AUTORESPONDER_AUTOMIZY_LastProcess' );
		$current_time             = time();
		$tries                    = $tries > 1 ? (int) $tries : 3;
		$error                    = false;
		// lets process every 10 seconds
		if ( ! $last_process || ( $current_time - $last_process ) > 10 ) {
			$queues = $WishlistAPIQueueInstance->get_queue( 'automizyar', $recnum, $tries, 'tries,name' );
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

				if ( $res !== true ) {
					$d = array(
						'notes' => $res,
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
					update_option( 'WLM_AUTORESPONDER_AUTOMIZY_LastProcess', $current_time );
				} else {
					add_option( 'WLM_AUTORESPONDER_AUTOMIZY_LastProcess', $current_time );
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
			// WLM API aren't being processed by the Integration.
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
		if ( ! $this->automizy_api ) {
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

