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

		register_setting('wt_appointments_settings', 'wt_confi_body');
		register_setting('wt_appointments_settings', 'wt_admin_confi_body');
		register_setting('wt_appointments_settings', 'wt_cancel_body');
		register_setting('wt_appointments_settings', 'wt_hourly_body');
		register_setting('wt_appointments_settings', 'wt_daily_body');

		// do_action('register_wt_appointments_settings_advanced');

	}

	public function submenu_page_callback() {

		$wt_appointments = get_option('wt_appointments_options');

		$time_start = $wt_appointments['time']['start'];
		$time_end = $wt_appointments['time']['end'];
		$time_inte = $wt_appointments['time']['inte'];

		$success_message = $wt_appointments['general']['success_message'];
		$success_redirect_page = $wt_appointments['general']['success_redirect_page'];
		$appointment_title = $wt_appointments['general']['appointment_title'];
		$form_title = $wt_appointments['general']['form_title'];
		$form_button = $wt_appointments['general']['form_button'];
		$admin_email = $wt_appointments['general']['admin_email'];

		$admin_confi_subject = $wt_appointments['admin_confi']['subject'];
		$admin_confi_heading = $wt_appointments['admin_confi']['heading'];
		$admin_confi_body = get_option('wt_admin_confi_body');

		$confi_subject = $wt_appointments['confi']['subject'];
		$confi_heading = $wt_appointments['confi']['heading'];
		$confi_body = get_option('wt_confi_body');

		$cancel_subject = $wt_appointments['cancel']['subject'];
		$cancel_heading = $wt_appointments['cancel']['heading'];
		$cancel_body = get_option('wt_cancel_body');

		$daily_subject = $wt_appointments['daily']['subject'];
		$daily_heading = $wt_appointments['daily']['heading'];
		$daily_body = get_option('wt_daily_body');

		$hourly_subject = $wt_appointments['hourly']['subject'];
		$hourly_heading = $wt_appointments['hourly']['heading'];
		$hourly_body = get_option('wt_hourly_body');

        ?>
        <div class="wrap">
        	<h2>Appointments Settings</h2>
        	<form method="post" action="options.php">
        		<?php settings_fields('wt_appointments_settings'); ?>

        		<table class="wt-main-table form-table welcome-panel" cellspacing="0" cellpadding="0">
        			<tr>
			   			<td>
			   				<table class="wt-table-group">
								<tbody>
									<tr>
										<th colspan="2"><h2><?php _e('Time Settings', 'book-appointment-online'); ?></h2></th>
									</tr>
									<tr>
										<th scope="row"><label for="wt_time_start"><?php _e('Starting Time', 'book-appointment-online'); ?></label></th>
										<td>
											<select name="wt_appointments_options[time][start]" id="wt_time_start">
												<?php
												$times = hoursRange( '00:00', '23:00', '60' );
												foreach ($times as $key => $value) {
													echo "<option value='$key' ".selected( $time_start, $key ).">$value</option>";
												}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="wt_time_end"><?php _e('Ending Time', 'book-appointment-online'); ?></label></th>
										<td>
											<select name="wt_appointments_options[time][end]" id="wt_time_end">
												<?php
												$times = hoursRange( '00:00', '23:00', '60' );
												foreach ($times as $key => $value) {
													echo "<option value='$key' ".selected( $time_end, $key ).">$value</option>";
												}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="wt_time_end"><?php _e('Time Interval', 'book-appointment-online'); ?></label></th>
										<td>
											<select name="wt_appointments_options[time][inte]" id="wt_time_end">
												<?php
												$times = array(15,30,45,60);
												foreach ($times as $key => $value) {
													echo "<option value='$value' ".selected( $time_inte, $value ).">$value Mins</option>";
												}
												?>
											</select>
										</td>
									</tr>

								</tbody>
							</table>
			   			</td>
			   		</tr>
			   		<tr>
			   			<td>
			   				<table class="wt-table-group">
								<tbody>
									<tr>
										<th colspan="2"><h2><?php _e('General Settings', 'book-appointment-online'); ?></h2></th>
									</tr>
									<tr>
										<th scope="row"><label for="wt_success_message"><?php _e('Success Message', 'book-appointment-online'); ?></label></th>
										<td>
											<input name="wt_appointments_options[general][success_message]" type="text" id="success_message" value="<?php echo $success_message; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="success_redirect_page"><?php _e('Success URL', 'book-appointment-online'); ?></label></th>
										<td>
											<input name="wt_appointments_options[general][success_redirect_page]" type="text" id="success_redirect_page" value="<?php echo $success_redirect_page; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="appointment_title"><?php _e('Appointment Title', 'book-appointment-online'); ?></label></th>
										<td>
											<input name="wt_appointments_options[general][appointment_title]" type="text" id="appointment_title" value="<?php echo $appointment_title; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="form_title"><?php _e('Form Title', 'book-appointment-online'); ?></label></th>
										<td>
											<input name="wt_appointments_options[general][form_title]" type="text" id="form_title" value="<?php echo $form_title; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="form_button"><?php _e('Form Button', 'book-appointment-online'); ?></label></th>
										<td>
											<input name="wt_appointments_options[general][form_button]" type="text" id="form_button" value="<?php echo $form_button; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="admin_email"><?php _e('Admin Email', 'book-appointment-online'); ?></label></th>
										<td>
											<input name="wt_appointments_options[general][admin_email]" type="email" id="admin_email" value="<?php echo $admin_email; ?>" class="regular-text">
										</td>
									</tr>

								</tbody>
							</table>
			   			</td>
			   		</tr>
			   		<tr>
			   			<td>
			   				<table class="wt-table-group">
								<tbody>
									<tr>
										<th colspan="2"><h2><?php _e('Admin Confirmation Notification Email', 'book-appointment-online'); ?></h2></th>
									</tr>
									<tr>
										<th scope="row"><label for="wt_admin_confi_email_sub"><?php _e('Email Subject', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[admin_confi][subject]" type="text" id="wt_confi_email_sub" value="<?php echo $admin_confi_subject; ?>" class="regular-text">
										</td>
									</tr>
									 
									<tr>
										<th scope="row"><label for="wt_confi_email_heading"><?php _e('Email Heading', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[admin_confi][heading]" type="text" id="wt_confi_email_heading" value="<?php echo $admin_confi_heading; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label><?php _e('Email Body', 'book-appointment-online'); ?></label></th>
										<td>
											<?php
											wp_editor( $admin_confi_body, 'wt_admin_confi_body', $settings = array('textarea_rows'=> '10') );
											?>
										</td>
									</tr>
								</tbody>
							</table>
			   			</td>
			   		</tr>
        			<tr>
			   			<td>
			   				<table class="wt-table-group">
								<tbody>
									<tr>
										<th colspan="2"><h2><?php _e('Confirmation Email', 'book-appointment-online'); ?></h2></th>
									</tr>
									<tr>
										<th scope="row"><label for="wt_confi_email_sub"><?php _e('Email Subject', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[confi][subject]" type="text" id="wt_confi_email_sub" value="<?php echo $confi_subject; ?>" class="regular-text">
										</td>
									</tr>
									 
									<tr>
										<th scope="row"><label for="wt_confi_email_heading"><?php _e('Email Heading', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[confi][heading]" type="text" id="wt_confi_email_heading" value="<?php echo $confi_heading; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label><?php _e('Email Body', 'book-appointment-online'); ?></label></th>
										<td>
											<?php
											// $name = 'wt_appointments[hourly][body]';
									        wp_editor( $confi_body, 'wt_confi_body', $settings = array('textarea_rows'=> '10') );
											?>
										</td>
									</tr>
								</tbody>
							</table>
			   			</td>
			   		</tr>

			   		<tr>
			   			<td>
			   				<table class="wt-table-group">
								<tbody>
									<tr>
										<th colspan="2"><h2><?php _e('Cancellation Email', 'book-appointment-online'); ?></h2></th>
									</tr>
									<tr>
										<th scope="row"><label for="wt_cancel_email_sub"><?php _e('Email Subject', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[cancel][subject]" type="text" id="wt_cancel_email_sub" value="<?php echo $cancel_subject; ?>" class="regular-text">
										</td>
									</tr>
									 
									<tr>
										<th scope="row"><label for="wt_cancel_email_heading"><?php _e('Email Heading', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[cancel][heading]" type="text" id="wt_cancel_email_heading" value="<?php echo $cancel_heading; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label><?php _e('Email Body', 'book-appointment-online'); ?></label></th>
										<td>
											<?php
											// $name = 'wt_appointments[hourly][body]';
									        wp_editor( $cancel_body, 'wt_cancel_body', $settings = array('textarea_rows'=> '10') );
											?>
										</td>
									</tr>
								</tbody>
							</table>
			   			</td>
			   		</tr>

			   		<tr>
			   			<td>
			   				<table class="wt-table-group">
								<tbody>
									<tr>
										<th colspan="2"><h2><?php _e('Reminder 1 (Before 24 Hours)', 'book-appointment-online'); ?></h2></th>
									</tr>
									<tr>
										<th scope="row"><label for="wt_daily_email_sub"><?php _e('Email Subject', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[daily][subject]" type="text" id="wt_daily_email_sub" value="<?php echo $daily_subject; ?>" class="regular-text">
										</td>
									</tr>
									 
									<tr>
										<th scope="row"><label for="wt_daily_email_heading"><?php _e('Email Heading', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[daily][heading]" type="text" id="wt_daily_email_heading" value="<?php echo $daily_heading; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label><?php _e('Email Body', 'book-appointment-online'); ?></label></th>
										<td>
											<?php
											// $name = 'wt_appointments[daily][body]';
									        wp_editor( $daily_body, 'wt_daily_body', $settings = array('textarea_rows'=> '10') );
											?>
										</td>
									</tr>
								</tbody>
							</table>
			   			</td>
			   		</tr>

			   		<tr>
			   			<td>
			   				<table class="wt-table-group">
								<tbody>
									<tr>
										<th colspan="2"><h2><?php _e('Reminder 2 (Before 30 Minutes)', 'book-appointment-online'); ?></h2></th>
									</tr>
									<tr>
										<th scope="row"><label for="wt_hourly_email_sub"><?php _e('Email Subject', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[hourly][subject]" type="text" id="wt_hourly_email_sub" value="<?php echo $hourly_subject; ?>" class="regular-text">
										</td>
									</tr>
									 
									<tr>
										<th scope="row"><label for="wt_hourly_email_heading"><?php _e('Email Heading', 'book-appointment-online'); ?></label></th>
										<td>
										<input name="wt_appointments_options[hourly][heading]" type="text" id="wt_hourly_email_heading" value="<?php echo $hourly_heading; ?>" class="regular-text">
										</td>
									</tr>
									<tr>
										<th scope="row"><label><?php _e('Email Body', 'book-appointment-online'); ?></label></th>
										<td>
											<?php
											// $name = 'wt_appointments[hourly][body]';
									        wp_editor( $hourly_body, 'wt_hourly_body', $settings = array('textarea_rows'=> '10') );
											?>
										</td>
									</tr>
								</tbody>
							</table>
			   			</td>
			   		</tr>
					
					<?php do_action('wt_appointments_email_options'); ?>
			   		<tr>
			   			<td><?php submit_button('Save', 'primary'); ?></td>
			   		</tr>
		   		</table>
		   		
	   		</form>

		   	<style type="text/css">
		   		
		   		.wt-main-table{
				    /*border: 1px solid #ddd;*/
				    width: 100%;
				    border-collapse: collapse;
				    background: #fff;
				}
				.wt-main-table .wt-table-group{
					width: 100%;
					border-collapse: collapse;
				}
				.wt-main-table .wt-table-group th,
				.wt-main-table .wt-table-group td{
					border: 1px solid #ddd;
					padding: 15px;
				}
				.wt-main-table .regular-text{
					width: 50%;
					max-width: 100%
				}
				
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
