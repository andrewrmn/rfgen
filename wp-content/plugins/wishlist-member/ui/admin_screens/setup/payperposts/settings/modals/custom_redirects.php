<?php
	$_redirects = array(
		'afterreg' => __( 'Custom After Registration Redirect for Pay Per Posts', 'wishlist-member' ),
		'login' => __( 'Custom After Login Redirect for Pay Per Posts', 'wishlist-member' ),
	);

?>
<?php foreach($_redirects AS $type => $title) : ?>
<div
	id="custom-redirects-modal-<?php echo $type; ?>"
	data-id="custom-redirects-<?php echo $type; ?>"
	data-label="custom-redirects-<?php echo $type; ?>"
	data-title="<span><?php echo $title; ?></span>"
	data-classes="modal-lg"
	data-show-default-footer=""
	style="display:none">
	<div class="body">
		<div class="-holder">
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save_payperpost_settings" />
			<div class="row">
				<div class="col-md-12">
					<h4 class="mb-3"><?php _e( 'Select one of the following options:', 'wishlist-member' ); ?></h4>
				</div>
				<div class="col-md-2 col-border-right redirect-type-toggle">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Page', 'wishlist-member' ); ?>',
							name: 'payperpost[<?php echo $type; ?>_redirect_type]',
							value : 'page',
							checked_value : <?php echo json_encode( $ppp_settings[ $type . '_redirect_type' ] ); ?>,
							type: 'radio',
							class: 'modal-input -redirect-type',
						}
					</template>
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Message', 'wishlist-member' ); ?>',
							name: 'payperpost[<?php echo $type; ?>_redirect_type]',
							value : 'message',
							checked_value : <?php echo json_encode( $ppp_settings[ $type . '_redirect_type' ] ); ?>,
							type: 'radio',
							class: 'modal-input -redirect-type',
						}
					</template>
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'URL', 'wishlist-member' ); ?>',
							name: 'payperpost[<?php echo $type; ?>_redirect_type]',
							value : 'url',
							checked_value : <?php echo json_encode( $ppp_settings[ $type . '_redirect_type' ] ); ?>,
							type: 'radio',
							class: 'modal-input -redirect-type',
						}
					</template>
				</div>
				<div class="col-md-10">
					<div class="type-page redirect-type" <?php if( wlm_arrval( $ppp_settings, $type . '_redirect_type' ) != 'page' ) echo 'style="display: none;"'; ?>>
						<div class="row">
							<template class="wlm3-form-group">
								{
									name  : 'payperpost[<?php echo $type; ?>_page]',
									value  : <?php echo json_encode( $ppp_settings[$type . '_page'] ); ?>,
									type  : 'select',
									style : 'width:100%',
									options : <?php echo $type; ?>_pages,
									class : 'system-page',
									column: 'col-md-8 col-sm-10',
								}
							</template>
							<div class="col-md-4 col-sm-2">
								<a href="#<?php echo $type; ?>_create_page" data-toggle="collapse" class="btn -success -icon-only" style="margin-bottom: 15px">
									<i class="wlm-icons">add</i>
								</a>
							</div>
						</div>
					    <div class="collapse" id="<?php echo $type; ?>_create_page">
						    <div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<input type="text" class="form-control create-page" placeholder="Page title" required="required">
									</div>
								</div>
								<div class="col-md-4">
									<a href="#" class="btn -primary -condensed -no-icon create-page-btn" title="Create Page">
										<span><?php _e( 'Create Page', 'wishlist-member' ); ?></span>
									</a>
									<a href="#<?php echo $type; ?>_create_page" data-toggle="collapse" class="btn -bare -condensed -icon-only" title="Create Page">
										<i class="wlm-icons">close</i>
									</a>						
								</div>
							</div>
						</div>
					</div>
					<div class="type-message redirect-type" <?php if( wlm_arrval( $ppp_settings, $type . '_redirect_type' ) != 'message' ) echo 'style="display: none;"'; ?>>
						<div class="row">
							<template class="wlm3-form-group">
								{
									name: 'payperpost[<?php echo $type; ?>_message]',
									value: <?php echo json_encode( stripslashes( $ppp_settings[$type . '_message'] ), JSON_UNESCAPED_SLASHES ); ?>,
									type: 'textarea',
									class: 'richtext',
									rows: 10,
									cols: 30,
									column: 'col-md-12',
									group_class : 'mb-2',
									id: 'payperpost-<?php echo $type; ?>-message'
								}
							</template>
							<div class="col-md-12">
								<button class="btn -default -condensed page-message-reset-button" data-target="#payperpost-<?php echo $type; ?>-message" data-type="<?php echo $type; ?>"><?php _e( 'Reset to Default', 'wishlist-member' ); ?></button>
								<?php if($type != 'logout') : ?>
									<template class="wlm3-form-group">
										{
											type : 'select',
											column : 'col-md-5 pull-right no-margin no-padding',
											'data-placeholder' : '<?php _e( 'Insert Merge Codes', 'wishlist-member' ); ?>',
											group_class : 'shortcode_inserter',
											style : 'width: 100%',
											value : -1,
											options : wlm_shortcodes,
											grouped: true,
											class : 'insert_text_at_caret',
											'data-target': '#payperpost-<?php echo $type; ?>-message',
										}
									</template>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="type-url redirect-type" <?php if( wlm_arrval( $ppp_settings, $type . '_redirect_type' ) != 'url' ) echo 'style="display: none;"'; ?>>
						<div class="row">
							<template class="wlm3-form-group">
								{
									name: 'payperpost[<?php echo $type; ?>_url]',
									value: <?php echo json_encode( $ppp_settings[$type . '_url'] ); ?>,
									type: 'text',
									placeholder: '<?php _e( 'Specify the URL', 'wishlist-member' ); ?>',
									column: 'col-md-12',
								}
							</template>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer">
		<a data-toggle="modal" data-target="#custom-redirects-<?php echo $type; ?>" data-btype="cancel" href="#" class="save-button btn -bare">
			<span><?php _e( 'Close', 'wishlist-member' ); ?></span>
		</a>
		<a data-toggle="modal" data-target="#custom-redirects-<?php echo $type; ?>" data-btype="save" href="" class="save-button btn -primary">
			<i class="wlm-icons">save</i>
			<span><?php _e( 'Save', 'wishlist-member' ); ?></span>
		</a>
		&nbsp;
		<a data-toggle="modal" data-target="#custom-redirects-<?php echo $type; ?>" data-btype="save-close" href="" class="save-button btn -success">
			<i class="wlm-icons">save</i>
			<span>Save &amp; Close</span>
		</a>
	</div>
</div>
<?php endforeach; ?>
