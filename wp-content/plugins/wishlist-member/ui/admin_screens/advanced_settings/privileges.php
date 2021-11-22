<?php
	global $wp_roles;
	$roles = $wp_roles->roles;
?>
<div class="page-header">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-8">
			<h2 class="page-title">
				<?php _e( 'Privileges', 'wishlist-member' ); ?>
			</h2>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
	</div>
</div>
<div class="content-wrapper">
	<div class="row">
		<?php
			$selected_roles = (array) $this->GetOption('wlmshortcode_role_access');
			$selected_roles = is_string( $selected_roles ) ? array() : $selected_roles;
			if ( is_array( $selected_roles ) ) {
				$selected_roles = array_unique( $selected_roles );
			} else {
				$selected_roles = array();
			}
		?>
		<div class="col-md-6">
			<div class="form-group">
	    		<label for="">
	    			<?php _e('WishList Member Shortcode/Mergecode Inserter Access', 'wishlist-member'); ?>
	    			<?php $this->tooltip(__('Users with the set WordPress Roles can access the WishList Member Shortcode/Mergecode Inserter when creating/updating posts and pages.', 'wishlist-member')); ?>
	    		</label>
				<select name="wlmshortcode_role_access[]" class="form-control wlm-select" multiple="multiple">
			     	<?php foreach( $roles as $rk => $role ) : ?>
			     	<?php $caps = isset( $role['capabilities'] ) ? (array) $role['capabilities'] : array();  ?>
			     	<?php if ( $rk != "administrator" && ( isset( $caps['edit_posts'] ) || isset( $caps['edit_pages'] ) ) ) : ?>
			     		<?php $selected = ( $selected_roles === false || in_array( $rk, $selected_roles ) ?  $selected = 'selected="selected"' : '' ); ?>
			     		<option value="<?php echo $rk; ?>" <?php echo $selected; ?> ><?php echo $role['name']; ?></option>
			     	<?php endif; ?>
			     	<?php endforeach; ?>
				</select>
				<small class="form-text text-muted"><?php _e( 'Administrators always have access.', 'wishlist-member' ); ?></small>
	    	</div>
		</div>
	</div>
	<div class="row">
		<?php
			$selected_roles = (array) $this->GetOption('wlmpageoptions_role_access');
			if ( is_array( $selected_roles ) ) {
				$selected_roles = array_unique( $selected_roles );
			} else {
				$selected_roles = array();
			}
		?>
		<div class="col-md-6">
			<div class="form-group">
	    		<label for="">
	    			<?php _e('Access to WishList Member Options in WordPress Pages/Post', 'wishlist-member'); ?>
	    			<?php $this->tooltip(__('Users with the set WordPress Roles can access the WishList Member Options when  creating/editing posts and pages.', 'wishlist-member')); ?>
	    		</label>
				<select name="wlmpageoptions_role_access[]" class="form-control wlm-select" multiple="multiple">
			     	<?php foreach( $roles as $rk => $role ) : ?>
			     	<?php $caps = isset( $role['capabilities'] ) ? (array) $role['capabilities'] : array();  ?>
			     	<?php if ( $rk != "administrator" && ( isset( $caps['edit_posts'] ) || isset( $caps['edit_pages'] ) ) ) : ?>
			     		<?php $selected = ( $selected_roles === false || in_array( $rk, $selected_roles ) ?  $selected = 'selected="selected"' : '' ); ?>
			     		<option value="<?php echo $rk; ?>" <?php echo $selected; ?> ><?php echo $role['name']; ?></option>
			     	<?php endif; ?>
			     	<?php endforeach; ?>
				</select>
				<small class="form-text text-muted"><?php _e( 'Administrators always have access.', 'wishlist-member' ); ?></small>
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