<?php
$user_access      = $wpm_access;
foreach ( $user_access AS $key => $user ) {
	if ( substr( $user, 0, 2 ) != 'U-' ) {
		unset( $user_access[$key] );
	} else {
		$user = get_userdata( substr( $user, 2 ) );
		if ( $user ) {
			$name = trim( $user->user_firstname . ' ' . $user->user_lastname );
			if ( ! $name ) {
				$name = $user->user_login;
			}
			$user_access[$key] = array(
				$user->ID,
				$name,
				$user->user_login,
				$user->user_email
				);
		} else {
			unset( $user_access[$key] );
		}
	}
}
$tbl_display = (count( $user_access ) && $wlm_payperpost) ? '' : 'display:none';
?>
<div class="wlm-inside wlm-inside02" style="display: none;">
	<!-- Pay Per Post Access -->
	<div id="wpm-payperpost-protected">
		<div class="form-group">
			<p><?php _e('Enable Pay Per Post for this content', 'wishlist-member'); ?></p>
			<div class="switch-toggle switch-toggle-wlm" style="width:140px">
				<input id="ppp-yes" name="wlm_payperpost" type="radio" value="Y" <?php if($wlm_payperpost) echo 'checked="checked"'; ?>>
				<label for="ppp-yes" onclick=""><?php _e('Yes', 'wishlist-member'); ?></label>
				<input id="ppp-no" name="wlm_payperpost" type="radio" value="N" <?php if(!$wlm_payperpost) echo 'checked="checked"'; ?>>
				<label for="ppp-no" onclick=""><?php _e('No', 'wishlist-member'); ?></label>
				<a href="" class="btn btn-primary"></a>
			</div>
		</div>
		<div id="wlm_payperpost_enable" style="display:<?php echo $wlm_payperpost ? 'block' : 'none'; ?>">
			<div class="wlm-grey-box">
				<p>
					<?php _e( 'Shopping Cart Integration SKU:', 'wishlist-member' ); ?>
					<strong>payperpost-<?php echo $post->ID; ?></strong>
				</p>
				<p><a href="admin.php?page=WishListMember&wl=setup/integrations/payment_provider" target="_blank"><?php _e( 'Click here for integration instructions', 'wishlist-member' ); ?></a></p>
			</div>
			<br>
			<div class="form-group">
				<p><?php _e('Allow Free Registration for this content', 'wishlist-member'); ?></p>
				<div class="switch-toggle switch-toggle-wlm" style="width:140px">
					<input id="fr-yes" name="wlm_payperpost_free" type="radio" value="Y" <?php if($wlm_payperpost_free) echo 'checked="checked"'; ?>>
					<label for="fr-yes" onclick=""><?php _e('Yes', 'wishlist-member'); ?></label>
					<input id="fr-no" name="wlm_payperpost_free" type="radio" value="N" <?php if(!$wlm_payperpost_free) echo 'checked="checked"'; ?>>
					<label for="fr-no" onclick=""><?php _e('No', 'wishlist-member'); ?></label>
					<a href="" class="btn btn-primary"></a>
				</div>
			</div>
			<div id="wlm_payperpost_free_url" class="wlm-grey-box">
				<div><p><?php _e( 'Free Registration URL:', 'wishlist-member' ); ?></p>
				<?php
				echo WLMREGISTERURL;
				echo '/payperpost/' . $post->ID;
				?>
				</div>
			</div>
			<br>
			<h2 class="wlm-h2"><?php _e('User Access', 'wishlist-member'); ?></h2>
			<p><?php _e('Click the button below to update per user access', 'wishlist-member'); ?></p>
			<a id="wlm3-ppp-modal-button" href="#TB_inline?&inlineId=wlm-ppp-modal&width=750&height=100%" name="<?php _e('Update User Access', 'wishlist-member'); ?>" class="wlm-btn"><?php _e('Update User Access', 'wishlist-member'); ?></a>
			<?php include 'payperpost-modal.php'; ?>
			<br>
			<br>
			<h2 class="wlm-h2"><?php _e('After Login', 'wishlist-member'); ?></h2>
			<div class="form-group">
				<p><?php _e( 'Use this post as After Login Page if a user has access to it', 'wishlist-member' ); ?></p>
				<div class="switch-toggle switch-toggle-wlm" style="width:140px">
					<input id="pppafterlogin-yes" name="wlm_payperpost_afterlogin" type="radio" value="Y" <?php if($wlm_payperpost_afterlogin) echo 'checked="checked"'; ?>>
					<label for="pppafterlogin-yes" onclick=""><?php _e('Yes', 'wishlist-member'); ?></label>
					<input id="pppafterlogin-no" name="wlm_payperpost_afterlogin" type="radio" value="N" <?php if(!$wlm_payperpost_afterlogin) echo 'checked="checked"'; ?>>
					<label for="pppafterlogin-no" onclick=""><?php _e('No', 'wishlist-member'); ?></label>
					<a href="" class="btn btn-primary"></a>
				</div>
			</div>
		</div>
	</div>
	<div id="wpm-payperpost-unprotected">
		<p><?php printf( __( 'Protection is disabled for this %s. Please enable content protection first in order to manage Pay Per Post Access.', 'wishlist-member' ), strtolower( $ptype_object->labels->singular_name ) ); ?></p>
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