<div class="horizontal-tabs">
	<div class="row no-gutters">
		<div class="col-12 col-md-auto">
			<div class="horizontal-tabs-sidebar">
				<ul class="nav nav-tabs -h-tabs flex-column" id="xys">
					<li class="active nav-item"><a class="active nav-link" data-toggle="tab" href="#" data-target="#loginform-logo"><?php _e( 'Logo', 'wishlist-member' ); ?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#" data-target="#loginform-box"><?php _e( 'Box', 'wishlist-member' ); ?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#" data-target="#loginform-fields"><?php _e( 'Fields', 'wishlist-member' ); ?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#" data-target="#loginform-button"><?php _e( 'Button', 'wishlist-member' ); ?></a></li>
				</ul>
			</div>
		</div>
		<div class="col">
			<div class="tab-content">
				<div class="tab-pane active" id="loginform-logo">
					<div class="row">
						<!-- Logo -->
						<template class="wlm3-form-group">
							{
								type : 'wlm3media',
								label : '<?php _e( 'Custom Logo', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_logo',
								value : <?php echo json_encode( htmlentities( $this->GetOption( 'login_styling_custom_logo' ) ) ) ?: '""'; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-8',
								group_class : 'img-uploader-big'
							}
						</template>
						<input type="hidden" name="login_styling_custom_logo_height" value="<?php echo $this->GetOption( 'login_styling_custom_logo_height' ); ?>">
						<input type="hidden" name="login_styling_custom_logo_width" value="<?php echo $this->GetOption( 'login_styling_custom_logo_width' ); ?>">
					</div>
				</div>
				<div class="tab-pane" id="loginform-box">
					<div class="row">
						<h4 class="col-12">
							<?php _e( 'Box', 'wishlist-member' ); ?>
							<hr>
						</h4>
						
						<!-- Box Alignment -->
						<template class="wlm3-form-group">
							{
								type : 'select',
								label : '<?php _e( 'Alignment', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_position',
								value : <?php echo json_encode( $this->GetOption( 'login_styling_custom_loginbox_position' ) ); ?>,
								options : [
									{ value : '', text : '<?php _e( 'Theme Default', 'wishlist-member' ) ?>' },
									{ value : '0 auto', text : '<?php _e( 'Center Aligned', 'wishlist-member' ) ?>' },
									{ value : '0 auto 0 0', text : '<?php _e( 'Left Aligned', 'wishlist-member' ) ?>' },
									
									{ value : '0 0 0 auto', text : '<?php _e( 'Right Aligned', 'wishlist-member' ) ?>' }
									
								],
								column : 'col-md-4',
								style : 'width: 100%'
							}
						</template>

						<!-- Box Width -->
						<template class="wlm3-form-group">
							{
								type : 'number',
								min : 0,
								label : '<?php _e( 'Width (px)', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_width',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_width' ) ) ); ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4',
							}
						</template>
						<div class="col-md-4 d-none d-md-block"></div>

						<!-- Box BG Color -->
						<template class="wlm3-form-group">
							{
								type : 'text',
								label : '<?php _e( 'Background Color', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_bgcolor',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_bgcolor' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4',
								class : 'wlm3colorpicker'
							}
						</template>

						<!-- Box FG Color -->
						<template class="wlm3-form-group">
							{
								type : 'text',
								label : '<?php _e( 'Text Color', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_fgcolor',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_fgcolor' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4',
								class : 'wlm3colorpicker'
							}
						</template>

						<!-- Box Text Size -->
						<template class="wlm3-form-group">
							{
								type : 'number',
								min : 0,
								label : '<?php _e( 'Text Size (px)', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_fontsize',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_fontsize' ) ) ); ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4'

							}
						</template>
					</div>
				</div>
				<div class="tab-pane" id="loginform-fields">
					<div class="row">
						<h4 class="col-12">
							<?php _e( 'Fields', 'wishlist-member' ); ?>
							<hr>
						</h4>

						<!-- Field BG Color -->
						<template class="wlm3-form-group">
							{
								type : 'text',
								label : '<?php _e( 'Background Color', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_fld_bgcolor',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_fld_bgcolor' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4',
								class : 'wlm3colorpicker'
							}
						</template>
						<!-- Field FG Color -->
						<template class="wlm3-form-group">
							{
								type : 'text',
								label : '<?php _e( 'Text Color', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_fld_fgcolor',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_fld_fgcolor' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4',
								class : 'wlm3colorpicker'
							}
						</template>
						<!-- Field Text Size -->
						<template class="wlm3-form-group">
							{
								type : 'number',
								min : 0,
								label : '<?php _e( 'Text Size (px)', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_fld_fontsize',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_fld_fontsize' ) ) ); ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4'
							}
						</template>
						<!-- Field Border Color -->
						<template class="wlm3-form-group">
							{
								type : 'text',
								label : '<?php _e( 'Border Color', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_fld_bordercolor',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_fld_bordercolor' ) ) ); ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4',
								class : 'wlm3colorpicker'
							}
						</template>
						<!-- Field Border Thickness -->
						<template class="wlm3-form-group">
							{
								type : 'number',
								min : 0,
								label : '<?php _e( 'Border Thickness (px)', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_fld_bordersize',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_fld_bordersize' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4'
							}
						</template>
						<!-- Field Rounder Corners -->
						<template class="wlm3-form-group">
							{
								type : 'number',
								min : 0,
								label : '<?php _e( 'Rounded Corners (px)', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_fld_roundness',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_fld_roundness' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4'
							}
						</template>
					</div>
				</div>
				<div class="tab-pane" id="loginform-button">
					<div class="row">
						<h4 class="col-12">
							<?php _e( 'Button', 'wishlist-member' ); ?>
							<hr>
						</h4>

						<!-- Button BG Color -->
						<template class="wlm3-form-group">
							{
								type : 'text',
								label : '<?php _e( 'Background Color', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_btn_bgcolor',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_btn_bgcolor' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4',
								class : 'wlm3colorpicker'
							}
						</template>
						<!-- Button FG Color -->
						<template class="wlm3-form-group">
							{
								type : 'text',
								label : '<?php _e( 'Text Color', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_btn_fgcolor',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_btn_fgcolor' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4',
								class : 'wlm3colorpicker'
							}
						</template>
						<!-- Button Text Size -->
						<template class="wlm3-form-group">
							{
								type : 'number',
								min : 0,
								label : '<?php _e( 'Text Size (px)', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_btn_fontsize',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_btn_fontsize' ) ) ); ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4'
							}
						</template>
						<!-- Button Border Color -->
						<template class="wlm3-form-group">
							{
								type : 'text',
								label : '<?php _e( 'Border Color', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_btn_bordercolor',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_btn_bordercolor' ) ) ); ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4',
								class : 'wlm3colorpicker'
							}
						</template>
						<!-- Button Border Thickness -->
						<template class="wlm3-form-group">
							{
								type : 'number',
								min : 0,
								label : '<?php _e( 'Border Thickness (px)', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_btn_bordersize',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_btn_bordersize' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4'
							}
						</template>
						<!-- Button Rounder Corners -->
						<template class="wlm3-form-group">
							{
								type : 'number',
								min : 0,
								label : '<?php _e( 'Rounded Corners (px)', 'wishlist-member' ); ?>',
								name : 'login_styling_custom_loginbox_btn_roundness',
								value : <?php echo json_encode( trim( $this->GetOption( 'login_styling_custom_loginbox_btn_roundness' ) )) ; ?>,
								placeholder : '<?php _e( 'Theme Default', 'wishlist-member' ); ?>',
								column : 'col-md-4'
							}
						</template>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>