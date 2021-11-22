<?php
	$needs_https = !preg_match( '#^https://#i', home_url() );
?>
<div class="row">
	<?php if( $needs_https ) : ?>
	<div class="col-12">
		<div class="form-text text-danger help-block">
			<p class="mb-0"><strong>WARNING</strong>: <?php _e( 'Zapier requires your site to use an SSL Certificate (ex. https://)', 'wishlist-member' ); ?></p>
		</div>
	</div>
	<?php endif; ?>
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'WishList Member URL', 'wishlist-member' ); ?>',
			value : '<?php echo esc_url( home_url( '/' ) ); ?>',
			column : 'col-md-6',
			readonly : 'readonly',
			class : 'copyable'
		}
	</template>
</div>
<div class="row">
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'WishList Member API Zapier Key', 'wishlist-member' ); ?>',
			name : 'zapier_settings[key]',
			column : 'col-md-6',
			class : 'applycancel'
		}
	</template>
</div>