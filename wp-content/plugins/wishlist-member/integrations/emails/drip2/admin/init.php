<?php
$wlmapikeys = new \WishListMember\APIKey();
$wlmapikey = $wlmapikeys->get( 'emails/' . $config['id'] ) ?: $wlmapikeys->add( 'emails/' . $config['id'] );

$data = $ar_data[$config['id']];
$data['tags'] = array();
thirdparty_integration_data($config['id'], $data);

