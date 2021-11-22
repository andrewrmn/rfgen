<?php foreach( $system_page_names AS $system_page ) : ?>
<?php
	$x_type_name = sprintf( '%s_type_%d', $system_page, $post->ID );
	$x_type_value = $this->GetOption( $x_type_name );
	if( empty( $x_type_value ) ) $x_type_value = 'default';
?>
<div id="system-pages-modal-<?php echo $system_page; ?>" style="display: none">
	<div class="system-pages-modal" data-pagetype="<?php echo $system_page; ?>">
		<div class="wlm-modal-content-container clearfix">
			<p><strong>Select one of the following options:</strong></p>
			<div class="wlm-modal-content modal-content-left">
				<ul data-pagetype="<?php echo $system_page; ?>">
					<li>
						<div class="form-check -with-tooltip">
							<label class="cb-container">
								<input class="wlm3_system_page_types" type="radio" name="<?php echo $x_type_name; ?>" value="default" <?php echo 'default' == $x_type_value ? 'checked="checked"' : ''; ?>>
								<span class="btn-radio"></span>
								<span class="text-content"><?php _e( 'Default', 'wishlist-member' ); ?> <a class="wlm-help-icon" href=""><img src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-help-24px.svg" alt=""></a></span>
							</label>
						</div>
					</li>
					<li>
						<div class="form-check -with-tooltip">
							<label class="cb-container">
								<input class="wlm3_system_page_types" type="radio" name="<?php echo $x_type_name; ?>" value="page" <?php echo 'page' == $x_type_value ? 'checked="checked"' : ''; ?>>
								<span class="btn-radio"></span>
								<span class="text-content"><?php _e( 'Page', 'wishlist-member' ); ?> <a class="wlm-help-icon" href=""><img src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-help-24px.svg" alt=""></a></span>
							</label>
						</div>
					</li>
					<li>
						<div class="form-check -with-tooltip">
							<label class="cb-container">
								<input class="wlm3_system_page_types" type="radio" name="<?php echo $x_type_name; ?>" value="message" <?php echo 'message' == $x_type_value ? 'checked="checked"' : ''; ?>>
								<span class="btn-radio"></span>
								<span class="text-content"><?php _e( 'Message', 'wishlist-member' ); ?> <a class="wlm-help-icon" href=""><img src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-help-24px.svg" alt=""></a></span>
							</label>
						</div>
					</li>
					<li>
						<div class="form-check -with-tooltip">
							<label class="cb-container">
								<input class="wlm3_system_page_types" type="radio" name="<?php echo $x_type_name; ?>" value="url" <?php echo 'url' == $x_type_value ? 'checked="checked"' : ''; ?>>
								<span class="btn-radio"></span>
								<span class="text-content"><?php _e( 'URL', 'wishlist-member' ); ?> <a class="wlm-help-icon" href=""><img src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-help-24px.svg" alt=""></a></span>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="wlm-modal-content modal-content-right">
				<div <?php if( $x_type_value != 'default' ) echo 'style="display: none;"'; ?> class="wlm3-system-pages" id="<?php printf( 'wlm3-%s-default', $system_page, $post->ID ); ?>">
					<?php printf( __('Using value from <strong>Advanced Options &gt; Global Defaults &gt; %s</strong> section', 'wishlist-member'), $default_section[$system_page] )	; ?>
				</div>
				<div <?php if( $x_type_value != 'page' ) echo 'style="display: none;"'; ?> class="wlm3-system-pages" id="<?php printf( 'wlm3-%s-page', $system_page, $post->ID ); ?>">
					<div class="wlm-page-select clearfix">
						<div class="wlm-modal-content wlm-page-select-left">
							<select class="form-control wlm-select wlm3-system-page-dropdown" data-placeholder="<?php _e('Select a Page', 'wishlist-member'); ?>" name="<?php echo $x = sprintf( '%s_internal_%d', $system_page, $post->ID ); ?>" id="" style="width: 100%">
								<option></option>
								<?php
									$x = $this->GetOption( $x );
									foreach($page_selections AS $page) {
										$selected = $x == $page->ID ? 'selected="selected"' : '';
										printf( '<option value="%d" %s>%s</option>', $page->ID, $selected, $page->post_title );
									}
								?>
							</select>
						</div>
						<div class="wlm-modal-content wlm-page-select-right">
							<a href="#" class="wlm-btn -icon-only -success wlm3-show-add-page">
								<img style="color: #fff" src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-add-24px.svg" alt="">
							</a>
						</div>
					</div>
					<div class="wlm-page-title clearfix wlm3-create-page" style="display: none">
						<div class="wlm-modal-content wlm-page-title-left">
							<input type="text" class="form-control wlm3-create-systempage-title" value="" placeholder="Page title" data-lpignore="true">
						</div>
						<div class="wlm-modal-content wlm-page-title-right">
							<a href="#" class="wlm3-create-systempage wlm-btn"><?php _e('Create Page', 'wishlist-member'); ?></a>
							<a href="#" class="wlm-btn -bare -icon-only wlm3-show-add-page"><img src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-close-24px-dark.svg" alt=""></a>
						</div>
					</div>
				</div>
				<div <?php if( $x_type_value != 'message' ) echo 'style="display: none;"'; ?> class="wlm3-system-pages" id="<?php printf( 'wlm3-%s-message', $system_page, $post->ID ); ?>">
					<div class="clearfix">
						<?php
							$name = sprintf( '%s_message_%d', $system_page, $post->ID );
							$editor_id = sprintf( '%s_message_mce', $system_page );
							$mce_value = trim( $this->GetOption( $name ) );
							if( empty( $mce_value ) ) {
								$mce_value = $this->page_templates[$system_page . '_internal'];
							}
						?>
						<textarea name="<?php echo $name; ?>" id="<?php echo $editor_id; ?>"><?php echo $mce_value; ?></textarea>
						<br>
					</div>
					<div class="clearfix">
						<div class="wlm-modal-content wlm-message-left">
							<a href="#" class="wlm-btn -default wlm3-reset-message"><?php _e('Reset to Default', 'wishlist-member'); ?></a>
						</div>
						<div class="wlm-modal-content wlm-message-right">
							<select class="form-control wlm-select wlm3-shortcodes" style="width: 100%" data-placeholder="<?php _e('Insert Merge Code', 'wishlist-member'); ?>">
								<option value=""></option>
								<?php if( 'non_members_error_page' == $system_page ) : ?>
								<option value="[loginurl]"><?php _e('Login URL', 'wishlist-member'); ?></option>
								<?php else : ?>
								<optgroup label="<?php _e('Common', 'wishlist-member'); ?>">
									<option value="[firstname]"><?php _e('First Name', 'wishlist-member'); ?></option>
									<option value="[lastname]"><?php _e('Last Name', 'wishlist-member'); ?></option>
									<option value="[email]"><?php _e('Email', 'wishlist-member'); ?></option>
									<option value="[username]"><?php _e('Username', 'wishlist-member'); ?></option>
									<option value="[memberlevel]"><?php _e('Membership Level', 'wishlist-member'); ?></option>
									<option value="[loginurl]"><?php _e('Login URL', 'wishlist-member'); ?></option>
								</optgroup>
								<optgroup label="<?php _e('Other', 'wishlist-member'); ?>">
									<option value="[wlm_website]"><?php _e('Website URL', 'wishlist-member'); ?></option>
									<option value="[wlm_biography]"><?php _e('Biography', 'wishlist-member'); ?></option>
									<option value="[wlm_company]"><?php _e('Company', 'wishlist-member'); ?></option>
									<option value="[wlm_address]"><?php _e('Address', 'wishlist-member'); ?></option>
									<option value="[wlm_address1]"><?php _e('Address 1', 'wishlist-member'); ?></option>
									<option value="[wlm_address2]"><?php _e('Address 2', 'wishlist-member'); ?></option>
									<option value="[wlm_state]"><?php _e('State', 'wishlist-member'); ?></option>
									<option value="[wlm_cty]"><?php _e('City', 'wishlist-member'); ?></option>
									<option value="[wlm_zip]"><?php _e('Zip', 'wishlist-member'); ?></option>
									<option value="[wlm_country]"><?php _e('Country', 'wishlist-member'); ?></option>
								</optgroup>
								<?php endif; ?>
								</select>
						</div>
					</div>
					<br>
				</div>
				<div <?php if( $x_type_value != 'url' ) echo 'style="display: none;"'; ?> class="wlm3-system-pages" id="<?php printf( 'wlm3-%s-url', $system_page, $post->ID ); ?>">
					<input type="text" class="form-control system-page-url" name="<?php echo $x = sprintf( '%s_%d', $system_page, $post->ID ); ?>" value="<?php echo $this->GetOption( $x ); ?>" placeholder="Specify the URL" data-lpignore="true">
				</div>
			</div>
		</div>
		<div class="wlm-modal-footer">
			<div class="" style="float:right; margin-top: 20px">
				<button class="wlm-btn -bare" onclick="tb_remove(); return false;"><?php _e('Close', 'wishlist-member'); ?></button>
				<button class="wlm3-save-system-page wlm-btn -with-icons">
				<i class="wlm-icons"><img class="wlm3-save-icon" src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-save-24px.svg" alt=""><img class="wlm3-save-icon" style="display: none" src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-update-24px.svg" alt=""></i>
				<span>Save</span>
				</button>
				<button class="wlm3-save-system-page -close wlm-btn -with-icons -success">
				<i class="wlm-icons"><img class="wlm3-save-icon" src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-save-24px.svg" alt=""><img class="wlm3-save-icon" style="display: none" src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-update-24px.svg" alt=""></i>
				<span>Save & Close</span>
				</button>
			</div>
		</div>
		<?php include 'modal-overlay.php'; ?>
	</div>
</div>
<?php endforeach; ?>