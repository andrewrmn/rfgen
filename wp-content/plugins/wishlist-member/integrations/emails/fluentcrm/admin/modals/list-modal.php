<?php foreach ( $lists as $type_id => $type ) : ?>
<div data-process="modal" id="fluentcrm-list-<?php echo $type_id; ?>-template" data-id="fluentcrm-list-<?php echo $type_id; ?>" data-label="fluentcrm-list-<?php echo $type_id; ?>"
	data-title="Editing List Actions for <strong><?php echo $type['title']; ?></strong>" data-show-default-footer="1" data-classes="modal-lg modal-fluentcrm-actions" style="display:none">
	<div class="body">
		<ul class="nav nav-tabs">
			<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#fluentcrm-list-add-<?php echo $type_id; ?>"><?php _e( 'When Added to this List', 'wishlist-member' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#fluentcrm-list-remove-<?php echo $type_id; ?>"><?php _e( 'When Removed from this List', 'wishlist-member' ); ?></a></li>
		</ul>
		<div class="tab-content">
			<?php $c_actions = array( 'add', 'remove' ); ?>
			<?php foreach ( $c_actions as $action ) : ?>
			<div class="tab-pane <?php echo $action == 'add' ? 'active in' : ''; ?>" id="fluentcrm-list-<?php echo $action; ?>-<?php echo $type_id; ?>">
				<div class="horizontal-tabs">
					<div class="row no-gutters">
						<div class="col-12 col-md-auto">
							<!-- Nav tabs -->
							<div class="horizontal-tabs-sidebar" style="min-width: 120px;">
								<ul class="nav nav-tabs -h-tabs flex-column" role="tablist">
									<li role="presentation" class="nav-item">
										<a href="#<?php echo $type_id; ?>-<?php echo $action; ?>-fluentcrmlist-level" class="nav-link pp-nav-link active" aria-controls="level" role="tab" data-type="level" data-title="Levels"
											data-toggle="tab"><?php _e( 'Levels', 'wishlist-member' ); ?></a>
									</li>
									<li role="presentation" class="nav-item">
										<a href="#<?php echo $type_id; ?>-<?php echo $action; ?>-fluentcrmlist-ppp" class="nav-link pp-nav-link" aria-controls="ppp" role="tab" data-type="ppp" data-title="Pay Per Post"
											data-toggle="tab"><?php _e( 'Pay Per Post', 'wishlist-member' ); ?></a>
									</li>
								</ul>
							</div>
						</div>
						<div class="col">
							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="<?php echo $type_id; ?>-<?php echo $action; ?>-fluentcrmlist-level">
									<div class="col-md-12">
										<div class="form-group">
											<label><?php _e( 'Add to Level', 'wishlist-member' ); ?></label>
											<select class="fluentcrm-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="fluentcrm_settings[list][<?php echo $type_id; ?>][<?php echo $action; ?>][add_level][]"></select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label><?php _e( 'Cancel from Level', 'wishlist-member' ); ?></label>
											<select class="fluentcrm-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="fluentcrm_settings[list][<?php echo $type_id; ?>][<?php echo $action; ?>][cancel_level][]"></select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label><?php _e( 'Remove from Level', 'wishlist-member' ); ?></label>
											<select class="fluentcrm-levels-select" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="fluentcrm_settings[list][<?php echo $type_id; ?>][<?php echo $action; ?>][remove_level][]"></select>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="<?php echo $type_id; ?>-<?php echo $action; ?>-fluentcrmlist-ppp">
									<div class="col-md-12">
										<div class="form-group">
											<label><?php _e( 'Add Pay Per Post', 'wishlist-member' ); ?></label>
											<select class="fluentcrm-levels-select-ppp" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="fluentcrm_settings[list][<?php echo $type_id; ?>][<?php echo $action; ?>][add_ppp][]">
												<?php
														$selected = isset( $fluentcrm_settings['list'][ $type_id ][ $action ]['add_ppp'] ) ? $fluentcrm_settings['list'][ $type_id ][ $action ]['add_ppp'] : array();
												foreach ( $selected as $key => $value ) {
													$p = get_post( $value );
													if ( $p ) {
														echo "<option value='{$p->ID}'>{$p->post_title}</option>";
													}
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label><?php _e( 'Remove Pay Per Post', 'wishlist-member' ); ?></label>
											<select class="fluentcrm-levels-select-ppp" multiple="multiple" data-placeholder="Select levels..." style="width:100%" name="fluentcrm_settings[list][<?php echo $type_id; ?>][<?php echo $action; ?>][remove_ppp][]">
												<?php
														$selected = isset( $fluentcrm_settings['list'][ $type_id ][ $action ]['remove_ppp'] ) ? $fluentcrm_settings['list'][ $type_id ][ $action ]['remove_ppp'] : array();
												foreach ( $selected as $key => $value ) {
													$p = get_post( $value );
													if ( $p ) {
														echo "<option value='{$p->ID}'>{$p->post_title}</option>";
													}
												}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php endforeach; ?>
