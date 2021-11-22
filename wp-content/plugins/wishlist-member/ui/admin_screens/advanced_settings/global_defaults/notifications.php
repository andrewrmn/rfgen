<?php
	$notifications = array(
		"registration"=>array(
			"title" => "Registration",
			"options"=> array(
				// "registration_notification" => array( "label"=>"Registration"),
				// "requireemailconfirmation_notification" => array( "label"=>"Require Email Confirmation"),
				// "requireadminapproval_notification" => array( "label"=>"Require Admin Approval"),
				// "registrationadminapproval_notification" => array( "label"=>"Admin Approval Notification"),
				// "incomplete_notification" => array( "label"=>"Incomplete Registration Notification"),
			)
		),
		"administration"=>array(
			"title" => "Administration",
			"options"=> array(
				// "notify_admin_of_newuser" => array( "label"=>"New Member Notification"),
				"unsub_notification" => array( "label"=>"Member Unsubscribe"),
			)
		),
		"maintenance"=>array(
			"title" => "Maintenance",
			"options"=> array(
				// "enable_retrieve_password_override" => array( "label"=>"Lost Info"),
				// "expiring_notification" => array( "label"=>"Expiring Member Notification"),
				// "password_hinting" => array( "label"=>"Password Hint Notification"),
			)
		),
	);
?>

<div class="content-wrapper">
	<h3 class="main-title"><?php _e( 'Notifications', 'wishlist-member' ); ?></h3>
	<?php foreach ( $notifications as $notification ) : ?>
		<div class="row">
			<div class="col-lg-12">
				<h4 class="section-title"><?php echo $notification["title"]; ?></h4>
			</div>
		</div>
		<?php foreach ( $notification["options"] as $key => $values ) : ?>
			<?php
				$option_val = $this->GetOption($key);
				$is_checked = $option_val && $option_val == "1" ? 'true' : 'false';
				$is_disabled = $option_val && $option_val == "1" ? '' : 'disabled="disabled"';
				$css_class = $option_val && $option_val == "1" ? '-primary' : '-disable';
			?>
			<div class="row">
				<div class="col-lg-1 col-md-2">
					<template class="wlm3-toggle-switch">
						{
							name : '<?php echo $key; ?>',
							value : '1',
							checked: <?php echo $is_checked; ?>,
							class : 'notification-switch',
							uncheck_value : 0,
						}
					</template>
					<input type="hidden" name="action" value="admin_actions" />
					<input type="hidden" name="WishListMemberAction" value="save" />
				</div>
				<div class="col-lg-4 col-md-4">
					<h5 class="title-label"><?php echo $values['label']; ?></h5>
				</div>
				<div class="col-lg-7 col-md-6">
					<a href="#" id="<?php echo $key; ?>_btn" class="btn -primary <?php echo $css_class; ?> edit-notification <?php echo $is_disabled; ?>">
						<i class="wlm-icons md-18">settings</i>
						<span class="text"><?php _e( 'Edit', 'wishlist-member' ); ?></span>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
		<br /><br />
	<?php endforeach; ?>
</div>


<!-- Modal -->

<div id="edit-notification-modal-info" data-id="edit-notification-modal" data-label="edit_notification_modal_modal" data-title="Editing Notification for '<span></span>'" style="display:none">
	<div class="body">
		<div class="content-wrapper -no-background -no-header"></div>
	</div>
	<div class="footer">
    <button type="button" class="btn -bare" data-dismiss="modal">Close</button>
    <button type="button" class="btn -primary edit-notification-save">Save</button>
    <input type="hidden" class="notification-button-id" value="" />
	</div>
</div>
<script>
	new wlm3_modal('#edit-notification-modal-info');
</script>

<!-- Holders -->
<div id="registration_notification_holder" class="d-none">
	<?php
		$email_address 	= $this->GetOption('register_email_addres');
		$email_address  = $email_address ? $email_address : $this->GetOption('email_sender_address');
		$email_name  	= $this->GetOption('register_email_name');
		$email_name  	= $email_name ? $email_name : $this->GetOption('email_sender_name');
		$email_subject  = $this->GetOption('register_email_subject');
		$email_body  	= $this->GetOption('register_email_body');
	?>
	<div class="row">
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Name', 'wishlist-member' ); ?>',
					name : 'register_email_name',
					value : <?php echo json_encode($email_name); ?>,
					placeholder : "Sender's name",
					required : 'required'
				}
			</template>
		</div>
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Email', 'wishlist-member' ); ?>',
					name : 'register_email_addres',
					value : <?php echo json_encode($email_address); ?>,
					placeholder : "Sender's email",
					required : 'required'
				}
			</template>
		</div>
		<div class="col-md-12">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Subject', 'wishlist-member' ); ?>',
					name : 'register_email_subject',
					value : <?php echo json_encode($email_subject); ?>,
					placeholder : '<?php _e( 'Email subject', 'wishlist-member' ); ?>',
					required : 'required'
				}
			</template>
		</div>
		<div class="col-md-12">
			<template class="wlm3-form-group">
				{
					type : 'textarea',
					name : 'register_email_body',
					value : <?php echo json_encode($email_body); ?>,
					placeholder : '<?php _e( 'Your message', 'wishlist-member' ); ?>',
					required : 'required',
					rows : 10,
				}
			</template>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>

<div id="requireemailconfirmation_notification_holder" class="d-none">
	<?php
		$email_address 	= $this->GetOption('confirm_email_address');
		$email_address  = $email_address ? $email_address : $this->GetOption('email_sender_address');
		$email_name  	= $this->GetOption('confirm_email_name');
		$email_name  	= $email_name ? $email_name : $this->GetOption('email_sender_name');
		$email_subject  = $this->GetOption('confirm_email_subject');
		$email_body  	= $this->GetOption('confirm_email_message');

		$email_conf_send_after	= $this->GetOption('email_conf_send_after');
		$email_conf_how_many	= $this->GetOption('email_conf_how_many');
		$email_conf_send_every	= $this->GetOption('email_conf_send_every');
	?>
	<div class="row">
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Name', 'wishlist-member' ); ?>',
					name : 'confirm_email_name',
					value : <?php echo json_encode($email_name); ?>,
					placeholder : "Sender's name",
					required : 'required'
				}
			</template>
		</div>
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Email', 'wishlist-member' ); ?>',
					name : 'confirm_email_address',
					value : <?php echo json_encode($email_address); ?>,
					placeholder : "Sender's email",
					required : 'required'
				}
			</template>
		</div>
		<div class="col-md-12">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Subject', 'wishlist-member' ); ?>',
					name : 'confirm_email_subject',
					value : <?php echo json_encode($email_subject); ?>,
					placeholder : '<?php _e( 'Email subject', 'wishlist-member' ); ?>',
					required : 'required'
				}
			</template>
		</div>
		<div class="col-md-12">
			<template class="wlm3-form-group">
				{
					type : 'textarea',
					name : 'confirm_email_message',
					value : <?php echo json_encode($email_body); ?>,
					placeholder : '<?php _e( 'Your message', 'wishlist-member' ); ?>',
					required : 'required',
					rows : 10,
				}
			</template>
		</div>
	</div>
	<h4><?php _e( 'Email Confirmation Reminder', 'wishlist-member' ); ?></h4>
	<div class="row">
		<div class="col-md-4">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Start Reminder After', 'wishlist-member' ); ?>',
					name : 'email_conf_send_after',
					value : <?php echo json_encode($email_conf_send_after); ?>,
					placeholder : "Remind after",
				}
			</template>
		</div>
		<div class="col-md-4">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'How Many Reminders', 'wishlist-member' ); ?>',
					name : 'email_conf_how_many',
					value : <?php echo json_encode($email_conf_how_many); ?>,
					placeholder : "Reminders to send",
				}
			</template>
		</div>
		<div class="col-md-4">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Send Every', 'wishlist-member' ); ?>',
					name : 'email_conf_send_every',
					value : <?php echo json_encode($email_conf_send_every); ?>,
					placeholder : "Send every",
				}
			</template>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>

<div id="requireadminapproval_notification_holder" class="d-none">
	<?php
		$email_address 	= $this->GetOption('requireadminapproval_email_address');
		$email_address  = $email_address ? $email_address : $this->GetOption('email_sender_address');
		$email_name  	= $this->GetOption('requireadminapproval_email_name');
		$email_name  	= $email_name ? $email_name : $this->GetOption('email_sender_name');
		$email_subject  = $this->GetOption('requireadminapproval_email_subject');
		$email_body  	= $this->GetOption('requireadminapproval_email_message');
	?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Name', 'wishlist-member' ); ?></label>
	    		<input type="text" name="requireadminapproval_email_name" value="<?php echo $email_name; ?>" class="form-control" placeholder="Sender's name" required />
	    	</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Email', 'wishlist-member' ); ?></label>
	    		<input type="text" name="requireadminapproval_email_address" value="<?php echo $email_address; ?>" class="form-control" placeholder="Sender's email" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<label for=""><?php _e( 'Subject', 'wishlist-member' ); ?></label>
	    		<input type="text" name="requireadminapproval_email_subject" value="<?php echo $email_subject; ?>" class="form-control" placeholder="Email subject" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<textarea class="form-control" name="requireadminapproval_email_message" rows="10" placeholder="Your message" required><?php echo $email_body; ?></textarea>
	    	</div>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>

<div id="registrationadminapproval_notification_holder" class="d-none">
	<?php
		$email_address 	= $this->GetOption('registrationadminapproval_email_address');
		$email_address  = $email_address ? $email_address : $this->GetOption('email_sender_address');
		$email_name  	= $this->GetOption('registrationadminapproval_email_name');
		$email_name  	= $email_name ? $email_name : $this->GetOption('email_sender_name');
		$email_subject  = $this->GetOption('registrationadminapproval_email_subject');
		$email_body  	= $this->GetOption('registrationadminapproval_email_message');
	?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Name', 'wishlist-member' ); ?></label>
	    		<input type="text" name="registrationadminapproval_email_name" value="<?php echo $email_name; ?>" class="form-control" placeholder="Sender's name" required />
	    	</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Email', 'wishlist-member' ); ?></label>
	    		<input type="text" name="registrationadminapproval_email_address" value="<?php echo $email_address; ?>" class="form-control" placeholder="Sender's email" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<label for=""><?php _e( 'Subject', 'wishlist-member' ); ?></label>
	    		<input type="text" name="registrationadminapproval_email_subject" value="<?php echo $email_subject; ?>" class="form-control" placeholder="Email subject" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<textarea class="form-control" name="registrationadminapproval_email_message" rows="10" placeholder="Your message" required><?php echo $email_body; ?></textarea>
	    	</div>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>

<div id="incomplete_notification_holder" class="d-none">
	<?php
		$email_address 	= $this->GetOption('incnotification_email_address');
		$email_address  = $email_address ? $email_address : $this->GetOption('email_sender_address');
		$email_name  	= $this->GetOption('incnotification_email_name');
		$email_name  	= $email_name ? $email_name : $this->GetOption('email_sender_name');
		$email_subject  = $this->GetOption('incnotification_email_subject');
		$email_body  	= $this->GetOption('incnotification_email_message');

		$email_conf_send_after	= $this->GetOption('incomplete_notification_first');
		$email_conf_how_many	= $this->GetOption('incomplete_notification_add');
		$email_conf_send_every	= $this->GetOption('incomplete_notification_add_every');
	?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Name', 'wishlist-member' ); ?></label>
	    		<input type="text" name="incnotification_email_name" value="<?php echo $email_name; ?>" class="form-control" placeholder="Sender's name" required />
	    	</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Email', 'wishlist-member' ); ?></label>
	    		<input type="text" name="incnotification_email_address" value="<?php echo $email_address; ?>" class="form-control" placeholder="Sender's email" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<label for=""><?php _e( 'Subject', 'wishlist-member' ); ?></label>
	    		<input type="text" name="incnotification_email_subject" value="<?php echo $email_subject; ?>" class="form-control" placeholder="Email subject" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<textarea class="form-control" name="incnotification_email_message" rows="10" placeholder="Your message" required><?php echo $email_body; ?></textarea>
	    	</div>
		</div>
	</div>
	<h4><?php _e( 'Incomplete Registration Reminder', 'wishlist-member' ); ?></h4>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
	    		<label for=""><?php _e( 'Start Reminder After', 'wishlist-member' ); ?></label>
	    		<input type="text" name="incomplete_notification_first" value="<?php echo $email_conf_send_after; ?>" class="form-control" placeholder="Remind after" />
	    	</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
	    		<label for="">How Many Reminders</label>
	    		<input type="text" name="incomplete_notification_add" value="<?php echo $email_conf_how_many; ?>" class="form-control" placeholder="Reminders to send" />
	    	</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
	    		<label for=""><?php _e( 'Send Every', 'wishlist-member' ); ?></label>
	    		<input type="text" name="incomplete_notification_add_every" value="<?php echo $email_conf_send_every; ?>" class="form-control" placeholder="Send every" />
	    	</div>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>

<div id="notify_admin_of_newuser_holder" class="d-none">
	<?php
		$email_subject  = $this->GetOption('newmembernotice_email_subject');
		$email_body  	= $this->GetOption('newmembernotice_email_message');
	?>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
	    		<label for=""><?php _e( 'Subject', 'wishlist-member' ); ?></label>
	    		<input type="text" name="newmembernotice_email_subject" value="<?php echo $email_subject; ?>" class="form-control" placeholder="Email subject" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<textarea class="form-control" name="newmembernotice_email_message" rows="10" placeholder="Your message" required><?php echo $email_body; ?></textarea>
	    	</div>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>

<div id="unsub_notification_holder" class="d-none">
	<?php
		$email_subject  = $this->GetOption('unsubscribe_notice_email_subject');
		$email_body  	= $this->GetOption('unsubscribe_notice_email_message');
	?>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
	    		<label for=""><?php _e( 'Subject', 'wishlist-member' ); ?></label>
	    		<input type="text" name="unsubscribe_notice_email_subject" value="<?php echo $email_subject; ?>" class="form-control" placeholder="Email subject" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<textarea class="form-control" name="unsubscribe_notice_email_message" rows="10" placeholder="Your message" required><?php echo $email_body; ?></textarea>
	    	</div>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>

<div id="enable_retrieve_password_override_holder" class="d-none">
	<?php
		$email_address 	= $this->GetOption('lostinfo_email_address');
		$email_address  = $email_address ? $email_address : $this->GetOption('email_sender_address');
		$email_name  	= $this->GetOption('lostinfo_email_name');
		$email_name  	= $email_name ? $email_name : $this->GetOption('email_sender_name');
		$email_subject  = $this->GetOption('lostinfo_email_subject');
		$email_body  	= $this->GetOption('lostinfo_email_message');
	?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Name', 'wishlist-member' ); ?></label>
	    		<input type="text" name="lostinfo_email_name" value="<?php echo $email_name; ?>" class="form-control" placeholder="Sender's name" required />
	    	</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Email', 'wishlist-member' ); ?></label>
	    		<input type="text" name="lostinfo_email_address" value="<?php echo $email_address; ?>" class="form-control" placeholder="Sender's email" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<label for=""><?php _e( 'Subject', 'wishlist-member' ); ?></label>
	    		<input type="text" name="lostinfo_email_subject" value="<?php echo $email_subject; ?>" class="form-control" placeholder="Email subject" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<textarea class="form-control" name="lostinfo_email_message" rows="10" placeholder="Your message" required><?php echo $email_body; ?></textarea>
	    	</div>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>

<div id="expiring_notification_holder" class="d-none">
	<?php
		$email_address 	= $this->GetOption('expiringnotification_email_address');
		$email_address  = $email_address ? $email_address : $this->GetOption('email_sender_address');
		$email_name  	= $this->GetOption('expiringnotification_email_name');
		$email_name  	= $email_name ? $email_name : $this->GetOption('email_sender_name');
		$email_subject  = $this->GetOption('expiringnotification_email_subject');
		$email_body  	= $this->GetOption('expiringnotification_email_message');

		$expiring_notification_days	= $this->GetOption('expiring_notification_days');
	?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Name', 'wishlist-member' ); ?></label>
	    		<input type="text" name="expiringnotification_email_name" value="<?php echo $email_name; ?>" class="form-control" placeholder="Sender's name" required />
	    	</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Email', 'wishlist-member' ); ?></label>
	    		<input type="text" name="expiringnotification_email_address" value="<?php echo $email_address; ?>" class="form-control" placeholder="Sender's email" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<label for=""><?php _e( 'Subject', 'wishlist-member' ); ?></label>
	    		<input type="text" name="expiringnotification_email_subject" value="<?php echo $email_subject; ?>" class="form-control" placeholder="Email subject" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<textarea class="form-control" name="expiringnotification_email_message" rows="10" placeholder="Your message" required><?php echo $email_body; ?></textarea>
	    	</div>
		</div>
	</div>
	<h4><?php _e( 'Expiring Membership Reminder', 'wishlist-member' ); ?></h4>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
	    		<label for="">Number of Days Before Expiration Date</label>
	    		<!-- start: v4 -->
	    		<input style="width: 50%;" type="text" name="expiring_notification_days" value="<?php echo $expiring_notification_days; ?>" class="form-control" placeholder="Days before expiration" />
	    		<small class="form-text text-muted" id="helpBlock">An Email Notification will be sent once each day based on the number entered into this field.</small>
	    		<!-- end: v4 -->
	    	</div>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>

<div id="password_hinting_holder" class="d-none">
	<?php
		$email_address 	= $this->GetOption('password_hint_email_address');
		$email_address  = $email_address ? $email_address : $this->GetOption('email_sender_address');
		$email_name  	= $this->GetOption('password_hint_email_name');
		$email_name  	= $email_name ? $email_name : $this->GetOption('email_sender_name');
		$email_subject  = $this->GetOption('password_hint_email_subject');
		$email_body  	= $this->GetOption('password_hint_email_message');
	?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Name', 'wishlist-member' ); ?></label>
	    		<input type="text" name="password_hint_email_name" value="<?php echo $email_name; ?>" class="form-control" placeholder="Sender's name" required />
	    	</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
	    		<label for=""><?php _e( 'Email', 'wishlist-member' ); ?></label>
	    		<input type="text" name="password_hint_email_address" value="<?php echo $email_address; ?>" class="form-control" placeholder="Sender's email" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<label for=""><?php _e( 'Subject', 'wishlist-member' ); ?></label>
	    		<input type="text" name="password_hint_email_subject" value="<?php echo $email_subject; ?>" class="form-control" placeholder="Email subject" required />
	    	</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
	    		<textarea class="form-control" name="password_hint_email_message" rows="10" placeholder="Your message" required><?php echo $email_body; ?></textarea>
	    	</div>
		</div>
	</div>
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</div>