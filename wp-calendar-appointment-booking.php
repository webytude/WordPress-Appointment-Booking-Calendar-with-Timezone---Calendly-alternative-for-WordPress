<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://webytude.com
 * @since             1.0.0
 * @package           Wt_Appointment_Booking
 *
 * @wordpress-plugin
 * Plugin Name:       WP Calendar Appointment Booking
 * Plugin URI:        https://webytude.com
 * Description:       This is a Wordpress plugin used for scheduling appointments, meetings, and events.
 * Version:           1.0.0
 * Author:            WebyTude
 * Author URI:        https://webytude.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wt-appointment-booking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WT_APPOINTMENT_BOOKING_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wt-appointment-booking-activator.php
 */
function activate_wt_appointment_booking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-appointment-booking-activator.php';
	Wt_Appointment_Booking_Activator::activate();


	require_once plugin_dir_path(  __FILE__ ) . 'includes/class-wt-appointment-email-schedule.php';
	Wt_Appointment_Email_Schedule::activate_wt_cron_hook();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wt-appointment-booking-deactivator.php
 */
function deactivate_wt_appointment_booking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-appointment-booking-deactivator.php';
	Wt_Appointment_Booking_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wt_appointment_booking' );
register_deactivation_hook( __FILE__, 'deactivate_wt_appointment_booking' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wt-appointment-booking.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wt_appointment_booking() {

	$plugin = new Wt_Appointment_Booking();
	$plugin->run();

}
run_wt_appointment_booking();
