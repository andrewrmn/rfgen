<?php
$data = new stdClass();

$data->payflowsettings = $this->GetOption( 'payflowsettings' );
if ( ! is_array( $data->payflowsettings ) ) {
	$this->SaveOption(
		'payflowsettings', $data->payflowsettings = array(
			'live' => array(),
			'sandbox' => array(),
			'sandbox_mode' => 0,
		)
	);
}

$data->payflowthankyou = $this->GetOption( 'payflowthankyou' );
if ( ! $data->payflowthankyou ) {
	$this->SaveOption( 'payflowthankyou', $data->payflowthankyou = $this->MakeRegURL() );
}

$data->paypalpayflowproducts = $this->GetOption( 'paypalpayflowproducts' );
if ( ! $data->paypalpayflowproducts ) {
	$data->paypalpayflowproducts = array();
}

$data->payflowthankyou_url = $wpm_scregister . $data->payflowthankyou;

thirdparty_integration_data( $config['id'], $data );
