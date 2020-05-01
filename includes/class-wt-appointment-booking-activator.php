<?php

/**
 * Fired during plugin activation
 *
 * @link       https://webytude.com
 * @since      1.0.0
 *
 * @package    Wt_Appointment_Booking
 * @subpackage Wt_Appointment_Booking/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wt_Appointment_Booking
 * @subpackage Wt_Appointment_Booking/includes
 * @author     WebyTude <https://webytude.com>
 */
class Wt_Appointment_Booking_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        	global $table_prefix, $wpdb;

        	$charset_collate = $wpdb->get_charset_collate();

        	$event_types = $table_prefix . "wt_event_types";

        	$appointments = $table_prefix . "wt_appointments";

        	$event_sql = " CREATE TABLE ". $event_types . " ( ";
                $event_sql .= "  ID int(11) NOT NULL AUTO_INCREMENT, ";
                $event_sql .= "  event_name MEDIUMTEXT DEFAULT NULL, ";
                $event_sql .= "  event_description MEDIUMTEXT DEFAULT NULL, ";
                $event_sql .= "  PRIMARY KEY  (ID) "; 
                $event_sql .= ") ".$charset_collate." ; ";

                $event_sql .= " CREATE TABLE ". $appointments . " ( ";
                $event_sql .= "  app_id int(11) NOT NULL AUTO_INCREMENT, ";
                $event_sql .= "  app_name varchar(255) DEFAULT NULL, ";
                $event_sql .= "  app_time DATETIME DEFAULT NULL, ";
                $event_sql .= "  app_email varchar(500) DEFAULT NULL, ";
                $event_sql .= "  app_comments MEDIUMTEXT DEFAULT NULL, ";
                $event_sql .= "  app_booked_time DATETIME DEFAULT NULL, ";
                $event_sql .= "  PRIMARY KEY  (app_id) "; 
                $event_sql .= ") ".$charset_collate." ; ";

                require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
                dbDelta($event_sql);
	}

}
