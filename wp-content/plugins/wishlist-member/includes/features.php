<?php

/**
 * Loads WishList Member features
 */
foreach ( glob( dirname( __DIR__ ) . '/features/*.php' ) as $x ) {
	require_once $x;
}

foreach( glob( dirname( __DIR__ ) . '/features/*/main.php' ) AS $x ) {
	require_once $x;
}