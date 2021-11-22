<?php

$wlmapikeys = new \WishListMember\APIKey();
$wlmapikey = $wlmapikeys->get( 'others/' . $config['id'] ) ?: $wlmapikeys->add( 'others/' . $config['id'] );
