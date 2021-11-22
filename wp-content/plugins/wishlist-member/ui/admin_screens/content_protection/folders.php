<div class="page-header">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-8">
			<h2 class="page-title pull-left">
				<?php _e( 'Folder Protection', 'wishlist-member' ); ?>
			</h2>
			<div class="pull-left -in-header" style="margin: 0 0 -5px 5px">
				<?php $enable_protection = $this->GetOption('folder_protection') ?>
				<template class="wlm3-form-group">
					{
						name  : 'folder_protection',
						value : '1',
						checked_value : '<?php echo $enable_protection; ?>',
						uncheck_value : '0',
						class : 'wlm_toggle-switch -in-header enable-protection',
						type  : 'checkbox',
					}
				</template>
				<input type="hidden" name="action" value="admin_actions" />
				<input type="hidden" name="WishListMemberAction" value="enable_folder_protection" />
			</div>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
	</div>
</div>
<?php
	if ( !$enable_protection ) {
		_e("Folder Protection is currently disabled.", 'wishlist-member');
		return;
	}
?>
<?php
$content_type = "folders";
$content_comment = false;

$rootOfFolders = trim( $this->GetOption( 'parentFolder' ) );
$folder_protection_full_path = $this->folder_protection_full_path( $rootOfFolders );
// Get Membership Levels
$wpm_levels = $this->GetOption('wpm_levels');
?>
<div class="header-tools -no-border">
	<div class="row">
		<div class="col-sm-8 col-md-4">
			<div class="form-group">
				<label class="sr-only" for=""><?php _e( 'Members Role', 'wishlist-member' ); ?></label>
				<select class="form-control wlm-select blk-actions" name="" id="" style="width: 100%">
					<option value="">- Select an Action -</option>
					<option value="protection">Edit Protection Status</option>
					<option value="add_level">Add Levels</option>
					<option value="remove_level">Remove Levels</option>
				</select>
			</div>
		</div>
		<div class="col-sm-4 col-md-2 offset-md-6">
			<a href="#" class="btn -primary -condensed settings-btn float-right">
				<i class="wlm-icons">settings</i>
				<span><?php _e( 'Settings', 'wishlist-member' ); ?></span>
			</a>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="table-wrapper -special table-responsive -cp-table">
			<table class="table table-condensed folder-list">
				<thead>
					<tr class="button-hover">
						<th style="width: 40px" class="text-center">
							<div class="form-check -for-tables">
								<input value="" type="checkbox" class="chk-all form-check-input">
								<label class="form-check-label d-none" for=""></label>
							</div>
						</th>
						<th width="50%">Folder Name</th>
						<th class="text-center">Status</th>
						<th class="text-center">Writable</th>
						<th class="text-center">.htaccess</th>
						<th class="text-center">Files</th>
						<th class="text-center">Force Download</th>
					</tr>
				</thead>
				<?php
					$items	= array();
					if ( $rootOfFolders and is_dir( $folder_protection_full_path ) ) {
						foreach ( glob( $folder_protection_full_path . '/*', GLOB_ONLYDIR ) as $dir_name ) {
							$item     = array();
							$dir_name = basename( $dir_name );
							$fullpath = $folder_protection_full_path . '/' . $dir_name;
							if ( is_dir( $fullpath ) ) {
								$folder_id          = $this->FolderID( $dir_name );
								$item['full_path']  = $fullpath;
								$item['post_title'] = basename( $fullpath );

								$item['writable']          = is_writable( $fullpath );
								$item['htaccess_exists']   = file_exists( $fullpath . '/.htaccess' );
								$item['htaccess_writable'] = is_writable( $fullpath . '/.htaccess' );
								$item['wlm_protection']    = array( $this->FolderProtected( $folder_id ) );
								$item['force_download']    = $this->FolderForceDownload( $folder_id );

								$item['ID'] = $folder_id;

								$items[] = $item;
							}
						}
					}
				?>
				<?php foreach ( $items as $item ) : ?>
					<?php
						include( $this->pluginDir3 ."/ui/admin_screens/content_protection/folders/content-item.php");
					?>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>


<!-- Modal -->
<div id="protection-modal" data-id="protection-modal" data-label="protection_modal_label" data-title="Edit Protection Status" data-classes="modal-sm" style="display:none">
	<div class="body">
		<div class="form-group">
			<label for="">Protection Status</label>
			<select class="form-control wlm-levels wlm-protection" name="protection" style="width: 100%" required>
				<option><?php _e( 'Unprotected', 'wishlist-member' ); ?></option>
				<option><?php _e( 'Protected', 'wishlist-member' ); ?></option>
				<!-- <option><?php _e( 'Inherited', 'wishlist-member' ); ?></option> -->
			</select>
		</div>
		<?php if ( $content_comment ) : ?>
			<input type="hidden" name="content_comment" value="1" />
		<?php endif; ?>
		<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
		<input type="hidden" name="contentids" value="" />
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><i class="wlm-icons">lock</i> <span><?php _e( 'Update Protection', 'wishlist-member' ); ?></span></button>
	</div>
</div>

<div id="add-level-modal" data-id="add-level-modal" data-label="add_level_modal_label" data-title="Add Levels" data-classes="modal-sm" style="display:none">
	<div class="body">
		<div class="form-group membership-level-select">
			<label for=""><?php _e( 'Membership Levels', 'wishlist-member' ); ?></label>
			<select class="form-control wlm-levels" multiple="multiple" name="wlm_levels[]" id="" style="width: 100%" data-placeholder="Select Membership Levels" required>
				<?php foreach ( $wpm_levels as $key => $value ) : ?>
				<option value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
			<?php endforeach; ?>
			</select>
		</div>
		<?php if ( $content_comment ) : ?>
			<input type="hidden" name="content_comment" value="1" />
		<?php endif; ?>
		<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
		<input type="hidden" name="contentids" value="" />
		<input type="hidden" name="level_action" value="add" />
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><i class="wlm-icons">add_circle_outline</i> <span><?php _e( 'Add Level', 'wishlist-member' ); ?></span></button>
	</div>
</div>

<div id="remove-level-modal" data-id="remove-level-modal" data-label="remove_level_modal_label" data-title="Remove Levels" data-classes="modal-sm" style="display:none">
	<div class="body">
		<div class="form-group">
			<label for=""><?php _e( 'Membership Levels', 'wishlist-member' ); ?></label>
			<select class="form-control wlm-levels" multiple="multiple" name="wlm_levels[]" id="" style="width: 100%" data-placeholder="Select Membership Levels" required>
				<?php foreach ( $wpm_levels as $key => $value ) : ?>
				<option value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
			<?php endforeach; ?>
			</select>
		</div>
		<?php if ( $content_comment ) : ?>
			<input type="hidden" name="content_comment" value="1" />
		<?php endif; ?>
		<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
		<input type="hidden" name="contentids" value="" />
		<input type="hidden" name="level_action" value="remove" />
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
		<button type="button" class="btn -primary save-button"><i class="wlm-icons">remove_circle_outline</i> <span><?php _e( 'Remove Level', 'wishlist-member' ); ?></span></button>
	</div>
</div>

<div id="settings-modal" data-id="settings-modal" data-label="settings_modal_label" data-title="Settings" data-classes="modal-lg" style="display:none">
	<div class="body">
		<div class="row">
			<?php
				$parentFolder = trim( $this->GetOption( 'parentFolder' ) );
				$parentFolder = $parentFolder ? $parentFolder : "";
			?>
			<template class="wlm3-form-group">
				{
					column : 'col-md-8',
					label : '<?php _e( 'The path to the parent folder', 'wishlist-member' ); ?>',
					tooltip : '<?php _e( 'By default, WishList Member will create a folder called "files" in the root directory of your website. If you would like to modify the name of this primary folder you can change it. This will be the folder all individual Level folders are placed inside.', 'wishlist-member' ); ?>',
					tooltip_size : 'md',
					name : 'parentFolder',
					'data-initial' : '<?php echo $parentFolder; ?>',
					value : '<?php echo $parentFolder; ?>',
					addon_left : '<?php echo ABSPATH; ?>',
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
			<div class="col-md-12">
				<p>* <?php _e( 'Using NGINX?', 'wishlist-member' ); ?> <a href="#wlm-nginx" data-toggle="collapse" ><?php _e( 'Click here.', 'wishlist-member' ); ?></a></p>
				<div id="wlm-nginx" class="collapse">
					<p><?php _e( 'Add the following line in your NGINX site configuration\'s server {} block:', 'wishlist-member' ); ?></p>
					<p><code>include <?php echo $this->wp_upload_path; ?>/wlm_file_protect_nginx.conf;</code></p>
					<p><a href="http://wlplink.com/go/nginxinfo" target="_blank"><?php _e( 'Read the knowledge base article for more info.', 'wishlist-member' ); ?></a></p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs" role="tablist" style="margin-top: 0px;">
					<li role="presentation" class="nav-item"><a class="edit-tab active nav-link" href="#auto-config" aria-controls="auto-config" role="tab" data-toggle="tab"><?php _e( 'Auto-Configure','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="edit-tab nav-link" href="#remove" aria-controls="remove" role="tab" data-toggle="tab"><?php _e( 'Remove Protection','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="edit-tab nav-link" href="#reset" aria-controls="reset" role="tab" data-toggle="tab"><?php _e( 'Reset Protection','wishlist-member' ); ?></a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="auto-config">
						<div class="row">
							<div class="col-md-12">
								<?php
									$folder_protection_autoconfig  = $this->GetOption('folder_protection_autoconfig');
									$folder_protection_autoconfig = $folder_protection_autoconfig ? 1 : 0;
								?>
								<template class="wlm3-form-group">
									{
										label : '<?php _e( 'Automatically configure folder protection for new levels', 'wishlist-member' ); ?>',
										name  : 'folder_protection_autoconfig',
										value : '1',
										checked_value : '<?php echo $folder_protection_autoconfig; ?>',
										uncheck_value : '0',
										class : 'wlm_toggle-switch toggle-autoconfigure',
										type  : 'checkbox'
									}
								</template>
								<input type="hidden" name="action" value="admin_actions" />
								<input type="hidden" name="WishListMemberAction" value="save" />
							</div>
						</div><br />
						<p><?php _e( 'When this feature is enabled the following actions will occur.', 'wishlist-member' ); ?> </p>
						<ol class="wlm-folder-autoconfig-actions">
				            <li>Each time a new membership level is created in your site, a folder will be created with a matching name. </li>
				            <li>The membership level will automatically be assigned access to this folder. </li>
				        </ol>
				        <small>* <?php _e( 'Please note, if you change the name of a membership level it will not update the folder name.', 'wishlist-member' ); ?> </small>
					</div>
					<div role="tabpanel" class="tab-pane" id="remove">
						<p><?php _e( 'Clicking the button below will perform the following actions:', 'wishlist-member' ); ?></p>
						<ol class="wlm-folder-autoconfig-actions">
				            <li>All assigned access to folders will be removed. </li>
				            <li>Folders will not be deleted, however no membership levels will have access to the folders.</li>
				        </ol>
						<div class="text-center">
							<a href="#" class="btn -primary reset-config-btn -condensed">
								<i class="wlm-icons">remove_circle_outline</i>
								<span><?php _e( 'Remove Protection', 'wishlist-member' ); ?></span>
							</a>
						</div>
						<input type="hidden" name="type" value="remove" />
						<input type="hidden" name="action" value="admin_actions" />
						<input type="hidden" name="WishListMemberAction" value="folder_protection_autoconfig" />
					</div>
					<div role="tabpanel" class="tab-pane" id="reset">
						<p><?php _e( 'Clicking the button below will perform the following actions:', 'wishlist-member' ); ?></p>
						<ol class="wlm-folder-autoconfig-actions">
				            <li><?php _e( 'Un-protect all folders being protected by WishList Member', 'wishlist-member' ); ?></li>
				            <li><?php _e( 'Create a folder at', 'wishlist-member' ); ?> <code><?php echo $folder_protection_full_path; ?></code> <?php _e( 'if it does not exist', 'wishlist-member' ); ?></li>
				            <li><?php _e( 'Create a sub-folder for each membership level if necessary and protect them accordingly', 'wishlist-member' ); ?></li>
				        </ol>
				        <div class="text-center">
							<a href="#" class="btn -primary reset-config-btn -condensed">
								<i class="wlm-icons">cached</i>
								<span><?php _e( 'Reset Protection', 'wishlist-member' ); ?></span>
							</a>
				        </div>
						<input type="hidden" name="type" value="reset" />
						<input type="hidden" name="action" value="admin_actions" />
						<input type="hidden" name="WishListMemberAction" value="folder_protection_autoconfig" />
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
	</div>
</div>

<div id="showfiles-modal" data-id="showfiles-modal" data-label="showfiles_modal_label" data-title="Files" data-classes="modal-md" style="display:none">
	<div class="body">
		<ul class="list-group no-margin">
		</ul>
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal"><?php _e( 'Close', 'wishlist-member' ); ?></button>
	</div>
</div>