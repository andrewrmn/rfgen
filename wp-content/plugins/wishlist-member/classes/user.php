<?php
/**
 * User Class for WishList Member
 */

namespace WishListMember;

defined( 'ABSPATH' ) || die();

require_once ABSPATH . '/wp-admin/includes/user.php';

/**
 * WishList Member User Class
 * Keeps all membership information in one place
 *
 * @package wishlistmember
 * @subpackage classes
 */
class User {

	/**
	 * User ID
	 *
	 * @var integer
	 */
	var $ID;

	/**
	 * User information
	 *
	 * @var WP_User object
	 */
	var $UserInfo;

	/**
	 * Sequential Upgrade setting
	 *
	 * @var boolean
	 */
	var $Sequential;

	/**
	 * Membership Levels
	 *
	 * @var array
	 */
	var $Levels = array();
	
	/**
	 * Array of active membership level IDs
	 * @var array
	 */
	var $active_levels = array();

	/**
	 * Pay Per Posts
	 *
	 * @var array
	 */
	var $PayPerPosts = array();

	/**
	 * Pointer to $WishListMemberInstance
	 *
	 * @var object
	 */
	var $WL;

	/**
	 * Constructor
	 */
	function __construct( $ID, $loadUser = null ) {
		global $wpdb, $WishListMemberInstance;

		$this->WL = &$WishListMemberInstance;

		/*
		 * if $ID is not numeric then it might be an email address or a username
		 */
		if ( ! is_numeric( $ID ) ) {
			$x = false;
			if ( filter_var( $ID, FILTER_VALIDATE_EMAIL ) ) {
				$x = get_user_by( 'email', $ID );
			}
			if ( ! $x ) {
				$x = get_user_by( 'login', $ID );
			}
			$ID = $x->ID ?: 0;
		}

		// verify User ID
		$ID += 0;
		$ID  = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->users}` WHERE `ID`={$ID}" );
		if ( ! $ID ) {
			return false;
		}

		// ID verified, save it
		$this->ID = $ID;

		// load user information if requested
		if ( $loadUser === true ) {
			$this->LoadUser();
		}

		// sequential setting
		$this->Sequential = $this->WL->IsSequential( $this->ID );

		$this->LoadLevels();

		$this->load_payperposts();
		
		return true;
	}

	/**
	 * Loads user information as returned by WP_User object
	 */
	function LoadUser() {
		$this->UserInfo = $this->WL->Get_UserData( $this->ID );
	}

	/**
	 * Loads membership levels including their
	 * - Status (Cancelled, Pending, UnConfirmed)
	 * - Timestamp
	 * - Transaction ID
	 */
	function LoadLevels() {
		$wpm_levels = $this->WL->GetOption( 'wpm_levels' );
		$x          = $this->WL->GetMembershipLevels( $this->ID );

		$levels = array();
		$ts     = $this->WL->UserLevelTimestamps( $this->ID );
		foreach ( $x as $lvl ) {
			if ( array_key_exists( $lvl, $ts ) ) {
				$levels[ $lvl ] = $ts[ $lvl ];
			} else {
				$levels[ $lvl ] = false;
			}
		}

		arsort( $levels );

		$this->Levels = array();
		foreach ( $levels as $level => $timestamp ) {
			$wpm_levels[ $level ] = ( isset( $wpm_levels[ $level ] ) ) ? $wpm_levels[ $level ] : '';
			if ( $wpm_levels[ $level ] ) {
				$allmetas = $this->WL->Get_All_UserLevelMetas( $this->ID, $level );

				// Fix for users who are using PHP versions as array_column only supports array objects on PHP 7.
				$option_name_array = $option_value_array = array();
				foreach ( $allmetas as $allmeta ) {
					$option_name_array[]  = $allmeta->option_name;
					$option_value_array[] = $allmeta->option_value;
				}

				$allmetas = array_combine( $option_name_array, $option_value_array );

				$this->Levels[ $level ]                      = new \stdClass();
				$this->Levels[ $level ]->Level_ID            = $level;
				$this->Levels[ $level ]->Name                = $wpm_levels[ $level ]['name'];
				$this->Levels[ $level ]->Timestamp           = $timestamp;
				$this->Levels[ $level ]->Cancelled           = $cancelled = isset( $allmetas['cancelled'] ) ? $allmetas['cancelled'] : false;
				$this->Levels[ $level ]->CancelDate          = empty( $allmetas['wlm_schedule_level_cancel'] ) ? false : strtotime( $allmetas['wlm_schedule_level_cancel'] );
				$this->Levels[ $level ]->CancelDateReason    = wlm_maybe_json_decode( wlm_arrval( $allmetas, 'schedule_level_cancel_reason' ) ) ?: '';
				$this->Levels[ $level ]->CancelledDate       = empty( $allmetas['cancelled_date'] ) ? false : strtotime( $allmetas['cancelled_date'] );
				$this->Levels[ $level ]->Pending             = $pending = isset( $allmetas['forapproval'] ) ? $allmetas['forapproval'] : false;
				$pending                                     = ( $pending ) ? true : false;
				$this->Levels[ $level ]->UnConfirmed         = $unconfirmed = isset( $allmetas['unconfirmed'] ) ? $allmetas['unconfirmed'] : false;
				$this->Levels[ $level ]->Expired             = $expired = $this->WL->LevelExpired( $level, $this->ID, $timestamp );
				$this->Levels[ $level ]->ExpiryDate          = $this->WL->LevelExpireDate( $level, $this->ID, $timestamp );
				$this->Levels[ $level ]->SequentialCancelled = isset( $allmetas['sequential_cancelled'] ) ? $allmetas['sequential_cancelled'] : false;
				$this->Levels[ $level ]->Scheduled           = (bool) $scheduled = $this->WL->IsLevelScheduled( $level, $this->ID );
				$this->Levels[ $level ]->ScheduleInfo        = wlm_maybe_unserialize( $scheduled );
				$allmetas['parent_level']                    = isset( $allmetas['parent_level'] ) ? $allmetas['parent_level'] : 0;
				$this->Levels[ $level ]->ParentLevel         = isset( $wpm_levels[ $allmetas['parent_level'] ] ) ? $allmetas['parent_level'] : false;
				$this->Levels[ $level ]->Active              = $active = ! ( $cancelled | $pending | $unconfirmed | $expired | (bool) $scheduled );
				if ( $active ) {
					$this->Levels[ $level ]->Status = array( __( 'Active', 'wishlist-member' ) );
					$this->active_levels[] = $level;
				} else {
					$statusNames = array();
					if ( $unconfirmed ) {
						$statusNames[] = __( 'Unconfirmed', 'wishlist-member' );
					}
					if ( $pending ) {
						$statusNames[] = __( 'For Approval', 'wishlist-member' );
					}
					if ( $cancelled ) {
						$statusNames[] = __( 'Cancelled', 'wishlist-member' );
					}
					if ( $scheduled ) {
						$statusNames[] = __( 'Scheduled', 'wishlist-member' );
					}
					if ( $expired === true ) {
						$statusNames[] = __( 'Expired', 'wishlist-member' );
					}
					$this->Levels[ $level ]->Status = $statusNames;
				}
			}
		}

		// MOVE to top for sorting purposes, FJ
		// timestamps
		// $ts = $this->WL->UserLevelTimestamps($this->ID);
		// foreach ($ts AS $level => $time) {
		// if ( (isset($this->Levels[$level]) && $this->Levels[$level]) )
		// $this->Levels[$level]->Timestamp = $time;
		// }

		// transaction IDs
		$txns = $this->WL->GetMembershipLevelsTxnIDs( $this->ID );
		foreach ( $txns as $level => $txn ) {
			if ( ( isset( $this->Levels[ $level ] ) && $this->Levels[ $level ] ) ) {
				$this->Levels[ $level ]->TxnID = $txn;
			}
		}
	}

	/**
	 * Adds Level to user obj in RAM.
	 *
	 * @param integer $levelID
	 */
	function AddLevelobj( $level ) {

		// $this->Levels[$level] = new stdClass();
		$this->Levels[ $level ]->Level_ID            = $level;
		$this->Levels[ $level ]->Name                = 'Name';
		$this->Levels[ $level ]->Cancelled           = 'NULL';
		$this->Levels[ $level ]->CancelDate          = false;
		$this->Levels[ $level ]->Pending             = null;
		$this->Levels[ $level ]->UnConfirmed         = null;
		$this->Levels[ $level ]->Expired             = false;
		$this->Levels[ $level ]->ExpiryDate          = false;
		$this->Levels[ $level ]->SequentialCancelled = null;
		$this->Levels[ $level ]->Active              = true;
		$this->Levels[ $level ]->Status              = array( __( 'Active', 'wishlist-member' ) );
		$this->Levels[ $level ]->Timestamp           = '';
		$this->Levels[ $level ]->TxnID               = '';

	}

	/**
	 * Adds user to Level
	 *
	 * @param integer $levelID
	 * @param string  $TransactionID
	 */
	function AddLevel( $levelID, $TransactionID ) {
		$x   = array_keys( $this->Levels );
		$x[] = $levelID;
		$this->WL->SetMembershipLevels( $this->ID, array_unique( $x ) );

		// transaction id
		$this->WL->SetMembershipLevelTxnID( $this->ID, $levelID, $TransactionID );

		// reload levels
		$this->LoadLevels();
	}

	/**
	 * Removes user from Level
	 *
	 * @param integer $levelID
	 */
	function RemoveLevel( $levelID ) {
		$x = array_unique( array_keys( $this->Levels ) );

		// remove level
		$k = array_search( $levelID, $x );
		if ( $k !== false ) {
			unset( $x[ $k ] );
		}

		// save it
		$this->WL->SetMembershipLevels( $this->ID, $x );

		// reload levels
		$this->LoadLevels();
	}

	/**
	 * Removes multiple levels
	 * @param array $levelIDs Array of Membership Level IDs
	 */
	function RemoveLevels( $levelIDs ) {
		$x = array_unique( array_keys( $this->Levels ) );
		$this->WL->SetMembershipLevels( $this->ID, array_diff( $x, $levelIDs ) );
		$this->LoadLevels();
	}
	/**
	 * Execute sequential upgrade for user
	 */
	function RunSequentialUpgrade() {
		$this->do_sequential( $this->ID );
	}

	/**
	 * Cancel Membership Level
	 *
	 * @param integer $levelID
	 */
	function CancelLevel( $levelID ) {
		$this->Levels[ $levelID ]->Cancelled = $this->WL->LevelCancelled( $levelID, $this->ID, true );
	}

	/**
	 * UnCancel Level
	 *
	 * @param integer $levelID
	 */
	function UnCancelLevel( $levelID ) {
		$this->Levels[ $levelID ]->Cancelled = $this->WL->LevelCancelled( $levelID, $this->ID, false );
	}

	/**
	 * Approve Membership Level
	 *
	 * @param integer $levelID
	 */
	function ApproveLevel( $levelID ) {
		$this->Levels[ $levelID ]->Pending = $this->WL->LevelForApproval( $levelID, $this->ID, false );
	}

	/**
	 * UnApprove Membership Level
	 *
	 * @param integer $levelID
	 */
	function UnApproveLevel( $levelID ) {
		$this->Levels[ $levelID ]->Pending = $this->WL->LevelForApproval( $levelID, $this->ID, true );
	}

	/**
	 * Confirm Membership Level (Used in Email Confirmation)
	 *
	 * @param integer $levelID
	 */
	function Confirm( $levelID ) {
		$this->Levels[ $levelID ]->UnConfirmed = $this->WL->LevelUnConfirmed( $levelID, $this->ID, false );
	}

	/**
	 * Confirm user's membership level registration by hash
	 *
	 * @param string $hash Hash Key
	 * @return mixed Level ID on success or FALSE on error
	 */
	function ConfirmByHash( $hash ) {
		$email    = $this->UserInfo->user_email;
		$username = $this->UserInfo->user_login;
		$key      = $this->WL->GetAPIKey();
		foreach ( $this->Levels as $levelID => $level ) {
			$h = md5( "{$email}__{$username}__{$levelID}__{$key}" );
			if ( $h == $hash && $level->UnConfirmed ) {
				$this->Confirm( $levelID );
				return $levelID;
			}
		}
		return false;
	}

	/**
	 * UnConfirm Membership Level (Used in Email Confirmation)
	 *
	 * @param integer $levelID
	 */
	function UnConfirm( $levelID ) {
		$this->Levels[ $level ]->UnConfirmed = $this->WL->LevelUnConfirmed( $levelID, $this->ID, true );
	}

	/**
	 * Enable Sequential Upgrade for User
	 */
	function EnableSequential() {
		$this->Sequential = $this->WL->IsSequential( $this->ID, true );
	}

	/**
	 * Disable Sequential Upgrade for User
	 */
	function DisableSequential() {
		$this->Sequential = $this->WL->IsSequential( $this->ID, false );
	}

	function IsExpired( $level ) {
		return $this->Levels[ $level ]->Expired === true;
	}

	function ExpireDate( $level ) {
		if ( $this->Levels[ $level ]->Expired === false ) {
			return false;
		} else {

		}
	}

	/**
	 * Executes the "Remove From Level" & "Add To Level" features
	 *
	 * @param array $the_levels Levels of the action
	 * @param array $removed_levels Levels that were removed
	 */
	function DoAddRemove( $the_levels, $removed_levels, $action = '' ) {
		$current_levels = array_keys( $this->Levels );
		$the_levels     = (array) $the_levels;
		$removed_levels = (array) $removed_levels;
		$wpm_levels     = $this->WL->GetOption( 'wpm_levels' );

		$action = $action == 'add' ? '' : $action;
		$action = ! empty( $action ) ? $action . '_' : $action;

		$remove = $add = array();
		foreach ( $the_levels as $level ) {

			if ( isset( $wpm_levels[ $level ] ) ) { // make sure that level is existing and active

				// get levels to remove or add
				$to_remove = array_keys( (array) $wpm_levels[ $level ][ $action . 'removeFromLevel' ] );
				$to_add    = array_keys( (array) $wpm_levels[ $level ][ $action . 'addToLevel' ] );
				$to_cancel = array_keys( (array) $wpm_levels[ $level ][ $action . 'cancelFromLevel' ] );
				// // we don't remove the newly added levels
				// $to_remove = array_diff($to_remove, $the_levels);
				// // we don't add the newly removed levels
				// $to_add = array_diff($to_add, $removed_levels);
				foreach ( $to_remove as $tr ) {
					// if key is not a levelid, use the value, this fix is for 3.0 from 2.9
					// 3.0 saves the levelid with numberic index eg. [0] => 1506100025107
					// while 2.9 saves the levelid as the index with value of 1 eg. [1508151640] => 1
					if ( ! isset( $wpm_levels[ $tr ] ) ) {
						$tr = isset( $wpm_levels[ $level ][ $action . 'removeFromLevel' ][ $tr ] ) ? $wpm_levels[ $level ][ $action . 'removeFromLevel' ][ $tr ] : $tr;
					}
					if ( ! isset( $wpm_levels[ $tr ] ) ) {
						continue; // still no luck? continue
					}

					if ( in_array( $tr, $current_levels ) ) {// only remove levels that this user currently have
						$remove[ $tr ] = $level;
					}
				}

				foreach ( $to_add as $ta ) {
					// if key is not a levelid, use the value, this fix is for 3.0 from 2.9
					// 3.0 saves the levelid with numberic index eg. [0] => 1506100025107
					// while 2.9 saves the levelid as the index with value of 1 eg. [1508151640] => 1
					if ( ! isset( $wpm_levels[ $ta ] ) ) {
						$ta = isset( $wpm_levels[ $level ][ $action . 'addToLevel' ][ $ta ] ) ? $wpm_levels[ $level ][ $action . 'addToLevel' ][ $ta ] : $ta;
					}
					if ( ! isset( $wpm_levels[ $ta ] ) ) {
						continue; // still no luck? continue
					}

					if ( ! in_array( $ta, $current_levels ) ) {// only add levels that this user does not have
						if ( array_key_exists( $ta, $add ) ) {// if this level is for add already, check level priority
							if ( $wpm_levels[ $level ]['levelOrder'] > $wpm_levels[ $add[ $ta ] ]['levelOrder'] ) {
								$add[ $ta ] = $level;
							}
						} else {
							$add[ $ta ] = $level;
						}
					}
				}

				foreach ( $to_cancel as $tc ) {
					// if key is not a levelid, use the value, this fix is for 3.0 from 2.9
					// 3.0 saves the levelid with numberic index eg. [0] => 1506100025107
					// while 2.9 saves the levelid as the index with value of 1 eg. [1508151640] => 1
					if ( ! isset( $wpm_levels[ $tc ] ) ) {
						$tc = isset( $wpm_levels[ $level ]['removeFromLevel'][ $tc ] ) ? $wpm_levels[ $level ]['removeFromLevel'][ $tc ] : $tc;
					}
					if ( ! isset( $wpm_levels[ $tc ] ) ) {
						continue; // still no luck? continue
					}

					if ( in_array( $tc, $current_levels ) ) {// only cancel levels that this user currently have
						$this->WL->LevelCancelled( $tc, $this->ID, true );
					}
				}
			}
		}

		$to_add_levels    = array_keys( $add );
		$to_remove_levels = array_keys( $remove );
		if ( count( $to_add_levels ) <= 0 && count( $to_remove_levels ) <= 0 ) {
			return; // nothing to do here
		}

		// we merge current levels with levels to be automatically added
		// and then we remove the remainings levels that are to be automatically removed
		$levels = array_unique( array_diff( array_merge( $current_levels, $to_add_levels ), $to_remove_levels ) );
		// we update the levels
		$xLevels = array(
			'Levels'            => array_unique( $levels ),
			'To_Removed_Levels' => array_unique( $to_remove_levels ),
			'Metas'             => array(),
		);

		if ( ! empty( $action ) ) { // we only add parent for ADD action
			foreach ( $levels as $key => $lvl ) {
				if ( isset( $add[ $lvl ] ) ) { // if this level is newly added, we add parent meta
					$xLevels['Metas'][ $lvl ] = array( array( 'parent_level', $add[ $lvl ] ) );
				}
			}
		}

		$res = $this->WL->SetMembershipLevels( $this->ID, (object) $xLevels );
	}

	/**
	 * Retrieve history for User
	 *
	 * @param string      $log_group
	 * @param string|null $log_key
	 * @return array|null Database query results
	 */
	function get_history( $log_group, $log_key = null ) {
		return \WishListMember\Logs::get( $this->ID, $log_group, $log_key );
	}
	
	/**
	 * Loads of all the User's Pay per Posts in the PayPerPosts property grouped by post type
	 * A special post type called _all_ contains of the post ids irregardless of the post type
	 */
	function load_payperposts() {
		$ppps = $this->WL->GetUser_PayPerPost( $this->ID, true );
		$this->PayPerPosts['_all_'] = array();
		foreach ( $ppps as $ppp ) {
			$this->PayPerPosts[ $ppp->type ][] = $ppp->content_id;
			$this->PayPerPosts['_all_'][]      = $ppp->content_id;
		}
	}

	/**
	 * Add Pay Per Posts to User
	 * @param string|array $payperpost_ids A string or an array of strings in the format of payperpost-[0-9]+
	 */
	function add_payperposts( $payperpost_ids ) {
		$this->WL->SetPayPerPost( $this->ID, (array) $payperpost_ids );
		$this->load_payperposts();
	}
	
	/**
	 * Remove Pay Per Posts from User
	 * @param string|array $payperpost_ids A string or an array of strings in the format of payperpost-[0-9]+
	 */
	function remove_payperposts( $payperpost_ids ) {
		foreach( (array) $payperpost_ids AS $payperpost_id ) {
			if( preg_match( '/^payperpost-(\d+)$/i', $payperpost_id, $match ) ) {
				if( $post_type = get_post_type( $match[1] ) ) {
					$this->WL->RemovePostUsers( $post_type, $match[1], $this->ID );
				}
			}
		}
		$this->load_payperposts();
	}
	
	/**
	 * Get a list of pay per post IDs matching a set of transaction ids
	 * @param  array $transaction_ids Array of transaction IDs
	 * @return array Array of Pay Per Post IDs
	 */
	function get_payperposts_by_transaction_ids( $transaction_ids ) {
		global $wpdb;
		if( !is_array( $transaction_ids ) || empty( $transaction_ids ) ) {
			return array();
		}
		$transaction_ids = array_map( function( $id ) use ( $wpdb ) {
			return $wpdb->prepare( '%s', $id );
		}, $transaction_ids);
		$table1 = wishlistmember_instance()->Tables->contentlevels;
		$table2 = wishlistmember_instance()->Tables->contentlevel_options;
		
		$query = $wpdb->prepare( 'SELECT `content_id` FROM `' . $table1 . '` WHERE `level_id`=%s AND `ID` IN (SELECT `contentlevel_id` FROM `' . $table2 . '` WHERE `option_name`="transaction_id" AND `option_value` IN ( ' . implode( ',', $transaction_ids ) . ' ) )', 'U-' . $this->ID );
		
		return $wpdb->get_col( $query );
		
	}

	/**
	 * Generates a one-time login link for $user_id
	 *
	 * @uses wlm_generate_password
	 * @uses add_user_meta
	 * @uses add_query_arg
	 *
	 * @param  integer $user_id User ID
	 * @return string One-Time login link
	 */
	static function generate_onetime_login_link( $user_id ) {
		// generate user's unique public and private key for one-time login link
		do {
			// generate random public key
			$public_key = wlm_generate_password();
			$public_key = sha1( $public_key ) . md5( $public_key );

			// generate private key from public key
			$private_key = md5( $public_key ) . sha1( $public_key );
		} while ( ! add_user_meta( $user_id, "otl-$private_key", time(), true ) );

		// generate the link and return it
		return add_query_arg(
			array(
				'wlmotl' => $public_key,
				'uid'    => $user_id,
			),
			site_url()
		);
	}

	/**
	 * Perform one-time login
	 *
	 * @uses $WishListMemberInstance::WPMAutoLogin
	 * @uses $WishListMemberInstance::Login
	 *
	 * @uses get_user_meta
	 * @uses delete_user_meta
	 *
	 * @param  integer $user_id  User ID
	 * @param  string  $public_key Public Key
	 */
	static function do_onetime_login( $user_id, $public_key ) {
		global $WishListMemberInstance;
		

		// generate private key from public key
		$private_key = md5( $public_key ) . sha1( $public_key );

		// login if private key is found for user
		if ( get_user_meta( $user_id, "otl-$private_key" ) ) {
			// delete the private key so it cannot be used again (thus one-time)
			delete_user_meta( $user_id, "otl-$private_key" );

			// get the user and login if user is valid
			$user = get_userdata( $user_id );
			if ( $user ) {
				// auto login
				$WishListMemberInstance->WPMAutoLogin( $user_id );
				// redirect to WishList Member after login page
				$_POST['log'] = $user->user_login;
				$_POST['wlm_redirect_to'] = trim( wlm_arrval( $_GET, 'redirect' ) ) ?: 'wishlistmember';
				$_COOKIE['wlmotl'] = 1;
				$WishListMemberInstance->Login( $user->user_login, $userinfo );
				exit;
			}
		}
		
		// redirect to login URL if we're still here
		wp_redirect( wp_login_url() );
		exit;
	}

	/**
	 * Get the URL of the user's profile photo
	 * 
	 * @param  integer $user_id User ID. $this->ID or current logged-in user's ID if not set.  
	 * @return string|false     URL or false if no profile photo is set
	 */
	function get_profile_photo( $user_id = null ) {
		if( empty( $user_id ) ) {
			$user_id = $this->ID ?: get_current_user_id();
		}
		return wlm_arrval( get_user_meta( $user_id, 'profile_photo', true ), 'url' ) ?: false;
	}
}
