<?php

add_action( 'wishlistmember3_autoresponder_subscribe', array( '\WishListMember\Autoresponders\iContact', 'subscribe' ), 10, 2 );
add_action( 'wishlistmember3_autoresponder_unsubscribe', array( '\WishListMember\Autoresponders\iContact', 'unsubscribe' ), 10, 2 );
