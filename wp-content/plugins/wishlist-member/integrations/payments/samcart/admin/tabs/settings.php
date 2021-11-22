<form>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Blog URL', 'wishlist-member' ); ?>',
				name : '',
				column : 'col-12 col-md-6',
				help_block : '<?php _e( 'Copy the Blog URL and paste it into SamCart in the following section: <strong>Settings > Integrations > New Integration > WishList Member</strong>.', 'wishlist-member' ); ?>',
				value : '<?php echo admin_url(); ?>',
				readonly : 'readonly',
				class : 'copyable',
			}
		</template>
		<div class="col-12">
			<label for=""><?php _e( 'API Key', 'wishlist-member' ); ?></label>
		</div>
		<template class="wlm3-form-group">
			{
				name : '',
				column : 'col-12 col-md-6',
				help_block : '<?php _e( 'Copy the API Key and paste it into SamCart in the following section: <strong>Settings > Integrations > New Integration > WishList Member</strong>.', 'wishlist-member' ); ?>',
				value : <?php echo json_encode( $wlmapikey ); ?>,
				readonly : 'readonly',
				id : '<?php echo $config['id']; ?>-apikey',
				'data-keyname' : 'payments/<?php echo $config['id']; ?>',
				class : 'copyable',
				tooltip : '<?php _e( 'Note: The API Key can be changed if needed in WishList Member in the following section: Advanced Options > API.', 'wishlist-member' ); ?>',
				tooltip_size : 'md',
				group_class : 'mb-2 mb-md-4'
			}
		</template>
		<div class="col-12 col-md-auto pl-md-0 pb-3 text-right">
			<button type="button" data-action="gen-api-key" data-target="#<?php echo $config['id']; ?>-apikey" name="button" class="btn -default -condensed">Generate New Key</button>
		</div>
	</div>
</form>
