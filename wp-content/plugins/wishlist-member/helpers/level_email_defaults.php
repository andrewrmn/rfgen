<?php
require $this->legacy_wlm_dir . '/core/InitialValues.php';

global $wpdb;
$x_level_email_defaults = array_combine(
	$wpdb->get_col( "SELECT `option_name`,`option_value` FROM `{$this->OptionsTable}` WHERE `option_name` IN ('cancel_email_message','cancel_email_subject','cancel_notification','confirm_email_message','confirm_email_subject','email_confirmed','email_confirmed_message','email_confirmed_subject','email_sender_address','email_sender_name','expiring_admin_message','expiring_admin_subject','expiring_notification_admin','expiring_notification','expiringnotification_email_message','expiringnotification_email_subject','incnotification_email_message','incnotification_email_subject','incomplete_notification','newmembernotice_email_message','newmembernotice_email_subject','newmembernotice_email_recipient','newuser_notification_user','notify_admin_of_newuser','register_email_body','register_email_subject','registrationadminapproval_email_message','registrationadminapproval_email_paid_message','registrationadminapproval_email_paid_subject','registrationadminapproval_email_subject','require_admin_approval_free_notification_admin','require_admin_approval_free_notification_user1','require_admin_approval_free_notification_user2','require_admin_approval_paid_notification_admin','require_admin_approval_paid_notification_user1','require_admin_approval_paid_notification_user2','requireadminapproval_admin_message','requireadminapproval_admin_paid_message','requireadminapproval_admin_paid_subject','requireadminapproval_admin_subject','requireadminapproval_email_message','requireadminapproval_email_paid_message','requireadminapproval_email_paid_subject','requireadminapproval_email_subject','uncancel_email_message','uncancel_email_subject','uncancel_notification')", 0 ),
	$wpdb->get_col( null, 1 )
);

$level_email_defaults = array(
	'require_email_confirmation_start'               => $WishListMemberInitialData['email_conf_send_after'],
	'require_email_confirmation_send_every'          => $WishListMemberInitialData['email_conf_send_every'],
	'require_email_confirmation_howmany'             => $WishListMemberInitialData['email_conf_how_many'],
	'require_email_confirmation_sender_name'         => $x_level_email_defaults['email_sender_name'],
	'require_email_confirmation_sender_email'        => $x_level_email_defaults['email_sender_address'],
	'require_email_confirmation_subject'             => $x_level_email_defaults['confirm_email_subject'] ?: $WishListMemberInitialData['confirm_email_subject'],
	'require_email_confirmation_message'             => $x_level_email_defaults['confirm_email_message'] ?: $WishListMemberInitialData['confirm_email_message'],

	'email_confirmed'                                => empty( $x_level_email_defaults['email_confirmed'] ) ? 0 : 1,
	'email_confirmed_sender_name'                    => $x_level_email_defaults['email_sender_name'],
	'email_confirmed_sender_email'                   => $x_level_email_defaults['email_sender_address'],
	'email_confirmed_subject'                        => $x_level_email_defaults['email_confirmed_subject'] ?: $WishListMemberInitialData['email_confirmed_subject'],
	'email_confirmed_message'                        => $x_level_email_defaults['email_confirmed_message'] ?: $WishListMemberInitialData['email_confirmed_message'],

	'require_admin_approval_free_notification_admin' => $x_level_email_defaults['require_admin_approval_free_notification_admin'],
	'require_admin_approval_free_admin_subject'      => $x_level_email_defaults['requireadminapproval_admin_subject'] ?: $WishListMemberInitialData['requireadminapproval_admin_subject'],
	'require_admin_approval_free_admin_message'      => $x_level_email_defaults['requireadminapproval_admin_message'] ?: $WishListMemberInitialData['requireadminapproval_admin_message'],

	'require_admin_approval_free_notification_user1' => $x_level_email_defaults['require_admin_approval_free_notification_user1'],
	'require_admin_approval_free_user1_sender_name'  => $x_level_email_defaults['email_sender_name'],
	'require_admin_approval_free_user1_sender_email' => $x_level_email_defaults['email_sender_address'],
	'require_admin_approval_free_user1_subject'      => $x_level_email_defaults['requireadminapproval_email_subject'] ?: $WishListMemberInitialData['requireadminapproval_email_subject'],
	'require_admin_approval_free_user1_message'      => $x_level_email_defaults['requireadminapproval_email_message'] ?: $WishListMemberInitialData['requireadminapproval_email_message'],

	'require_admin_approval_free_notification_user2' => $x_level_email_defaults['require_admin_approval_free_notification_user2'],
	'require_admin_approval_free_user2_sender_name'  => $x_level_email_defaults['email_sender_name'],
	'require_admin_approval_free_user2_sender_email' => $x_level_email_defaults['email_sender_address'],
	'require_admin_approval_free_user2_subject'      => $x_level_email_defaults['registrationadminapproval_email_subject'] ?: $WishListMemberInitialData['registrationadminapproval_email_subject'],
	'require_admin_approval_free_user2_message'      => $x_level_email_defaults['registrationadminapproval_email_message'] ?: $WishListMemberInitialData['registrationadminapproval_email_message'],

	'require_admin_approval_paid_notification_admin' => $x_level_email_defaults['require_admin_approval_paid_notification_admin'],
	'require_admin_approval_paid_admin_subject'      => $x_level_email_defaults['requireadminapproval_admin_paid_subject'] ?: $WishListMemberInitialData['requireadminapproval_admin_paid_subject'],
	'require_admin_approval_paid_admin_message'      => $x_level_email_defaults['requireadminapproval_admin_paid_message'] ?: $WishListMemberInitialData['requireadminapproval_admin_paid_message'],

	'require_admin_approval_paid_notification_user1' => $x_level_email_defaults['require_admin_approval_paid_notification_user1'],
	'require_admin_approval_paid_user1_sender_name'  => $x_level_email_defaults['email_sender_name'],
	'require_admin_approval_paid_user1_sender_email' => $x_level_email_defaults['email_sender_address'],
	'require_admin_approval_paid_user1_subject'      => $x_level_email_defaults['requireadminapproval_email_paid_subject'] ?: $WishListMemberInitialData['requireadminapproval_email_paid_subject'],
	'require_admin_approval_paid_user1_message'      => $x_level_email_defaults['requireadminapproval_email_paid_message'] ?: $WishListMemberInitialData['requireadminapproval_email_paid_message'],

	'require_admin_approval_paid_notification_user2' => $x_level_email_defaults['require_admin_approval_paid_notification_user2'],
	'require_admin_approval_paid_user2_sender_name'  => $x_level_email_defaults['email_sender_name'],
	'require_admin_approval_paid_user2_sender_email' => $x_level_email_defaults['email_sender_address'],
	'require_admin_approval_paid_user2_subject'      => $x_level_email_defaults['registrationadminapproval_email_paid_subject'] ?: $WishListMemberInitialData['registrationadminapproval_email_paid_subject'],
	'require_admin_approval_paid_user2_message'      => $x_level_email_defaults['registrationadminapproval_email_paid_message'] ?: $WishListMemberInitialData['registrationadminapproval_email_paid_message'],

	'incomplete_notification'                        => $x_level_email_defaults['incomplete_notification'],
	'incomplete_start'                               => $WishListMemberInitialData['incomplete_notification_first'],
	'incomplete_start_type'                          => null,
	'incomplete_send_every'                          => $WishListMemberInitialData['incomplete_notification_add_every'],
	'incomplete_howmany'                             => $WishListMemberInitialData['incomplete_notification_add'],
	'incomplete_sender_name'                         => $x_level_email_defaults['email_sender_name'],
	'incomplete_sender_email'                        => $x_level_email_defaults['email_sender_address'],
	'incomplete_subject'                             => $x_level_email_defaults['incnotification_email_subject'] ?: $WishListMemberInitialData['incnotification_email_subject'],
	'incomplete_message'                             => $x_level_email_defaults['incnotification_email_message'] ?: $WishListMemberInitialData['incnotification_email_message'],

	'newuser_notification_admin'                     => $x_level_email_defaults['notify_admin_of_newuser'],
	'newuser_admin_recipient'                        => $x_level_email_defaults['newmembernotice_email_recipient'] ?: $x_level_email_defaults['email_sender_address'],
	'newuser_admin_subject'                          => $x_level_email_defaults['newmembernotice_email_subject'] ?: $WishListMemberInitialData['newmembernotice_email_subject'],
	'newuser_admin_message'                          => $x_level_email_defaults['newmembernotice_email_message'] ?: $WishListMemberInitialData['newmembernotice_email_message'],

	'newuser_notification_user'                      => $x_level_email_defaults['newuser_notification_user'],
	'newuser_user_sender_name'                       => $x_level_email_defaults['email_sender_name'],
	'newuser_user_sender_email'                      => $x_level_email_defaults['email_sender_address'],
	'newuser_user_subject'                           => $x_level_email_defaults['register_email_subject'] ?: $WishListMemberInitialData['register_email_subject'],
	'newuser_user_message'                           => $x_level_email_defaults['register_email_body'],

	'expiring_notification_admin'                    => $x_level_email_defaults['expiring_notification_admin'],
	'expiring_admin_send'                            => $WishListMemberInitialData['expiring_notification_days'],
	'expiring_admin_subject'                         => $x_level_email_defaults['expiring_admin_subject'] ?: $WishListMemberInitialData['expiring_admin_subject'],
	'expiring_admin_message'                         => $x_level_email_defaults['expiring_admin_message'] ?: $WishListMemberInitialData['expiring_admin_message'],

	'expiring_notification_user'                     => $x_level_email_defaults['expiring_notification'],
	'expiring_user_send'                             => $WishListMemberInitialData['expiring_notification_days'],
	'expiring_user_sender_name'                      => $x_level_email_defaults['email_sender_name'],
	'expiring_user_sender_email'                     => $x_level_email_defaults['email_sender_address'],
	'expiring_user_subject'                          => $x_level_email_defaults['expiringnotification_email_subject'] ?: $WishListMemberInitialData['expiringnotification_email_subject'],
	'expiring_user_message'                          => $x_level_email_defaults['expiringnotification_email_message'] ?: $WishListMemberInitialData['expiringnotification_email_message'],

	'cancel_notification'                            => $x_level_email_defaults['cancel_notification'],
	'cancel_sender_name'                             => $x_level_email_defaults['email_sender_name'],
	'cancel_sender_email'                            => $x_level_email_defaults['email_sender_address'],
	'cancel_subject'                                 => $x_level_email_defaults['cancel_email_subject'] ?: $WishListMemberInitialData['cancel_email_subject'],
	'cancel_message'                                 => $x_level_email_defaults['cancel_email_message'] ?: $WishListMemberInitialData['cancel_email_message'],

	'uncancel_notification'                          => $x_level_email_defaults['uncancel_notification'],
	'uncancel_sender_name'                           => $x_level_email_defaults['email_sender_name'],
	'uncancel_sender_email'                          => $x_level_email_defaults['email_sender_address'],
	'uncancel_subject'                               => $x_level_email_defaults['uncancel_email_subject'] ?: $WishListMemberInitialData['uncancel_email_subject'],
	'uncancel_message'                               => $x_level_email_defaults['uncancel_email_message'] ?: $WishListMemberInitialData['uncancel_email_message'],
);
