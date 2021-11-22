<?php // initialization

if(!class_exists('WLM3_WooCommerce_Hooks')) {
	class WLM3_WooCommerce_Hooks {
		var $wlm3;
		function __construct() {
			$this->wlm3 = $GLOBALS['WishListMemberInstance'];
			add_action('wp_ajax_wlm3_save_woocommerce_product', array($this, 'save_product'));
			add_action('wp_ajax_wlm3_delete_woocommerce_product', array($this, 'delete_product'));
		}

		function save_product() {
			extract($_POST);
			$data = array(
				'status' => false,
				'message' => '',
				'data' => array(),
			);

			$id = trim( $id );

			if(empty($id)) {
				$data['message'] = 'Product ID Required';
				wp_send_json($data);
			}
			$products = $this->__delete_product($old_id);

			$products[$id] = array_merge( (array) $products[$id], (array) $access );
			$products[$id] = array_diff(array_unique($products[$id]), array('', null, 0) );

			$this->wlm3->SaveOption('woocommerce_products', array_diff( $products, array('', null, 0) ) );
			$data['status'] = true;
			$data['message'] = 'Product Saved';
			$data['data']['woocommerce_products'] = $products;
			wp_send_json($data);
		}

		function delete_product() {
			extract($_POST);
			$data = array(
				'status' => true,
				'message' => '',
				'data' => array(),
			);

			$data['data']['woocommerce_products'] = $this->__delete_product($id);
			wp_send_json($data);
		}

		private function __delete_product($id) {
			$products = $this->wlm3->GetOption('woocommerce_products');
			unset($products[$id]);
			$this->wlm3->SaveOption('woocommerce_products', $products);
			return $products;
		}
	}
	new WLM3_WooCommerce_Hooks;
}