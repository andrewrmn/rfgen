<?php
/**
 * Level Option Class for WishList Member
 *
 * @author Mike Lopez <mjglopez@gmail.com>
 * @package wishlistmember
 */

namespace WishListMember;

defined( 'ABSPATH' ) || die();

class Level_Options {

	function __construct( $tblprefix ) {
		$this->Table = $tblprefix . 'level_options';
	}

	function save_option( $lvlid, $name, $data ) {
		global $wpdb;
		$data = array(
			'level_id'     => $lvlid,
			'option_name'  => $name,
			'option_value' => wlm_maybe_serialize( $data ),
		);
		return $wpdb->insert( $this->Table, $data );
	}

	function update_option( $id, $data ) {
		global $wpdb;
		$data = array(
			'option_value' => wlm_maybe_serialize( $data ),
		);
		return $wpdb->update( $this->Table, $data, array( 'ID' => $id ) );
	}

	function delete_option( $id ) {
		global $wpdb;
		return $wpdb->delete( $this->Table, array( 'ID' => $id ) );
	}

	function get_options( $lvlid = null, $name = null, $limit = null ) {
		global $wpdb;

		$sort  = ' ORDER BY ID ASC';
		$limit = (int) $limit;
		$limit = $limit != null ? " LIMIT 0,{$limit}" : '';
		$where = array();
		if ( $lvlid ) {
			$lvlid   = (int) $lvlid;
			$where[] = "level_id={$lvlid}";
		}
		if ( $name ) {
			$where[] = "option_name='{$name}'";
		}
		if ( count( $where ) > 0 ) {
			$where = 'WHERE ' . implode( ' AND ', $where );
		}

		$query = "SELECT * FROM {$this->Table} {$where} {$sort} {$limit}";
		return $wpdb->get_results( $query );
	}

	function get_option( $id ) {
		global $wpdb;
		$query = "SELECT * FROM {$this->Table} WHERE ID={$id}";
		return $wpdb->get_row( $query );
	}
}
