<?php
$incremental_index = time();
$fields = array(
	'nonce' => array(
		'type'  => 'hidden',
		'name'  => 'nonce',
		'label' => '',
		'value' => wp_create_nonce('stripe-do-charge'),
		'class' => ''
	),
	'stripe_action' => array(
		'type'  => 'hidden',
		'name'  => 'stripe_action',
		'label' => '',
		'value' => 'charge',
		'class' => ''
	),
	'charge_type' => array(
		'type'  => 'hidden',
		'name'  => 'charge_type',
		'label' => '',
		'value' => 'existing',
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
		'placeholder' =>  __('Email', 'wishlist-member'),
		'value'       => $current_user->user_email,
    'col'         => 'col-12',
	),
  $incremental_index++ => array('type' => 'break'),
	'coupon' => array(
		'type'        => $coupon ? 'text' : 'none',
		'name'        => 'coupon',
		'label'       =>  __('Coupon Code', 'wishlist-member'),
		'placeholder' => __('Coupon Code', 'wishlist-member'),
		'class'       => 'stripe-coupon',
		'value'       => "",
		'error_text'  => __( 'Invalid Coupon Code', 'wishlist-member' ),
	),
  $incremental_index++ => array('type' => 'break'),
  $incremental_index++ => array('type' => 'heading', 'text' => __( 'Card Details', 'wishlist-member' ) ),
	//card fields
  'cc_fields' => array(
      'type' => 'cc_fields',
      'has' => ['cc_cvc']
  ),
  $incremental_index++ => array('type' => 'break'),

);

//if amount or currency was overriden, lets put a hash
//this will insure that amount is not rigged
if ( $btn_hash ) {
	$fields['btn_hash'] = array(
		'type'  => 'hidden',
		'name'  => 'btn_hash',
		'label' => '',
		'value' => wp_create_nonce( $btn_hash ),
		'class' => ''
	);
	$fields['custom_amount'] = array(
		'type'  => 'hidden',
		'name'  => 'custom_amount',
		'label' => '',
		'value' => $amt,
		'class' => ''
	);
	$fields['custom_currency'] = array(
		'type'  => 'hidden',
		'name'  => 'custom_currency',
		'label' => '',
		'value' => $currency,
		'class' => ''
	);
}

if(!empty($stripe_cust_id)) {
	// unset($fields['first_name']);
	// unset($fields['last_name']);
	// unset($fields['email']);
	// unset($fields['cc_number']);
	// unset($fields['cc_expmonth']);
	// unset($fields['cc_expyear']);
	// unset($fields['cc_cvc']);
	// unset($fields['cc_fields']);
}

$data['fields']             = $fields;
$data['heading']            = $heading;
$data['hide_button_currency'] = (int) $hide_button_currency;

$data['panel_button_label'] = $panel_btn_label;
if($hide_button_currency == 0) $data['panel_button_label'] .= ' ' . $currency . ' ' . $amt;

$data['button_label']       = $panel_btn_label;;
if($hide_button_currency == 0) $data['button_label'] .= ' ' . $currency . ' ';

$data['form_action']        = $stripethankyou_url;
$data['id']                 = $sku;
$data['logo']               = $logo;
$data['showlogin']          = (bool) $showlogin;

