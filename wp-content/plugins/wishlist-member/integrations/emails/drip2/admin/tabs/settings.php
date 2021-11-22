<div class="row">
	<div class="col-auto mb-4"><?php echo $config_button; ?></div>
	<?php echo $api_status_markup; ?>		
</div>
<div class="row api-required">
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'Account', 'wishlist-member' ); ?>',
			type : 'select',
			name : 'account',
			column : 'col-12 col-md-6',
			style : 'width: 100%',
			group_class : 'no-margin'
		}
	</template>

	<div class="col-md-12">
		<hr>
		<h3><?php _e( 'WishList Member API Information', 'wishlist-member' ); ?></h3>
		<br>
	</div>
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'WordPress URL', 'wishlist-member' ); ?>',
			name : '',
			column : 'col-12 col-md-6',
			value : '<?php echo admin_url(); ?>',
			class : 'copyable',
			readonly : 'readonly',
		}
	</template>
	<div class="col-12">
		<label for=""><?php _e( 'API Key', 'wishlist-member' ); ?></label>
	</div>
	<template class="wlm3-form-group">
		{
			name : '',
			column : 'col-12 col-md-6',
			value : <?php echo json_encode( $wlmapikey ); ?>,
			readonly : 'readonly',
			id : '<?php echo $config['id']; ?>-apikey',
			'data-keyname' : 'emails/<?php echo $config['id']; ?>',
			class : 'copyable',
			group_class : 'mb-2 mb-md-4'
		}
	</template>
	<div class="col-12 col-md-auto pl-md-0 pb-3 text-right">
		<button type="button" data-action="gen-api-key" data-target="#<?php echo $config['id']; ?>-apikey" name="button" class="btn -default -condensed">Generate New Key</button>
	</div>

</div>