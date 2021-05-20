<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       www.hexacom.com
 * @since      1.0.0
 *
 * @package    Eb_Calendar_Form
 * @subpackage Eb_Calendar_Form/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Eb_Calendar_Form
 * @subpackage Eb_Calendar_Form/public
 * @author     Eduar Berroteran <berroterane@gmail.com>
 */
class Eb_Calendar_Form_Public {

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
		 * defined in Eb_Calendar_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Eb_Calendar_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/eb-calendar-form-public.css', array(), $this->version, 'all' );

	}

	public function shortcode_ejemplo(){
		include_once plugin_dir_path( __FILE__ ) . '/partials/eb-calendar-form-public-display.php';
		add_shortcode( 'MENSAJE', 'dcms_mensaje' );
	}

	public function shortcode_admin_vista(){
		include_once plugin_dir_path( __FILE__ ) . '/partials/eb-calendar-form-public-display.php';
		add_shortcode( 'ADMIN_VISTA', 'dcms_admin_vista' );
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
		 * defined in Eb_Calendar_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Eb_Calendar_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/eb-calendar-form-public.js', array( 'jquery' ), $this->version, false );

	}

}
