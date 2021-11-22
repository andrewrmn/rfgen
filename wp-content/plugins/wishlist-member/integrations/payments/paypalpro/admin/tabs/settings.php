<form>
	<div class="row">
		<?php echo $pp_upgrade_instructions; ?>
		<div class="col-auto mb-4"><?php echo $config_button; ?></div>
	</div>
	<input type="hidden" class="-url" name="paypalprothankyou" />
	<input type="hidden" name="action" value="admin_actions" />
	<input type="hidden" name="WishListMemberAction" value="save" />
</form>