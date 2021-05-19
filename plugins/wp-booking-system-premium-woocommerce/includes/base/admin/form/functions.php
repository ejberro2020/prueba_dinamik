<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Booking Restrictions tab to form editor page
 *
 * @param array $tabs
 *
 * @return array
 *
 */
function wpbs_submenu_page_edit_form_tabs_woocommerce_field_mapping($tabs)
{

    $tabs['form-options']['woocommerce_field_mapping'] = __('WooCommerce Field Mapping', 'wp-booking-system-woocommerce');

    return $tabs;
}
add_filter('wpbs_submenu_page_edit_form_sub_tabs', 'wpbs_submenu_page_edit_form_tabs_woocommerce_field_mapping', 5, 1);

/**
 * Add Email Reminder tab content to form editor page
 *
 */
function wpbs_submenu_page_edit_form_tab_woocommerce_field_mapping()
{
    include 'views/view-edit-form-tab-woocommerce.php';
}
add_action('wpbs_submenu_page_edit_form_tabs_form_options_woocommerce_field_mapping', 'wpbs_submenu_page_edit_form_tab_woocommerce_field_mapping');

/**
 * Save meta fields when form is saved
 *
 * @param array $meta_fields
 *
 * @return array
 *
 */
function wpbs_wc_edit_forms_meta_fields($meta_fields)
{
    $wc_billing_fields = wpbs_wc_get_billing_fields();

    foreach($wc_billing_fields as $field_id => $field_name){
        $meta_fields['wc_field_mapping_' . $field_id] = array('translations' => false, 'sanitization' => 'sanitize_text_field');
    }

    return $meta_fields;
}
add_filter('wpbs_edit_forms_meta_fields', 'wpbs_wc_edit_forms_meta_fields', 10, 1);

function wpbs_wc_get_billing_fields()
{
    return array(
        'billing_first_name' => __('First Name', 'wp-booking-system-woocommerce'),
        'billing_last_name' => __('Last Name', 'wp-booking-system-woocommerce'),
        'billing_company' => __('Company', 'wp-booking-system-woocommerce'),
        'billing_address_1' => __('Address 1', 'wp-booking-system-woocommerce'),
        'billing_address_2' => __('Address 2', 'wp-booking-system-woocommerce'),
        'billing_city' => __('City', 'wp-booking-system-woocommerce'),
        'billing_postcode' => __('Postcode', 'wp-booking-system-woocommerce'),
        'billing_country' => __('Country', 'wp-booking-system-woocommerce'),
        'billing_state' => __('State', 'wp-booking-system-woocommerce'),
        'billing_email' => __('Email', 'wp-booking-system-woocommerce'),
        'billing_phone' => __('Phone', 'wp-booking-system-woocommerce'),
    );
}
