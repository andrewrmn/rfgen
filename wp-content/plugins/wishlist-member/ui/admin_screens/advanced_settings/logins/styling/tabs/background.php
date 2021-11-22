<div class="horizontal-tabs">
	<div class="row no-gutters">
		<div class="col-12 col-md-auto">
			<div class="horizontal-tabs-sidebar">
				<ul class="nav nav-tabs -h-tabs flex-column" id="xys">
					<li class="active nav-item"><a class="active nav-link" data-toggle="tab" href="#" data-target="#background-image"><?php _e( 'Image', 'wishlist-member' ); ?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#" data-target="#background-color"><?php _e( 'Color', 'wishlist-member' ); ?></a></li>
				</ul>
			</div>
		</div>
		<div class="col">
			<div class="tab-content">
				<div class="tab-pane active" id="background-image">
					<div class="row">
						<div class="col">
							<div class="row">
								<template class="wlm3-form-group">
									{
										type : 'wlm3media',
										label : '<?php _e( 'Image', 'wishlist-member' ); ?>',
										name : 'login_styling_custom_bgimage',
										value : <?php echo json_encode( htmlentities ( $this->GetOption( 'login_styling_custom_bgimage' ) ) ) ?: '""'; ?>,
										placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ) ?>',
										column : 'col-12',
										group_class : 'img-uploader-big'
									}
								</template>
							</div>
						</div>
						<div class="col-md-4">

							<div class="row">
								<template class="wlm3-form-group">
									{
										type : 'select',
										label : '<?php _e( 'Position', 'wishlist-member' ); ?>',
										name : 'login_styling_custom_bgposition',
										value : <?php echo json_encode( $this->GetOption( 'login_styling_custom_bgposition' ) ); ?>,
										options : [
											{ value : '', text : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>' },
											{ value : 'center center', text : '<?php _e( 'Centered', 'wishlist-member' ); ?>' },
											{ value : 'left top', text : '<?php _e( 'Top Left', 'wishlist-member' ); ?>' },
											{ value : 'right top', text : '<?php _e( 'Top Right', 'wishlist-member' ); ?>' },
											{ value : 'left bottom', text : '<?php _e( 'Bottom Left', 'wishlist-member' ); ?>' },
											{ value : 'right bottom', text : '<?php _e( 'Bottom Right', 'wishlist-member' ); ?>' },
											{ value : 'center top', text : '<?php _e( 'Top Center', 'wishlist-member' ); ?>' },
											{ value : 'right center', text : '<?php _e( 'Right Center', 'wishlist-member' ); ?>' },
											{ value : 'center bottom', text : '<?php _e( 'Bottom Center', 'wishlist-member' ); ?>' },
											{ value : 'left center', text : '<?php _e( 'Left Center', 'wishlist-member' ); ?>' }
										],
										column: 'col-12',
										style: 'width: 100%'
									}
								</template>
								<template class="wlm3-form-group">
									{
										type : 'select',
										label : '<?php _e( 'Repeat', 'wishlist-member' ); ?>',
										name : 'login_styling_custom_bgrepeat',
										value : <?php echo json_encode( $this->GetOption( 'login_styling_custom_bgrepeat' ) ); ?>,
										options : [
											{ value : '', text : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>' },
											{ value : 'no-repeat', text : '<?php _e( 'Do Not Repeat', 'wishlist-member' ); ?>' },
											{ value : 'repeat', text : '<?php _e( 'Repeat', 'wishlist-member' ); ?>' },
											{ value : 'repeat-x', text : '<?php _e( 'Repeat Horizontally', 'wishlist-member' ); ?>' },
											{ value : 'repeat-y', text : '<?php _e( 'Repeat Vertically', 'wishlist-member' ); ?>' },
											{ value : 'space', text : '<?php _e( 'Smart Spacing', 'wishlist-member' ); ?>' },
										],
										column: 'col-12',
										style: 'width: 100%'
									}
								</template>
								<template class="wlm3-form-group">
									{
										type : 'select',
										label : '<?php _e( 'Size', 'wishlist-member' ); ?>',
										name : 'login_styling_custom_bgsize',
										value : <?php echo json_encode( $this->GetOption( 'login_styling_custom_bgsize' ) ); ?>,
										options : [
											{ value : '', text : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>' },
											{ value : 'auto', text : '<?php _e( 'Original Size', 'wishlist-member' ); ?>' },
											{ value : 'cover', text : '<?php _e( 'Fill', 'wishlist-member' ); ?>' },
											{ value : 'contain', text : '<?php _e( 'Fit', 'wishlist-member' ); ?>' },
											{ value : '100% 100%', text : '<?php _e( 'Stretch', 'wishlist-member' ); ?>' },
										],
										column: 'col-12',
										style: 'width: 100%'
									}
								</template>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="background-color">
					<div class="row">
						<template class="wlm3-form-group">
							{
								type : 'text',
								label : '<?php _e( 'Background Color', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_bgcolor',
								value : <?php echo trim( json_encode( $this->GetOption( 'login_styling_custom_bgcolor' ) ) ); ?>,
								column: 'col-md-4',
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								class : 'wlm3colorpicker'
							}
						</template>
					</div>
					<div class="row">
						<template class="wlm3-form-group">
							{
								type : 'select',
								label : '<?php _e( 'Background Blend Mode', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_bgblend',
								value : <?php echo json_encode( $this->GetOption( 'login_styling_custom_bgblend' ) ); ?>,
								options : [
									{ value : '', text : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>' },
									{ value : 'normal', text : '<?php _e( 'None', 'wishlist-member' ); ?>' },
									{ value : 'multiply', text : '<?php _e( 'Multiply', 'wishlist-member' ); ?>' },
									{ value : 'overlay', text : '<?php _e( 'Overlay', 'wishlist-member' ); ?>' },
									{ value : 'luminosity', text : '<?php _e( 'Luminosity', 'wishlist-member' ); ?>' },
								],
								column: 'col-md-4',
								style: 'width: 100%'
							}
						</template>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>