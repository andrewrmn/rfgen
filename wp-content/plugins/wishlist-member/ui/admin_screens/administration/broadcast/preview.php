<div class="row broadcasts-preview">
	<div class="col-md-6">
		<div class="form-group">
			<label for="">From Name</label>
			<?php if ( $data['from_name'] ) : ?>
				<p><?php echo $data['broadcast_use_custom_sender_info'] ? $data['from_name'] : $this->GetOption('email_sender_name'); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="">From Email</label>
			<?php if ( $data['from_email'] ) : ?>
				<p><?php echo $data['broadcast_use_custom_sender_info'] ? $data['from_email'] : $this->GetOption('email_sender_address'); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<div class="col-md-12">
		<div class="form-group">
			<label for="">Email Subject</label>
			<?php if ( $data['subject'] ) : ?>
				<p><?php echo $data['subject']; ?></p>
			<?php endif; ?>
		</div>
	</div>
	<div class="col-md-12">
		<div class="form-group">
			<label for="">Email Message (<?php echo $data['sent_as']; ?>)</label>
			<p>
				<?php
					$address = array();
					$street1 = $this->GetOption('email_sender_street1');
					$street2 = $this->GetOption('email_sender_street2');
					$city = $this->GetOption('email_sender_city');
					$state = $this->GetOption('email_sender_state');
					$zip = $this->GetOption('email_sender_zipcode');
					$country = $this->GetOption('email_sender_country');
					if ( trim($street1) ) $address[] = trim($street1);
					if ( trim($street2) ) $address[] = trim($street2);
					if ( trim($city) ) $address[] = trim($city);
					if ( trim($state) ) $address[] = trim($state);
					if ( trim($zip) ) $address[] = trim($zip);
					if ( trim($country) ) $address[] = trim($country);
					// echo implode(", ", $address);

					$canspamaddress = "";
					$address = array();
					$street1 = $this->GetOption('email_sender_street1');
					$street2 = $this->GetOption('email_sender_street2');
					$city = $this->GetOption('email_sender_city');
					$state = $this->GetOption('email_sender_state');
					$zip = $this->GetOption('email_sender_zipcode');
					$country = $this->GetOption('email_sender_country');
					if ( trim($city) ) $address[] = trim($city);
					if ( trim($state) ) $address[] = trim($state);
					if ( trim($zip) ) $address[] = trim($zip);
					if ( trim($country) ) $address[] = trim($country);
					$canspamaddress = trim( $street1 ) .", ";
					if ( trim( $street2 ) != "" ) $canspamaddress .= trim( $street2 ) .", ";
					$canspamaddress .= implode(", ", $address);

					$footer = "\n\n";
					$signature = isset( $data['signature'] ) ? trim( $data['signature'] ) : '';
					if ( !empty($signature) ) $footer .= $signature ."\n\n";
					//add unsubcribe and user details link
					$footer .= sprintf(WLMCANSPAM, "XX" . '/' . substr(md5("XX" . WLMUNSUBKEY), 0, 10)) . "\n\n";
					$footer .= $canspamaddress;

					$msg = trim($data['message']);

					if ($data['sent_as'] == "html") {
						$fullmsg = $msg .wpautop($footer);
					} else {
						$fullmsg = $msg . $footer;
						// $fullmsg = nl2br(wordwrap(htmlentities($fullmsg)));
						$fullmsg = nl2br(htmlentities($fullmsg));
					}

					echo stripslashes($fullmsg);
				?>
			</p>
		</div>
		<?php if ( $data['send_to_admin'] ) : ?>
			<?php if ( $data['admin_email_sent'] ) : ?>
				<em class="pull-right mb-3 text-primary">A copy of this email was sent to (<?php echo $this->GetOption('email_sender_address') ?>)</em>
			<?php else: ?>
				<em class="pull-right mb-3 text-danger"><?php _e( 'An error occured while sending a copy of this email to the site administrator.', 'wishlist-member' ); ?></em>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
<div class="row send-to-ml">
	<div class="col-md-12">
		<div class="form-group">
			<?php if ( $data['send_to'] == "send_mlevels" ) : ?>
				<label for="">Send to Membership Level/s</label>
				<p>
					<?php
						$total_recipients = 0;
						$levels = array();
						foreach ( $data['send_mlevels'] as $lvl ) {
							if ( isset( $wpm_levels[$lvl] ) ) $levels[] = $wpm_levels[$lvl]['name'];
							$total_recipients += (int) $wpm_levels[$lvl]['count'];
						}
						echo implode( ", ", $levels) ." ({$total_recipients} recipients)";
					?>
				</p>
			<?php else: ?>
				<label for="">Sending to Saved Searches</label>
				<p>
				<?php
					if ( $data['save_searches'] != ""){
						$save_searches = $this->GetSavedSearch( $data['save_searches'] );
						if ( $save_searches ) {
							$save_searches = $save_searches[0];
							$usersearch = isset( $save_searches["search_term"] ) ? $save_searches["search_term"] : '';
							$usersearch = isset( $save_searches["usersearch"] ) ? $save_searches["usersearch"] : $usersearch;
							$wp_user_search = new \WishListMember\User_Search( $usersearch, '', '', '', '', '', 99999999, $save_searches );
							$total_recipients = $wp_user_search->total_users;
						}
						if( $total_recipients ) {
							echo $data['save_searches'] ." ({$total_recipients} recipients)";
						} else {
							echo $data['save_searches'] ." (0 recipient)";
						}
					} else {
						_e("no saved search selected", 'wishlist-member');
					}
				?>
				</p>
			<?php endif; ?>
		</div>
	</div>		
</div>
