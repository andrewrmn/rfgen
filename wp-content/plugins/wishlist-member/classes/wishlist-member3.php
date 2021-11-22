<?php
if ( ! class_exists( 'WishListMember3' ) ) {
	class WishListMember3 extends WishListMember3_Hooks {
		function __construct( $pluginfile, $sku, $menuid, $title, $link ) {
			$this->access_control = new WishListAcl();
			parent::__construct( $sku, $menuid, $title, $link );
			$this->constructor3( $pluginfile, $sku, $menuid, $title, $link );
			$this->hooks_init();
		}
	}
}

