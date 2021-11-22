<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="aweberapi-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="aweberapi-lists-modal-<?php echo $level->id; ?>"
	data-label="aweberapi-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">		
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#aweberapi-settings-<?php echo $level->id; ?>">Settings</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#aweberapi-when-added-<?php echo $level->id; ?>">When Added</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#aweberapi-when-cancelled-<?php echo $level->id; ?>">When Cancelled</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#aweberapi-when-removed-<?php echo $level->id; ?>">When Removed</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<div class="tab-pane active in" id="aweberapi-settings-<?php echo $level->id; ?>">
				<div class="row">
					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'List', 'wishlist-member' ); ?>',
							type : 'select',
							column : 'col-md-12 lists_column',
							name : 'connections[<?php echo $level->id; ?>]',
							'data-mirror-value' : '#aweberapi-lists-<?php echo $level->id; ?>',
							style : 'width: 100%',
							class : 'aweberapi-connections',
						}
					</template>

					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Ad Tracking', 'wishlist-member' ); ?>',
							column : 'col-md-4',
							type : 'text',
							name : 'ad_tracking[<?php echo $level->id; ?>]',
							'data-mirror-value' : '#aweberapi-adtracking-<?php echo $level->id; ?>',
						}
					</template>			

					<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Action if Member is Removed or Cancelled from Level', 'wishlist-member' ); ?>',
							type : 'select',
							column : 'col-md-8',
							name : 'autounsub[<?php echo $level->id; ?>]',
							'data-mirror-value' : '#aweberapi-unsubscribe-<?php echo $level->id; ?>',
							options : [
							{value : 'nothing', text : 'Do Nothing (Contact will remain on Selected List)'},
							{value : 'unsubscribe', text : 'Unsubscribe Contact from Selected List'},
							{value : 'delete', text : 'Delete Contact from Selected List'},
							],
							style : 'width: 100%',
						}
					</template>
				</div>
			</div>

			<div class="row tab-pane" id="aweberapi-when-added-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Apply Tags', 'wishlist-member' ); ?>',
						column : 'col-md-12',
						type : 'text',
						name : 'level_tag[<?php echo $level->id; ?>][added][apply]',
						placeholder : '<?php _e( 'tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>',
						tooltip : '<?php _e( 'Type in your tags separated by commas. Ex. tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>'
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Remove Tags', 'wishlist-member' ); ?>',
						column : 'col-md-12',
						type : 'text',
						name : 'level_tag[<?php echo $level->id; ?>][added][remove]',
						placeholder : '<?php _e( 'tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>',
						tooltip : '<?php _e( 'Type in your tags separated by commas. Ex. tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>'
					}
				</template>			
			</div>

			<div class="row tab-pane" id="aweberapi-when-cancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Apply Tags', 'wishlist-member' ); ?>',
						column : 'col-md-12',
						type : 'text',
						name : 'level_tag[<?php echo $level->id; ?>][cancelled][apply]',
						placeholder : '<?php _e( 'tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>',
						tooltip : '<?php _e( 'Type in your tags separated by commas. Ex. tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>'
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Remove Tags', 'wishlist-member' ); ?>',
						column : 'col-md-12',
						type : 'text',
						name : 'level_tag[<?php echo $level->id; ?>][cancelled][remove]',
						placeholder : '<?php _e( 'tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>',
						tooltip : '<?php _e( 'Type in your tags separated by commas. Ex. tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>'
					}
				</template>			
			</div>

			<div class="row tab-pane" id="aweberapi-when-removed-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Apply Tags', 'wishlist-member' ); ?>',
						column : 'col-md-12',
						type : 'text',
						name : 'level_tag[<?php echo $level->id; ?>][removed][apply]',
						placeholder : '<?php _e( 'tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>',
						tooltip : '<?php _e( 'Type in your tags separated by commas. Ex. tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>'
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Remove Tags', 'wishlist-member' ); ?>',
						column : 'col-md-12',
						type : 'text',
						name : 'level_tag[<?php echo $level->id; ?>][removed][remove]',
						placeholder : '<?php _e( 'tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>',
						tooltip : '<?php _e( 'Type in your tags separated by commas. Ex. tag 1, tag 2, tag 3 ...', 'wishlist-member' ); ?>'
					}
				</template>			
			</div>
		</div>
	</div>
</div>
<?php
endforeach;
?>
