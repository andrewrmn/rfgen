<?php

add_action( 'wishlistmember3_autoresponder_subscribe', array( '\WishListMember\Autoresponders\GetResponse', 'subscribe' ), 10, 2 );
add_action( 'wishlistmember3_autoresponder_unsubscribe', array( '\WishListMember\Autoresponders\GetResponse', 'unsubscribe' ), 10, 2 );
