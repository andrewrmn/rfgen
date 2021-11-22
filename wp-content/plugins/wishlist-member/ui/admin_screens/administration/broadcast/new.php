<div class="broadcast-new-holder" style="display:none;" >
	<div class="page-header">
		<div class="row">
			<div class="col-md-9 col-sm-9 col-xs-7">
				<h2 class="page-title">
					<?php _e( 'Create Email Broadcast', 'wishlist-member' ); ?>
				</h2>
			</div>
			<div class="col-md-3 col-sm-3 col-xs-5">
				<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
			</div>
		</div>
	</div>
	<div role="tabpanel" class="tab-pane active" id="levels-access">
		<div class="content-wrapper">
			<div class="row form-holder">
				<div class="col-12 mb-2">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Use Global Sender Info', 'wishlist-member' ); ?>',
							name  : 'broadcast_use_custom_sender_info',
							value : '0',
							uncheck_value : '1',
							type  : 'checkbox',
							checked_value : '<?php echo $this->GetOption('broadcast_use_custom_sender_info') ? 1 : 0; ?>',
							id : 'broadcast_use_custom_sender_info'
						}
					</template>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6">
					<div class="form-group">
						<label for=""><?php _e( 'From Name', 'wishlist-member' ); ?></label>
						<input type="text" name="from_name" class="form-control -custom-sender" required="required" value="<?php echo $this->GetOption('last_broadcast_sender_name') ?: $this->GetOption('email_sender_name'); ?>">
						<input type="text" disabled class="form-control -global-sender -global-sender-name d-none" value="<?php echo $this->GetOption('email_sender_name'); ?>">
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6">
					<div class="form-group">
						<label for="">From Email</label>
						<input type="text" name="from_email" class="form-control -custom-sender" required="required" value="<?php echo $this->GetOption('last_broadcast_sender_address') ?: $this->GetOption('email_sender_address'); ?>">
						<input type="text" disabled class="form-control -global-sender -global-sender-email d-none" value="<?php echo $this->GetOption('email_sender_address'); ?>">
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for=""><?php _e( 'Send To', 'wishlist-member' ); ?></label>
						<select name="send_to" class="form-control wlm-send-to wlm-select" required="required" style="width: 100%">
							<option value="send_mlevels">Membership Levels</option>
							<option value="send_search">Saved Searches</option>
						</select>
					</div>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 no-margin mb-sm-3">
					<div class="form-group wlm-levels-holder no-margin membership-level-select">
						<label for=""><?php _e( 'Membership Level', 'wishlist-member' ); ?></label>
						<select class="form-control wlm-levels" multiple="multiple" name="send_mlevels[]" required="required" style="width: 100%">
							<?php foreach ( $wpm_levels as $key => $value ) : ?>
							<option value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
							<?php endforeach; ?>
						</select>
						<div class="row no-gutters" style="margin-top: 10px;">
							<div class="col-md-6 col-sm-6">
								<template class="wlm3-form-group">
									{
										label : '<?php _e( 'Include Cancelled Levels', 'wishlist-member' ); ?>',
										name  : 'otheroptions[]',
										id : 'IncludeCancelledLevels',
										value : 'c',
										type  : 'checkbox',
									}
								</template>
							</div>
							<div class="col-md-6 col-sm-6 pl-sm-3">
								<template class="wlm3-form-group">
									{
										label : '<?php _e( 'Include Pending Levels', 'wishlist-member' ); ?>',
										name  : 'otheroptions[]',
										id : 'IncludePendingLevels',
										value : 'p',
										type  : 'checkbox',
									}
								</template>
							</div>
						</div>
					</div>
					<div class="form-group save-searches-holder d-none">
						<label for=""><?php _e( 'Saved Searches', 'wishlist-member' ); ?></label>
						<select name="save_searches" class="form-control wlm-save-searches wlm-select" style="width: 100%">
							<option value="">- Saved Searches -</option>
							<?php foreach ($this->GetAllSavedSearch() as $value): ?>
								<option value="<?php echo $value['name']; ?>"><?php echo $value['name'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="col-md-12 mt-sm-3">
					<div class="form-group">
						<label for="">
							<?php _e( 'Subject', 'wishlist-member' ); ?>
						</label>
						<input type="text" name="subject" class="form-control" required="required">
					</div>
				</div>
				<div class="col-md-12">
					<div class="message-box">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="nav-item">
								<a href="#send-text" aria-value="text" role="tab" data-toggle="tab" class="active nav-link html-text html-text-t"><?php _e( 'Text', 'wishlist-member' ); ?></a>
							</li>
							<li role="presentation" class="nav-item">
								<a href="#send-html" aria-value="html" role="tab" data-toggle="tab" class="nav-link html-text html-text-h"><?php _e( 'HTML', 'wishlist-member' ); ?></a>
							</li>
							<li class="pull-right nav-item">
									<template class="wlm3-form-group">{
										type : 'select',
										column : 'col-md-12 pull-right no-margin no-padding',
										'data-placeholder' : '<?php _e( 'Insert Merge Codes', 'wishlist-member' ); ?>',
										group_class : 'shortcode_inserter mb-0',
										style : 'width: 100%',
										options : get_merge_codes(),
										grouped: true,
										class : 'insert_text_at_caret',
										'data-target' : '[id=broadcast-message]',
									}</template>
							</li>
						</ul>
						<div class="tab-content">
							<div class="form-group">
								<input type="hidden" name="sent_as" class="broadcast-sentas" value="text" />
								<textarea style="height: auto !important" autofocus="true" cols="30" rows="12" class="form-control broadcast-message" id="broadcast-message" ></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="">Signature</label>
						<textarea style="height: auto !important; min-height: 20px;" name="signature" cols="30" rows="2" class="form-control broadcast-signature"><?php echo $signature; ?></textarea>
					</div>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<template class="wlm3-form-group">
						{
							label : '<?php printf( __( "Check this box to send this email to the site administrator\'s email address (%s)", 'wishlist-member' ), $this->GetOption('email_sender_address') ); ?>',
							name  : 'send_to_admin',
							id : 'send_to_admin',
							value : '1',
							type  : 'checkbox',
							column: 'mb-4',
						}
					</template>
				</div>
			</div>
			<div class="preview-holder">
			</div>
			<div class="panel-footer -content-footer">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<a href="#" class="btn -primary pull-right preview-broadcast-btn">
							<i class="wlm-icons">search</i>
							<span><?php _e( 'Preview Broadcast', 'wishlist-member' ); ?></span>
						</a>
						<a href="#" class="btn -primary pull-right save-broadcast-btn">
							<i class="wlm-icons">send</i>
							<span><?php _e( 'Send Broadcast', 'wishlist-member' ); ?></span>
						</a>
						<a href="#" class="btn -default pull-right edit-broadcast-btn mr-2">
							<i class="wlm-icons">edit</i>
							<span><?php _e( 'Edit Broadcast', 'wishlist-member' ); ?></span>
						</a>
						<a href="#" class="btn -bare -default pull-right cancel-broadcast-btn">
							<span><?php _e( 'Cancel', 'wishlist-member' ); ?></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>