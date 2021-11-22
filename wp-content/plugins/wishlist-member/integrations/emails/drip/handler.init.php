<?php

add_action( 'wishlistmember3_autoresponder_subscribe', array( '\WishListMember\Autoresponders\Drip', 'subscribe' ), 10, 2 );
add_action( 'wishlistmember3_autoresponder_unsubscribe', array( '\WishListMember\Autoresponders\Drip', 'unsubscribe' ), 10, 2 );
