<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://webytude.com
 * @since      1.0.0
 *
 * @package    Wt_Appointment_Booking
 * @subpackage Wt_Appointment_Booking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wt_Appointment_Booking
 * @subpackage Wt_Appointment_Booking/admin
 * @author     WebyTude <https://webytude.com>
 */
class Wt_Appointment_Email_Schedule {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * The WT directory Part of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $wt_path;

	/**
	 * The WT directory Part of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $wt_url;


	/**
	 * The WT Set admin email
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $admin_email;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		/*
		$upload = wp_upload_dir();
		$permissions = 755;
		$upload_dir = $upload['basedir'] . "/wt-log/";
		if (!is_dir($upload_dir)) mkdir($upload_dir, $permissions);
		$umask = umask($oldmask);
		$chmod = chmod($upload_dir, $permissions);
		*/

		$upload_dir = wp_upload_dir();
		// $this->wt_path = $upload_dir['basedir'] . "/wt-log/";
		// $this->wt_url = $upload_dir['baseurl'] . "/wt-log/";

		$this->wt_path = $upload_dir['basedir'] . "/ip2location/";
		$this->wt_url = $upload_dir['baseurl'] . "/ip2location/";

		$wt_appointments = get_option('wt_appointments_options');
		if( !empty( $wt_appointments['general']['admin_email'] ) ){
        	$this->admin_email = $wt_appointments['general']['admin_email'];
        }
        else{
        	$this->admin_email = get_option( 'admin_email' );
        }
	}

	public function run() {

		// register_activation_hook( __FILE__, array( $this, 'activate_wt_cron_hook' ) );
		add_filter( 'cron_schedules', array( $this, 'add_twice_in_hours' ) );

	    add_action( 'wt_daily_email_schedule', array( $this, 'wt_daily_email_schedule_fun' ));
	    add_action( 'wt_hourly_email_schedule', array( $this, 'wt_hourly_email_schedule_fun' ));
	    // add_action( 'admin_init', array( $this, 'wt_daily_email_schedule_fun' ));
	}

	
	public function add_twice_in_hours( $schedules ) {
	    $schedules['twice_in_hours'] = array(
	            'interval'  => 1800,
	            'display'   => __( 'Every 30 Minutes', 'textdomain' )
	    );
	    return $schedules;
	}

	public function activate_wt_cron_hook() {

		if ( !wp_next_scheduled( 'wt_daily_email_schedule' ) ) {
	        wp_schedule_event(time(), 'daily', 'wt_daily_email_schedule');
	    }
	    if ( !wp_next_scheduled( 'wt_hourly_email_schedule' ) ) {
		    // wp_schedule_event(time(), 'hourly', 'wt_hourly_email_schedule');
		    wp_schedule_event(time(), 'twice_in_hours', 'wt_hourly_email_schedule');
		}

	}

	/**
	 * Functionality for hourly cron check
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function wt_hourly_email_schedule_fun() {
		global $wpdb;

	    $table_appointments = $wpdb->prefix . "wt_appointments";

	    $email = new Wt_Appointment_Email( $this->plugin_name, $this->version );

	   	$current_time = current_time( 'mysql' );
	   	$format= 'Y-m-d H:i:s';
	   	// $next_time = date_i18n( $format, strtotime( $current_time . '+1 hours' ) );
	   	$next_time = date_i18n( $format, strtotime( $current_time . '+30 minutes' ) );

	    $query = "SELECT * FROM {$table_appointments} AS wr_p WHERE wr_p.app_time BETWEEN '$current_time' AND '$next_time' AND wr_p.app_status = 'upcoming' ORDER BY wr_p.app_time ASC";
	    $results = $wpdb->get_results( $query );

	    if(  !empty( $results )){
		    $wt_appointments = get_option('wt_appointments_options');
		    foreach ($results as $key => $value) {

		    	$request = array();
		        $request['subject'] = $wt_appointments['hourly']['subject'];
			    $request['heading'] = $wt_appointments['hourly']['heading'];
			    $request['body'] = apply_filters('the_content', get_option('wt_hourly_body'));

			    $event_date = date_i18n( "M d, Y", strtotime( $value->app_user_time ) );
			    $event_time = date_i18n( "h:i A", strtotime( $value->app_user_time ) );

			    $request['sent_to'] = $value->app_email;
			    $request['event_date'] = $event_date;
			    $request['event_time'] = $event_time;
			    $request['invitee_full_name'] = $value->app_name;
			    $request['event_host_name'] = "Admin";
			    // $request['save_file'] = true;

				$email->load_edm_template("", "", $request);

				// $email->wt_add_log_file("hourly", $request['sent_to']);


				$request = array();
		        $request['subject'] = $wt_appointments['hourly']['subject'];
			    $request['heading'] = $wt_appointments['hourly']['heading'].' Time Test';
			    $request['body'] = apply_filters('the_content', get_option('wt_hourly_body'));

			    $event_date = date_i18n( "M d, Y", strtotime( $value->app_time ) );
			    $event_time = date_i18n( "h:i A", strtotime( $value->app_time ) );

			    $request['sent_to'] = $this->admin_email;
			    $request['event_date'] = $event_date;
			    $request['event_time'] = $event_time;
			    $request['invitee_full_name'] = "Admin";
			    $request['event_host_name'] = "Admin";
			    // $request['save_file'] = true;
			    // $request['die'] = true;

				$email->load_edm_template("", "", $request);
				// $email->wt_add_log_file("hourly_admin", $request['sent_to']);

		    }
	    }
	}

	/**
	 * Functionality for daily cron check
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function wt_daily_email_schedule_fun() {
		global $wpdb;

		$email = new Wt_Appointment_Email( $this->plugin_name, $this->version );

	    $table_appointments = $wpdb->prefix . "wt_appointments";

	   	$current_time = current_time( 'mysql' );

	   	$next_week_start = date_i18n( 'Y-m-d 00:00:00', strtotime( $current_time . '+1 days' ) );
	   	$next_week_end = date_i18n( 'Y-m-d 23:59:00', strtotime( $current_time . '+1 days' ) );

	    $query = "SELECT * FROM {$table_appointments} AS wr_p WHERE wr_p.app_time BETWEEN '$next_week_start' AND '$next_week_end' AND wr_p.app_status = 'upcoming' ORDER BY wr_p.app_time ASC";

	    $results = $wpdb->get_results( $query );

	    if(  !empty( $results )){
		    $wt_appointments = get_option('wt_appointments_options');
		    foreach ($results as $key => $value) {

		    	$request = array();
		        $request['subject'] = $wt_appointments['daily']['subject'];
			    $request['heading'] = $wt_appointments['daily']['heading'];
			    $request['body'] = apply_filters('the_content', get_option('wt_daily_body'));

			    $event_date = date_i18n( "M d, Y", strtotime( $value->app_user_time ) );
			    $event_time = date_i18n( "h:i A", strtotime( $value->app_user_time ) );

			    $request['sent_to'] = $value->app_email;
			    $request['event_date'] = $event_date;
			    $request['event_time'] = $event_time;
			    $request['invitee_full_name'] = $value->app_name;
			    $request['event_host_name'] = "Admin";
			    // $request['save_file'] = true;
			    // $request['die'] = true;

				$email->load_edm_template("", "", $request);

				// $email->wt_add_log_file("daily", $request['sent_to']);

				$request = array();
		        $request['subject'] = $wt_appointments['daily']['subject'];
			    $request['heading'] = $wt_appointments['daily']['heading'].' Time Test';
			    $request['body'] = apply_filters('the_content', get_option('wt_daily_body'));

			    $event_date = date_i18n( "M d, Y", strtotime( $value->app_time ) );
			    $event_time = date_i18n( "h:i A", strtotime( $value->app_time ) );

			    $request['sent_to'] = $this->admin_email;
			    $request['event_date'] = $event_date;
			    $request['event_time'] = $event_time;
			    $request['invitee_full_name'] = "Admin";
			    $request['event_host_name'] = "Admin";
			    // $request['save_file'] = true;
			    
				$email->load_edm_template("", "", $request);
				// $email->wt_add_log_file("daily_admin", $request['sent_to']);

		    }
	    }
	}
 
}
