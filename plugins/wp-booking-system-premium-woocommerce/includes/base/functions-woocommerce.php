<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get the ID of the product linked to WP Booking System.
 *
 * @return int
 *
 */
function wpbs_wc_get_booking_item_id()
{
    // Get Settings
    $settings = get_option('wpbs_settings', array());

    $product_id = (isset($settings['payment_wc_product_id'])) ? absint($settings['payment_wc_product_id']) : null;

    $product_id = apply_filters('wpbs_wc_product_id', $product_id);

    // Check if product is set
    if (empty($product_id)) {
        return false;
    }

    // Return product id
    return $product_id;
}

/**
 * Change the product price in the cart to match the price of the booking
 *
 */
function wpbs_wc_change_product_price($cart)
{

    if (!$cart) {
        $cart = wc()->cart;
    }

    // Loop through cart items
    foreach ($cart->cart_contents as $cartItemId => $cartItem) {

        if ($cartItem['product_id'] == wpbs_wc_get_booking_item_id()) {

            // Get payment id
            $payment_id = $cartItem['_wpbs_payment_id'];

            if (!$payment_id) {
                return false;
            }

            $payment_data = get_transient($payment_id);

            // Set the price
            $cartItem['data']->set_price($payment_data['amount_to_pay']);

            // Customize the name to include the calendar name.
            if (isset($cartItem['_calendar_name']) && strpos($cartItem['data']->get_name(), $cartItem['_calendar_name']) === false) {
                $cartItem['data']->set_name($cartItem['data']->get_name() . ' - ' . $cartItem['_calendar_name']);
            }

            break;
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'wpbs_wc_change_product_price', 10, 1);
add_action('woocommerce_before_cart_contents', 'wpbs_wc_change_product_price', 10, 1);

/**
 * Force the quantity of the booking product to always be 1
 *
 * @param object
 *
 */
function wpbs_wc_change_product_quantity($cart)
{

    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    if (did_action('woocommerce_before_calculate_totals') >= 2) {
        return;
    }

    if (!$cart) {
        $cart = wc()->cart;
    }

    foreach ($cart->cart_contents as $cartItemId => $cartItem) {

        if ($cartItem['product_id'] == wpbs_wc_get_booking_item_id()) {

            $cart->set_quantity($cartItemId, 1);

            break;
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'wpbs_wc_change_product_quantity', 20, 1);

/**
 * Replace the quantity selector with a plain text message
 *
 */
function wpbs_cart_qty($product_quantity, $cart_item_key, $cart_item)
{
    if ($cart_item['product_id'] == wpbs_wc_get_booking_item_id()) {
        return '<span class="wpbs-woocommerce-fixed-quantity">' . $cart_item['quantity'] . '</span>';
    }

    return $product_quantity;
}
add_filter('woocommerce_cart_item_quantity', 'wpbs_cart_qty', 10, 3);

/**
 * Save the payment ID meta value
 *
 */
function wpbs_wc_save_payment_id($itemId, $orderItem, $orderId)
{
    if (isset($orderItem->legacy_values['_wpbs_payment_id'])) {
        wc_update_order_item_meta($itemId, '_wpbs_payment_id', $orderItem->legacy_values['_wpbs_payment_id']);
        wc_update_order_item_meta($itemId, '_calendar_name', $orderItem->legacy_values['_calendar_name']);
        wc_update_order_item_meta($itemId, '_booking_start_date', $orderItem->legacy_values['_booking_start_date']);
        wc_update_order_item_meta($itemId, '_booking_end_date', $orderItem->legacy_values['_booking_end_date']);
    }
}
add_action('woocommerce_new_order_item', 'wpbs_wc_save_payment_id', 10, 3);

/**
 * Helper function to add the booking product to the cart
 *
 * @param string $id
 *
 */
function wpbs_wc_add_to_cart($id)
{
    // Get cart
    $cart = WC()->cart;

    if (!method_exists($cart, 'add_to_cart')) {
        return false;
    }

    // Empty it.
    $cart->empty_cart();

    $quantity = 1;

    $payment_data = get_transient($id);

    $calendar = wpbs_get_calendar($payment_data['calendar_id']);

    $cart_meta = array(
        '_wpbs_payment_id' => $id,
        '_calendar_name' => $calendar->get_name(),
        '_booking_start_date' => wpbs_date_i18n(get_option('date_format'), wpbs_convert_js_to_php_timestamp($payment_data['post_data']['calendar']['start_date'])),
        '_booking_end_date' => wpbs_date_i18n(get_option('date_format'), wpbs_convert_js_to_php_timestamp($payment_data['post_data']['calendar']['end_date'])),
    );

    $cart->add_to_cart(wpbs_wc_get_booking_item_id(), $quantity, 0, array(), $cart_meta);
}

/**
 * Pre-fill Checkout form values with booking form values
 *
 */
add_filter('woocommerce_checkout_get_value', function ($input, $key) {

    if (!isset($cart)) {
        $cart = wc()->cart;
    }

    // Loop through cart items
    foreach ($cart->cart_contents as $cartItemId => $cartItem) {

        if ($cartItem['product_id'] == wpbs_wc_get_booking_item_id()) {

            // Get payment id
            $payment_id = $cartItem['_wpbs_payment_id'];

            if (!$payment_id) {
                return false;
            }

            $payment_data = get_transient($payment_id);

            break;
        }
    }

    if(!isset($payment_data)){
        return $input;
    }

    $form_id = $payment_data['post_data']['form']['id'];

    $field_mapping_id = wpbs_get_form_meta($form_id, 'wc_field_mapping_' . $key, true);

    if (empty($field_mapping_id)) {
        return $input;
    }

    $form_fields = $payment_data['form_fields'];

    foreach ($form_fields as $form_field) {
        if ($form_field['id'] != $field_mapping_id) {
            continue;
        }

        // Get value
        $value = (isset($form_field['user_value'])) ? $form_field['user_value'] : '';

        // Handle Pricing options differently
        if (wpbs_form_field_is_product($form_field['type'])) {
            $value = wpbs_get_form_field_product_values($form_field);
        }

        $value = wpbs_get_field_display_user_value($value);

        return $value;

    }

    return $input;

}, 10, 2);

/**
 * Check the cart for invalid booking products or items
 *
 */
function wpbs_wc_check_cart()
{
    if (!WC()->cart) {
        return;
    }

    $reservation_in_cart = false;

    // Check booking product
    foreach (WC()->cart->get_cart() as $cartItemKey => $cartItem) {
        if ($cartItem['product_id'] == wpbs_wc_get_booking_item_id()) {

            $reservation_in_cart = true;

            // Check if a payment_id is present
            if (!array_key_exists('_wpbs_payment_id', $cartItem)) {
                WC()->cart->remove_cart_item($cartItemKey);
                continue;
            }

            $booking_transient_id = $cartItem['_wpbs_payment_id'];

            // Check if the payment_id still exists
            if (!get_transient($booking_transient_id)) {
                WC()->cart->remove_cart_item($cartItemKey);
                continue;
            }

        }
    }

    // Check that there are no other products
    if ($reservation_in_cart === true) {
        foreach (WC()->cart->get_cart() as $cartItemKey => $cartItem) {
            if ($cartItem['product_id'] == wpbs_wc_get_booking_item_id()) {

                continue;
            }

            WC()->cart->remove_cart_item($cartItemKey);

            wc_add_notice(__("Other products cannot be added to the cart while a reservation is present."), 'error');

        }
    }
}
add_action('wp_loaded', 'wpbs_wc_check_cart', 10, 0);

/**
 * When an order is completed, redirect the user back to the calendar page
 *
 */
function wpbs_wc_maybe_redirect_after_order_received()
{
    // Check if we are on the order-received page
    if (is_wc_endpoint_url('order-received')) {
        global $wp;

        $settings = get_option('wpbs_settings', array());

        // Get the order ID from the browser url
        $request_url = explode('/', $wp->request);
        $order_id = intval(end($request_url));

        // Get an instance of the WC_Order object
        $order = wc_get_order($order_id);

        if (!$order) {
            return;
        }

        // If the order status is 'failed' we stop the function
        if ($order->has_status('failed')) {
            return;
        }

        foreach ($order->get_items() as $orderItemId => $orderItem) {
            if ($orderItem->get_product_id() != wpbs_wc_get_booking_item_id()) {
                continue;
            }

            $wpbs_payment_id = $orderItem->get_meta('_wpbs_payment_id');

            if (empty($wpbs_payment_id)) {
                return false;
            }

            $order_id = $orderItem->get_order_id();

            // Check if we skip WooCommerce's payment confirmation page or not
            if (!isset($settings['payment_wc_skip_confirmation_page']) || $settings['payment_wc_skip_confirmation_page'] != 'on') {
                // We don't skip it, just save our data in the database
                wpbs_wc_save_payment($order_id);
            } else {
                // We redirect back to the calendar page
                $payment_data = get_transient($wpbs_payment_id);
                $redirect_url = add_query_arg(array('wpbs-wc-order-id' => $order_id), $payment_data['return_url']);
                wp_redirect($redirect_url);
                exit;
            }

        }
    }
}
add_action('template_redirect', 'wpbs_wc_maybe_redirect_after_order_received');

/**
 * Make the booking product a virtual one
 *
 */
function wpbs_wc_force_virtual($virtual, $product)
{
    if ($product->get_id() == wpbs_wc_get_booking_item_id()) {
        $virtual = true;
    }
    return $virtual;
}
add_filter('woocommerce_is_virtual', 'wpbs_wc_force_virtual', 10, 2);

/**
 * Make the booking product hidden
 *
 */
function wpbs_wc_force_invisivle($visible, $product_id)
{
    if ($product_id == wpbs_wc_get_booking_item_id()) {
        $visible = false;
    }
    return $visible;
}
add_filter('woocommerce_product_is_visible', 'wpbs_wc_force_invisivle', 10, 2);

/**
 * Hide the payment id meta on the orders page
 *
 */
function wpbs_wc_hide_order_itemmeta($meta)
{
    $meta[] = '_wpbs_payment_id';
    return $meta;
}
add_filter('woocommerce_hidden_order_itemmeta', 'wpbs_wc_hide_order_itemmeta', 10, 1);

/**
 * Disable WooCommerce customer emails for new orders
 *
 * @param bool $enabled
 * @param WC_Order $order
 *
 * @return bool
 *
 */
function wpbs_wc_disable_emails($enabled, $order)
{
    $settings = get_option('wpbs_settings', array());

    // Check if option is enabled
    if (!isset($settings['payment_wc_disable_emails']) || $settings['payment_wc_disable_emails'] != 'on') {
        return $enabled;
    }

    if(is_null($order)){
        return $enabled;
    }

    // Check if a
    foreach ($order->get_items() as $orderItemId => $orderItem) {
        if ($orderItem->get_product_id() == wpbs_wc_get_booking_item_id()) {
            return false;
        }
    }
    return $enabled;
}
add_filter('woocommerce_email_enabled_customer_processing_order', 'wpbs_wc_disable_emails', 10, 2);
add_filter('woocommerce_email_enabled_customer_completed_order', 'wpbs_wc_disable_emails', 10, 2);

function wpbs_wc_email_order_details($order, $sent_to_admin, $plain_text, $email)
{

    $payment_data = false;

    foreach ($order->get_items() as $orderItemId => $orderItem) {
        if ($orderItem->get_product_id() != wpbs_wc_get_booking_item_id()) {
            continue;
        }

        $wpbs_payment_id = $orderItem->get_meta('_wpbs_payment_id');

        if (empty($wpbs_payment_id)) {
            return false;
        }

        $payment_data = get_transient($wpbs_payment_id);

    }

    if (empty($payment_data)) {
        return false;
    }

    $form_id = $payment_data['post_data']['form']['id'];
    $language = $payment_data['post_data']['calendar']['language'];
    $calendar_name = wpbs_get_calendar($payment_data['calendar_id'])->get_name();

    $fields_array = [
        wpbs_get_form_default_string($form_id, 'start_date', $language) => wpbs_date_i18n(get_option('date_format'), wpbs_convert_js_to_php_timestamp($payment_data['post_data']['calendar']['start_date'])),
        wpbs_get_form_default_string($form_id, 'end_date', $language) => wpbs_date_i18n(get_option('date_format'), wpbs_convert_js_to_php_timestamp($payment_data['post_data']['calendar']['end_date'])),
        __('Calendar Name', 'wp-booking-system-woocommerce') => $calendar_name,
    ];

    $html_output = '<h2>' . __('Booking Details', 'wp-booking-system-woocommerce') . '</h2><table cellspacing="0" cellpadding="6" class="wpbs-order-meta"><tbody>';

    foreach ($fields_array as $label => $value) {
        if (!empty($value)) {
            $html_output .= '
            <tr>
                <th>' . $label . '</th>
                <td>' . $value . '</td>
            </tr>';
        }
    }

    $html_output .= '</tbody></table><br>';

    $html_output .= '
    <style>
        .wpbs-order-meta {width: 100%; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; margin-bottom:8px;}
        .wpbs-order-meta th {text-align: left; border-top-width: 4px; color: #737373; border: 1px solid #e4e4e4; padding: 12px; width:58%;}
        .wpbs-order-meta td {text-align: left; border-top-width: 4px; color: #737373; border: 1px solid #e4e4e4; padding: 12px;}
    </style>';

    echo $html_output;
}
add_action('woocommerce_email_order_details', 'wpbs_wc_email_order_details', 25, 4);

/**
 * Display meta keys nicely in wp-admin
 *
 */
function wpbs_order_item_display_meta_key($display_key)
{
    if ($display_key == '_calendar_name') {
        return __('Calendar Name', 'wp-booking-system-woocommerce');
    }

    if ($display_key == '_booking_start_date') {
        return __('Start Date', 'wp-booking-system-woocommerce');
    }

    if ($display_key == '_booking_end_date') {
        return __('End Date', 'wp-booking-system-woocommerce');
    }

    return $display_key;
}
add_filter('woocommerce_order_item_display_meta_key', 'wpbs_order_item_display_meta_key', 10, 2);
