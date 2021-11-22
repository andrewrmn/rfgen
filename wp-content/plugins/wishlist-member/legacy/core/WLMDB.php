<?php

/**
 * Plugin Methods Class for WishList Member
 *
 * @author Mike Lopez <mjglopez@gmail.com>
 * @package wishlistmember
 *
 * @version $$
 * $LastChangedBy: mike $
 * $LastChangedDate: 2021-01-14 13:28:49 -0500 (Thu, 14 Jan 2021) $
 */
if ( ! defined( 'ABSPATH' )
) {
	die();
}
if ( ! class_exists( 'WishListMemberDBMethods' ) ) {

	/**
	 * Plugin Methods WishList Member Class
	 *
	 * @package wishlistmember
	 * @subpackage classes
	 */
	class WishListMemberDBMethods extends WishListMemberCore {

		/**
		 * Migrate Level Information for all Content to Database
		 *
		 * @global object $wpdb
		 */
		function MigrateContentLevelData() {
			global $wpdb;

			if ( get_option( $this->PluginOptionName . '_MigrateContentLevelData' ) == 1 ) {
				return;
			}

			ignore_user_abort( true );
			// migrate category levels
			$content_types = array(
				'MembershipCategories' => '~CATEGORY',
				'MembershipPages'      => 'page',
				'MembershipPosts'      => 'post',
				'MembershipComments'   => '~COMMENT',
			);

			foreach ( $content_types as $Option => $content_type ) {
				$data = $this->GetOption( $Option );
				foreach ( (array) $data as $level => $ids ) {
					$ids = array_diff( array_unique( explode( ',', $ids ) ), array( '0', '' ) );
					if ( count( $ids ) ) {
						foreach ( $ids as $id ) {
							if ( is_numeric( $id ) ) {
								$wpdb->insert(
									$this->Tables->contentlevels,
									array(
										'content_id' => $id,
										'level_id'   => $level,
										'type'       => $content_type,
									),
									array( '%d', '%s', '%s' )
								);
							}
						}
					}
				}
			}

			// category protection
			$ids = array_diff( get_terms( array( 'taxonomy' => 'category', 'fields' => 'ids', 'get' => 'all' ) ), explode( ',', $this->GetOption( 'CatProtect' ) ) );
			if ( count( $ids ) ) {
				foreach ( $ids as $id ) {
					if ( is_numeric( $id ) ) {
						$wpdb->insert(
							$this->Tables->contentlevels,
							array(
								'content_id' => $id,
								'level_id'   => 'Protection',
								'type'       => '~CATEGORY',
							),
							array( '%d', '%s', '%s' )
						);
					}
				}
			}

			// category protection
			$ids = array_diff( array_unique( explode( ',', $this->GetOption( 'Protect' ) ) ), array( '0', '' ) );
			if ( count( $ids ) ) {
				foreach ( $ids as $id ) {
					if ( is_numeric( $id ) ) {
						$wpdb->insert(
							$this->Tables->contentlevels,
							array(
								'content_id' => $id,
								'level_id'   => 'Protection',
								'type'       => 'post',
							),
							array( '%d', '%s', '%s' )
						);
					}
				}
			}

			update_option( $this->PluginOptionName . '_MigrateContentLevelData', 1 );
		}

		/**
		 * Migrate Level Information
		 *
		 * @global object $wpdb
		 */
		function MigrateLevelData() {
			global $wpdb;

			if ( $this->DataMigrated != 1 || get_option( $this->PluginOptionName . '_MigrateLevelData' ) == 1 ) {
				return;
			}

			ignore_user_abort( true );
			$this->CreateWLMDBTables();

			$userlevelsTable        = $this->Tables->userlevels;
			$userlevelsTableOptions = $this->Tables->userlevel_options;
			$userTableOptions       = $this->Tables->user_options;

			// user level data
			$memberLevels = (array) $this->GetOption( 'Members' );
			$cancelled    = (array) $this->GetOption( 'Cancelled' );
			$unconfirmed  = (array) $this->GetOption( 'UnConfirmed' );
			$forapproval  = (array) $this->GetOption( 'Pending' );

			$levels     = \WishListMember\Level::get_all_levels();
			$allmembers = array();
			foreach ( $levels as $level ) {
				$members    = array_unique( explode( ',', $memberLevels[ $level ] ) );
				$allmembers = array_merge( $allmembers, $members );

				foreach ( $members as $member ) {
					/* Membership Level */
					$data = array(
						'user_id'  => $member,
						'level_id' => $level,
					);
					if ( $wpdb->insert( $userlevelsTable, $data ) ) {
						$userlevel_id = $wpdb->insert_id;

						/* Transaction IDs */
						$trans = get_usermeta( $member, 'wlm_sctxns' );
						$data  = array(
							'userlevel_id' => $userlevel_id,
							'option_name'  => 'transaction_id',
							'option_value' => wlm_maybe_serialize( $trans[ $level ] ),
						);
						$wpdb->insert( $userlevelsTableOptions, $data );

						/* Level Registration Dates */
						$regdates = get_usermeta( $member, 'wpm_leveltimestamp' );
						$this->UserLevelTimestamp( $member, $level, $regdates[ $level ] );

						/* Cancelled Status */
						$status = preg_match( '/,(' . $member . ';[0-9]*),/', ',' . $cancelled[ $level ] . ',', $match ) > 0;
						if ( $status ) {
							list($id, $date) = explode( ';', $match[1] );
							$this->LevelCancelled( $level, $member, true, $date );
						}

						/* Unconfirmed Status */
						$status = preg_match( '/,' . $member . ',/', ',' . $unconfirmed[ $level ] . ',' ) > 0;
						if ( $status ) {
							$this->LevelUnConfirmed( $level, $member, true );
						}

						/* For Approval Status */
						$status = preg_match( '/,' . $member . ',/', ',' . $forapproval[ $level ] . ',' ) > 0;
						if ( $status ) {
							$this->LevelForApproval( $level, $member, true );
						}
					}
				}
			}

			$allmembers = array_unique( $allmembers );

			// per user data
			$nonseq = array_unique( explode( ',', wlm_arrval( $memberLevels, 'nonsequential' ) ) );
			foreach ( $allmembers as $member ) {
				// sequential upgrade
				$seq  = in_array( $member, $nonseq ) ? 0 : 1;
				$data = array(
					'user_id'      => $member,
					'option_name'  => 'sequential',
					'option_value' => wlm_maybe_serialize( $seq ),
				);
				$wpdb->insert( $userTableOptions, $data );
			}
			// migrate all wpm_ and wlm_ data except wpm_leveltimestamp and wlm_sctxns
			$query = "INSERT INTO `$userTableOptions` (`user_id`,`option_name`,`option_value`)
				SELECT `user_id`,`meta_key`,`meta_value` FROM `{$wpdb->usermeta}`
					WHERE `meta_key`<>'wpm_leveltimestamp'
					AND `meta_key`<>'wlm_sctxns'
					AND (`meta_key` LIKE 'wlm%' OR `meta_key` LIKE 'wpm%')";
			$wpdb->query( $query );

			/*
			 * remove old data format for membership levels, cancelled status,
			 * unconfirmed status, and pending status from our options table
			 */

			$this->DeleteOption( 'Members' );
			$this->DeleteOption( 'Cancelled' );
			$this->DeleteOption( 'UnConfirmed' );
			$this->DeleteOption( 'Pending' );

			/* end of data migration */
			update_option( $this->PluginOptionName . '_MigrateLevelData', 1 );
		}

		/**
		 * Create WishList Member Database Tables
		 */
		function CreateWLMDBTables() {
			global $wpdb;

			/*
			 * Important: This now makes use of dbDelta function
			 *
			 * Please refer to the following URL for instructions:
			 * http://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
			 *
			 * VIOLATORS OF dbDelta RULES WILL BE PROSECUTED :D
			 */

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$charset_collate = $wpdb->get_charset_collate();

			$table = $this->TablePrefix .'level_options';
			$structure = "CREATE TABLE {$table} (
			  ID bigint(20) NOT NULL AUTO_INCREMENT,
			  level_id bigint(20) NOT NULL,
			  option_name varchar(64) NOT NULL,
			  option_value longtext NOT NULL,
			  autoload varchar(20) NOT NULL DEFAULT 'yes',
			  PRIMARY KEY  (ID)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* Content Levels */
			$table     = $this->TablePrefix . 'contentlevels';
			$structure = "CREATE TABLE {$table} (
			  ID bigint(20) NOT NULL AUTO_INCREMENT,
			  content_id bigint(20) NOT NULL,
			  level_id varchar(32) NOT NULL,
			  type varchar(21) NOT NULL,
			  PRIMARY KEY  (ID),
			  UNIQUE KEY content_id (content_id,level_id,type),
			  KEY content_id2 (content_id),
			  KEY level_id (level_id),
			  KEY type (type)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* Content Levels Options */
			$table     = $this->TablePrefix . 'contentlevel_options';
			$structure = "CREATE TABLE {$table} (
			  ID bigint(20) NOT NULL AUTO_INCREMENT,
			  contentlevel_id bigint(20) NOT NULL,
			  option_name varchar(64) NOT NULL,
			  option_value longtext NOT NULL,
			  autoload varchar(20) NOT NULL DEFAULT 'yes',
			  PRIMARY KEY  (ID),
			  UNIQUE KEY contentlevel_id (contentlevel_id,option_name),
			  KEY autoload (autoload),
			  KEY contentlevel_id2 (contentlevel_id),
			  KEY option_name (option_name)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* User Levels */
			$table     = $this->TablePrefix . 'userlevels';
			$structure = "CREATE TABLE {$table} (
			  ID bigint(20) NOT NULL AUTO_INCREMENT,
			  user_id bigint(20) NOT NULL,
			  level_id bigint(20) NOT NULL,
			  PRIMARY KEY  (ID),
			  UNIQUE KEY user_id (user_id,level_id),
			  KEY user_id2 (user_id),
			  KEY level_id (level_id)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* User Level Options Levels */
			$table     = $this->TablePrefix . 'userlevel_options';
			$structure = "CREATE TABLE {$table} (
			  ID bigint(20) NOT NULL AUTO_INCREMENT,
			  userlevel_id bigint(20) NOT NULL,
			  option_name varchar(64) NOT NULL,
			  option_value longtext NOT NULL,
			  autoload varchar(20) NOT NULL DEFAULT 'yes',
			  PRIMARY KEY  (ID),
			  UNIQUE KEY userlevel_id (userlevel_id,option_name),
			  KEY autoload (autoload),
			  KEY userlevel_id2 (userlevel_id),
			  KEY option_name (option_name)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* User Options */
			$table     = $this->TablePrefix . 'user_options';
			$structure = "CREATE TABLE {$table} (
			  ID bigint(20) NOT NULL AUTO_INCREMENT,
			  user_id bigint(20) NOT NULL,
			  option_name varchar(64) NOT NULL,
			  option_value longtext NOT NULL,
			  autoload varchar(20) NOT NULL DEFAULT 'yes',
			  PRIMARY KEY  (ID),
			  UNIQUE KEY user_id (user_id,option_name),
			  KEY autoload (autoload),
			  KEY user_id2 (user_id),
			  KEY option_name (option_name)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* API Queue */
			$table     = $this->TablePrefix . 'api_queue';
			$structure = "CREATE TABLE {$table} (
			  ID bigint(20) NOT NULL AUTO_INCREMENT,
			  name varchar(64) CHARACTER SET latin1 NOT NULL,
			  value longtext NOT NULL,
			  notes varchar(500) CHARACTER SET latin1 DEFAULT NULL,
			  tries int(11) NOT NULL,
			  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY  (ID)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* WLM Logs */
			$table     = $this->TablePrefix . 'logs';
			$structure = "CREATE TABLE {$table} (
			  ID bigint NOT NULL AUTO_INCREMENT,
			  user_id bigint NOT NULL,
			  log_group varchar(32) NOT NULL,
			  log_key varchar(64) NOT NULL,
  			  date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  log_value longtext NOT NULL,
			  PRIMARY KEY  (ID),
			  UNIQUE KEY user_id__log_group__log_key__date_added (user_id,log_group,log_key,date_added),
			  KEY user_id (user_id),
			  KEY user_id__log_group (user_id,log_group),
			  KEY user_id__log_group__log_key (user_id,log_group,log_key)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* Email Broadcast Table */
			$table     = $this->TablePrefix . 'emailbroadcast';
			$structure = "CREATE TABLE {$table} (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  from_name varchar(200) NOT NULL,
			  from_email varchar(200) NOT NULL,
			  subject varchar(400) NOT NULL,
			  text_body text NOT NULL,
			  footer text,
			  send_to varchar(15) DEFAULT NULL,
			  mlevel text NOT NULL,
			  sent_as varchar(5) DEFAULT NULL,
			  status varchar(10) NOT NULL DEFAULT 'Queueing',
			  otheroptions text,
			  total_queued int(11) DEFAULT '0',
			  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  UNIQUE KEY id (id),
			  PRIMARY KEY  (id)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* Email Queue */
			$table     = $this->TablePrefix . 'email_queue';
			$structure = "CREATE TABLE {$table} (
			  id bigint(20) NOT NULL AUTO_INCREMENT,
			  broadcastid int(9) NOT NULL,
			  userid bigint(20) NOT NULL,
			  failed int(1) NOT NULL DEFAULT '0',
			  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  UNIQUE KEY id (id),
			  KEY broadcastid (broadcastid)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			// let s check if we already have a foreign key
			// if not, lets add it
			// if we have, let check if the script already made multiple instance of FKs and delete them if it did
			$dbname = DB_NAME;
			$q      = "SELECT CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = '$dbname' AND TABLE_NAME = '{$table}' AND REFERENCED_TABLE_NAME = '{$this->TablePrefix}emailbroadcast'";
			$fks    = $wpdb->get_col( $q );
			if ( count( $fks ) > 0 ) {
				unset( $fks[0] ); // lets leave the first one
				if ( count( $fks ) > 0 ) { // delete the others
					foreach ( $fks as $fk ) {
						$wpdb->query( "ALTER TABLE {$table} DROP FOREIGN KEY `{$fk}`" );
					}
				}
			} else {
				try {
					// enclose in try..catch as this query can fail
					$wpdb->query( "ALTER TABLE {$table} ADD FOREIGN KEY (broadcastid) REFERENCES {$this->TablePrefix}emailbroadcast (id) ON DELETE CASCADE ON UPDATE CASCADE" );
				} catch ( Exception $e ) {}
			}

			// for Scheduler
			$old_table = $wpdb->prefix . 'wlm_contentsched';
			$table     = $wpdb->prefix . 'wlcc_contentsched';
			$dbname    = DB_NAME;
			$structure = "SELECT * FROM Information_schema.tables WHERE table_name='{$old_table}' AND table_schema='" . DB_NAME . "'";
			$tb        = $wpdb->query( $structure );
			if ( $tb ) { // check if the old table exists then rename
				$structure = "ALTER TABLE {$old_table} RENAME {$table}";
				$wpdb->query( $structure );
			}

			$structure = "CREATE TABLE {$table} (
			  id bigint(20) NOT NULL auto_increment,
			  post_id bigint(20) NOT NULL,
			  mlevel varchar(15) NOT NULL,
			  num_days int(11) NOT NULL,
			  date_added timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  PRIMARY KEY  (id)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;
			// lets add hide_days NOTE: dbDelta seems not to work
			$structure = "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='{$dbname}' AND TABLE_NAME='{$table}' AND COLUMN_NAME='hide_days'";
			$isexist   = (bool) $wpdb->get_var( $structure );
			if ( ! $isexist ) {
				$structure = "ALTER TABLE `{$table}` ADD `hide_days` int(11) NOT NULL DEFAULT 0 AFTER `num_days`";
				$wpdb->query( $structure );
			}

			// For Archiver
			$table     = $wpdb->prefix . 'wlcc_contentarchiver';
			$structure = "CREATE TABLE {$table} (
			  id bigint(20) NOT NULL auto_increment,
			  post_id bigint(20) NOT NULL,
			  mlevel varchar(15) NOT NULL,
			  exp_date datetime,
			  date_added timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  PRIMARY KEY  (id)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			// for Manager
			$table     = $wpdb->prefix . 'wlcc_contentmanager_repost';
			$structure = "CREATE TABLE {$table} (
			  id bigint(20) NOT NULL auto_increment,
			  post_id bigint(20) NOT NULL,
			  due_date datetime NOT NULL,
			  rep_num int,
			  rep_by varchar(10),
			  rep_end int,
			  date_added timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  PRIMARY KEY  (id)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			$table     = $wpdb->prefix . 'wlcc_contentmanager_move';
			$structure = "CREATE TABLE {$table} (
			  id bigint(20) NOT NULL auto_increment,
			  post_id bigint(20) NOT NULL,
			  action varchar(15) NOT NULL,
			  categories text NOT NULL,
			  due_date datetime NOT NULL,
			  date_added timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  PRIMARY KEY  (id)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			$table     = $wpdb->prefix . 'wlcc_contentmanager_set';
			$structure = "CREATE TABLE {$table} (
			  id bigint(20) NOT NULL auto_increment,
			  post_id bigint(20) NOT NULL,
			  due_date datetime NOT NULL,
			  status varchar(10) NOT NULL,
			  date_added timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  PRIMARY KEY  (id)
			) {$charset_collate};";
			dbDelta( $structure );
			$this->Tables->$table = $table;

			/* reload table names */
			$this->LoadTables( true );
		}

		/**
		 * Retreive contentlevel_id from a level/content combination
		 *
		 * @global object $wpdb
		 * @param <type> $level_id
		 * @param <type> $content_id
		 * @return <type>
		 */
		function Get_ContentLevelID( $level_id, $content_id ) {
			global $wpdb;
			$query = $wpdb->prepare( "SELECT `ID` FROM `{$this->Tables->contentlevels}` WHERE `content_id`=%d AND `level_id`=%s", $content_id, $level_id );
			return $wpdb->get_var( $query );
		}

		/**
		 * Retrieve Content Level Meta
		 *
		 * @global object $wpdb
		 * @param <type> $level_id
		 * @param <type> $content_id
		 * @param <type> $meta
		 * @return <type>
		 */
		function Get_ContentLevelMeta( $level_id, $content_id, $meta ) {
			global $wpdb;
			$contentlevel_id = $this->Get_ContentLevelID( $level_id, $content_id );
			if ( $contentlevel_id ) {
				$query = $wpdb->prepare( "SELECT `option_value` FROM `{$this->Tables->contentlevel_options}` WHERE `option_name`=%s AND `contentlevel_id`=%d", $meta, $contentlevel_id );
				return $wpdb->get_var( $query );
			}
			return false;
		}

		/**
		 * Add new Content Level Meta
		 *
		 * @global object $wpdb
		 * @param <type> $level_id
		 * @param <type> $content_id
		 * @param <type> $meta
		 * @param <type> $value
		 * @return <type>
		 */
		function Add_ContentLevelMeta( $level_id, $content_id, $meta, $value ) {
			global $wpdb;
			$contentlevel_id = $this->Get_ContentLevelID( $level_id, $content_id );
			if ( $this->Get_ContentLevelMeta( $level_id, $content_id, $meta ) ) {
				return false;
			}
			$data   = array(
				'contentlevel_id' => $contentlevel_id,
				'option_name'     => $meta,
				'option_value'    => $value,
			);
			$format = array(
				'%d',
				'%s',
				'%s',
			);
			return $wpdb->insert( $this->Tables->contentlevel_options, $data, $format );
		}

		/**
		 * Update Content Level Meta
		 *
		 * @global object $wpdb
		 * @param <type> $level_id
		 * @param <type> $content_id
		 * @param <type> $meta
		 * @param <type> $value
		 * @return <type>
		 */
		function Update_ContentLevelMeta( $level_id, $content_id, $meta, $value ) {
			global $wpdb;
			$contentlevel_id = $this->Get_ContentLevelID( $level_id, $content_id );
			$data            = array(
				'contentlevel_id' => $contentlevel_id,
				'option_name'     => $meta,
				'option_value'    => $value,
			);
			$where           = $data;
			unset( $where['option_value'] );
			$format       = array(
				'%d',
				'%s',
				'%s',
			);
			$where_format = array(
				'%d',
				'%s',
			);
			return $wpdb->update( $this->Tables->contentlevel_options, $data, $where, $format, $where_format );
		}

		/**
		 * Delete Content Level Meta
		 *
		 * @global object $wpdb
		 * @param <type> $level_id
		 * @param <type> $content_id
		 * @param <type> $meta
		 * @return <type>
		 */
		function Delete_ContentLevelMeta( $level_id, $content_id, $meta ) {
			global $wpdb;
			$contentlevel_id = $this->Get_ContentLevelID( $level_id, $content_id );
			$query           = $wpdb->prepare( "DELETE FROM `{$this->Tables->contentlevel_options}` WHERE `contentlevel_id`=%d AND `option_name`=%s", $contentlevel_id, $meta );
			return $wpdb->query( $query );
		}

		/**
		 * Retrieves all Content Level Meta and returns it as an associative array
		 *
		 * @global object $wpdb
		 * @param <type> $level_id
		 * @param <type> $content_id
		 * @return array
		 */
		function Get_AllContentLevelMeta( $level_id, $content_id ) {
			global $wpdb;
			$contentlevel_id = $this->Get_ContentLevelID( $level_id, $content_id );
			$query           = $wpdb->prepare( "SELECT `option_name`,`option_value` FROM `{$this->Tables->contentlevel_options}` WHERE `contentlevel_id`=%d", $contentlevel_id );
			$results         = $wpdb->get_results( $query, OBJECT_K );
			foreach ( $results as $k => $v ) {
				$results[ $k ] = $v->option_value;
			}
			return $results;
		}

		/**
		 * Deletes all Content Level Meta
		 *
		 * @global object $wpdb
		 * @param <type> $level_id
		 * @param <type> $content_id
		 * @return <type>
		 */
		function Delete_AllContentLevelMeta( $level_id, $content_id ) {
			global $wpdb;
			$contentlevel_id = $this->Get_ContentLevelID( $level_id, $content_id );
			$query           = $wpdb->prepare( "DELETE FROM `{$this->Tables->contentlevel_options}` WHERE `contentlevel_id`=%d", $contentlevel_id );
			return $wpdb->query( $query );
		}

		/**
		 * Retrieve WishList Member user meta information
		 *
		 * @global object $wpdb
		 * @param int    $user_id
		 * @param string $meta
		 * @return mixed
		 */
		function Get_UserMeta( $user_id, $meta ) {
			global $wpdb;
			if ( empty( $user_id ) ) {
				return false;
			}

			$cache_key   = $user_id . '_' . $meta;
			$cache_group = $this->Tables->user_options;
			$table       = $this->Tables->user_options;

			$value = wlm_cache_get( $cache_key, $cache_group );
			if ( $value === false ) {
				$row = $wpdb->get_row( $q = $wpdb->prepare( "SELECT `option_value` FROM `{$table}` WHERE `user_id`=%d AND `option_name`='%s'", $user_id, $meta ) );
				if ( ! is_object( $row ) ) {
					return false;
				}
				$value = $row->option_value;
				$value = wlm_maybe_unserialize( $value );
				wlm_cache_set( $cache_key, $value, $cache_group );
			}
			return $value;
		}

		/**
		 * Retrieve User ID based on  Level Meta Information
		 *
		 * @global object $wpdb
		 * @param <type> $meta
		 * @param <type> $value
		 * @return integer User ID
		 */
		function Get_UserID_From_UserMeta( $meta, $value ) {
			global $wpdb;
			$query = "SELECT `user_id` as ID FROM `{$this->Tables->user_options}`  WHERE  `option_name`='%s' AND `option_value`='%s' LIMIT 1";
			$query = $wpdb->prepare( $query, $meta, $value );
			$row   = $wpdb->get_row( $query );
			if ( ! is_object( $row ) ) {
				return false;
			}
			return $row->ID;
		}

		/**
		 * Add WishList Member user meta information
		 *
		 * @global object $wpdb
		 * @param int    $user_id
		 * @param string $meta
		 * @param mixed  $value
		 * @return mixed
		 */
		function Add_UserMeta( $user_id, $meta, $value ) {
			global $wpdb;
			// do not add meta if no userid or the value is null
			if ( empty( $user_id ) || is_null( $value ) ) {
				return false;
			}

			$x = $this->Get_UserMeta( $user_id, $meta );
			if ( $x !== false && ! is_null( $x ) ) {
				return false;
			}
			$cache_key   = $user_id . '_' . $meta;
			$cache_group = $this->Tables->user_options;
			$table       = $this->Tables->user_options;

			if ( is_bool( $value ) ) {
				$value = (int) $value;
			}

			$data = array(
				'user_id'      => $user_id,
				'option_name'  => $meta,
				'option_value' => wlm_maybe_serialize( $value ),
			);

			$format = array( '%d', '%s', '%s' );

			$x = $wpdb->insert( $table, $data, $format );
			if ( $x !== false ) {
				wlm_cache_delete( $cache_key, $cache_group );
				return true;
			}
			return false;
		}

		/**
		 * Update WishList Member user meta information
		 *
		 * @global object $wpdb
		 * @param int    $user_id
		 * @param string $meta
		 * @param mixed  $value
		 * @return mixed
		 */
		function Update_UserMeta( $user_id, $meta, $value ) {
			global $wpdb;
			// do not update meta if no userid or the value is null
			if ( empty( $user_id ) || is_null( $value ) ) {
				return false;
			}

			$cache_key   = $user_id . '_' . $meta;
			$cache_group = $this->Tables->user_options;
			$table       = $this->Tables->user_options;

			if ( is_bool( $value ) ) {
				$value = (int) $value;
			}

			$data = $this->Get_UserMeta( $user_id, $meta );

			if ( $data === $value ) {
				return true;
			}

			if ( $data === false ) {
				return $this->Add_UserMeta( $user_id, $meta, $value );
			}

			$data = array(
				'user_id'      => $user_id,
				'option_name'  => $meta,
				'option_value' => wlm_maybe_serialize( $value ),
			);

			$format = array( '%d', '%s', '%s' );

			$where = array(
				'user_id'     => $user_id,
				'option_name' => $meta,
			);

			$x = $wpdb->update( $table, $data, $where, $format );
			if ( $x !== false ) {
				wlm_cache_delete( $cache_key, $cache_group );
				return true;
			}
			return false;
		}

		/**
		 * Delete WishList Member user meta information
		 *
		 * @global object $wpdb
		 * @param int    $user_id
		 * @param string $meta
		 */
		function Delete_UserMeta( $user_id, $meta ) {
			global $wpdb;
			$cache_key   = $user_id . '_' . $meta;
			$cache_group = $this->Tables->user_options;
			$table       = $this->Tables->user_options;
			$wpdb->query( $wpdb->prepare( "DELETE FROM `{$table}` WHERE `user_id`=%d AND `option_name`='%s'", $user_id, $meta ) );
			wlm_cache_delete( $cache_key, $cache_group );
		}

		/**
		 * Get Primary Key of UserLevel
		 *
		 * @global object $wpdb
		 * @param int $user_id
		 * @param int $level_id
		 * @return int
		 */
		function Get_UserLevelIndex( $user_id, $level_id ) {
			global $wpdb;
			$cache_key   = $user_id . '_' . $level_id;
			$cache_group = $this->Tables->userlevels;

			$value = wlm_cache_get( $cache_key, $cache_group );
			if ( $value === false ) {
				if ( $level_id === '' ) {
					$query = "SELECT `ID` FROM `{$this->Tables->userlevels}` WHERE `user_id`=%d";
				} else {
					$query = "SELECT `ID` FROM `{$this->Tables->userlevels}` WHERE `user_id`=%d AND `level_id`=%s";
				}
				$query = $wpdb->prepare( $query, $user_id, $level_id );
				$row   = $wpdb->get_row( $query );
				if ( ! is_object( $row ) ) {
					return false;
				}
				$value = $row->ID;
				wlm_cache_set( $cache_key, $value, $cache_group );
			}
			return $value;
		}

		/**
		 * Get UserLevel meta information
		 *
		 * @global object $wpdb
		 * @param int    $user_id
		 * @param int    $level_id
		 * @param string $meta
		 * @return mixed
		 */
		function Get_UserLevelMeta( $user_id, $level_id, $meta ) {
			global $wpdb;
			if ( empty( $user_id ) ) {
				return false;
			}

			$cache_key   = $user_id . '_' . $level_id . '_' . $meta;
			$cache_group = $this->Tables->userlevel_options;

			$value = wlm_cache_get( $cache_key, $cache_group );
			if ( $value !== false ) {
				return $value;
			}

			$userlevel_id = $this->Get_UserLevelIndex( $user_id, $level_id );
			if ( $userlevel_id === false ) {
				$value = null;
			} else {
				$query = $wpdb->prepare( "SELECT `option_value` FROM `{$this->Tables->userlevel_options}` WHERE `userlevel_id`=%d AND `option_name`='%s'", $userlevel_id, $meta );
				$row   = $wpdb->get_row( $query );
				// rewritten so that we don't actually return "false" instead
				// cache the null value so that we don't have
				// to hit the db next time.
				if ( ! is_object( $row ) ) {
					$value = null;
				} else {
					$value = $row->option_value;
				}
			}
			$value = wlm_maybe_unserialize( $value );
			wlm_cache_set( $cache_key, $value, $cache_group );
			return $value;
		}

		/**
		 * Get ALL UserLevel meta information
		 *
		 * @global object $wpdb
		 * @param int $user_id
		 * @param int $level_id
		 * @return array
		 */
		function Get_All_UserLevelMetas( $user_id, $level_id ) {
			global $wpdb;
			if ( empty( $user_id ) ) {
				return false;
			}

			$cache_key   = $user_id . '_' . $level_id . '_all';
			$cache_group = $this->Tables->userlevel_options;

			$value = wlm_cache_get( $cache_key, $cache_group );
			if ( $value !== false ) {
				return $value;
			}

			$userlevel_id = $this->Get_UserLevelIndex( $user_id, $level_id );
			if ( $userlevel_id === false ) {
				$value = null;
			} else {
				$query  = $wpdb->prepare( "SELECT `option_name`,`option_value` FROM `{$this->Tables->userlevel_options}` WHERE `userlevel_id`=%d", $userlevel_id );
				$result = $wpdb->get_results( $query );
				// rewritten so that we don't actually return "false" instead
				// cache the null value so that we don't have
				// to hit the db next time.
				if ( ! is_object( $result ) && ! is_array( $result ) ) {
					$value = null;
				} else {
					$value = $result;
				}
			}
			$value = wlm_maybe_unserialize( $value );
			wlm_cache_set( $cache_key, $value, $cache_group );
			return $value;
		}

		/**
		 * Add UserLevel meta information
		 *
		 * @global object $wpdb
		 * @param int    $user_id
		 * @param int    $level_id
		 * @param string $meta
		 * @param mixed  $value
		 * @return boolean
		 */
		function Add_UserLevelMeta( $user_id, $level_id, $meta, $value ) {
			global $wpdb;
			if ( empty( $user_id ) ) {
				return false;
			}

			$x = $this->Get_UserLevelMeta( $user_id, $level_id, $meta );

			if ( $x !== false && ! is_null( $x ) ) {
				return false;
			}
			$cache_key   = $user_id . '_' . $level_id . '_' . $meta;
			$cache_group = $this->Tables->userlevel_options;

			$value        = wlm_maybe_serialize( $value );
			$userlevel_id = $this->Get_UserLevelIndex( $user_id, $level_id );

			if ( ! $userlevel_id ) {
				return false;
			}

			$data   = array(
				'userlevel_id' => $userlevel_id,
				'option_name'  => $meta,
				'option_value' => wlm_maybe_serialize( $value ),
			);
			$format = array( '%d', '%s', '%s' );
			$result = $wpdb->insert( $this->Tables->userlevel_options, $data, $format );
			if ( $result !== false ) {
				wlm_cache_delete( $cache_key, $cache_group );
				return true;
			}
			return false;
		}

		/**
		 * Update UserLevel meta information
		 *
		 * @global object $wpdb
		 * @param int    $user_id
		 * @param int    $level_id
		 * @param string $meta
		 * @param mixed  $value
		 * @return boolean
		 */
		function Update_UserLevelMeta( $user_id, $level_id, $meta, $value ) {
			global $wpdb;
			if ( empty( $user_id ) ) {
				return false;
			}

			$cache_key   = $user_id . '_' . $level_id . '_' . $meta;
			$cache_group = $this->Tables->userlevel_options;

			$value = wlm_maybe_serialize( $value );
			if ( ! $this->Add_UserLevelMeta( $user_id, $level_id, $meta, $value ) ) {
				$userlevel_id = $this->Get_UserLevelIndex( $user_id, $level_id );
				$data         = array(
					'userlevel_id' => $userlevel_id,
					'option_name'  => $meta,
					'option_value' => wlm_maybe_serialize( $value ),
				);
				$format       = array( '%d', '%s', '%s' );
				$where        = array(
					'userlevel_id' => $userlevel_id,
					'option_name'  => $meta,
				);
				$result       = $wpdb->update( $this->Tables->userlevel_options, $data, $where, $format );
				if ( $result !== false ) {
					wlm_cache_delete( $cache_key, $cache_group );
					return true;
				}
				return false;
			}
			return true;
		}

		/**
		 * Delete UserLevel meta information
		 *
		 * @global object $wpdb
		 * @param int    $user_id
		 * @param int    $level_id
		 * @param string $meta
		 * @return boolean
		 */
		function Delete_UserLevelMeta( $user_id, $level_id, $meta ) {
			global $wpdb;
			$cache_key   = $user_id . '_' . $level_id . '_' . $meta;
			$cache_group = $this->Tables->userlevel_options;

			$userlevel_id = $this->Get_UserLevelIndex( $user_id, $level_id );

			if ( is_array( $userlevel_id ) ) {
				foreach ( $userlevel_id as $k => $v ) {
					$userlevel_id[ $k ] = (int) $v;
				}
				$userlevel_id = "'" . implode( "','", $userlevel_id ) . "'";
			} else {
				$userlevel_id = (int) $userlevel_id;
			}
			$query  = $wpdb->prepare( "DELETE FROM `{$this->Tables->userlevel_options}` WHERE `userlevel_id` IN ({$userlevel_id}) AND `option_name`=%s", $meta );
			$result = $wpdb->query( $query );

			if ( $result !== false ) {
				wlm_cache_delete( $cache_key, $cache_group );
				return true;
			}
			return false;
		}

		function Delete_UserLevelMeta2( $user_id, $level_id, $meta ) {
			global $wpdb;

			$cache_key    = $user_id . '_' . $level_id . '_' . $meta;
			$cache_group  = $this->Tables->userlevel_options;
			$userlevel_id = $this->Get_UserLevelIndex( $user_id, $level_id );
			if ( is_array( $userlevel_id ) ) {
				foreach ( $userlevel_id as $k => $v ) {
					$userlevel_id[ $k ] = (int) $v;
				}
				$userlevel_id = "'" . implode( "','", $userlevel_id ) . "'";
			} else {
				$userlevel_id = (int) $userlevel_id;
			}
			$query  = $wpdb->prepare( "DELETE FROM `{$this->Tables->userlevel_options}` WHERE `userlevel_id` IN ({$user_id}) AND `option_name`=%s", $meta );
			$result = $wpdb->query( $query );

			if ( $result !== false ) {
				wlm_cache_delete( $cache_key, $cache_group );
				return true;
			}
			return false;
		}


		function Delete_User_Scheduled_LevelsMeta( $user_id ) {
			global $wpdb;
			// remove meta scheduled cancellations and removals
			$query  = $wpdb->prepare( "DELETE FROM `{$this->Tables->userlevel_options}` WHERE `userlevel_id` IN (SELECT `ID` FROM `{$this->Tables->userlevels}` WHERE `user_id` = %d) AND (`option_name` LIKE 'wlm_schedule_level%%' OR `option_name` LIKE 'scheduled_remove')", $user_id );
			$result = $wpdb->query( $query );

			// delete scheduled levels
			$query  = $wpdb->prepare( "DELETE `l`.* FROM `{$this->Tables->userlevels}` `l` LEFT JOIN `{$this->Tables->userlevel_options}` `lo` ON `l`.`ID`=`lo`.`userlevel_id` WHERE `l`.`user_id`=%d AND `lo`.`option_name` IN ('scheduled_add','scheduled_move')", $user_id );
			$result = $wpdb->query( $query );
			return true;
		}

		/**
		 * Retrieve Membership Levels based on User Level Meta Information
		 *
		 * @global object $wpdb
		 * @param int    $uid User ID
		 * @param string $meta
		 * @param string $value (optional)
		 * @return array Membership Levels
		 */
		function Get_Levels_From_UserLevelsMeta( $uid, $meta, $value = null ) {
			global $wpdb;
			if ( is_null( $value ) ) {
				$value = '%';
			}

			$query = "SELECT DISTINCT `ul`.`level_id` FROM `{$this->Tables->userlevel_options}` AS `ulm` LEFT JOIN `{$this->Tables->userlevels}` AS `ul` ON `ulm`.`userlevel_id`=`ul`.`ID` WHERE `ul`.`user_id`=%d AND `ulm`.`option_name`='%s' AND `ulm`.`option_value` LIKE '%s'";
			$query = $wpdb->prepare( $query, $uid, $meta, $value );
			return $wpdb->get_col( $query );
		}

		/**
		 * Get User which level is not yet added and get schedule content
		 *
		 * @global object $wpdb
		 * @param type $uid
		 * @param type $meta
		 * @return array
		 */
		function Get_Scheduled_UserLevelMeta( $uid, $meta ) {
			global $wpdb;

			$query   = "SELECT `option_name`, `option_value` FROM `{$this->Tables->userlevel_options}` WHERE `userlevel_id`=%d AND `option_name` LIKE '%s'";
			$query   = $wpdb->prepare( $query, $uid, $meta . '%' );
			$results = $wpdb->get_row( $query, ARRAY_N );
			return $results;
		}

		/**
		 * Retrieve User ID based on User Level Meta Information
		 *
		 * @global object $wpdb
		 * @param <type> $meta
		 * @param <type> $value
		 * @return integer User ID
		 */
		function Get_UserID_From_UserLevelsMeta( $meta, $value ) {
			global $wpdb;
			$query = "SELECT DISTINCT `ul`.`user_id` AS `ID` FROM `{$this->Tables->userlevel_options}` AS `ulm` JOIN `{$this->Tables->userlevels}` AS `ul` ON `ulm`.`userlevel_id`=`ul`.`ID` WHERE `ulm`.`option_name`='%s' AND `ulm`.`option_value`='%s' LIMIT 1";
			$query = $wpdb->prepare( $query, $meta, $value );
			$row   = $wpdb->get_row( $query );
			if ( ! is_object( $row ) ) {
				return false;
			}
			return $row->ID;
		}

		/**
		 * Cache User Level Information for the specified IDs
		 *
		 * @param array $ids User IDs
		 */
		function PreLoad_UserLevelsMeta( $ids, $force_cache = null ) {
			global $wpdb;
			if ( ! is_array( $ids ) || ! count( $ids ) ) {
				return;
			}

			if ( ! is_array( $force_cache ) ) {
				$force_cache = array(
					'cancelled'         => 0,
					'unconfirmed'       => 0,
					'forapproval'       => 0,
					'registration_date' => '',
					'transaction_id'    => '',
				);
			}

			foreach ( $ids as $k => $v ) {
				$ids[ $k ] = (int) $v;
			}
			$ids = "'" . implode( "','", $ids ) . "'";

			$query   = "SELECT `ulm`.`option_name` AS `option_name`, `ulm`.`option_value` AS `option_value`, `ul`.`level_id`, `ul`.`user_id` AS `user_id` FROM `{$this->Tables->userlevel_options}` AS `ulm` JOIN `{$this->Tables->userlevels}` AS `ul` ON `ul`.`ID`=`ulm`.`userlevel_id` WHERE `ul`.`user_id` IN ({$ids}) ORDER BY `ul`.`user_id` ASC, `ul`.`level_id` ASC";
			$results = $wpdb->get_results( $query );
			if ( ! count( $results ) ) {
				return;
			}

			$cache_group = $this->Tables->userlevel_options;
			$fcache      = $force_cache;
			$prev_key    = '';
			foreach ( $results as $result ) {
				$key = $result->user_id . '_' . $result->level_id;
				if ( $prev_key != $key && $prev_key != '' ) {
					foreach ( $fcache as $meta => $value ) {
						wlm_cache_set( $key . '_' . $meta, $value, $cache_group );
					}
					$fcache = $force_cache;
				}
				$cache_key = $result->user_id . '_' . $result->level_id . '_' . $result->option_name;
				$value     = wlm_maybe_unserialize( $result->option_value );
				wlm_cache_set( $cache_key, $value, $cache_group );
				unset( $fcache[ $result->option_name ] );
				$prev_key = $key;
			}
		}

		/**
		 * Migrate Schedule Level Meta Information for all membersip levels to Database
		 *
		 * @global object $wpdb
		 */
		function MigrateScheduledLevelsMeta() {
			global $wpdb;
			$query = $wpdb->prepare( "SELECT ID, option_name, option_value, userlevel_id FROM `{$this->Tables->userlevel_options}` WHERE `option_name` LIKE %s", 'wlm_schedule_level_%' );
			$metas = $wpdb->get_results( $query );

			foreach ( $metas as $record ) {
				list($current_schedule_type, $level) = explode( '-', $record->option_name );
				$schedule_type                       = $this->checkScheduleLevelType( $current_schedule_type );

				if ( $schedule_type ) {
					$values = array(
						'date'             => $record->option_value,
						'is_current_level' => $this->checkScheduledLevelIsActive( $schedule_type, $level ),
						'type'             => $schedule_type,
					);

					if ( in_array( $schedule_type, array( 'add', 'move' ) ) ) {
						$object         = new stdClass();
						$object->Levels = array( $level );
						$object->Metas  = array( $level => array( array( 'scheduled_' . $schedule_type, $values ) ) );
						$this->SetMembershipLevels( $record->userlevel_id, $object, null, null, null, null, null, null, true );
						$record->userlevel_id = $this->Get_UserLevelIndex( $record->userlevel_id, $level );
					} else {
						$data   = array(
							'userlevel_id' => $record->userlevel_id,
							'option_name'  => 'scheduled_' . $schedule_type,
							'option_value' => wlm_maybe_serialize( $values ),
						);
						$result = $wpdb->insert( $this->Tables->userlevel_options, $data );
					}
					$wpdb->delete( $this->Tables->userlevel_options, array( 'ID' => $record->ID ) );
				}
			}
		}

		// Helper function to check old record schedule type.
		private function checkScheduleLevelType( $type ) {
			if ( preg_match( '/_(add|remove|move)$/', $type, $matches ) ) {
				return $matches[1];
			} else {
				return false;
			}
		}

		private function checkScheduledLevelIsActive( $type, $level_id ) {

			global $wpdb;
			$query     = $wpdb->prepare( "SELECT user_id FROM `{$this->Tables->userlevels}` WHERE `level_id` = %s", $level_id );
			$user_info = $wpdb->get_row( $query );
			if ( $type === 'remove' ) {
				$cancelled   = $this->LevelCancelled( $level_id, $user_info->user_id );
				$pending     = $this->LevelForApproval( $level_id, $user_info->user_id );
				$unconfirmed = $this->LevelUnConfirmed( $level_id, $user_info->user_id );
				$expired     = $this->LevelExpired( $level_id, $user_info->user_id );
				if ( ! $cancelled && ! $pending && ! $unconfirmed && ! $expired ) {
					return true;
				}
			}
			return false;
		}
	}

}
