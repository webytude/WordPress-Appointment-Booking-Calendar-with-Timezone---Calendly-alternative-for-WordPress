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

	 
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wt-appointment-booking-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-pignose', plugin_dir_url( __FILE__ ) . 'css/pignose.calendar.css', array(), $this->version, 'all' );

		wp_enqueue_script( $this->plugin_name.'-pignose', plugin_dir_url( __FILE__ ) . 'js/pignose.calendar.full.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wt-appointment-booking-public.js', array( 'jquery' ), $this->version, false );

		$wt_object = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
		);
		wp_localize_script( $this->plugin_name, 'wt_object', $wt_object );

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/wt-appointment-booking-public-display.php';

	}

	// ==============================================================================
	// Add your comments here
	// ==============================================================================
	public function wt_take_appointments(){
	    global $wpdb;
	    $table_appointments = $wpdb->prefix . "wt_appointments";
		$request = $_REQUEST;


		$date = sanitize_text_field( $request['wt-date'] );
		$time = sanitize_text_field( $request['wt-time'] );
		$date_time = $date." ".$time;

		
		$status = $wpdb->insert( 
		    $table_appointments, 
		    array(
		        'app_name'			=> sanitize_text_field( $request['name'] ),
		        'app_time' 			=> sanitize_text_field( $date_time ),
		        'app_email'   		=> sanitize_email( $request['email'] ),
		        'app_comments'  	=> sanitize_text_field( $request['comments'] ),
		        'app_booked_time'  	=> sanitize_text_field( current_time( 'mysql' ) ),
		    )
		);
		
		if($status !== false){
	        $message = "<h2 class='success-message'>Your information has been submitted successfully</h2>";
	        $data['status'] = 'success';

	        
	        $fullname = $request['name'];
	    	$body = "
	    	Dear $fullname, <br>
	    	I would like to confirm your appointment with Fitness intellect team on $date_time. Please contact me with any questions and keep me informed if there should be any changes.
		    ";

	    	$request = array(
		    	'subject' => "Confirmation of appointment",
		    	'heading' => "Confirmation of appointment",
		    	'body' => $body,
		    	'fullname' => $request['name'],
		    	'sent_to' => $request['email'],
		    );

	    	$email = new Wt_Appointment_Email( $this->plugin_name, $this->version );
			$email->load_edm_template("", "", $request);
			$email->wt_add_log_file("conf", $request['email']);
			
	    }
	    else{
	        $message = "<h2 class='error-message'>Opp!!! There is some issue please try again</h2>";
	        $data['status'] = 'error';
	    }

		$data['message'] = $message;
	    echo json_encode( $data );
	    die(); 

	}

}

function hoursRange( $lower = 0, $upper = 23, $step = 1, $format = '' ) {
    $times = array();


     $hrs = 2;

    if ( empty( $format ) ) {
        $format = 'g:i a';
    }
    if( !empty( $lower ) ){
    	$lower = $lower * 3600;
    }
    if( !empty( $upper ) ){
    	$upper = $upper * 3600;
    }   
    if( !empty( $step ) ){
    	$step = $step * 60;
    }
    if( $lower >= $upper ){
    	return;
    }


    foreach ( range( $lower, $upper, $step ) as $increment ) {
        $increment = gmdate( 'H:i', $increment );

        list( $hour, $minutes ) = explode( ':', $increment );

        $date = new DateTime( $hour . ':' . $minutes );

        $times[(string) $increment] = $date->format( $format );
    }

    return $times;
}


