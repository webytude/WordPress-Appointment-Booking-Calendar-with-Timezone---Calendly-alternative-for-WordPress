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
class Wt_Appointment_Email {

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
	}

	public function run() {
	    // add_action( 'admin_init', array( $this, 'wt_daily_email_schedule_fun' ));
	}
 

	public function wt_add_log_file($name, $text){

        $date = date("d_g");
        $file = $this->wt_path."/{$name}-{$date}.txt"; 
        $myfile = fopen($file, "a") or die("Unable to open file!");
        fwrite($myfile, "$text"."\n");
        fclose($myfile);


        /*
        $file = $this->wt_url."/{$name}---{$date}.txt"; 
        $myfile = fopen($file, "a") or die("Unable to open file!");
        fwrite($myfile, "$text"."\n");
        fclose($myfile);
        */

    }

    // ==============================================================================
	// Load EDM global
	// ==============================================================================
	function load_edm_template($acf_group = '', $extra_body = '', $request = array()) {

		$email_dir_url = plugin_dir_url( __DIR__ ) ."templates/emails/";

		/*
		$html = file_get_contents( $email_dir_url ."/edm-header.html" );
	    $html .= file_get_contents( $email_dir_url ."/edm-body.html" );
	    $html .= file_get_contents( $email_dir_url ."/edm-footer.html" );
	    */
	    $html = '{{edm_body}}';


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

	    $sent_to = $request['sent_to'];
	    $admin_email = get_option('admin_email');

	    $site_title = html_entity_decode(get_bloginfo( 'name' ));

	    $headers[] = 'From: '.$site_title.' <'.$admin_email.'>';
	    $headers[] = 'Content-Type: text/html; charset=UTF-8';


	    // $event_date = date_i18n( "l, M jS", strtotime( $request['event_date'] ) );
	    $event_date = date( "l, M jS", strtotime( $request['event_date'] ) );

	    $invitee_full_name = $request['invitee_full_name'];
	    // $event_time = date_i18n( "h:i a", strtotime( $request['event_time'] ) );
	    $event_time = date( "h:i a", strtotime( $request['event_time'] ) );

	    $event_host_name = $request['event_host_name'];
	    $event_time = date( "h:i a", strtotime( $request['event_time'] ) );
	    $event_host_time = date( "h:i a", strtotime( $request['event_host_time'] ) );

	    $comments  = isset( $request['comments'] ) ? $request['comments'] : '';

	    $find = array('{{logo}}', '{{fullname}}', '{{edm_email}}', '{{edm_phone}}','{{edm_tel}}', '{{footer_edm_text}}', '{{heading}}', '{{edm_body}}', '{{site_url}}');
	    $replace = array($logo, $fullname, $email, $phone, $edm_tel, $footer_detail, $heading, $body, $site_url);
	    $body = str_replace($find, $replace, $html);

	    ## this for backend content
	    $find = array('{{event_date}}', '{{invitee_full_name}}', '{{event_time}}', '{{event_host_name}}', '{{event_host_time}}', '{{comments}}');
	    $replace = array($event_date, $invitee_full_name, $event_time, $event_host_name, $event_host_time, $comments);

	    $subject	= str_replace($find, $replace, $subject);
	    $heading 	= str_replace($find, $replace, $heading);
	    $body 		= str_replace($find, $replace, $body);

	    if( isset( $request['die'] ) ){
		    echo $body;die;
	    }
	    if( isset( $request['save_file'] ) ){
	    	$date = date("d_g_i_s");
			$file = $this->wt_path."/edm_$date.html"; 
			$myfile = fopen($file, "a") or die("Unable to open file!");
			$txt = "user id date";
			fwrite($myfile, "\n". $body);
			fclose($myfile); 
	    }
	    if( !empty( $subject ) ){
	    	$sent = wp_mail($sent_to, $subject, $body, $headers);
	    }
	}
}
