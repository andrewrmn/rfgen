<?php

if ( ! class_exists( 'WLM3_WebHooks_Hooks' ) ) {
	class WLM3_WebHooks_Hooks {
		private $wlm3;
		function __construct() {
			$this->wlm3 = $GLOBALS['WishListMemberInstance'];
			add_action( 'wp_ajax_wlm3_delete_incoming_webhook', array( $this, 'delete_incoming_webhook' ) );
		}

		/**
		 * Action: wp_ajax_wlm3_delete_incoming_webhook
		 *
		 * Deletes an incoming webhook configuration
		 */
		function delete_incoming_webhook() {
			$setting = $this->wlm3->GetOption( 'webhooks_settings' );
			unset( $setting['incoming'][ wlm_arrval( $_POST, 'id' ) ] );
			$setting = array_merge(
				array(
					'outgoing' => array(),
					'incoming' => array(),
				),
				$setting
			);
			$this->wlm3->SaveOption( 'webhooks_settings', $setting );
			wp_send_json(
				array(
					'success' => true,
					'data'    => array(
						'webhooks_settings' => $setting,
					),
				)
			);
		}
	}
	new WLM3_WebHooks_Hooks();
}
