<?php
$data = new stdClass();

$data->cydecthankyou = $this->GetOption( 'cydecthankyou' );
if ( ! $data->cydecthankyou ) {
	$this->SaveOption( 'cydecthankyou', $data->cydecthankyou = $this->MakeRegURL() );
}
$data->cydecsecret = $this->GetOption( 'cydecsecret' );
if ( ! $data->cydecsecret ) {
	$this->SaveOption( 'cydecsecret', $data->cydecsecret = $this->PassGen() . $this->PassGen() );
}

$data->cydecthankyou_url = $wpm_scregister . $data->cydecthankyou;

thirdparty_integration_data($config['id'], $data);
