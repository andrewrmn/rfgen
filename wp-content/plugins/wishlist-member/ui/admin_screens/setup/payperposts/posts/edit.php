<?php

	$data = $this->GetOption( 'payperpost-' . $post->ID );

	$data = array_merge( $this->ppp_email_defaults, array_diff( is_array( $data ) ? $data : [], [''] ) );
	$data['free_ppp'] = (int) $this->Free_PayPerPost( $post->ID );
	$data['is_ppp'] = (int) $this->PayPerPost( $post->ID );
?>
<style type="text/css">
	#wlm3-tabbar {
		display: none;
	}
</style>
<script type="text/javascript">
	var payperpost_data = <?php echo json_encode( $data ); ?>;
</script>
<div class="show-saving">
	<form id="ppps-form">
		<input type="hidden" id="first-save">
		<div id="save-action-fields">
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save_payperpost" />
			<input type="hidden" name="id" value="<?php echo $post->ID; ?>">
		</div>
 		<div class="page-header">
			<div class="large-form">
				<div class="row">
					<div class="col-sm-auto col-md-auto col-lg-auto">
						<h2 class="page-title"><?php printf( __( 'Pay Per Post: %s','wishlist-member' ), $post->post_title ); ?></h2>
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="all-ppps-data">
			<div class="col-md-12">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs responsive-tabs -no-background levels-edit-tabs" role="tablist">
					<li role="presentation" class="nav-item"><a class="nav-link active show" href="#ppps_access" role="tab" data-toggle="tab"><?php _e( 'Access','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#ppps_notifications" role="tab" data-toggle="tab"><?php _e( 'Notifications','wishlist-member' ); ?></a></li>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content">
					<?php
						include_once 'edit/access.php';
						include_once 'edit/notifications.php';
					?>
				</div>
			</div>
			<?php
				// per level modals
				include_once 'edit/modal/email_notifications.php';
			?>
		</div>
	</form>
</div>
