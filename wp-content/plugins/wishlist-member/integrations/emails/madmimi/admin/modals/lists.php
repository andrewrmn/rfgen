<?php
foreach ( $wpm_levels as $lid => $level ) :
	$level     = (object) $level;
	$level->id = $lid;
	?>
<div
	data-process="modal"
	id="madmimi-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="madmimi-lists-modal-<?php echo $level->id; ?>"
	data-label="madmimi-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-classes="modal-lg"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<ul class="nav nav-tabs">
			<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#madmimi-ar-when-added-<?php echo $level->id; ?>"><?php _e( 'When Added', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#madmimi-ar-when-removed-<?php echo $level->id; ?>"><?php _e( 'When Removed', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#madmimi-ar-when-cancelled-<?php echo $level->id; ?>"><?php _e( 'When Cancelled', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#madmimi-ar-when-uncancelled-<?php echo $level->id; ?>"><?php _e( 'When Uncancelled', 'wishlist-member' ); ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="row tab-pane active" id="madmimi-ar-when-added-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'madmimi-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][added][add]',
					multiple : 'multiple',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'madmimi-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][added][remove]',
					multiple : 'multiple',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
			</div>
			<div class="row tab-pane" id="madmimi-ar-when-removed-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'madmimi-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][removed][add]',
					multiple : 'multiple',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'madmimi-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][removed][remove]',
					multiple : 'multiple',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
			</div>
			<div class="row tab-pane" id="madmimi-ar-when-cancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'madmimi-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][cancelled][add]',
					multiple : 'multiple',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'madmimi-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][cancelled][remove]',
					multiple : 'multiple',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
			</div>
			<div class="row tab-pane" id="madmimi-ar-when-uncancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'madmimi-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][uncancelled][add]',
					multiple : 'multiple',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'madmimi-lists-select',
					style : 'width: 100%',
					name : 'list_actions[<?php echo $level->id; ?>][uncancelled][remove]',
					multiple : 'multiple',
					column : 'col-12',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
			</div>
		</div>
	</div>
</div>
	<?php
endforeach;
?>
