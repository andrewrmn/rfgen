<?php

namespace WishListMember\Autoresponders;

class iContact {
	static function subscribe( $email, $level_id ) {
		self::process( $email, $level_id );
	}

	static function unsubscribe( $email, $level_id ) {
		self::process( $email, $level_id, true );
	}

	static function process( $email, $level_id, $unsub = false ) {
		global $WishListMemberInstance;
		$ar = ( new \WishListMember\Autoresponder( 'icontact' ) )->settings;

		$listID = $ar['icID'][ $level_id ];  // listID used for membership level
		if ( $listID ) {
			list($fName, $lName) = explode( ' ', $WishListMemberInstance->ARSender['name'], 2 );
			$emailAddress        = $WishListMemberInstance->ARSender['email'];
			// retrieve Icontact credentials
			$icUserName    = $ar['icusername'];
			$icAppPassword = $ar['icapipassword'];
			$icAppID       = $ar['icapiid'];
			$icAcctID      = $ar['icaccountid'];
			$icFolderID    = $ar['icfolderid'];
			$iclog         = $ar['iclog'];
			$icID          = $ar['icID'];
			// get client info
			$params = array(
				array(
					'firstName' => $fName,
					'lastName'  => $lName,
					'email'     => $emailAddress,
				),
			);
			if ( ! $unsub ) {
				$contactId = self::addContact( $icUserName, $icAppPassword, $icAppID, $icAcctID, $icFolderID, $params );
				if ( is_numeric( $contactId ) ) {
					if ( is_array( $listID ) ) {
						foreach ( $listID as $list ) {
							$res = self::contactListSubscription( $icUserName, $icAppPassword, $icAppID, $icAcctID, $icFolderID, $contactId, $list, 'normal' );
						}
					} else {
						$res = self::contactListSubscription( $icUserName, $icAppPassword, $icAppID, $icAcctID, $icFolderID, $contactId, $listID, 'normal' );
					}
				}
			} else {
				if ( $iclog[ $level_id ] == 1 && $icID[ $level_id ] != '' ) {
					$date    = date( 'F j, Y, h:i:s A' );
					$logfile = ABSPATH . $icID[ $level_id ] . '.txt';
					if ( file_exists( $logfile ) ) {
							$logfilehandler = fopen( $logfile, 'a' );
					}
					if ( $logfilehandler ) {
						$txt = '[' . $fName . ' ' . $lName . ']: ' . $emailAddress;
						$log = '[' . $date . '] ' . $txt . "\n";
						fwrite( $logfilehandler, $log );
						fclose( $logfilehandler );
					}
				}
			}
		}
	}

	// Add contact to list
	// function addContact($icUserName,$icAppPassword,$icAppID,$icAcctID,$icFolderID,$params){
	static function addContact( $icUserName, $icAppPassword, $icAppID, $icAcctID, $icFolderID, $params ) {
		$contactId    = null;
		$errorMessage = '';
		$response     = self::callResource( $icUserName, $icAppPassword, $icAppID, "/a/{$icAcctID}/c/{$icFolderID}/contacts", 'POST', $params );
		if ( $response['code'] == 200 ) {
			$contactId    = $response['data']['contacts'][0]['contactId'];
			$warningCount = 0;
			if ( ! empty( $response['data']['warnings'] ) ) {
				$warningCount = count( $response['data']['warnings'] );
			}
			if ( $warningCount > 0 ) {
				$errorMessage = "<p>Added contact {$contactId}, with {$warningCount} warnings.</p>\n";
			}
		} else {
			$errorMessage = "<h1>Error - Add Contact {$response['code']}</h1>\n";
		}
		return ( $errorMessage == '' ? $contactId : $errorMessage );
	}

	// After adding the contact you can subscribe it to the list, this is the function to subscribe the user to list
	static function contactListSubscription( $icUserName, $icAppPassword, $icAppID, $icAcctID, $icFolderID, $contactId, $listID, $status ) {
		global $welcomeMessageId;
		$response = self::callResource(
			$icUserName,
			$icAppPassword,
			$icAppID,
			"/a/{$icAcctID}/c/{$icFolderID}/subscriptions",
			'POST',
			array(
				array(
					'contactId' => $contactId,
					'listId'    => $listID,
					'status'    => $status,
				),
			)
		);
		if ( $response['code'] == 200 ) {
			$errorMessage = '';
			$warningCount = 0;
			if ( ! empty( $response['data']['warnings'] ) ) {
				$warningCount = count( $response['data']['warnings'] );
			}
			if ( $warningCount > 0 ) {
				$errorMessage = "<p>Subscribed/Unsubscribe contact {$contactId} to list {$listId}, with {$warningCount} warnings.</p>\n";
			}
		} else {
			$errorMessage  = "<h1>Error - Subscribe Contact to List</h1>\n";
			$errorMessage .= "<p>Error Code: {$response['code']}</p>\n";
		}
		return $errorMessage;
	}

	// This function is used to make request & pull data from Icontact, parameters are Icontact Credentials saved on autoresponder settings page)
	static function callResource( $icUserName, $icAppPassword, $icAppID, $url, $method, $data = null ) {
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'Api-Version: 2.0',
			'Api-AppId: ' . $icAppID,
			'Api-Username: ' . $icUserName,
			'Api-Password: ' . $icAppPassword,
		);
		$apiUrl  = 'https://app.icontact.com/icp';
		$url     = $apiUrl . $url;
		$handle  = curl_init();
		curl_setopt( $handle, CURLOPT_URL, $url );
		curl_setopt( $handle, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false );

		switch ( $method ) {
			case 'POST':
				curl_setopt( $handle, CURLOPT_POST, true );
				curl_setopt( $handle, CURLOPT_POSTFIELDS, json_encode( $data ) );
				break;
			case 'PUT':
				curl_setopt( $handle, CURLOPT_PUT, true );
				$file_handle = fopen( $data, 'r' );
				curl_setopt( $handle, CURLOPT_INFILE, $file_handle );
				break;
			case 'DELETE':
				curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, 'DELETE' );
				break;
		}

		$response = curl_exec( $handle );
		$response = json_decode( $response, true );
		$code     = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
		curl_close( $handle );
		return array(
			'code' => $code,
			'data' => $response,
		);
	}

	/* End of Functions */
}
