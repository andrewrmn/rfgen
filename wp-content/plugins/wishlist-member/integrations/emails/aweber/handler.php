<?php

namespace WishListMember\Autoresponders;

class AWeber {
	static function subscribe( $email, $level_id ) {
		self::process( $email, $level_id, true );
	}

	static function unsubscribe( $email, $level_id ) {
		self::process( $email, $level_id, false );
	}

	static function process( $email, $level_id, $subscribe ) {
		global $WishListMemberInstance;
		$ar = ( new \WishListMember\Autoresponder( 'aweber' ) )->settings;

		$headers = "Content-type: text/plain; charset=us-ascii\r\n";
		if ( $ar['email'][ $level_id ] ) {
			$sendto = $ar['email'][ $level_id ];
			if ( strpos( $sendto, '@' ) === false ) {
				$sendto .= '@aweber.com';
			}
			if ( $subscribe ) {
				$name           = $WishListMemberInstance->ARSender['name'];
				$message        = "{$email}\n{$name}";
				$WishListMemberInstance->ARSender = array(
					'name'  => 'Aweber Subscribe Parser',
					'email' => $WishListMemberInstance->GetOption( 'email_sender_address' ),
				);
				wp_mail( $sendto, 'A New Member has Registered', $message, $headers );
			} else {
				$WishListMemberInstance->ARSender = array(
					'name'  => 'Aweber Remove',
					'email' => $ar['remove'][ $level_id ],
				);
				$subject        = 'REMOVE#' . $email . '#WLMember';
				wp_mail( $sendto, $subject, 'AWEBER UNSUBSCRIBE', $headers );
			}
		}
	}
}
