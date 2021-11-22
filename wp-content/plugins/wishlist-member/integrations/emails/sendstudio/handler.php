<?php

namespace WishListMember\Autoresponders;

class SendStudio {

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
		static $ar;

		if ( empty( $ar ) ) {
			$ar = ( new \WishListMember\Autoresponder( 'sendstudio' ) )->settings;
		}

		// get email address
		if ( is_numeric( $email_or_id ) ) {
			$userdata = get_userdata( $email_or_id );
		} elseif ( filter_var( $email_or_id, FILTER_VALIDATE_EMAIL ) ) {
			$userdata = get_user_by( 'email', $email_or_id );
		} else {
			return; // email_or_id is neither a valid ID or email address
		}
		if ( ! $userdata ) {
			return; // invalid user_id
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
			$add    = $ar['list_actions'][ $level_id ][ $action ]['add'] ?: '';
			$remove = $ar['list_actions'][ $level_id ][ $action ]['remove'] ?: '';
			if ( $add ) {
				self::process( $add, $ar, $userdata, false );
			}
			if ( $remove ) {
				self::process( $remove, $ar, $userdata, true );
			}
		}
	}

	static function process( $list_id, $ar, $userdata, $unsubscribe = false ) {
		if ( $list_id ) { // $list_id should not be empty
			$emailAddress = $userdata->user_email;
			$ssPath       = $ar['sspath']; // get the SendStudio XML Path
			$ssUname      = $ar['ssuname']; // get the SendStudio XML Username
			$ssToken      = $ar['sstoken']; // get the SendStudio XML Token
			$ssFnameId    = $ar['ssfnameid']; // get the SendStudio Custom Field First Name ID
			$ssLnameId    = $ar['sslnameid']; // get the SendStudio Custom Field Last Name ID

			if ( $unsubscribe ) { // if the Unsubscribe
				self::ssListUnsubscribe( $ssPath, $ssUname, $ssToken, $list_id, $userdata->user_email );
			} else { // else Subscribe
				self::ssListSubscribe( $ssPath, $ssUname, $ssToken, $ssFnameId, $ssLnameId, $list_id, $userdata->user_email, $userdata->first_name, $userdata->last_name );
			}
		}
	}

	/* Function for Subscribing Members */

	static function ssListSubscribe( $ssPath, $ssUname, $ssToken, $ssFnameId, $ssLnameId, $listID, $emailAddress, $fName, $lName ) {
		/* Prepare the data */
		$xml = '<xmlrequest>
			<username>' . $ssUname . '</username>
			<usertoken>' . $ssToken . '</usertoken>
			<requesttype>subscribers</requesttype>
			<requestmethod>AddSubscriberToList</requestmethod>
			<details>
				<emailaddress>' . $emailAddress . '</emailaddress>
				<mailinglist>' . $listID . '</mailinglist>
				<format>html</format>
				<confirmed>yes</confirmed>
				<customfields>
					<item>
						<fieldid>' . ( $ssFnameId == '' ? '2' : $ssFnameId ) . '</fieldid>
						<value>' . $fName . '</value>
					</item>
					<item>
						<fieldid>' . ( $ssLnameId == '' ? '3' : $ssLnameId ) . '</fieldid>
						<value>' . $lName . '</value>
					</item>
				</customfields>
			</details>
		</xmlrequest>';
		self::SendRequest( $xml, $ssPath );
	}

	/* Function for UnSubscribing Members */

	static function ssListUnsubscribe( $ssPath, $ssUname, $ssToken, $listID, $emailAddress ) {
		/* Prepare the data */
		$xml = '<xmlrequest>
			<username>' . $ssUname . '</username>
			<usertoken>' . $ssToken . '</usertoken>
			<requesttype>subscribers</requesttype>
			<requestmethod>DeleteSubscriber</requestmethod>
			<details>
				<emailaddress>' . $emailAddress . '</emailaddress>
				<list>' . $listID . '</list>
			</details>
		</xmlrequest>';
		self::SendRequest( $xml, $ssPath );
	}

	/* Function for Sending Request */

	static function SendRequest( $xml, $ssPath ) {
		wp_remote_post(
			$ssPath,
			array(
				'blocking'   => false,
				'user-agent' => 'WishList Member/' . WLM3_PLUGIN_VERSION,
				'body'       => $xml,
			)
		);
	}

	/* End of Funtions */
}

