<div id="<?php echo $_the_id; ?>" class="content-wrapper">
	<div class="row">
		<?php $option_val = $this->GetOption('only_show_content_for_level') ?>
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Only show content for each membership level', 'wishlist-member' ); ?>',
					name  : 'only_show_content_for_level',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip : '<?php _e( 'Commonly referred to as the "Hide/Show" feature.<br><br>All Protected content will be completely hidden from Non-Members when this setting is enabled.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('rss_hide_protected') ?>
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Hide protected posts from public RSS', 'wishlist-member' ); ?>',
					name  : 'rss_hide_protected',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip : '<?php _e( 'All protected posts will be hidden from your RSS Feed if this setting is enabled.<br><br>Note: An excerpt will be displayed in your RSS Feed if this setting is disabled.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('hide_from_search') ?>
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Hide protected content from search results', 'wishlist-member' ); ?>',
					name  : 'hide_from_search',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip : '<?php _e( 'Protected content will be hidden from searches conducted using the interior site search if this setting is enabled.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('protect_after_more') ?>
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Protect all content after the "more" tags', 'wishlist-member' ); ?>',
					name  : 'protect_after_more',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip : '<?php _e( 'Protects all content below a More Tag in a Post if this setting is enabled.<br><br>Non-Members can see the content above the More Tag in a post list but if the Post title or the More Tag is clicked, they will be directed to the Non-Members page.', 'wishlist-member' ); ?>',
					tooltip_size : 'md'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('auto_insert_more') ?>
		<div class="col-md-7">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Automatically protect content by inserting the "more" tag into all posts if the "more" tag is not inserted into any post', 'wishlist-member' ); ?>',
					name  : 'auto_insert_more',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch wlm_toggle-adjacent',
					type  : 'checkbox',
					tooltip : '<?php _e( 'A More Tag can be automatically inserted into all Posts and the number of words to display before the inserted More Tag can be set.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
		<div class="col-md-5"></div>
		<div class="col-sm-8 col-md-5 col-xxl-4 col-xxxl-3 offset-md-1 mb-2" <?php echo $option_val == 1 ? '' : 'style="display:none"'; ?>>
			<?php $option_val = $this->GetOption('auto_insert_more_at') ?>
			<template class="wlm3-form-group">
				{
					name  : 'auto_insert_more_at',
					value : '<?php echo $option_val+0; ?>',
					addon_left : 'Insert the "more" tag after',
					addon_right : 'words',
					group_class : 'no-margin',
					'data-initial' : '<?php echo $option_val +0; ?>',
					class : 'text-center auto-insert-more-at',
					size : '3',
				}
			</template>
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('exclude_pages') ?>
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Hide after login page and after registration page of each level', 'wishlist-member' ); ?>',
					name  : 'exclude_pages',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip : '<?php _e( 'Any pages set as an After Registration or After Login page will be hidden from site navigation if this setting is enabled.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('default_protect') ?>
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Automatically Protect New Content', 'wishlist-member' ); ?>',
					name  : 'default_protect',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip : '<?php _e( 'All newly created Posts and Pages will automatically be protected if this setting is enabled.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
</div>