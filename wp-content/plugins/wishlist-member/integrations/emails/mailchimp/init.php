<?php // initialization
if ( ! class_exists( 'WLM3_MailChimp_Hooks' ) ) {
	class WLM3_MailChimp_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_mailchimp_test_keys', array( $this, 'test_keys' ) );
			add_action( 'wp_ajax_wlm3_mailchimp_get_list_groups', array( $this, 'get_list_groups' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data           = array(
				'status'  => false,
				'message' => '',
				'lists'   => array(),
			);
			$transient_name = 'wlmmchmp_' . md5( $mcapi );
			$ar             = $this->wlm->GetOption( 'Autoresponders' );
			if ( $save ) {
				$ar['mailchimp']['mcapi'] = $mcapi;
				$this->wlm->SaveOption( 'Autoresponders', $ar );
				delete_transient( $transient_name );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_die( json_encode( $transient_result ) );
				}
			}

			// connect and get info
			$lists = array();
			$mc    = \WishListMember\Autoresponders\MailChimp::_interface()->api( $mcapi );

			if ( $mc->get_last_error() != '' ) {
				$data['message'] = $mc->get_last_error();
			} else {
				$data['status'] = true;
				$rec_count      = 100;
				$offset         = 0;
				do {
					$lists = $mc->get(
						'lists',
						array(
							'count'  => $rec_count,
							'offset' => $offset * $rec_count,
						)
					);
					if ( $mc->get_last_error() == '' ) {
						$data['lists'] = array_merge( $data['lists'], $lists['lists'] );
						$offset++;
					} else {
						$lists           = false;
						$data['status']  = false;
						$data['message'] = $mc->get_last_error();
					}
				} while ( isset( $lists['lists'] ) && $lists['lists'] );
			}

			set_transient( $transient_name, $data, 60 * 15 );
			wp_send_json( $data );
		}
		function get_list_groups() {
			extract( $_POST['data'] );
			$groups = \WishListMember\Autoresponders\MailChimp::_interface()->mc_get_lists_groups( $mcapi, $list_id );
			wp_send_json( array( 'groups' => $groups ) );
		}
	}
	new WLM3_MailChimp_Hooks();
}
