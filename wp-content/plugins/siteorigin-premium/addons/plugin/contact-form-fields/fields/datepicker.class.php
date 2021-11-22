<?php

class SiteOrigin_Widget_ContactForm_Field_Datepicker extends SiteOrigin_Widget_ContactForm_Field_Base {

	protected function initialize( $options ) {
		$datetime_options = $options['field']['datetime_options'];
		$datepicker_deps  = array( 'jquery' );

		if ( ! empty( $datetime_options['show_datepicker'] ) ) {
			wp_enqueue_script(
				'moment-js',
				plugin_dir_url( __FILE__ ) . 'fields/js/moment' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js',
				array( 'jquery' ),
				false
			);
			wp_enqueue_style( 'sowb-pikaday' );
			wp_enqueue_script( 'sowb-pikaday-jquery' );
			array_push( $datepicker_deps, 'moment-js', 'sowb-pikaday' );
		}
		if ( ! empty( $datetime_options['show_timepicker'] ) ) {
			
			wp_enqueue_style( 'jquery-timepicker', plugin_dir_url( __FILE__ ) . 'css/jquery.timepicker.css' );
			$timepicker_rel_path = 'js/jquery.timepicker' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js';
			$timepicker_mtime = filemtime( plugin_dir_path( __FILE__ ) . $timepicker_rel_path );
			wp_enqueue_script(
				'jquery-timepicker',
				plugin_dir_url( __FILE__ ) . $timepicker_rel_path ,
				array( 'jquery' ),
				$timepicker_mtime
			);
			array_push( $datepicker_deps, 'jquery-timepicker' );
		}
		wp_enqueue_style( 'so-contactform-datepicker', plugin_dir_url( __FILE__ ) . 'css/so-contactform-datepicker.css' );
		$datepicker_rel_path = 'js/so-contactform-datepicker' . SITEORIGIN_PREMIUM_JS_SUFFIX . '.js';
		$datepicker_mtime = filemtime( plugin_dir_path( __FILE__ ) . $datepicker_rel_path );
		wp_enqueue_script(
			'so-contactform-datepicker',
			plugin_dir_url( __FILE__ ) . $datepicker_rel_path,
			$datepicker_deps,
			$datepicker_mtime
		);
	}

	protected function render_field( $options ) {
		$datetime_options = $options['field']['datetime_options'];
		$field_id = $options['field_id'];
		$width_class = ! ( empty( $datetime_options['show_datepicker'] ) || empty( $datetime_options['show_timepicker'] ) ) ? 'half-width' : '';

		if ( ! empty( $datetime_options['show_datepicker'] ) ) {
			$datepicker_id = $field_id . '_datepicker';
			$datepicker_label = $datetime_options['datepicker_label'];
			$disabled = array( 'days' => array(), 'dates' => array() );
			if( $datetime_options['disable_days'] === 'weekdays' ) {
				$disabled['days'] = array_map( 'strval', range( 1, 5 ) );
			}
			if( ! empty( $datetime_options['disabled_dates'] ) ) {
				$to_disable = explode( ',', $datetime_options['disabled_dates'] );
				foreach( $to_disable as $disable ) {

					if(  strtolower( $disable ) === 'today' || strtolower( $disable ) === 'tomorrow' ) {
						$is_today = strtolower( $disable ) === 'today';
						if ( $is_today ) {
							$disabled['dates'][] = date_create()->getTimestamp() * 1000;
						} else {
							$disabled['dates'][] = strtotime( '+1 days', date_create()->getTimestamp() ) * 1000;
						}

					} else if( strlen( $disable ) == 3 ) { // Day of week
						$day = date( 'w', strtotime( $disable ) );
						if ( ! in_array( $disabled, $day ) ) {
							$disabled['days'][] = $day;
						}
					} else if( strlen( $disable ) == 8 ) {
						$disabled['dates'][] = strtotime( $disable ) * 1000;// convert to milliseconds for JS epoch
					} else if( strlen( $disable ) == 17 ) {
						$range = explode( '-', $disable );
						$start = strtotime( $range[0] );
						$end = strtotime( $range[1] );
						$d = $start;
						do {
							$disabled['dates'][] = $d * 1000;
							$d += ( 60 * 60 * 24 );
						} while ( $d <= $end );
					}
				}
			}
			$datepicker_options = array(
				'disableWeekends' => $datetime_options['disable_days'] === 'weekends',
				'bound' => empty( $datetime_options['calendar_always_visible'] ),
				'disabled' => $disabled,
				'isRTL' => is_rtl(),
				'i18n' => array(
					'previousMonth' => __( 'Previous Month', 'siteorigin-premium' ),
					'nextMonth' => __( 'Next Month', 'siteorigin-premium' ),
					'months' => array(
						__( 'January', 'siteorigin-premium' ),
						__( 'February', 'siteorigin-premium' ),
						__( 'March', 'siteorigin-premium' ),
						__( 'April', 'siteorigin-premium' ),
						__( 'May', 'siteorigin-premium' ),
						__( 'June', 'siteorigin-premium' ),
						__( 'July', 'siteorigin-premium' ),
						__( 'August', 'siteorigin-premium' ),
						__( 'September', 'siteorigin-premium' ),
						__( 'October', 'siteorigin-premium' ),
						__( 'November', 'siteorigin-premium' ),
						__( 'December', 'siteorigin-premium' ),
					),
					'weekdays' => array(
						__( 'Sunday', 'siteorigin-premium' ),
						__( 'Monday', 'siteorigin-premium' ),
						__( 'Tuesday', 'siteorigin-premium' ),
						__( 'Wednesday', 'siteorigin-premium' ),
						__( 'Thursday', 'siteorigin-premium' ),
						__( 'Friday', 'siteorigin-premium' ),
						__( 'Saturday', 'siteorigin-premium' ),
					),
					'weekdaysShort' => array(
						__( 'Sun', 'siteorigin-premium' ),
						__( 'Mon', 'siteorigin-premium' ),
						__( 'Tue', 'siteorigin-premium' ),
						__( 'Wed', 'siteorigin-premium' ),
						__( 'Thu', 'siteorigin-premium' ),
						__( 'Fri', 'siteorigin-premium' ),
						__( 'Sat', 'siteorigin-premium' ),
					)
				),
				'firstDay' => ( empty( $datetime_options['first_day'] ) || $datetime_options['first_day'] === 'sunday' ) ? 0 : 1,
			);

			$premium_options = SiteOrigin_Premium_Options::single();
			$contact_form_fields_settings = $premium_options->get_settings( 'plugin/contact-form-fields' );

			if ( ! empty( $contact_form_fields_settings['year_range_min'] ) ) {
				$max_year = $contact_form_fields_settings['year_range_max'];
				$min_year = $contact_form_fields_settings['year_range_min'];
			} else {
				$max_year = date( 'Y' );
				$min_year = apply_filters( 'siteorigin_premium_datepicker_min_year', date( 'Y' ) - 110 );
			}
			$datepicker_options['yearRange'] = "$min_year,$max_year";

			if ( ! empty( $contact_form_fields_settings['use_date_format'] ) && $contact_form_fields_settings['use_date_format'] ) {
				switch( get_option( 'date_format' ) ) {
					case 'd/m/Y':
						$datepicker_options['date_format'] = 'DD/MM/YY';
						break;
					case 'm/d/Y':
						$datepicker_options['date_format'] = 'MM/DD/YY';
						break;
					case 'Y/m/d':
						$datepicker_options['date_format'] = 'YY/MM/DD';
						break;
				}
			}
			?>
			<div class="sow-form-field datepicker-container<?php echo ' ' . esc_attr( $width_class ) ?>">
				<?php if( ! empty( $datepicker_label ) ) : ?>
					<label for="<?php echo esc_attr( $datepicker_id ) ?>">
						<strong>
							<?php echo esc_html( $datepicker_label ) ?>
						</strong>
					</label>
				<?php endif; ?>
				<input type="text" id="<?php echo esc_attr( $datepicker_id ) ?>" class="so-premium-datepicker sow-text-field" data-options="<?php echo esc_attr( json_encode( $datepicker_options ) ); ?>"/>
			</div>
			<?php
		}

		if ( ! empty( $datetime_options['show_timepicker'] ) ) {
			$timepicker_id = $field_id . '_timepicker';
			$timepicker_label = $datetime_options['timepicker_label'];
			$timepicker_options = array(
				'disableTimeRanges' => $this->parseTimeRanges( $datetime_options['disabled_times'] ),
				'hideDisabledTimes' => ! empty( $datetime_options['hide_disabled_times'] ),
				'timeFormat' => ! empty( $datetime_options['timepicker_custom_format'] ) ? 'H:i' : get_option( 'time_format' ),
			);
			?>
			<div class="sow-form-field timepicker-container<?php echo ' ' . esc_attr( $width_class ) ?>">
				<?php if( ! empty( $timepicker_label ) ) : ?>
					<label for="<?php echo esc_attr( $timepicker_id ) ?>">
						<strong>
							<?php echo esc_html( $timepicker_label ) ?>
						</strong>
					</label>
				<?php endif; ?>
				<input type="text" id="<?php echo esc_attr( $timepicker_id ) ?>" class="so-premium-timepicker<?php echo ' ' . esc_attr( $width_class ) ?> sow-text-field" data-options="<?php echo esc_attr( json_encode( $timepicker_options ) ); ?>"/>
			</div>
			<?php
		}
		?>
		<input type="hidden" name="<?php echo esc_attr( $options['field_name'] ) ?>"
		       id="<?php echo esc_attr( $field_id ) ?>" value="<?php echo esc_attr( $options['value'] ) ?>"
		       class="so-contactform-datetime"/>
		<?php
	}

	/**
	 * @param $timeRanges String containing time ranges of the form 1:00pm-2:30pm,15:00-16:00
	 *
	 * Note: This converts 24h time to 12h time as the jQuery timepicker plugin doesn't support 24h time. We also allow
	 * ranges with only a start time, and then assume the end time is 30 minutes later.
	 *
	 * @return array
	 */
	private function parseTimeRanges( $timeRanges ) {
		$rangeStrings = explode( ',', $timeRanges );
		$ranges = array();
		foreach ( $rangeStrings as $rangeString ) {
			if ( empty( $rangeString ) ) continue;

			$range = explode( '-', $rangeString );

			$start_time = strtotime( $range[0] );

			if ( empty( $start_time ) ) {
				continue;
			}

			// If no end time was specified assume 30 minutes after start time.
			$end_time = count( $range ) == 1 ? strtotime( '+30 minutes', $start_time ) : strtotime( $range[1] );

			// If parsing end time failed, assume 30 minutes after start time.
			if ( empty( $end_time ) ) {
				$end_time = strtotime( '+30 minutes', $start_time );
			}

			$start_of_day = date_create( '@' . $start_time )->setTime( 0, 0, 0 )->getTimestamp();
			$end_of_day = date_create( '@' . $start_time )->setTime( 23, 59, 59 )->getTimestamp();

			// If end time of day is after start time of day then we split into 2 time ranges:
			// - from 00:00 to end_time
			// - from start_time to 23:59:59
			$day_start_time = $start_time - $start_of_day;
			$day_end_time = $end_time - $start_of_day;

			if ( $day_end_time < $day_start_time ) {
				$ranges[] = array( date( 'g:ia', $start_of_day ), date( 'g:ia', $day_end_time ) );
				$ranges[] = array( date( 'g:ia', $day_start_time ), date( 'g:ia', $end_of_day ) );

			} else {
				$ranges[] = array( date( 'g:ia', $start_time ), date( 'g:ia', $end_time ) );
			}

		}

		return $ranges;
	}

}
