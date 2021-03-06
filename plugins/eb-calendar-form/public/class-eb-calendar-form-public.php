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

		/**wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/eb-calendar-form-public.css', array(), $this->version, 'all' );**/

		/**
		 * AÑADIR VARIOS ARCHIVO CSS DE BOOTSTRAP 5.
		*/
		global $post;
		wp_register_style( 'custom_css_bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css'  );
		wp_register_style( 'custom_css_jquery', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css'  );
		wp_register_style( 'custom_css2', plugin_dir_url( __FILE__ ) . 'css/eb-calendar-form-public.css' );


		if ( strstr($post->post_content, '[ADD_INMUEBLE]') || strstr($post->post_content, '[LISTAWOOPED]')) 
		{
			wp_enqueue_style('custom_css_bootstrap', false, $this->version, false );
			wp_enqueue_style('custom_css_jquery' );
			
			wp_enqueue_style('custom_css2' );
		}
	
		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), '5.0.1', 'all' );
		
	}
	
	public function shortcode_lista_ped_woo(){
		include_once plugin_dir_path( __FILE__ ) . '/partials/eb-lista-pedidos-woo.php';
		
		add_shortcode( 'LISTAWOOPED', 'dcms_lista_ped_woo' );
	} 
	public function shortcode_detalles_ped_woo(){
		include_once plugin_dir_path( __FILE__ ) . '/partials/eb-detalles-pedidos-woo.php';
		
		add_shortcode( 'DETALLESPED', 'dcms_detalle_ped_woo' );
	} 
	public function shortcode_ejemplo(){
		include_once plugin_dir_path( __FILE__ ) . '/partials/eb-calendar-form-public-display.php';
		
		add_shortcode( 'MENSAJE', 'dcms_mensaje' );
	} 
	public function shortcode_agregar_inmueble(){
		include_once plugin_dir_path( __FILE__ ) . '/partials/eb-agregar-inmueble.php';
		include_once plugin_dir_path( __FILE__ ) . '/partials/eb-funciones-agregar-inmueble.php';
		add_shortcode( 'ADD_INMUEBLE', 'dcms_agregar_inmueble' );
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

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/eb-calendar-form-public.js', array( 'jquery' ), $this->version, false ); 
		
		/**
		 * AÑADIR VARIOS ARCHIVO CSS DE BOOTSTRAP 5.
		*/
		
		//wp_register_script( 'custom_js_jquery_ui', plugin_dir_url( __FILE__ ) . 'js/jquery-v1.12.1-ui.min.js' );
		global $post;
		wp_register_script( 'custom_js_bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js' );	
		
		wp_register_script( 'custom_js2', plugin_dir_url( __FILE__ ) . 'js/eb-calendar-form-public.js', array(), false , true );
		wp_register_script( 'custom_js_eb-manual', plugin_dir_url( __FILE__ ) . 'js/eb-manual-js.js' );

		wp_enqueue_script('jquery');
		if ( strstr( $post->post_content, '[LISTAWOOPED]' ) ||  strstr( $post->post_content, '[ADD_INMUEBLE]' ) ) 
		{
		
		wp_enqueue_script('jquery-ui-datepicker');
		//wp_enqueue_script('custom_js_jquery_ui', array( 'jquery' ), $this->version, false);
		wp_enqueue_script('custom_js_bootstrap', array( 'jquery' ), $this->version, false );
		
		wp_enqueue_script('custom_js2', array( 'jquery', 'jquery-ui-datepicker' ) );
		wp_enqueue_script('custom_js_eb-manual', array( 'jquery', 'jquery-ui-datepicker' ) );
		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false ); 
		}
	}


	 
}
