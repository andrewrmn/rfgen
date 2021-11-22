<?php
$data = (array) $this->GetOption( 'tutorlms_settings' );

thirdparty_integration_data(
	$config['id'], array(
		'tutorlms_settings' => $data,
	)
);

