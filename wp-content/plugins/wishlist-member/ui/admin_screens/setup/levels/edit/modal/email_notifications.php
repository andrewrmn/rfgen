<div
	id="email-notification-settings-modal"
	data-id="email-notification-settings"
	data-label="email-notification-settings"
	data-title="<span></span>"
	data-classes="modal-lg"
	data-show-default-footer=""
	style="display:none">
	<div class="body">
		<?php
			include_once 'email_notifications/require_email_confirmation.php';
			include_once 'email_notifications/require_admin_approval_free.php';
			include_once 'email_notifications/require_admin_approval_paid.php';
			include_once 'email_notifications/incomplete.php';
			include_once 'email_notifications/newuser.php';
			include_once 'email_notifications/expiring.php';
			include_once 'email_notifications/cancel.php';
			include_once 'email_notifications/uncancel.php';
		?>
	</div>
	<div class="footer">
		<?php echo $modal_footer; ?>
	</div>
</div>

<style type="text/css">
	#email-notification-settings textarea {
		min-height: 5rem;
		max-width: 100%;
	}
	#email-notification-settings .nav-tabs {
		margin-top: 0;
		margin-bottom: 20px;
	}
	#email-notification-settings .form-inline.pull-right .form-group {
		margin-left: 1em;
	}
	#email-notification-settings .form-inline.pull-left .form-group {
		margin-right: 1em;
	}
</style>