<?php $country_list = $this->get_country_list(); ?>
<div class="content-wrapper">
	<h3 class="main-title">Administrator Information <?php $this->tooltip(__('This information is optional. However, it will be required in order to send Email Broadcasts. <br><br>CAN-SPAM requires a physical mailing address be provided in emails in order to send Email Broadcasts to members.', 'wishlist-member'), 'lg'); ?></h3>
	<form action="">
		<div class="row">
			<div class="col-lg-6 col-md-6">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Name', 'wishlist-member' ); ?>',
						label_extra: '(required)',
						name : '<?php $this->Option('email_sender_name'); ?>',
						value : "<?php $this->OptionValue(); ?>",
						required : 'required'
					}
				</template>
			</div>
			<div class="col-lg-6 col-md-6">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Email', 'wishlist-member' ); ?>',
						label_extra: '(required)',
						name : '<?php $this->Option('email_sender_address'); ?>',
						value : "<?php $this->OptionValue(); ?>",
						required : 'required'
					}
				</template>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Street', 'wishlist-member' ); ?>',
						name : '<?php $this->Option('email_sender_street1'); ?>',
						value : "<?php $this->OptionValue(); ?>"
					}
				</template>
			</div>
			<div class="col-lg-12 col-md-12">
				<template class="wlm3-form-group">
					{
						name : '<?php $this->Option('email_sender_street2'); ?>',
						value : "<?php $this->OptionValue(); ?>"
					}
				</template>
			</div>
			<div class="col-lg-4 col-md-3">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'City/Town', 'wishlist-member' ); ?>',
						name : '<?php $this->Option('email_sender_city'); ?>',
						value : "<?php $this->OptionValue(); ?>"
					}
				</template>
			</div>
			<div class="col-lg-4 col-md-3">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'State/Province', 'wishlist-member' ); ?>',
						name : '<?php $this->Option('email_sender_state'); ?>',
						value : "<?php $this->OptionValue(); ?>"
					}
				</template>
			</div>
			<div class="col-lg-4 col-md-2">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Zip/Postal Code', 'wishlist-member' ); ?>',
						name : '<?php $this->Option('email_sender_zipcode'); ?>',
						value : "<?php $this->OptionValue(); ?>"
					}
				</template>
			</div>
			<div class="col-lg-3 col-md-4">
				<div class="form-group">
					<label for="">Country</label>
					<select class="form-control wlm-select" name="<?php $this->Option('email_sender_country'); ?>" style="width: 100%" data-placeholder="Select a Country">
						<option></option>
						<?php $c = $this->GetOption("email_sender_country"); ?>
						<?php foreach ($country_list as $country): ?>
							<?php $selected = ($c == $country) ? 'selected="selected"' : ''; ?>
							<option value="<?php echo $country; ?>" <?php echo $selected; ?>><?php echo $country; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="save" />
	</form>
	<div class="panel-footer -content-footer">
		<div class="row">
			<div class="col-lg-12 text-right">
				<a href="#" class="btn -primary admin-info-save">
					<i class="wlm-icons">save</i>
					<span class="text"><?php _e( 'Save', 'wishlist-member' ); ?></span>
				</a>
			</div>
		</div>
	</div>
</div>