<?php
$data = new stdClass();

$data->pwcthankyou = $this->GetOption( 'pwcthankyou' );
if ( ! $data->pwcthankyou ) {
	$this->SaveOption( 'pwcthankyou', $data->pwcthankyou = $this->MakeRegURL() );
}
$data->pwcsecret = $this->GetOption( 'pwcsecret' );
if ( ! $data->pwcsecret ) {
	$this->SaveOption( 'pwcsecret', $data->pwcsecret = $this->PassGen() . $this->PassGen() );
}

$data->pwcapikey = $this->GetOption( 'pwcapikey' );
if ( ! $data->pwcapikey ) {
	$this->SaveOption( 'pwcapikey',  $data->pwcapikey = '' );
}

$data->pwcmerchantid = $this->GetOption( 'pwcmerchantid' );
if ( ! $data->pwcsecret ) {
	$this->SaveOption( 'pwcmerchantid',  $data->pwcmerchantid = '' );
}

$data->pwcthankyou_url = $wpm_scregister . $data->pwcthankyou;

thirdparty_integration_data( $config['id'], $data );
