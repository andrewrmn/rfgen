<?php
$data = (array) $this->GetOption( 'elearncommerce_settings' );

thirdparty_integration_data(
	$config['id'], array(
		'elearncommerce_settings' => $data,
	)
);

