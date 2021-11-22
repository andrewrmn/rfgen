<?php
foreach ( $wpm_levels as $lid => $level ) :
	$level     = (object) $level;
	$level->id = $lid;
	?>
<div
	data-process="modal"
	id="mailpoet-lists-modal-<?php echo $level->id; ?>-template"
	data-id="mailpoet-lists-modal-<?php echo $level->id; ?>"
	data-label="mailpoet-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg"
	style="display:none">
	<div class="body">
		<ul class="nav nav-tabs">
			<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#mailpoet-ar-when-added-<?php echo $level->id; ?>"><?php _e( 'When Added', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mailpoet-ar-when-removed-<?php echo $level->id; ?>"><?php _e( 'When Removed', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mailpoet-ar-when-cancelled-<?php echo $level->id; ?>"><?php _e( 'When Cancelled', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mailpoet-ar-when-uncancelled-<?php echo $level->id; ?>"><?php _e( 'When Uncancelled', 'wishlist-member' ); ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="row tab-pane active" id="mailpoet-ar-when-added-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					xmultiple : 'xmultiple',
					class : 'mailpoet-lists-select',
					style : 'width: 100%',
					name : 'lists[<?php echo $level->id; ?>][added][add]',
					column : 'col-12',
					'data-mirror-value' : '#mailpoet-lists-added-add-<?php echo $level->id; ?>',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from List', 'wishlist-member' ); ?>',
					type : 'select',
					xmultiple : 'xmultiple',
					class : 'mailpoet-lists-select',
					style : 'width: 100%',
					name : 'lists[<?php echo $level->id; ?>][added][remove]',
					column : 'col-12',
					'data-mirror-value' : '#mailpoet-lists-added-remove-<?php echo $level->id; ?>',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
			</div>
			<div class="row tab-pane" id="mailpoet-ar-when-removed-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					xmultiple : 'xmultiple',
					class : 'mailpoet-lists-select',
					style : 'width: 100%',
					name : 'lists[<?php echo $level->id; ?>][removed][add]',
					column : 'col-12',
					'data-mirror-value' : '#mailpoet-lists-removed-add-<?php echo $level->id; ?>',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from List', 'wishlist-member' ); ?>',
					type : 'select',
					xmultiple : 'xmultiple',
					class : 'mailpoet-lists-select',
					style : 'width: 100%',
					name : 'lists[<?php echo $level->id; ?>][removed][remove]',
					column : 'col-12',
					'data-mirror-value' : '#mailpoet-lists-removed-remove-<?php echo $level->id; ?>',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
			</div>
			<div class="row tab-pane" id="mailpoet-ar-when-cancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					xmultiple : 'xmultiple',
					class : 'mailpoet-lists-select',
					style : 'width: 100%',
					name : 'lists[<?php echo $level->id; ?>][cancelled][add]',
					column : 'col-12',
					'data-mirror-value' : '#mailpoet-lists-cancelled-add-<?php echo $level->id; ?>',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from List', 'wishlist-member' ); ?>',
					type : 'select',
					xmultiple : 'xmultiple',
					class : 'mailpoet-lists-select',
					style : 'width: 100%',
					name : 'lists[<?php echo $level->id; ?>][cancelled][remove]',
					column : 'col-12',
					'data-mirror-value' : '#mailpoet-lists-cancelled-remove-<?php echo $level->id; ?>',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
			</div>
			<div class="row tab-pane" id="mailpoet-ar-when-uncancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to List', 'wishlist-member' ); ?>',
					type : 'select',
					xmultiple : 'xmultiple',
					class : 'mailpoet-lists-select',
					style : 'width: 100%',
					name : 'lists[<?php echo $level->id; ?>][uncancelled][add]',
					column : 'col-12',
					'data-mirror-value' : '#mailpoet-lists-uncancelled-add-<?php echo $level->id; ?>',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from List', 'wishlist-member' ); ?>',
					type : 'select',
					xmultiple : 'xmultiple',
					class : 'mailpoet-lists-select',
					style : 'width: 100%',
					name : 'lists[<?php echo $level->id; ?>][uncancelled][remove]',
					column : 'col-12',
					'data-mirror-value' : '#mailpoet-lists-uncancelled-remove-<?php echo $level->id; ?>',
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
