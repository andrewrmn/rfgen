<?php

if ( isset( $WishListMemberInstance ) ) {
	// register_activation_hook( $WishListMemberInstance->pluginPath, array( &$WishListMemberInstance, 'Activate' ) );
	register_deactivation_hook( $WishListMemberInstance->pluginPath, array( &$WishListMemberInstance, 'Deactivate' ) );
	add_action( 'admin_head', array( &$WishListMemberInstance, 'AdminHead' ), 1 );

	/* my hooks */
	// init
	add_action( 'plugins_loaded', array( &$WishListMemberInstance, 'shortcodes_init' ) );

	add_action( 'init', array( &$WishListMemberInstance, 'Init' ) );

	add_action( 'admin_init', array( &$WishListMemberInstance, 'dismiss_wlm_update_notice' ) );

	add_action( 'admin_footer', array( &$WishListMemberInstance, 'IntegrationErrors' ) );
	add_action( 'admin_init', array( &$WishListMemberInstance, 'dismiss_hq_announcement' ) );

	// Loads Scripts
	add_action( 'wp_enqueue_scripts', array( &$WishListMemberInstance, 'frontend_scripts_and_styles' ), 9999999999 );

	/* register widget when loading the WP core. only for wp2.8+ */
	if ( version_compare( $wp_version, '2.8', '>=' ) ) {
		add_action( 'widgets_init', array( &$WishListMemberInstance, 'WishListWidget_register_widgets' ) );
	}

	// user handling
	add_action( 'delete_user', array( &$WishListMemberInstance, 'DeleteUser' ) );
	add_action( 'deleted_user', array( &$WishListMemberInstance, 'DeletedUser' ) );
	add_action( 'profile_update', array( &$WishListMemberInstance, 'ProfileUpdate' ) );

	// Content Handling
	add_action( 'admin_init', array( &$WishListMemberInstance, 'PreparePostPageOptions' ), 1 );
	add_action( 'wp_insert_post', array( &$WishListMemberInstance, 'SavePostPage' ), 10, 2 );

	// Miscellaneous
	add_action( 'wp_login', array( &$WishListMemberInstance, 'Login' ), 10, 2 );

	// Get ID of the logged in user before wp_logout removes it.
	add_action( 'clear_auth_cookie', array( &$WishListMemberInstance, 'GetUserIDBeforeLogout' ) );
	add_action( 'wp_logout', array( &$WishListMemberInstance, 'Logout' ) );

	add_action( 'wp_footer', array( &$WishListMemberInstance, 'Footer' ) );
	add_action( 'wp_head', array( &$WishListMemberInstance, 'WPHead' ) );

	// Password Hinting
	if ( $WishListMemberInstance->GetOption( 'password_hinting' ) ) {
		add_action( 'wp_ajax_nopriv_PasswordHintSubmit', array( &$WishListMemberInstance, 'PasswordHintSubmit' ) );
		add_filter( 'login_errors', array( &$WishListMemberInstance, 'PasswordHinting' ) );
		add_filter( 'lostpassword_form', array( &$WishListMemberInstance, 'PasswordHintingEmail' ) );
	}

	add_filter( 'cron_schedules', array( &$WishListMemberInstance, 'WLMCronSchedules' ) );

	// excluded pages
	add_filter( 'wp_list_pages_excludes', array( &$WishListMemberInstance, 'ExcludePages' ) );

	// 404
	add_filter( '404_template', array( &$WishListMemberInstance, 'The404' ) );
	// registration stuff
	add_filter( 'the_posts', array( &$WishListMemberInstance, 'RegistrationPage' ) );

	// template hooks
	// add_filter('archive_template', array(&$WishListMemberInstance, 'Process'));
	add_action( 'template_redirect', array( &$WishListMemberInstance, 'Process' ), 1 ); // we want our hook to run first
	// add_filter('taxonomy_template', array(&$WishListMemberInstance, 'Process'));
	// add_filter('page_template', array(&$WishListMemberInstance, 'Process'));
	// add_filter('single_template', array(&$WishListMemberInstance, 'Process'));
	// add_filter('category_template', array(&$WishListMemberInstance, 'Process'));
	// add_filter('tag_template', array(&$WishListMemberInstance, 'Process'));
	// auto insert more tag
	add_filter( 'the_posts', array( &$WishListMemberInstance, 'TheMore' ) );

	// feed link
	add_filter( 'feed_link', array( &$WishListMemberInstance, 'FeedLink' ) );

	// handling of private and register tags
	add_filter( 'the_content', array( &$WishListMemberInstance, 'TheContent' ) );
	add_filter( 'the_content_feed', array( &$WishListMemberInstance, 'TheContent' ) );

	// mail sender information
	add_filter( 'wp_mail_from', array( &$WishListMemberInstance, 'MailFrom' ), 9999999 );
	add_filter( 'wp_mail_from_name', array( &$WishListMemberInstance, 'MailFromName' ), 9999999 );
	// mail subject
	add_filter( 'wp_mail', array( &$WishListMemberInstance, 'MailSubject' ), 9999999 );

	// hooks for the "Only show content for each membership level" setting
	add_action( 'pre_get_posts', array( &$WishListMemberInstance, 'OnlyShowContentForLevel' ) );
	add_action( 'wp_list_pages_excludes', array( &$WishListMemberInstance, 'OnlyListPagesForLevel' ) );
	add_filter( 'list_terms_exclusions', array( &$WishListMemberInstance, 'OnlyListCatsForLevel' ) );
	add_filter( 'get_previous_post_where', array( &$WishListMemberInstance, 'OnlyShowPrevNextLinksForLevel' ) );
	add_filter( 'get_next_post_where', array( &$WishListMemberInstance, 'OnlyShowPrevNextLinksForLevel' ) );
	add_filter( 'wp_get_nav_menu_items', array( &$WishListMemberInstance, 'OnlyListNavMenuItemsForLevel' ) );
	add_filter( 'comment_feed_where', array( &$WishListMemberInstance, 'OnlyShowCommentsForLevel' ) );
	add_filter( 'the_comments', array( &$WishListMemberInstance, 'RecentComments' ), 10, 2 );

	add_action( 'edit_user_profile', array( &$WishListMemberInstance, 'ProfilePage' ) );
	add_action( 'show_user_profile', array( &$WishListMemberInstance, 'ProfilePage' ) );

	add_action( 'wishlistmember_backup_queue', array( &$WishListMemberInstance, 'ProcessBackupQueue' ) );
	add_action( 'wishlistmember_import_queue', array( &$WishListMemberInstance, 'ProcessImportMembers' ) );
	add_action( 'wishlistmember_email_queue', array( &$WishListMemberInstance, 'SendQueuedMail' ) );
	add_action( 'wishlistmember_unsubscribe_expired', array( &$WishListMemberInstance, 'UnsubscribeExpired' ) );
	add_action( 'wishlistmember_check_scheduled_cancelations', array( &$WishListMemberInstance, 'CancelScheduledCancelations' ) );
	add_action( 'wishlistmember_check_level_cancelations', array( &$WishListMemberInstance, 'CancelScheduledLevels' ) );
	add_action( 'wishlistmember_registration_notification', array( &$WishListMemberInstance, 'NotifyRegistration' ) );
	add_action( 'wishlistmember_email_confirmation_reminders', array( &$WishListMemberInstance, 'EmailConfirmationReminders' ) );
	add_action( 'wishlistmember_expring_members_notification', array( &$WishListMemberInstance, 'ExpiringMembersNotification' ) );
	add_action( 'wishlistmember_api_queue', array( &$WishListMemberInstance, 'ProcessApiQueue' ) );
	add_action( 'wishlistmember_syncmembership_count', array( &$WishListMemberInstance, 'SyncMembershipCount' ) );

	// hook for Scheduled User Levels
	add_action( 'wishlistmember_run_scheduled_user_levels', array( &$WishListMemberInstance, 'RunScheduledUserLevels' ) );

	// hook for User Level Actions
	add_action( 'wishlistmember_run_user_level_actions', array( &$WishListMemberInstance, 'process_scheduled_level_actions' ) );

	// prevent deletion of post if its pay per post
	add_action( 'before_delete_post', array( &$WishListMemberInstance, 'CheckPostToDelete' ) );
	add_action( 'wp_trash_post', array( &$WishListMemberInstance, 'CheckPostToDelete' ) );

	// RSS Enclosures
	if ( $WishListMemberInstance->GetOption( 'disable_rss_enclosures' ) ) {
		add_filter( 'rss_enclosure', array( &$WishListMemberInstance, 'RSSEnclosure' ) );
	}

	// Adding html markup on editor for tinymce lightbox
	// add_filter('the_editor', array(&$WishListMemberInstance, 'AddEditorLightBoxMarkup'), 1234567890);
	// this causes issue with page builders, lets move it to Footer of admin area
	add_action( 'admin_footer', array( $WishListMemberInstance, 'AddEditorLightBoxMarkup' ) );

	// Attachments
	add_action( 'add_attachment', array( &$WishListMemberInstance, 'Add_Attachment' ) );
	add_action( 'edit_attachment', array( &$WishListMemberInstance, 'Edit_Attachment' ) );
	add_action( 'clean_attachment_cache', array( &$WishListMemberInstance, 'Edit_Attachment' ) );
	add_action( 'edit_attachment', array( &$WishListMemberInstance, 'SavePostPage' ) );
	add_action( 'delete_attachment', array( &$WishListMemberInstance, 'Delete_Attachment' ) );

	//** REMOVING THIS BECAUSE OF LEVEL ACTION UPDATE ********************/
	// add_filter('wishlistmember_attachments_load', array(&$WishListMemberInstance, 'ReloadAttachments'));
	// Auto Add/Remove/Cancel from Levels Hooks
	// add_action( 'wishlistmember_add_user_levels', array( &$WishListMemberInstance, 'DoAutoAddRemove' ), 10, 3 ); // moved to SetMembershipLevels
	// add_action( 'wishlistmember_pre_remove_user_levels', array( &$WishListMemberInstance, 'Remove_DoAutoAddRemove' ), 10, 2 ); // moved to SetMembershipLevels
	// add_action( 'wishlistmember_cancel_user_levels', array( &$WishListMemberInstance, 'Cancel_DoAutoAddRemove' ), 10, 2 ); // moved to SetMembershipLevels

	add_action( 'wishlistmember_process_level_actions', array( &$WishListMemberInstance, 'record_user_level_actions' ), 1, 3 ); // moved to SetMembershipLevels
	// Auto Remove Child levels when parent is removed
	add_action( 'wishlistmember_remove_user_levels', array( &$WishListMemberInstance, 'DoRemoveChildLevels' ), 1, 3 ); // moved to SetMembershipLevels

	add_action( 'wishlistmember_approve_user_levels', array( &$WishListMemberInstance, 'DoUpdateChildStatus' ), 1, 2 );
	add_action( 'wishlistmember_unapprove_user_levels', array( &$WishListMemberInstance, 'DoUpdateChildStatus' ), 1, 2 );

	add_action( 'wishlistmember_confirm_user_levels', array( &$WishListMemberInstance, 'DoUpdateChildStatus' ), 1, 2 );
	add_action( 'wishlistmember_unconfirm_user_levels', array( &$WishListMemberInstance, 'DoUpdateChildStatus' ), 1, 2 );

	add_action( 'wishlistmember_cancel_user_levels', array( &$WishListMemberInstance, 'DoUpdateChildStatus' ), 1, 2 );
	add_action( 'wishlistmember_uncancel_user_levels', array( &$WishListMemberInstance, 'DoUpdateChildStatus' ), 1, 2 );

	add_filter( 'wishlistmember_user_expire_date', array( &$WishListMemberInstance, 'DoExpireChildStatus' ), 1, 3 );


	add_action( 'wishlistmember_approve_user_levels', array( &$WishListMemberInstance, 'Remove_Pending_To_Add_Autoresponder' ), 2, 3 );
	add_action( 'wishlistmember_confirm_user_levels', array( &$WishListMemberInstance, 'Remove_Pending_To_Add_Autoresponder' ), 2, 3 );
	add_action( 'wishlistmember_user_registered', array( &$WishListMemberInstance, 'UserRegisteredCleanup' ), 10, 3 );

	// Temp email handling
	// note that our priority for this filter is ridiculously low to ensure that it runs last
	add_filter( 'sanitize_email', array( &$WishListMemberInstance, 'TempEmailSanitize' ), 1234567890 );

	add_filter( 'site_transient_update_plugins', array( &$WishListMemberInstance, 'Plugin_Update_Notice' ) );
	add_filter( 'plugins_api', array( &$WishListMemberInstance, 'Plugin_Info_Hook' ), 10, 3 );

	add_filter( 'upgrader_pre_install', array( &$WishListMemberInstance, 'Pre_Upgrade' ), 10, 2 );
	add_filter( 'upgrader_post_install', array( &$WishListMemberInstance, 'Post_Upgrade' ), 10, 2 );

	add_filter( 'rewrite_rules_array', array( &$WishListMemberInstance, 'RewriteRules' ) );

	add_action( 'wp_ajax_wlm_update_membership_level', array( &$WishListMemberInstance, 'WLMUpdate_MembershipLevel' ) );
	add_action( 'wp_ajax_wlm_user_search', array( &$WishListMemberInstance, 'WLMUserSearch_Ajax' ) );
	add_action( 'wp_ajax_wlm_payperpost_search', array( &$WishListMemberInstance, 'WLM_PayPerPost_Search' ) );
	add_action( 'wp_ajax_wlm_feeds', array( &$WishListMemberInstance, 'DashboardFeeds' ) );
	add_action( 'wp_ajax_wlm_delete_saved_search', array( &$WishListMemberInstance, 'WLMDeleteSavedSearch_Ajax' ) );
	add_action( 'wp_ajax_wlm_unschedule_single', array( &$WishListMemberInstance, 'wlm_unschedule_single' ) );

	add_action( 'wp_ajax_wlm_update_protection', array( &$WishListMemberInstance, 'update_protection_ajax' ) );
	add_action( 'wp_ajax_wlm_get_ppp_users', array( &$WishListMemberInstance, 'get_ppp_users_ajax' ) );
	add_action( 'wp_ajax_wlm_contenttab_bulk_action', array( &$WishListMemberInstance, 'contenttab_bulk_action_ajax' ) );

	add_action( 'wp_ajax_wlm_dismiss_nag', array( &$WishListMemberInstance, 'dismiss_wlm_nag' ) );

	add_action( 'admin_init', array( &$WishListMemberInstance, 'Upgrade_Check' ) );

	add_action( 'wishlistmember_after_registration', array( &$WishListMemberInstance, 'Add_Additional_Levels' ) );

	add_action( 'wishlistmember_migrate_file_protection', array( &$WishListMemberInstance, 'Run_FileProtect_Migration' ) );

	add_filter( 'user_request_action_email_content', array( &$WishListMemberInstance, 'privacy_user_request_email' ), 10, 2 );
	add_filter( 'user_request_action_email_subject', array( &$WishListMemberInstance, 'privacy_user_request_email_subject' ), 10, 3 );
	add_filter( 'user_confirmed_action_email_content', array( &$WishListMemberInstance, 'privacy_user_delete_email' ), 10, 2 );
	add_filter( 'wp_privacy_personal_data_email_content', array( &$WishListMemberInstance, 'privacy_personal_data_email' ), 10, 2 );
	add_filter( 'wp_privacy_personal_data_exporters', array( &$WishListMemberInstance, 'register_privacy_personal_data_exporter' ) );
	add_filter( 'wp_privacy_personal_data_erasers', array( &$WishListMemberInstance, 'register_privacy_personal_data_eraser' ) );

	// setup shopping carts
	include( $WishListMemberInstance->pluginDir . '/lib/integration.shoppingcarts.php' );
	$ActiveShoppingCarts = (array) $WishListMemberInstance->GetOption( 'ActiveShoppingCarts' );
	foreach ( $wishlist_member_shopping_carts as $wlm_integration_file => $wlm_integration_data ) {
		if ( ! empty( $wlm_integration_data['php_minimum'] ) ) {
			if ( version_compare( phpversion(), $wlm_integration_data['php_minimum'] ) < 0 ) {
				if ( ! empty( $wlm_integration_data['php_minimum_msg'] ) ) {
					$WishListMemberInstance->integration_errors[ $wlm_integration_file ] = $wlm_integration_data['php_minimum_msg'];
				}
				if ( ! empty( $wlm_integration_data['active_indicators'] ) ) {
					$WishListMemberInstance->active_integration_indicators[ $wlm_integration_file ] = $wlm_integration_data['active_indicators'];
				}
				continue;
			}
		}
		if ( in_array( $wlm_integration_file, $ActiveShoppingCarts ) ) {
			if( empty( $wlm_integration_data['handler'] ) ) {
				$WishListMemberInstance->LoadInitFile( $wlm_integration_file );
				$WishListMemberInstance->RegisterSCIntegration( $wlm_integration_data['optionname'], $wlm_integration_file, $wlm_integration_data['classname'], $wlm_integration_data['methodname'] );
			} else {
				$handler = sprintf( '%s/integrations/payments/%s/handler.php', $WishListMemberInstance->pluginDir3, $wlm_integration_data['name'] );
				if( file_exists( $handler ) ) {
					require_once $handler;
				}
			}
		}
	}

	// setup autoresponders
	$ar_used = $WishListMemberInstance->GetOption( 'Autoresponders' );
	$ar_used = isset( $ar_used['ARProvider'] ) && $ar_used['ARProvider'] ? $ar_used['ARProvider'] : false;
	$ars_used = array_merge( array( $ar_used ), (array) $WishListMemberInstance->GetOption( 'active_email_integrations' ) );
	foreach ( $ars_used as $ar_used ) {
		if ( ! $ar_used ) {
			continue;
		}
		include_once( $WishListMemberInstance->pluginDir . '/lib/integration.autoresponders.php' );
		foreach ( $wishlist_member_autoresponders as $wlm_integration_file => $wlm_integration_data ) {
			// only load the currently used autoresponder init file
			if ( $wlm_integration_data['optionname'] == $ar_used ) {
				if( !empty( $wlm_integration_data['handler'] ) ) {
					$wlm_integration_file = sprintf( '%s/integrations/emails/%s/handler.php', $WishListMemberInstance->pluginDir3, $wlm_integration_file );
				} else {
					$wlm_integration_file = sprintf( '%s/legacy/lib/%s', $WishListMemberInstance->pluginDir3, $wlm_integration_file );
				}
				$WishListMemberInstance->LoadInitFile( $wlm_integration_file );
				$WishListMemberInstance->RegisterARIntegration( wlm_arrval( $wlm_integration_data, 'optionname' ), $wlm_integration_file, wlm_arrval( $wlm_integration_data, 'classname' ), wlm_arrval( $wlm_integration_data, 'methodname' ) );
			}
		}
	}

	// setup other integrations for wlm3
	$wishlist_member_other_integrations = (array) $WishListMemberInstance->GetOption( 'active_other_integrations' );
	foreach ( $wishlist_member_other_integrations as $integration ) {
		$wlm_integration_files = array(
			sprintf( '%s/lib/integration.other.%s.php', $WishListMemberInstance->pluginDir, $integration ),
			sprintf( '%s/lib/integration.webinar.%s.php', $WishListMemberInstance->pluginDir, $integration ),
			sprintf( '%s/integrations/others/%s/handler.php', $WishListMemberInstance->pluginDir3, $integration ),
		);
		foreach ( $wlm_integration_files as $wlm_integration_file ) {
			if ( file_exists( $wlm_integration_file ) ) {
				include_once $wlm_integration_file;
			}
		}
	}
}