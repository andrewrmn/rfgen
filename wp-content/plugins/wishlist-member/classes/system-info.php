<?php
/**
 * System Info Class for WishList Member
 *
 * @author Fel Jun Palawan <fjpalawan@gmail.com>
 * @package wishlistmember
 */

namespace WishListMember;

defined( 'ABSPATH' ) || die();

/**
 * System Info Class
 *
 * @package wishlistmember
 * @subpackage classes
 */
class System_Info {

	/**
	 * Fields
	 *
	 * @var array
	 */
	var $fields = array();

	/**
	 * Info
	 *
	 * @var array
	 */
	var $info = array();

	var $theme = null;

	function __construct() {
		$server_flds = array(
			'os'                 => 'Operating System',
			'software'           => 'Software',
			'mysql_version'      => 'MySQL version',
			'php_version'        => 'PHP Version',
			'php_max_input_vars' => 'PHP Max Input Vars',
			'php_max_post_size'  => 'PHP Max Post Size',
			'asp_tags'           => 'ASP Tags',
			// 'gd_installed' => 'GD Library',
			// 'zip_installed' => 'ZIP Installed',
			'openssl'            => 'OpenSSL',
			'curl'               => 'cURL',
			'write_permissions'  => 'Write Permissions',
			'path'               => 'Install Folder',
		);

		$wordpress_flds = array(
			'version'             => 'Version',
			'site_url'            => 'Site URL',
			'home_url'            => 'Home URL',
			'is_multisite'        => 'WP Multisite',
			'max_upload_size'     => 'Max Upload Size',
			'memory_limit'        => 'Memory limit',
			'permalink_structure' => 'Permalink Structure',
			'language'            => 'Language',
			'timezone'            => 'Timezone',
			'admin_email'         => 'Admin Email',
			'debug_mode'          => 'Debug Mode',
		);

		$user_flds = array(
			'role'   => 'Role',
			'locale' => 'WP Profile lang',
			'agent'  => 'User Agent',
		);

		$this->theme = wp_get_theme();
		$theme_flds  = array(
			'name'           => 'Name',
			'version'        => 'Version',
			'author'         => 'Author',
			'is_child_theme' => 'Child Theme',
		);
		if ( $this->theme->parent() ) {
			$parent_fields = array(
				'parent_name'    => 'Parent Theme Name',
				'parent_version' => 'Parent Theme Version',
				'parent_author'  => 'Parent Theme Author',
			);
			$theme_flds    = array_merge( $theme_flds, $parent_fields );
		}

		$plugin_flds    = array();
		$active_plugins = $this->get_active_plugins();
		foreach ( $active_plugins as $key => $value ) {
			$plugin_flds[ $key ]           = $value['Name'] . ' - ' . $value['Version'];
			$this->info['plugins'][ $key ] = array(
				'value'   => $value['Author'],
				'fld_url' => $value['PluginURI'],
				'val_url' => $value['AuthorURI'],
			);
		}

		$this->fields = array(
			'server'    => array(
				'title'  => 'Server Environment',
				'fields' => $server_flds,
			),
			'wordpress' => array(
				'title'  => 'WordPress Environment',
				'fields' => $wordpress_flds,
			),
			'theme'     => array(
				'title'  => 'Theme',
				'fields' => $theme_flds,
			),
			'user'      => array(
				'title'  => 'User',
				'fields' => $user_flds,
			),
			'plugins'   => array(
				'title'  => 'Active Plugins',
				'fields' => $plugin_flds,
			),
		);

		if ( is_multisite() ) {
			$nplugin_flds   = array();
			$active_plugins = $this->get_network_plugins();
			foreach ( $active_plugins as $key => $value ) {
				$nplugin_flds[ $key ]                  = $value['Name'] . ' - ' . $value['Version'];
				$this->info['network_plugins'][ $key ] = array(
					'value'   => $value['Author'],
					'fld_url' => $value['PluginURI'],
					'val_url' => $value['AuthorURI'],
				);
			}
			$this->fields['network_plugins'] = array(
				'title'  => 'Network Plugins',
				'fields' => $nplugin_flds,
			);
		};

		$this->info['theme']     = $this->get_theme_values();
		$this->info['user']      = $this->get_user_values();
		$this->info['server']    = $this->get_server_values();
		$this->info['wordpress'] = $this->get_wordpress_values();
	}

	function get_theme_values() {
		global $wpdb;
		$theme = array();

		$theme['name']           = array( 'value' => $this->theme->get( 'Name' ) );
		$theme['version']        = array( 'value' => $this->theme->get( 'Version' ) );
		$theme['author']         = array( 'value' => $this->theme->get( 'Author' ) );
		$theme['is_child_theme'] = array( 'value' => is_child_theme() ? 'Yes' : 'No' );

		if ( $this->theme->parent() ) {
			$theme['parent_name']    = array( 'value' => $this->theme->parent()->get( 'Name' ) );
			$theme['parent_version'] = array( 'value' => $this->theme->parent()->get( 'Version' ) );
			$theme['parent_author']  = array( 'value' => $this->theme->parent()->get( 'Author' ) );
		}
		return $theme;
	}

	function get_user_values() {
		global $wpdb;
		$user = array();

		$role         = null;
		$current_user = wp_get_current_user();
		if ( ! empty( $current_user->roles ) ) {
			$role = $current_user->roles[0];
		}
		$user['role'] = array( 'value' => $role );

		$user['locale'] = array( 'value' => get_locale() );

		$user['agent'] = array( 'value' => $_SERVER['HTTP_USER_AGENT'] );

		return $user;
	}

	function get_server_values() {
		global $wpdb;
		$server = array();

		$server['os'] = array( 'value' => PHP_OS );

		$server['software'] = array( 'value' => $_SERVER['SERVER_SOFTWARE'] );

		$server['mysql_version'] = array( 'value' => $wpdb->db_version() );

		$server['php_version'] = array( 'value' => PHP_VERSION );
		if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
			$server['php_version']['notes'] = _( 'We recommend to use php 5.4.0 or higher' );
		}

		$server['php_max_input_vars'] = array( 'value' => ini_get( 'max_input_vars' ) );

		$server['php_max_post_size'] = array( 'value' => ini_get( 'post_max_size' ) );
		$server['asp_tags']          = array( 'value' => ini_get( 'asp_tags' ) ?: 'Off' );

		$gdlib = 'No';
		if ( extension_loaded( 'gd' ) ) {
			$gdlib = gd_info();
			$gdlib = $gdlib['GD Version'];
		}
		$server['gd_installed'] = array( 'value' => $gdlib );

		$server['zip_installed'] = array( 'value' => extension_loaded( 'zip' ) ? 'Yes' : 'No' );
		if ( $server['zip_installed']['value'] == 'No' ) {
			$server['zip_installed']['notes'] = _( 'Zip Library not installed' );
		}

		$openssl = 'No';
		if ( extension_loaded( 'openssl' ) ) {
			$openssl = OPENSSL_VERSION_TEXT . ' (ver. ' . OPENSSL_VERSION_NUMBER . ')';
		}
		$server['openssl'] = array( 'value' => $openssl );

		$curl = 'No';
		if ( extension_loaded( 'curl' ) ) {
			$curl = curl_version();
			$curl = $curl['version'];

			$ch = curl_init( 'https://www.howsmyssl.com/a/check' );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$data = curl_exec( $ch );
			curl_close( $ch );
			$json = json_decode( $data );
			if ( isset( $json->tls_version ) ) {
				$curl .= ' (using ' . $json->tls_version . ')';
			} else {
				$curl .= ' (TLS not available)';
			}
		}
		$server['curl'] = array( 'value' => $curl );

		$paths_to_check = array( ABSPATH => 'WordPress root directory' );
		$write_problems = array();
		$wp_upload_dir  = wp_upload_dir();
		if ( $wp_upload_dir['error'] ) {
			$write_problems[] = 'WordPress root uploads directory';
		}
		$wlm_uploads_path = $wp_upload_dir['basedir'];
		if ( is_dir( $wlm_uploads_path ) ) {
			$paths_to_check[ $wlm_uploads_path ] = 'Uploads directory';
		}
		$htaccess_file = ABSPATH . '/.htaccess';
		if ( file_exists( $htaccess_file ) ) {
			$paths_to_check[ $htaccess_file ] = '.htaccess file';
		}
		foreach ( $paths_to_check as $dir => $description ) {
			if ( ! is_writable( $dir ) ) {
				$write_problems[] = $description;
			}
		}
		if ( $write_problems ) {
			$value  = 'There are some writing permissions issues with the following directories/files:' . '<br />- ';
			$value .= implode( '<br />- ', $write_problems );
		} else {
			$value = 'OK';
		}
		$server['write_permissions'] = array( 'value' => $value );

		$server['path'] = array( 'value' => ABSPATH );

		return $server;
	}

	function get_wordpress_values() {
		global $wp_rewrite;
		$wordpress = array();

		$wordpress['version']         = array( 'value' => get_bloginfo( 'version' ) );
		$wordpress['site_url']        = array( 'value' => get_site_url() );
		$wordpress['home_url']        = array( 'value' => get_home_url() );
		$wordpress['is_multisite']    = array( 'value' => is_multisite() ? 'Yes' : 'No' );
		$wordpress['max_upload_size'] = array( 'value' => size_format( wp_max_upload_size() ) );

		$wordpress['memory_limit'] = array( 'value' => WP_MEMORY_LIMIT );
		$min_recommended_memory    = '64M';
		$memory_limit_bytes        = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
		$min_recommended_bytes     = wp_convert_hr_to_bytes( $min_recommended_memory );
		// if ( $memory_limit_bytes < $min_recommended_bytes ) {
		// $wordpress['memory_limit']['notes'] = sprintf(
		// _( 'We recommend setting memory to at least %1$s. For more information, read about <a href="%2$s">how to Increase memory allocated to PHP</a>.'),
		// $min_recommended_memory, 'https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP'
		// );
		// }

		$structure = $wp_rewrite->permalink_structure;
		if ( ! $structure ) {
			$structure = 'Plain';
		}
		$wordpress['permalink_structure'] = array( 'value' => $structure );

		$wordpress['language'] = array( 'value' => get_bloginfo( 'language' ) );

		$timezone = get_option( 'timezone_string' );
		if ( ! $timezone ) {
			$timezone = get_option( 'gmt_offset' );
		}
		$wordpress['timezone'] = array( 'value' => $timezone );

		$wordpress['admin_email'] = array( 'value' => get_option( 'admin_email' ) );
		$wordpress['debug_mode']  = array( 'value' => WP_DEBUG ? 'Active' : 'Inactive' );

		return $wordpress;
	}

	function get_raw() {
		$file = '';
		foreach ( $this->fields as $key => $fld ) :
			$file .= "*** {$fld['title']} ***" . "\r\n";
			foreach ( $fld['fields'] as $fld_key => $fld_label ) :
				$file .= str_pad( $fld_label, 20, ' ' ) . ':' . $this->info[ $key ][ $fld_key ]['value'] . "\r\n";
			endforeach;
			$file .= "\r\n";
		endforeach;
		return $file;
	}

	function get_active_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$active_plugins = get_option( 'active_plugins' );
		$plugins        = array_intersect_key( get_plugins(), array_flip( $active_plugins ) );

		return $plugins;
	}

	function get_network_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}
		$active_plugins = get_site_option( 'active_sitewide_plugins' );
		$plugins        = array_intersect_key( get_plugins(), $active_plugins );

		return $plugins;
	}
}
