<?php
if ( ! class_exists( 'WLM3_Evidence_Hooks' ) ) {
	class WLM3_Evidence_Hooks {
		function __construct() {
			add_action( 'wp_ajax_wlm3_evidence_test_webhook', array( $this, 'test_webhook' ) );
		}

		public function test_webhook() {
			global $WishListMemberInstance;

			if ( ! is_user_logged_in() || ! is_admin() ) {
				wp_send_json_error();
			}

			do_action( 'wishlistmember_add_user_levels', 'wlm3-evidence-webhook-test', array( $_POST['level'] ?: 'wlm3-evidence-webhook-test' ), array() );
				wp_send_json_success();
		}

	}
	new WLM3_Evidence_Hooks();
}
