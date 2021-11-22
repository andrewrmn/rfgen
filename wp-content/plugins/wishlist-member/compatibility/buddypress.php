<?php
/**
 * BuddyPress compatibility code
 */

// Don't run redirect_canonical on BP profile pages.
add_filter(
	'redirect_canonical',
	function ( $redirect_url ) {
		if (
			( function_exists( 'bp_is_my_profile' ) && bp_is_my_profile() ) ||
			( function_exists( 'bp_is_current_component' ) && ! bp_is_current_component() )
		) {
			if( empty( $_GET['p'] ) ) {
				return false;
			}
		}
		return $redirect_url;
	}
);

// Start: Compatibiltiy fix for BuddyPress members and groups
add_filter(
	'wishlistmember_process_protection',
	function( $redirect ) {
		global $wp_query, $bp, $wp;

		// Let's check protection for the page set as forums page in BuddyBoss
		if ( function_exists( 'bbp_get_forums_url' ) ) {
			$current_page = home_url( add_query_arg( array(), $wp->request ) ) . '/';
			if ( bbp_get_forums_url() == $current_page ) {
				$forum_page_id = get_option( '_bbp_root_slug_custom_slug' );
				if ( $forum_page_id ) {

					$wpm_current_user = wp_get_current_user();

					if ( wishlistmember_instance()->Protect( $forum_page_id ) ) {
						if ( is_user_logged_in() ) {
							$res_access = wlmapi_member_can_access( $wpm_current_user->ID, 'page', $forum_page_id );
							if ( $res_access ) {
								return 'STOP';
							} else {
								return 'NOACCESS';
							}
						} else {
							return 'NOACCESS';
						}
					} else {
						return 'STOP';
					}
				}
			}
		}

		// BuddyBoss compatibility for component pages
		if ( function_exists( 'bp_is_user' ) && function_exists( 'bp_is_group' ) ) {

			// Don't run protection when current page is the currently logged in users profile page
			if ( function_exists( 'bp_is_my_profile' ) ) {
				if ( bp_is_my_profile() ) {
					return 'STOP';
				}
			}

			if ( bp_is_user() || bp_is_members_directory() ) {
				$bp_content_slug = $bp->pages->members->slug;
			} elseif ( bp_is_group() ) {
				$bp_content_slug = $bp->pages->groups->slug;
			}

			if ( empty( $bp_content_slug ) ) {
				if ( bp_is_current_component( 'groups' ) ) { // Check if current page is the groups component page
					$bp_content_slug = $bp->pages->groups->slug;
				} elseif ( bp_is_current_component( 'activity' ) ) {
					$bp_content_slug = $bp->pages->activity->slug;
				}
			}

			if ( ! empty( $bp_content_slug ) ) {
				$wp_query2 = new WP_Query( array( 'pagename' => $bp_content_slug ) );

				$wpm_current_user = wp_get_current_user();

				/**
				 * Get the page ID of the component page. (ie. Forums/Groups/Activity/Members's directory page)
				 */
				if ( empty( $wp_query2->post->ID ) ) {
					$page_id = $wp_query2->queried_object->ID;
				} else {
					$page_id = $wp_query2->post->ID;
				}

				/**
				 * If the $page_id is not protected then let the user view it.
				 * If it's protected then see if the logged in user can access it.
				 */
				if ( wishlistmember_instance()->Protect( $page_id ) ) {
					if ( is_user_logged_in() ) {

						$wpm_levels = (array) wishlistmember_instance()->GetOption( 'wpm_levels' );

						// Get levels that has access to the protected page.
						$levels_with_access = wishlistmember_instance()->GetContentLevels( 'page', $page_id );

						// Get levels of the logged in user and see if any of them has access to the page.
						$user_levels = new \WishListMember\User( $wpm_current_user->ID );

						foreach ( $user_levels->Levels as $user_level ) {

							// If one of user's level has access to all pages then just return.
							if ( isset( $wpm_levels[ $user_level->Level_ID ]['allpages'] ) ) {
								return 'STOP';
							}

							if ( in_array( $user_level->Level_ID, $levels_with_access ) ) {
								if ( $user_level->Active ) {
									return 'STOP';
								}
							}
						}
						return 'NOACCESS';
					} else {
						return 'NOACCESS';
					}
				} else {
					return 'STOP';
				}
			}
		}

		return $redirect; // return $redirect to allow for chaining
	},
	10,
	3
);

/**
 * If BuddyPress/BuddyBoss is active then don't run this function on topics/discussion pages
 * as the $content->query_vars['post__not_in'] hides the first reply when it contains at least one post ID.
 */
add_filter(
	'wishlistmember_only_show_content_for_level',
	function( $content ) {
		if ( function_exists( 'bp_current_component' ) ) {
			$bp_topic_slug = get_option( '_bbp_topic_slug' );
			if ( $bp_topic_slug ) {
				if ( strpos( $_SERVER['REQUEST_URI'], $bp_topic_slug ) !== false ) {
					return false;
				}
			}
		}
		return $content;
	}
);

/**
 * Inherit protection from parent topic/forum
 */
add_action(
	'save_post',
	function ( $post_id, $post, $update ) {
		// Only proceed if the custom post type is added (not updated)
		if ( ! $update && ! is_admin() ) {
			// Also, only process if it's on the frontend

			// For now only process BBPress Topics and replies created by members in the front end
			if ( isset( $_POST['action'] ) && ( $_POST['action'] == 'bbp-new-topic' || $_POST['action'] == 'bbp-new-reply' ) ) {

				$post_type = get_post_type( $post_id );

				if ( $post_type == 'reply' ) {
					$lvls_that_have_access = wishlistmember_instance()->GetContentLevels( 'topic', $_POST['bbp_topic_id'] );
				} else {
					$lvls_that_have_access = wishlistmember_instance()->GetContentLevels( 'forum', $_POST['bbp_forum_id'] );
				}

				// Add the levels the parent forum have
				wishlistmember_instance()->SetContentLevels( $post_type, $post_id, $lvls_that_have_access );

				if ( wishlistmember_instance()->Protect( $_POST['bbp_forum_id'] ) ) {
					// Protect the Topic or reply if parent forum is protected
					wishlistmember_instance()->Protect( $post_id, true );
				}
			}
		}
	},
	10,
	3
);




