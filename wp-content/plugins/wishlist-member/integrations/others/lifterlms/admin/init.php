<?php
$data = (array) $this->GetOption( 'lifterlms_settings' );

thirdparty_integration_data(
	$config['id'], array(
		'lifterlms_settings' => $data,
	)
);