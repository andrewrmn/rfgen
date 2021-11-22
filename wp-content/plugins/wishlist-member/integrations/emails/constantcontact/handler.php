<?php

namespace WishListMember\Autoresponders;

class ConstantContact {
	static function user_registered( $user_id, $data ) {
		self::added_to_level( $user_id, array( $data['wpm_id'] ) );
	}

	static function added_to_level( $user_id, $level_id ) {
		$level_id = wlm_remove_inactive_levels( $user_id, $level_id );
		self::pre_process( $user_id, $level_id, 'added' );
	}

	static function removed_from_level( $user_id, $level_id ) {
		self::pre_process( $user_id, $level_id, 'removed' );
	}

	static function uncancelled_from_level( $user_id, $levels ) {
		self::pre_process( $user_id, $levels, 'uncancelled' );
	}

	static function cancelled_from_level( $user_id, $levels ) {
		self::pre_process( $user_id, $levels, 'cancelled' );
	}

	static function pre_process( $email_or_id, $levels, $action ) {

		// get email address
		if ( is_numeric( $email_or_id ) ) {
			$userdata = get_userdata( $email_or_id );
		} elseif ( filter_var( $email_or_id, FILTER_VALIDATE_EMAIL ) ) {
			$userdata = get_user_by( 'email', $email_or_id );
		} else {
			return; // email_or_id is neither a valid ID or email address
		}
		if ( ! $userdata ) {
			return;
		}

		// make sure email is not temp
		if ( ! trim( $userdata->user_email ) || preg_match( '/^temp_[0-9a-f]+/i', $userdata->user_email ) ) {
			return;
		}

		// make sure levels is an array
		if ( ! is_array( $levels ) ) {
			$levels = array( $levels );
		}

		foreach ( $levels as $level_id ) {
			self::process( $userdata, $level_id, $action );
		}
	}

	static function process( $userdata, $level_id, $action ) {
		global $WishListMemberInstance;
		static $ar;

		if ( ! $ar ) {
			$ar = ( new \WishListMember\Autoresponder( 'constantcontact' ) )->settings;
		}

		$add    = $ar['list_actions'][ $level_id ][ $action ]['add'] ?: '';
		$remove = $ar['list_actions'][ $level_id ][ $action ]['remove'] ?: '';

		require_once $WishListMemberInstance->pluginDir . '/extlib/ConstantContact.php';

		// $listID     = $ar['ccID'][ $level_id ]; // get the list ID of the Membership Level
		// $ccUnsub    = ( $ar['ccUnsub'][ $level_id ] == 1 ? true : false );
		$ccusername = $ar['ccusername'];
		$ccpassword = $ar['ccpassword'];
		$ccerror    = '';
		if ( $ccusername != '' && $ccpassword != '' ) { // username and password should not be empty
			$new_cc = new \ConstantContact( $ccusername, $ccpassword );

			if ( is_object( $new_cc ) && $new_cc->get_service_description() ) {
				if ( ! is_object( $new_cc ) ) {
					$ccerror = "There's an unknown error that occured. Please contact support.";
				}
				// Otherwise, if there is a response code, deal with the connection error
			} elseif ( is_object( $new_cc ) and isset( $new_cc->http_response_code ) ) {
				$error   = $new_cc->http_get_response_code_error( $new_cc->http_response_code );
				$ccerror = $error;
			}

			// if no error was found, continue the process
			if ( $ccerror == '' ) {
				if ( $remove ) {
					// check if email exist
					$contact = $new_cc->get_contact_by_email( urlencode( $userdata->user_email ) );
					if ( $contact ) { // if email exist unsubscribe it from the list
						$lists = $contact['lists']; // get current contact's list
						$key   = array_search( $remove, $lists );
						if ( $key ) {
							unset( $lists[ $key ] );
						}
						$additional_fields = array(
							'FirstName' => $userdata->first_name,
							'LastName'  => $userdata->last_name,
						);
						$new_cc->update_contact( $contact['id'], $userdata->user_email, $lists, $additional_fields );
					}
				}
				if ( $add ) {
					// check if email exist
					$contact = $new_cc->get_contact_by_email( urlencode( $userdata->user_email ) );
					if ( $contact ) { // if email exist update the contact
						$lists = $contact['lists']; // get current contact's list
						if ( ! in_array( $add, $lists ) ) {
							$lists[] = $add;
						} //add the list id to this contact's lists
						$additional_fields = array(
							'FirstName' => $userdata->first_name,
							'LastName'  => $userdata->last_name,
						);
						$new_cc->update_contact( $contact['id'], $userdata->user_email, $lists, $additional_fields );
					} else {  // else create a new contact
						$lists             = array( $add );
						$additional_fields = array(
							'FirstName' => $userdata->first_name,
							'LastName'  => $userdata->last_name,
						);
						$new_cc->create_contact( $userdata->user_email, $lists, $additional_fields );
					}
				}
			}
		}
	}
}

