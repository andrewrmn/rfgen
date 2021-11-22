<?php

add_action( 'wishlistmember3_autoresponder_subscribe', array( '\WishListMember\Autoresponders\AWeber', 'subscribe' ), 10, 2 );
add_action( 'wishlistmember3_autoresponder_unsubscribe', array( '\WishListMember\Autoresponders\AWeber', 'unsubscribe' ), 10, 2 );
