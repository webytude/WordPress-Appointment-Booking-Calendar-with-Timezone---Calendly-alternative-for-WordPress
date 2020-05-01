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
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wt-appointment">
	<div class="wt-header">
		<h2>Meeting schedule</h2>

	</div>
	<div class="wt-calendar">
		<div class="calendar"></div>
		<div class="times">
			<?php
			$times = hoursRange( 9, 16, 15 );
			if( !empty( $times )){
				echo "<ul>";
				foreach ($times as $key => $value) {
					echo "<li>
						<div class='time'><a class='cta-button-outline' href='#'>$value</a></div>
						<div class='time-confirm'><a class='cta-button' href='#' data-time='$key' tabindex='-1'>Confirm</a></div>
					</li>";
				}
				echo "</ul>";
			}
			?>
		</div>
	</div>
	<div class="appointments-form-blk">

		<form class="appointments-form" action="" method="post">
			<h3 class="wt-form-title">Appointment Details <a href="#" class="back">Back</a> </h3>

			<input type="hidden" name="action" value="wt_take_appointments">
			<input type="hidden" name="wt-date" value="">
			<input type="hidden" name="wt-time" value="">

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
			
			<fieldset class="input-group submit-btn">		 
				<input type="submit" value="Schedule" class="cta-button">
				<div class="ajax-message"></div>
			</fieldset>
		</form>
	</div>
</div>