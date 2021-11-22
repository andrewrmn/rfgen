<?php
foreach ( $wpm_levels as $lid => $level ) :
	$level     = (object) $level;
	$level->id = $lid;
	?>
<div
	data-process="modal"
	id="generic2-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="generic2-lists-modal-<?php echo $level->id; ?>"
	data-label="generic2-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-classes="modal-lg"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<ul class="nav nav-tabs">
			<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#generic2-ar-when-added-<?php echo $level->id; ?>"><?php _e( 'When Added', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#generic2-ar-when-removed-<?php echo $level->id; ?>"><?php _e( 'When Removed', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#generic2-ar-when-cancelled-<?php echo $level->id; ?>"><?php _e( 'When Cancelled', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#generic2-ar-when-uncancelled-<?php echo $level->id; ?>"><?php _e( 'When Uncancelled', 'wishlist-member' ); ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="row tab-pane active" id="generic2-ar-when-added-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to Post URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'list_actions[<?php echo $level->id; ?>][added][add]',
					column : 'col-12',
					tooltip : '<?php _e( 'The Post URL to subscribe to.', 'wishlist-member' ); ?>',
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from Post URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'list_actions[<?php echo $level->id; ?>][added][remove]',
					column : 'col-12',
					tooltip : '<?php _e( 'The Post URL to unsubscribe from.', 'wishlist-member' ); ?>',
				}
				</template>
			</div>
			<div class="row tab-pane" id="generic2-ar-when-removed-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to Post URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'list_actions[<?php echo $level->id; ?>][removed][add]',
					column : 'col-12',
					tooltip : '<?php _e( 'The Post URL to subscribe to.', 'wishlist-member' ); ?>',
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from Post URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'list_actions[<?php echo $level->id; ?>][removed][remove]',
					column : 'col-12',
					tooltip : '<?php _e( 'The Post URL to unsubscribe from.', 'wishlist-member' ); ?>',
				}
				</template>
			</div>
			<div class="row tab-pane" id="generic2-ar-when-cancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to Post URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'list_actions[<?php echo $level->id; ?>][cancelled][add]',
					column : 'col-12',
					tooltip : '<?php _e( 'The Post URL to subscribe to.', 'wishlist-member' ); ?>',
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from Post URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'list_actions[<?php echo $level->id; ?>][cancelled][remove]',
					column : 'col-12',
					tooltip : '<?php _e( 'The Post URL to unsubscribe from.', 'wishlist-member' ); ?>',
				}
				</template>
			</div>
			<div class="row tab-pane" id="generic2-ar-when-uncancelled-<?php echo $level->id; ?>">
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Add to Post URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'list_actions[<?php echo $level->id; ?>][uncancelled][add]',
					column : 'col-12',
					tooltip : '<?php _e( 'The Post URL to subscribe to.', 'wishlist-member' ); ?>',
				}
				</template>
				<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Remove from Post URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'list_actions[<?php echo $level->id; ?>][uncancelled][remove]',
					column : 'col-12',
					tooltip : '<?php _e( 'The Post URL to unsubscribe from.', 'wishlist-member' ); ?>',
				}
				</template>
			</div>
		</div>
	</div>
</div>
	<?php
endforeach;
?>
