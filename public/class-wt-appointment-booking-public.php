<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://webytude.com
 * @since      1.0.0
 *
 * @package    Wt_Appointment_Booking
 * @subpackage Wt_Appointment_Booking/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wt_Appointment_Booking
 * @subpackage Wt_Appointment_Booking/public
 * @author     WebyTude <https://webytude.com>
 */
class Wt_Appointment_Booking_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'wp_ajax_wt_take_appointments', array($this,'wt_take_appointments') ); 
		add_action( 'wp_ajax_nopriv_wt_take_appointments', array($this,'wt_take_appointments') ); 


		add_action( 'wp_ajax_wt_get_user_timezone', array($this,'wt_get_user_timezone') ); 
		add_action( 'wp_ajax_nopriv_wt_get_user_timezone', array($this,'wt_get_user_timezone') ); 

	}

	public function run() {

		add_shortcode('appointments_form', array($this, 'appointments_form_func'));

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wt_Appointment_Booking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wt_Appointment_Booking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wt-appointment-booking-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wt_Appointment_Booking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wt_Appointment_Booking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wt-appointment-booking-public.js', array( 'jquery' ), $this->version, false );

	}

	public function appointments_form_func( $atts, $content = "" ){
		// return "content = $content";

	 	wp_enqueue_style( $this->plugin_name.'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name.'-pignose', plugin_dir_url( __FILE__ ) . 'css/pignose.calendar.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wt-appointment-booking-public.css', array(), $this->version, 'all' );


		wp_enqueue_script( $this->plugin_name.'select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name.'-pignose', plugin_dir_url( __FILE__ ) . 'js/pignose.calendar.full.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wt-appointment-booking-public.js', array( 'jquery' ), $this->version, false );

		$wt_object = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
		);
		wp_localize_script( $this->plugin_name, 'wt_object', $wt_object );

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/wt-appointment-booking-public-display.php';

	}

	// ==============================================================================
	// Convert time based on timezone
	// ==============================================================================
	public function get_converted_timezone( $time, $format, $timezone, $default_timezone = NULL ){

		if( $default_timezone == NULL ){
			$default_timezone = get_option( 'timezone_string' );
		}
		date_default_timezone_set( $default_timezone );

		$ob_time = new DateTime( $time );

		$user_tz = new DateTimeZone( $timezone );
		$ob_time->setTimezone( $user_tz );

		return $ob_time->format( $format );
	}
	public function wt_get_user_timezone(){
		
		$request = $_REQUEST;
		$timezone = $request['timezone'];
		$date = $request['date'];

		$format = 'H:i';
		$wt_appointments = get_option('wt_appointments_options');

		$time_start = $wt_appointments['time']['start'];
		$time_end = $wt_appointments['time']['end'];
		$time_inte = $wt_appointments['time']['inte'];

		$time_start = $this->get_converted_timezone( $time_start, $format, $timezone );
		$time_end = $this->get_converted_timezone( $time_end, $format, $timezone );

		$times = hoursRange( $time_start, $time_end, $time_inte );
		$booked_times = $this->wt_get_booked_app_times( $timezone, $date );

		$times_html = '';
		if( !empty( $times )){
			$times_html = "<ul>";
			foreach ($times as $key => $value) {

				if( in_array($key, $booked_times) ){
					$times_html .= "<li class='booked'>
						<div class='time'><a class='cta-button-outline' href='#'>$value</a></div>
					</li>";
				}
				else{
					$times_html .= "<li>
						<div class='time'><a class='cta-button-outline' href='#'>$value</a></div>
						<div class='time-confirm'><a class='cta-button' href='#' data-time='$key' tabindex='-1'>Confirm</a></div>
					</li>";
				}
			}
			$times_html .= "</ul>";
		}
		

		$data['times_html'] = $times_html;
	    echo json_encode( $data );
	    die(); 
	}

	// ==============================================================================
	// Insert appointment
	// ==============================================================================
	public function wt_take_appointments(){
	    global $wpdb;
	    $table_appointments = $wpdb->prefix . "wt_appointments";
		$request = $_REQUEST;

		$format = 'Y-m-d H:i:s';
		// $format = 'Y-m-d H:i:s e';

		$date = sanitize_text_field( $request['wt-date'] );
		$time = sanitize_text_field( $request['wt-time'] );
		$timezone = sanitize_text_field( $request['wt-timezone'] );
		$date_time = $date." ".$time;

		$system_timezone = get_option( 'timezone_string' );

		$system_time = $this->get_converted_timezone( $date_time, $format, $system_timezone, $timezone );
		$user_time = $this->get_converted_timezone( $date_time, $format, $timezone, $timezone );

		$status = $wpdb->insert( 
		    $table_appointments, 
		    array(
		        'app_name'			=> sanitize_text_field( $request['name'] ),
		        'app_time' 			=> sanitize_text_field( $system_time ),
		        'app_email'   		=> sanitize_email( $request['email'] ),
		        'app_comments'  	=> sanitize_text_field( $request['comments'] ),
		        'app_user_time' 	=> sanitize_text_field( $user_time ),
		        'app_timezone' 		=> sanitize_text_field( $timezone ),
		        'app_booked_time'  	=> sanitize_text_field( current_time( 'mysql' ) ),
		    )
		);
		
		if($status !== false){
	        $wt_appointments = get_option('wt_appointments_options');

	        $success_message = $wt_appointments['general']['success_message'];
			$success_redirect_page = $wt_appointments['general']['success_redirect_page'];

			if( !empty( $success_message ) ){
	        	$message = "<h2 class='success-message'>$success_message</h2>";
			}
			else{
				$message = "<h2 class='success-message'>Your information has been submitted successfully</h2>";
			}
	        $data['status'] = 'success';
	        if( !empty( $success_redirect_page ) ){
	        	$data['redirect'] = $success_redirect_page;
	        }


	        $request['sent_to'] = $request['email'];
	        $request['subject'] = $wt_appointments['confi']['subject'];
		    $request['heading'] = $wt_appointments['confi']['heading'];
		    $request['body'] = apply_filters('the_content', get_option('wt_confi_body'));

		    $request['event_date'] = $date;
		    $request['event_time'] = sanitize_text_field( $date_time );
		    $request['invitee_full_name'] = sanitize_text_field( $request['name'] );
		    $request['event_host_name'] = "Admin";
		    // $request['save_file'] = true;
		    // $request['die'] = true;

	    	$email = new Wt_Appointment_Email( $this->plugin_name, $this->version );
			$email->load_edm_template("", "", $request);
			// $email->wt_add_log_file("conf", $request['email']);


			// ----------------------------
			// Send Mail to admin
			// ----------------------------
			if( !empty( $wt_appointments['general']['admin_email'] ) ){
	        	$request['sent_to'] = $wt_appointments['general']['admin_email'];
	        }
	        else{
	        	$request['sent_to'] = get_option( 'admin_email' );
	        }

	        $request['subject'] = $wt_appointments['admin_confi']['subject'];
		    $request['heading'] = $wt_appointments['admin_confi']['heading'];
		    $request['body'] = apply_filters('the_content', get_option('wt_admin_confi_body'));
		    
	        $request['event_host_time'] = sanitize_text_field( $system_time );
		    // $request['save_file'] = true;
		    $email->load_edm_template("", "", $request);
			
	    }
	    else{
	        $message = "<h2 class='error-message'>Opp!!! There is some issue please try again</h2>";
	        $data['status'] = 'error';
	    }

		$data['message'] = $message;
	    echo json_encode( $data );
	    die(); 
	}
	public function wt_get_booked_app_times( $timezone = NULL, $selected_date = NULL){
		global $wpdb;
		$data = array();
		$table_appointments = $wpdb->prefix . "wt_appointments";

		if( $timezone == NULL ){
			$timezone = get_option( 'timezone_string' );
		}
		if( $selected_date == NULL ){
			$selected_date = current_time( 'mysql' );

			$next_day_start = date_i18n( 'Y-m-d 00:00:00', strtotime( $selected_date . '+1 days' ) );
		   	$next_day_end = date_i18n( 'Y-m-d 23:59:00', strtotime( $selected_date . '+1 days' ) );
		}
		else{
			$next_day_start = date( 'Y-m-d 00:00:00', strtotime( $selected_date ) );
		   	$next_day_end = date( 'Y-m-d 23:59:00', strtotime( $selected_date ) );
		}

	    $query = "SELECT app_time FROM {$table_appointments} AS wr_p WHERE wr_p.app_time BETWEEN '$next_day_start' AND '$next_day_end'";

		$appointments = $wpdb->get_results( $query );

		$times = array();
		$format = 'H:i';
		if( $appointments ){
			foreach ($appointments as $key => $value) {
				$app_time = $this->get_converted_timezone( $value->app_time, $format, $timezone );
				$times[] = $app_time;
			}
		}
		return $times;
	}


	/**
	 * Get user IP address
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_ip(){
		// Get server IP address
		$server_ip = (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : '';

		// If website is hosted behind CloudFlare protection.
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $_SERVER['HTTP_CF_CONNECTING_IP'];
		}

		if (isset($_SERVER['X-Real-IP']) && filter_var($_SERVER['X-Real-IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $_SERVER['X-Real-IP'];
		}

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));

			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) && $ip != $server_ip) {
				return $ip;
			}
		}

		return $_SERVER['REMOTE_ADDR'];
	}

}

//function hoursRange( $lower = 0, $upper = 23, $step = 1, $format = '' ) {
// ==============================================================================
// Calculate time shift wise
// Ref :: https://www.w3schools.in/php-script/split-a-time-slot-between-the-start-and-end-time-using-the-time-interval/
// ==============================================================================
function hoursRange($StartTime, $EndTime, $Duration = "60") {
    $AddMins = $Duration * 60;

	$start_time    = strtotime( $StartTime ); //Get Timestamp
    $end_time      = strtotime( $EndTime ); //Get Timestamp
    $has_next_day = false;
    if( $end_time <= $start_time ){
    	$end_time = strtotime ($EndTime . ' + 1 days');
    	$has_next_day = true;
    }

    while ( $start_time <= $end_time ) {
	    // $ReturnArray[] = date ("G:i", $start_time);

	    $time_key = date ("H:i", $start_time);
	    $time_val = date ("h:i a", $start_time);

	    $ReturnArray[$time_key] = $time_val;
	    $start_time += $AddMins; //end_time check
    }
    return $ReturnArray;
}


