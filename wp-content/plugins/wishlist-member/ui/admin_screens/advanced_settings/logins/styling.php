<?php 
	$login_styling_enable_custom_template = $this->GetOption( 'login_styling_enable_custom_template' );
?>
<div class="page-header -no-background">
	<div class="row align-items-center">
		<div class="col-auto pr-0">
			<div class="large-form">
				<h2 class="page-title"><?php _e( 'WordPress Login Page Styling', 'wishlist-member' ); ?></h2>
			</div>
		</div>
		<div class="col-auto">
			<div class="form-group m-0 d-inline-block">
				<label class="switch-light switch-wlm mt-1">
					<input type="checkbox" value="1" uncheck_value="" name="login_styling_enable_custom_template" class="auto-save" <?php echo $login_styling_enable_custom_template ? 'checked' : ''; ?>>
					<span>
						<span>
							<i class="wlm-icons md-18 ico-check">
							check</i>
						</span>
						<span>
							<i class="wlm-icons md-18 ico-close">
							close</i>
						</span>
						<a>
						</a>
					</span>
				</label>
			</div>
		</div>
		<div class="col">
			<div class="text-right d-none text-muted font-italic">
				<?php _e( 'Active Template:', 'wishlist-member' ); ?>
				<span id="active-template"></span>
			</div>
		</div>

	</div>
</div>
<div class="content-wrapper pt-0">
	<!-- Custom Login Styling Disabled -->
	<div class="custom-styling-status mt-4 <?php echo $login_styling_enable_custom_template ? 'd-none' : ''; ?>">
			<h3><?php _e( 'WordPress Login Page Styling is Disabled', 'wishlist-member' ); ?></h3>
			<br>
			<p><?php _e( 'Activate WordPress login page styling by clicking the toggle button above.', 'wishlist-member' ); ?></p>
	</div>

	<!-- Custom Login Styling Enabled -->
	<div id="custom-styling-section" class="showing-templates-tab custom-styling-status <?php echo $login_styling_enable_custom_template ? '' : 'd-none'; ?>">

		<ul class="nav nav-tabs" id="custom-login-styling-nav-tabs">
			<li class="active nav-item"><a class="nav-link active" data-toggle="tab" href="#templates"><?php _e( 'Templates', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#loginform"><?php _e( 'Login Form', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#background"><?php _e( 'Background', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#css"><?php _e( 'Custom CSS', 'wishlist-member' ); ?></a></li>
			<li class="nav-item ml-auto for-css-tab"><a class="nav-link" href="https://help.wishlistproducts.com/knowledge-base/wp-login-form-custom-styling/" target="_blank"><?php _e( 'Learn More', 'wishlist-member' ); ?></a></li>
		</ul>

		<div id="custom-login-styling-save-area" class="tab-content">
			<div class="tab-pane active" id="templates">
				<?php require_once 'styling/tabs/template.php'; ?>
			</div>
			<div class="tab-pane" id="loginform">
				<?php require_once 'styling/tabs/loginform.php'; ?>
			</div>
			<div class="tab-pane" id="background">
				<?php require_once 'styling/tabs/background.php'; ?>
			</div>
			<div class="tab-pane" id="css">
				<?php require_once 'styling/tabs/css.php'; ?>
			</div>
		</div>

		<div class="panel-footer -content-footer">
			<div class="row">
				<div class="col-md-6 text-left">
					<a href="#" class="btn -default reset-background-btn for-background-tab">
						<i class="wlm-icons">cached</i>
						<span><?php _e( 'Reset Background to Default', 'wishlist-member' ); ?></span>
					</a>
					<a href="#" class="btn -default reset-loginform-btn for-loginform-tab">
						<i class="wlm-icons">cached</i>
						<span><?php _e( 'Reset Login Form to Default', 'wishlist-member' ); ?></span>
					</a>
					<a href="#" class="btn -default reset-css-btn for-css-tab">
						<i class="wlm-icons">cached</i>
						<span><?php _e( 'Reset Custom CSS to Default', 'wishlist-member' ); ?></span>
					</a>
				</div>
				<div class="col-md-6 text-right">
					<a href="#" id="save-custom-login-styling-btn" class="for-background-tab for-loginform-tab for-css-tab btn -primary save-settings">
						<i class="wlm-icons">save</i>
						<span class="text"><?php _e( 'Save', 'wishlist-member' ); ?></span>
					</a>					
					<a href="#" id="save-and-reset-custom-login-styling-btn" class="for-templates-tab btn -primary save-settings">
						<i class="wlm-icons">save</i>
						<span class="text"><?php _e( 'Save', 'wishlist-member' ); ?></span>
					</a>					
				</div>
			</div>
		</div>
	</div>

</div>