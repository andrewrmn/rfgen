<div id="stripe-invoice-detail">
	<div class="stripe-invoice-container">
		<div class="stripe-invoice-header">
			<h2>
				<?php _e('Invoice', 'wishlist-member') ?>
				
			</h2>
			<a class="stripe-close" href="#"></a>
		</div>
		<span class="stripe-waiting" style="display:none">...</span>
		<div id="stripe-invoice-content"></div>
		<div style="float: right; padding-right: 10px;"><button class="stripe-button stripe-invoice-print"><?php _e('Print', 'wishlist-member') ?></button></div>
	</div>
</div>


<!-- fake frame for printing -->
<iframe id="print_frame" name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>


<div id="stripe-membership-status" class="wlm-stripe-membership-status" style="overflow-x:auto;">

	<table id="wlm-stripe-table1" class="wlm-stripe-subhead">
		<tr>
			<td class="wlm-stripe-subhead-title"><strong><?php _e("Membership Status", "wishlist-member") ?></strong></td>
			<td class="wlm-stripe-subhead-pastinv">
				<?php if( count( $txnids ) > 0 ) : ?>
					<strong><a class="stripe_invoices"  href="<?php echo $stripethankyou_url ?>" data-id=""><?php _e("View Past Invoices", "wishlist-member") ?> <span class="stripe-waiting" style="display:none">...</span></a></strong>
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<div id="stripe-invoice-list" class="wlm-stripe-invlist-holder"></div>
	<?php if( count( $txnids ) > 0 ) : ?>
		<table id="wlm-stripe-table3" class="wlm-stripe-sublist">
			<thead>
				<tr>
					<th class="wlm-stripe-sublist-item-head"><?php _e("Item", "wishlist-member") ?></th>
					<th class="wlm-stripe-sublist-status-head"><?php _e("Status", "wishlist-member") ?></th>
					<th class="wlm-stripe-sublist-payment-head"><?php _e("Payment Info", "wishlist-member") ?></th>
					<th class="wlm-stripe-sublist-action-head"><?php _e("Cancel", "wishlist-member") ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($txnids as $proftxn): 
					?>
					<?php $level = $wlm_user->Levels[$proftxn['level_id']]; ?>
					<?php if (!empty($proftxn['txn'])): ?>
						<tr class="wlm-stripe-sublist-row wlm-stripe-sublist-row-<?php echo $proftxn['level_id']; ?>">
							<td class="wlm-stripe-sublist-item-col">
								<?php echo $proftxn['level']['name']; ?>
							</td>
							<td class="wlm-stripe-sublist-status-col">
								<?php if($proftxn['type'] == 'membership'): ?> 
									<?php echo implode(',', $level->Status) ?> 
									<?php if($level->SequentialCancelled): ?>
										<br> <i><small>(Sequential Upgrade Stopped)</small></i>
									<?php endif; ?>

									<?php if($proftxn['subs_cancelled']): ?>
										<br> <i><small>(<?php echo $proftxn['subs_cancelled_msg']?>)</small></i>
									<?php endif; ?>
								<?php else: ?>
									
									<?php _e('Active', 'wishlist-member') ?>
								<?php endif; ?>
							</td>
							<td class="wlm-stripe-sublist-payment-col">
								<a href="#" class="update-payment-info">
									<?php if($proftxn['stripe_connected'] && !$proftxn['subs_cancelled']) : ?>
										<?php _e("Update Payment Info", 'wishlist-member') ?></a>
									<?php endif; ?>
								<div id="update-stripe-info" class="update-stripe-info">
									<form method="post" action="<?php echo $stripethankyou_url ?>" id="profile-form-credit-<?php echo $proftxn['level_id']; ?>">
										<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('stripe-do-update_payment') ?>"/>
										<input type="hidden" name="stripe_action" value="update_payment"/>
										<input type="hidden" name="wlm_level" value="<?php echo $proftxn['level_id']?>"/>
										<input type="hidden" name="redirect_to" value="<?php echo get_permalink() ?>"/>
										<input type="hidden" name="txn_id" value="<?php echo $WishListMemberInstance->Get_UserMeta($current_user->ID, 'stripe_cust_id'); ?>"/>

										<div class="wlm-stripe-form-row" >
											<div id="profile-card-element-<?php echo $proftxn['level_id']; ?>" class="profile-card-element" style="height: 40px;
											padding: 10px 12px;	border: 1px solid transparent;border-radius: 4px; background-color: white;
											box-shadow: 0 1px 3px 0 #e6ebf1;	-webkit-transition: box-shadow 150ms ease;transition: 
											box-shadow 150ms ease;">
											 	<!-- A Stripe Element will be inserted here. -->
											</div>
											<!-- Used to display form errors. -->
											<div id="profile-card-errors-<?php echo $proftxn['level_id']; ?>" role="alert" class="regform-error" style="display:none;"></div>
										</div>

										<p style="margin-top: 8px;"><input class="update-payment-info-cancel" type="submit" name="cancel" value="cancel"> <input type="submit" name="Submit" value="Save"/></p>
									</form>
								</div>
							</td>
							<td class="wlm-stripe-sublist-action-col">
								<?php if($proftxn['type'] == 'membership'): ?>
									<?php 
									if ($level->Active && !$level->SequentialCancelled && !$proftxn['subs_cancelled'] && !$proftxn['level_connected_to_purchase']): 
										if($proftxn['stripe_connected']): 
											if($proftxn['hide_cancel_button'] == 'no'):
										?>
											<form method="post" action="<?php echo $stripethankyou_url ?>">
												<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('stripe-do-cancel') ?>"/>
												<input type="hidden" name="stripe_action" value="cancel"/>
												<input type="hidden" name="wlm_level" value="<?php echo $proftxn['level_id']?>"/>
												<input type="hidden" name="redirect_to" value="<?php echo get_permalink() ?>"/>
												<input type="hidden" name="txn_id" value="<?php echo $proftxn['txn'] ?>"/>
												<input type="submit" class="stripe-cancel" name="Cancel" value="<?php _e("Cancel Subscription", "wishlist-member") ?>"/>
											</form>
										<?php 
											endif;
										else: 
											echo _e('Not connected to a Stripe Plan Purchase.', 'wishlist-member');
										endif;
									endif; ?>
								<?php else: ?>

								<?php endif; ?>
							</td>
							<!--<td><a href="#">View</a></td>-->
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<div class="wlm-stripe-empty-sublist"><?php _e('No Record Found.', 'wishlist-member') ?>
				</div>
	<?php endif; ?>
</div>
