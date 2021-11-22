<?php
$data = new stdClass();

$data->cbthankyou = $this->GetOption( 'cbthankyou' );
if ( ! $data->cbthankyou ) {
	$this->SaveOption( 'cbthankyou', $data->cbthankyou = $this->MakeRegURL() );
}
$data->cbsecret = $this->GetOption( 'cbsecret' );
if ( ! $data->cbsecret ) {
	$this->SaveOption( 'cbsecret', $data->cbsecret = strtoupper( $this->PassGen() . $this->PassGen() ) );
}

$data->cbproducts = (array) $this->GetOption( 'cbproducts' );
if ( ! $data->cbproducts ) {
	$this->SaveOption( 'cbproducts', $data->cbproducts = array() );
}

$data->cb_eot_cancel = wlm_maybe_unserialize( $this->GetOption( 'cb_eot_cancel' ) );
if ( ! is_array( $data->cb_eot_cancel ) ) {
	$this->SaveOption( 'cb_eot_cancel', $data->cb_eot_cancel = array() );
}

$data->cb_scrcancel = wlm_maybe_unserialize( $this->GetOption( 'cb_scrcancel' ) );
if ( ! is_array( $data->cb_scrcancel ) ) {
	$data->cb_scrcancel = array_combine( array_keys( $wpm_levels ), array_fill( 0, count( $wpm_levels ), '1' ) );
	$this->SaveOption( 'cb_scrcancel', $data->cb_scrcancel = $data->cb_scrcancel );
}


$data->cbvendor = strtolower( $this->GetOption( 'cbvendor' ) );

$data->cbthankyou_url = $wpm_scregister . $data->cbthankyou;

thirdparty_integration_data( $config['id'], $data );
