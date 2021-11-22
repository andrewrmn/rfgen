<?php
$data = new stdClass();

$data->paypalprosettings = $this->GetOption( 'paypalprosettings' );
if ( ! is_array( $data->paypalprosettings ) ) {
	$this->SaveOption(
		'paypalprosettings', $data->paypalprosettings = array(
			'live' => array(),
			'sandbox' => array(),
			'sandbox_mode' => 0,
		)
	);
}

$data->paypalprothankyou = $this->GetOption( 'paypalprothankyou' );
if ( ! $data->paypalprothankyou ) {
	$this->SaveOption( 'paypalprothankyou', $data->paypalprothankyou = $this->MakeRegURL() );
}

$data->paypalproproducts = $this->GetOption( 'paypalproproducts' );
if ( ! $data->paypalproproducts ) {
	$data->paypalproproducts = array();
}

$data->paypalprothankyou_url = $wpm_scregister . $data->paypalprothankyou;

thirdparty_integration_data( $config['id'], $data );
