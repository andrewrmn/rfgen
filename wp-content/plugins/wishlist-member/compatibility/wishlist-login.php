<?php

// Fix for Wishlist Login 2.0's Post Login functionality
add_filter(
	'wishlistmember_process_protection',
	function( $redirect ) {
		global $WishListLogin2Instance;
		if ( isset( $WishListLogin2Instance ) ) {
			if ( $WishListLogin2Instance->show_login() && $WishListLogin2Instance->do_login_box ) {
				return 'STOP';
			}
		}
    return $redirect;
	}
);
