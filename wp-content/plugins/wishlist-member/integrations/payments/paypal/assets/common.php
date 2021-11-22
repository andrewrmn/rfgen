<?php
$pp_upgrade_instructions = <<<STRING
<div class="col-md-12">
	<p><a href="#%1\$s-upgrade" class="hide-show"><?php _e( 'PayPal Personal Account Users Upgrade Instructions', 'wishlist-member' ); ?></a></p>
	<div class="d-none" id="%1\$s-upgrade">
		<div class="panel">
			<div class="panel-body">
				<ol style="list-style: decimal;">
					<li><p class="mb-0">Go to <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_registration-run" target="_blank">https://www.paypal.com/cgi-bin/webscr?cmd=_registration-run</a></p></li>
					<li><p class="mb-0"><?php _e( 'Click on the Upgrade Your Account link.', 'wishlist-member' ); ?></p></li>
					<li><p class="mb-0"><?php _e( 'Click on the Upgrade Now Button.', 'wishlist-member' ); ?></p></li>
					<li><p class="mb-0"><?php _e( 'If the existing account is a Personal PayPal account, there will be a choice to upgrade to a Premier or Business account.', 'wishlist-member' ); ?></p></li>
					<li><p class="mb-0"><?php _e( 'Choose to upgrade to a Premier or Business account and follow the instructions.', 'wishlist-member' ); ?></p></li>
					<li><p class="mb-0"><?php _e( 'If the existing account is a Premier PayPal account, the ability to upgrade to a Business account will be presented with instructions that can be followed.', 'wishlist-member' ); ?></p></li>
				</ol>
			</div>
		</div>
	</div>
</div>
STRING;

$pp_upgrade_instructions = sprintf($pp_upgrade_instructions, $config['id']);