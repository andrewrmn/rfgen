<div class="wlm-inside wlm-inside01">
	<!-- Content Protection Toggle -->
	<div class="form-group">
		<div class="switch-toggle switch-toggle-wlm -semi-compressed">
			<input id="protection-settings-unprotected" name="protection_settings" type="radio" value="0" <?php if($protection_settings == '0') echo 'checked="checked"'; ?>>
			<label for="protection-settings-unprotected" onclick=""><?php _e('Unprotected', 'wishlist-member'); ?></label>
			<input id="protection-settings-protected" name="protection_settings" type="radio" value="1" <?php if($protection_settings == '1') echo 'checked="checked"'; ?>>
			<label for="protection-settings-protected" onclick=""><?php _e('Protected', 'wishlist-member'); ?></label>
			<input id="protection-settings-inherited" name="protection_settings" type="radio" value="2" <?php if($protection_settings == '2') echo 'checked="checked"'; ?>>
			<label for="protection-settings-inherited" onclick=""><?php _e('Inherited', 'wishlist-member'); ?></label>
			<a href="" class="btn btn-primary"></a>
		</div>
	</div>
	<input type="hidden" name="wpm_protect" value="">
	<input type="hidden" name="wlm_inherit_protection" value="">
	<div id="wpm-access-options">
		<hr>
		<!-- Membership Levels -->
		<?php if ( count( $wpm_levels ) ): ?>
		<h2 class="wlm-h2"><?php _e( 'Access', 'wishlist-member' ); ?></h2>
		<?php
		$all_access = array();
		$options = '';
		foreach ( ( array ) $wpm_levels AS $id => $level ) {
			if( wlm_arrval( $level, $allindex ) ) {
				$all_access[] = $level['name'];
				continue;
			}
			$options .= sprintf('<option value="%s" %s>%s</option>', $id, in_array($id, $wpm_access) ? 'selected="selected"' : '', $level['name']);
		}
		if($all_access) {
			printf('<p>%s <strong><em>%s</em></strong></p><br>', sprintf(_n('Level with access to all %s:', 'Levels with access to all %s:', count($all_access), 'wishlist-member'), $allindex == 'allposts' ? 'posts' : 'pages'), implode(', ', $all_access));
		}
		?>
		<div class="form-group" id="wpm-access-form">
			<p class="float-left" for=""><?php _e('Select the membership level(s) that can access this content:', 'wishlist-member'); ?></p>
			<a class="float-right -text-light" id="select-all-levels" href="#"><?php _e('Select All', 'wishlist-member'); ?></a>
			<a class="float-right -text-light" id="clear-all-levels" href="#" style="display: none;"><?php _e('Clear All', 'wishlist-member'); ?></a>
			<div style="clear: both">
				<select name="wpm_access[]" id="" class="form-control wlm-select" style="width: 100%" multiple="multiple"><?php echo $options; ?></select>				
			</div>
			<br>
			<div class="form-group">
				<?php
					$post_type_object = get_post_type_object( $post->post_type );
					$post_type_labels = get_post_type_labels( $post_type_object );
					$parent_name = $post_type_labels->singular_name;
					/**
					 * Filters the name of the child post
					 * @param string name of the child
					 * @param object post type object
					 */
					$child_name = apply_filters( 'wishlistmember_post_inheritance_child_name', 'Content', $post_type_object );
					$pass_content_protection = wishlistmember_instance()->SpecialContentLevel( $post->ID, 'Pass_Content_Protection' ) == 'Y';
				?>
				<p><?php printf( __('Automatically apply Protection Settings to new %s under this %s', 'wishlist-member'), $child_name, $parent_name ); ?></p>
				<div class="switch-toggle switch-toggle-wlm" style="width:140px">
					<input id="pass_content_protection_yes" name="pass_content_protection" type="radio" value="Y" <?php echo $pass_content_protection ? '' : 'checked="checked"'; ?>>
					<label for="pass_content_protection_yes" onclick=""><?php _e('Yes', 'wishlist-member'); ?></label>
					<input id="pass_content_protection_no" name="pass_content_protection" type="radio" value="N" <?php echo $pass_content_protection ? '' : 'checked="checked"'; ?>>
					<label for="pass_content_protection_no" onclick=""><?php _e('No', 'wishlist-member'); ?></label>
					<a href="" class="btn btn-primary"></a>
				</div>
			</div>
			<div id="pass-content-protection">
				<br>
				<p><?php printf( __('Apply Protection Settings to %s under this %s', 'wishlist-member'), $child_name, $parent_name ); ?></p>
				<a href="#" id="pass-protection-to-existing" class="wlm-btn -with-icons">
					<i class="wlm-icons"><img src="<?php echo $this->pluginURL3; ?>/ui/images/settings.svg" alt=""></i>
					<span><?php printf( __( 'Protect Existing Now', 'wishlist-member' ), $child_name ); ?></span>
				</a>
				<span class="notif1" style="display:none; padding-left: 1em;">Applying protection...</span>
				<span class="notif2 wlm-saved" style="display:none; padding-left: 1em;">Applied</span>
				<div id="pass-protection-confirmation" class="wlm-grey-box" style="display: none">
					<p>
						<?php printf( __( 'Are you sure you want to apply Protection Settings to all existing %s under this %s?', 'wishlist-member' ), $child_name, $parent_name ); ?>
						&nbsp;
						<a href="#" id="pass-protection-to-existing-confirm" data-postid="<?php echo $post->ID; ?>" class="wlm-btn">
							<span><?php _e( 'Yes', 'wishlist-member' ); ?></span>
						</a>
						<a href="#" id="pass-protection-to-existing-cancel" class="wlm-btn -default">
							<span><?php _e( 'No', 'wishlist-member' ); ?></span>
						</a>
					</p>
				</div>
			</div>
		</div>
		<div id="wpm-access-inherited">
			<?php if($protected_taxonomies || $ancestor) : ?>
				<p>
					<?php
					_e('Inherited From:', 'wishlist-member');
					$titles = array();
					if($protected_taxonomies) {
						foreach($protected_taxonomies AS $id) {
							$t = get_term( $id );
							$titles[] = $t->name;
						}
					} else {
						foreach($ancestor AS $id) {
							$titles[] = get_the_title( $id );
						}
					}
					echo ' ' . implode(', ', $titles);
					?>
				</p>
				<p><?php _e('Inherited Status:', 'wishlist-member'); ?> <?php echo $parent_protect ? __('Protected', 'wishlist-member') : __('Unprotected', 'wishlist-member'); ?></p>
				<?php if ($parent_protect) : ?>
				<p>
					<?php
					$inherited_levels = array();
					foreach ( ( array ) $parent_levels AS $id ) {
						if($wpm_levels[$id][$allindex]) continue;
						if(empty($wpm_levels[$id])) continue;
						$inherited_levels[] = $wpm_levels[$id]['name'];
					}
					$inherited_levels = array_unique( $inherited_levels );
					echo _n('Inherited Level:', 'Inherited Levels:', count($inherited_levels), 'wishlist-member');
					echo ' ';
					echo count($inherited_levels) ? implode(', ', $inherited_levels) : __('None', 'wishlist-member');
					?>
				</p>
				<?php endif; ?>
			<?php else : ?>
				<p><?php _e('No parent to inherit protection from.', 'wishlist-member'); ?></p>
			<?php endif; ?>
		</div>
		<?php else : ?>
		<p><?php _e('No membership levels found', 'wishlist-member'); ?></p>
		<?php endif; ?>
	</div>
	<br>
	<hr>
	<div style="text-align: right;">
		<div class="wlm-saved" style="display: none"><?php _e('Saved', 'wishlist-member'); ?></div>
		<div class="wlm-saving" style="display: none"><?php _e('Saving...', 'wishlist-member'); ?></div>
		<a href="#" class="wlm-btn -with-icons -success -centered-span wlm-postpage-apply">
			<i class="wlm-icons"><img src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-save-24px.svg" alt=""></i>
			<span><?php _e('Apply Settings', 'wishlist-member'); ?></span>
		</a>
	</div>

</div>