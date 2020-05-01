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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		
		$upload = wp_upload_dir();
		$permissions = 755;
		$upload_dir = $upload['basedir'] . "/wt-log/";
		if (!is_dir($upload_dir)) mkdir($upload_dir, $permissions);
		$umask = umask($oldmask);
		$chmod = chmod($upload_dir, $permissions);

		$upload_dir = wp_upload_dir();
		$this->wt_path = $upload_dir['basedir'] . "/wt-log/";
		$this->wt_url = $upload_dir['baseurl'] . "/wt-log/";
	}

	public function run() {

		// register_activation_hook( __FILE__, array( $this, 'activate_wt_cron_hook' ) );

	    add_action( 'wt_daily_email_schedule', array( $this, 'wt_daily_email_schedule_fun' ));
	    add_action( 'wt_hourly_email_schedule', array( $this, 'wt_hourly_email_schedule_fun' ));
	    // add_action( 'admin_init', array( $this, 'wt_daily_email_schedule_fun' ));
	}

	public function activate_wt_cron_hook() {

		if ( !wp_next_scheduled( 'wt_daily_email_schedule' ) ) {
	        wp_schedule_event(time(), 'daily', 'wt_daily_email_schedule');
	    }
	    if ( !wp_next_scheduled( 'wt_hourly_email_schedule' ) ) {
		    wp_schedule_event(time(), 'hourly', 'wt_hourly_email_schedule');
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

	   	$current_time = current_time( 'mysql' );
	   	$format= 'Y-m-d H:i:s';
	   	$next_time = date_i18n( $format, strtotime( $current_time . '+1 hours' ) );

	    $query = "SELECT * FROM {$table_appointments} AS wr_p WHERE wr_p.app_time BETWEEN '$current_time' AND '$next_time' ORDER BY wr_p.app_time ASC";

	    
	    $results = $wpdb->get_results( $query );


	    if(  !empty( $results )){
		    foreach ($results as $key => $value) {

		    	$fullname = $value->app_name;
		    	$body = "
		    	Hi, $fullname<br>
			    	Your Appointment are as below<br>
			    	Time:	{$value->app_time}
			    ";

		    	$request = array(
			    	'subject' => "Appointment reminder",
			    	'heading' => "Appointment reminder",
			    	'body' => $body,
			    	'fullname' => $value->app_name,
			    	'sent_to' => $value->app_email,
			    );

			    $this->load_edm_template("", '', $request);
			    $this->wt_add_log_file("hourly", $value->app_email);
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
	    $table_appointments = $wpdb->prefix . "wt_appointments";

	   	$current_time = current_time( 'mysql' );

	   	$next_week_start = date_i18n( 'Y-m-d 00:00:00', strtotime( $current_time . '+7 days' ) );
	   	$next_week_end = date_i18n( 'Y-m-d 23:59:00', strtotime( $current_time . '+7 days' ) );

	    $query = "SELECT * FROM {$table_appointments} AS wr_p WHERE wr_p.app_time BETWEEN '$next_week_start' AND '$next_week_end' ORDER BY wr_p.app_time ASC";

	    
	    $results = $wpdb->get_results( $query );


	    if(  !empty( $results )){
		    foreach ($results as $key => $value) {

		    	$fullname = $value->app_name;
		    	$body = "
		    	Hi, $fullname<br>
			    	Your Appointment are as below<br>
			    	Time:	{$value->app_time}
			    ";

		    	$request = array(
			    	'subject' => "Appointment reminder",
			    	'heading' => "Appointment reminder",
			    	'body' => $body,
			    	'fullname' => $value->app_name,
			    	'sent_to' => $value->app_email,
			    );

			    $this->load_edm_template("", '', $request);
			    $this->wt_add_log_file("daily", $value->app_email);
		    }
	    }
	}

	public function wt_add_log_file($name, $text){

        $date = date("d_g");
        $file = $this->wt_path."/{$name}-{$date}.txt"; 
        $myfile = fopen($file, "a") or die("Unable to open file!");
        fwrite($myfile, "$text"."\n");
        fclose($myfile);
    }

    // ==============================================================================
	// Load EDM global
	// ==============================================================================
	function load_edm_template($acf_group = '', $extra_body = '', $request = array()) {

		$email_dir_url = plugin_dir_url( __DIR__ ) ."templates/emails/";

		$html = file_get_contents( $email_dir_url ."/edm-header.html" );
	    $html .= file_get_contents( $email_dir_url ."/edm-body.html" );
	    $html .= file_get_contents( $email_dir_url ."/edm-footer.html" );


	    $site_url = site_url();
	    $logo = $email_dir_url."images/logo.png";
	    
	    $email = "contact@fitnessintellect.com";
	    $phone = '';
	    $edm_tel = preg_replace('/\D/', '', $phone);
	    $footer_detail = "Fitness Intellect";


	    $subject = $request['subject'];
	    $heading = $request['heading'];	    
	    $body = $request['body'];

	    ## if add extra detail more them backend option
	    $body .= $extra_body;

	    $request['save_file'] = true;

	    $fullname = $request['fullname'];
	    $sent_to = $request['sent_to'];

	    $admin_email = get_option('admin_email');

	    $site_title = html_entity_decode(get_bloginfo( 'name' ));

	    $headers[] = 'From: '.$site_title.' <'.$admin_email.'>';
	    $headers[] = 'Content-Type: text/html; charset=UTF-8';

	    ## this for backend content
	    $find = array('{{logo}}', '{{fullname}}', '{{edm_email}}', '{{edm_phone}}','{{edm_tel}}', '{{footer_edm_text}}', '{{heading}}', '{{edm_body}}', '{{site_url}}');
	    $replace = array($logo, $fullname, $email, $phone, $edm_tel, $footer_detail, $heading, $body, $site_url);

	    $html = str_replace($find, $replace, $html);

	    $find = array('{{logo}}', '{{fullname}}', '{{edm_email}}', '{{edm_phone}}','{{edm_tel}}', '{{footer_edm_text}}', '{{heading}}', '{{edm_body}}', '{{site_url}}');
	    $replace = array($logo, $fullname, $email, $phone, $edm_tel, $footer_detail, $heading, $body, $site_url);

	    $body = str_replace($find, $replace, $html);

	    if( isset( $request['die'] ) ){
		    echo $body;die;
	    }
	    
	    if( !empty( $subject ) ){
	    	$sent = wp_mail($sent_to, $subject, $body, $headers);
	    }
	}
}
