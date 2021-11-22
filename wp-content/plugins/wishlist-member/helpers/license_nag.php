<?php
	$xwl = $this->get_screen();
?>
<?php if ( $xwl != 'setup/getting-started' && ! $this->GetOption( 'LicenseKey' ) && ! $this->isURLExempted( strtolower( get_bloginfo( 'url' ) ) ) ) : ?>
<div id="license-nag" class="container-fluid pt-3 <?php if ( $xwl != 'dashboard' ) echo 'pb-3'; ?>">
<div class="row">
	<div class="col-md-12">
		<div class="form-text text-danger help-block mb-0">
			<p><?php _e( 'A valid WishList Member license key is required to qualify for updates and support.', 'wishlist-member' ); ?></p>
			<form>
				<input type="hidden" name="action" value="admin_actions">
				<input type="hidden" name="WishListMemberAction" value="activate_license">
				<div class="form-inline">
					<div class="form-group mb-0">
						<label for="license-key"><?php _e( 'License Key', 'wishlist-member' ); ?></label>
						<input type="text" class="form-control mx-sm-3" id="license-key" size="32" name="licensekey">
					</div>
					<button type="button" class="btn -primary -condensed license-nag-btn">
						<?php _e( 'Activate License', 'wishlist-member' ); ?>
					</button>
				</div>
			</form>
			<p id="license-nag-error" class="text-danger mt-3"></p>
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
$('.license-nag-btn').click(function() {
	var $btn = $(this);
	$btn.disable_button();
	$('#license-nag-error').html('');
	var licensekey = $('#license-key').val().trim();
	if(licensekey === '****************') {
		licensekey = '';
	}
	$.post(
		WLM3VARS.ajaxurl,
		{
			action : 'admin_actions',
			WishListMemberAction : 'activate_license',
			licensekey : licensekey
		},
		function(result) {
			$btn.disable_button({disable:false});				
			result = JSON.parse(result);
			if(!result.success) {
				$('#license-nag-error').html(result.msg);
			} else {
				$('#license-nag').hide();
				$('.wlm-message-holder').show_message({message:result.msg});
			}
		}
	);
});
</script>
<?php endif; ?>
