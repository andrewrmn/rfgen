<?php

/*
 * Authorize.net Shopping Cart Integration Functions
 * Original Author : Peter Indiola
 * Version: $Id: integration.shoppingcart.authorizenet.php 5606 2019-01-31 17:52:20Z mike $
 */

//$__classname__ = 'WLM_INTEGRATION_AuthorizeNet';
//$__optionname__ = 'anthankyou';
//$__methodname__ = 'AuthorizeNet';

if (!class_exists('WLM_INTEGRATION_AuthorizeNet')) {

	class WLM_INTEGRATION_AuthorizeNet {

		function AuthorizeNet($that) {

			require_once($that->pluginDir . '/extlib/anet_sdk/AuthorizeNet.php');
			define("AUTHORIZENET_API_LOGIN_ID", $that->GetOption('anloginid'));
			define("AUTHORIZENET_TRANSACTION_KEY", $that->GetOption('antransid'));
			define("AUTHORIZENET_SIGNATURE_KEY", $that->GetOption('anmd5hash'));			
                        $anetsandbox = $that->GetOption('anetsandbox');
                        
			$request = new AuthorizeNetTD;
			if ((int)$anetsandbox != 1) 
			   $request->setSandbox(false);			
			$response = $request->getTransactionDetails(wlm_arrval($_GET,'x_trans_id'));

			// Check if transaction response, transaction_id and authCode if present.
			if (!isset($response->xml->transaction->responseCode) || !isset($response->xml->transaction->authCode) ||
					!isset($response->xml->transaction->transId)) {
				return;
			}

			// Check if transaction code is approved.
			if ($response->xml->transaction->responseCode != 1) {
				return;
			}

			foreach ($response->xml->transaction->lineItems->lineItem as $lineItem) {
				$_POST['wpm_id'] = (string) $lineItem->itemId;
			}

			foreach ($response->xml->transaction->billTo as $billTo) {
				$_POST['lastname'] = (string) $billTo->lastName;
				$_POST['firstname'] = (string) $billTo->firstName;
				$_POST['password1'] = $_POST['password2'] = 'sldkfjsdlkfj';
			}

			foreach ($response->xml->transaction->customer as $customer) {
				$_POST['username'] = (string) $customer->email;
				$_POST['email'] = (string) $customer->email;
			}

			$_POST['action'] = 'wpm_register';
			$_POST['sctxnid'] = (string) $response->xml->transaction->transId;

			// Generate hash for checking with authorize.net submitted hash value.
			$hash = (string) $_GET['x_SHA2_Hash'];
			$string = '^' . implode('^', [
				$_REQUEST['x_trans_id'],
				$_REQUEST['x_test_request'],
				$_REQUEST['x_response_code'],
				$_REQUEST['x_auth_code'],
				$_REQUEST['x_cvv2_resp_code'],
				$_REQUEST['x_cavv_response'],
				$_REQUEST['x_avs_code'],
				$_REQUEST['x_method'],
				$_REQUEST['x_account_number'],
				$_REQUEST['x_amount'],
				$_REQUEST['x_company'],
				$_REQUEST['x_first_name'],
				$_REQUEST['x_last_name'],
				$_REQUEST['x_address'],
				$_REQUEST['x_city'],
				$_REQUEST['x_state'],
				$_REQUEST['x_zip'],
				$_REQUEST['x_country'],
				$_REQUEST['x_phone'],
				$_REQUEST['x_fax'],
				$_REQUEST['x_email'],
				$_REQUEST['x_ship_to_company'],
				$_REQUEST['x_ship_to_first_name'],
				$_REQUEST['x_ship_to_last_name'],
				$_REQUEST['x_ship_to_address'],
				$_REQUEST['x_ship_to_city'],
				$_REQUEST['x_ship_to_state'],
				$_REQUEST['x_ship_to_zip'],
				$_REQUEST['x_ship_to_country'],
				$_REQUEST['x_invoice_num'],
			]) . '^';
			$digest = strtoupper( hash_hmac( 'sha512', $string, pack('H*', AUTHORIZENET_SIGNATURE_KEY) ) );

			if ( function_exists('hash_equals') ) {
				$equals = hash_equals( $digest, $hash );
			} else {
			   	$equals = $digest === $hash;
			}

			if( $equals ) {
				$that->ShoppingCartRegistration();
			} else {

				// Check if there's an MD5 hash
				$x_md5_hash = (string) $_GET['x_MD5_Hash'];

				if ( $x_md5_hash ) {

					$amount = (string) $response->xml->transaction->authAmount;
					$transaction_id = (string) $response->xml->transaction->transId;

					$amount = isset($amount) ? $amount : "0.00";

					// Generate hash for checking with authorize.net submitted hash value.
					$generated_hash = strtoupper( md5( AUTHORIZENET_SIGNATURE_KEY . AUTHORIZENET_API_LOGIN_ID . $transaction_id . $amount) );

					// Let's verify is authorize.net and generate hash is valid.
					if ($x_md5_hash === $generated_hash) {
						$that->ShoppingCartRegistration();
					} else {
						$that->ShoppingCartDeactivate();
					}
				} else {
					$that->ShoppingCartDeactivate();
				}
			}
		}

	}

}
?>
