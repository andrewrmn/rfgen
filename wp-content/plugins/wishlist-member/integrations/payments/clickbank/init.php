<?php // initialization

if(!class_exists('WLM3_Clickbank_Hooks')) {
	class WLM3_Clickbank_Hooks {
		var $wlm3;
		function __construct() {
			$this->wlm3 = $GLOBALS['WishListMemberInstance'];
			add_action('wp_ajax_wlm3_save_clickbank_product', array($this, 'save_product'));
			add_action('wp_ajax_wlm3_delete_clickbank_product', array($this, 'delete_product'));
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
			$products = $this->__delete_product($old_access, $old_id);

			$products[$access][] = $id;
			$products[$access] = array_diff(array_unique($products[$access]), array('', null, 0));

			$this->wlm3->SaveOption('cbproducts', $products);
			$data['status'] = true;
			$data['message'] = 'Product Saved';
			$data['data']['cbproducts'] = $products;
			wp_send_json($data);
		}

		function delete_product() {
			extract($_POST);
			$data = array(
				'status' => true,
				'message' => '',
				'data' => array(),
			);

			$data['data']['cbproducts'] = $this->__delete_product($access, $id);
			wp_send_json($data);
		}

		private function __delete_product($access, $id) {
			$products = $this->wlm3->GetOption('cbproducts');

			if(!empty($products[$access])) {
				$products[$access] = array_diff($products[$access], array($id));
				$this->wlm3->SaveOption('cbproducts', $products);
			}

			return $products;

		}
	}
	new WLM3_Clickbank_Hooks;
}