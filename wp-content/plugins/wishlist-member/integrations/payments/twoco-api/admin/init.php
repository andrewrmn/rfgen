<?php

$data = new stdClass();

// currencies
$data->currencies = array( 'USD', 'AED', 'ARS', 'AUD', 'BRL', 'CAD', 'CHF', 'DKK', 'EUR', 'GBP', 'HKD', 'ILS', 'INR', 'JPY', 'LTL', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'RON', 'RUB', 'SEK', 'SGD', 'TRY', 'ZAR' );
foreach ( $data->currencies as &$c ) {
	$c = array(
		'value' => $c,
		'text' => $c,
	);
}
unset( $c );

// rebill interval
$data->rebill_interval = array();
for ( $i = 1; $i <= 30; $i++ ) {
	$data->rebill_interval[] = array(
		'value' => $i,
		'text' => $i,
	);
}

// billing periods
$data->rebill_interval_type = array(
	array(
		'value' => '2',
		'text' => 'Week(s)',
	),
	array(
		'value' => '3',
		'text' => 'Month(s)',
	),
	array(
		'value' => '4',
		'text' => 'Year(s)',
	),
);

// thank you url
$data->twocothankyou = trim( $this->GetOption( 'twocothankyou' ) );
if ( ! $data->twocothankyou ) {
	$this->SaveOption( 'twocothankyou', $data->twocothankyou = $this->MakeRegURL() );
}
$data->twocothankyou_url = $wpm_scregister . $data->twocothankyou;


// Set the Thank You URL for 2Checkout API, the thank you url above is for the 2CO legacy.
$data->twocheckoutapithankyouurl = trim( $this->GetOption( 'twocheckoutapithankyouurl' ) );
if ( ! $data->twocheckoutapithankyouurl ) {
	$this->SaveOption( 'twocheckoutapithankyouurl', $data->twocheckoutapithankyouurl = $this->MakeRegURL() );
}
$data->twocheckoutapithankyouurl_url = $wpm_scregister . $data->twocheckoutapithankyouurl;

// legacy 2co settings
$data->twocovendorid = trim( $this->GetOption( 'twocovendorid' ) );
$data->twocosecret = trim( $this->GetOption( 'twocosecret' ) );

// settings
$data->twocheckoutapisettings = (array) $this->GetOption( 'twocheckoutapisettings' );

// form settings
$form_defaults = array(
	'formheading' => 'Register for %level',
	'buttonlabel' => 'Join %level',
	'panelbuttonlabel' => 'Pay',
	'supportemail' => get_option( 'admin_email' ),
);

$data->twocheckoutapisettings = wp_parse_args( $data->twocheckoutapisettings, $form_defaults );

thirdparty_integration_data( $config['id'], $data );
