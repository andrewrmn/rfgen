<?php

$data = new stdClass();

// thank you url
$data->recurlythankyou = trim( $this->GetOption( 'recurlythankyou' ) );
if ( ! $data->recurlythankyou ) {
	$this->SaveOption( 'recurlythankyou', $data->recurlythankyou = $this->MakeRegURL() );
}
$data->recurlythankyou_url = $wpm_scregister . $data->recurlythankyou;

$data->recurlyapikey = (array) $this->GetOption( 'recurlyapikey' );
$data->recurlyconnections = (array) $this->GetOption( 'recurlyconnections' );
$data->plans = array();

thirdparty_integration_data( $config['id'], $data );
