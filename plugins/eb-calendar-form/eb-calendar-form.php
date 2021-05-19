<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.hexacom.com
 * @since             1.0.0
 * @package           Eb_Calendar_Form
 *
 * @wordpress-plugin
 * Plugin Name:       EB Calendar Form
 * Plugin URI:        www.dinamiktravels.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Eduar Berroteran
 * Author URI:        www.hexacom.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       eb-calendar-form
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EB_CALENDAR_FORM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-eb-calendar-form-activator.php
 */
function activate_eb_calendar_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-eb-calendar-form-activator.php';
	Eb_Calendar_Form_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-eb-calendar-form-deactivator.php
 */
function deactivate_eb_calendar_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-eb-calendar-form-deactivator.php';
	Eb_Calendar_Form_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_eb_calendar_form' );
register_deactivation_hook( __FILE__, 'deactivate_eb_calendar_form' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-eb-calendar-form.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_eb_calendar_form() {

	$plugin = new Eb_Calendar_Form();
	$plugin->run();

}
run_eb_calendar_form();
