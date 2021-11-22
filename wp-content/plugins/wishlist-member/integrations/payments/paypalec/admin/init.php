<?php
$data = new stdClass();

$data->paypalecsettings = $this->GetOption( 'paypalecsettings' );
if ( ! is_array( $data->paypalecsettings ) ) {
	$this->SaveOption(
		'paypalecsettings', $data->paypalecsettings = array(
			'live' => array(),
			'sandbox' => array(),
			'sandbox_mode' => 0,
		)
	);
}

$data->paypalec_spb = $this->GetOption( 'paypalec_spb' );
$data->paypalec_spb['enable'] = $data->paypalec_spb['enable'] + 0;

$data->paypalec_cancel_url = $this->GetOption( 'paypalec_cancel_url' );
if ( ! $data->paypalec_cancel_url ) {
	$this->SaveOption( 'paypalec_cancel_url', $data->paypalec_cancel_url = get_bloginfo( 'url' ) );
}

$data->paypalecthankyou = $this->GetOption( 'paypalecthankyou' );
if ( ! $data->paypalecthankyou ) {
	$this->SaveOption( 'paypalecthankyou', $data->paypalecthankyou = $this->MakeRegURL() );
}

$data->paypalecproducts = $this->GetOption( 'paypalecproducts' );
if ( ! $data->paypalecproducts ) {
	$data->paypalecproducts = array();
}

$data->paypaleceotcancel = $this->GetOption( 'paypaleceotcancel' );
if ( $data->paypaleceotcancel ) {
	$data->paypaleceotcancel = wlm_maybe_unserialize( $data->paypaleceotcancel );
} else {
	$data->paypaleceotcancel = array();
}

$data->paypalecsubscrcancel = (array) wlm_maybe_unserialize( $this->GetOption( 'paypalecsubscrcancel' ) ) + array_combine( array_keys( $wpm_levels ), array_fill( 0, count( $wpm_levels ), 1 ) );

$data->paypalecthankyou_url = $wpm_scregister . $data->paypalecthankyou;

$data->paypalec_ipnforwarding = $this->GetOption( 'paypalec_ipnforwarding' );

thirdparty_integration_data( $config['id'], $data );
