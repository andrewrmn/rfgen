<?php
$data = (array) $this->GetOption( 'senseilms_settings' );

thirdparty_integration_data(
	$config['id'], array(
		'senseilms_settings' => $data,
	)
);