<?php

$wlmapikeys = new \WishListMember\APIKey();
$wlmapikey = $wlmapikeys->get( 'payments/' . $config['id'] ) ?: $wlmapikeys->add( 'payments/' . $config['id'] );
