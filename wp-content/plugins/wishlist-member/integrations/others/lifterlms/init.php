<?php // initialization
if(!class_exists('WLM3_LifterLMS_Hooks')) {
	class WLM3_LifterLMS_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			add_action('wp_ajax_wlm3_lifterlms_check_plugin', array($this, 'check_plugin'));
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
				if ( in_array( 'LifterLMS', $active_plugins ) || isset($active_plugins['lifterlms/lifterlms.php']) || is_plugin_active('lifterlms/lifterlms.php') ) {
					$data["status"] = true;
					$data["message"] = "Lifter LMS plugin is installed and activated";
					$the_posts = new WP_Query(array( 'post_type' =>  'course','nopaging'=>true));
					$courses = [];
					if ( count($the_posts->posts) ) {
						foreach ( $the_posts->posts as $key => $c ) {
							$courses[$c->ID] = $c->post_title;
						}
						$data["courses"] = $courses;
					} else {
						$data["message"] = "You need to create a Lifter LMS course in order proceed";
					}

					$the_memberships = new WP_Query(array( 'post_type' =>  'llms_membership','nopaging'=>true));
					$memberships = [];
					if ( count($the_memberships->posts) ) {
						foreach ( $the_memberships->posts as $key => $c ) {
							$memberships[$c->ID] = $c->post_title;
						}
						$data["memberships"] = $memberships;
					}

					if ( !function_exists("llms_enroll_student") ) {
						$data["status"] = false;
						$data["message"] = "Lifter LMS is activated but the resources needed are missing. Please contact support.";
					}
				} else {
					$data["message"] = "Please install and activate your LifterLMS plugin";
				}
			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			wp_die( json_encode($data) );
		}
	}
	new WLM3_LifterLMS_Hooks;
}