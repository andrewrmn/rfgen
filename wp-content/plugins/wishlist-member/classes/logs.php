<?php

/**
 * WishList Member Logs API
 */
namespace WishListMember;

class Logs {
	/**
	 * Add log entry
	 *
	 * @uses \wpdb::replace
	 *
	 * @param int             $user_id
	 * @param string          $log_group
	 * @param string          $log_key
	 * @param mixed           $data
	 * @param string|int|null $timestamp Unix timestamp or a valid date string
	 * @return int|false The number of rows inserted/affected, or false on error
	 */
	static function add( $user_id, $log_group, $log_key, $log_value, $timestamp = null ) {
		global $wpdb, $WishListMemberInstance;
		if ( empty( $user_id ) || empty( $log_group ) || empty( $log_key ) || empty( $log_value ) ) {
			return false;
		}

		$timestamp = self::_compute_time( $timestamp );

		$data = array(
			'user_id'    => (int) $user_id,
			'log_group'  => (string) $log_group,
			'log_key'    => (string) $log_key,
			'date_added' => $timestamp,
			'log_value'  => wlm_maybe_serialize( $log_value ),
		);
		return $wpdb->replace( $WishListMemberInstance->Tables->logs, $data );
	}

	/**
	 * Retrieve log entries
	 *
	 * @uses \wpdb::get_results
	 *
	 * @param int             $user_id
	 * @param string|null     $log_group
	 * @param string|null     $log_key
	 * @param string|int|null $timestamp Unix timestamp or a valid date string
	 * @return array|null Database query results
	 */
	static function get( $user_id, $log_group = null, $log_key = null, $timestamp = null ) {
		global $wpdb, $WishListMemberInstance;

		$where = array( $wpdb->prepare( '`user_id`=%d', $user_id ) );

		if ( $log_group ) {
			$where [] = $wpdb->prepare( '`log_group` = %s', $log_group );
		}
		if ( $log_key ) {
			$where [] = $wpdb->prepare( '`log_key` = %s', $log_key );
		}
		if ( $timestamp ) {
			$where[] = $wpdb->prepare( '`date_added` = %s', self::_compute_time( $timestamp ) );
		}

		$where = ' WHERE ' . implode( ' AND ', $where );

		$query = "SELECT * FROM `{$WishListMemberInstance->Tables->logs}` " . $where ." ORDER BY date_added DESC";

		return $wpdb->get_results( $query );
	}

	/**
	 * Delete log entries
	 *
	 * @uses \wpdb::delete
	 *
	 * @param int             $user_id
	 * @param string|null     $log_group
	 * @param string|null     $log_key
	 * @param string|int|null $timestamp Unix timestamp or a valid date string
	 * @return int|false The number of rows deleted, or false on error.
	 */
	static function delete( $user_id, $log_group = null, $log_key = null, $timestamp = null ) {
		global $wpdb, $WishListMemberInstance;

		$data = array(
			'user_id' => (int) $user_id,
		);
		if ( $log_group ) {
			$data['log_group'] = (string) $log_group;
		}
		if ( $log_key ) {
			$data['log_key'] = (string) $log_key;
		}
		if ( $timestamp ) {
			$data['date_added'] = self::_compute_time( $timestamp );
		}

		var_dump( $data );

		return $wpdb->delete( $WishListMemberInstance->Tables->logs, $data );

	}

	/**
	 * Converts $timestamp into MySQL datatime format
	 * Generates datetime from time() if passed an empty or invalid $timestamp
	 *
	 * @param string|int $timestamp Unix timestamp or a valid date string
	 * @return string MySQL datetime
	 */
	private static function _compute_time( $timestamp ) {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}\d{2}$/', $timestamp ) ) {
			if ( ! is_numeric( $timestamp ) ) {
				$timestamp = strtotime( $timestamp ) ?: time();
			}
			$timestamp = gmdate( 'Y-m-d H:i:s', $timestamp );
		}
		return $timestamp;
	}
}
