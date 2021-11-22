<?php

namespace WishListMember;

defined( 'ABSPATH' ) || die();

/**
 * WishListMember_User_Search extends \WP_User_Query to allow for better member (a.k.a. user) search
 */
class User_Search extends \WP_User_Query {

	public $additional_filters;

	/**
	 * Constructor
	 *
	 * @param string $search_term
	 * @param string $page
	 * @param string $role
	 * @param string $ids
	 * @param string $sortby
	 * @param string $sortorder
	 * @param int    $howmany
	 * @param array  $more_filters
	 */
	function __construct( $search_term = '', $page = '', $role = '', $ids = 'no longer used', $sortby = '', $sortorder = '', $howmany = 15, $more_filters = array() ) {

		if ( empty( $page ) ) {
			$page = 1;
		}

		$query = array(
			'offset'  => ( $page - 1 ) * $howmany,
			'role'    => $role,
			'number'  => $howmany,
			'orderby' => $sortby,
			'order'   => $sortorder,
			'fields'  => 'ID',
		);
		// add search term if not empty
		if ( trim( $search_term ) != '' ) {
			$query['search']         = '*' . trim( $search_term ) . '*';
			$query['search_columns'] = array( 'user_login', 'user_nicename', 'user_email' );
		}

		$this->SortOrder      = $sortorder ? $sortorder : 'ASC';
		$this->search_term    = $search_term;
		$this->users_per_page = $howmany;
		$this->total_users    = $this->total_users_for_query;

		$this->additional_filters = $more_filters;
		parent::__construct( $query );
	}

	/**
	 * filter to add display name in the search
	 */
	function user_search_columns_filter( $cols ) {
		if ( ! in_array( 'display_name', $cols ) ) {
			$cols[] = 'display_name';
		}
		return $cols;
	}


	/**
	 * our own prepare_query
	 * first, we call the original one
	 * then do our own stuff for
	 * levels, statuses, etc.
	 */
	function prepare_query( $query = array() ) {
		global $wpdb;
		global $WishListMemberInstance;

		// add display_name to search columns
		add_filter( 'user_search_columns', array( $this, 'user_search_columns_filter' ), 10, 3 );
		parent::prepare_query( $query );
		add_filter( 'user_search_columns', array( $this, 'user_search_columns_filter' ), 10 );

		$wpm_levels = $WishListMemberInstance->GetOption( 'wpm_levels' );

		$search_sql = array();

		$this->additional_filters['level']  = trim( $this->additional_filters['level'] );
		$this->additional_filters['status'] = isset( $this->additional_filters['status'] ) ? trim( $this->additional_filters['status'] ) : false;

		/**
		 * Filters By Transaction ID
		 */
		$transactionid = isset( $this->additional_filters['transactionid'] ) ? $this->additional_filters['transactionid'] : false;
		if ( $transactionid ) {
			$search_sql[] = $wpdb->prepare( '( ulo.option_name=%s AND ulo.option_value LIKE %s )', 'transaction_id', '%' . $wpdb->esc_like( $transactionid ) . '%' );
		}

		/**
		 * Filters By User Address
		*/
		$useraddress = isset( $this->additional_filters['useraddress'] ) ? $this->additional_filters['useraddress'] : false;
		if ( $useraddress ) {
			$search_sql[] = $wpdb->prepare( '( uo.option_name=%s AND uo.option_value LIKE %s )', 'wpm_useraddress', '%' . $wpdb->esc_like( $useraddress ) . '%' );
		}

		/**
		 * Filters By Membership Level
		 * incomplete/nonmembers has special handling
		 */
		$level = $this->additional_filters['level'];
		if ( ! empty( $level ) ) {
			if ( $level == 'incomplete' ) {
				$search_sql[] = "$wpdb->users.user_login REGEXP '^temp_[a-f0-9]{32}'";
				$search_sql[] = "$wpdb->users.user_login = $wpdb->users.user_email";
			} elseif ( $level == 'nonmembers' ) {
				$search_sql[]                       = '(ul.level_id  IS NULL)';
				$this->additional_filters['status'] = '';
			} elseif ( $level == 'members' ) {
				/** For WLM 3.0 Members are users with at least 1 membership level regardless of the status */
				$mlevels        = \WishListMember\Level::get_all_levels();
				$levels_implode = "'" . implode( "','", $mlevels ) . "'";
				$user_query     = new \WP_User_Query(
					array(
						'fields'      => 'ID',
						'count_total' => false,
						'blog_id'     => $GLOBALS['blog_id'],
					)
				);
				$query          = "SELECT DISTINCT `user_id` FROM `{$WishListMemberInstance->Tables->userlevels}` WHERE `level_id` IN ($levels_implode) AND `user_id` IN ({$user_query->request}) ORDER BY `user_id`";
				$search_sql[]   = " ( $wpdb->users.ID IN  (" . $query . ') ) ';
				/** This code wont work if you have so many members, too long query string */
				// $membersid = $WishListMemberInstance->MemberIDs();
				// $membersid = count($membersid) > 0 ? $membersid : array(0);
				// $search_sql[] = " ( $wpdb->users.ID IN  (".implode(',', $membersid).") ) ";
				$search_sql[] = " ( $wpdb->users.ID NOT IN  (0) ) ";
			} else {
				$search_sql[] = $wpdb->prepare( 'ul.level_id=%s', $this->additional_filters['level'] );
			}
		}

		/** Filters By Sequential Status */
		$sequential_filter = isset( $this->additional_filters['sequential'] ) ? $this->additional_filters['sequential'] : false;
		if ( $sequential_filter ) {
			$filter       = $sequential_filter == 'on' ? 1 : 0;
			$search_sql[] = $wpdb->prepare( "( uo.option_name='sequential' AND uo.option_value=%d ) ", $filter );
		}

		/**
		 * Filters By Status
		 * Note that expired members are handled differently
		 */
		$status = isset( $this->additional_filters['status'] ) ? $this->additional_filters['status'] : false;
		if ( $status ) {

			$expired_sql  = array();
			$inactive_sql = array();
			$active_sql   = array();

			// expired members are specially handled
			$ids = array();

			if ( isset( $level ) && ! in_array( $level, array( 'nonmembers', 'incomplete', 'members' ) ) ) {
				$expiredmembers = $WishListMemberInstance->ExpiredMembersID( false, $level );
			} else {
				$expiredmembers = $WishListMemberInstance->ExpiredMembersID();
			}

			// flatten the result
			$ids = call_user_func_array( 'array_merge', $expiredmembers );
			if ( empty( $ids ) ) {
				$ids = array( -1 );
			}
			$expired_sql[] = "$wpdb->users.ID IN (" . implode( ',', $ids ) . ')';

			$inactives = array( 'cancelled', 'unconfirmed', 'forapproval' );
			foreach ( $inactives as $i ) {
				$inactive_sql[] = $wpdb->prepare( '( ulo.option_name=%s AND ulo.option_value=%d )', $i, 1 );
			}

			switch ( $status ) {
				case 'active':
						/**
						 * Inactive = Expired OR Cancelled OR Needs Approval OR Needs Email Confirmation
						 * Active = !Inactive
						 */
							// $cancelledMemberIDs = $WishListMemberInstance->CancelledMemberIDs();
							// $unConfirmedMemberIDs = $WishListMemberInstance->UnConfirmedMemberIDs();
							// $approvalMemberIDs = $WishListMemberInstance->ForApprovalMemberIDs();
						// Funny,  unlike above we need flatten the $expiredmembers result.
							// $expiredmembersIDs  = call_user_func_array('array_merge', $expiredmembers);
						// Alright sparky. let's exclude who we dont want!!
							// if(!empty($cancelledMemberIDs)) $search_sql[] = " ( $wpdb->users.ID NOT IN  (".implode(',', $cancelledMemberIDs).") ) ";
							// if(!empty($unConfirmedMemberIDs)) $search_sql[] = " ( $wpdb->users.ID NOT IN  (".implode(',', $unConfirmedMemberIDs).") ) ";
							// if(!empty($approvalMemberIDs)) $search_sql[] = " ( $wpdb->users.ID NOT IN  (".implode(',', $approvalMemberIDs).") ) ";
							// if(!empty($expiredmembersIDs)) $search_sql[] = " ( $wpdb->users.ID NOT IN  (".implode(',', $expiredmembersIDs).") ) ";

					/** For WLM 3.0 ACtive members are users with at least 1 active membership level */

					if ( ! empty( $level ) ) {
						$activeids = $WishListMemberInstance->ActiveMemberIDs( $level, false, false );
					} else {
						$activeids = $WishListMemberInstance->ActiveMemberIDs( null, false, false );
					}

					$activeids = count( $activeids ) > 0 ? $activeids : array( 0 );
					// var_dump($wpdb->last_query);
					// IF THIS CODE HAS ERRORS, use last query instead of activeids
					$search_sql[] = " ( $wpdb->users.ID IN  (" . implode( ',', $activeids ) . ') ) ';
					$search_sql[] = " ( $wpdb->users.ID NOT IN  (0) ) ";

					break;
				case 'expired':
					$search_sql = array_merge( $search_sql, $expired_sql );
					break;
				case 'inactive':
					$or_sql       = array_merge( $expired_sql, $inactive_sql );
					$search_sql[] = '(' . implode( ' OR ', $or_sql ) . ')';
					break;
				case 'scheduled':
						$search_sql[] = "( ulo.option_name LIKE 'scheduled_%' )";
					break;
				case 'cancelled':
				case 'unconfirmed':
				case 'forapproval':
				case 'sequential_cancelled':
					if ( ! $transactionid ) {
						$search_sql[] = $wpdb->prepare( '( ulo.option_name=%s AND ulo.option_value=%d )', $status, 1 );
					}
				default:
					break;
			}
		}

		/**
		 * Filter by Date Ranges
		 * Again, due to expired being computed on the fly
		 * it has to be handled in a specific way
		 */
		$date_meta = ! empty( $this->additional_filters['date_type'] ) ? $this->additional_filters['date_type'] : false;
		if ( $date_meta ) {
			// no real option rather than initiate a sub-query since dates are stored as strings
			if ( $date_meta == 'expiration_date' ) {
				$ids             = array();
				$expired_ts_from = strtotime( $this->additional_filters['from_date'] );
				$expired_ts_to   = strtotime( $this->additional_filters['to_date'] );
				if ( $expired_ts_to <= 0 ) {
					$expired_ts_to = time();
				}
				$expiredmembers = $WishListMemberInstance->ExpiredMembersID();
				foreach ( $expiredmembers as $level_id => $expired_per_level ) {
					foreach ( $expired_per_level as $user_id ) {
						$expired_ts = $WishListMemberInstance->LevelExpireDate( $level_id, $user_id );
						if ( ( $expired_ts >= $expired_ts_from ) && ( $expired_ts <= $expired_ts_to ) ) {
							$ids[] = $user_id;
						}
					}
				}
			} else {
				$level_filter = '';
				if ( isset( $level ) && ! in_array( $level, array( 'nonmembers', 'incomplete', 'members' ) ) ) {
					$level_filter = $level;
				}

				$ids = $WishListMemberInstance->GetMembersIDByDateRange( $date_meta, $this->additional_filters['from_date'], $this->additional_filters['to_date'], $level_filter );
			}
			// nothing found? force to return nothing
			if ( empty( $ids ) ) {
				$ids = array( -1 );
			}
			$search_sql[] = "$wpdb->users.ID IN (" . implode( ',', $ids ) . ')';

		}
		if ( ! empty( $search_sql ) ) {
			$search_sql         = implode( ' AND ', $search_sql );
			$this->query_where .= " AND $search_sql";
		}

		$this->query_orderby = "GROUP BY $wpdb->users.ID $this->query_orderby";

		$this->query_from .= ''
			. " LEFT JOIN {$WishListMemberInstance->Tables->userlevels} ul on ($wpdb->users.ID=ul.user_id)"
			. " LEFT JOIN {$WishListMemberInstance->Tables->userlevel_options} ulo on (ulo.userlevel_id=ul.ID)"
			. " LEFT JOIN {$WishListMemberInstance->Tables->user_options} uo on ($wpdb->users.ID=uo.user_id)";
	}

	function query() {
		global $wpdb;
		global $WishListMemberInstance;

		$qv =& $this->query_vars;

		// We will only sort by level registration date if we're filtering by membership level
		$level = trim( $this->additional_filters['level'] );

		if ( ( ! empty( $level ) ) && ( $level != 'incomplete' ) && ( $level != 'nonmembers' ) && ( $level != 'members' ) ) {

			$this->query_from = " FROM $wpdb->users"
			. " LEFT JOIN $wpdb->usermeta on ($wpdb->users.ID=$wpdb->usermeta.user_id)"
			. " LEFT JOIN {$WishListMemberInstance->Tables->userlevels} ul on ($wpdb->users.ID=ul.user_id)"
			. " LEFT JOIN {$WishListMemberInstance->Tables->userlevel_options} ulo on (ulo.userlevel_id=ul.ID)"
			. " LEFT JOIN {$WishListMemberInstance->Tables->user_options} uo on ($wpdb->users.ID=uo.user_id)";

			$query            = "SELECT DISTINCT($wpdb->users.ID), ulo.option_value, ulo.option_name $this->query_from $this->query_where $this->query_orderby $this->query_limit";
			$unprocessed_data = $wpdb->get_results( $query, ARRAY_A );

			// Run this query without limit so we can get the total count of users found, (For Pagination)
			$wpdb->get_results( "SELECT DISTINCT($wpdb->users.ID), ulo.option_value, ulo.option_name $this->query_from $this->query_where $this->query_orderby " );

			$levels_data = array();

			// loop through results and convert date to timestamp for easier sorting
			foreach ( $unprocessed_data as $data ) {

				// if there's no registration_date, it means the level is not active, all non active members will
				// be at the bottom of the result, will be on top if sorted reveresed based on registration date
				if ( $data['option_name'] == 'registration_date' ) {
					$date      = explode( '#', $data['option_value'] );
					$timestamp = strtotime( $date[0] );
				} else {
					$timestamp = strtotime( time() );
				}

				$levels_data[ $timestamp . '-' . $data['ID'] ] = $data['ID'];
			}

			if ( $this->SortOrder == 'ASC' ) {
				ksort( $levels_data );
			} else {
				krsort( $levels_data );
			}

			$this->results = $levels_data;
		} else {
			$query = "SELECT $this->query_fields $this->query_from $this->query_where $this->query_orderby $this->query_limit";

			if ( is_array( $qv['fields'] ) || 'all' == $qv['fields'] ) {
				$this->results = $wpdb->get_results( $query );
			} else {
				$this->results = $wpdb->get_col( $query );
			}
		}

		/**
		 * Filter SELECT FOUND_ROWS() query for the current WP_User_Query instance.
		 *
		 * @since 3.2.0
		 *
		 * @global wpdb $wpdb WordPress database object.
		 *
		 * @param string $sql The SELECT FOUND_ROWS() query for the current WP_User_Query.
		 */
		if ( isset( $qv['count_total'] ) && $qv['count_total'] ) {
			$this->total_users = $wpdb->get_var( apply_filters( 'found_users_query', 'SELECT FOUND_ROWS()' ) );
		}

		if ( ! $this->results ) {
			return;
		}

		if ( 'all_with_meta' == $qv['fields'] ) {
			cache_users( $this->results );

			$r = array();
			foreach ( $this->results as $userid ) {
				$r[ $userid ] = new \WP_User( $userid, '', $qv['blog_id'] );
			}

			$this->results = $r;
		} elseif ( 'all' == $qv['fields'] ) {
			foreach ( $this->results as $key => $user ) {
				$this->results[ $key ] = new \WP_User( $user );
			}
		}
	}
}

