<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://webytude.com
 * @since      1.0.0
 *
 * @package    Wt_Appointment_Booking
 * @subpackage Wt_Appointment_Booking/public/partials
 */


$wt_appointments = get_option('wt_appointments_options');

$appointment_title = $wt_appointments['general']['appointment_title'];
if( empty( $appointment_title ) ){
	$appointment_title = __( 'Meeting schedule' );
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wt-appointment">

	<div class="siteloader">
		<div class="loader-content"></div>
	</div>
	<div class="wt-header">
		<h2><?php echo $appointment_title; ?></h2>
	</div>

	<div class="wt-calendar">
		<div class="wt-calendar-left">
			<div class="calendar"></div>
			<div class="timezone">
				<?php
				
				$data = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$this->get_ip() ));

				if( isset( $data['geoplugin_timezone'] ) && !empty( $data['geoplugin_timezone'] )){
					$system_timezone = $data['geoplugin_timezone'];
				}
				else{
					$system_timezone = get_option( 'timezone_string' );
				}
				?>
				<span class="timezone-lable">Your current Time-zone</span>
				<select class="timezone-list select2">
				<?php
					// $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);					
					//echo wp_timezone_choice( $system_timezone );

					echo '<option selected="selected" value="">' . __( 'Select a city' ) . '</option>';
					foreach (timezone_identifiers_list() as $zone) {
						echo "<option value='$zone' ".selected($system_timezone, $zone, false).">$zone</option>";
					}
				?>
			</select>
			</div>
		</div>
		<div class="times">
			<?php

			$time_start = $wt_appointments['time']['start'];
			$time_end = $wt_appointments['time']['end'];
			$time_inte = $wt_appointments['time']['inte'];
 
			$time_start = !empty( $time_start ) ? $time_start : 9;
			$time_end = !empty( $time_end ) ? $time_end : 9;
			$time_inte = !empty( $time_inte ) ? $time_inte : 30;

			$format = 'H:i';
			$time_start = $this->get_converted_timezone( $time_start, $format, $system_timezone );
			$time_end = $this->get_converted_timezone( $time_end, $format, $system_timezone );

			$times = hoursRange( $time_start, $time_end, $time_inte );
			$booked_times = $this->wt_get_booked_app_times();

			if( !empty( $times )){
				echo "<ul>";
				foreach ($times as $key => $value) {
					if( in_array($key, $booked_times) ){
						echo "<li class='booked'>
							<div class='time'><a class='cta-button-outline' href='#'>$value</a></div>
						</li>";
					}
					else{
						echo "<li>
							<div class='time'><a class='cta-button-outline' href='#'>$value</a></div>
							<div class='time-confirm'><a class='cta-button' href='#' data-time='$key' tabindex='-1'>Confirm</a></div>
						</li>";
					}
					
				}
				echo "</ul>";
			}
			?>
		</div>
	</div>
	<div class="appointments-form-blk">
		<?php
		$form_title = $wt_appointments['general']['form_title'];
		$form_button = $wt_appointments['general']['form_button'];
		if( empty( $form_title ) ){
			$form_title = __( 'Please enter the following details and click the blue button' );
		}
		if( empty( $form_button ) ){
			$form_button = __( 'Schedule Event' );
		}
		?>
		<form class="appointments-form" action="" method="post">
			<h3 class="wt-form-title"><?php echo $form_title; ?> <a href="#" class="back">Back</a> </h3>

			<input type="hidden" name="action" value="wt_take_appointments">
			<input type="hidden" name="wt-date" value="">
			<input type="hidden" name="wt-time" value="">
			<input type="hidden" name="wt-timezone" value="<?php echo $system_timezone; ?>">

			<fieldset class="input-group fieldItem">				
				<label class="input-lbl">Name</label>
				<input type="text" name="name" placeholder="Name" class="required">
			</fieldset>
			<fieldset class="input-group fieldItem">				
				<label class="input-lbl">Email</label>
				<input type="email" name="email" placeholder="Email" class="required">
			</fieldset>
			<fieldset class="input-group fieldItem">				
				<label class="input-lbl">Comments</label>
				<textarea name="comments" placeholder="Comments"></textarea>
			</fieldset>
			<?php
			$selected_zone = ''; 
			$locale = null; 
			// echo "<pre>"; print_r( wp_timezone_choice( $selected_zone, $locale ) ); echo "</pre>";
			?>
			
			<fieldset class="input-group submit-btn">		 
				<input type="submit" value="<?php echo $form_button; ?>" class="cta-button">
				<div class="ajax-message"></div>
			</fieldset>
		</form>
	</div>
</div>