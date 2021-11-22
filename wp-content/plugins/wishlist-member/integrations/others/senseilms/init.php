<?php // initialization
if(!class_exists('WLM3_SenseiLMS_Hooks')) {
	class WLM3_SenseiLMS_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			add_action('wp_ajax_wlm3_senseilms_check_plugin', array($this, 'check_plugin'));
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
				if ( in_array( 'Sensei LMS', $active_plugins ) || isset($active_plugins['sensei-lms/sensei-lms.php']) || is_plugin_active('sensei-lms/sensei-lms.php') ) {
					$data["status"] = true;
					$data["message"] = "Sensei LMS plugin is installed and activated";
					$the_posts = new WP_Query(array( 'post_type' =>  'course','nopaging'=>true));
					$courses = [];
					if ( count($the_posts->posts) ) {
						foreach ( $the_posts->posts as $key => $c ) {
							$courses[$c->ID] = $c->post_title;
						}
						$data["courses"] = $courses;
					} else {
						$data["message"] = "You need to create a Sensei LMS course in order proceed";
					}
					if ( !class_exists('Sensei_Utils') ) {
						$data["status"] = false;
						$data["message"] = "Sensei LMS is activated but the resources needed are missing. Please contact support.";
					}
				} else {
					$data["message"] = "Please install and activate your SenseiLMS plugin";
				}
			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			wp_die( json_encode($data) );
		}
	}
	new WLM3_SenseiLMS_Hooks;
}