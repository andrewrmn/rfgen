<?php
$data = new stdClass();

$data->genericthankyou = $this->GetOption( 'genericthankyou' );
if ( ! $data->genericthankyou ) {
	$this->SaveOption( 'genericthankyou', $data->genericthankyou = $this->MakeRegURL() );
}
$data->genericsecret = $this->GetOption( 'genericsecret' );
if ( ! $data->genericsecret ) {
	$this->SaveOption( 'genericsecret', $data->genericsecret = $this->PassGen() . $this->PassGen() );
}

$data->genericthankyou_url = $wpm_scregister . $data->genericthankyou;

thirdparty_integration_data($config['id'], $data);
