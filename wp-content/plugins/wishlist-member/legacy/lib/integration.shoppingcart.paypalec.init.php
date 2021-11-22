<?php
include_once($this->pluginDir . '/lib/integration.shoppingcart.paypalcommon.php');

class WlmpaypalecInit {
	private $forms;
	private $wlm;
	private $products;

	public function load_popup() {
		global $WishListMemberInstance;
		wp_enqueue_script( 'wlm-jquery-fancybox' );
		wp_enqueue_style( 'wlm-jquery-fancybox' );
		wp_enqueue_script( 'wlm-popup-regform' );
		wp_enqueue_style( 'wlm-popup-regform-style' );

	}
	public function __construct() {
		add_action('admin_init', array($this, 'use_underscore'));
		add_shortcode( 'wlm_paypalec_btn', array($this, 'paypalecbtn'));
		add_action('wp_footer', array($this, 'footer'), 100);

		/**
		 * Add PayPal Checkout shortcode inserter
		 * @uses wlm_paypal_shortcode_buttons
		 * @param array   $shortcodes Integration shortcodes manifest
		 * @return array              Filter shortcodes manifest
		 */
		add_filter( 'wishlistmember_integration_shortcodes', function ( $shortcodes ) {
			return wlm_paypal_shortcode_buttons(
				$shortcodes,
				'wlm_paypalec_btn',
				__( 'PayPal Checkout Integration', 'wishlist-member' ),
				wishlistmember_instance()->GetOption('paypalecproducts'),
				wishlistmember_instance()->GetOption('paypalec_spb')
			);
		} );

		add_action('wp_ajax_wlm_paypalec_new-product', array($this, 'new_product'));
		add_action('wp_ajax_wlm_paypalec_all-products', array($this, 'get_all_products'));
		add_action('wp_ajax_wlm_paypalec_save-product', array($this, 'save_product'));
		add_action('wp_ajax_wlm_paypalec_delete-product', array($this, 'delete_product'));

		global $WishListMemberInstance;

		if(empty($WishListMemberInstance)) {
			return;
		}
		$this->wlm      = $WishListMemberInstance;
		$this->products = $WishListMemberInstance->GetOption('paypalecproducts');
	}
	public function footer() {
		foreach((array) $this->forms as $f) {
			echo $f;
		}
		if(!empty($this->forms) && is_array($this->forms)) :
	?>
		<script type="text/javascript">
		jQuery(function($) {
		<?php
			$skus = array_keys($this->forms);
			foreach($skus as $sku) {
				echo sprintf("$('#regform-%s .regform-form').PopupRegForm();", $sku);
			}
		?>
		});
		</script>
	<?php
		endif;
	}
	public function use_underscore() {
		global $WishListMemberInstance;
		if(is_admin() && isset($_GET['page']) && $_GET['page'] == $WishListMemberInstance->MenuID && isset($_GET['wl']) && $_GET['wl'] == 'integration') {
			wp_enqueue_script('underscore-wlm', $WishListMemberInstance->pluginURL . '/js/underscore-min.js', array('underscore'), $WishListMemberInstance->Version);
		}
	}

	public function paypalecbtn( $atts, $content) {
		global $WishListMemberInstance, $wlm_paypal_buttons;
		static $spb_lib_loaded = false;

		$spb = $WishListMemberInstance->GetOption('paypalec_spb');
		$funding = empty($spb['funding']) ? null : implode(',', $spb['funding']);
		$valid = array(
			'sku'=> null,
			'btn' => 'pp_pay:s',
			'layout' => $spb['layout'],
			'shape' => $spb['shape'],
			'size' => $spb['size'],
			'color' => $spb['color'],
			'funding' => $funding,
		);

		$atts = shortcode_atts( $valid, $atts );

		$sku = $atts['sku'];
		$btn = $atts['btn'];

		$this->load_popup();
		$products   = $WishListMemberInstance->GetOption('paypalecproducts');
		// standard buttons
		$wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');
		$product    = $products[$sku];
		$content    = trim($content);
		$btn        = trim($btn);

		$paypalecthankyou     = $WishListMemberInstance->GetOption('paypalecthankyou');
		$paypalecthankyou_url = $WishListMemberInstance->make_thankyou_url( $paypalecthankyou );
		$paypalec_settings    = $WishListMemberInstance->GetOption('paypalecsettings');

		//adding "t" to make it unique, else it will always tru in prevent duplicate registrations
		$paypalec_purchase_url = sprintf('%s?action=purchase-express&id=%s&t=%s', $paypalecthankyou_url, $sku, time());

		if(!empty($spb['enable'])) {
			$atts['funding'] = explode(',', $atts['funding']);
			// smart payment buttons
			$btn = '';
			if(!$spb_lib_loaded) {
				$btn .= '<script style="text/javascript" src="https://www.paypalobjects.com/api/checkout.min.js"></script>';
				$spb_lib_loaded = true;
			}

			$funding = array('ELV', 'CREDIT', 'CARD');
			$allowed = array_intersect($funding, (array) $atts['funding']);
			$disallowed = array_diff($funding, (array) $atts['funding']);
			foreach($allowed AS &$a) $a = 'paypal.FUNDING.' . $a;
			unset ($a);
			foreach($disallowed AS &$a) $a = 'paypal.FUNDING.' . $a;
			unset ($a);

			$allowed = implode(',', $allowed);
			$disallowed = implode(',', $disallowed);

			$env = empty($paypalec_settings['sandbox_mode']) ? 'production' : 'sandbox';
			$btn .= <<<STRING
<span class="paypalec-smart_payment_button" id="paypal-spbutton-container-{$sku}"></span>
<script type="text/javascript">
paypal.Button.render({
env: '{$env}',
style: { layout: '{$atts['layout']}', size: '{$atts['size']}', shape: '{$atts['shape']}', color: '{$atts['color']}' },
funding: { allowed: [ {$allowed} ], disallowed: [ {$disallowed} ] },
commit: true,
payment: function (data, actions) {
  return new paypal.Promise(function(resolve, reject) {
		jQuery.get('{$paypalec_purchase_url}&spb=1', function(result) {
			if(result.token) {
  			return resolve(result.token);
			} else {
				reject(new Error('Error'));
			}
		});
  });
},
onAuthorize: function (data, actions) { return actions.redirect() }
}, '#paypal-spbutton-container-{$sku}');
</script>			
STRING;
			return $btn;
		}

		// non-smart payment buttons
		if(!$btn) {
			$btn = $content;
		}

		if(!empty($wlm_paypal_buttons[$btn])) {
			$btn = $wlm_paypal_buttons[$btn];
		}

		$imgbtn = false;
		if($btn) {
			if(filter_var($btn, FILTER_VALIDATE_URL)) {
				$btn = sprintf('<img border="0" style="border:none" class="wlm-paypal-button" src="%s">', $btn);
				$imgbtn = true;
			}
		}

		$panel_button_label = 'Pay';
		if($product['recurring']) {
			$amt = $product['init_amount'];
			$amt = $product['init_amount'] . ' for the first ' . $product['recur_billing_frequency'] . ' ' .$product['recur_billing_period'] .'/s';
			$amt .= ' and <br>' . $product['recur_amount'] . ' every ' . $product['recur_billing_frequency'] . ' ' . $product['recur_billing_period'] .'/s' . ' after';
		} else {
			$amt = $product['amount'];
		}

		$settings             = $WishListMemberInstance->GetOption('paypalecthankyou_url');
		if($imgbtn) {
			$btn = sprintf('<a onclick="window.location=\'%s\'" id="paypal-ec-%s" class="wlm-paypal-button">%s</a>', $paypalec_purchase_url, $sku, $btn);
		} else {
			$btn = sprintf('<button onclick="window.location=\'%s\'" id="paypal-ec-%s" class="wlm-paypal-button">%s</button>', $paypalec_purchase_url, $sku, $btn);
		}
		return $btn;
	}

	//ajax methods

	public function delete_product() {
		$id = $_POST['id'];
		unset($this->products[$id]);
		$this->wlm->SaveOption('paypalecproducts', $this->products);
	}
	public function save_product() {

		$id = $_POST['id'];
		$product = $_POST;
		$this->products[$id] = $product;
		$this->wlm->SaveOption('paypalecproducts', $this->products);
		echo json_encode($this->products[$id]);
		die();
	}

	public function get_all_products() {
		$products = $this->products;
		echo json_encode($products);
		die();
	}

	public function new_product() {
		$products = $this->products;
		if(empty($products)) {
			$products = array();
		}

		//create an id for this button
		$id = strtoupper(substr(sha1( microtime()), 1, 10));

		$product = array(
			'id'            => $id,
			'name'          => $_POST['name'] . ' Product',
			'currency'      => 'USD',
			'amount'        => 10,
			'recurring'     => 0,
			'sku'           => $_POST['sku'],
			'checkout_type' => 'express-checkout'
		);

		$this->products[$id] = $product;
		$this->wlm->SaveOption('paypalecproducts', $this->products);

		echo json_encode($product);
		die();
	}
}


$wlm_paypalec_init = new WlmpaypalecInit();

