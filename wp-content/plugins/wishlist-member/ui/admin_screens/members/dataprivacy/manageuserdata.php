<?php
	$error_message = __('(It appears you do not have the proper WordPress privilege to access this feature)', 'wishlist-member');
	$can_export = current_user_can( 'export_others_personal_data' );
	$can_erase = current_user_can( 'erase_others_personal_data' );

	$export_link = $can_export ? sprintf('a target="_parent" href="%s"', admin_url('tools.php?page=export_personal_data')) : 'span';
	$erase_link = $can_erase ? sprintf('a target="_parent" href="%s"', admin_url('tools.php?page=remove_personal_data')) : 'span';
?>
<div class="content-wrapper">
	<div class="row">
		<div class="col-md-12">
			<p><?php _e( 'This functionality is handled directly by WordPress.', 'wishlist-member' ); ?></p>
			<ul>
				<li>
					<<?php echo $export_link; ?> class="d-inline-block" style="width: 150px">Export Personal Data</<?php echo explode(' ', $export_link)[0]; ?>>
					<?php if(!$can_export) : ?><em class="d-inline-block"><?php echo $error_message; ?></em><?php endif; ?>
				</li>
				<li>
					<<?php echo $erase_link; ?> class="d-inline-block" style="width: 150px">Erase Personal Data</<?php echo explode(' ', $erase_link)[0]; ?>>
					<?php if(!$can_erase) : ?><em class="d-inline-block"><?php echo $error_message; ?></em><?php endif; ?>
				</li>
			</ul>
		</div>
	</div>
</div>

