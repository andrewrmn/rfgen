<?php

$data = new stdClass();

// thank you url
$data->spreedlythankyou = trim( $this->GetOption( 'spreedlythankyou' ) );
if ( ! $data->spreedlythankyou ) {
	$this->SaveOption( 'spreedlythankyou', $data->spreedlythankyou = $this->MakeRegURL() );
}
$data->spreedlythankyou_url = $wpm_scregister . $data->spreedlythankyou;

$data->spreedlyname = trim( $this->GetOption( 'spreedlyname' ) );
$data->spreedlytoken = trim( $this->GetOption( 'spreedlytoken' ) );

thirdparty_integration_data( $config['id'], $data );
