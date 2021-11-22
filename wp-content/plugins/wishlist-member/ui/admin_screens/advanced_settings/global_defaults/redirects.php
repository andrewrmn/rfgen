<?php
	$system_pages_arr = array(
		"error"=>array(
			"title" => "Error Pages",
			"options"=> array(
				"after_registration" => array(
					"label" => "After Registration",
					"tooltip" => "The After Registration Page will be displayed after a successful registration to the site. <br><br>Note: This page will only appear one time for the member immediately after registration.",
					"tooltip_size" => "md",
				),
				"membership_forapproval" => array(
					"label" => "Membership Requires Approval",
					"tooltip" => "This set page will be displayed to a Member when their Membership Level requires approval by the administrator.",
					"tooltip_size" => "md",
				),
				"membership_forconfirmation" => array(
					"label" => "Membership Requires Confirmation",
					"tooltip" => "This set page will be displayed to a Member when their Membership Level has not yet been confirmed.",
					"tooltip_size" => "md",
				),
				"after_login" => array(
					"label" => "After Login",
					"tooltip" => "The After Login Page will be displayed after every successful login by a Member.",
					"tooltip_size" => "",
				),
				"after_logout" => array(
					"label" => "After Logout",
					"tooltip" => "The After Logout Page will be displayed after every successful logout by a Member.",
					"tooltip_size" => "",
				),
				"unsubscribe" => array(
					"label" => "Unsubscribe Confirmation",
					"tooltip" => "When a user unsubscribes from the email broadcast list in WishList Member they will see this message, page or URL.",
					"tooltip_size" => "",
				),
				"resubscribe" => array(
					"label" => "Resubscribe Confirmation",
					"tooltip" => "When a user resubscribes to the email broadcast list in WishList Member they will see this message, page or URL.",
					"tooltip_size" => "",
				),
			)
		),
	);

	$pages = get_pages('exclude=' . implode(',', $this->ExcludePages(array(), true)));
?>
<div class="content-wrapper">
	<?php foreach ($system_pages_arr as $system_pages): ?>
		<?php foreach ($system_pages['options'] as $key => $option ): ?>
			<?php
				$page_type = $this->GetOption( $key .'_type' );
				if ( $page_type === false ) {
					$p = $this->GetOption( $key .'_internal');
					if ( $p ) {
						$page_type = "internal";
					} else {
						$pages_url = $this->GetOption( $key );
						$page_type = $pages_url ? "url" : "text";
					}
				}
			?>
			<div class="row">
				<div class="col-sm-7 col-md-4 col-lg-4 col-xl-3 col-xxl-3">
					<div class="form-group">
						<label>
							<span class="title-label"><?php echo $option["label"] ?></span>
							<?php $this->tooltip( $option["tooltip"], $option["tooltip_size"] ); ?>
						</label>
					</div>
				</div>
				<div class="col-sm-5 col-md-4">
					<a href="#" class="btn -primary configure-btn -condensed" ptype="<?php echo $page_type ?>" key="<?php echo $key ?>" title="<?php echo $option["label"] ?>">
						<i class="wlm-icons">settings</i>
						<span><?php _e( 'Configure', 'wishlist-member' ); ?></span>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endforeach; ?>
</div>

<!-- Modal -->
<div id="configure-pages" data-id="configure-pages" data-label="configure-pages-label" data-title="Configure System Pages" data-classes="modal-lg" style="display:none">
	<div class="body">
		<div class="row settings-content">
			<div class="col-md-12">
				<h4 class="mb-3"><?php _e( 'Select one of the following options:', 'wishlist-member' ); ?></h4>
			</div>
			<div class="col-md-2 mb-3 col-border-right">
				<template class="wlm3-form-group">
				  {
				    label : '<?php _e( 'Page', 'wishlist-member' ); ?>',
				    name  : 'sp',
				    value : 'internal',
				    type  : 'radio',
				    id : 'sp-internal',
				    tooltip : '<?php _e( 'This option can be used in order to select a specific page created in WordPress.', 'wishlist-member' ); ?>'
				  }
				</template>
				<template class="wlm3-form-group">
				  {
				    label : '<?php _e( 'Message', 'wishlist-member' ); ?>',
				    name  : 'sp',
				    value : 'text',
				    type  : 'radio',
				    id : 'sp-text',
				    tooltip : '<?php _e( 'This option can be used in order to specify a message that will automatically be displayed by WishList Member.', 'wishlist-member' ); ?>'
				  }
				</template>
				<template class="wlm3-form-group">
				  {
				    label : '<?php _e( 'URL', 'wishlist-member' ); ?>',
				    name  : 'sp',
				    value : 'url',
				    type  : 'radio',
				    id : 'sp-url',
				    tooltip : '<?php _e( 'This option can be used in order to redirect to a specific URL that may be located or hosted outside of your WordPress site.', 'wishlist-member' ); ?>'
				  }
				</template>
			</div>
			<div class="col-md-10">
				<input type="hidden" name="type" class="system-page-type" value="" />
				<div class="sp-text-content">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group mb-2">
					    		<textarea class="form-control system-page-text" name="text" cols="30" rows="10" placeholder="Your message" required></textarea>
					    	</div>
						</div>
						<div class="col-md-12">
							<button class="btn -default -condensed page-message-reset-button">Reset to Default</button>
							<template class="wlm3-form-group">{
								type : 'select',
								column : 'col-md-5 pull-right no-margin no-padding',
								'data-placeholder' : '<?php _e( 'Insert Merge Codes', 'wishlist-member' ); ?>',
								group_class : 'shortcode_inserter mb-0',
								style : 'width: 100%',
								options : get_merge_codes([{value : '[password]', text : 'Password'}]),
								grouped: true,
								class : 'insert_text_at_caret',
								'data-target' : '.system-page-text',
							}</template>
						</div>
					</div>
				</div>
				<div class="sp-page-content" style="display:none">
					<div class="row">
						<div class="col-md-8">
						  <div class="form-group">
						    <select class="form-control wlm-select wlm-select-pages system-page-internal" name="internal" style="width: 100%" data-placeholder="Select a page">
						      <option></option>
						      <?php foreach ( $pages as $p ): ?>
						        <option value="<?php echo $p->ID; ?>" ><?php echo $p->post_title; ?></option>
						      <?php endforeach; ?>
						    </select>
						  </div>
						</div>
						<div class="col-md-4">
						  <a href="javascript:void(0);" class="btn -success -icon-only add-page-btn" style="margin-bottom: 15px" title="Add a page">
						    <i class="wlm-icons">add</i>
						  </a>
						</div>
					</div>
					<div class="row create-page-holder">
						<div class="col-md-8">
							<div class="form-group">
								<input type="text" class="form-control " name="page_title" value="" placeholder="Page title" required="required" />
							</div>
						</div>
						<div class="col-md-4">
							<a href="javascript:void(0);" class="btn -primary -condensed -no-icon create-page-btn" title="Create Page">
								<span><?php _e( 'Create Page', 'wishlist-member' ); ?></span>
							</a>
							<a href="javascript:void(0);" class="btn -bare -condensed -no-icon hide-create-page-btn" title="Create Page">
								<i class="wlm-icons">close</i>
							</a>
						</div>
					</div>
				</div>
				<div class="sp-url-content" style="display:none">
					<div class="row">
						<div class="col-md-10">
							<div class="form-group ">
								<input type="text" class="form-control system-page-url" name="url" value="" placeholder="Specify the URL" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer">
    <button type="button" class="btn -bare" data-dismiss="modal">
    	<span><?php _e( 'Close', 'wishlist-member' ); ?></span>
    </button>
    <button type="button" class="btn -primary save-button">
    	<i class="wlm-icons">save</i>
    	<span><?php _e( 'Save', 'wishlist-member' ); ?></span>
    </button>
    <button class="-close btn -success -modal-btn save-button">
    	<i class="wlm-icons">save</i>
    	<span><?php _e( 'Save & Close', 'wishlist-member' ); ?></span>
    </button>
	</div>
</div>

