<?php
foreach ( $wpm_levels as $lid => $level ) :
	$level     = (object) $level;
	$level->id = $lid;
	?>
<div
	data-process="modal"
	id="convertkit-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="convertkit-lists-modal-<?php echo $level->id; ?>"
	data-label="convertkit-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg"
	style="display:none">
	<div class="body">
		<ul class="nav nav-tabs">
			<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#convertkit-ar-when-added-<?php echo $level->id; ?>"><?php _e( 'When Added', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#convertkit-ar-when-removed-<?php echo $level->id; ?>"><?php _e( 'When Removed', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#convertkit-ar-when-cancelled-<?php echo $level->id; ?>"><?php _e( 'When Cancelled', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#convertkit-ar-when-uncancelled-<?php echo $level->id; ?>"><?php _e( 'When Uncancelled', 'wishlist-member' ); ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="row tab-pane active" id="convertkit-ar-when-added-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'convertkit-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][added][add]',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe from all forms when member is added to this level', 'wishlist-member' ); ?>',
					type : 'checkbox',
					value : '1',
					check_value : '1',
					uncheck_value : '0',
					column : 'col-12',
					name : 'list_actions[<?php echo $level->id; ?>][added][remove]',
				}
				</template>
			</div>
			<div class="row tab-pane" id="convertkit-ar-when-removed-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'convertkit-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][removed][add]',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe from all forms when member is removed from this level', 'wishlist-member' ); ?>',
					type : 'checkbox',
					value : '1',
					check_value : '1',
					uncheck_value : '0',
					column : 'col-12',
					name : 'list_actions[<?php echo $level->id; ?>][removed][remove]',
				}
				</template>
			</div>
			<div class="row tab-pane" id="convertkit-ar-when-cancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'convertkit-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][cancelled][add]',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe from all forms when member is cancelled from this level', 'wishlist-member' ); ?>',
					type : 'checkbox',
					value : '1',
					check_value : '1',
					uncheck_value : '0',
					column : 'col-12',
					name : 'list_actions[<?php echo $level->id; ?>][cancelled][remove]',
				}
				</template>
			</div>
			<div class="row tab-pane" id="convertkit-ar-when-uncancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'convertkit-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][uncancelled][add]',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe from all forms when member is uncancelled from this level', 'wishlist-member' ); ?>',
					type : 'checkbox',
					value : '1',
					check_value : '1',
					uncheck_value : '0',
					column : 'col-12',
					name : 'list_actions[<?php echo $level->id; ?>][uncancelled][remove]',
				}
				</template>
			</div>
		</div>
	</div>
</div>
	<?php
endforeach;
?>
