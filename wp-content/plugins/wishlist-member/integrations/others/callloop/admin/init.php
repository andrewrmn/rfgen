<?php

$data = (array) $this->GetOption( 'callloop_settings' );

thirdparty_integration_data(
	$config['id'], array('callloop_settings' => $data)
);
