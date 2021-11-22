<?php
$data = new stdClass();

$data->redoakcartthankyou = $this->GetOption( 'redoakcartthankyou' );
if ( ! $data->redoakcartthankyou ) {
	$this->SaveOption( 'redoakcartthankyou', $data->redoakcartthankyou = $this->MakeRegURL() );
}
$data->redoakcartsecret = $this->GetOption( 'redoakcartsecret' );
if ( ! $data->redoakcartsecret ) {
	$this->SaveOption( 'redoakcartsecret', $data->redoakcartsecret = $this->PassGen() . $this->PassGen() );
}

$data->redoakcartthankyou_url = $wpm_scregister . $data->redoakcartthankyou;

thirdparty_integration_data($config['id'], $data);
