<?php
$data = new stdClass();

$data->twocothankyou = $this->GetOption( 'twocothankyou' );
if ( ! $data->twocothankyou ) {
	$this->SaveOption( 'twocothankyou', $data->twocothankyou = $this->MakeRegURL() );
}
$data->twocovendorid = $this->GetOption( 'twocovendorid' );
$data->twocosecret = (string) $this->GetOption( 'twocosecret' );
$data->twocodemo = $this->GetOption( 'twocodemo' ) + 0;
$data->twocothankyou_url = $wpm_scregister . $data->twocothankyou;

thirdparty_integration_data($config['id'], $data);
