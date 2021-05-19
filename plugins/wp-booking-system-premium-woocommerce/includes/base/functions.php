<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the Base files
 *
 */
function wpbs_wc_include_files_base()
{
    // Get legend dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include Payment Ajax Functions
    if (file_exists($dir_path . 'functions-actions-woocommerce.php')) {
        include $dir_path . 'functions-actions-woocommerce.php';
    }

    // Include Part Payments  Functions
    if (file_exists($dir_path . 'functions-part-payments.php')) {
        include $dir_path . 'functions-part-payments.php';
    }

    // Include WooCommerce Filters
    if (file_exists($dir_path . 'functions-woocommerce.php')) {
        include $dir_path . 'functions-woocommerce.php';
    }

}
add_action('wpbs_wc_include_files', 'wpbs_wc_include_files_base');

/**
 * Register Payment Method
 *
 * @param array
 *
 */
function wpbs_wc_register_payment_method($payment_methods)
{
    $payment_methods['woocommerce'] = 'WooCommerce';
    return $payment_methods;
}
add_filter('wpbs_payment_methods', 'wpbs_wc_register_payment_method');

/**
 * Default form values
 *
 */
function wpbs_wc_settings_wc_defaults()
{
    return array(
        'display_name' => __('WooCommerce', 'wp-booking-system-woocommerce'),
        'description' => __('Check out with WooCommerce.', 'wp-booking-system-woocommerce'),
        'button_label' => __('Pay with WooCommerce.', 'wp-booking-system-woocommerce'),
    );
}

/**
 * Check if payment method is enabled in settings page
 *
 */
function wpbs_wc_form_outputter_payment_method_enabled_woocommerce($active)
{
    $settings = get_option('wpbs_settings', array());
    if (isset($settings['payment_wc_enable']) && $settings['payment_wc_enable'] == 'on') {
        return true;
    }
    return false;
}
add_filter('wpbs_form_outputter_payment_method_enabled_woocommerce', 'wpbs_wc_form_outputter_payment_method_enabled_woocommerce');

/**
 * Get the payment method's name
 *
 */
function wpbs_wc_form_outputter_payment_method_name_woocommerce($active, $language)
{
    $settings = get_option('wpbs_settings', array());
    if (!empty($settings['payment_wc_name_translation_' . $language])) {
        return $settings['payment_wc_name_translation_' . $language];
    }
    if (!empty($settings['payment_wc_name'])) {
        return $settings['payment_wc_name'];
    }
    return wpbs_wc_settings_wc_defaults()['display_name'];
}
add_filter('wpbs_form_outputter_payment_method_name_woocommerce', 'wpbs_wc_form_outputter_payment_method_name_woocommerce', 10, 2);

/**
 * Get the payment method's name
 *
 */
function wpbs_wc_form_outputter_payment_method_description_woocommerce($active, $language)
{
    $settings = get_option('wpbs_settings', array());
    if (!empty($settings['payment_wc_description_translation_' . $language])) {
        return $settings['payment_wc_description_translation_' . $language];
    }
    if (!empty($settings['payment_wc_description'])) {
        return $settings['payment_wc_description'];
    }
    return wpbs_wc_settings_wc_defaults()['description'];
}
add_filter('wpbs_form_outputter_payment_method_description_woocommerce', 'wpbs_wc_form_outputter_payment_method_description_woocommerce', 10, 2);

/**
 * Get the button label name
 *
 */
function wpbs_wc_get_button_label($language)
{
    $settings = get_option('wpbs_settings', array());
    if (!empty($settings['payment_wc_button_label_translation_' . $language])) {
        return $settings['payment_wc_button_label_translation_' . $language];
    }
    if (!empty($settings['payment_wc_button_label'])) {
        return $settings['payment_wc_button_label'];
    }
    return wpbs_wc_settings_wc_defaults()['button_label'];
}