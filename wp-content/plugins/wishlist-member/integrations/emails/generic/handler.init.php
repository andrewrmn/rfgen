<?php

add_action( 'wishlistmember3_autoresponder_subscribe', array( '\WishListMember\Autoresponders\Generic', 'subscribe' ), 10, 2 );
add_action( 'wishlistmember3_autoresponder_unsubscribe', array( '\WishListMember\Autoresponders\Generic', 'unsubscribe' ), 10, 2 );
