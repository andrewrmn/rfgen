<?php
$data = new stdClass();

$data->pptoken = $this->GetOption( 'pptoken' );
$data->ppemail = $this->GetOption( 'ppemail' );
$data->ppsandbox = (int) $this->GetOption( 'ppsandbox' );
$data->ppsandboxtoken = $this->GetOption( 'ppsandboxtoken' );
$data->ppsandboxemail = $this->GetOption( 'ppsandboxemail' );
$data->ppthankyou = $this->GetOption( 'ppthankyou' );
if ( ! $data->ppthankyou ) {
	$this->SaveOption( 'ppthankyou', $data->ppthankyou = $this->MakeRegURL() );
}

$data->paypalpsproducts = $this->GetOption('paypalpsproducts');
if(!$data->paypalpsproducts) $data->paypalpsproducts = array();

$data->eotcancel = $this->GetOption( 'eotcancel' );
if ( $data->eotcancel ) {
	$data->eotcancel = wlm_maybe_unserialize( $data->eotcancel );
} else {
	$data->eotcancel = array();
}

$data->subscrcancel = (array) wlm_maybe_unserialize( $this->GetOption( 'subscrcancel' ) ) + array_combine( array_keys( $wpm_levels ), array_fill( 0, count( $wpm_levels ), 1 ) );

$data->ppthankyou_url = $wpm_scregister . $data->ppthankyou;

thirdparty_integration_data( $config['id'], $data );
