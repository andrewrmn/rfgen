<?php
	$api_queue = new WishlistAPIQueue;
	$queue_count = $api_queue->count_queue("import_member_queue", 0);
?>
<div class="page-header">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-8">
			<h2 class="page-title">
				<?php _e( 'Import Members', 'wishlist-member' ); ?>
			</h2>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
	</div>
</div>
<div class="content-wrapper">
<?php
	if ( $queue_count > 0 ) {
		$in_queue = $this->GetOption('import_member_queue_count');
		if ( !$in_queue || $in_queue <= 0 ) {
			$this->SaveOption('import_member_queue_count', $queue_count );
			$in_queue = $queue_count;
		}
		include( $this->pluginDir3 ."/ui/admin_screens/members/import/queue.php");
	}
	include( $this->pluginDir3 ."/ui/admin_screens/members/import/form.php");
?>
</div>