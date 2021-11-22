<?php

$data = new stdClass();

$data->currencies = array( 'USD', 'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BWP', 'BZD', 'CAD', 'CDF', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD', 'EEK', 'EGP', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GIP', 'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'ISK', 'JMD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KRW', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'LTL', 'LVL', 'MAD', 'MDL', 'MGA', 'MKD', 'MNT', 'MOP', 'MRO', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SEK', 'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'STD', 'SVC', 'SZL', 'THB', 'TJS', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX', 'UYU', 'UZS', 'VEF', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XOF', 'XPF', 'YER', 'ZAR', 'ZMW' );
foreach ( $data->currencies as &$currency ) {
	$currency = array(
		'value' => $currency,
		'text'  => $currency,
	);
}
unset( $currency );

$data->stripethankyou = trim( $this->GetOption( 'stripethankyou' ) );
if ( ! $data->stripethankyou ) {
	$this->SaveOption( 'stripethankyou', $data->stripethankyou = $this->MakeRegURL() );
}

$data->stripeapikey         = trim( $this->GetOption( 'stripeapikey' ) );
$data->stripepublishablekey = trim( $this->GetOption( 'stripepublishablekey' ) );
$data->stripeconnections    = $this->GetOption( 'stripeconnections' );
if ( ! is_array( $data->stripeconnections ) ) {
	$data->stripeconnections = array();
}

// @since 3.6 merge "plans" with "plan" array so our product dropdown shows all selected plans
foreach ( $data->stripeconnections as &$sconn ) {
		$sconn['plan'] = array( $sconn['plan'] );
	if ( ! empty( $sconn['plans'] ) ) {
		$x = json_decode( stripslashes( $sconn['plans'] ) );
		if ( is_array( $x ) ) {
			$sconn['plan'] = array_merge( $sconn['plan'], array_values( $x ) );
		}
	}
}
unset( $sconn );

$data->stripesettings = $this->GetOption( 'stripesettings' );
if ( ! is_array( $data->stripesettings ) ) {
	$data->stripesettings = array();
}
$data->stripesettings = wp_parse_args(
	$data->stripesettings,
	array(
		'endsubscriptiontiming' => 'periodend',
		'prorate'               => 'yes',
		'currency'              => 'USD',
		'formheading'           => 'Register for %level',
		'buttonlabel'           => 'Join %level',
		'panelbuttonlabel'      => 'Pay',
		'supportemail'          => get_option( 'admin_email' ),
	)
);

$data->stripethankyou_url = $wpm_scregister . $data->stripethankyou;

$data->pages = get_pages( 'exclude=' . implode( ',', $this->ExcludePages( array(), true ) ) );
foreach ( $data->pages as &$page ) {
	$page = array(
		'value' => $page->ID,
		'text'  => $page->post_title,
	);
}
unset( $page );

$data->plan_options = array();
$data->plans        = array();

thirdparty_integration_data( $config['id'], $data );
