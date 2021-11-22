<?php
	$pages = get_pages('exclude=' . implode(',', $this->ExcludePages(array(), true)));
	foreach($pages AS &$page) {
		$page = array(
			'value' => $page->ID,
			'text' => $page->post_title,
		);
	}
	array_unshift($pages, array('value' => 0, 'text' => '- None -'));
	unset($page);
	$pages = json_encode($pages);
?>
<div class="content-wrapper data-privacy">
	<div class="row">
		<div class="col-md-12"><h3 class="main-title"><?php _e ('Terms of Service Agreement', 'wishlist-member'); ?></h3></div>
	</div>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Require Terms of Service Agreement on Registration Form', 'wishlist-member' ); ?>',
				name : 'privacy_require_tos_on_registration',
				type : 'toggle-adjacent-disable',
				value : 1,
				uncheck_value : 0,
				checked_value : '<?php echo $x = $this->GetOption( 'privacy_require_tos_on_registration' ); ?>',
				column: 'col-md-6'
			}
		</template>
		<div class="col-md-6">
			<button data-toggle="modal" data-target="#require-tos-on-regform-modal" class="btn -primary -condensed <?php echo !$x ? '-disable' : '';?>"  <?php echo !$x ? 'disabled' : '';?>>
				<i class="wlm-icons">settings</i>
				<span class="text"><?php _e( 'Configure', 'wishlist-member' ); ?></span>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12"><br><h3 class="main-title"><?php _e ('Additional Marketing Consent', 'wishlist-member'); ?></h3></div>
	</div>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Display Consent Checkbox for Additional Marketing on Registration Form', 'wishlist-member' ); ?>',
				name : 'privacy_enable_consent_to_market',
				type : 'toggle-adjacent-disable',
				value : 1,
				uncheck_value : 0,
				checked_value : '<?php echo $x = $this->GetOption( 'privacy_enable_consent_to_market' ); ?>',
				column: 'col-md-6'
			}
		</template>
		<div class="col-md-6">
			<button data-toggle="modal" data-target="#additional-marketing-consent-modal" class="btn -primary -condensed <?php echo !$x ? '-disable' : '';?>"  <?php echo !$x ? 'disabled' : '';?>>
				<i class="wlm-icons">settings</i>
				<span class="text"><?php _e( 'Configure', 'wishlist-member' ); ?></span>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12"><br><h3 class="main-title"><?php _e ('Legal Pages', 'wishlist-member'); ?></h3></div>
	</div>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Display Terms of Service Link on Site Footer', 'wishlist-member' ); ?>',
				name : 'privacy_display_tos_on_footer',
				type : 'toggle-adjacent',
				value : 1,
				uncheck_value : 0,
				checked_value : '<?php echo $x = $this->GetOption( 'privacy_display_tos_on_footer' ); ?>',
				column: 'col-md-6'
			}
		</template>
		<div class="col" <?php echo !$x ? 'style="display:none;"' : ''; ?>>
		<template class="wlm3-form-group">
			{
				group_class : 'mb-0',
				name : 'privacy_tos_page',
				type : 'select',
				options : <?php echo $pages; ?>,
				value : '<?php echo $this->GetOption('privacy_tos_page'); ?>',
				style : 'width: 100%',
				class : 'privacy-page',
			}
		</template>
		</div>
		<div class="col-auto" <?php echo !$x ? 'style="display:none;"' : ''; ?>>
			<a href="#privacy_tos_create_page" data-toggle="collapse" class="btn -success -icon-only" style="margin-bottom: 15px">
				<i class="wlm-icons">add</i>
			</a>
		</div>
	</div>
    <div class="collapse" id="privacy_tos_create_page">
		<div class="row">
			<div class="col-md-6"></div>
			<div class="col">
				<div class="form-group">
					<input type="text" class="form-control create-page" placeholder="Page title" required="required">
				</div>
			</div>
			<div class="col-auto">
				<a href="#" data-input="privacy_tos_page" class="btn -primary -condensed -no-icon create-page-btn" title="Create Page">
					<span><?php _e( 'Create Page', 'wishlist-member' ); ?></span>
				</a>
				<a href="#privacy_tos_create_page" data-toggle="collapse" class="btn -bare -condensed -icon-only" title="Create Page">
					<i class="wlm-icons">close</i>
				</a>						
			</div>
		</div>
	</div>

	<br class="d-sm-block">

	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Display Privacy Policy Link on Site Footer', 'wishlist-member' ); ?>',
				name : 'privacy_display_pp_on_footer',
				type : 'toggle-adjacent',
				value : 1,
				uncheck_value : 0,
				checked_value : '<?php echo $x = $this->GetOption( 'privacy_display_pp_on_footer' ); ?>',
				column: 'col-md-6'
			}
		</template>
		<div class="col" <?php echo !$x ? 'style="display:none;"' : ''; ?>>
		<template class="wlm3-form-group">
			{
				group_class : 'mb-0',
				name : 'privacy_pp_page',
				type : 'select',
				options : <?php echo $pages; ?>,
				value : '<?php echo $this->GetOption('privacy_pp_page'); ?>',
				style : 'width: 100%',
				class : 'privacy-page',
			}
		</template>
		</div>
		<div class="col-auto" <?php echo !$x ? 'style="display:none;"' : ''; ?>>
			<a href="#privacy_pp_create_page" data-toggle="collapse" class="btn -success -icon-only" style="margin-bottom: 15px">
				<i class="wlm-icons">add</i>
			</a>
		</div>
	</div>
	<div class="collapse" id="privacy_pp_create_page">
		<div class="row">
			<div class="col-md-6"></div>
			<div class="col">
				<div class="form-group">
					<input type="text" class="form-control create-page" placeholder="Page title" required="required">
				</div>
			</div>
			<div class="col-auto">
				<a href="#" data-input="privacy_pp_page" class="btn -primary -condensed -no-icon create-page-btn" title="Create Page">
					<span><?php _e( 'Create Page', 'wishlist-member' ); ?></span>
				</a>
				<a href="#privacy_pp_create_page" data-toggle="collapse" class="btn -bare -condensed -icon-only" title="Create Page">
					<i class="wlm-icons">close</i>
				</a>						
			</div>
		</div>
	</div>
<?php
$modal_footer = <<<STRING
	<button class="btn -bare modal-cancel save-button">
		<span>Close</span>
	</button>
	<button class="modal-save-and-continue save-button btn -primary">
		<i class="wlm-icons">save</i>
		<span>Save</span>
	</button>
	&nbsp;
	<button class="modal-save-and-close save-button btn -success">
		<i class="wlm-icons">save</i>
		<span>Save &amp; Close</span>
	</button>
STRING;
	include_once 'settings/modal/additional_marketing_consent.php';
	include_once 'settings/modal/require_tos_on_reg_form.php';
?>
</div>
