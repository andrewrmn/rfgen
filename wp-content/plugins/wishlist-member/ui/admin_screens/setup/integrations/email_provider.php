<?php
	include '_integration_common.php';

	$activate_thirdparty_providers = (array) $this->GetOption('active_email_integrations');
	$ar_data = $this->GetOption('Autoresponders');
	if(wlm_arrval($ar_data, 'ARProvider')) {

		$activate_thirdparty_providers[] = $ar_data['ARProvider'];
		$activate_thirdparty_providers = array_unique($activate_thirdparty_providers);

		unset($ar_data['ARProvider']);

		$this->SaveOption('Autoresponders', $ar_data);
		$this->SaveOption('active_email_integrations', $activate_thirdparty_providers);
	}
?>
<div id="all-integrations-parent" class="show-saving">
	<div class="content-wrapper collapse <?php if($requested_integration == '*') echo 'show'; ?>" data-parent="#all-integrations-parent" id="all-integrations">
		<form action="">
			<div class="row integration-providers pt-3">
				<?php
				// load providers
				$providers = glob($this->pluginDir3.'/integrations/emails/' . strtolower( $requested_integration ), GLOB_ONLYDIR);

				// load configs
				$configs = array();
				foreach($providers AS $folder) {
					$configs[$folder] = include($folder . '/config.php');
				}
				// sort by name
				uasort($configs, function($a, $b) {
					return strnatcmp(strtolower($a['name']), strtolower($b['name']));
				});

				$thirdparty_providers = array();
				foreach([1, 0] AS $show_active) : 
				foreach($configs AS $folder => $config) :
					$thirdparty_providers[] = $config['id'];
					$active = $this->email_integration_is_active($config['id']) ? ' active ' : '';
					if(($show_active && !$active) || (!$show_active && $active)) {
						continue;
					}
					if(!$show_legacy_integrations && !$active && wlm_arrval($config, 'legacy')) continue;
					$no_settings = wlm_arrval($config,'no_settings') ? ' no-settings ' : '';
					$integration_name = empty($config['nickname']) ? $config['name'] : $config['nickname'];
				?>
				<div class="col-md-2 col-sm-3 col-xs-4 -providers" data-name="<?php echo $integration_name; ?>">
					<div id="thirdparty-provider-<?php echo $config['id']; ?>" class="integration-toggle-container text-center<?php echo $active; echo $no_settings; ?>">
						<a href="<?php echo add_query_arg(['page' => $this->MenuID, 'wl' => 'setup/integrations/email_provider/' . $config['id']], admin_url('admin.php')); ?>" class="integration-toggle <?php echo $config['id']; ?>" data-provider="<?php echo $config['id']; ?>" data-title="<?php echo $config['name']; ?>">
							<img class="img-greyscale" src='<?php echo $this->pluginURL3."/ui/images/logos/{$config['id']}-logo.png"; ?>' alt="">
							<span class="marker text-center">
								<i class="wlm-icons md-18">check</i>
							</span>
						</a>
						<h5 class="title-label"><?php echo $config['name']; ?></h5>
					</div>
				</div>
				<?php 
				endforeach;
				endforeach;
				?>
			</div>
		</form>
	</div>
	<div id="wlm3-thirdparty-provider">
		<?php

			// load active email integrations
			foreach($thirdparty_providers AS $provider) :
				if($provider != basename($requested_integration)) continue;
				$path = $this->pluginDir3.'/integrations/emails/' . strtolower( $provider );
				$config = include ($path . '/config.php');

				printf("\n<script type='text/javascript'>\nvar wlm3_integration_config = %s\n</script>\n", json_encode($config));

				$is_active = $this->email_integration_is_active($provider);
				if(!$show_legacy_integrations && !$is_active && wlm_arrval($config, 'legacy')) continue;
				$no_save = wlm_arrval($config, 'no_settings') === true;

				$config_button = sprintf('<button type="button" class="btn -primary" data-target="#configure-%s" data-toggle="modal"><i class="wlm-icons">settings</i><span>Configure</span></button>', $config['id']);
			?>
			<div id="thirdparty-provider-container-<?php echo $provider; ?>" data-parent="#all-integrations-parent" data-type="email" data-link="<?php echo wlm_arrval($config, 'link'); ?>" data-name="<?php echo wlm_arrval($config, 'name'); ?>" data-provider="<?php echo $provider; ?>" class="thirdparty-provider-container collapse">
				<div class="page-header -no-background">
					<div class="row">
						<div class="col-auto" data-provider="<?php echo $config['id']; ?>">
							<label class="switch-light switch-wlm mt-1">
								<input type="checkbox" value="1" name="toggle-thirdparty-provider" skip-save="1">
								<span>
									<span>
										<i class="wlm-icons md-18 ico-check">
										check</i>
									</span>
									<span>
										<i class="wlm-icons md-18 ico-close">
										close</i>
									</span>
									<a>
									</a>
								</span>
							</label>
						</div>
						<div class="col pl-0">
							<div class="large-form">
								<h2 class="page-title"><?php echo $config['name']; ?></h2>
							</div>
						</div>
					</div>
				</div>
				<div class="content-wrapper -active">
					<?php
						include_once( $path . '/admin.php'); // include admin interface
						$modals = glob( $path . '/admin/modals/*.php' );
						foreach( $modals AS $modal ) {
							include_once( $modal );
						}
					?>
					<div class="panel-footer -content-footer">
						<div class="row">
							<div class="col-md-12 text-right">
								<?php echo $tab_footer; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="content-wrapper -inactive">
					<div class="row">
						<div class="col-md-12">
							<h3><?php _e( 'Integration is Inactive', 'wishlist-member' ); ?></h3>
							<br>
							<p><?php _e( 'Activate this integration by clicking the toggle button above.', 'wishlist-member' ); ?></p>
							<p class="inactive-text"><a href="<?php echo $config['link']; ?>" class="inactive-link" target="_blank">Learn more about <span class="inactive-name"><?php echo $config['name']; ?></span></a></p>
						</div>
					</div>
					<div class="panel-footer -content-footer">
						<div class="col-md-12 text-right">
							<?php echo $tab_footer; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php
	$actives = json_encode(array_values($activate_thirdparty_providers));
	echo "\n<script type='text/javascript'>\n";
	echo "var activate_thirdparty_providers = $actives;\n";
	echo "var thirdparty_provider_index_format = '%s';\n";
	echo "</script>\n";
?>