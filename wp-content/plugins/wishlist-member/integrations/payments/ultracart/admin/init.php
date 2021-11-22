<?php
$data = new stdClass();

$data->ultracartthankyou = $this->GetOption( 'ultracartthankyou' );
if ( ! $data->ultracartthankyou ) {
	$this->SaveOption( 'ultracartthankyou', $data->ultracartthankyou = $this->MakeRegURL() );
}
$data->ultracartsecret = $this->GetOption( 'ultracartsecret' );
if ( ! $data->ultracartsecret ) {
	$this->SaveOption( 'ultracartsecret', $data->ultracartsecret = $this->PassGen() . $this->PassGen() );
}

$data->ultracartthankyou_url = $wpm_scregister . $data->ultracartthankyou;

thirdparty_integration_data($config['id'], $data);
