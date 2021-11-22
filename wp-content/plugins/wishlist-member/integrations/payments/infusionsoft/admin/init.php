<?php
$data = new stdClass();

$data->ismachine = trim($this->GetOption('ismachine'));
$data->isapikey = trim($this->GetOption('isapikey'));

$data->isthankyou = $this->GetOption( 'isthankyou' );
if ( ! $data->isthankyou ) {
	$this->SaveOption( 'isthankyou', $data->isthankyou = $this->MakeRegURL() );
}
$data->isthankyou_url = $wpm_scregister . $data->isthankyou;

if(isset($_GET['isenable_log'])) {
	$this->SaveOption('isenable_log', (int) $_GET['isenable_log']);
}
$data->isenable_log = (bool) $this->GetOption('isenable_log');

$tags = array('istags_add_app', 'istags_add_rem', 'istags_remove_app', 'istags_remove_rem', 'istags_cancelled_app', 'istags_cancelled_rem', 'istagspp_add_app', 'istagspp_add_rem', 'istagspp_remove_app', 'istagspp_remove_rem');

foreach($tags AS $tag) {
	$x = $this->GetOption($tag);
	if($x) {
		$x = wlm_maybe_unserialize($x);
	} else {
		$x = array();
	}
	$data->$tag = $x;
}

thirdparty_integration_data($config['id'], $data);

