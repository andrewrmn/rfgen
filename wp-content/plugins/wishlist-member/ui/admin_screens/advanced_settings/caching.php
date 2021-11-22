<div class="page-header">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-8">
			<h2 class="page-title">
				<?php _e( 'Caching', 'wishlist-member' ); ?>
			</h2>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
	</div>
</div>
<div class="content-wrapper">
	<div class="row">
		<?php
			$prefix = $this->GetOption('CookiePrefix');
			$prefix = $prefix ? $prefix : "wlm_";
		?>
		<div class="col-md-12">
			<label for="">
				Cookie Prefix
				<?php $this->tooltip(__('Prepends the specified prefix to cookies set by WishList Member. This can help prevent caching on specific cookies by certain web hosting providers.', 'wishlist-member')); ?>
			</label>
			<div class="row">
				<div class="col-md-3 no-margin">
					<template class="wlm3-form-group">
						{
							name  : 'CookiePrefix',
							value : '<?php echo $prefix; ?>',
							group_class : 'no-margin',
							'data-initial' : '<?php echo $prefix; ?>',
							class : 'cookie-prefix-apply',
						}
					</template>
					<br />
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h4><?php _e( 'Caching Instructions:', 'wishlist-member' ); ?></h4>
			<p><?php _e( 'WishList Member does not do any caching on its own. We utilize the WordPress cache functions.', 'wishlist-member' ); ?></p>
			<p><?php _e( 'If you would like to run a third-party caching plugin on your site, we recommend reading the following help article for more details.', 'wishlist-member' ); ?></p>
			<p><a href="https://help.wishlistproducts.com/knowledge-base/caching/" target="_blank">https://help.wishlistproducts.com/knowledge-base/caching/</a></p>
		</div>
	</div>

</div>




