<?php

require_once dirname(__FILE__) . '/../lib/integration.shoppingcart.twoco-api.php';

class WishListtwoco_apiIntegrationInit {
	private $forms;

	public function __construct() {

	}

}

class WLM_TwoCo_Api_ShortCodes {
	protected $folder = 'wlm_twoco_api';
	public function __construct() {
		add_shortcode('wlm_twoco_api_btn', array($this, 'wlm_twoco_api_btn'));
		
		//hook after the regform resources are already loaded
		add_action('wp_footer', array($this, 'footer'));
		
		// add shortcodes to shortcodes manifest
		/**
		 * Add integration shortcodes to shortcodes manifest
		 * @param  array $shortcodes integration shortcodes manifest
		 * @return array filtered shortcodes manifest
		 */
		add_filter( 'wishlistmember_integration_shortcodes', function( $shortcodes ) {
			$levels = wishlistmember_instance()->GetOption( 'wpm_levels' );
			$str = __( ' Registration Button', 'wishlist-member' );
			$codes = array();
			foreach( $levels as $i => $l ) {
				$codes[sprintf( 'wlm_twoco_api_btn sku="%s"', $i )] = array( 'label' => $l['name'] . $str );
			}
			if( $codes ) {
				$shortcodes['2Checkout Payment API Integration'] = $codes;
			}
			return $shortcodes;
		} );
	}
	public function get_view_path($handle) {
		global $WishListMemberInstance;
		return sprintf($WishListMemberInstance->pluginDir .'/extlib/'.$this->folder.'/%s.php', $handle);
	}
	public function profile_form($user) {
		$user_id = $user;
		if(is_object($user)) {
			$user_id = $user->ID;
		}

		global $WishListMemberInstance;
		global $pagenow;
		if($pagenow == 'profile.php' || $pagenow == 'user-edit.php') {
			$stripe_cust_id = $WishListMemberInstance->Get_UserMeta($user_id, 'stripe_cust_id');
			include $this->get_view_path('stripe_user_profile');
		}
	}
	public function update_profile($user) {
		$user_id = $user;
		if(is_object($user)) {
			$user_id = $user->ID;
		}
		if(current_user_can('manage_options')) {
			global $WishListMemberInstance;
			if(isset($_POST['stripe_cust_id'])) {
				$WishListMemberInstance->Update_UserMeta($user_id, 'stripe_cust_id', trim($_POST['stripe_cust_id']));
			}
		}
	}
	public function notices() {
		if(extension_loaded('curl')) {
			return;
		}

		if($_GET['page'] == 'WishListMember' && $_GET['wl'] =='integration') {
			$msg = '<div class="error fade"><p>';
			$msg .= __('<strong>WishList Member Notice:</strong> The <strong>Stripe</strong> integration will not work properly. Please enable <strong>Curl</strong>.', 'wishlist-member');
			$msg .= '</p></div>';
			echo $msg;
		}
	}

	public function load_popup() {
		global $WishListMemberInstance;
		wp_enqueue_script( 'wlm-jquery-fancybox' );
		wp_enqueue_style( 'wlm-jquery-fancybox' );
		wp_enqueue_script( 'wlm-popup-regform' );
		wp_enqueue_style( 'wlm-popup-regform-style' );
	
		global $WishListMemberInstance;
		$twocheckoutapisettings    = $WishListMemberInstance->GetOption('twocheckoutapisettings');
		//Check if Sandbox is enabled
		if($twocheckoutapisettings['twocheckoutapi_sandbox'])
			$twoco_url = 'sandbox.2checkout.com';
		else
			$twoco_url = 'www.2checkout.com';
		
		wp_enqueue_script('wlm-api-2co-min', 'https://'.$twoco_url.'/checkout/api/2co.min.js', array(), $WishListMemberInstance->Version, true);
		wp_enqueue_script('wlm-api-2co-publickey', 'https://'.$twoco_url.'/checkout/api/script/publickey/'.$twocheckoutapisettings['twocheckoutapi_publishable_key'].'', array(), $WishListMemberInstance->Version, true);
	}
	public function wlm_twoco_api_btn($atts, $content) {
		ob_start();
		
		global $WishListMemberInstance;
		global $current_user;
		

		global $current_user;
		$class = empty($regform_cust_id)? 'regform-form' : null;

		$regform_cust_id = 0;

		global $WishListMemberInstance;
		$twocheckoutapisettings    = $WishListMemberInstance->GetOption('twocheckoutapisettings');

	
		$this->load_popup();
		extract(shortcode_atts(array(
					'sku' => null,
						), $atts));

		if (empty($sku)) {
			return null;
		}

		$wpm_levels               = $WishListMemberInstance->GetOption('wpm_levels');
		$twoco_apisettings             = $WishListMemberInstance->GetOption('twocheckoutapisettings');
		$twoco_apisettings['skip_cvc'] = true;
		extract($twoco_apisettings);


		$ppp_level    = $WishListMemberInstance->IsPPPLevel($sku);
		$level_name   = $wpm_levels[$sku]['name'];

		if($ppp_level) {
			$level_name = $ppp_level->post_title;
		}



		$btn_label       = empty($buttonlabel) ? "Join %level" : $buttonlabel;
		$btn_label       = str_replace('%level', $level_name, $btn_label);
		$panel_btn_label = empty($twoco_apisettings['panelbuttonlabel']) ? "Pay" : $twoco_apisettings['panelbuttonlabel'];
		$panel_btn_label = str_replace('%level', $level_name, $panel_btn_label);
		$settings        = $connections[$sku];
		$amt             = $settings['rebill_init_amount'];
		$currency        = empty($twoco_apisettings['currency'])? 'USD' : $twoco_apisettings['currency'];
		$thankyouurl     = $WishListMemberInstance->make_thankyou_url( $WishListMemberInstance->GetOption('twocheckoutapithankyouurl') );
		?>
		<?php if (empty($content)) : ?>
			<button class="regform-button go-regform" style="width: auto" id="go-regform-<?php echo $sku ?>" class="" href="#regform-<?php echo $sku ?>"><?php echo $btn_label ?></button>
		<?php else: ?>
			<a id="go-regform-<?php echo $sku ?>" class="go-regform" href="#regform-<?php echo $sku ?>"><?php echo $content ?></a>
		<?php endif; ?>

		<input type="hidden" id="hiddensku" value="">	
			
		<?php
			//retrieve fields
			global $current_user;
			$path = sprintf($WishListMemberInstance->pluginDir .'/extlib/'.$this->folder.'/form_new_fields.php');
			include $path;

			$data['sc_details'] = array(
				'sku'             => $atts['sku'],
			);

			$this->forms[$sku] = wlm_build_payment_form($data);
		//include $this->get_view_path('form_css');
		?>

		<?php
		$btn = ob_get_clean();
		return $btn;
	}

	public function footer() {

		global $current_user;
		global $WishListMemberInstance;

		$twocheckoutapisettings       = $WishListMemberInstance->GetOption('twocheckoutapisettings');
		$private_key = trim( $twocheckoutapisettings['twocheckoutapi_private_key'] );
		$seller_id = trim( $twocheckoutapisettings['twocheckoutapi_seller_id'] );
		$publishable_key = trim( $twocheckoutapisettings['twocheckoutapi_publishable_key'] );
		$panel_btn_label = empty($twocheckoutapisettings['panelbuttonlabel']) ? "Pay" : $twocheckoutapisettings['panelbuttonlabel'];

		$waiting_text = __("Waiting", "wishlist-member");
		$unauthorized_text = __("Please check your 2Checkout API's settings in WishList Member", "wishlist-member");

		if ( isset($this->forms) && !empty( $this->forms ) && is_array( $this->forms ) ) {
			foreach((array) $this->forms as $f) {
				echo $f;
			}
		}
?>
<script type="text/javascript">
jQuery(function($) {
<?php
	if(!empty($this->forms) && is_array($this->forms)) {
		$skus = array_keys($this->forms);
		foreach($skus as $sku) {

			echo <<<str
            var successCallback$sku = function(data) {
                var myForm = document.getElementById('regform-form-$sku');
               
                // Set the token as the value for the token input
                myForm.token.value = data.response.token.token;
               
                // IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
                myForm.submit();
            };

            // Called when token creation fails.
            var errorCallback$sku = function(data) {
            	console.log('errorcallback');
                if (data.errorCode === 200) {
                    tokenRequest();
                } else {
                     console.log(data);
                     console.log(data.errorMsg);
                     alert(data.errorMsg + ": $unauthorized_text");
                     $("#regform-form-$sku").find(":submit").text('$panel_btn_label').prop('disabled', false);;
                }
            };

            var tokenRequest$sku = function() {

                // Setup token request arguments
                var form$sku = document.querySelector("#regform-form-$sku");
                var args$sku = {
                    sellerId: "$seller_id",
                    publishableKey: "$publishable_key",
                    ccNo: form$sku.elements.cc_number.value,
                    cvv: form$sku.elements.cc_cvc.value,
                    expMonth:form$sku.elements.cc_expmonth.value,
                    expYear: form$sku.elements.cc_expyear.value
                };

                // Make the token request
                TCO.requestToken(successCallback$sku, errorCallback$sku, args$sku);
            };

            $(function() {
                // Pull in the public encryption key for our environment
                TCO.loadPubKey('$publishable_key');

                $("#regform-form-$sku").submit(function(e) {

                    // Disable and set the submit button's text to processing.
                    $(this).find(":submit").text('$waiting_text'+'..').prop('disabled', true);;

                    // Call our token request function
                    tokenRequest$sku();

                    // Prevent form from submitting
                    return false;
                });
            });

str;

			if(is_user_logged_in()) {
				echo sprintf("
						$('#regform-%s .regform-form').PopupRegForm({
						validate_first_name: false,
						validate_last_name: false,
						validate_email: false
						});", $sku);
			} else {
				echo sprintf("$('#regform-%s .regform-form').PopupRegForm({});", $sku);
			}
		}
	}
?>
});
</script>
<?php
	}

}



$sc = new WLM_TwoCo_Api_ShortCodes();
$twoco_api_init = new WishListtwoco_apiIntegrationInit();
?>
