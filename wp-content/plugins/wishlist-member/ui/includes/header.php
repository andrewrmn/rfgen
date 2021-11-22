<div class="wlm3wrapper wlm3block">
<?php
	do_action('wishlistmember_ui_header_scripts');
	$body_classes = array( 'wlm3body', 'show-saving' );

	if( $this->GetOption( 'wlm3sidebar_state' ) ) {
		$body_classes[] = 'nav-collapsed';
	}

	if( ! $this->GetOption( 'show_legacy_features' ) ) {
		$body_classes[] = 'hide-legacy-features';
	}

	$body_classes = $body_classes ? sprintf( ' class="%s"', implode( ' ', $body_classes ) ) : '';
?>
<div<?php echo $body_classes; ?>>
