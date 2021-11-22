<?php // initialization
if ( ! class_exists( 'WLM3_ConvertKit_Hooks' ) ) {
	class WLM3_ConvertKit_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_convertkit_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data = array(
				'status'  => false,
				'message' => '',
			);

			$transient_name = 'wlmckapi_' . md5( $ckapi );
			if ( $save ) {
				$ar                        = $this->wlm->GetOption( 'Autoresponders' );
				$ar['convertkit']['ckapi'] = $ckapi;
				$this->wlm->SaveOption( 'Autoresponders', $ar );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_die( json_encode( $transient_result ) );
				}
			}

			// connect and get lists
			$ck = \WishListMember\Autoresponders\ConvertKit::_interface()->cksdk( $ckapi );
			if ( $ck->last_error != '' ) {
				$data['message'] = $ck->last_error;
			} else {
				$f = $ck->get_forms();
				if ( $f === false ) {
					$data['message'] = $ck->last_error;
				} else {
					$data['status'] = true;
					$data['lists']  = array();
					if ( isset( $f['forms'] ) && is_array( $f['forms'] ) ) {
						foreach ( $f['forms'] as $key => $value ) {
							$data['lists'][] = array(
								'value' => $value['id'],
								'text'  => $value['name'],
							);
						}
					}
				}
			}

			set_transient( $transient_name, $data, 60 * 15 );
			wp_die( json_encode( $data ) );
		}
	}
	new WLM3_ConvertKit_Hooks();
}
