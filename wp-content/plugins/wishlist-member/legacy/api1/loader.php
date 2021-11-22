<?php

/**
 * This:
 * 1. Autoloads the Legacy WLMAPI when needed
 * 2. Processes remote requests to WLMAPI
 */

// WLMAPI autoloader
spl_autoload_register(
	function( $class ) {
		if ( 'WLMAPI' == $class ) {
			require_once __DIR__ . '/api.php';
		}
	}
);

// catch WLMAPI requests
add_action(
	'init',
	function () {
		/* check for REST API Call */
		if ( isset( $_GET['WLMAPI'] ) ) {
			list($func, $key, $params) = explode( '/', $_GET['WLMAPI'], 3 );
			$params                    = explode( '/', $params );
			foreach ( (array) $params as $k => $v ) { // find arrays.  arrays are specified by separating values with commas
				if ( strpos( $v, ',' ) !== false ) {
					$params[ $k ] = explode( ',', $v );
				}
			}
			echo WLMAPI::__remoteProcess( $func, $key, $params );
			exit;
		}
	}
);
