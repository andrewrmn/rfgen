<?php
	$wlm_css = $this->GetOption('wlm_css');
	if ( $wlm_css === false ) {
		require($this->legacy_wlm_dir . '/core/InitialValues.php');

		$wlm_css = '';

		// registration form CSS
		$css = $this->GetOption('reg_form_css');
		if($css === false) {
			if ( isset( $WishListMemberInitialData['reg_form_css'] )  ) {
				$wlm_css .= $WishListMemberInitialData['reg_form_css'] ."\n";
			}
		} else {
			$wlm_css .= $css;
		}

		// sidebar widget CSS
		$css = $this->GetOption('sidebar_widget_css');
		if($css === false) {
			if ( isset( $WishListMemberInitialData['sidebar_widget_css'] )  ) {
				$wlm_css .= $WishListMemberInitialData['sidebar_widget_css'] ."\n";
			}
		} else {
			$wlm_css .= $css;
		}

		// login mergecode CSS
		$css = $this->GetOption('login_mergecode_css');
		if($css === false) {
			if ( isset( $WishListMemberInitialData['login_mergecode_css'] )  ) {
				$wlm_css .= $WishListMemberInitialData['login_mergecode_css'] ."\n";
			}
		} else {
			$wlm_css .= $css;
		}
	}
?>
<link rel=stylesheet href="<?php echo $this->pluginURL3 .'/assets/css/' ?>codemirror.css">
<script>
	var codemirror_plugin = "<?php echo $this->pluginURL3; ?>/assets/js/codemirror.js";
	var codemirror_mode_css = "<?php echo $this->pluginURL3; ?>/assets/js/codemirror/mode-css.js";
	var editor = null;
	<?php
		if ( isset( $_POST['reset_custom_css'] ) ) {
			$msg = __("CSS has been reset back to Default", 'wishlist-member');
			echo '$(".wlm-message-holder").show_message({message:' .$msg .'});';
		}
	?>
</script>
<style>
  .CodeMirror { border: 1px solid #ddd; }
  .CodeMirror pre { padding-left: 8px; line-height: 1.25; }
</style>

<div class="page-header">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-8">
			<h2 class="page-title">
				<?php _e( 'Custom CSS', 'wishlist-member' ); ?>
			</h2>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="content-wrapper">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<textarea id="customcss" cols="30" rows="18" class="form-control custom-css" style="height: 300px;"><?php echo $wlm_css; ?></textarea>
						<br>
						<a href="#" class="btn -default reset-btn -condensed">
							<i class="wlm-icons">cached</i>
							<span><?php _e( 'Reset to Default', 'wishlist-member' ); ?></span>
						</a>
					</div>
				</div>
			</div>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
			<div class="panel-footer -content-footer">
				<div class="row">
					<div class="col-lg-12 text-right">
						<a href="#" class="btn -primary save-settings">
							<i class="wlm-icons">save</i>
							<span class="text"><?php _e( 'Save', 'wishlist-member' ); ?></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="reset-modal" data-id="reset-modal" data-label="reset_modal_label" data-title="Reset Custom CSS" data-classes="modal-lg" style="display:none">
	<div class="body">
		<h5 class="message"><?php _e( 'Do you want to reset to Default CSS?', 'wishlist-member' ); ?></h5>
	</div>
	<div class="footer">
		<button type="button" class="btn -bare cancel-button" data-dismiss="modal"><?php _e( 'Cancel', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><span class="text">Yes</span></button>
	</div>
</div>