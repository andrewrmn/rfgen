<div style="display: none">
<div id="regform-<?php echo $id; ?>" class="wlm-regform container-rs regform <?php echo $additional_classes; ?>">
	<div class="regform-container row-rs">
		<div class="col-12 regform-header">
			<?php if ( ! empty( $logo ) ) : ?>
				<img class="regform-logo" src="<?php echo $logo; ?>"></img>
			<?php endif; ?>
			<p class="heading-2"><?php echo stripslashes( $heading ); ?></p>

			<?php if ( ! is_user_logged_in() && $showlogin ) : ?>
			<p class="regform-login-link-holder">
				<?php _e( 'Existing users please <a href="" class="regform-open-login">login</a> before purchasing ', 'wishlist-member' ); ?>
			</p>
			<?php endif; ?>
		</div>

		<?php if ( wlm_arrval( $_GET, 'status' ) == 'fail' ) : ?>
			<div class="regform-error">
				<p>
					<?php
					if ( isset( $_GET['status'] ) && $_GET['status'] == 'fail' ) {
						_e( 'An error has occured while processing payment, please try again', 'wishlist-member' );}
					?>
					<?php
					if ( ! empty( $_GET['reason'] ) ) {
						echo '<br/>Reason: ' . strip_tags( wlm_arrval( $_GET, 'reason' ) );}
					?>
				</p>
			</div>
		<?php endif; ?>
		
		<div class="regform-new">
			<form action="<?php echo $form_action; ?>" class="regform-form" id="regform-form-<?php echo str_replace( '-', '', $data['sc_details']['sku'] ); ?>" method="post">
			<?php if ( ! empty( $panel_beforetext ) ) : ?>
				<div class="col-12 regform-description">
					<p class="regform-aftertext"><?php echo $panel_beforetext; ?></p>
				</div>
			<?php endif; ?>
			<?php
			foreach ( $fields as $f ) {
				$colsize = $f['col'] ?: 'col-6';
				switch ( $f['type'] ) {
					case 'heading':
						printf( '<p class="heading-3">%s</p>', $f['text'] );
						break;
					case 'hidden':
						echo sprintf( '<input type="hidden" name="%s" value="%s"/>%s', $f['name'], $f['value'], "\n" );
						break;
					case 'text':
						echo sprintf(
							'<div class="txt-fld %6$s %1$s"><label for="%1$s">%2$s</label><input id=""'
							. ' class="regform-%1$s %5$s" name="%1$s" type="text" placeholder="%3$s" value="%4$s" /><span class="error_text">%7$s</span></div>',
							$f['name'],
							$f['label'],
							$f['placeholder'] ?: $f['label'],
							$f['value'],
							$f['class'],
							$colsize,
							$f['error_text']
						);
						break;
					case 'select':
						$options = array();
						foreach ( $f['value'] as $k => $v ) {
							if ( is_numeric( $k ) ) {
								$options[] = sprintf( '<option>%s</option>', $v );
							} else {
								$options[] = sprintf( '<option value="%s">%s</option>', htmlentities( $k ), $v );
							}
						}

						echo sprintf(
							'<div class="txt-fld %6$s %1$s"><label for="%1$s">%2$s</label><select id="" class="regform-%1$s %5$s" name="%1$s" placeholder="%3$s">%4$s</select></div>',
							$f['name'],
							$f['label'],
							$f['placeholder'] ?: $f['label'],
							implode( '', $options ),
							$f['class'],
							$colsize
						);
						break;
					case 'cc_fields':
						$cc_has = (array) $f['has'];

						if ( isset( $data['sc'] ) && $data['sc'] == 'stripe' ) {
							$sku = str_replace( '-', '', $data['sc_details']['sku'] );
							// For Stripe we use Stripe Elements which generates the CC input fields..
							if ( ! $data['sc_details']['stripe_payment_method_id'] ) {
								?>
							<div class="wlm-stripe-form-row" >
								<div id="card-element-<?php echo $sku; ?>" class="card-element" style="height: 40px;
								padding: 10px 12px;	border: 1px solid transparent;border-radius: 4px; background-color: white;
								box-shadow: 0 1px 3px 0 #e6ebf1;	-webkit-transition: box-shadow 150ms ease;transition: 
								box-shadow 150ms ease;">
									 <!-- A Stripe Element will be inserted here. -->
								</div>
							<!-- Used to display form errors. -->
							<div id="card-errors-<?php echo $sku; ?>" role="alert" class="regform-error" 
							style="display:none;"></div>
							</div>
								<?php
							} else {
								echo '<div class="txt-fld col-12">';
								echo __( 'Card that will be used: ', 'wishlist-member' ) . '<b>' . strtoupper( $data['sc_details']['stripe_card_brand'] ) . '</b> card ending in ' . $data['sc_details']['stripe_card_last4'] . '<br>';
								echo '</div><br>';
							}
						} else {
							if ( in_array( 'cc_type', $cc_has ) ) {
								$options   = array();
								$options[] = sprintf( '<option value="%s">%s</option>', 'Visa', __( 'Visa', 'wishlist-member' ) );
								$options[] = sprintf( '<option value="%s">%s</option>', 'MasterCard', __( 'MasterCard', 'wishlist-member' ) );
								$options[] = sprintf( '<option value="%s">%s</option>', 'Discover', __( 'Discover', 'wishlist-member' ) );
								$options[] = sprintf( '<option value="%s">%s</option>', 'Amex', __( 'American Express', 'wishlist-member' ) );

								echo sprintf(
									'<div class="txt-fld col-3"><label>%1$s</label><select name="cc_type">%2$s</select></div>',
									__( 'Card Type', 'wishlist-member' ),
									implode( '', $options )
								);
							}

							// card number
							echo sprintf(
								'<div class="txt-fld col-4"><label>%1$s</label><input type="text" autocomplete="false" class="regform-cardnumber" name="cc_number" placeholder="●●●● ●●●● ●●●● ●●●●"></div>',
								__( 'Card Number', 'wishlist-member' )
							);

							// card expiration
							// echo '<div class="col-5"> <div class="row-rs">';
							$meta_class = in_array( 'cc_cvc', $cc_has ) ? '' : '';
							echo sprintf(
								'<div class="col-3 col-6-sm"><div class="txt-fld expires %2$s"><label>%1$s</label><input autocomplete="false" placeholder="MM" maxlength="2"  class="regform-expmonth floated-input" name="cc_expmonth" type="text" /><input autocomplete="false" placeholder="YY" maxlength="2"  class="regform-expyear floated-input" name="cc_expyear" type="text" /></div></div>
	',
								__( 'Expires', 'wishlist-member' ),
								$meta_class
							);

							// card cvc
							if ( in_array( 'cc_cvc', $cc_has ) ) {
								echo sprintf(
									'<div class="txt-fld code col-2 col-6-sm"><label>%1$s</label><input autocomplete="false" placeholder="CVC" maxlength="4"  class="regform-cvc" name="cc_cvc" type="text" /></div>',
									__( 'Card Code', 'wishlist-member' )
								);
							}
							// echo '</div></div>';
						}
						break;
				}
			}
			?>

			<?php if ( ! empty( $panel_aftertext ) ) : ?>
				<div class="col-12 regform-description">
					<p class="regform-aftertext"><?php echo $panel_aftertext; ?></p>
				</div>
			<?php endif; ?>
			<div class="btn-fld col-12">
				<?php
				if ( isset( $data['sc'] ) ) :
					switch ( $data['sc'] ) {
						case 'stripe': // Add Description of payment and separate button from the amounnt description
							$stripe_cur = $data['sc_details']['currency'];
							if ( $data['sc_details']['is_subscription'] ) { // If subscription then include the interval of the payment

								// @since 3.6 display dropdown if stripe is configured to have multiple plans for a level
								$show_options = count( $data['sc_details']['plan_details'] ) > 1;
								if ( $show_options ) {
									$plan_options = sprintf( '<option value="">%s</option>', __( 'Select a Plan', 'wishlist-member' ) );
								}

								foreach ( $data['sc_details']['plan_details'] as $plan_details ) {
									$stripe_cur     = strtoupper( $plan_details->currency ?: $stripe_cur );
									$xamt           = number_format( $plan_details->unit_amount / 100, 2, '.', '' );
									if( $plan_details->recurring ) {
										$interval_count = $plan_details->recurring->interval_count;
										$interval       = ucwords( strtolower( $plan_details->recurring->interval ) );

										$every_text = __( 'Every', 'wishlist-member' );
										switch($interval) {
											case 'Day':
												$interval_text = __( 'Day', 'wishlist-member' );
												break;
											case 'Week':
												$interval_text = __( 'Week', 'wishlist-member' );
												break;	
											case 'Month':
												$interval_text = __( 'Month', 'wishlist-member' );
												break;
											case 'Year':
												$interval_text = __( 'Year', 'wishlist-member' );
												break;
										}

										if ( $interval_count == 1 ) {
											$pay_desc = sprintf( '%s %s %s %s', $stripe_cur, $xamt, $every_text, $interval_text );
										} else {
											$pay_desc = sprintf( '%s %s %s %d %ss', $stripe_cur, $xamt, $every_text, $interval_count, $interval_text );
										}
									} else {
										$pay_desc = sprintf( '%s %s One time', $stripe_cur, $xamt );
									}

									// @since 3.6 prepare dropdown options
									if ( $show_options ) {
										$plan_options .= sprintf( '<option value="%s">%s</option>', $plan_details->id, $pay_desc );
									}
								}

								// @since 3.6 generate dropdown select input
								if ( $show_options ) {
									$pay_desc = sprintf( '<span style="display: inline">' . __( 'Payment Plan', 'wishlist-member' ) . '&nbsp; </span><select name="stripe_plan" class="regform-payment_plan">%s</select>', $plan_options );
									$product_price = '';
								} else {
									$product_price = $pay_desc;
								}
							} else {
								$pay_desc = $stripe_cur . ' ' . number_format( $data['sc_details']['amt'], 2, '.', '' );
							}
							echo '
									<div style="float:right;"><button class="regform-button" name="regform-button" data-text="' . $data['sc_details']['panel_btn_label'] . '" data-price="' . trim( $product_price ) . '">' . $data['sc_details']['panel_btn_label'] . '</button></div>
									<div class="btn-fld-info" style="float:left; text-align: left; white-space: nowrap">' . $pay_desc . '</div>
									';
							break;
					}
					?>
					</div>
				<?php else : ?>
					<div class="row-rs">
						<button class="regform-button col-4" name="regform-button"><?php echo $panel_button_label; ?></button>
						<div class="btn-fld-info col-8">

							<?php if ( $data['payment_description'] ) : ?>
								<?php echo $data['payment_description']; ?>
							<?php elseif ( $amt || $amount ) : ?>
								<?php echo $currency; ?> <?php echo number_format( $amt ?: $amount, 2, '.', '' ); ?>
							<?php endif; ?>
						</div>						
					</div>
					<button style="display:none" name = "product_price" id ="amount" value=""><?php echo $button_label; ?></button>				
				<?php endif; ?>
			</div>
			</form>
		</div>

		<?php if ( ! is_user_logged_in() ) : ?>
		<div class="regform-login" style="display: none;">
			<form method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">
				<div class="txt-fld col-12">
					<label for=""><?php _e( 'Username:', 'wishlist-member' ); ?></label>
					<input id="" class="regform-username" name="log" type="text" placeholder="Username" />
				</div>
				<div class="txt-fld col-12">
					<label for=""><?php _e( 'Password:', 'wishlist-member' ); ?></label>
					<input id="" class="regform-password" name="pwd" type="password" placeholder="************" />
				</div>
				<input type="hidden" name="wlm_redirect_to" value="<?php echo get_permalink(); ?>#regform-<?php echo $id; ?>" />
				<div class="btn-fld col-12">
						<a href="" class="regform-close-login"><?php _e( 'Cancel', 'wishlist-member' ); ?></a>
					<button class="regform-button"><?php _e( 'Login', 'wishlist-member' ); ?></button>
				</div>
			</form>
		</div>
		<?php endif; ?>
	</div>
</div>
</div>
