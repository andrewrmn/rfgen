<?php
	$redirect_types = array(
		'afterreg',
		'login',
		'logout',
	);


?>
<div
	id="custom-redirects-modal"
	data-id="custom-redirects"
	data-label="custom-redirects"
	data-title="<span></span>"
	data-classes="modal-lg"
	data-show-default-footer=""
	style="display:none">
	<div class="body">
		<?php foreach($redirect_types AS $type) : ?>
		<div class="<?php echo $type; ?>-redirect -holder" data-type="<?php echo $type; ?>">
			<div class="row">
				<div class="col-md-12">
					<h4 class="mb-3"><?php _e( 'Select one of the following options:', 'wishlist-member' ); ?></h4>
				</div>
				<div class="col-md-2 col-border-right redirect-type-toggle">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Page', 'wishlist-member' ); ?>',
							name: '<?php echo $type; ?>_redirect_type',
							value : 'page',
							type: 'radio',
							class: 'modal-input -redirect-type',
						}
					</template>
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Message', 'wishlist-member' ); ?>',
							name: '<?php echo $type; ?>_redirect_type',
							value : 'message',
							type: 'radio',
							class: 'modal-input -redirect-type',
						}
					</template>
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'URL', 'wishlist-member' ); ?>',
							name: '<?php echo $type; ?>_redirect_type',
							value : 'url',
							type: 'radio',
							class: 'modal-input -redirect-type',
						}
					</template>
				</div>
				<div class="col-md-10">
					<div class="type-page redirect-type">
						<div class="row">
							<template class="wlm3-form-group">
								{
									name  : '<?php echo $type; ?>_page',
									type  : 'select',
									style : 'width:100%',
									options : js_pages,
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
					<div class="type-message redirect-type">
						<div class="row">
							<template class="wlm3-form-group">
								{
									name: '<?php echo $type; ?>_message',
									type: 'textarea',
									class : 'levels-richtext',
									rows: 10,
									cols: 30,
									column: 'col-md-12',
									group_class : 'mb-2',
								}
							</template>
							<div class="col-md-12">
								<button class="btn -default -condensed page-message-reset-button"><?php _e( 'Reset to Default', 'wishlist-member' ); ?></button>
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
											'data-target': '[name=<?php echo $type; ?>_message]',
										}
									</template>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="type-url redirect-type">
						<div class="row">
							<template class="wlm3-form-group">
								{
									name: '<?php echo $type; ?>_url',
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
		<?php endforeach; ?>
	</div>
	<div class="footer">
		<?php echo $modal_footer; ?>
	</div>
</div>
