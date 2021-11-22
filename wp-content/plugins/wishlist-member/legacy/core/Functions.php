<?php

/**
 * Converts $value to an absolute integer
 * @param mixed $value
 * @return integer
 */
function wlm_abs_int($value) {
	return abs((int) $value);
}

/**
 * adds a metadata to the user levels
 * note: right now only supports adding is_latest_registration
 * @param array user_levels
 * @param meta_name is_latest_registration
 *
 * Metadata implementations
 * is_latest_registration - if the current level is the latest level
 * the user has registered in, that level will have $obj->is_lastest_registration = 1
 *
 *
 */
function wlm_add_metadata(&$user_levels, $meta_name = 'is_latest_registration') {
	if ( ! is_array($user_levels) || count($user_levels) <= 0 ) return;
	if ($meta_name = 'is_latest_registration') {
		$idx = 0;
		$ref_ts = 0;
		foreach ($user_levels as $i => $item) {
			if ( is_object( $item ) ){
				$item->is_latest_registration = 0;
				if ($item->Timestamp > $ref_ts) {
					$idx = $i;
					$ref_tx = $item->Timestamp;
				}
			}
		}
		if(isset($user_levels[$idx]) && is_object($user_levels[$idx])) {
			$user_levels[$idx]->is_latest_registration = 1;
		}
		//break early please
		return;
	}
}

function wlm_diff_microtime($mt_old, $mt_new = '') {
	if (empty($mt_new)) {
		$mt_new = microtime();
	}
	list($old_usec, $old_sec) = explode(' ', $mt_old);
	list($new_usec, $new_sec) = explode(' ', $mt_new);
	$old_mt = ((float) $old_usec + (float) $old_sec);
	$new_mt = ((float) $new_usec + (float) $new_sec);
	return number_format($new_mt - $old_mt, 32);
}

/**
 * Prints text to specified file for debugging purposes
 *
 * @param  string $text            Text to print
 * @param  string $filename        Optional destination filename. If none specified, then it will create a file prefixed with wlmdebug_ at the system temp dir
 * @param  string $cookie_to_check Optional cookie to check. If specified, then text is printed only if cookie is non-empty
 */
function wlm_debugout($text, $filename = null, $cookie_to_check = null) {
	if(!is_null($cookie_to_check) && empty($_COOKIE[$cookie_to_check])) return;

	$filename = $filename ? $filename : realpath(sys_get_temp_dir()) . '/wlmdebug_' . date('YMd');

	$text = trim($text) . "\n";

	file_put_contents($filename, $text, FILE_APPEND);
}

/**
 * Dissects the form part of a custom registration form
 * and returns an array of dissected field entries
 * @param string $custom_registration_form_data
 * @return array
 */
function wlm_dissect_custom_registration_form($custom_registration_form_data) {

	function fetch_label($string) {
		if (preg_match('#<td class="label".*?>(.*?)</td>#', $string, $match)) {
			return $match[1];
		} elseif (preg_match('#<td class="label ui-sortable-handle".*?>(.*?)</td>#', $string, $match)) {
			return $match[1];
		} else {
			return false;
		}
	}

	function fetch_desc($string) {
		if (preg_match('#<div class="desc".*?>(.*?)</div></td>#s', $string, $match)) {
			return $match[1];
		} else {
			return false;
		}
	}

	function fetch_attributes($tag, $string) {
		preg_match('#<' . $tag . '.+?>#', $string, $match);
		preg_match_all('# (.+?)="([^"]*?)"#', $match[0], $matches);
		$attrs = array_combine($matches[1], $matches[2]);
		unset($attrs['class']);
		unset($attrs['id']);
		return $attrs;
	}

	function wlm_fetch_options($type, $string) {
		$string = str_replace( [ "\n", "\r" ], '', $string );
		switch ($type) {
			case 'checkbox':
			case 'radio':
				preg_match_all('#<label[^>]*?>\s*<input.+?value="([^"]*?)"[^>]*?>(.*?)\s*</label>#', $string, $matches);
				$options = array();
				for ($i = 0; $i < count($matches[0]); $i++) {
					$option = array(
						'value' => $matches[1][$i],
						'text' => $matches[2][$i],
						'checked' => (int) preg_match('#checked="checked"#', $matches[0][$i])
					);
					$options[] = $option;
				}
				return $options;
				break;
			case 'select':
				preg_match_all('#<option value="([^"]*?)".*?>(.*?)</option>#', $string, $matches);
				$options = array();
				for ($i = 0; $i < count($matches[0]); $i++) {
					$option = array(
						'value' => $matches[1][$i],
						'text' => $matches[2][$i],
						'selected' => (int) preg_match('#selected="selected"#', $matches[0][$i])
					);
					$options[] = $option;
				}
				return $options;
				break;
		}

		return false;
	}

	$form = wlm_maybe_unserialize($custom_registration_form_data);

	$form_data = $form['form'];

	preg_match_all('#<tr class="(.*?li_(fld|submit).*?)".*?>(.+?)</tr>#is', $form_data, $fields);

	$field_types = $fields[1];
	$fields = $fields[3];

	foreach ($fields AS $key => $value) {
		$fields[$key] = array('fields' => $value, 'types' => explode(' ', $field_types[$key]));

		if (in_array('required', $fields[$key]['types'])) {
			$fields[$key]['required'] = 1;
		}
		if (in_array('systemFld', $fields[$key]['types'])) {
			$fields[$key]['required'] = 1;
			$fields[$key]['system_field'] = 1;
		}
		if (in_array('wp_field', $fields[$key]['types'])) {
			$fields[$key]['wp_field'] = 1;
		}

		$fields[$key]['description'] = fetch_desc($fields[$key]['fields']);

		if (in_array('field_special_paragraph', $fields[$key]['types'])) {
			$fields[$key]['type'] = 'paragraph';
			$fields[$key]['text'] = $fields[$key]['description'];
			unset($fields[$key]['description']);
		} elseif (in_array('field_special_header', $fields[$key]['types'])) {
			$fields[$key]['type'] = 'header';
			$fields[$key]['text'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_tos', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			unset($fields[$key]['attributes']['value']);
			unset($fields[$key]['attributes']['checked']);
			$options = wlm_fetch_options('checkbox', $fields[$key]['fields']);
			$fields[$key]['attributes']['value'] = trim( $options[0]['value'] );
			$fields[$key]['text'] = trim(preg_replace('#<[/]{0,1}a.*?>#', '', html_entity_decode($options[0]['value'])));
			$fields[$key]['type'] = 'tos';
			$fields[$key]['required'] = 1;
			$fields[$key]['lightbox'] = (int) in_array('lightbox_tos', $fields[$key]['types']);
		} elseif (in_array('field_radio', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			unset($fields[$key]['attributes']['checked']);
			unset($fields[$key]['attributes']['value']);
			$fields[$key]['options'] = wlm_fetch_options('radio', $fields[$key]['fields']);
			$fields[$key]['type'] = 'radio';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_checkbox', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			unset($fields[$key]['attributes']['checked']);
			unset($fields[$key]['attributes']['value']);
			$fields[$key]['options'] = wlm_fetch_options('checkbox', $fields[$key]['fields']);
			$fields[$key]['type'] = 'checkbox';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_select', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('select', $fields[$key]['fields']);
			$fields[$key]['options'] = wlm_fetch_options('select', $fields[$key]['fields']);
			$fields[$key]['type'] = 'select';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_textarea', $fields[$key]['types']) OR in_array('field_wp_biography', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('textarea', $fields[$key]['fields']);
			preg_match('#<textarea.+?>(.*?)</textarea>#', $fields[$key]['fields'], $match);
			$fields[$key]['attributes']['value'] = $match[1];
			$fields[$key]['type'] = 'textarea';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_hidden', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			$fields[$key]['type'] = 'hidden';
		} elseif (in_array('li_submit', $fields[$key]['types'])) {
			preg_match('#<input .+?value="(.+?)".*?>#', $fields[$key]['fields'], $match);
			$submit_label = $match[1];
			unset($fields[$key]);
		} else {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			$fields[$key]['type'] = 'input';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		}

		unset($fields[$key]['fields']);
		unset($fields[$key]['types']);
	}

	ksort($fields);
	$fields = array('fields' => $fields, 'submit' => $submit_label);

	return $fields;
}

/**
 * Checks if the requested array index is set and returns its value
 * @param array $array_or_object
 * @param string|number $index
 * @param string|number ...$indexes
 * @return mixed
 */
function wlm_arrval( $array_or_object, $index ) {
	$indexes = func_get_args();
	array_shift( $indexes );
	foreach( $indexes as $index ) {
		$type = false;
		if ( is_array( $array_or_object ) && isset( $array_or_object[$index] ) ) {
			$array_or_object = $array_or_object[$index];
		} elseif ( is_object( $array_or_object ) && isset( $array_or_object->$index ) ) {
			$array_or_object = $array_or_object->$index;
		} else {
			return;
		}
	}
	return $array_or_object;
}

/**
 * Function to correctly interpret boolean representations
 * - interprets false, 0, n and no as FALSE
 * - interprets true, 1, y and yes as TRUE
 *
 * @param mixed $value representation to interpret
 * @param type $no_match_value value to return if representation does not match any of the expected representations
 * @return boolean|$no_match_value
 */
function wlm_boolean_value($value, $no_match_value = false) {
	$value = trim(strtolower($value));
	if(in_array($value,array(false, 0, 'false','0','n','no'),true)){
		return false;
	}
	if(in_array($value,array(true, 1, 'true','1','y','yes'),true)){
		return true;
	}
	return $no_match_value;
}

function wlm_admin_in_admin() {

         return ((current_user_can('administrator') || current_user_can('wishlist_admin')) && is_admin());
}


/**
 * wlm cache functions
 */

function wlm_cache_flush() {
	wlm_cache_group_suffix(true);
}

function wlm_cache_set() {
	$args = func_get_args();
	$args[2] .= wlm_cache_group_suffix();
	return call_user_func_array('wp_cache_set', $args);
}

function wlm_cache_get() {
	$args = func_get_args();
	$args[1] .= wlm_cache_group_suffix();
	return call_user_func_array('wp_cache_get', $args);
}

function wlm_cache_delete($key, $group) {
	$args = func_get_args();
	$args[1] .= wlm_cache_group_suffix();
	return call_user_func_array('wp_cache_delete', $args);
}

function wlm_cache_group_suffix($reset = false) {
	static $wlm_cache_group_suffix;
	if(is_null($wlm_cache_group_suffix) && empty($reset)) {
		$wlm_cache_group_suffix = get_option( 'wlm_cache_group_suffix' );
	}
	if(empty($wlm_cache_group_suffix) || !empty($reset)) {
		$wlm_cache_group_suffix = microtime(true);
		update_option( 'wlm_cache_group_suffix', $wlm_cache_group_suffix );
	}
	return $wlm_cache_group_suffix;
}

// end of wlm cache functions

if (!function_exists('sys_get_temp_dir')) {

	function sys_get_temp_dir() {
		if ($temp = getenv('TMP'))
			return $temp;
		if ($temp = getenv('TEMP'))
			return $temp;
		if ($temp = getenv('TMPDIR'))
			return $temp;
		$temp = tempnam(__FILE__, '');
		if (file_exists($temp)) {
			unlink($temp);
			return dirname($temp);
		}
		return null;
	}

}

/**
 * Calls the WishList Member API 2 Internally
 * @param type $request (i.e. "/levels");
 * @param type $method (GET, POST, PUT, DELETE)
 * @param type $data (optional) Associate array of data to pass
 * @return type array WishList Member API2 Result
 */
function WishListMemberAPIRequest($request, $method = 'GET', $data = null) {
	require_once('API2.php');
	$api = new WLMAPI2($request, strtoupper($method), $data);
	return $api->result;
}


if(!function_exists('wlm_get_category_root')) {
	function wlm_get_category_root($id) {
		$cat = get_category($id);
		if($cat->parent) {
			$ancestors = get_ancestors($cat->term_id, 'category');
			$root        = count($ancestors) - 1;
			$root        = $ancestors[$root];
			return $root;
		} else {
			return $cat->term_id;
		}
	}
}

/**
 * @param id the category_id
 * @param string category|post
 * @return array returns a list of categories/posts and posts under category_id
 */
if(!function_exists('wlm_get_category_children')) {
	function wlm_get_category_children($id, $type = 'category') {
		$categories = array();
		$posts      = array();

		$categories = get_categories('child_of='.$id);

		$cats = array();
		foreach($categories as $c) {
			$cats[] = $c->term_id;
		}

		if($type == 'category') {
			return $cats;
		}

		$args = array(
			'category'       => $id,
			'posts_per_page' => -1
		);
		return get_posts($args);
	}
}


if(!function_exists('wlm_get_post_root')) {
	function wlm_get_post_root($id) {
		$cats  = get_the_category($id);
		$roots = array();
		foreach($cats as $c) {
			$roots[] = wlm_get_category_root($c);
		}
		return $roots;
	}
}


if(!function_exists('wlm_get_page_root')) {
	function wlm_get_page_root($id) {
		$post = get_post($id);
		if($post->post_parent) {
			$ancestors = get_post_ancestors($id);
			$root        = count($ancestors) - 1;
			$root        = $ancestors[$root];
		} else {
			$root        = $post->ID;
		}
		return $root;
	}
}
if(!function_exists('wlm_get_page_children')) {
	function wlm_get_page_children($page_id) {
		$children = array();
//		$root     = get_post($page_id);
//		$wp_query = new WP_Query();
//		$wp_pages = $wp_query->query(array('post_type' => 'page', 'posts_per_page' => 999));
//
//		$descendants = get_page_children($root->ID, $wp_pages);
        $descendants = get_children(array('post_parent' => $page_id, 'post_types' => get_post_types()));
		foreach($descendants as $d) {
			$children[] = $d->ID;
		}
		return $children;
	}

}

if(!function_exists('wlm_build_payment_form')) {
	function wlm_build_payment_form($data, $additional_classes='') {
		ob_start();
		extract((array) $data);
		include dirname(__FILE__).'/../resources/forms/popup-regform.php';
		$str = ob_get_clean();
		$str = preg_replace('/\s+/', ' ', $str);
		return $str;
	}

}

if(!function_exists('wlm_video_tutorial')) {
	function wlm_video_tutorial () {
		global $WishListMemberInstance;
		$args = func_get_args();
		$version = explode('.', $WishListMemberInstance->Version);

		// we only take the first digit of minor to comply
		// with john's URL format for tutorial video links
		$version = $version[0] . '-' . substr((string) $version[1], 0, 1);
		$parts = strtolower(implode('-', $args));
		$url = 'http://go.wlp.me/wlm:%s:vid:%s';
		return sprintf($url, $version, $parts);
	}
}

if(!function_exists('wlm_xss_sanitize')) {
	function wlm_xss_sanitize (&$string) {
		$string = preg_replace('/[<>]/', '', strip_tags($string));
	}
}

if( !function_exists( 'wlm_scrutinize_password' ) ) {
	/**
	 * Scrutinize a password's strength based on WishList Member settings
	 * @param  string $password Password to scrutinize
	 * @return true|string      TRUE if password passed scrutiny or error message if not
	 */
	function wlm_scrutinize_password( $password ) {
		$passmin = ( (int) wishlistmember_instance()->GetOption( 'min_passlength' ) ) ?: 8;
		$password = trim($password);
		/* validate password length */
		if ( strlen( $password ) < $passmin ) {
			return sprintf( __( 'Password has to be at least %d characters long and must not contain spaces.', 'wishlist-member' ), $passmin );
		}
		/* validate password strength (if enabled) */
		if ( wishlistmember_instance()->GetOption( 'strongpassword' ) && !wlm_check_password_strength( $password ) ) {
			return __( 'Please provide a strong password. Password must contain at least one uppercase letter, one lowercase letter, one number and one special character.', 'wishlist-member' );
		}
		return true;
	}
}

if(!function_exists('wlm_check_password_strength')) {
	function wlm_check_password_strength($password) {
		if(!preg_match('/[a-z]/', $password)) {
			return false;
		}
		if(!preg_match('/[A-Z]/', $password)) {
			return false;
		}
		if(!preg_match('/[0-9]/', $password)) {
			return false;
		}
		$chars = preg_quote('`~!@#$%^&*()-_=+[{]}|;:",<.>\'\?');
		if(!preg_match('/['.$chars.']/', $password)) {
			return false;
		}
		return true;
	}
}

function wlm_is_email($email) {
	return is_email( stripslashes($email) );
}

if(!function_exists('wlm_setcookie')) {
	function wlm_setcookie() {
		global $WishListMemberInstance;
		$args = func_get_args();
		$prefix = trim($WishListMemberInstance->GetOption('CookiePrefix'));
		if($prefix) {
			$args[0] = $prefix . $args[0];
		}
		return call_user_func_array('setcookie', $args);
	}
}
if(!class_exists('wlm_cookies')) {
	class wlm_cookies {
		private $prefix;
		function __construct() {
			global $wpdb;
			$tablename = $wpdb->prefix . 'wlm_options';
			$this->prefix = trim($wpdb->get_var("SELECT `option_value` FROM `{$tablename}` WHERE `option_name`='CookiePrefix'"));
		}
		function __set($name, $value) {
			$_COOKIE[$this->prefix . $name] = $value;
		}
		function __get($name) {
			return isset($_COOKIE[$this->prefix . $name]) ? $_COOKIE[$this->prefix . $name] : '';
		}
		function __isset($name) {
			return isset($_COOKIE[$this->prefix . $name]);
		}
		function __unset($name) {
			unset($_COOKIE[$this->prefix . $name]);
		}
	}
}

if(!function_exists('wlm_set_time_limit')) {
	function wlm_set_time_limit($time_limit = '') {
		$disabled = explode(',', ini_get('disable_functions'));
  		if(!in_array('set_time_limit', $disabled)) {
  			@set_time_limit($time_limit);
  			return;
  		}

	}
}

if( ! function_exists( 'wlm_insert_user' ) ) {
	/**
	 * Wrapper function for wp_insert_user that takes multisites
	 * into consideration
	 *
	 * If the user being added already exists in the multisite
	 * then simply add the same user to the current blog instead
	 * of attempting to create a new one
	 *
	 * @uses wp_insert_user
	 *
	 * @param array $userdata array compatible with WordPress' wp_insert_user
	 * @return int|WP_Error
	 */
	function wlm_insert_user( $userdata ) {
		global $wishlist_member_inserting_user;
		if ( is_multisite() ) {
			$blog_id = get_current_blog_id();
			$mu_user = get_user_by( 'email', $userdata['user_email'] );
			if ( $mu_user ) {
				if ( is_user_member_of_blog( $mu_user->ID, $blog_id ) ) {
					return false;
				} else {
					add_user_to_blog( $blog_id, $mu_user->ID, get_option('default_role') );
					return $mu_user->ID;
				}
			}
		}
		$wishlist_member_inserting_user = true;
		$result = wp_insert_user( $userdata );
		$wishlist_member_inserting_user = false;
		return $result;
	}
}

if( ! function_exists( 'wlm_create_user' ) ) {
	/**
	 * Replacement function for wp_create_user that
	 * takes multisites into consideration.
	 *
	 * @uses wlm_insert_user
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @return int|WP_Error
	 */
	function wlm_create_user( $username, $password, $email = '' ) {
		$user_login = wp_slash( $username );
		$user_email = wp_slash( $email );
		$user_pass  = $password;

		$userdata = compact( 'user_login', 'user_email', 'user_pass' );
		return wlm_insert_user( $userdata );
	}
}

if( ! function_exists( 'wlm_parse_size' ) ) {
	function wlm_parse_size( $size ) {
	  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	  if ( $unit ) {
	    return round( $size * pow(1024, stripos('bkmgtpezy', $unit[0] ) ) );
	  } else {
	    return round( $size );
	  }
	}
}

if( ! function_exists( 'wlm_get_file_upload_max_size' ) ) {
	function wlm_get_file_upload_max_size() {
	    $max_size   = wlm_parse_size(ini_get('post_max_size'));
	    $upload_max = wlm_parse_size(ini_get('upload_max_filesize'));
	    if ( $upload_max > 0 && $upload_max < $max_size ) {
	    	$max_size = $upload_max;
	    }
	    return $max_size;
	}
}

if( ! function_exists( 'wlm_get_client_ip' ) ) {
	function wlm_get_client_ip() {
		$sources = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR' );
		foreach( $sources AS $ip ) {
			if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false ) {
				return $ip;
			}
		}
		return $_SERVER['REMOTE_ADDR'];
	}
}

if( ! function_exists( 'wlm_enqueue_script' ) ) {
	/**
	 * @uses wp_enqueue_script - https://developer.wordpress.org/reference/functions/wp_enqueue_script/
	 * this is wp_enqueue_script on steroids
	 */
	function wlm_enqueue_script() {
		global $WishListMemberInstance, $current_screen;
		$args = func_get_args();

		wp_deregister_script( $args[0] );
		if( empty ( $args[5] ) ) {
			$args[0] = 'wishlistmember3-js-' . $args[0];
		}

		list( $url, $key, $data ) = array_pad( explode( '|', $args[1], 3), 3, '' );
		if( ! strpos( $url, '://' ) && strpos( $url, '/wp-content/' ) === false ) {
			$args[1] = $WishListMemberInstance->get_js( $url );
		}

		if( empty($args[2] ) ) $args[2] = array();
		array_walk( $args[2], function( &$value ) {
			if( substr( $value, 0, 1 ) == '-' ) $value = 'wishlistmember3-js' . $value;
		} );

		if( empty($args[3] ) ) $args[3] = $WishListMemberInstance->Version;
		call_user_func_array( 'wp_enqueue_script', $args );

		if( ! empty( $key ) && ! empty( $data ) && function_exists( 'wp_script_add_data' ) ) {
			wp_script_add_data( $args[0], $key, $data );
		}
	}
}

if( ! function_exists( 'wlm_enqueue_style' ) ) {
	/**
	 * @uses wp_enqueue_style - https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 * this is wp_enqueue_style on steroids
	 */
	function wlm_enqueue_style() {
		global $WishListMemberInstance, $current_screen;
		$args = func_get_args();
		if( empty ( $args[5] ) ) {
			$args[0] = 'wishlistmember3-css-' . $args[0];
		}

		list( $url, $key, $data ) = array_pad( explode( '|', $args[1], 3), 3, '' );
		if( ! strpos( $url, '://' ) && strpos( $url, '/wp-content/' ) === false ) {
			$args[1] = $WishListMemberInstance->get_css( $url );
		}

		if( empty($args[2] ) ) $args[2] = array();
		array_walk( $args[2], function( &$value ) {
			if( substr( $value, 0, 1 ) == '-' ) $value = 'wishlistmember3-css' . $value;
		} );

		if( empty($args[3] ) ) $args[3] = $WishListMemberInstance->Version;
		call_user_func_array( 'wp_enqueue_style', $args );

		if( ! empty( $key ) && ! empty( $data ) ) {
			wp_style_add_data( $args[0], $key, $data );
		}
	}
}

if( ! function_exists( 'wlm_form_field' ) )  {
	/**
	 * Generate and return standardized form field markup
	 * @param  array   $attributes   An array of attributes as supported by the input element. Special markup generated for type=textarea,select,checkbox,radio,submit,reset,button. options=array supported for type=select,checkbox,radio
	 * @return string                Standardized form field markup
	 */
	function wlm_form_field( $attributes ) {
		static $password_generator = false;
		static $password_metered = false;
		wp_enqueue_style( 'wlm3_form_css' );

		$defaults = [
			'label' => '',
			'name' => '',
			'type' => 'text',
			'value' => '',
			'options' => [],
			'class' => '',
			'id' => '',
			'description' => '',
			'text' => '',
			'lightbox' => '',
		];

		$hide = __( 'Hide', 'wishlist-member' );
		$show = __( 'Show', 'wishlist-member' );
		$cancel = __( 'Cancel', 'wishlist-member' );

		$attributes = wp_parse_args( $attributes, $defaults );

		$label = trim( $attributes[ 'label' ] );
		unset( $attributes[ 'label' ] );

		$value = $attributes[ 'value' ];
		unset( $attributes[ 'value' ] );

		$options = (array) $attributes[ 'options' ];
		unset( $attributes[ 'options' ] );

		$type = $attributes[ 'type' ];
		unset( $attributes[ 'type' ] );

		$text = $attributes[ 'text' ];
		unset( $attributes[ 'text' ] );

		$lightbox = $attributes[ 'lightbox' ];
		unset( $attributes[ 'lightbox' ] );

		if( !$attributes['id'] && $attributes['name'] ) {
			$attributes['id'] = 'wlm_form_field_' . $attributes['name'];
		}

		$description = trim( $attributes['description'] );
		unset( $attributes['description'] );
		if( $description ) {
			$description = sprintf( '<div class="wlm3-form-description">%s</div>', $description );
		}

		switch( $type ) {
			case 'paragraph':
				$field = sprintf( '<div class="wlm3-form-text">%s</div>', $text );
				break;
			case 'header':
				$field = sprintf( '<div class="wlm3-form-header">%s</div>', $text );
				break;
			case 'tos':
				if( $lightbox ) {
					wp_enqueue_script( 'wlm-jquery-fancybox' );
					wp_enqueue_style( 'wlm-jquery-fancybox' );
				}
				$field = [ 'input' ];
				$attributes[ 'class' ] .= ' form-checkbox fld';
				$attributes[ 'type' ] = 'checkbox';
				$attributes[ 'value' ] = $value;
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				if( !preg_match( '#((<p|div|br>)|</[a-zA-Z]+[0-9]*>)#', $description ) ) { // convert to html
					$description = nl2br( $description );
				}
				if( $lightbox ) {
					$description = sprintf( '<div style="display:none;"><div id="%s-lightbox">%s</div></div>', $attributes['id'], $description );
					$text = sprintf( '<a class="wlm3-tos-fancybox" href="#%s-lightbox">%s</a>', $attributes['id'], $text );
				} else {
					$description = sprintf( '<div class="wlm3-form-tos">%s</div>', $description );
				}

				$field = str_replace( [ '%%%field%%%', '%%%label%%%' ], [ implode( ' ', $field ), trim( $text ) ], '<label><%%%field%%%> %%%label%%%</label>' );
				break;
			case 'textarea':
				$attributes[ 'class' ] .= ' wlm3-form-field fld';
				$field = [ 'textarea' ];
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				$field = '<' . implode( ' ', $field ) . '>' . $value . '</textarea>';
				break;
			case 'select':
				$attributes[ 'class' ] .= ' wlm3-form-field fld';
				if( isset( $attributes[ 'multiple' ] ) && !preg_match( '/\[\]$/', $attributes[ 'name' ] ) ) {
					$attributes[ 'name' ] .= '[]';
				}
				$field = [ 'select' ];
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				foreach( $options AS $k => &$v ) {
					$selected = $k == $value ? ' selected="selected"' : '';
					$v = sprintf( '<option value="%s"%s>%s</option>', htmlentities( $k ), $selected, $v );
				}
				unset( $v );
				$field = '<' . implode( ' ', $field ) . '>' . implode( '', $options ) . '</select>';
				break;
			case 'checkbox':
				if( count( $options ) > 1 && !preg_match( '/\[\]$/', $attributes[ 'name' ] ) ) {
					$attributes[ 'name' ] .= '[]';
				}
			case 'radio':
				$attributes[ 'class' ] .= ' form-checkbox fld';
				$field = '';
				$checkbox = [ 'input' ];
				$attributes[ 'type' ] = $type;
				foreach( $attributes AS $k => $v ) {
					$checkbox[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				foreach( $options AS $k => $v ) {
					$checkbox['c'] = $k == $value ? 'checked="checked"' : '';
					$checkbox['v'] = sprintf( 'value="%s"', htmlentities( $k ) );
					$field .= str_replace( [ '%%%field%%%', '%%%label%%%' ], [ implode( ' ', $checkbox ), $v ], '<label><%%%field%%%> %%%label%%%</label>' );
				}
				break;
			case 'button':
				$field = [ 'button' ];
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				$field = '<' . implode( ' ', $field ) . '>' . $value . '</button>';
				break;
			case 'rawhtml':
				$field = $value;
				break;
			case 'profile_photo':
				$str = __( 'Select File', 'wishlist-member' );

				$gravatar = wlm_get_gravatar();
				$src = $value ?: wishlistmember_instance()->pluginURL3 . '/assets/images/grey.png';
				
				$upload_icon = sprintf( '<img src="%s" height="30" width="30">', wishlistmember_instance()->pluginURL3 . '/ui/images/cloud_upload-24px.svg' );
				$delete_icon = sprintf( '<img src="%s" height="30" width="30">', wishlistmember_instance()->pluginURL3 . '/ui/images/highlight_off-24px.svg' );
				$undo_icon = sprintf( '<img src="%s" height="30" width="30">', wishlistmember_instance()->pluginURL3 . '/ui/images/restore-24px.svg' );
				$gravatar_logo = sprintf( '<img src="%s" height="30" width="30">', wishlistmember_instance()->pluginURL3 . '/ui/images/gravatar-logo.svg' );

				$field = '<div class="wlm3-profile-photo-container -clean"><div class="wlm3-profile-photo"><input type="hidden" name="' . $attributes[ 'name' ] . '" old-value="' . $value . '" value="' . $value . '"><img gravatar-src="' . $gravatar . '" old-src="' . $src . '" src="' . $src . '" class="profile-photo" /></div><div class="wlm3-profile-photo-icons"><div><label title="Upload Photo"><span class="wlm3-profile-photo-uploader">' . $upload_icon . '</span><input type="file" name="' . $attributes['name'] . '-upload" accept="image/jpeg,image/jpg,image/png"></label><label title="Gravatar"><span class="wlm3-profile-photo-gravatar">' . $gravatar_logo . '</span></label><label title="Delete"><span class="wlm3-profile-photo-clear">' . $delete_icon . '</span></label><label title="Undo unsaved changes" class="-undo"><span class="wlm3-profile-photo-undo">' . $undo_icon . '</span></label></div></div></div>';

				break;
			case 'password_generator':
				if(!$password_generator) {
					$type = 'text';

					$id = '_' . md5( rand() . microtime() );
					$attributes[ 'id' ] = 'wlm3-password-field' . $id;

					$attributes[ 'onkeyup' ] = sprintf( 'wlm3_password_strength(this, \'%1$s\')', $id );
					$attributes[ 'style' ] .= ' display: none;';

					$append = sprintf( '<div id="wlm3-password-generator-strength%1$s"></div>', $id );

					$prepend = sprintf( '<button id="wlm3-password-generator-button%1$s" type="button" onclick="wlm3_generate_password(\'%1$s\'); return false">%2$s</button>', $id, __( 'Generate Password', 'wishlist-member' ) );
					$prepend .= sprintf( '<div id="wlm3-password-generator-buttons%1$s" style="display: none;"><button id="wlm3-password-generator-toggle%1$s" onclick="wlm3_generate_password_toggle(this, \'%1$s\'); return false;" data-hide="%2$s" data-show="%3$s">%2$s</button> <button id="wlm3-password-generator-cancel" onclick="wlm3_generate_password_hide(\'%1$s\'); return false;">%4$s</button></div>', $id, $hide, $show, $cancel );
					$password_generator = true;
				} else {
					$type = 'password';
				}
				$from_passgen = true;
			case 'password_metered':
				if( empty( $from_passgen ) ) {
					$type = 'password';

					$id = '_' . md5( rand() . microtime() );
					$attributes[ 'id' ] = 'wlm3-password-field' . $id;

					$attributes[ 'onkeyup' ] = sprintf( 'wlm3_password_strength(this, \'%1$s\')', $id );

					$append = sprintf( '<div id="wlm3-password-generator-strength%1$s"></div>', $id );
					$prepend = sprintf( '<div id="wlm3-password-generator-buttons%1$s" style="display: none;"><button id="wlm3-password-generator-toggle%1$s" onclick="wlm3_generate_password_toggle(this, \'%1$s\'); return false;" data-hide="%2$s" data-show="%3$s">%3$s</button></div>', $id, $hide, $show );
				}

				wp_enqueue_script( 'wlm3_form_js' );
				wp_enqueue_script( 'jquery' );
			case 'password':
				$password_toggle = (bool) wlm_arrval( $attributes, 'toggle' );
				unset( $attributes['toggle'] );
				$value = '';

			default:
				if( !in_array( $type, [ 'submit', 'reset', 'image' ] ) ) {
					$attributes[ 'class' ] .= ' wlm3-form-field fld';
				}
				$attributes[ 'type' ] = $type;
				$attributes[ 'value' ] = $value ;
				$field = [ 'input' ];
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				$field = '<' . implode( ' ', $field ) . '>';
				if( !empty( $prepend ) ) {
					$field = $prepend . $field;
				}
				if( !empty( $append ) ) {
					$field .= $append;
				}
		}

		switch( $type ) {
			case 'submit':
			case 'button':
			case 'image':
			case 'reset':
				$markup = '<p>%%%field%%%</p>';
				break;
			case 'hidden':
				$markup = '%%%field%%%';
				break;
			case 'password':
				if( $password_toggle ) {
					$field = sprintf( '<span class="wishlist-member-login-password">%s<a href="#" class="dashicons dashicons-visibility" aria-hidden="true"></a></span>', $field );
				}
			default:
				$markup = $label ? '<div class="wlm3-form-group"><label>%%%label%%%</label>%%%field%%%%%%description%%%</div>' : '<div class="wlm3-form-group">%%%field%%%%%%description%%%</div>';
		}

		$code = str_replace( [ '%%%label%%%', '%%%field%%%', '%%%description%%%' ], [ $label, $field, $description ], $markup );

		return $code;
	}
}

if( !function_exists( 'wlm_get_import_file_csv_separator' ) ) {
	/**
	 * Attempt to auto-detect the separator used in a CSV file
	 * Important: This function rewinds the file pointer to the beginning of the file
	 *
	 * @param  resource $file_resource File handle
	 * @return string
	 */
	function wlm_detect_csv_separator( $file_resource ) {
		$separators = [ ',' => 0, ';' => 0, '|' => 0, "\t" => 0 ];

		rewind( $file_resource );
		$line = fgets( $file_resource );
		rewind( $file_resource );

		foreach( $separators AS $sep => &$count ) {
			$count = count( str_getcsv( $line, $sep ) );
		}
		unset( $count );

		return array_search( max( $separators ), $separators );
	}
}

if( !function_exists( 'wlm_get_active_plugins' ) ) {
	/**
	 * Attempt to auto-detect the separator used in a CSV file
	 * Important: This function rewinds the file pointer to the beginning of the file
	 *
	 * @param  resource $file_resource File handle
	 * @return string
	 */
	function wlm_get_active_plugins() {
		$active = get_option('active_plugins');
		$plugins = get_plugins();
		$active_plugins = array();
		foreach ( $active as $a ) {
		    if ( isset($plugins[$a]) ) {
		    	$active_plugins[$a] = isset($plugins[$a]['Name']) ? $plugins[$a]['Name'] : $a;
		    }
		}
		return $active_plugins;
	}
}

if( !function_exists( 'wlm_post_type_is_excluded' ) ) {
	function wlm_post_type_is_excluded( $post_type ) {
		/**
		 * Filters post types includes from WishList Member protection
		 * @param array Array of post types
		 */
		$excluded_post_types = apply_filters( 'wishlistmember_excluded_post_types', array() );
		return in_array( $post_type, (array) $excluded_post_types );
	}
}

if( !function_exists( 'wlm_generate_key' ) ) {
	function wlm_generate_key( $length = 128 ) {
		if( $length < 1 ) $length = 128;
		return substr( base64_encode( openssl_random_pseudo_bytes ( $length * 5 ) ), 0, $length );
	}
}

if( !function_exists( 'wlm_select2' ) ) {
	/**
	 * Load select2 into jQuery.fn.wlmselect2
	 *
	 * To be used in areas where we need select2 outside of WishList Member's screen
	 */
	function wlm_select2() {
		global $WishListMemberInstance;
		// styles
		wlm_enqueue_style( 'select2', 'select2.min.css' );
		wlm_enqueue_style( 'select2-bootstrap', 'select2-bootstrap.min.css' );

		// scripts
		wp_register_script( 'wlmselect2', $WishListMemberInstance->pluginURL3 . '/assets/js/wlmselect2.js', '', '', true );
		wp_localize_script( 'wlmselect2', 'wlmselect2src', $WishListMemberInstance->pluginURL3 . '/assets/js/select2.min.js' );
		wp_enqueue_script( 'wlmselect2' );
	}
}


if( !function_exists( 'wlm_replace_recursive' ) ) {

	/**
	 * Replaces the first data with the second data
	 * and does so recursively if both are arrays
	 *
	 * Replace $data1 with $data2 if any of th following is true
	 * - $data1 is not an array
	 * - $data1 is a sequentially indexed array
	 * - $data2 is not an array
	 *
	 * If $data1 is an associative array and $data2 is an array
	 * then recursively replace $data1 with $data2
	 *
	 * @param mixed $data1
	 * @param mixed $data2
	 * @return mixed
	 */
	function wlm_replace_recursive( $data1, $data2 ) {
		// $data1 is not an array
		if ( ! is_array( $data1 ) ) {
			return $data2;
		}
		// $data1 is a sequentially indexed array
		if ( array_keys( $data1 ) === range( 0, count( $data1 ) - 1 ) ) {
			return $data2;
		}
		// $data2 is not an array
		if ( ! is_array( $data2 ) ) {
			return $data2;
		}

		// at this point we can be sure of two things:
		// $data1 is an associative array
		// $data2 is an array (associative or not)
		foreach ( array_keys( $data2 ) as $key ) {
			if ( isset( $data1[ $key ] ) ) {
				// if there's a matching $key between $data1 and $data2 then recursively replace it
				$data1[ $key ] = wlm_replace_recursive( $data1[ $key ], $data2[ $key ] );
			} else {
				// if $data1 has not matching $key then create it
				$data1[ $key ] = $data2[ $key ];
			}
		}

		return $data1;
	}

}

if( !function_exists( 'wlm_has_html' ) ) {
	/**
	 * Checks if $data contains HTML code
	 * @param string $data
	 * @return boolean
	 */
	function wlm_has_html( $data ) {
		return preg_match( '#(</p>|</div>|</a>|</span>|<br\b.*?>)#i', $data ) ? true : false;
	}
}

if( !function_exists( 'wlm_generate_passowrd' ) ) {
	/**
	 * Wrapper function for wp_generate_password
	 * Checks if wp_generate_password exists and if not, include wp-includes/pluggable.php to create the function
	 *
	 * @uses wp_generate_password()
	 *
	 * @param  integer $length              The length of password to generate.
	 * @param  boolean $special_chars       Whether to include standard special characters.
	 * @param  boolean $extra_special_chars Whether to include other special characters. Used when generating secret keys and salts.
	 * @return string                       The random password.
	 */
	function wlm_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
		if( !function_exists( 'wp_generate_password' ) ) {
			include_once ABSPATH . '/' . WPINC . '/pluggable.php';
		}
		return call_user_func_array( 'wp_generate_password', func_get_args() );
	}
}

if( !function_exists( 'wlm_remove_inactive_levels' ) ) {
	/**
	 * Remove inactive levels from the passed array of Level IDs
	 * @param  integer $user_id User ID
	 * @param  array   $levels  Array of Level IDs
	 * @return array            $levels but with all the inactive levels for the user removed
	 */
	function wlm_remove_inactive_levels( $user_id, $levels ) {
		$user_levels = wishlistmember_instance()->GetMembershipLevels( $user_id, false, true, null, false, false );
		return array_intersect( (array) $levels, $user_levels );
	}
}


if( !function_exists( 'wlm_maybe_json_encode' ) ) {
	/**
	 * JSON encodes data if it's non-scalar or if it's boolean
	 * Accepts all parameters supported by php.net/json_encode
	 * Note: Use json_last_error() if you want to check for actual encoding error
	 * @param  mixed $data Required. Data to encode into JSON
	 * @return mixed       JSON Encoded string if $data is non-scalar or boolean. Otherwise return unchanged $data
	 */
	function wlm_maybe_json_encode( $data ) {
		if( !is_scalar( $data ) || is_boolean( $data ) ) {
			$data = call_user_func_array( 'json_encode', func_get_args() );
		}
		return $data;
	}
}

if( !function_exists( 'wlm_maybe_json_decode' ) ) {
	/**
	 * Attempts to decode $data as JSON
	 * Accepts all parameters supported by php.net/json_decode
	 * Note: Use json_last_error() if you want to check for actual decoding error
	 * @param  string $data Required. Data to decode as JSON
	 * @return mixed        Decoded JSON if $data is string and no error occured during decoding. Otherwise return original $data
	 */
	function wlm_maybe_json_decode( $data ) {
		if( is_string( $data ) ) {
			$x = call_user_func_array( 'json_decode', func_get_args() );
			if( json_last_error() === JSON_ERROR_NONE ) {
				$data = $x;
			}
		}
		return $data;
	}
}

if( ! function_exists( 'wlm_serialize_corrector' ) ) {
	/**
	 * Attempt to fix corrupted serialized data if necessary
	 * Returns $serialized_string if it doesn't even look like serialized data
	 * 
	 * @param  string $serialized_string Broken serialized data to fix
	 * @return string                    Repaired serialized data
	 */
	function wlm_serialize_corrector( $serialized_string ){
		if( !is_string( $serialized_string ) ) {
			// strings only
			return $serialized_string;
		}
		
		// arrays, objects and strings only and if it's actually broken
		if ( preg_match( '/^([aos]):\d+:/i', $serialized_string, $match ) && !is_serialized( $serialized_string ) ) {
			$fixed_string = preg_replace_callback( '/s\:(\d+)\:\"(.*?)\";/s', function( $matches ) { return 's:' . strlen( $matches[2]) . ':"' . $matches[2] . '";'; }, $serialized_string );
			
			if( $match[1] == 'o' ) {
				// objects begin with uppercase 'O'
				$fixed_string[0] = 'O';
			} elseif( in_array( $match[1], array( 'A', 'S' ) ) ) {
				// arrays and strings begin with lowercase 'a' and 's' respectively
				$fixed_string[0] = strtolower( $match[1] );
			}

			if( is_serialized( $fixed_string ) ) {
				return $fixed_string;
			}
		}
		
		// return original string
		return $serialized_string;
	}
}

if( !function_exists( 'wlm_maybe_serialize' ) ) {
	/**
	 * Serializes data using WordPress' `maybe_serialize()` function
	 * BUT only if $data is not already serialized.
	 *
	 * Note: Calling `maybe_serialize()` alone will re-serialize an
	 * already serialized string thus the need for this function
	 * 
	 * @param  mixed $data Data to serialize
	 * @return string      Serialized data
	 */
	function wlm_maybe_serialize( $data ) {
		if( !is_serialized( $data ) ) {
			$data = maybe_serialize( $data );
		}
		return $data;
	}
}

if( !function_exists( 'wlm_maybe_unserialize' ) ) {
	/**
	 * Unserializes data using WordPress' `maybe_unserialize()` function
	 * but also ensures to take care of possible double serialization caused
	 * by WordPress' `maybe_serialize()` weird behavior of re-serializing
	 * already serialized data
	 * 
	 * @param  string $data Data to unserialize
	 * @return mixed        Unserialized data
	 */
	function wlm_maybe_unserialize( $data ) {
		do {
			$data = maybe_unserialize( wlm_serialize_corrector( $data ) );
		} while( is_serialized( $data ) );
		return $data;
	}
}

if( !function_exists( 'wlm_get_gravatar' ) ) {
	/**
	 * Returns gravatar URL from email
	 * @param  string $email  (optional) Email address. Default value: Current logged-in user's email address
	 * @param  array  $params (optional) Array of arguments to pass to gravatar. Default value: [ 's' => 512, 'd' => 'mm', 'r' => '' ]. See https://codex.wordpress.org/Using_Gravatars.
	 * @return [type]         [description]
	 */
	function wlm_get_gravatar( $email = null, $params = array() ) {
		$params = wp_parse_args(
			$data,
			array(
				's'    => 512,
				'd'     => 'mm',
				'r'      => '',
			)
		);
		
		if( empty( $email ) ) {
			$email = ( $email = wp_get_current_user() ) ? $email->user_email : '';
		}
		return sprintf( '//www.gravatar.com/avatar/%s?%s', md5( strtolower( trim( $email ) ) ), http_build_query( $params ) );
	}
}

if( !function_exists( 'wlm_is_image' ) ) {
	/**
	 * Checks if a file is a valid png, jpeg or gif image
	 * by calling finfo_open() and finfo_file() or exif_imagetype()
	 * or getimagesize() - whichever is available in that order
	 *
	 * Returns false if none of the above functions is available
	 * 
	 * @since 3.9
	 * @param  string  $path_to_image Path to image to check
	 * @return boolean
	 */
	function wlm_is_image( $path_to_image ) {
		// check using finfo functions
		if( function_exists( 'finfo_open' ) && function_exists( 'finfo_file' ) ) {
			$info = finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $path_to_image );
			return in_array( $info, array( 'image/png', 'image/jpeg', 'image/gif' ) );
		}
		
		// check using exif_imagetype 
		if( function_exists( 'exif_imagetype' ) ) {
			$info = exif_imagetype( $path_to_image );
			return in_array( $info, array( IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF ) );
		}
		
		// check using getimagesize
		if( function_exists( 'getimagesize' ) ) {
			$info = getimagesize( $path_to_image );
			return is_array( $info ) && in_array( wlm_arrval( $info, 2 ), array( IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF ) );
		}
		
		// no available functions to check, return false
		return false;
	}
}

if( !function_exists( 'wlm_generate_username' ) ) {
	/**
	 * Generates a username based on the userdata and format provided
	 *
	 * Returns false if data provided isnt enough
	 *
	 * @since 3.9
	 * @param  array  $userdata array of user's data
	 * @param  array  $username_format the format of the username you want
	 * @return mixed  The username string generated or False if some data are missing
	 */
	function wlm_generate_username( $userdata, $username_format ) {

		if ( !isset($userdata['first_name']) && !isset($userdata['last_name']) && !isset($userdata['email']) ) return false;
		if ( empty($username_format) ) return false;

		// 1. replace {name}, {fname}, {lname} and {email} shortcodes
		$shortcodes = array(
			'{name}'  => trim( $userdata['first_name'] . ' ' . $userdata['last_name'] ),
			'{fname}' => trim( $userdata['first_name'] ),
			'{lname}' => trim( $userdata['last_name'] ),
			'{email}' => trim( $userdata['email'] ),
		);
		$username   = str_replace( array_keys( $shortcodes ), $shortcodes, $username_format );

		// 2. replace {rand_ltr n}, {rand_num n} and {rand_mix n} shortcodes
		if ( preg_match_all( '/{rand_(ltr|num|mix)\s+(\d+)}/', $username_format, $matches ) ) {
			$ltr = implode( '', range( 'a', 'z' ) + range( 'A', 'Z' ) );
			$num = implode( range( 0, 9 ) );
			foreach ( $matches[0] as $index => $code ) {
				$pos = strpos( $username, $code );
				if ( $pos !== false ) {
					$length = $matches[2][ $index ];
					switch ( $matches[1][ $index ] ) {
						case 'ltr':
							$ltr     = str_shuffle( $ltr );
							$replace = substr( $ltr, 0, $length );
							break;
						case 'num':
							$num     = str_shuffle( $num );
							$replace = substr( $num, 0, $length );
							break;
						default:
							$ltr     = str_shuffle( $ltr );
							$replace = substr( $ltr, 0, ceil( $length / 2 ) );

							$num      = str_shuffle( $num );
							$replace .= substr( $num, 0, floor( $length / 2 ) );

							$replace = str_shuffle( $replace );
					}
					$username = substr_replace( $username, $replace, $pos, strlen( $code ) );
				}
			}
		}

		// 3. sanitize the generated shortcode and trim it to WP's 60-character limit
		$username = substr( trim( sanitize_user( preg_replace( '/\s+/', ' ', $username ), true ) ), 0, 60 );

		if ( empty($username) ) return false;

		// 4. make sure the username is unique. if not keep appending -n until it's unique
		$counter = 2;
		while ( get_user_by( 'login', str_replace( ',', '-', $username ) ) ) {
			$replace = ',' . $counter;
			if ( strlen( $username ) >= 60 ) {
				$username = substr( $username, 0, 60 - strlen( $replace ) );
			}
			$username  = preg_replace( '/\,\d+$/', '', $username );
			$username .= $replace;
			$counter++;
		}
		$username = str_replace( ',', '-', $username );

		return $username;
	}
}

if( !function_exists( 'wlm_get_apache_version' ) ) {
	/**
	 * Get version of Apache
	 * Uses `apache_get_version()` if available.
	 * Otherwise use `$_SERVER['SERVER_SOFTWARE']`
	 * 
	 * @return string|false Apache version number if it's found or false otherwise
	 */
	function wlm_get_apache_version() {
		if( function_exists( 'apache_get_version' ) ) {
			$version = apache_get_version();
		} else {
			$version = wlm_arrval( $_SERVER, 'SERVER_SOFTWARE' );
		}
		if( preg_match( '/Apache\/([0-9\.]+)/i', $version, $version ) ) {
			return $version[1];
		}
		return false;
	}
}