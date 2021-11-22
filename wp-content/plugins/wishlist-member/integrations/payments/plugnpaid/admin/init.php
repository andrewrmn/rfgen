<?php

$data = new stdClass;

$data->plugnpaidthankyou = trim( $this->GetOption( 'plugnpaidthankyou' ) );
if ( ! $data->plugnpaidthankyou ) {
	$this->SaveOption( 'plugnpaidthankyou', $data->plugnpaidthankyou = $this->MakeRegURL() );
}

$data->plugnpaid_products = (array) $this->GetOption( 'plugnpaid_products' );

$data->plugnpaidapikey = trim( $this->GetOption( 'plugnpaidapikey' ) );

$data->plugnpaidthankyou_url = $wpm_scregister . $data->plugnpaidthankyou;

thirdparty_integration_data( $config['id'], $data );
