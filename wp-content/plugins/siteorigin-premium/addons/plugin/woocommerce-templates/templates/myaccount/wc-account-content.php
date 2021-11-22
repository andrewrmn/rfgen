<?php

class SiteOrigin_Premium_WooCommerce_Account_Content extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'so-wc-account-content',
			__( 'Account content', 'siteorigin-premium' ),
			array( 'description' => __( 'Display the account content.', 'siteorigin-premium' ) ),
			array()
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		?>
		<div class="woocommerce-MyAccount-content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
		<?php
		echo $args['after_widget'];
	}
}

register_widget( 'SiteOrigin_Premium_WooCommerce_Account_Content' );

