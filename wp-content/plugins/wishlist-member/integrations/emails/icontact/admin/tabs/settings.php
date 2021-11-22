<div class="row">
	<div class="col-auto mb-4"><?php echo $config_button; ?></div>
	<?php echo $api_status_markup; ?>		
</div>
<div class="row api-required">
	<template class="wlm3-form-group">{label : '<?php _e( 'Account ID', 'wishlist-member' ); ?>', type : 'text', readonly : 'readonly', name : 'icaccountid', column : 'col-md-4'}</template>
	<template class="wlm3-form-group">{label : '<?php _e( 'Folder', 'wishlist-member' ); ?>', type : 'select', name : 'icfolderid', column : 'col-md-4'}</template>
</div>