<div id="levels-create" style="display:none;" class="show-saving">
	<form id="level-form">
		<input type="hidden" id="first-save">
		<div id="save-action-fields">
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save_membership_level" />
			<input type="hidden" name="id">
		</div>
 		<div class="page-header">
			<div class="large-form">
				<div class="row">
					<div class="col-sm-auto col-md-auto col-lg-auto">
						<h2 class="page-title"><?php _e( 'Level Name','wishlist-member' ); ?></h2>
					</div>
					<div class="col-sm-5 col-md-6 col-lg-6 level-name-holder">
						<input name="name" placeholder="Enter Level Name" data-initial="" required="required" class="form-control input-lg" type="text">
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="all-level-data">
			<div class="col-md-12">
				<!-- Nav tabs -->
				<!-- start: v4 -->
				<ul class="nav nav-tabs responsive-tabs -no-background levels-edit-tabs" role="tablist">
				<!-- end: v4 -->
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_access" role="tab" data-toggle="tab"><?php _e( 'Access','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_registrations" role="tab" data-toggle="tab"><?php _e( 'Registrations','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_requirements" role="tab" data-toggle="tab"><?php _e( 'Requirements','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_additional_settings" role="tab" data-toggle="tab"><?php _e( 'Additional Settings','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_notifications" role="tab" data-toggle="tab"><?php _e( 'Email Notifications','wishlist-member' ); ?></a></li>
					<?php if ( count( $wpm_levels ) > 1 ) : ?>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_actions" role="tab" data-toggle="tab"><?php _e( 'Actions','wishlist-member' ); ?></a></li>
					<?php endif; ?>
					<?php foreach( $level_edit_tabs AS $tab_key => $tab_label ) : ?>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_<?php echo $tab_key; ?>" role="tab" data-toggle="tab"><?php echo $tab_label; ?></a></li>
					<?php endforeach; ?>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content">
					<?php
						$level_id = wlm_arrval( $_GET, 'level_id' );
						$level_data = ( new \WishListMember\Level( $level_id ) )->get_data();
						// tab panes
						include_once 'edit/access.php';
						include_once 'edit/registrations.php';
						include_once 'edit/requirements.php';
						include_once 'edit/additional_settings.php';
						include_once 'edit/notifications.php';
						include_once 'edit/actions.php';
						include_once 'edit/hidden.php';
					?>
					<?php foreach( $level_edit_tabs AS $tab_key => $tab_label ) : ?>
						<div role="tabpanel" class="tab-pane extra-tabs" id="" data-id="levels_<?php echo $tab_key; ?>">
							<div class="content-wrapper">
								<?php do_action( 'wishlistmember_level_edit_tab_pane_' . $tab_key, $level_id, $level_data ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
			// per level modals
			include_once 'edit/modal/header_footer.php';
			include_once 'edit/modal/autocreate_account.php';
			include_once 'edit/modal/email_notifications.php';
			include_once 'edit/modal/terms_and_conditions.php';
			include_once 'edit/modal/custom_redirects.php';
			include_once 'edit/modal/level_actions.php';
		?>
	</form>
</div>
<?php
	// global modals
	include_once 'edit/modal/recaptcha.php';
?>