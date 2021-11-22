<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="slack-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="slack-lists-modal-<?php echo $level->id; ?>"
	data-label="slack-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">	
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#slack-when-added-<?php echo $level->id; ?>"><?php _e( 'When Added', 'wishlist-member' ); ?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#slack-when-removed-<?php echo $level->id; ?>"><?php _e( 'When Removed', 'wishlist-member' ); ?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#slack-when-cancelled-<?php echo $level->id; ?>"><?php _e( 'When Cancelled', 'wishlist-member' ); ?></a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<?php foreach( array( 'added', 'removed', 'cancelled' ) AS $tab ) : ?>
			<div class="tab-pane <?php echo $tab == 'added' ? 'active in' : ''; ?>" id="slack-when-<?php echo $tab; ?>-<?php echo $level->id; ?>">
				<div class="row mb-3">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Enable', 'wishlist-member' ); ?>',
							type : 'toggle-adjacent',
							name : 'slack_settings[<?php echo $tab; ?>][active][<?php echo $level->id; ?>]',
							value : '1',
							uncheck_value : '0',
							column : 'col-12',
						}
					</template>
					<div class="col-12" style="<?php if( !$data[$tab]['active'][$level->id] ) echo 'display: none;'; ?>">
						<div class="row">
							<template class="wlm3-form-group">
								{
									label : '<?php _e( 'Message', 'wishlist-member' ); ?>',
									type : 'textarea',
									name : 'slack_settings[<?php echo $tab; ?>][text][<?php echo $level->id; ?>]',
									help_block : '<?php printf( __( 'Supported shortcodes: %s', 'wishlist-member' ), '{name} {fname} {lname} {email} {level} {sitename} {siteurl}'); ?>',
									column : 'col-12',
									placeholder : '{name} <?php echo $tab; ?> <?php echo $tab == 'added' ? 'to' : 'from'; ?> {level} at {sitename}',
								}
							</template>
						</div>
						<div class="row">
							<div class="col-12">
								<label><?php _e('Custom Channel','wishlist-member'); ?></label>
							</div>
						</div>
						<div class="row">
							<template class="wlm3-form-group">
								{
									type : 'toggle-adjacent-disable',
									name : 'slack_settings[<?php echo $tab; ?>][custom_channel_enabled][<?php echo $level->id; ?>]',
									value : '1',
									uncheck_value : '0',
									column : 'col-auto custom-webhook-toggle',
								}
							</template>
							<template class="wlm3-form-group">
								{
									type : 'text',
									name : 'slack_settings[<?php echo $tab; ?>][custom_channel][<?php echo $level->id; ?>]',
									column : 'col px-0',
									placeholder : 'ex. #my-channel',
								}
							</template>
							<div class="col-auto">
								<button class="btn -default -condensed slack-test-webhook" data-trigger="<?php echo $tab; ?>" data-level="<?php echo $level->id; ?>"><?php _e('Test','wishlist-member');?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php
endforeach;
?>
