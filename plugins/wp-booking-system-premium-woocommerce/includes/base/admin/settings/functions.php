<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add the WooCommerce submenu to the Payments Tab
 *
 */
function wpbs_wc_settings_page_tab($tabs)
{

    $tabs['woocommerce'] = 'WooCommerce';
    return $tabs;
}
add_filter('wpbs_submenu_page_settings_payment_tabs', 'wpbs_wc_settings_page_tab', 1);

/**
 * Add the WooCommerce Settings to the WooCommerce Payments tab
 *
 */
function wpbs_wc_settings_page_tab_woocommerce()
{
    $settings = get_option('wpbs_settings', array());
    $defaults = wpbs_wc_settings_wc_defaults();

    include 'views/view-payment-settings-woocommerce.php';
}
add_action('wpbs_submenu_page_payment_settings_tab_woocommerce', 'wpbs_wc_settings_page_tab_woocommerce');
