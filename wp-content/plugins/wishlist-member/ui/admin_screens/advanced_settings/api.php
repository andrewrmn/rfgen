<div class="page-header">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-8">
			<h2 class="page-title">
				<?php _e( 'API', 'wishlist-member' ); ?>
			</h2>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
	</div>
</div>
<div class="content-wrapper">
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'API URL', 'wishlist-member' ); ?>',
				column : 'col-12 col-md-6',
				value : '<?php echo admin_url(); ?>',
				class : 'copyable',
				readonly : 'readonly',
				tooltip : '<?php _e( 'All WishList Member API requests for this site should be sent to this URL.', 'wishlist-member' ); ?>',
			}
		</template>
	</div>
	<div class="row">
		<?php
			$api_key = $this->GetOption('WLMAPIKey');
			if ( !$api_key ) {
				$api_key = wlm_generate_password( 50, false );
				$this->GetOption('WLMAPIKey', $api_key );
			}
		?>
		<div class="col-md-12">
			<label for="">
				<?php _e( 'API Key', 'wishlist-member' ); ?>
			</label>
			<div class="row">
				<div class="col-12 col-md-6 no-margin">
					<div class="form-group no-margin">
						<div class="input-group -form-tight">
							<input type="text" name="WLMAPIKey" class="form-control api-key-apply" data-initial="<?php echo $api_key; ?>" value="<?php echo $api_key; ?>" />
							<div class="input-group-append">
								<button class="btn -default generate"><?php _e('Generate', 'wishlist-member'); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<br>
			<ul class="list-unstyled">
				<li><?php _e( 'This key is used by developers to access the WishList Member API. It is also used by certain WishList Member integrations.', 'wishlist-member' ); ?></li>
				<li>* <?php _e( 'Please note, if this key is modified any integrations that use the key will need to be updated and reconnected.', 'wishlist-member' ); ?></li>
			</ul>
			<!-- start: v4 -->
			<small class="form-text text-muted">
				<?php _e( 'For documentation and examples visit our site for developers:', 'wishlist-member' ); ?>
				<a href="https://codex.wishlistproducts.com" target="blank">codex.wishlistproducts.com</a>
			</small>
			<!-- end: v4 -->
		</div>
	</div>
</div>