<?php
/**
 * WebHooks Integration Handler
 */
namespace WishListMember\Integrations\Others;

/**
 * WebHooks Integration Class
 */
class WebHooks {
	private $incoming = array();
	private $outgoing = array();

	const SUCCESS                  = 1;
	const ERR_NOT_FOUND            = 1001;
	const ERR_INVALID_EMAIL        = 1002;
	const ERR_ACCOUNT_EXISTS       = 1003;
	const ERR_ALREADY_A_MEMBER     = 1004;
	const ERR_CANNOT_CREATE_MEMBER = 1005;

	static $terminate_codes = array(
		self::SUCCESS                  => array( 200, array( 'success' => 1 ) ),
		self::ERR_NOT_FOUND            => array( 404, array( 'error' => 'Not Found' ) ),
		self::ERR_INVALID_EMAIL        => array( 400, array( 'error' => 'Invalid Email Address' ) ),
		self::ERR_ACCOUNT_EXISTS       => array( 409, array( 'error' => 'Account Already Exists' ) ),
		self::ERR_ALREADY_A_MEMBER     => array( 409, array( 'error' => 'Already a Member' ) ),
		self::ERR_CANNOT_CREATE_MEMBER => array( 409, array( 'error' => 'Cannot Create Member' ) ),
	);

	/**
	 * Constructor
	 */
	function __construct() {
		global $WishListMemberInstance;

		$settings       = $WishListMemberInstance->GetOption( 'webhooks_settings' );
		$this->incoming = wlm_arrval( $settings, 'incoming' );
		$this->outgoing = wlm_arrval( $settings, 'outgoing' );

		$this->add_hooks();
	}

	function add_hooks() {
		// hook to handle incoming webhooks
		add_action( 'init', array( $this, 'receive_webhook' ) );

		// hooks when a user is added to or removed from membership levels
		add_action( 'wishlistmember_add_user_levels', array( $this, 'levels_added' ), 10, 2 );
		add_action( 'wishlistmember_remove_user_levels', array( $this, 'levels_removed' ), 10, 2 );

		// hooks when a user is cancelled or uncancelled from membership levels
		add_action( 'wishlistmember_cancel_user_levels', array( $this, 'levels_cancelled' ), 10, 2 );
		add_action( 'wishlistmember_uncancel_user_levels', array( $this, 'levels_uncancelled' ), 10, 2 );
	}

	function remove_hooks() {
		// hook to handle incoming webhooks
		remove_action( 'init', array( $this, 'receive_webhook' ) );

		// hooks when a user is added to or removed from membership levels
		remove_action( 'wishlistmember_add_user_levels', array( $this, 'levels_added' ), 10 );
		remove_action( 'wishlistmember_remove_user_levels', array( $this, 'levels_removed' ), 10 );

		// hooks when a user is cancelled or uncancelled from membership levels
		remove_action( 'wishlistmember_cancel_user_levels', array( $this, 'levels_cancelled' ), 10 );
		remove_action( 'wishlistmember_uncancel_user_levels', array( $this, 'levels_uncancelled' ), 10 );

		// suppress if WLM settings tells us so
		remove_action( 'wishlistmember_suppress_other_integrations', array( $this, 'remove_hooks' ) );
	}

	/**
	 * Outputs http response code with json data
	 * or redirects to $this->post_data[redirect] if set
	 *
	 * @param int    $terminate_code
	 * @param string $reason
	 * @param mixed  $return_data
	 */
	private function terminate( $terminate_code = 1, $reason = '' ) {

		if ( empty( self::$terminate_codes[ $terminate_code ] ) ) {
			$terminate_code = 1;
		}

		list( $http_response_code, $return_data) = self::$terminate_codes[ $terminate_code ];

		$return_data['code'] = $terminate_code;
		if ( $reason ) {
			$return_data['reason'] = $reason;
		}

		$redirect = wlm_arrval( $this->post_data, 'redirect' );
		if ( $redirect ) {
			wp_redirect( add_query_arg( $return_data, $redirect ) );
		} else {
			http_response_code( $http_response_code );
			wp_send_json( $return_data );
		}
		exit;
	}

	/**
	 * Hook: init
	 *
	 * Receives and processes webhooks
	 */
	function receive_webhook() {
		global $WishListMemberInstance;

		// $_GET['wlm_webhook'] required
		$hook = wlm_arrval( $_GET, 'wlm_webhook' );
		if ( ! $hook ) {
			return;
		}

		// $hook must be valid
		$data = wlm_arrval( $this->incoming, $hook );
		if ( ! $data ) {
			$this->terminate( self::ERR_NOT_FOUND );
		}
		
		// $_POST required
		if ( strpos( strtolower( trim( $_SERVER['CONTENT_TYPE'] ) ), 'application/json' ) === 0 ) {
			$this->post_data = json_decode( file_get_contents( 'php://input' ) );
		} elseif ( $_POST ) {
			$this->post_data = $_POST;
		} elseif ( $_GET && wlm_arrval( $data, 'process_get_requests' ) ) {
			$this->post_data = $_GET;
			unset( $this->post_data['wlm_webhook'] );
			unset( $this->post_data['wlmdebug'] );
		} else {
			$this->post_data = '';
		}

		if ( ! $this->post_data ) {
			return;
		}

		$map = array_merge(
			array(
				'email'     => 'email',
				'username'  => 'username',
				'password'  => 'password',
				'firstname' => 'firstname',
				'lastname'  => 'lastname',
			),
			(array) wlm_arrval( $data, 'map' )
		);

		array_walk(
			$map,
			function( &$val, $key, $post_data ) {
				$key  = preg_replace( '/["\'\s]/', '', trim( $val ) ?: $key );
				$keys = preg_split( '/[\[\]]/', $key );
				$val  = $post_data;
				while ( $keys ) {
					$key = trim( (string) array_shift( $keys ) );
					if ( strlen( $key ) ) {
						$val = wlm_arrval( $val, $key );
					}
				}
				$val = (string) $val;
			},
			$this->post_data
		);

		$email = $map['email'];
		if ( ! is_email( $email ) ) {
			$this->terminate( self::ERR_INVALID_EMAIL ); // bad request
		}

		unset( $map['email'] );
		$username = $map['username'];
		unset( $map['username'] );
		$password = $map['password'];
		unset( $map['password'] );
		$firstname = $map['firstname'];
		unset( $map['firstname'] );
		$lastname = $map['lastname'];
		unset( $map['lastname'] );

		$actions = array_merge(
			array(
				'add'      => array(),
				'remove'   => array(),
				'cancel'   => array(),
				'uncancel' => array(),
			),
			(array) wlm_arrval( $data, 'actions' )
		);

		$id = get_user_by( 'email', $email );
		if ( $id ) {
			$id = $id->ID;
		}

		// add
		if ( $actions['add'] && is_array( $actions['add'] ) ) {
			if ( $id ) {
				if ( $x = wlm_arrval( $this->post_data, 'new_users_only' ) ) {
					// redirect to "new_users_only" value if it looks like a URL
					if( preg_match( '#(http|https)://.+#i', $x ) ) {
						$this->post_data['redirect'] = $x;
					}
					$this->terminate( self::ERR_ACCOUNT_EXISTS );
				}
				/*
				 * if the user exists and 'new_members_only' is true in the post data then
				 * we only allow registrations if there is at least one level in the webhook's
				 * 'add' configuration that the user is not yet a member of
				 */
				if ( $x = wlm_arrval( $this->post_data, 'new_members_only' ) ) {
					if ( ! array_diff( $actions['add'], array_keys( wlmapi_get_member_levels( $id ) ) ) ) {
						// redirect to "new_members_only" value if it looks like a URL
						if( preg_match( '#(http|https)://.+#i', $x ) ) {
							$this->post_data['redirect'] = $x;
						}
						$this->terminate( self::ERR_ALREADY_A_MEMBER );
					}
				}

				wlmapi_update_member(
					$id,
					array(
						'Levels'                       => $actions['add'],
						'ObeyRegistrationRequirements' => 1,
						'SendMailPerLevel'             => 1,
					)
				);
			} else {
				$member = array(
					'user_email'                   => $email,
					'user_login'                   => $this->generate_username( $username, $email, $firstname, $lastname, trim( wlm_arrval( $data, 'username_format' ) ) ?: '{email}' ),
					'user_pass'                    => $password ?: $WishListMemberInstance->PassGen( 12 ),
					'first_name'                   => $firstname,
					'last_name'                    => $lastname,
					'Levels'                       => $actions['add'],
					'ObeyRegistrationRequirements' => 1,
					'SendMailPerLevel'             => 1,
				);

				$x = wlmapi_add_member( $member );
				if ( $x['success'] ) {
					$id = $x['member'][0]['ID'];
				} else {
					$this->terminate( self::ERR_CANNOT_CREATE_MEMBER, $x['ERROR'] );
				}
			}
		}

		if ( $id ) {
			// remove
			if ( $actions['remove'] && is_array( $actions['remove'] ) ) {
				wlmapi_update_member( $id, array( 'RemoveLevels' => $actions['remove'] ) );
			}

			// cancel
			if ( $actions['cancel'] && is_array( $actions['cancel'] ) ) {
				foreach ( $actions['cancel'] as $level ) {
					wlmapi_update_level_member_data(
						$level,
						$id,
						array(
							'Cancelled'        => 1,
							'SendMailPerLevel' => 1,
						)
					);
				}
			}

			// uncancel
			if ( $actions['uncancel'] && is_array( $actions['uncancel'] ) ) {
				foreach ( $actions['uncancel'] as $level ) {
					wlmapi_update_level_member_data(
						$level,
						$id,
						array(
							'Cancelled'        => 0,
							'SendMailPerLevel' => 1,
						)
					);
				}
			}
		}

		$this->terminate( self::SUCCESS );

		exit;
	}

	private function send_webhook( $uid, $levels, $action ) {
		$user = new \WishListMember\User( $uid, true );
		if ( empty( $user->ID ) ) {
			return;
		}

		$data = array(
			'id'        => $user->ID,
			'email'     => $user->UserInfo->user_email,
			'login'     => $user->UserInfo->user_login,
			'firstname' => $user->UserInfo->first_name,
			'lastname'  => $user->UserInfo->last_name,
			'levels'    => $user->Levels,
		);

		foreach ( (array) $levels as $level ) {
			$actions = (array) wlm_arrval( $this->outgoing, $level );
			$urls    = wlm_arrval( $actions, $action );
			if ( preg_match_all( '/\bhttp[s]{0,1}:\/\/[^\s]+/', $urls, $matches ) ) {
				foreach ( $matches[0] as $url ) {
					wp_remote_post(
						$url,
						array(
							'body'       => $data,
							'user-agent' => 'WishList Member/' . $this->wlm->Version,
							'timeout'    => 1,
							'blocking'   => false,
						)
					);
				}
			}
		}
	}

	/**
	 * Hook: wishlistmember_add_user_levels
	 *
	 * @uses WebHooks::send_webhook
	 * @param integer $uid User ID
	 * @param array   $levels Membership Level IDs
	 */
	function levels_added( $uid, $levels ) {
		$this->send_webhook( $uid, $levels, 'add' );
	}

	/**
	 * Hook: wishlistmember_remove_user_levels
	 *
	 * @uses WebHooks::send_webhook
	 * @param integer $uid User ID
	 * @param array   $levels Membership Level IDs
	 */
	function levels_removed( $uid, $levels ) {
		$this->send_webhook( $uid, $levels, 'remove' );
	}

	/**
	 * Hook: wishlistmember_cancel_user_levels
	 *
	 * @uses WebHooks::send_webhook
	 * @param integer $uid User ID
	 * @param array   $levels Membership Level IDs
	 */
	function levels_cancelled( $uid, $levels ) {
		$this->send_webhook( $uid, $levels, 'cancel' );
	}

	/**
	 * Hook: wishlistmember_uncancel_user_levels
	 *
	 * @uses WebHooks::send_webhook
	 * @param integer $uid User ID
	 * @param array   $levels Membership Level IDs
	 */
	function levels_uncancelled( $uid, $levels ) {
		$this->send_webhook( $uid, $levels, 'uncancel' );
	}
	
	/**
	 * Generate username
	 * @param  string $username   Default username
	 * @param  string $email      Email address
	 * @param  string $first_name First Name
	 * @param  string $last_name  Last Name
	 * @param  string $format     Username format
	 * @return string             Generated username
	 */
	private function generate_username( $username, $email, $first_name, $last_name, $format ) {
		$username = trim( $username );
		if( $username ) {
			// $format is verbatim $username if provided
			$format = $username;
		} else {
			// default $format is {email}
			$format = trim( $format ) ?: '{email}';
		}
		
		// if $first_name is not set, set it to $username or $email 
		if( !$first_name ) {
			$first_name = $username ?: $email;
		}
		
		return wlm_generate_username( compact( 'email', 'first_name', 'last_name' ), $format );
	}
}

// initialize
new WebHooks();
