<?php

$data = (array) $this->GetOption( 'WLMiDev' );

thirdparty_integration_data(
	$config['id'], array('WLMiDev' => $data)
);
