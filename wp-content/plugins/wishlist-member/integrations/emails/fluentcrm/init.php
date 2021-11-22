<?php // initialization
if(!class_exists('WLM3_FluentCRM_Hooks')) {
	class WLM3_FluentCRM_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			add_action('wp_ajax_wlm3_fluentcrm_check_plugin', array($this, 'check_plugin'));
		}
		function check_plugin() {
			// extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
				'lists' => array(),
				'tags' => array(),
			);
			// connect and get info
			try {
				$active_plugins  = wlm_get_active_plugins();
				if ( in_array( 'FluentCRM - Marketing Automation For WordPress', $active_plugins ) || isset($active_plugins['fluent-crm/fluent-crm.php']) || is_plugin_active('fluent-crm/fluent-crm.php') ) {
					$data["status"] = true;
					$data["message"] = "FluentCRM plugin is installed and activated";

				    $listApi = FluentCrmApi('lists');
				    $allLists = $listApi->all();
				    $lists = [];
					foreach ( $allLists as $key => $value ) {
						$lists[$value->id] = $value->title;
					}
					$data["lists"] = $lists;

				    $tagApi = FluentCrmApi('tags');
				    $allTags = $tagApi->all();
				    $tags = [];
					foreach ( $allTags as $key => $value ) {
						$tags[$value->id] = $value->title;
					}
					$data["tags"] = $tags;
				} else {
					$data["message"] = "Please install and activate FluentCRM plugin";
				}
			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			wp_die( json_encode($data) );
		}
	}
	new WLM3_FluentCRM_Hooks;
}