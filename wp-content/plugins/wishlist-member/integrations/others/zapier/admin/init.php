<?php

$data = (array) $this->GetOption( 'zapier_settings' );

$key = trim( $data['key'] );
if ( empty( $key ) ) {
	$data['key'] = sha1( microtime() . time() . rand() );
	$this->SaveOption( 'zapier_settings', $data );
}

thirdparty_integration_data(
	$config['id'], array(
		'zapier_settings' => $data,
	)
);
