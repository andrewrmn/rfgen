<?php

$xproducts = wc_get_products( array( 'limit' => -1 ) );
$products  = array();
foreach ( $xproducts as $product ) {
	$products[ $product->id ] = array(
		'id'     => $product->id,
		'value'  => $product->id,
		'name'   => $product->name,
		'text'   => $product->name,
		'sku'    => $product->sku,
		'status' => $product->status,
	);
}
unset( $xproducts );

// initial values
if ( ! is_array( $this->GetOption( 'woocommerce_settings' ) ) ) {
	$this->AddOption( 'woocommerce_settings', array() );
	$this->AddOption( 'woocommerce_products', array() );
	$this->AddOption( 'woocommerce_eot_cancel', array() );
	$this->AddOption( 'woocommerce_scrcancel', array() );
}

thirdparty_integration_data(
	$config['id'],
	array(
		'woocommerce_settings'   => (array) ( $this->GetOption( 'woocommerce_settings' ) ?: array() ),
		'woocommerce_products'   => (array) ( array_diff( $this->GetOption( 'woocommerce_products' ), array( null, '', false ) ) ?: array() ),
		'woocommerce_eot_cancel' => (array) ( $this->GetOption( 'woocommerce_eot_cancel' ) ?: array() ),
		'woocommerce_scrcancel'  => (array) ( $this->GetOption( 'woocommerce_scrcancel' ) ?: array() ),
		'products'               => $products,
	)
);
