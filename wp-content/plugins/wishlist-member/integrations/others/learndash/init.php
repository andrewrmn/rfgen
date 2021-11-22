<?php // initialization
if(!class_exists('WLM3_LearnDash_Hooks')) {
	class WLM3_LearnDash_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			add_action('wp_ajax_wlm3_learndash_get_data', array($this, 'get_data'));
		}
		function get_data() {
			// extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
				'courses' => array(),
				'groups' => array(),
			);
			// connect and get info
			try {
				$data["status"] = true;
				$data["message"] = "LearnDash plugin is isntalled and activated";
				$the_posts = new WP_Query(array( 'post_type' =>  'sfwd-courses','nopaging'=>true));
				$courses = [];
				if ( count($the_posts->posts) ) {
					foreach ( $the_posts->posts as $key => $c ) {
						$courses[$c->ID] = $c->post_title;
					}
					$data["courses"] = $courses;
				} else {
					$data["message"] = "You need to create a LearnDash course in order proceed";
				}
				$the_groups = new WP_Query(array( 'post_type' =>  'groups','nopaging'=>true));
				$groups = [];
				if ( count($the_groups->posts) ) {
					foreach ( $the_groups->posts as $key => $c ) {
						$groups[$c->ID] = $c->post_title;
					}
					$data["groups"] = $groups;
				}
			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			wp_die( json_encode($data) );
		}
	}
	new WLM3_LearnDash_Hooks;
}