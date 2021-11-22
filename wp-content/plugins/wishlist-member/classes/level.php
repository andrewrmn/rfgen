<?php
/**
 * Level Class for WishList Member
 */

namespace WishListMember;

defined( 'ABSPATH' ) || die();

// Putting it here to fix the fatal errros when cron is run via WP-CLI.
// Looks like WP-CLI handles the globals differently and as a result caused fatal errors.
global $WishListMemberInstance;

/**
 * WishList Member Level Class
 *
 * @package wishlistmember
 * @subpackage classes
 */
class Level {
	var $ID       = null;
	private $data = array();
	
	/**
	 * Constructor
	 * @param string|integer $levelID Level ID to initialize the object with
	 */
	function __construct( $levelID ) {
		global $WishListMemberInstance;

		if ( ! in_array( get_class( $WishListMemberInstance ), array( 'WishListMember', 'WishListMember3' ) ) ) {
			return;
		}
		$wpm_levels = $WishListMemberInstance->GetOption( 'wpm_levels' );
		if ( isset( $wpm_levels[ $levelID ] ) ) {
			$this->data       = array_merge( $WishListMemberInstance->level_defaults, $wpm_levels[ $levelID ] );
			$this->ID         = $levelID;
			$this->data['ID'] = $levelID;
		}
	}
	
	/**
	 * Return full level data
	 * @return array
	 */
	function get_data() {
		return $this->data;
	}

	// START: MAGIC METHODS

	/**
	 * Gets level properties
	 *
	 * @param string $name
	 * @return mixed
	 */
	function __get( $name ) {
		if ( 'id' == $name ) {
			$name = 'ID';
		}
		return $this->data[ $name ] ?: null;
	}

	/**
	 * Sets the level properties
	 *
	 * @param string $name
	 * @param mixed  $value
	 * @return void
	 */
	function __set( $name, $value ) {
		if ( 'id' == $name ) {
			$name = 'ID';
		}
		$this->data[ $name ] = $value;
	}
	
	/**
	 * Checks if a property is set
	 * @param  string  $name
	 * @return boolean
	 */
	function __isset( $name ) {
		return isset( $this->data[ $name ] );
	}

	/**
	 * Handle calls to deprecated methods by calling the new method instead
	 *
	 * @param  string $name Name of method that was called
	 * @param  array  $args Arguments passed to method
	 * @return mixed  Whatever the return value of the new method is
	 */
	function __call( $name, $args ) {
		// handle deprecated functions
		$deprecated_functions = array(
			'CountMembers' => 'count_members',
		);
		$fxn                  = wlm_arrval( $deprecated_functions, $name );
		if ( $fxn ) {
			trigger_error( __CLASS__ . '->' . $name . '() is deprecated. Use ' . __CLASS__ . '->' . $fxn . '() instead.', E_USER_DEPRECATED );
			return call_user_func_array( array( $this, $fxn ), $args );
		}

		throw new \Exception( __CLASS__ . '->' . $name . '() does not exist.' );
	}

	/**
	 * Handle calls to deprecated static methods by calling the new static method instead
	 *
	 * @param  string $name Name of method that was called
	 * @param  array  $args Arguments passed to method
	 * @return mixed  Whatever the return value of the new method is
	 */
	static function __callStatic( $name, $args ) {
		// handle deprecated functions
		$deprecated_functions = array(
			'GetAllLevels'      => 'get_all_levels',
			'UpdateLevelsCount' => 'update_levels_count',
		);
		$fxn                  = wlm_arrval( $deprecated_functions, $name );
		if ( $fxn ) {
			error_reporting( E_ALL );
			trigger_error( __CLASS__ . '::' . $name . '() is deprecated. Use ' . __CLASS__ . '::' . $fxn . '() instead.', E_USER_DEPRECATED );
			return call_user_func_array( array( __CLASS__, $fxn ), $args );
		}

		throw new \Exception( __CLASS__ . '::' . $name . '() does not exist.' );
	}

	// CLOSE: MAGIC METHODS

	// START: METHODS

	/**
	 * Save the current level
	 *
	 * @return void
	 */
	public function save() {
		global $WishListMemberInstance;

		$wpm_levels              = $WishListMemberInstance->GetOption( 'wpm_levels' );
		$wpm_levels[ $this->ID ] = $this->data;
		$WishListMemberInstance->SaveOption( 'wpm_levels', $wpm_levels );
	}

	/**
	 * Count Members in Level
	 *
	 * @param bool $activeOnly Set to TRUE to count active members only
	 * @return integer
	 */
	function count_members( $activeOnly = false ) {
		global $wpdb, $WishListMemberInstance;
		$table         = $WishListMemberInstance->TablePrefix . 'userlevels';
		$table_options = $WishListMemberInstance->TablePrefix . 'userlevel_options';

		$member_count = wlm_cache_get( 'wishlist_member_all_levels_members_count', 'wishlist-member' );
		$user_query   = new \WP_User_Query(
			array(
				'fields'      => 'ID',
				'count_total' => false,
			)
		);
		if ( $member_count === false ) {
			$results = $wpdb->get_results( $query = "SELECT `level_id`,COUNT(*) AS `cnt` FROM `{$table}` WHERE `user_id` IN ({$user_query->request}) GROUP BY `level_id`" );
			foreach ( $results as $result ) {
				$member_count[ $result->level_id ] = $result->cnt;
			}
			wlm_cache_set( 'wishlist_member_all_levels_members_count', $member_count, 'wishlist-member' );
		}

		if ( $activeOnly ) {
			$date  = $this->noexpire == 1 ? '1000-00-00 00:00:00' : date( 'Y-m-d H:i:s', strtotime( "-{$this->expire} {$this->calendar}" ) );
			$query = "SELECT COUNT(DISTINCT `ul`.`user_id`) FROM `$table` AS `ul` LEFT JOIN `$table_options` AS `ulo`
				ON `ul`.`ID`=`ulo`.`userlevel_id`
				AND (`ulo`.`option_name` IN ('cancelled','forapproval','unconfirmed','registration_date')
				AND `ulo`.`option_value`<>''
				AND `ulo`.`option_value`<>0
				and `ulo`.`option_value`<='$date')
					WHERE `user_id` IN ({$user_query->request})
					AND `ul`.`level_id`=$this->ID
					AND `ulo`.`userlevel_id` IS NULL";
			return $wpdb->get_var( $query );
		} else {

			return ( isset( $member_count[ $this->ID ] ) ? $member_count[ $this->ID ] : '' );
		}
	}

	// === CLOSE: METHODS ===

	// === START: STATIC METHODS ==

	/**
	 * Checks all levels and returns TRUE if at least one has 'autocreate_account_enable' set to 1. Returns FALSE otherwise
	 *
	 * @return bool
	 */
	static function any_can_autocreate_account_for_integration() {
		global $WishListMemberInstance;

		$wpm_levels = $WishListMemberInstance->GetOption( 'wpm_levels' );
		foreach ( $wpm_levels as $level ) {
			if ( ! empty( $level['autocreate_account_enable'] ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get all Membership Levels
	 *
	 * @global object $WishListMemberInstance
	 * @param boolean $fullData Set to TRUE to return complete level information or FALSE to return just the IDs
	 * @return array
	 */
	static function get_all_levels( $fullData = false ) {
		global $WishListMemberInstance;

		$levels = $WishListMemberInstance->GetOption( 'wpm_levels' );
		if ( ! is_array( $levels ) ) {
			return array();
		}
		$levelIDs = array_keys( $levels );
		if ( $fullData ) {
			$levels = array();
			foreach ( $levelIDs as $levelID ) {
				$level = new \WishListMember\Level( $levelID );
				if ( $level->ID == $levelID ) {
					$levels[] = $level;
				}
			}
			return $levels;
		} else {
			return $levelIDs;
		}
	}

	/**
	 * Update the member count of all membership levels
	 *
	 * @global $WishListMemberInstance
	 */
	static function update_levels_count() {
		global $WishListMemberInstance;
		$levels     = self::get_all_levels( true );
		$wpm_levels = $WishListMemberInstance->GetOption( 'wpm_levels' );
		foreach ( $levels as $level ) {
			$wpm_levels[ $level->ID ]['count'] = $level->count_members();
		}
		$WishListMemberInstance->SaveOption( 'wpm_levels', $wpm_levels );
	}

	// === CLOSE: STATIC METHODS ===
}
