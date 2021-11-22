<?php

/**
 * Autoloads classes namespaced under WishListMember
 *
 * Translates:
 * - WishListMember\Some_Class_Name to classes/some-class-name.php
 * - WishListMember\SubNameSpace\Some_Class_Namne to classes/subnamespace/subclass.php
 */
spl_autoload_register(
	function( $class ) {
		// for classes in /classes folder 
		if ( preg_match( '/^WishListMember\\\(.+)/i', $class, $match ) ) {
			$file = dirname( __DIR__ ) . '/classes/' . str_replace( array( '_', '\\' ), array( '-', '/' ), strtolower( $match[1] ) ) . '.php';
			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}

		// for autoresponder classes
		if ( preg_match( '/^WishListMember\\\Autoresponders\\\(.+)/i', $class, $match ) ) {
			$file = dirname( __DIR__ ) . '/integrations/emails/' . str_replace( array( '_', '\\' ), array( '-', '/' ), strtolower( $match[1] ) ) . '/handler.php';
			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	}
);
