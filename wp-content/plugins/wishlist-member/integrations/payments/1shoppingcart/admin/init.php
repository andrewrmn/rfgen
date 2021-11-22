<?php
$data = new stdClass();

$data->scthankyou = $this->GetOption( 'scthankyou' );
if ( ! $data->scthankyou ) {
	$this->SaveOption( 'scthankyou', $data->scthankyou = $this->MakeRegURL() );
}

$data->scthankyou_url = $wpm_scregister . $data->scthankyou . '.PHP';

$data->onescmerchantid = $this->GetOption( 'onescmerchantid' );
$data->onescapikey = $this->GetOption( 'onescapikey' );

// Other Settings
$data->onescgraceperiod = $this->GetOption( 'onescgraceperiod' );
if ( ! $data->onescgraceperiod ) {
	$this->SaveOption( 'onescgraceperiod', $data->onescgraceperiod = 3 );
}
$data->onesc_include_upsells = $this->GetOption( 'onesc_include_upsells' ) + 0;

thirdparty_integration_data($config['id'], $data);
