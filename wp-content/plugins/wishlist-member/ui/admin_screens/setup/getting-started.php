<?php
$wpm_levels = $this->GetOption('wpm_levels');
$levelid = isset( $_GET["levelid"] ) ? $_GET["levelid"] : false;
if ( $levelid ) {
	$level_data = isset( $wpm_levels[ $levelid ] ) ? $wpm_levels[ $levelid ] : $this->level_defaults;
} else {
	$level_data = $this->level_defaults;
}
if( $levelid ) $level_data['id'] = $levelid;
?>
<div class="row">
	<div class="col-md-1 col-sm-1"></div>
	<div class="col-md-10 col-sm-10">
			<div class="getting-started">
				<?php if ( $levelid ): ?>
					<input type='hidden' name='levelid' value='<?php echo $levelid; ?>' />
				<?php endif; ?>
				<?php
					if ( $this->GetOption('LicenseStatus') != 1 ) {
						include( $this->pluginDir3 ."/ui/admin_screens/setup/getting-started/license.php");
					} else {
						if ( count($wpm_levels) > 0 && $levelid === false ) {
							include( $this->pluginDir3 ."/ui/admin_screens/setup/getting-started/start.php");
						} else {
							include( $this->pluginDir3 ."/ui/admin_screens/setup/getting-started/step-1.php");
						}
					}
				?>
			</div>
	</div>
	<div class="col-md-1 col-sm-1"></div>
</div>