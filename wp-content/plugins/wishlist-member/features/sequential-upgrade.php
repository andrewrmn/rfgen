<?php

namespace WishListMember\Features;

new Sequential_Upgrade();

/**
 * Sequential Upgrade Feature
 */
class Sequential_Upgrade {

	function __construct() {
		// add `wishlistmember_sequential_upgrade` action to be triggered by cron
		add_action( 'wishlistmember_sequential_upgrade', array( $this, 'do_sequential' ) );

		// add sequential upgrade wp-cron triggered every 15 minutes
		if ( ! wp_next_scheduled( 'wishlistmember_sequential_upgrade' ) ) {
			wp_schedule_event( time(), 'wlm_15minutes', 'wishlistmember_sequential_upgrade' );
		}

		// add `wishlistmember_sequential_upgrade` to the list of cron hooks
		// that are to be removed by WLM whenever it finds the need to do so
		add_filter(
			'wishlistmember_remove_cron_hooks',
			function( $hooks ) {
				$hooks[] = 'wishlistmember_sequential_upgrade';
				return $hooks;
			}
		);

		// add our methods to WishList Member's instance
		add_filter(
			'wishlistmember_instance_methods',
			function( $methods ) {
				$methods['DoSequential']  = array( array( $this, 'do_sequential' ), true ); // deprecated
				$methods['do_sequential'] = array( array( $this, 'do_sequential' ) );

				$methods['DoSequentialForUser']    = array( array( $this, 'do_sequential_for_user' ), true ); // deprecated
				$methods['do_sequential_for_user'] = array( array( $this, 'do_sequential_for_user' ) );
        
				return $methods;
			}
		);
	}

	/**
	 * Execute sequential upgrade for all users if called by cron
	 * Execute sequential upgrade for currently logged in user only if not called by cron
	 *
	 * @global object $wpdb
	 * @param int|array $user_ids
	 */
	function do_sequential( $user_ids = '' ) {
		global $wpdb;
		ignore_user_abort( true );
		wishlistmember_instance()->set_timezone_to_wp();

		$wlm_is_doing_sequential_name = 'wlm_is_doing_sequential_' . $_SERVER['REMOTE_ADDR'];

		if ( get_transient( $wlm_is_doing_sequential_name ) == 'yes' ) {
			return;
		}

		set_transient( $wlm_is_doing_sequential_name, 'yes', 60 * 60 * 24 );
		wlm_set_time_limit( 60 * 60 * 12 );

		if ( is_int( $user_ids ) and ! empty( $user_ids ) ) {
			$user_ids = array( $user_ids );
		} elseif ( ! is_array( $user_ids ) ) {
			$user_ids   = array();
			$wpm_levels = (array) wishlistmember_instance()->GetOption( 'wpm_levels' );

			$levels_for_sequential = array();
			foreach ( $wpm_levels as $level_id => $level ) {
				if ( is_int( $level_id ) && ! empty( $level['upgradeTo'] ) && ! empty( $wpm_levels[ $level['upgradeTo'] ] ) ) {
					if ( ! ( ! $level['upgradeTo'] || ! $level['upgradeMethod'] || ( $level['upgradeSchedule'] == 'ondate' && $level['upgradeOnDate'] < 1 ) || ( $level['upgradeMethod'] == 'MOVE' && ! ( (int) $level['upgradeAfter'] ) && empty( $level['upgradeSchedule'] ) ) ) ) {

						  $levels_for_sequential[] = $level_id;
					}
				}
			}
			if ( $levels_for_sequential ) {
				$levels_for_sequential = "'" . implode( "','", $levels_for_sequential ) . "'";
				$user_ids              = $wpdb->get_col( 'SELECT DISTINCT `user_id` FROM `' . wishlistmember_instance()->Tables->user_options . "` WHERE `option_name`='sequential' AND `option_value`='1' AND `user_id` IN (SELECT DISTINCT `user_id` FROM `" . wishlistmember_instance()->Tables->userlevels . "` WHERE `level_id` IN ({$levels_for_sequential}))" );
			}
		}

		if ( ! empty( $user_ids ) ) {
			$force_sync = false;

			$user_ids = array_chunk( $user_ids, 1000 );
			while ( $chunk = array_shift( $user_ids ) ) {
				// wlm_cache_flush(); // cache is flushed on syncmembership
				wishlistmember_instance()->PreLoad_UserLevelsMeta( $chunk );
				while ( $user_id = array_shift( $chunk ) ) {
					if ( true === wishlistmember_instance()->do_sequential_for_user( $user_id ) ) {
						$force_sync = true;
					}
				}
			}

			if ( $force_sync ) {
				wishlistmember_instance()->SyncMembership();
			}
		}

		wlm_set_time_limit( ini_get( 'max_execution_time' ) );

		delete_transient( $wlm_is_doing_sequential_name );

	}

  /**
   * Runs sequential upgrade for the specified user
   * @param  int     $id              User ID
   * @param  boolean $sync_membership True to sync membership details, default False
   */
	function do_sequential_for_user( $id, $sync_membership = false ) {
		static $wpm_levels = null;

		ignore_user_abort( true );
		wishlistmember_instance()->set_timezone_to_wp();

		$id = (int) $id;
		if ( empty( $id ) ) {
			return;
		}

		if ( wishlistmember_instance()->IsTempUser( $id ) ) {
			return;
		}

		// make sure that only one instance for this user is running
		// used time to fix issues with some undesired behaviors if using 1
		$fourteen_minutes = time() - ( 60 * 14 );
		$last_run         = get_transient( 'wlm_is_doing_sequential_for_' . $id );
		if ( false && $last_run && $last_run >= $fourteen_minutes ) {
			return $last_run; }
		set_transient( 'wlm_is_doing_sequential_for_' . $id, time(), 60 * 14 );

		if ( empty( $wpm_levels ) ) {
			$wpm_levels = wishlistmember_instance()->GetOption( 'wpm_levels' );
		}

		$user_levels = new \WishListMember\User( $id );
		if ( ! $user_levels->Sequential ) {
			return;

		}
		$user_levels     = $user_levels->Levels;
		$original_levels = array_keys( $user_levels );
		$processed       = array();
		$time            = time();

		$new_levels                = array();
		$upgradeEmailNotifications = array();
		do {
			$keep_going = false;
			foreach ( $user_levels as $level_id => $user_level ) {
				if ( $user_level->Active ) {
					if ( ! $user_level->SequentialCancelled ) {
						if ( ! in_array( $level_id, $processed ) ) {
							  $processed[ $level_id ] = $level_id;
							  $level_info             = &$wpm_levels[ $level_id ];
							if ( isset( $wpm_levels[ $level_info['upgradeTo'] ] ) || $level_info['upgradeMethod'] == 'REMOVE' ) {
								if ( $level_info['upgradeSchedule'] == 'ondate' ) {
										  $upgrade_date = $level_info['upgradeOnDate'];

								} else {
									$period       = $level_info['upgradeAfterPeriod'] ? $level_info['upgradeAfterPeriod'] : 'days';
									$upgrade_date = strtotime( $x = '+' . $level_info['upgradeAfter'] . ' ' . $period, $user_level->Timestamp );

								}
								if ( $upgrade_date && $time > $upgrade_date ) {

									/*
									start: decide whether to send welcome email or not
									* 0 = do not send
									* 1 = level settings
									* 2 = always send
									*
									* the logic makes sure that the higher setting is obeyed
									* and takes into account the possibility that the seq upgrade
									* went through the same level twice i.e. Levels "A" and "B" upgrades
									* to the same Level "C" and the upgrade happened at the same time.
									* If Level A is set to "always send" (value=2) and Level B is set to
									* "do not send" (value=0) then the email will be sent because 2 > 0
									*/
									$x = (int) wlm_arrval( $upgradeEmailNotifications, $level_info['upgradeTo'] );
									$y = (int) wlm_arrval( $level_info, 'upgradeEmailNotification' );
									if ( $y > $x ) {
										$upgradeEmailNotifications[ $level_info['upgradeTo'] ] = $y;
									}
									/* end: decide whether to send welcome email or not */

									// If the Upgrade To level was already previously processed, we skip the loop
									// This is to avoid the infinite loop on this scenario (Move from Level A to Level B on XXX and Move from Level B to Level A on XXX.)
									if ( in_array( $level_info['upgradeTo'], (array) $new_levels ) ) {
										continue;
									}

									$keep_going = true;
									if ( $level_info['upgradeMethod'] == 'MOVE' || $level_info['upgradeMethod'] == 'REMOVE' ) {
										unset( $processed[ $level_id ] );
										unset( $user_levels[ $level_id ] );

									}
									if ( ! isset( $user_levels[ $level_info['upgradeTo'] ] ) && $level_info['upgradeMethod'] != 'REMOVE' ) {
										$new_levels[]                            = $level_info['upgradeTo'];
										$user_levels[ $level_info['upgradeTo'] ] = (object) array(
											'Timestamp' => $upgrade_date,
											'TxnID'     => $user_level->TxnID,
											'Active'    => true,
										);

									}
								}
							}
						}
					}
				}
			}
		} while ( $keep_going );

		$seqlevels     = array_keys( $user_levels );
		$seqlevels_new = array_diff( $seqlevels, $original_levels );

		wishlistmember_instance()->SetMembershipLevels( $id, $seqlevels, null, true, true, true );

		$ts = array();
		$tx = array();
		foreach ( $user_levels as $level_id => $user_level ) {
			$ts[ $level_id ] = $user_level->Timestamp;
			$tx[ $level_id ] = $user_level->TxnID;

		}

		wishlistmember_instance()->UserLevelTimestamps( $id, $ts );
		wishlistmember_instance()->SetMembershipLevelTxnIDs( $id, $tx );

		/*
		start: send welcome email if configured in seq upgrade */
		// password is always unknown
		$macros = array( '[password]' => '********' );
		foreach ( $seqlevels_new as $level_id ) {
			// set the current email_template_level so send_email_template() knows what level we're dealing with
			wishlistmember_instance()->email_template_level = $level_id;
			// set level name macro
			$macros['[memberlevel]'] = $wpm_levels[ $level_id ]['name'];

			switch ( (int) wlm_arrval( $upgradeEmailNotifications, $level_id ) ) {
				case 2: // always send email
					  // send the per level email template even if it's off
					  // also save the Closure in $x so we can remove the action later
					add_filter(
						'wishlistmember_per_level_template_setting',
						$x = function ( $setting_value, $setting_name, $user_id, $level_id ) {
							if ( 'newuser_notification_user' == $setting_name && $level_id == wishlistmember_instance()->email_template_level ) {
								return true;
							}
							return $setting_value;
						},
						10,
						4
					);
				case 1: // send email according to level setting
					  // send the email template
					  wishlistmember_instance()->send_email_template( 'registration', $id, $macros );

					  // since we are in a loop we remove our 'wishlistmember_per_level_template_setting' filter
					  // because we do not want multiple filters of the same type being registered
					if ( ! empty( $x ) ) {
						remove_filter( 'wishlistmember_per_level_template_setting', $x );
						unset( $x );
					}
					break;
				case 0: // do nothing. sequential upgrade does not send emails by default
				default:
					break;

			}
		}
		unset( wishlistmember_instance()->send_email_template );
		/* end: send welcome email if configured in seq upgrade */

		do_action( 'wlm_do_sequential_upgrade', $id, $seqlevels_new, $seqlevels );

		if ( $sync_membership ) {
			wishlistmember_instance()->SyncMembership();
		}

		return true;

	}

}
