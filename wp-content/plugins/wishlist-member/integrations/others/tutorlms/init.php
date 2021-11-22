<?php // initialization
if(!class_exists('WLM3_TutorLMS_Hooks')) {
	class WLM3_TutorLMS_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			add_action('wp_ajax_wlm3_tutorlms_check_plugin', array($this, 'check_plugin'));
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
				if ( in_array( 'TutorLMS', $active_plugins ) || isset($active_plugins['tutor/tutor.php']) ) {
					$data["status"] = true;
					$data["message"] = "Tutor LMS plugin is installed and activated";
					$the_posts = new WP_Query(array( 'post_type' =>  'courses','nopaging'=>true));
					$courses = [];
					if ( count($the_posts->posts) ) {
						foreach ( $the_posts->posts as $key => $c ) {
							$courses[$c->ID] = $c->post_title;
						}
						$data["courses"] = $courses;
					} else {
						$data["status"] = false;
						$data["message"] = "You need to create a Tutor LMS course in order proceed";
					}
					if ( !function_exists('tutor_utils') ) {
						$data["status"] = false;
						$data["message"] = "Tutor LMS is activated but the functions needed are missing. Please contact support.";
					}
				} else {
					$data["message"] = "Please install and activate your Tutor LMS plugin";
				}
			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			wp_die( json_encode($data) );
		}
	}
	new WLM3_TutorLMS_Hooks;
}