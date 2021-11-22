<?php
$incremental_index = time();
$fields = array(
	'nonce' => array(
		'type'  => 'hidden',
		'name'  => 'nonce',
		'label' => '',
		'value' => wp_create_nonce('eway-do-charge'),
		'class' => ''
	),
	'regform_action' => array(
		'type'  => 'hidden',
		'name'  => 'regform_action',
		'label' => '',
		'value' => 'charge',
		'class' => ''
	),
	'charge_type' => array(
		'type'  => 'hidden',
		'name'  => 'charge_type',
		'label' => '',
		'value' => is_user_logged_in() ? 'existing' : 'new',
		'class' => '',
	),
	'subscription' => array(
		'type'  => 'hidden',
		'name'  => 'subscription',
		'label' => '',
		'value' => $settings['subscription'],
		'class' => ''
	),
	'redirect_to' => array(
		'type'  => 'hidden',
		'name'  => 'redirect_to',
		'label' => '',
		'value' => get_permalink(),
		'class' => ''
	),
	'sku' => array(
		'type'  => 'hidden',
		'name'  => 'sku',
		'label' => '',
		'value' => $sku,
		'class' => ''
	),
	//name fields
  $incremental_index++ => array('type' => 'heading', 'text' => __( 'Personal Information', 'wishlist-member' ) ),
	'first_name' => array(
		'type'        => 'text',
		'name'        => 'first_name',
		'label'       => __('First Name', 'wishlist-member'),
		'placeholder' => __('First Name', 'wishlist-member'),
		'value'       => $current_user->first_name,
    'col'         => 'col-6',
	),
	'last_name' => array(
		'type'        => 'text',
		'name'        => 'last_name',
		'label'       => __('Last Name', 'wishlist-member'),
		'placeholder' => __('Last Name', 'wishlist-member'),
		'value'       => $current_user->last_name,
    'col'         => 'col-6',
	),
	'email' => array(
		'type'        => 'text',
		'name'        => 'email',
		'label'       => __('Email', 'wishlist-member'),
		'placeholder' => __('Email', 'wishlist-member'),
		'value'       => $current_user->user_email,
		'col'         => 'col-12',
	),
  $incremental_index++ => array('type' => 'break'),
  $incremental_index++ => array('type' => 'heading', 'text' => __( 'Card Details', 'wishlist-member' ) ),
	//card fields
	'cc_fields' => array(
    'type' => 'cc_fields',
    'has' => []
	),

  $incremental_index++ => array('type' => 'break'),
);

$heading            = empty($settings['formheading']) ? "Register for %level" : $settings['formheading'];
$heading            = str_replace('%level', $level_name, $heading);
$panel_button_label =  str_replace('%waiting', '<span class="regform-waiting">...</span> ',  $panel_btn_label);
$panel_button_label =  str_replace('%currency', $currency,  $panel_button_label);
$panel_button_label =  str_replace('%amount', $amt,  $panel_button_label);

$data['fields']             = $fields;
$data['heading']            = $heading;
$data['panel_button_label'] = $panel_button_label;
$data['form_action']        = $thankyouurl;
$data['id']                 = $sku;
$data['logo']               = $logo;
$data['showlogin']          = true;

$currency = '';
if($settings['subscription']) {
	switch($settings['rebill_interval_type']){
		case '1': $interval = _n('Day', '%d Days', $settings['rebill_interval'], 'wishlist-member'); break;
		case '2': $interval = _n('Week', '%d Weeks', $settings['rebill_interval'], 'wishlist-member'); break;
		case '3': $interval = _n('Month', '%d Months', $settings['rebill_interval'], 'wishlist-member'); break;
		case '4': $interval = _n('Year', '%d Years', $settings['rebill_interval'], 'wishlist-member'); break;
	}
	$interval = sprintf($interval, $settings['rebill_interval']);
	$data['payment_description'] = $settings['rebill_init_amount'] ? sprintf(__('%.2f %s then ', 'wishlist-member'), $settings['rebill_init_amount'], $currency) : '';
	$data['payment_description'] .= sprintf('%.2f %s every %s until %s', $settings['rebill_recur_amount'], $currency, $interval, $settings['rebill_end_date']);
} else {
	$data['payment_description'] = sprintf('%.2f %s', $settings['rebill_init_amount'], $currency);
}