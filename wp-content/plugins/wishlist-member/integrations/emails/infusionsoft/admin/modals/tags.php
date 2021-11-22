<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="infusionsoft-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="infusionsoft-lists-modal-<?php echo $level->id; ?>"
	data-label="infusionsoft-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Tags for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">	
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#infusionsoft-ar-when-added-<?php echo $level->id; ?>"><?php _e( 'When Added', 'wishlist-member' ); ?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#infusionsoft-ar-when-removed-<?php echo $level->id; ?>"><?php _e( 'When Removed', 'wishlist-member' ); ?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#infusionsoft-ar-when-cancelled-<?php echo $level->id; ?>"><?php _e( 'When Cancelled', 'wishlist-member' ); ?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#infusionsoft-ar-when-uncancelled-<?php echo $level->id; ?>"><?php _e( 'When Uncancelled', 'wishlist-member' ); ?></a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<div class="row tab-pane active in" id="infusionsoft-ar-when-added-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Apply Tags:', 'wishlist-member' ); ?>',
						column : 'col-12',
						type : 'select',
						multiple : 'multiple',
						name : 'istags_add_app[<?php echo $level->id; ?>][]',
						class : 'infusionsoft-tags',
						grouped : true,
						'data-placeholder' : '<?php _e( 'Select tags...', 'wishlist-member' ); ?>',
						style : 'width: 100%',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Remove Tags:', 'wishlist-member' ); ?>',
						column : 'col-12',
						type : 'select',
						multiple : 'multiple',
						name : 'istags_add_rem[<?php echo $level->id; ?>][]',
						class : 'infusionsoft-tags',
						grouped : true,
						'data-placeholder' : '<?php _e( 'Select tags...', 'wishlist-member' ); ?>',
						style : 'width: 100%',
					}
				</template>			
			</div>
			<div class="row tab-pane" id="infusionsoft-ar-when-removed-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Apply Tags:', 'wishlist-member' ); ?>',
						column : 'col-12',
						type : 'select',
						multiple : 'multiple',
						name : 'istags_remove_app[<?php echo $level->id; ?>][]',
						class : 'infusionsoft-tags',
						grouped : true,
						'data-placeholder' : '<?php _e( 'Select tags...', 'wishlist-member' ); ?>',
						style : 'width: 100%',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Remove Tags:', 'wishlist-member' ); ?>',
						column : 'col-12',
						type : 'select',
						multiple : 'multiple',
						name : 'istags_remove_rem[<?php echo $level->id; ?>][]',
						class : 'infusionsoft-tags',
						grouped : true,
						'data-placeholder' : '<?php _e( 'Select tags...', 'wishlist-member' ); ?>',
						style : 'width: 100%',
					}
				</template>
			</div>
			<div class="row tab-pane" id="infusionsoft-ar-when-cancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Apply Tags:', 'wishlist-member' ); ?>',
						column : 'col-12',
						type : 'select',
						multiple : 'multiple',
						name : 'istags_cancelled_app[<?php echo $level->id; ?>][]',
						class : 'infusionsoft-tags',
						grouped : true,
						'data-placeholder' : '<?php _e( 'Select tags...', 'wishlist-member' ); ?>',
						style : 'width: 100%',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Remove Tags:', 'wishlist-member' ); ?>',
						column : 'col-12',
						type : 'select',
						multiple : 'multiple',
						name : 'istags_cancelled_rem[<?php echo $level->id; ?>][]',
						class : 'infusionsoft-tags',
						grouped : true,
						'data-placeholder' : '<?php _e( 'Select tags...', 'wishlist-member' ); ?>',
						style : 'width: 100%',
					}
				</template>
			</div>
			<div class="row tab-pane" id="infusionsoft-ar-when-uncancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Apply Tags:', 'wishlist-member' ); ?>',
						column : 'col-12',
						type : 'select',
						multiple : 'multiple',
						name : 'istags_uncancelled_app[<?php echo $level->id; ?>][]',
						class : 'infusionsoft-tags',
						grouped : true,
						'data-placeholder' : '<?php _e( 'Select tags...', 'wishlist-member' ); ?>',
						style : 'width: 100%',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Remove Tags:', 'wishlist-member' ); ?>',
						column : 'col-12',
						type : 'select',
						multiple : 'multiple',
						name : 'istags_uncancelled_rem[<?php echo $level->id; ?>][]',
						class : 'infusionsoft-tags',
						grouped : true,
						'data-placeholder' : '<?php _e( 'Select tags...', 'wishlist-member' ); ?>',
						style : 'width: 100%',
					}
				</template>
			</div>
		</div>
	</div>
</div>
<?php
endforeach;
?>
