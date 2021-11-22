<?php // initialization

if ( ! class_exists( 'WLM3_PayPalPayflow_Hooks' ) ) {
	class WLM3_PayPalPayflow_Hooks {
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;

			add_action( 'wp_ajax_payflow_delete_product', array( $this, 'delete_product' ) );
		}

		function delete_product() {
			while(isset($_SESSION[__FUNCTION__])) {
				sleep(1);
			}
			$_SESSION[__FUNCTION__] = 1;
			$products = $this->wlm->GetOption('paypalpayflowproducts');
			unset($products[wlm_arrval($_POST, 'id')]);
			$this->wlm->SaveOption('paypalpayflowproducts', $products);
			unset($_SESSION[__FUNCTION__]);
			wp_send_json(array('success' => true, 'products' => $products));
		}
	}

	new WLM3_PayPalPayflow_Hooks;
}