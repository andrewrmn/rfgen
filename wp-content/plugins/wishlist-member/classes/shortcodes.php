<?php
/**
 * WishList Member ShortCodes
 *
 * @author Mike Lopez <mjglopez@gmail.com>
 * @package wishlistmember
 */

namespace WishListMember;

defined( 'ABSPATH' ) || die();

class Shortcodes {

	var $shortcodes          = array(
		array( 'wlm_firstname', 'wlmfirstname', 'firstname' ),
		'First Name',
		'userinfo',
		array( 'wlm_lastname', 'wlmlastname', 'lastname' ),
		'Last Name',
		'userinfo',
		array( 'wlm_email', 'wlmemail', 'email' ),
		'Email Address',
		'userinfo',
		array( 'wlm_memberlevel', 'wlmmemberlevel', 'memberlevel' ),
		'Membership Levels',
		'userinfo',
		array( 'wlm_username', 'wlmusername', 'username' ),
		'Username',
		'userinfo',
		array( 'wlm_profileurl', 'wlmprofileurl', 'profileurl' ),
		'Profile URL',
		'userinfo',
		array( 'wlm_password', 'wlmpassword', 'password' ),
		'Password',
		'userinfo',
		array( 'wlm_autogen_password' ),
		'Auto Generated Password',
		'userinfo',
		array( 'wlm_website', 'wlmwebsite', 'website' ),
		'URL',
		'userinfo',
		array( 'wlm_aim', 'wlmaim', 'aim' ),
		'AIM ID',
		'userinfo',
		array( 'wlm_yim', 'wlmyim', 'yim' ),
		'Yahoo ID',
		'userinfo',
		array( 'wlm_jabber', 'wlmjabber', 'jabber' ),
		'Jabber ID',
		'userinfo',
		array( 'wlm_biography', 'wlmbiography', 'biography' ),
		'Biography',
		'userinfo',
		array( 'wlm_company', 'wlmcompany', 'company' ),
		'Company',
		'userinfo',
		array( 'wlm_address', 'wlmaddress', 'address' ),
		'Address',
		'userinfo',
		array( 'wlm_address1', 'wlmaddress1', 'address1' ),
		'Address 1',
		'userinfo',
		array( 'wlm_address2', 'wlmaddress2', 'address2' ),
		'Address 2',
		'userinfo',
		array( 'wlm_city', 'wlmcity', 'city' ),
		'City',
		'userinfo',
		array( 'wlm_state', 'wlmstate', 'state' ),
		'State',
		'userinfo',
		array( 'wlm_zip', 'wlmzip', 'zip' ),
		'Zip',
		'userinfo',
		array( 'wlm_country', 'wlmcountry', 'country' ),
		'Country',
		'userinfo',
		array( 'wlm_loginurl', 'wlm_loginurl', 'loginurl' ),
		'Login URL',
		'userinfo',
		array( 'wlm_logouturl', 'wlm_logouturl', 'logouturl' ),
		'Log out URL',
		'userinfo',
		array( 'wlm_rss', 'wlmrss' ),
		'RSS Feed URL',
		'rss',
		array( 'wlm_expiration', 'wlm_expiry', 'wlmexpiry' ),
		'Level Expiry Date',
		'levelinfo',
		array( 'wlm_joindate', 'wlmjoindate' ),
		'Level Join Date',
		'levelinfo',
		array( 'wlm_payperpost' ),
		'Registered Pay Per Post',
		'registered_payperpost',
	);
	var $manifest            = array(
		'Mergecodes'   => array(
			'Member'          => array(
				'wlm_firstname' => array(
					'label' => 'First Name',
				),
				'wlm_lastname'  => array(
					'label' => 'Last Name',
				),
				'wlm_email'     => array(
					'label' => 'Email',
				),
				'wlm_username'  => array(
					'label' => 'Username',
				),
			),
			'Access'          => array(
				'wlm_memberlevel'    => array(
					'label' => 'Membership Levels',
				),
				'wlm_userpayperpost' => array(
					'label'      => 'Pay Per Posts',
					'attributes' => array(
						'sort' => array(
							'label'   => 'Sort Order',
							'type'    => 'radio',
							'options' => array(
								'ascending'  => array(
									'label' => 'Ascending',
								),
								'descending' => array(
									'label' => 'Descending',
								),
							),
							'default' => 'ascending',
						),
					),
				),
				'wlm_rss'            => array(
					'label' => 'RSS Feed',
				),
				'wlm_contentlevels'  => array(
					'label'      => 'Content Levels',
					'attributes' => array(
						'type'           => array(
							'columns' => 3,
							'label'   => 'List Type',
							'type'    => 'select',
							'options' => array(
								'comma' => array(
									'label' => 'Comma',
								),
								'ol'    => array(
									'label' => 'Numbered List',
								),
								'ul'    => array(
									'label' => 'Bullet List',
								),
							),
							'default' => 'comma',
						),
						'link_target'    => array(
							'columns'     => 3,
							'label'       => 'Link Target',
							'type'        => 'text',
							'placeholder' => '_blank',
						),
						'class'          => array(
							'columns'     => 6,
							'label'       => 'CSS Class',
							'type'        => 'text',
							'placeholder' => 'wlm_contentlevels',
						),
						'salespage_only' => array(
							'columns' => 6,
							'type'    => 'checkbox',
							'options' => array(
								'1' => array(
									'label'     => 'Only display Levels with a Sales Page URL configured',
									'unchecked' => 0,
								),
							),
							'default' => 1,
						),
						'show_link'      => array(
							'columns'    => 6,
							'type'       => 'checkbox',
							'dependency' => '[name="salespage_only"]:checked',
							'options'    => array(
								'1' => array(
									'label'     => 'Link to Sales Page URL',
									'unchecked' => 0,
								),
							),
							'default'    => 1,
						),
					),
				),
			),
			'Login'           => array(
				'wlm_loginform' => array(
					'label' => 'Login Form',
				),
				'wlm_loginurl'  => array(
					'label' => 'Login URL',
				),
				'wlm_logouturl' => array(
					'label' => 'Log out URL',
				),
			),
			'Profile'         => array(
				'wlm_profileform' => array(
					'label'      => 'Profile Form',
					'attributes' => array(
						'list_subscription' => array(
							'label'   => 'Mailing List Subscription',
							'type'    => 'checkbox',
							'options' => array(
								'show' => array(
									'label'     => 'Show option to subscribe/unsubscribe from mailing list',
								),
							),
							'default' => 'show',
						),
						'profile_photo' => array(
							'label'   => 'Profile Photo',
							'type'    => 'checkbox',
							'options' => array(
								'show' => array(
									'label'     => 'Show option to upload profile photo',
								),
							),
						),
					),
				),
				'wlm_profileurl'  => array(
					'label' => 'Profile URL',
				),
				'wlm_profilephoto'  => array(
					'label' => 'Profile Photo',
					'attributes' => array(
						'url_only' => array(
							'label' => 'Return Format',
							'type' => 'select',
							'options' => array(
								'' => array(
									'label' => 'HTML Image',
								),
								'1' => array(
									'label' => 'URL Only',
								),
							),
						),
						'cropping' => array(
							'dependency' => '[name="url_only"] option:selected[value=""]',
							'type' => 'select',
							'label' => 'Cropping',
							'options' => array(
								'' => array(
									'label' => 'No Cropping',
								),
								'circle' => array(
									'label' => 'Circle',
								),
								'square' => array(
									'label' => 'Square',
								),
							),
							'columns' => 3,
						),
						'size' => array(
							'dependency' => '[name="url_only"] option:selected[value=""]&&[name="cropping"] option:selected:not([value=""])',
							'type' => 'number',
							'label' => 'Size',
							'placeholder' => 200,
							'columns' => 3,
						),
						'height' => array(
							'dependency' => '[name="url_only"] option:selected[value=""]&&[name="cropping"] option:selected[value=""]',
							'type' => 'number',
							'label' => 'Height',
							'placeholder' => 200,
							'columns' => 3,
						),
						'width' => array(
							'dependency' => '[name="url_only"] option:selected[value=""]&&[name="cropping"] option:selected[value=""]',
							'type' => 'number',
							'label' => 'Width',
							'placeholder' => 200,
							'columns' => 3,
						),
						'class' => array(
							'dependency' => '[name="url_only"] option:selected[value=""]',
							'type' => 'text',
							'label' => 'CSS Classes',
							'columns' => 3,
						),
					),
				),
			),
			'Join Date'       => array(),
			'Expiration Date' => array(),
			'Address'         => array(
				'wlm_company'  => array(
					'label' => 'Company',
				),
				'wlm_address'  => array(
					'label' => 'Address',
				),
				'wlm_address1' => array(
					'label' => 'Address 1',
				),
				'wlm_address2' => array(
					'label' => 'Address 2',
				),
				'wlm_city'     => array(
					'label' => 'City',
				),
				'wlm_state'    => array(
					'label' => 'State',
				),
				'wlm_zip'      => array(
					'label' => 'Zip',
				),
				'wlm_country'  => array(
					'label' => 'Country',
				),
			),
			'Custom Fields'   => array(),
			'Other'           => array(
				'wlm_website'   => array(
					'label' => 'Website',
				),
				'wlm_aim'       => array(
					'label' => 'AOL Instant Messenger',
				),
				'wlm_yim'       => array(
					'label' => 'Yahoo Instant Messenger',
				),
				'wlm_jabber'    => array(
					'label' => 'Jabber',
				),
				'wlm_biography' => array(
					'label' => 'Biography',
				),
			),
		),
		'Shortcodes'   => array(
			// mergecodes array
			'wlm_ismember'             => array(
				'label'     => 'Is Member',
				'enclosing' => 'Enter content to show to members',
			),
			'wlm_nonmember'            => array(
				'label'     => 'Non-Member',
				'enclosing' => 'Enter content to show to non-members',
			),
			'wlm_private'              => array(
				'label'      => 'Private Tags',
				'enclosing'  => 'Enter content',
				'attributes' => array(
					'levels'  => array(
						'label'   => 'Membership Levels',
						'type'    => 'select-multiple',
						'columns' => 9,
						'options' => array(),
					),
					'reverse' => array(
						'type'    => 'checkbox',
						'label'   => '&nbsp;',
						'columns' => 3,
						'options' => array(
							'1' => array(
								'label' => 'Reverse Private Tag',
							),
						),
					),
				),
			),
			'wlm_register'             => array(
				'label'      => 'Registration Forms',
				'attributes' => array(
					'level' => array(
						'type'    => 'select',
						'options' => array(),
					),
				),
			),
			'wlm_member_action_button' => array(
				'label'      => 'Member Action Button',
				'attributes' => array(
					'action'          => array(
						'label'   => 'Action',
						'columns' => 3,
						'type'    => 'select',
						'options' => array(
							'add'      => array(
								'label' => 'Add to',
							),
							'move'     => array(
								'label' => 'Move to',
							),
							'remove'   => array(
								'label' => 'Remove from',
							),
							'cancel'   => array(
								'label' => 'Cancel from',
							),
							'uncancel' => array(
								'label' => 'Uncancel from',
							),
						),
					),
					'level'           => array(
						'label'       => 'Access',
						'columns'     => 9,
						'type'        => 'select',
						'placeholder' => 'Choose a Level',
						'options'     => array(),
					),
					'redirect-choice' => array(
						'dependency' => '[name="level"] option:selected[value!=""]',
						'label'      => 'Redirect URL',
						'columns'    => 3,
						'type'       => 'select',
						'options'    => array(
							''       => array(
								'label'      => 'After Registration Page',
								'dependency' => '[name="action"] option:selected[value="add"],[name="action"] option:selected[value="move"]',
							),
							'return' => array(
								'label' => 'Return to Same Page',
							),
							'url'    => array(
								'label' => 'URL',
							),
						),
					),
					'redirect'        => array(
						'label'      => '&nbsp;',
						'type'       => 'url',
						'columns'    => 9,
						'dependency' => '[name="redirect-choice"] option:selected[value="url"] && [name="level"] option:selected[value!=""]',
					),
					'label'           => array(
						'dependency' => '[name="level"] option:selected[value!=""]',
						'label'      => 'Button Label',
						'columns'    => -6,
					),
					'class'           => array(
						'dependency' => '[name="level"] option:selected[value!=""]',
						'label'      => 'Additional CSS Classes',
						'columns'    => 6,
					),
				),
			),
		),
		'Integrations' => array(),
	);
	var $custom_user_data    = array();
	var $shortcode_functions = array();
	var $wpm_levels          = array();

	function __construct() {
		global $WishListMemberInstance, $wpdb;
		if ( isset( $WishListMemberInstance ) ) {

			$this->wpm_levels = $WishListMemberInstance->GetOption( 'wpm_levels' );
			$wpm_levels       = &$this->wpm_levels;
			$wpm_levels       = $wpm_levels ? $wpm_levels : array(); // make sure the $wpm_levels is an array

			if ( \WishListMember\Level::any_can_autocreate_account_for_integration() ) {
				$this->manifest['Mergecodes']['Member']['wlm_autogen_password'] = array( 'label' => 'Auto-generated Password' );
			}

			// join and expiration date
			$level_options = array();
			if ( $wpm_levels ) {
				foreach ( (array) $wpm_levels as $level ) {
					$level_options[ $level['name'] ] = array( 'label' => $level['name'] );
					if ( strpos( $level['name'], '/' ) === false ) {
						$this->manifest['Mergecodes']['Join Date'][ 'wlm_joindate ' . $level['name'] ]         = array( 'label' => $level['name'] );
						$this->manifest['Mergecodes']['Expiration Date'][ 'wlm_expiration ' . $level['name'] ] = array( 'label' => $level['name'] );
					}
				}
			} else {
				unset( $this->manifest['Mergecodes']['Join Date'] );
				unset( $this->manifest['Mergecodes']['Expiration Date'] );
			}

			// custom fields
			$custom_fields = wishlistmember_instance()->GetCustomFieldsMergeCodes();
			if ( count( $custom_fields ) ) {
				foreach ( $custom_fields as $custom_field ) {
					$this->manifest['Mergecodes']['Custom Fields'][ substr( $custom_field, 1, -1 ) ] = array( 'label' => $custom_field );
				}
			} else {
				unset( $this->manifest['Mergecodes']['Custom Fields'] );
			}

			// private tags and registration form options
			$this->manifest['Shortcodes']['wlm_private']['attributes']['levels']['options'] = $level_options;
			$this->manifest['Shortcodes']['wlm_register']['attributes']['level']['options'] = $level_options;

			// member action button
			$options['Membership Levels'] = array(
				'options' => $level_options,
				'label'   => 'Membership Levels',
			);
			// member action button CPTs
			foreach ( wishlistmember_instance()->GetPayPerPosts( array( 'post_title' ) ) as $wlm_post_type => $wlm_posts ) {
				if ( $wlm_posts && $ptype = get_post_type_object( $wlm_post_type ) ) {
					$options[ $ptype->label ] = array(
						'options'    => array(),
						'label'      => $ptype->label,
						'dependency' => '[name="action"] option:selected[value="add"],[name="action"] option:selected[value="remove"]',
					);
					foreach ( $wlm_posts as $wlm_post ) {
						$options[ $ptype->label ]['options'][ 'payperpost-' . $wlm_post->ID ] = array( 'label' => $wlm_post->post_title );
					}
				}
			}
			$this->manifest['Shortcodes']['wlm_member_action_button']['attributes']['level']['options']        = $options;
			$this->manifest['Shortcodes']['wlm_member_action_button']['attributes']['redirect']['placeholder'] = site_url();
			
			$this->manifest['Shortcodes'] = apply_filters( 'wishlistmember_shortcodes', $this->manifest['Shortcodes'] );
			$this->manifest['Mergecodes'] = apply_filters( 'wishlistmember_mergecodes', $this->manifest['Mergecodes'] );
			$this->manifest['Integrations'] = apply_filters( 'wishlistmember_integration_shortcodes', $this->manifest['Integrations'] );

			// Initiate custom registration fields array
			// $this->custom_user_data = $wpdb->get_col("SELECT DISTINCT SUBSTRING(`option_name` FROM 8) FROM `{$WishListMemberInstance->Tables->user_options}` WHERE `option_name` LIKE 'custom\_%' AND `option_name` <> 'custom_'");
			$this->custom_user_data = $wpdb->get_col( "SELECT SUBSTRING(`option_name` FROM 8) FROM `{$WishListMemberInstance->Tables->user_options}` WHERE `option_name` LIKE 'custom\_%' AND `option_name` <> 'custom\_' GROUP BY `option_name`" );

			// User Information
			$shortcodes = $this->shortcodes;
			for ( $i = 0; $i < count( $shortcodes ); $i = $i + 3 ) {
				foreach ( (array) $shortcodes[ $i ] as $shortcode ) {
					$this->_add_shortcode( $shortcode, array( &$this, $shortcodes[ $i + 2 ] ) );
				}
			}

			// Get and Post data passed on Registration
			$shortcodes = array(
				'wlmuser',
				'wlm_user',
			);
			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'get_and_post' ) );
			}

			// Powered By WishList Member
			$shortcodes = array(
				'wlm_counter',
				'wlmcounter',
			);
			foreach ( $shortcodes as $shortcode ) {
				add_shortcode( $shortcode, array( &$this, 'counter' ) );
			}

			$shortcodes = array( 'wlm_min_passlength', 'wlmminpasslength' );

			foreach ( $shortcodes as $shortcode ) {
				add_shortcode( $shortcode, array( $this, 'min_password_length' ) );
			}

			// Login Form
			$shortcodes = array(
				'wlm_loginform',
				'wlmloginform',
				'loginform',
			);
			foreach ( $shortcodes as $shortcode ) {
				add_shortcode( $shortcode, array( &$this, 'login' ) );
			}

			// Profile Form
			add_shortcode( 'wlm_profileform', array( &$this, 'profile_form' ) );

			// Profile Photo
			add_shortcode( 'wlm_profilephoto', array( &$this, 'profile_photo' ) );

			// Membership level with access to post/page
			$shortcodes = array(
				'wlm_contentlevels',
				'wlmcontentlevels',
			);
			foreach ( $shortcodes as $shortcode ) {
				add_shortcode( $shortcode, array( &$this, 'content_levels_list' ) );
			}

			// Custom Registration Fields
			$shortcodes = array(
				'wlm_custom',
				'wlmcustom',
			);
			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'custom_registration_fields' ) );
			}

			// Is Member and Non Member
			$shortcodes = array(
				'wlm_ismember',
				'wlmismember',
			);
			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'ismember' ) );
			}

			$shortcodes = array(
				'wlm_nonmember',
				'wlmnonmember',
			);
			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'nonmember' ) );
			}

			$invalid_shortcode_chars = '@[<>&/\[\]\x00-\x20]@';

			$shortcodes = array(
				'wlm_register',
				'wlmregister',
				'register',
			);

			// Disable old register shotrtcodes if configured
			// This will reduce the number of shortcodes WLM is registering,
			// Specially helpful with sites with large number of levels
			if ( ! $WishListMemberInstance->GetOption( 'disable_legacy_reg_shortcodes' ) ) {
				// Registration Form Tags
				foreach ( $wpm_levels as $level ) {
					if ( ! preg_match( $invalid_shortcode_chars, $level['name'] ) ) {
						$shortcodes[] = 'wlm_register_' . urlencode( $level['name'] );
						// $shortcodes[] = 'wlmregister_' . $level['name'];
					}
				}
			}

			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'regform' ) );
			}

			// has access
			$shortcodes = array( 'has_access', 'wlm_has_access' );

			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'hasaccess' ) );
			}

			// has no access
			$shortcodes = array( 'has_no_access', 'wlm_has_no_access' );

			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'hasnoaccess' ) );
			}

			// Private Tags
			$shortcodes = array(
				'wlm_private',
				'wlmprivate',
				'private',
			);
			// Disable old private tags if configured
			// This will reduce the number of shortcodes WLM is registering,
			// Specially helpful with sites with large number of levels
			if ( ! $WishListMemberInstance->GetOption( 'disable_legacy_private_tags' ) ) {
				foreach ( $wpm_levels as $level ) {
					if ( ! preg_match( $invalid_shortcode_chars, $level['name'] ) ) {
						$shortcodes[] = 'wlm_private_' . $level['name'];
						// $shortcodes[] = 'wlmprivate_' . $level['name'];
					}
				}
			}
			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'private_tags' ) );
			}

			// Reverse Private Tag
			$shortcodes = array(
				'!wlm_private',
				'!wlmprivate',
				'!private',
			);
			// Disable old private tags if configured
			// This will reduce the number of shortcodes WLM is registering,
			// Specially helpful with sites with large number of levels
			if ( ! $WishListMemberInstance->GetOption( 'disable_legacy_private_tags' ) ) {
				foreach ( $wpm_levels as $level ) {
					if ( ! preg_match( $invalid_shortcode_chars, $level['name'] ) ) {
						$shortcodes[] = '!private_' . $level['name'];
						$shortcodes[] = '!wlm_private_' . $level['name'];
						// $shortcodes[] = '!wlmprivate_' . $level['name'];
					}
				}
			}
			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'reverse_private_tags' ) );
			}

			// User Payperpost
			$shortcodes = array(
				'wlm_userpayperpost',
				'wlmuserpayperpost',
			);
			foreach ( $shortcodes as $shortcode ) {
				$this->_add_shortcode( $shortcode, array( &$this, 'user_payperpost' ) );
			}

			// member action button
			$this->_add_shortcode( 'wlm_member_action_button', array( $this, 'member_action_button' ) );

			// Process our shortcodes in the sidebar too!
			if ( ! is_admin() ) {
				add_filter( 'widget_text', 'do_shortcode', 11 );
			}

			// fix where shortcodes are not supported in input tag value attribute
			// https://make.wordpress.org/core/2015/07/23/changes-to-the-shortcode-api/
			add_filter( 'wp_kses_allowed_html', array( &$this, 'wlm_kses_allowed_tags' ), 10, 2 );
		}
	}
	
	function enqueue_shortcode_inserter_js() {
		wp_enqueue_script( 'wishlistmember-shortcode-insert-js', wishlistmember_instance()->pluginURL3 . '/assets/js/shortcode-inserter.js', array( 'jquery' ), wishlistmember_instance()->Version, true );
	}

	function wlm_kses_allowed_tags( $allowed_tags, $context ) {
		if ( is_admin() || ! in_the_loop() ) {
			return $allowed_tags;
		}
		if ( $context == 'post' && is_array( $allowed_tags ) ) {
			if ( ! isset( $allowed_tags['input'] ) ) {
				$allowed_tags['input'] = array( 'value' => true );
			} else {
				// other might have added some attributes for input
				// this will prevent from overwriting other attributes
				if ( ! isset( $allowed_tags['input']['value'] ) || ! $allowed_tags['input']['value'] ) {
					$allowed_tags['input']['value'] = true;
				}
			}
		}
		return $allowed_tags;
	}

	function ismember( $atts, $content, $code ) {
		global $WishListMemberInstance;
		global $wp_query;

		$is_userpost = false;

		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}

		if ( wlm_arrval( $current_user->caps, 'administrator' ) ) {
			return do_shortcode( $content );
		}

		if ( $WishListMemberInstance->GetOption( 'payperpost_ismember' ) ) {
			$is_userpost = in_array( $wp_query->post->ID, $WishListMemberInstance->GetMembershipContent( $wp_query->post->post_type, 'U-' . $current_user->ID ) );
		}

		$user_levels = $WishListMemberInstance->GetMembershipLevels( $current_user->ID, null, true, null, true );
		if ( count( $user_levels ) || $is_userpost ) {
			return do_shortcode( $content );
		} else {
			return '';
		}
	}

	function nonmember( $atts, $content, $code ) {
		global $WishListMemberInstance;

		global $wp_query;

		$is_userpost = false;

		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}

		if ( wlm_arrval( $current_user->caps, 'administrator' ) ) {
			return do_shortcode( $content );
		}

		if ( $WishListMemberInstance->GetOption( 'payperpost_ismember' ) ) {
			$is_userpost = in_array( $wp_query->post->ID, $WishListMemberInstance->GetMembershipContent( $wp_query->post->post_type, 'U-' . $current_user->ID ) );
		}

		$user_levels = $WishListMemberInstance->GetMembershipLevels( $current_user->ID, null, true, null, true );
		if ( count( $user_levels ) || $is_userpost ) {
			return '';
		} else {
			return do_shortcode( $content );
		}
	}

	function regform( $atts, $content, $code ) {
		global $WishListMemberInstance;

		if ( in_array( $code, array( 'wlm_register', 'wlmregister', 'register' ) ) ) {
			$level_name = implode( ' ', $atts );
		} else {
			if ( substr( $code, 0, 12 ) == 'wlm_register' ) {
				$level_name = substr( $code, 13 );
			} else {
				$level_name = substr( $code, 12 );
			}
		}

		foreach ( $this->wpm_levels as $level_id => $level ) {
			if ( trim( strtoupper( $level['name'] ) ) == trim( strtoupper( html_entity_decode( $level_name ) ) ) ) {
				return do_shortcode( $WishListMemberInstance->RegContent( $level_id, true ) );
			}
		}
		return '';
	}

	function private_tags( $atts, $content, $code ) {
		global $WishListMemberInstance;
		$atts = is_array( $atts ) ? array( implode( ' ', $atts ) ) : $atts; // lets glue attributes together for level names with spaces

		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}

		if ( wlm_arrval( $current_user->caps, 'administrator' ) ) {
			return do_shortcode( $content );
		}

		$user_levels = $WishListMemberInstance->GetMembershipLevels( $current_user->ID, null, true, null, true );

		$level_names = array();

		if ( $code == 'wlm_private' or $code == 'wlmprivate' ) {
			foreach ( $atts as $key => $value ) {
				$value = trim( $value, "'" );
				if ( is_int( $key ) ) {
					$level_names = array_merge( $level_names, explode( '|', $value ) );
					unset( $atts[ $key ] );
				}
			}
		} else {
			if ( substr( $code, 0, 11 ) == 'wlm_private' ) {
				$level_names[] = substr( $code, 12 );
			} else {
				$level_names[] = substr( $code, 11 );
			}
		}

		$level_names = array_map( 'trim', $level_names );
		$level_ids   = array();

		foreach ( $this->wpm_levels as $level_id => $level ) {
			$level_ids[ $level['name'] ] = $level_id;
		}

		$match = false;
		foreach ( $level_names as $level_name ) {
			$level_id = $level_ids[ $level_name ];
			if ( in_array( $level_id, $user_levels ) ) {
				$match = true;
				break;
			}
		}

		if ( $match ) {
			return do_shortcode( $content );
		} else {
			$protectmsg = $WishListMemberInstance->GetOption( 'private_tag_protect_msg' );
			$protectmsg = str_replace( '[level]', implode( ', ', $level_names ), $protectmsg );
			$protectmsg = do_shortcode( $protectmsg );
			return $protectmsg;
		}
	}

	function reverse_private_tags( $atts, $content, $code ) {
		global $WishListMemberInstance;
		$atts = is_array( $atts ) ? array( implode( ' ', $atts ) ) : $atts; // lets glue attributes together for level names with spaces

		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}

		if ( wlm_arrval( $current_user->caps, 'administrator' ) ) {
			return do_shortcode( $content );
		}

		$user_levels = $WishListMemberInstance->GetMembershipLevels( $current_user->ID, null, true, null, true );
		$level_names = array();

		if ( $code == '!private' or $code == '!wlm_private' ) {
			foreach ( $atts as $key => $value ) {
				$value = trim( $value, "'" );
				if ( is_int( $key ) ) {
					$level_names = array_merge( $level_names, explode( '|', $value ) );
					unset( $atts[ $key ] );
				}
			}
		} else {
			if ( substr( $code, 0, 8 ) == '!private' ) {
				$level_names[] = substr( $code, 9 );
			} else {
				$level_names[] = substr( $code, 13 );
			}
		}

		$level_names = array_map( 'trim', $level_names );

		// lets get the valid levels in the tag
		$tag_levels = array();
		foreach ( $this->wpm_levels as $level_id => $level ) {
			if ( in_array( $level['name'], $level_names ) ) {
				$tag_levels[] = $level_id;
			}
		}

		// now we have the users level and the levels in the tag
		// lets check if one of levels in the tag is in users level
		$user_match_level = array_intersect( $tag_levels, $user_levels );

		if ( count( $user_match_level ) > 0 ) { // if theres a level in the tag that users have
			// display the message
			$protectmsg = $WishListMemberInstance->GetOption( 'reverse_private_tag_protect_msg' );
			$protectmsg = str_replace( '[level]', implode( ', ', $level_names ), $protectmsg );
			return $protectmsg;

		} else { // if user does not have all levels in the tag, return the content
			return do_shortcode( $content );
		}
	}

	function userinfo( $atts, $content, $code ) {
		global $WishListMemberInstance, $wlm_cookies;

		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}

		$wpm_useraddress = $WishListMemberInstance->Get_UserMeta( $current_user->ID, 'wpm_useraddress' );
		static $password = null;
		switch ( $code ) {

			case 'firstname':
			case 'wlm_firstname':
			case 'wlmfirstname':
				return $current_user->first_name;
			break;
			case 'lastname':
			case 'wlm_lastname':
			case 'wlmlastname':
				return $current_user->last_name;
			break;
			case 'email':
				if ( ( current_user_can( 'administrator' ) && is_plugin_active( 'mailpoet/mailpoet.php' ) && wp_get_theme() == 'Thrive Theme Builder' ) ) {
					return '[email]';
					break;
				} else {
					return $current_user->user_email;
					break;
				}
			case 'wlm_email':
			case 'wlmemail':
				return $current_user->user_email;
			break;
			case 'memberlevel':
			case 'wlm_memberlevel':
			case 'wlmmemberlevel':
				$user_levels = $WishListMemberInstance->GetMembershipLevels( $current_user->ID, $names = true, $activeOnly = null, $no_cache = null, $no_userlevels = true );
				if ( $user_levels ) {
					return $user_levels;
				} else {
					return __( 'No Membership Level', 'wishlist-member' );
				}

				break;
			case 'username':
			case 'wlm_username':
			case 'wlmusername':
				return $current_user->user_login;
			break;
			case 'profileurl':
			case 'wlm_profileurl':
			case 'wlmprofileurl':
				return get_bloginfo( 'wpurl' ) . '/wp-admin/profile.php';
			break;
			case 'password':
			case 'wlm_password':
			case 'wlmpassword':
				/* password shortcode retired to prevent security issues */
				return '********';
			break;
			case 'wlm_autogen_password':
				return empty( $wlm_cookies->wlm_autogen_pass ) ? '********' : $wlm_cookies->wlm_autogen_pass;
			break;
			case 'website':
			case 'wlm_website':
			case 'wlmwebsite':
				return $current_user->user_url;
			break;
			case 'aim':
			case 'wlm_aim':
			case 'wlmaim':
				return $current_user->aim;
			break;
			case 'yim':
			case 'wlm_yim':
			case 'wlmyim':
				return $current_user->yim;
			break;
			case 'jabber':
			case 'wlm_jabber':
			case 'wlmjabber':
				return $current_user->jabber;
			break;
			case 'biography':
			case 'wlm_biography':
			case 'wlmbiography':
				return $current_user->description;
			break;
			case 'company':
			case 'wlm_company':
			case 'wlmcompany':
				return $wpm_useraddress['company'];
			break;
			case 'address':
			case 'wlm_address':
			case 'wlmaddress':
				$address = $wpm_useraddress['address1'];
				if ( ! empty( $wpm_useraddress['address2'] ) ) {
					$address .= '<br />' . $wpm_useraddress['address2'];
				}
				return $address;
			break;
			case 'address1':
			case 'wlm_address1':
			case 'wlmaddress1':
				return $wpm_useraddress['address1'];
			break;
			case 'address2':
			case 'wlm_address2':
			case 'wlmaddress2':
				return $wpm_useraddress['address2'];
			break;
			case 'city':
			case 'wlm_city':
			case 'wlmcity':
				return $wpm_useraddress['city'];
			break;
			case 'state':
			case 'wlm_state':
			case 'wlmstate':
				return $wpm_useraddress['state'];
			break;
			case 'zip':
			case 'wlm_zip':
			case 'wlmzip':
				return $wpm_useraddress['zip'];
			break;
			case 'country':
			case 'wlm_country':
			case 'wlmcountry':
				return $wpm_useraddress['country'];
			break;
			case 'loginurl':
			case 'wlm_loginurl':
			case 'wlmloginurl':
				return wp_login_url();
			break;
			case 'wlm_logouturl':
			case 'wlmlogouturl':
				if ( ! is_user_logged_in() ) {
					return;
				}

				return wp_logout_url();
			break;
		}
	}

	function get_and_post( $atts, $content, $code ) {
		global $WishListMemberInstance;
		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}

		switch ( $atts ) {
			case 'post':
				$userpost = (array) $WishListMemberInstance->WLMDecrypt( $current_user->wlm_reg_post );
				if ( $atts[1] ) {
					return $userpost[ $atts[1] ];
				} else {
					return nl2br( print_r( $userpost, true ) );
				}
				break;
			case 'get':
				$userpost = (array) $WishListMemberInstance->WLMDecrypt( $current_user->wlm_reg_get );
				if ( $atts[1] ) {
					return $userpost[ $atts[1] ];
				} else {
					return nl2br( print_r( $userpost, true ) );
				}
				break;
		}
	}

	function rss( $atts, $content, $code ) {
		return get_bloginfo( 'rss2_url' );
	}

	function levelinfo( $atts, $content, $code ) {
		global $WishListMemberInstance;
		static $wpm_levels = null, $wpm_level_names = null;

		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}

		if ( is_null( $wpm_levels ) ) {
			$wpm_levels = (array) $WishListMemberInstance->GetOption( 'wpm_levels' );
		}

		if ( is_null( $wpm_level_names ) ) {
			$wpm_level_names = array();
			foreach ( $wpm_levels as $id => $level ) {
				$wpm_level_names[ trim( $level['name'] ) ] = $id;
			}
		}
		switch ( $code ) {
			case 'wlm_expiry':
			case 'wlmexpiry':
			case 'wlm_expiration';
				$format = wlm_arrval( $atts, 'format' ) ?: get_option( 'date_format' );
				unset( $atts['format'] );

				$level_name = trim( implode( ' ', $atts ) );
				$level_id   = $wpm_level_names[ $level_name ];

				// Don't return text if user doesn't belong to the level
				$user_levels = $WishListMemberInstance->GetMembershipLevels( $current_user->ID, null, true, null, true );
				if ( count( $user_levels ) ) {
					if ( in_array( $level_id, $user_levels ) ) {
						$expiry_date = $WishListMemberInstance->LevelExpireDate( $level_id, $current_user->ID );
						if ( $expiry_date !== false ) {
								return date_i18n( $format, $expiry_date );
						}
					}
				}
				break;
			case 'wlm_joindate':
			case 'wlmjoindate':
				if ( $atts['format'] ) {
					$format = $atts['format'];
					unset( $atts['format'] );
				} else {
					$format = get_option( 'date_format' );
				}

				$level_name = trim( implode( ' ', $atts ) );
				$level_id   = $wpm_level_names[ $level_name ];
				$join_date  = $WishListMemberInstance->UserLevelTimestamp( $current_user->ID, $level_id );
				if ( $join_date !== false ) {
					return date_i18n( $format, $join_date );
				}
				break;
		}
		return '';
	}

	function counter( $atts, $content, $code ) {
		global $WishListMemberInstance;
		$x = $WishListMemberInstance->ReadURL( 'http://wishlistactivation.com/wlm-sites.txt' );
		if ( $x !== false && $x > 0 ) {
			$WishListMemberInstance->SaveOption( 'wlm_counter', $x );
		} else {
			$x = $WishListMemberInstance->GetOption( 'wlm_counter' );
		}
		return $x;
	}

	function login( $atts, $content, $code ) {
		global $WishListMemberInstance, $wp;
		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}

		if ( ! $current_user->ID ) {
			if ( trim( $code ) == 'wlm_profileform' ) {
				$redirect = home_url( add_query_arg( array(), $wp->request ) );
			} elseif ( $WishListMemberInstance->GetOption( 'enable_login_redirect_override' ) ) {
				$redirect = ! empty( $_GET['wlfrom'] ) ? esc_attr( stripslashes( $_GET['wlfrom'] ) ) : 'wishlistmember';
			} else {
				$redirect = '';
			}
			$loginurl  = esc_url( site_url( 'wp-login.php', 'login_post' ) );
			$loginurl2 = wp_lostpassword_url();

			$txtLost = __( 'Lost your Password?', 'wishlist-member' );

			$username_field = wlm_form_field(
				array(
					'label' => __( 'Username or Email Address', 'wishlist-member' ),
					'type'  => 'text',
					'name'  => 'log',
				)
			);
			$password_field = wlm_form_field(
				array(
					'label'  => __( 'Password', 'wishlist-member' ),
					'type'   => 'password',
					'name'   => 'pwd',
					'toggle' => true,
				)
			);
			$remember_field = wlm_form_field(
				array(
					'type'    => 'checkbox',
					'name'    => 'rememberme',
					'options' => array( 'forever' => __( 'Remember Me', 'wishlist-member' ) ),
				)
			);
			$submit_button  = wlm_form_field(
				array(
					'type'  => 'submit',
					'name'  => 'wp-submit',
					'value' => __( 'Login', 'wishlist-member' ),
				)
			);

			if ( $WishListMemberInstance->GetOption( 'show_onetime_login_option' ) ) {
				$otl = '<p class="wlmember_login_shortcode_otl_request"><a href="' . add_query_arg( 'action', 'wishlistmember-otl', wp_login_url() ) . '">' . wishlistmember_instance()->GetOption( 'onetime_login_link_label' ) . '</a></p>';
			}
			$form = <<<STRING
<form action="{$loginurl}" method="post" class="wlm_inpageloginform">
<input type="hidden" name="wlm_redirect_to" value="{$redirect}" />
<input type="hidden" name="redirect_to" value="{$redirect}" />
<div class="wlm3-form">
{$username_field}
{$password_field}
{$remember_field}
{$submit_button}
{$otl}
<p>
<a href="{$loginurl2}">{$txtLost}</a>					
</p>
</div>
</form>
STRING;
		} else {
			$form = $WishListMemberInstance->Widget( array(), true );
		}
		$form = "<div class='WishListMember_LoginMergeCode'>{$form}</div>";
		return $form;
	}
	
	function profile_photo( $atts ) {
		extract(
			shortcode_atts(
				array(
					'cropping' => '',
					'size' => '150',
					'width' => '',
					'height' => '',
					'class' => '',
					'url_only' => '',
				),
				$atts
			)
		);
		
		$profile = ( new \WishListMember\User( get_current_user_id() ) )->get_profile_photo() ?: wishlistmember_instance()->pluginURL3 . '/assets/images/grey.png';
		if( $url_only ) {
			return $profile;
		}
				
		$cropping = strtolower( trim( $cropping ) );
		if( in_array( $cropping, array( 'circle', 'square' ) ) ) {
			$style = sprintf( 'width:%1$dpx;height:%1$dpx;', $size );
			$style .= 'object-fit:cover;';
			if( $cropping == 'circle' ) {
				$style .= 'border-radius: 50%;';
			}
		} else {
			$width = $width ? sprintf( 'width:%dpx;', $width ) : '';
			$height = $height ? sprintf( 'height:%dpx;', $height ) : '';
			$style = $width . $height;
		}
		
		return sprintf( '<img src="%s" class="%s" style="%s">', $profile, $class, $style );
	}

	function profile_form( $atts, $content, $code ) {
		global $wp;
		global $WishListMemberInstance;
		static $processed;
		if ( ! empty( $processed ) ) {
			return ''; // process only once
		}
		$processed = true;

		if ( ! is_user_logged_in() ) {
			if ( ! empty( $atts['nologin'] ) ) {
				return '';
			} else {
				return $this->login( array(), $content, 'wlm_profileform' );
			}
		}

		$user    = wp_get_current_user();
		$options = array(
			$user->user_login => $user->user_login,
			$user->nickname   => $user->nickname,
		);
		if ( $user->first_name ) {
			$options[ $user->first_name ] = $user->first_name;
		}
		if ( $user->last_name ) {
			$options[ $user->last_name ] = $user->last_name;
		}
		if ( $user->first_name && $user->last_name ) {
			$fl             = implode( ' ', array( $user->first_name, $user->last_name ) );
			$lf             = implode( ' ', array( $user->last_name, $user->first_name ) );
			$options[ $fl ] = $fl;
			$options[ $lf ] = $lf;
		}

		$required = array();
		if ( isset( $_GET['wlm_required'] ) && is_array( $_GET['wlm_required'] ) && $_GET['wlm_required'] ) {
			foreach ( $_GET['wlm_required'] as $r ) {
				switch ( $r ) {
					case 'nickname':
						$required[] = sprintf( '<p>%s</p>', __( 'Nickname required', 'wishlist-member' ) );
						break;
					case 'user_email':
						$required[] = sprintf( '<p>%s</p>', __( 'Email required', 'wishlist-member' ) );
						break;
					case 'new_pass':
								$required[] = sprintf( '<p>%s</p>', __( 'Password not accepted', 'wishlist-member' ) );
						break;
				}
			}
		}

		$fields  = '';
		$fields .= wlm_form_field(
			array(
				'type'  => 'hidden',
				'name'  => '_wlm3_nonce',
				'value' => wp_create_nonce( 'update-profile_' . $user->ID ),
			)
		);
		$fields .= wlm_form_field(
			array(
				'type'  => 'hidden',
				'name'  => 'referrer',
				'value' => $_SERVER['REQUEST_URI'],
			)
		);
		$fields .= wlm_form_field(
			array(
				'type'  => 'hidden',
				'name'  => 'WishListMemberAction',
				'value' => 'UpdateUserProfile',
			)
		);

		$profile_photo = strtolower( wlm_arrval( $atts, 'profile_photo' ) ) == 'show';
		if ( $profile_photo ) {
			$fields .= wlm_form_field( 
				array(
					'type'  => 'profile_photo',
					'name'  => 'profile_photo',
					'value' => wlm_arrval( get_user_meta( $user->ID, 'profile_photo', true ), 'url' ),
				)
			);
		}

		$fields .= wlm_form_field(
			array(
				'type'     => 'text',
				'name'     => 'first_name',
				'onchange' => 'wlm3_update_displayname(this)',
				'value'    => $user->first_name,
				'label'    => __(
					'First Name',
					'wishlist-member'
				),
			)
		);
		$fields .= wlm_form_field(
			array(
				'type'     => 'text',
				'name'     => 'last_name',
				'onchange' => 'wlm3_update_displayname(this)',
				'value'    => $user->last_name,
				'label'    => __(
					'Last Name',
					'wishlist-member'
				),
			)
		);
		$fields .= wlm_form_field(
			array(
				'type'     => 'text',
				'name'     => 'nickname',
				'onchange' => 'wlm3_update_displayname(this)',
				'value'    => $user->nickname,
				'label'    => __(
					'Nickname',
					'wishlist-member'
				),
			)
		);
		$fields .= wlm_form_field(
			array(
				'type'    => 'select',
				'name'    => 'display_name',
				'value'   => $user->display_name,
				'options' => $options,
				'label'   => __(
					'Display Name',
					'wishlist-member'
				),
			)
		);
		$fields .= wlm_form_field(
			array(
				'type'  => 'email',
				'name'  => 'user_email',
				'value' => $user->user_email,
				'label' => __(
					'Email',
					'wishlist-member'
				),
			)
		);

		$show_mailinglist = true;
		if( isset( $atts['hide_mailinglist'] ) ) {
			// legacy attribute. kept for the sake of backwards compatibility
			$show_mailinglist = ! in_array( strtolower( wlm_arrval( $atts, 'hide_mailinglist' ) ), array( 'yes', 'true', 1 ) );
		}
		if( isset( $atts['list_subscription'] ) ) {
			$show_mailinglist =  strtolower( wlm_arrval( $atts, 'list_subscription' ) ) == 'show';
		}
		
		if ( $show_mailinglist ) {
			$fields .= wlm_form_field(
				array(
					'type'    => 'checkbox',
					'name'    => 'wlm_subscribe',
					'value'   => (int) ( ! (bool) $WishListMemberInstance->Get_UserMeta( $user->ID, 'wlm_unsubscribe' ) ),
					'options' => array(
						'1' => __(
							'Subscribed to Mailing List',
							'wishlist-member'
						),
					),
				)
			);
		}
		$fields .= wlm_form_field(
			array(
				'type'  => 'password_generator',
				'name'  => 'new_pass',
				'value' => '',
				'label' => __(
					'New Password',
					'wishlist-member'
				),
			)
		);
		$fields .= wlm_form_field(
			array(
				'type'  => 'submit',
				'name'  => 'save-profile',
				'value' => __(
					'Update Profile',
					'wishlist-member'
				),
			)
		);

		$javascript = <<<STRING
<script type="text/javascript">
function wlm3_update_displayname(elm) {
if(!elm.value.trim()) return;
var elms = document.forms['wishlist-member-profile-form'].elements;
var options = elms.display_name.options;
if(elm.name == 'nickname') {
options[options.length] = new Option(elm.value);
}
if(elm.name == 'first_name' || elm.name == 'last_name') {
options[options.length] = new Option(elm.value);
var fn = elms.first_name.value.trim();
var ln = elms.last_name.value.trim();
if(fn && ln) {
options[options.length] = new Option(fn + ' ' + ln);
options[options.length] = new Option(ln + ' ' + fn);
}
}
}
</script>
STRING;

		if ( $required ) {
			$required = sprintf( '<div class="wlm3-profile-error">%s</div>', implode( '', $required ) );
		} else {
			$required = '';
		}

		if ( wlm_arrval( $_REQUEST, 'wlm_profile' ) == 'saved' ) {
			$required = sprintf( '<div class="wlm3-profile-ok"><p>%s</p></div>', __( 'Profile saved', 'wishlist-member' ) );
		} else {
			$message = '';
		}

		return sprintf( '<form name="wishlist-member-profile-form" method="POST" action="%s" enctype="multipart/form-data"><div id="wishlist-member-profile-form" class="wlm3-form">%s%s%s</div></form>%s', user_admin_url(), $message, $required, $fields, $javascript );
	}

	function content_levels_list( $atts, $content, $code ) {
		global $WishListMemberInstance;
		$wpm_levels = $WishListMemberInstance->GetOption( 'wpm_levels' );
		$type_list  = array( 'comma', 'ol', 'ul' );
		if ( ! is_array( $atts ) ) {
			$atts = array();
		}
		$atts['link_target']    = isset( $atts['link_target'] ) ? $atts['link_target'] : '_blank';
		$atts['type']           = isset( $atts['type'] ) ? $atts['type'] : 'comma';
		$atts['class']          = isset( $atts['class'] ) ? $atts['class'] : 'wlm_contentlevels';
		$atts['show_link']      = isset( $atts['show_link'] ) ? $atts['show_link'] : 1;
		$atts['salespage_only'] = isset( $atts['salespage_only'] ) ? $atts['salespage_only'] : 1;

		$atts['type']           = in_array( $atts['type'], $type_list ) ? $atts['type'] : 'comma';
		$atts['link_target']    = $atts['link_target'] != '' ? "target='{$atts['link_target']}'" : '';
		$atts['class']          = $atts['class'] != '' ? $atts['class'] : 'wlm_contentlevels';
		$atts['show_link']      = $atts['show_link'] == 0 ? false : true;
		$atts['salespage_only'] = $atts['salespage_only'] == 0 ? false : true;

		$redirect = ! empty( $_GET['wlfrom'] ) ? $_GET['wlfrom'] : false;
		$post_id  = url_to_postid( $redirect );
		$ret      = array();
		if ( $redirect && $post_id !== 0 ) {
			$ptype  = get_post_type( $post_id );
			$levels = $WishListMemberInstance->GetContentLevels( $ptype, $post_id );
			foreach ( $levels as $level ) {
				$salespage        = trim( wlm_arrval( $wpm_levels[ $level ], 'salespage' ) );
				$enable_salespage = (bool) wlm_arrval( $wpm_levels[ $level ], 'enable_salespage' );
				if ( isset( $wpm_levels[ $level ] ) ) {
					if ( $atts['show_link'] && $salespage != '' && $enable_salespage ) {
						$ret[] = "<a class='{$atts['class']}_link' href='{$wpm_levels[$level]['salespage']}' {$atts['link_target']}>{$wpm_levels[$level]['name']}</a>";
					} else {
						if ( ! $atts['salespage_only'] ) {
							$ret[] = $wpm_levels[ $level ]['name'];
						}
					}
				}
			}
		}
		if ( $ret ) {
			if ( $atts['type'] == 'comma' ) {
				$holder = implode( ',', $ret );
				$holder = trim( $holder, ',' );
			} else {
				$holder  = "<{$atts['type']} class='{$atts['class']}'><li>";
				$holder .= implode( '</li><li>', $ret );
				$holder .= "</li></{$atts['type']}>";
			}
			$ret = $holder;
		} else {
			$ret = '';
		}
		return $ret;
	}

	function custom_registration_fields( $atts, $content, $code ) {
		global $WishListMemberInstance, $wpdb;
		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}

		$atts = array_values( $atts );
		if ( ! is_array( $atts[0] ) ) {
			switch ( $atts[0] ) {
				case '':
					$query   = $wpdb->prepare( "SELECT * FROM `{$WishListMemberInstance->Tables->user_options}` WHERE `user_id`=%d AND `option_name` LIKE 'custom\_%%'", $current_user->ID );
					$results = $wpdb->get_results( $query );
					$results = $WishListMemberInstance->GetUserCustomFields( $current_user->ID );
					if ( ! empty( $results ) ) {
						$output = array();
						foreach ( $results as $key => $value ) {
								$output[] = sprintf( '<li>%s : %s</li>', $key, implode( '<br />', (array) $value ) );
						}
						$output = trim( implode( '', $output ) );
						if ( $output ) {
							return '<ul>' . $output . '</ul>';
						}
					}
					break;
				default:
					$field = 'custom_' . $atts[0];
					return trim( $WishListMemberInstance->Get_UserMeta( $current_user->ID, $field ) );
				return implode( '<br />', (array) $WishListMemberInstance->Get_UserMeta( $current_user->ID, $field ) );
			}
		}
	}

	function manual_process( $user_id, $content, $dataonly = false ) {
		$user = get_userdata( $user_id );
		if ( $user->ID ) {
			$GLOBALS['wlm_shortcode_user'] = $user;
			$pattern                       = get_shortcode_regex();
			preg_match_all( '/' . $pattern . '/s', $content, $matches, PREG_SET_ORDER );
			if ( is_array( $matches ) && count( $matches ) ) {
				$data = array();
				foreach ( $matches as $match ) {
					$scode = $match[2];
					$code  = $match[0];
					if ( isset( $this->shortcode_functions[ $scode ] ) ) {
						if ( ! isset( $data[ $code ] ) ) {
							$data[ $code ] = do_shortcode_tag( $match );
						}
					}
				}
				if ( $dataonly == false ) {
					$content = str_replace( array_keys( $data ), $data, $content );
				} else {
					$content = $data;
				}
			}
		}
		return $content;
	}

	function min_password_length() {
		global $WishListMemberInstance, $wpdb;
		$min_value = $WishListMemberInstance->GetOption( 'min_passlength' );
		if ( ! $min_value ) {
			$min_value = 8;
		}
		return $min_value;
	}
	function hasaccess( $atts, $content ) {
		extract(
			shortcode_atts(
				array(
					'post' => null,
				),
				$atts
			)
		);

		$pid = $post;
		if ( empty( $pid ) ) {
			global $post;
			$pid = $post->ID;
		}

		global $current_user;
		global $WishListMemberInstance;

		if ( $WishListMemberInstance->HasAccess( $current_user->ID, $pid ) ) {
			return $content;
		}
		return null;
	}
	function hasnoaccess( $atts, $content ) {
		extract(
			shortcode_atts(
				array(
					'post' => null,
				),
				$atts
			)
		);

		$pid = $post;
		if ( empty( $pid ) ) {
			global $post;
			$pid = $post->ID;
		}

		global $current_user;
		global $WishListMemberInstance;

		if ( $WishListMemberInstance->HasAccess( $current_user->ID, $pid ) ) {
			return null;
		}
		return $content;
	}

	function user_payperpost( $atts ) {
		global $WishListMemberInstance;
		extract(
			shortcode_atts(
				array(
					'sort' => 'ascending',
				),
				$atts
			)
		);

		if ( wlm_arrval( wlm_arrval( $GLOBALS, 'wlm_shortcode_user' ), 'ID' ) ) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval( $GLOBALS, 'current_user' );
		}
		$ppp_uid      = 'U-' . $current_user->ID;
		$user_ppplist = $WishListMemberInstance->GetUser_PayPerPost( $ppp_uid );
		if ( strtolower( $sort ) == 'descending' ) {
			$user_ppplist = array_reverse( $user_ppplist );
		}
		$ppp_list = '<ul>';
		foreach ( $user_ppplist as $list ) {
			$link      = get_permalink( $list->content_id );
			$ppp_list .= '<li><a href="' . $link . '">' . get_the_title( $list->content_id ) . '</a></li>';
		}

		$ppp_list .= '</ul>';
		return '' . $ppp_list . '';
	}

	function registered_payperpost( $atts ) {
		global $WishListMemberInstance;
		$ppp = trim( wlm_arrval( $_GET, 'l' ) );
		if ( ! $ppp || ! $WishListMemberInstance->IsPPPLevel( $ppp ) || ! preg_match( '/\d+$/', $ppp, $match ) ) {
			return '';
		}

		$title = get_the_title( $match[0] );
		$url   = get_permalink( $match[0] );

		if ( ! $url ) {
			return '';
		}
		if ( ! $title ) {
			$title = $url;
		}

		return sprintf( '<a href="%s">%s</a>', $url, $title );
	}

	function member_action_button( $atts ) {
		static $__css_loaded = false;
		// no buttons for non-logged in customers
		if ( ! is_user_logged_in() ) {
			return '';
		}
		// default shortcode attributes
		$atts = shortcode_atts(
			array(
				'level'    => '',
				'action'   => 'ADD',
				'label'    => 'Register to %level%',
				'class'    => '',
				'redirect' => '',
			),
			$atts,
			'wlm_member_action_button'
		);
		// extract attributes
		extract( $atts );

		$level_name = '';
		if ( $payperpost = wishlistmember_instance()->IsPPPLevel( $level ) ) {
			// get post title as $level_name
			$level_name = get_the_title( $payperpost->ID );
			// assign $level to $level_id
			$level_id = $level;
		} else {
			// look for the level id and assign it to $level_id
			foreach ( $this->wpm_levels as $x => $wpm_level ) {
				if ( trim( strtoupper( $wpm_level['name'] ) ) == trim( strtoupper( html_entity_decode( $level ) ) ) ) {
					$level_id   = $x;
					$level_name = $wpm_level['name'];
					break;
				}
			}
		}

		// level not found, return empty string
		if ( empty( $level_name ) ) {
			return '';
		}

		// validate action
		$action = strtoupper( $action );
		if ( ! in_array( $action, $payperpost ? array( 'ADD', 'REMOVE' ) : array( 'MOVE', 'ADD', 'REMOVE', 'CANCEL', 'UNCANCEL' ) ) ) {
			return '';
		}

		// get user
		$user = new \WishListMember\User( get_current_user_id() );
		if ( empty( $user->ID ) ) {
			return '';
		}

		$disabled = false;
		switch ( $action ) {
			case 'MOVE':
			case 'ADD':
				// disable if user is already in the level
				if ( $payperpost ) {
					if ( in_array( $payperpost->ID, $user->PayPerPosts['_all_'] ) ) {
						$disabled = true;
					}
				} else {
					if ( isset( $user->Levels[ $level_id ] ) ) {
						$disabled = true;
					}
				}
				break;
			case 'REMOVE':
				// disable if user is not in the level
				if ( $payperpost ) {
					if ( ! in_array( $payperpost->ID, $user->PayPerPosts['_all_'] ) ) {
						$disabled = true;
					}
				} else {
					if ( ! isset( $user->Levels[ $level_id ] ) ) {
						$disabled = true;
					}
				}
				break;
			case 'CANCEL':
				// disable if user is not in the level or is in the level but the level is Cancelled
				if ( ! isset( $user->Levels[ $level_id ] ) || $user->Levels[ $level_id ]->Cancelled ) {
					$disabled = true;
				}
				break;
			case 'UNCANCEL':
				// disable if user is not in the level or is in the level but the level is not Cancelled
				if ( ! isset( $user->Levels[ $level_id ] ) || ! $user->Levels[ $level_id ]->Cancelled ) {
					$disabled = true;
				}
				break;
		}

		// generate link
		$link = wp_nonce_url(
			add_query_arg(
				array(
					'wishlistmember_member_action_button' => $action,
					'level'                               => $level_id,
					'redirect'                            => $redirect,
				),
				site_url()
			),
			'wishlistmember_member_action_button'
		);

		// add css on first use
		$button = '';
		if ( ! $__css_loaded ) {
			$button       = '<style>.wishlistmember-member-action-button.-disabled { background: grey; color: white; border-color: grey }</style>';
			$__css_loaded = true;
		}

		// generate button
		$button .= sprintf(
			'<button onclick="location.href=\'%s\'" class="wishlistmember-member-action-button %s %s" %s>%s</button>',
			$link,
			$class,
			$disabled ? '-disabled' : '',
			$disabled ? 'disabled="disabled"' : '',
			str_ireplace( '%level%', $level_name, $label )
		);

		return $button;
	}

	function _add_shortcode( $shortcode, $function ) {
		$this->shortcode_functions[ $shortcode ] = $function;
		add_shortcode( $shortcode, $function );
	}
	
	/**
	 * Generate the shortcodes menu for TinyMCE inserter
	 * @param  array $codes
	 */
	function _render_tinymce_shortcode_menu( $codes = null ) {
		static $menu_level;
		$this->enqueue_shortcode_inserter_js();
		$menu_level++;
		if ( is_null( $codes ) ) {
			$codes = $this->manifest;
		}
		$output = array();
		foreach ( $codes as $key => $code ) {
			if( isset( $code['label'] ) ) {
				$attributes = ( ! empty( $code['attributes'] ) || ! empty( $code['enclosing'] ) );
				$output[] = array( 'text' => $code['label'], 'onclick' => sprintf( 'function() { wlm.tinymce_show_shortcode( %s, %s, %s ) }', json_encode( $key ), json_encode( $code['label'] ), json_encode( $attributes ) ) );
			} else {
				// group menu
				$menu = array( 'text' => $key );
				if( is_array( $code ) ) {
					$menu = $menu + (array) $this->_render_tinymce_shortcode_menu( $code );
				}
				$output[] = $menu;
			}
		}
		$menu_level--;
		return array( 'menu' => $output );
	}
	
	/**
	 * Displays the shortcodes menu
	 * @param  array $codes
	 */
	function _render_shortcode_menu( $codes = null ) {
		static $menu_level;
		$this->enqueue_shortcode_inserter_js();
		$menu_level++;
		if ( $menu_level == 1 ) {
			echo '<div class="wlm-shortcodes-menu mb-3">';
		}
		if ( is_null( $codes ) ) {
			$codes = $this->manifest;
		}
		foreach ( $codes as $key => $code ) {
			if ( isset( $code['label'] ) ) {
				if ( ! empty( $code['attributes'] ) || ! empty( $code['enclosing'] ) ) {
					$key = '#wlm-shortcode-inserter-' . $key;
				}
				printf( '<a class="dropdown-item shortcode-creator" href="#" data-value="%s">%s</a>', htmlentities( $key ), $code['label'] );
			} else {
				if( empty( $code ) ) {
					continue;
				}
				$variation     = $menu_level < 2 ? 'dropdown' : 'dropright';
				$dropdown_item = $menu_level < 2 ? 'pr-3 py-2' : 'dropdown-item';

				$id = $menu_level > 1 ? 'wlm-codes-' . preg_replace( '/[^0-9a-z]+/', '-', trim( strtolower( $key ) ) ) : '';
				printf( '<div class="btn-group %s"><a class="%s dropdown-toggle" data-toggle="dropdown" data-target="#%s" aria-haspopup="true" aria-expanded="false">%s</a>', $variation, $dropdown_item, $id, $key );
				printf( '<div class="dropdown-menu" id="%s">', $id );
				$this->_render_shortcode_menu( $code );
				echo '</div></div>';
			}
		}
		$menu_level--;
		if ( ! $menu_level ) {
			echo '</div>';
		}
	}

	/**
	 * Displays the shortcodes attributes
	 * @param  array $shortcodes
	 */
	function _render_shortcode_attributes_form( $shortcodes = null ) {
		$this->enqueue_shortcode_inserter_js();
		if ( is_null( $shortcodes ) ) {
			$shortcodes = $this->manifest;
		}
		foreach ( $shortcodes as $shortcode => $options ) {
			if ( is_array( $options ) ) {
				if ( ! empty( $options['attributes'] ) || ! empty( $options['enclosing'] ) ) {
					echo '<form data-shortcode="' . $shortcode . '" id="wlm-shortcode-inserter-' . $shortcode . '" class="wlm-shortcode-attributes row" style="display:none">';
					printf( '<h3 class="mb-3 col-12">%s</h3>', $options['label'] );
					$has_preview = !empty( $options['has_preview'] );
					if( $has_preview ) {
						echo '<div class="col-6"><div class="row">';
					}
					if ( isset( $options['attributes'] ) && is_array( $options['attributes'] ) ) {
						foreach ( $options['attributes'] as $attr_name => $attr_options ) {
								$dependency = empty( $attr_options['dependency'] ) ? '' : sprintf( 'data-dependency="%s"', htmlentities( $attr_options['dependency'] ) );
								$columns    = wlm_arrval( $attr_options, 'columns' );
							if ( $columns < 0 ) {
								echo '<div class="w-100"></div>';
							}
								printf( '<div %s class="wlm-shortcode-attribute col-%d">', $dependency, abs( $columns ) ?: '12' );
								echo '<div class="form-group">';
								echo '<label class="d-block">' . wlm_arrval( $attr_options, 'label' ) . '</label>';
								$multiple = '';
							switch ( $attr_options['type'] ) {
								case 'select-multiple':
									$multiple = ' multiple="multiple"';
								case 'select':
									$separator = trim( wlm_arrval( $attr_options, 'separator' ) ) ?: '|';
									$placeholder = ! empty( $attr_options['placeholder'] ) ? sprintf( ' data-placeholder="%s"', $attr_options['placeholder'] ) : '';
									echo '<select style="width:100%;" data-separator="' . $separator . '" class="wlm-select form-control" name="' . $attr_name . '"' . $multiple . $placeholder . '>';
									if ( $placeholder ) {
										echo '<option value="" />';
									}
									// recursive function to render options and optgroups
									$this->__options( $attr_options['options'], $attr_options );
									echo '</select>';
									break;
								case 'checkbox':
								case 'radio':
									$inline = (bool) wlm_arrval( $attr_options, 'inline' ) ? ' form-check-inline' : '';
									foreach ( $attr_options['options'] as $value => $value_options ) {
										$unchecked  = isset( $value_options['unchecked'] ) ? sprintf( '<input type="hidden" name="%s" value="%s">', $attr_name, $value_options['unchecked'] ) : '';
										$checked    = ( isset( $attr_options['default'] ) && in_array( $value, (array) $attr_options['default'] ) ) ? ' checked="checked"' : '';
										$dependency = empty( $value_options['dependency'] ) ? '' : sprintf( 'data-dependency="%s"', htmlentities( $value_options['dependency'] ) );
										printf( '<div class="form-check%9$s"><input id="%8$s" class="form-check-input" type="%2$s" name="%3$s" value="%4$s"%5$s><label for="%8$s" %1$s class="form-check-label">%6$s</label>%7$s</div>', $dependency, $attr_options['type'], $attr_name, $value, $checked, $value_options['label'], $unchecked, uniqid( 'id.', true ), $inline );
									}
									break;
								case 'text':
								default:
									printf( '<input class="form-control" type="text" name="%s" value="%s" placeholder="%s">', $attr_name, $attr_options['default'], $attr_options['placeholder'] );
							}
							echo '</div>';
							echo '</div>';
						}
					}
					if ( ! empty( $options['enclosing'] ) ) {
						$placeholder = preg_match('/[a-zA-Z]/', $options['enclosing'] ) ? sprintf( ' placeholder="%s"', htmlentities( $options['enclosing'] ) ) : '';
						printf( '<div class="form-group col-12"><label>Content</label><textarea class="form-control" name="__enclosed_content__"%s></textarea></div>', $placeholder );
					}
					if( $has_preview ) {
						echo '</div></div><div class="col-6 wlm-shortcode-inserter-preview"></div>';
					}
					
					echo '</form>';
				} else {
					$this->_render_shortcode_attributes_form( $options );
				}
			}
		}
	}

	private function __options( $options, $attr_options ) {
		foreach ( $options as $value => $voptions ) {
			$dependency = empty( $voptions['dependency'] ) ? '' : sprintf( 'data-dependency="%s"', htmlentities( $voptions['dependency'] ) );
			if ( isset( $voptions['options'] ) && is_array( $voptions['options'] ) ) {
				// optgroup
				printf( '<optgroup label="%s" %s>', $voptions['label'], $dependency );
				$this->__options( $voptions['options'], $attr_options );
				echo '</optgroup>';
			} else {
				// option
				$selected = ( isset( $attr_options['default'] ) && in_array( $value, (array) $attr_options['default'] ) ) ? ' selected="selected"' : '';
				printf( '<option %s value="%s"%s>%s</option>', $dependency, $value, $selected, $voptions['label'] );
			}
		}
	}
}


