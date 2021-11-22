<?php
class cksdk
{
    private $api_secret = "";
    private $api_url = 'https://api.convertkit.com/v3';
    public  $last_error = "";

    function __construct( $api_secret  ) {

        $this->api_secret = $api_secret;
        $this->last_error = "";

        if ( !function_exists( 'curl_init' ) || !function_exists( 'curl_setopt' ) ) {
            $this->api_secret = "";
            trigger_error("cURL not supported.");
            $this->last_error = "cURL not supported.";
        }
    }

    public function get_forms() {
        $request = '/forms';
        $args = array(
            'api_secret' => $this->api_secret,
        );
        return $this->make_request( $request, 'GET', $args );
    }

    public function form_subscribe( $form_id, $args ) {
        $request = "/forms/{$form_id}/subscribe";
        $args['api_secret'] = $this->api_secret;
        return $this->make_request( $request, 'POST', $args );
    }

    public function form_unsubscribe( $email ) {
        $request = '/unsubscribe';
        $args = array(
            'api_secret' => $this->api_secret,
            'email' => $email,
        );
        return $this->make_request( $request, 'PUT', $args );
    }

    public function make_request( $request, $method = 'GET', $args = array()) {
        $this->last_error = "";
        $url = $this->api_url . $request . '?' . http_build_query( $args );
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
        ));
        curl_setopt( $ch, CURLOPT_USERAGENT, 'WLM/CKIntegration(wishlist-member)');
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        if ( 'PUT' == $method ){
            curl_setopt( $ch, CURLOPT_PUT, true );
        }
        $results = curl_exec($ch);
        $header  = curl_getinfo( $ch );
        curl_close($ch);

        if ( $results ) {
            $results = json_decode( $results, true );
        }

        $status_code = 418;
        if ( isset( $header['http_code'] ) ) {
            $status_code = (int) $header['http_code'];
        } else if ( isset( $results['status'] ) ) {
            $status_code = (int) $results['status'];
        }

        if ( $status_code > 201 ) {
            if ( isset( $results['error'] ) ) $this->last_error = $results['error'];
            if ( isset( $results['message'] ) ) $this->last_error .= ':' .$results['message'];
            if ( empty( $this->last_error ) ) $this->last_error = "Unknown error";
            $this->last_error = $status_code .':' .$this->last_error;
            $results = false;
        }

        return $results;
    }

}
