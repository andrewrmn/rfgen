<?php // initialization
if ( ! class_exists( 'WLM3_Ontraport_Hooks' ) ) {
	class WLM3_Ontraport_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_wlm3_ontraport_test_keys', array( $this, 'test_keys' ) );
		}
		function test_keys() {
			extract( $_POST['data'] );
			$data = array(
				'status' => false,
				'message' => '',
			);

			$transient_name = 'wlmontrprt_' . md5( $app_id . $api_key );
			if ( $save ) {
				$ar = $this->wlm->GetOption( 'Autoresponders' );
				$ar['ontraport']['app_id'] = $app_id;
				$ar['ontraport']['api_key'] = $api_key;
				$this->wlm->SaveOption( 'Autoresponders', $ar );
			} else {
				$transient_result = get_transient( $transient_name );
				if ( $transient_result ) {
					$transient_result['cached'] = 1;
					wp_send_json( $transient_result );
				}
			}

		    set_error_handler(function($errno, $errstr, $errfile, $errline) {});

			try {
				if ( empty( $app_id ) ) {
					throw new Exception( 'Application ID not specified.' );
				}
				if ( empty( $api_key ) ) {
					throw new Exception( 'Api Key not specified.' );
				}

				$api_url = 'https://api.ontraport.com/cdata.php';
				// $api_url = 'https://api.moon-ray.com/cdata.php'; // for testing demo
				
				$post_options = array(
					'body' => array(
						'appid' => $app_id,
						'key' => $api_key,
						'return_id' => 1,
						'reqType' => 'fetch_sequences',
						'data' => '',
					),
				);

				$result = wp_remote_retrieve_body( wp_remote_post( $api_url, $post_options ) );
				$xml = new SimpleXMLElement($result);

				if ( preg_match( '#<error>(.+?)</error>#i', $result, $match ) ) {
					$data['message'] = $match[1];
				} else {
					preg_match_all( '#<sequence id=["\'](.+?)["\']>(.+?)</sequence>#i', $result, $matches );
					$data['sequence_options'] = array();
					foreach ( $matches[1] as $key => $val ) {
						$data['sequence_options'][] = array(
							'id' => $val,
							'value' => $val,
							'text' => $matches[2][ $key ],
							'name' => $matches[2][ $key ],
						);
					}

					$post_options['body']['reqType'] = 'pull_tag';

					$result = wp_remote_retrieve_body( wp_remote_post( $api_url, $post_options ) );
					preg_match_all( '#<tag id=["\'](.+?)["\']>(.+?)</tag>#i', $result, $matches );
					$data['tag_options'] = array();
					foreach ( $matches[1] as $key => $val ) {
						$data['tag_options'][] = array(
							'id' => $val,
							'value' => $val,
							'text' => $matches[2][ $key ],
							'name' => $matches[2][ $key ],
						);
					}

					$data['status'] = true;
				}
			} catch ( Exception $e ) {
				$data['message'] = 'Please check your App ID and API Key.';
			}

			restore_error_handler();

			set_transient( $transient_name, $data, 60 * 15 );
			wp_send_json( $data );
		}
	}
	new WLM3_Ontraport_Hooks();
}
