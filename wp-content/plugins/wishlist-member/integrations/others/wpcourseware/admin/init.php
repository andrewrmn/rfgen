<?php
$data = (array) $this->GetOption( 'wpcourseware_settings' );

thirdparty_integration_data(
	$config['id'], array(
		'wpcourseware_settings' => $data,
	)
);

