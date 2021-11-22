<?php // initialization

if ( ! class_exists( 'WLM3_PayPalPro_Hooks' ) ) {
	class WLM3_PayPalPro_Hooks {
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_paypalpro_delete_product', array( $this, 'delete_product' ) );
		}

		function delete_product() {
			while(isset($_SESSION[__FUNCTION__])) {
				sleep(1);
			}
			$_SESSION[__FUNCTION__] = 1;
			$products = $this->wlm->GetOption('paypalproproducts');
			unset($products[wlm_arrval($_POST, 'id')]);
			$this->wlm->SaveOption('paypalproproducts', $products);
			unset($_SESSION[__FUNCTION__]);
			wp_send_json(array('success' => true, 'products' => $products));
		}
	}

	new WLM3_PayPalPro_Hooks;
}