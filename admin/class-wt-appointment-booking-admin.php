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
class Wt_Appointment_Booking_Admin {

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


	private $admin_menu;

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

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function run() {

		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Register the admin manu the admin area.
	 *
	 * @since    1.0.0
	 */
	public function admin_menu() {

		global $customMenu, $customSubMenu;
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

		$admin_menu = add_menu_page( 'Appointments', 'Appointments', 'manage_options', 'wt-appointments', array( $this, 'wt_appointments' ), 'dashicons-calendar-alt', 10);

		add_submenu_page( 'wt-appointments', 'Appointments Settings', 'Appointments Settings', 'manage_options', 'wt-settings', array($this, 'submenu_page_callback') );


		add_action( 'admin_init', array( $this, 'register_wt_appointments_settings' ) );
        
	}

	public function register_wt_appointments_settings() {

		register_setting('wt_appointments_settings', 'wt_appointments_options');

		register_setting('wt_appointments_settings', 'wt_register_body');
		register_setting('wt_appointments_settings', 'wt_hourly_body');
		register_setting('wt_appointments_settings', 'wt_daily_body');

		// do_action('register_wt_appointments_settings_advanced');

	}

	public function submenu_page_callback() {

		$wt_appointments = get_option('wt_appointments_options');

		$hourly_subject = $wt_appointments['hourly']['subject'];
		$hourly_heading = $wt_appointments['hourly']['heading'];
		$hourly_body = get_option('wt_hourly_body');

        ?>
        <div class="wrap">
        	<h2>Appointments Settings</h2>
        	<form method="post" action="options.php">
        		<?php settings_fields('wt_appointments_settings'); ?>

        		<table class="form-table welcome-panel">
			   		<tr>
			   			<td>
			   				<table class="form-table-group">
								<tbody>
									<tr>
										<th colspan="2"><h2>User Mail - Hourly Reminder</h2></th>
									</tr>
									<tr>
										<th scope="row"><label for="wt_hourly_email_sub"><?php _e('Email Subject', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments[hourly][subject]" type="text" id="wt_hourly_email_sub" value="<?php echo $hourly_subject; ?>" class="regular-text">
										</td>
									</tr>
									 
									<tr>
										<th scope="row"><label for="wt_hourly_email_heading"><?php _e('Email heading', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments[hourly][heading]" type="text" id="wt_hourly_email_heading" value="<?php echo $hourly_heading; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="wt_hourly_email_body"><?php _e('Email Body', 'book-appointment-online'); ?></label></th>
										<td>
											<?php
											// $name = 'wt_appointments[hourly][body]';
									        wp_editor( $hourly_body, 'wt_hourly_body', $settings = array('textarea_rows'=> '10') );
											?>
										</td>
									</tr>
								</tbody>
							</table>
							<?php do_action('wt_appointments_email_options'); ?>
			   			</td>
			   		</tr>
			   		<tr>
			   			<td><?php submit_button('Save', 'primary'); ?></td>
			   		</tr>
		   		</table>
		   		
	   		</form>

		   	<style type="text/css">
		   		/*
		   		.form-table-group {
				    border: 1px solid #ddd;
				    width: 100%;
				}
				.form-table-group th,
				.form-table-group td{
					border: 1px solid #ddd;
				}
				*/
		   	</style>
		</div>
        <?php
    }

	/**
	 * Register the stylesheets for the admin area.
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
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wt-appointment-booking-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wt-appointment-booking-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function wt_appointments() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-list-table/wt-appointments-list.php';

		/*
		$appointments = new appointments_List_Table();
		$appointments->prepare_items();
		$appointments->search_box('search', 'search_match');
		$appointments->views();
		$appointments->display();	
		*/

		$appointments = new appointments_List_Table();
		echo '<div class="wrap"><h2>Appointments</h2>'; 
			$appointments->prepare_items();
			$appointments->views();
			echo '<form method="post">';	
				echo ' <input type="hidden" name="page" value="pmc_fs_search">';
				$appointments->search_box( 'search', 'search_id' );
				$appointments->display();  
			echo '</form>';
		echo '</div>';

	}

}
