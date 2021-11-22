<div class="page-header">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-8">
			<h2 class="page-title">
				<?php _e( 'Miscellaneous', 'wishlist-member' ); ?>
			</h2>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
	</div>
</div>
<div class="content-wrapper">
	<div class="row">
		<?php $option_val = $this->GetOption('show_linkback') ?>
		<div class="col-md-7">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Show "Powered by WishList Member" Link in Footer', 'wishlist-member' ); ?>',
					name  : 'show_linkback',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip: '<?php _e( 'The "Powered by WishList Member - Membership Software" link will appear in the site footer if this setting is enabled.<br><br>Note: This link can also be attached to a WishList Products Affiliate Account using the Affiliate ID field below.', 'wishlist-member' ); ?>',
					tooltip_size: 'lg'
				}	
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<br />
	<div class="row">
		<div class="col-md-12">
			<label for="">
				Affiliate ID or Link
				<?php $this->tooltip(__('A WishList Products Affiliate ID or Affiliate Link can be added to track sales that originate from clicks of the "Powered by WishList Member - Membership Software" link in the site footer. If you simply add your Affiliate ID, the link will be formatted to go directly to the WishList Member sales page. Alternatively, you can use a link to go to a specific landing page in our system.', 'wishlist-member'), 'lg'); ?>
			</label>
			<div class="row">
				<div class="col-md-6 no-margin">
					<template class="wlm3-form-group">
						{
							name  : 'affiliate_id',
							value : '<?php echo $this->GetOption('affiliate_id'); ?>',
							group_class : 'no-margin',
							'data-initial' : '<?php echo $this->GetOption('affiliate_id'); ?>',
							class : 'affiliateid-apply',
						}
					</template>
					<br />
				</div>
			</div>
			<a href="http://wishlistproducts.com/affiliates/" class="" target="_blank">
				Learn more about the WishList Products Affiliate Program
			</a>
		</div>
	</div>
	<br />
	<div class="row">
		<?php $option_val = $this->GetOption('menu_on_top') ?>
		<div class="col-md-7">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Prioritize WishList Member to the top of the WordPress Menu', 'wishlist-member' ); ?>',
					name  : 'menu_on_top',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip: '<?php _e( 'This feature controls the location of the WishList Member menu in the WordPress menu. After enabling or disabling this feature you will need to refresh your browser window in order for the change to take place.', 'wishlist-member' ); ?>',
					tooltip_size: 'lg'
				}	
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<?php if ( $this->GetOption('show_legacy_features') ) : ?>
		<br />
		<div class="row">
			<?php $option_val = $this->GetOption('send_activation_problem_notice'); ?>
			<div class="col-md-7">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Notify Admin of License Activation Problem', 'wishlist-member' ); ?>',
						name  : 'send_activation_problem_notice',
						value : '1',
						checked_value : '<?php echo $option_val; ?>',
						uncheck_value : '0',
						class : 'wlm_toggle-switch notification-switch',
						type  : 'checkbox',
						tooltip: '<?php _e( 'If YES is selected, an email will be sent to the admin when WishList Member cannot reach the License Activation Server. This email will be sent once every 48 hours for a maximum of 3 times.', 'wishlist-member' ); ?>',
						tooltip_size: 'lg'
					}
				</template>
				<input type="hidden" name="action" value="admin_actions" />
				<input type="hidden" name="WishListMemberAction" value="save" />
			</div>
		</div>
	<?php endif; ?>
</div>