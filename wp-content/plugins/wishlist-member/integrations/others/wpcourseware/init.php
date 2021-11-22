<?php // initialization
if(!class_exists('WLM3_WPCourseware_Hooks')) {
	class WLM3_WPCourseware_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			add_action('wp_ajax_wlm3_wpcourseware_check_plugin', array($this, 'check_plugin'));
		}
		function check_plugin() {
			// extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
				'courses' => array(),
			);
			// connect and get info
			try {
				$active_plugins  = wlm_get_active_plugins();
				if ( in_array( 'WP Courseware', $active_plugins ) || isset($active_plugins['wp-courseware/wp-courseware.php']) ) {
					$data["status"] = true;
					$data["message"] = "WP Courseware plugin is isntalled and activated";

					$the_posts = WPCW_courses_getCourseList(false);
					$courses = [];
					if ( count($the_posts) ) {
						foreach ( $the_posts as $key => $c ) {
							$courses[$key] = $c;
						}
						$data["courses"] = $courses;
					} else {
						$data["message"] = "You need to create a WP Courseware course in order proceed";
					}

					//check for WPCourseware WLM Add On
					if ( in_array( 'WP Courseware - Wishlist Member Add On', $active_plugins ) || isset($active_plugins['wishlist-member-addon-for-wp-courseware/wp-courseware-wishlist-member.php']) ) {
						$data["status"] = false;
						$data["message"] = "This integration does not work with <strong>WP Courseware - Wishlist Member Add On</strong> activated. Please deactivate the plugin in order to use this feature.";
					}

					if ( !function_exists('WPCW_courses_syncUserAccess') ) {
						$data["status"] = false;
						$data["message"] = "WP Courseware is activated but the functions needed are missing. Please contact support.";
					}
				} else {
					$data["message"] = "Please install and activate your WP Courseware plugin";
				}
			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			wp_die( json_encode($data) );
		}
	}
	new WLM3_WPCourseware_Hooks;
}