<?php
$data = new stdClass();

$data->anloginid = $this->GetOption( 'anloginid' );
$data->antransid = $this->GetOption( 'antransid' );
$data->anmd5hash = $this->GetOption( 'anmd5hash' );
$data->anetsandbox = (int) $this->GetOption( 'anetsandbox' );
$data->anthankyou = $this->GetOption( 'anthankyou' );
if ( ! $data->anthankyou ) {
	$this->SaveOption( 'anthankyou', $data->anthankyou = $this->MakeRegURL() );
}

$data->anthankyou_url = $wpm_scregister . $data->anthankyou;

thirdparty_integration_data($config['id'], $data);
