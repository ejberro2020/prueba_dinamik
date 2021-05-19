<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Ignore reCaptcha on confirmation screen
 *
 */
function wpbs_validate_recaptcha_payment_confirmation_woocommerce($response, $form_data)
{
    if (isset($form_data['wpbs-woocommerce-confirmation-loaded']) && $form_data['wpbs-woocommerce-confirmation-loaded'] == '1') {
        return true;
    }
    return $response;
}
add_filter('wpbs_validate_recaptcha_payment_confirmation', 'wpbs_validate_recaptcha_payment_confirmation_woocommerce', 10, 2);

/**
 * Show the payment confirmation page after submitting the form
 *
 */
function wpbs_wc_submit_form_payment_confirmation($response, $post_data, $form, $form_args, $form_fields, $calendar_id)
{

    // Check if another payment method was already found
    if ($response !== false) {
        return $response;
    }

    $payment_found = false;

    // Check if payment method is enabled.
    foreach ($form_fields as $form_field) {
        if ($form_field['type'] == 'payment_method' && $form_field['user_value'] == 'woocommerce') {
            $payment_found = true;
            break;
        }
    }

    if ($payment_found === false) {
        return false;
    }

    // Parse POST data
    parse_str($post_data['form_data'], $form_data);

    // Check if the payment screen was shown
    if (isset($form_data['wpbs-woocommerce-confirmation-loaded']) && $form_data['wpbs-woocommerce-confirmation-loaded'] == '1') {
        return false;
    }

    // Check if a WooCommerce Product is selected
    if (wpbs_wc_get_booking_item_id() === false) {
        return json_encode(
            array(
                'success' => false,
                'html' => '<p class="wpbs-form-general-error">' . __("No WooCommerce product is selected for handling reservations.", 'wp-booking-system-woocommerce') . '</p>',
            )
        );
    }

    // Check if WPBS currency matches WC currency
    if (get_woocommerce_currency() !== wpbs_get_currency()) {
        return json_encode(
            array(
                'success' => false,
                'html' => '<p class="wpbs-form-general-error">' . __("The currency in WooCommerce does not match the one set in WP Booking System.", 'wp-booking-system-woocommerce') . '</p>',
            )
        );
    }

    // Generate form
    $form_outputter = new WPBS_Form_Outputter($form, $form_args, $form_fields, $calendar_id);

    // Check if post data exists and matches form values
    if (wpbs_validate_payment_form_consistency($form_fields) === false) {
        return json_encode(
            array(
                'success' => false,
                'html' => '<strong>' . __('Something went wrong. Please refresh the page and try again.', 'wp-booking-system-woocommerce') . '</strong>',
            )
        );
    }

    // Get price
    $payment = new WPBS_Payment;
    $prices = $payment->calculate_prices($post_data, $form, $form_args, $form_fields);

    $total = $payment->get_total();

    // Check if part payments are used
    if (wpbs_part_payments_enabled() == true && $payment->is_part_payment()) {
        $total = $payment->get_total_first_payment();
    }

    // Check if price is greater than the minimum allowed, 0.5;
    if ($total <= 0.5) {
        return json_encode(
            array(
                'success' => false,
                'html' => '<p class="wpbs-form-general-error">' . __("The minimum payable amount is 0.50$", 'wp-booking-system-woocommerce') . '</p>',
            )
        );
    }

    // Get plugin settings
    $settings = get_option('wpbs_settings', array());

    // Invoice item description
    $invoice_item_description = (!empty($settings['payment_wc_invoice_name_translation_' . $form_outputter->get_language()])) ? $settings['payment_wc_invoice_name_translation_' . $form_outputter->get_language()] : (!empty($settings['payment_wc_invoice_name']) ? $settings['payment_wc_invoice_name'] : get_bloginfo('name') . ' Booking');

    /**
     * Save Booking data
     *
     */
    $booking_data = array(
        'post_data' => $post_data,
        'form_id' => $form->get('id'),
        'form_args' => $form_args,
        'form_fields' => $form_fields,
        'calendar_id' => $calendar_id,
        'prices' => $prices,
        'amount_to_pay' => $total,
        'return_url' => add_query_arg( array('wpbs-wc-confirmation' => 1), $form_data['wpbs-return-url']),
        'payment_type' => '',
    );

    // Generate an unique ID for the transient id
    $booking_data_transient_id = 'wpbs-woocommerce-' . current_time('timestamp') . '_' . $form_outputter->get_unique();

    // Save all the booking details for later use, if the payment is accepted
    set_transient($booking_data_transient_id, $booking_data, DAY_IN_SECONDS * 7);

    wpbs_wc_add_to_cart($booking_data_transient_id);

    /**
     * Prepare Response
     *
     */
    $woocommerce_output = '';

    $woocommerce_output .= '<div class="wpbs-payment-confirmation-woocommerce-form">';

    if (wpbs_part_payments_enabled() == true && $payment->is_part_payment()) {
        $woocommerce_output .= '<label>' . wpbs_get_payment_default_string('amount_billed', $form_outputter->get_language()) . '</label><input class="wpbs-payment-confirmation-woocommerce-input" type="text" value="' . wpbs_get_formatted_price($total, $payment->get_currency()) . '" readonly>';
    }

    $woocommerce_output .= '<div id="wpbs-woocommerce-payment-button"><a href="' . wc_get_checkout_url() . '"><span>'.wpbs_wc_get_button_label($form_outputter->get_language()).'</span></a></div>';

    $woocommerce_output .= '</div>';

    $woocommerce_output .= '
    <script>
        jQuery(".wpbs-woocommerce-payment-confirmation-inner-' . $form_outputter->get_unique() . '").parents(".wpbs-main-wrapper").find(".wpbs-container").addClass("wpbs-disable-selection");
    </script>
    ';

    $output = wpbs_form_payment_confirmation_screen($form_outputter, $payment, 'woocommerce', $woocommerce_output);

    return json_encode(
        array(
            'success' => false,
            'html' => $output,
        )
    );

}
add_filter('wpbs_submit_form_before', 'wpbs_wc_submit_form_payment_confirmation', 10, 6);

/**
 * Save the order in the database and maybe capture the payment
 *
 */
function wpbs_wc_action_save_payment_details($booking_id, $post_data, $form, $form_args, $form_fields)
{

    // Parse POST data
    parse_str($post_data['form_data'], $form_data);

    // Check if woocommerce is enabled
    if (!isset($post_data['wpbs-wc-order-id'])) {
        return false;
    }

    $order_id = absint($post_data['wpbs-wc-order-id']);

    // Get order
    $order = wc_get_order($order_id);

    // Get price
    $payment = new WPBS_Payment;
    $details['price'] = $payment->calculate_prices($post_data, $form, $form_args, $form_fields);

    if (wpbs_part_payments_enabled() == true && $payment->is_part_payment()) {
        $details['part_payments'] = array('deposit' => false, 'final_payment' => false);
    }

    if (isset($details['part_payments']['deposit'])) {
        $details['part_payments']['deposit'] = true;
    }

    $details['raw']['id'] = '<a target="_blank" href="' . get_edit_post_link($order_id) . '">#' . $order_id . '</a>';
    $details['raw']['amount_received'] = $order->get_total();

    $status = 'completed';

    // Save Order
    wpbs_insert_payment(array(
        'booking_id' => $booking_id,
        'gateway' => 'woocommerce',
        'order_id' => $order_id,
        'order_status' => $status,
        'details' => $details,
        'date_created' => current_time('Y-m-d H:i:s'),
    ));

}
add_action('wpbs_submit_form_after', 'wpbs_wc_action_save_payment_details', 10, 5);

/**
 * Process the data after WooCommerce redirected us back to the calendar page.
 *
 */
function wpbs_wc_process_response()
{

    // Check if we're on the woocommerce confirmation page
    if (!isset($_GET['wpbs-wc-confirmation'])) {
        return false;
    }

    // Check if an id was returned
    if (!isset($_GET['wpbs-wc-order-id'])) {
        return false;
    }

    $order_id = absint($_GET['wpbs-wc-order-id']);

    // Get order
    $order = wc_get_order($order_id);

    // Get Payment Data
    foreach ($order->get_items() as $orderItemId => $orderItem) {
        if ($orderItem->get_product_id() != wpbs_wc_get_booking_item_id()) {
            continue;
        }

        // Get transient with booking data
        $wpbs_payment_id = $orderItem->get_meta('_wpbs_payment_id');

    }

    // Exit if no payment data was sent.
    if (!isset($wpbs_payment_id)) {
        return false;
    }

    // Get booking data from transient
    $booking_data = get_transient($wpbs_payment_id);

    // Check if booking data exists
    if (empty($booking_data)) {
        return false;
    }

    // Disable date selection on calendar
    add_filter('wpbs_calendar_outputter_custom_class', function () {
        return 'wpbs-disable-selection wpbs-scroll-to-calendar';
    });

    global $form_output;

    $form_output = '';

    // If the payment failed
    if ($order->get_status() == 'failed') {
        $form_output .= '<p class="wpbs-payment-error-message">The payment was declined. Your credit card or bank account was not charged.</p>';

        // If the payment succeeded
    } else {

        // Delete the transient to prevent double bookings
        delete_transient($wpbs_payment_id);

        // Add the payment ID to the $post_data
        $booking_data['post_data']['wpbs-wc-order-id'] = $order_id;

        // Finally submit the form, saving booking details, sending emails, etc
        $handler = new WPBS_Form_Handler($booking_data['post_data'], $booking_data['form_id'], $booking_data['form_args'], $booking_data['form_fields'], $booking_data['calendar_id']);
        $response = $handler->get_response();

        // Handle the form confirmation
        if ($response['confirmation_type'] == 'redirect') {

            // Redirect to a page
            wp_redirect($response['confirmation_redirect_url']);
            exit();

        } else {

            // Create the output containing the confirmation message and the tracking script
            $confirmation_message = (!empty($response['confirmation_message'])) ? $response['confirmation_message'] : '<p>The form was successfully submitted.</p>';

            $form_output .= '<div class="wpbs-form-confirmation-message">' . $confirmation_message . '</div>';

            if (isset($response['tracking_script']) && !empty($response['tracking_script'])) {
                $form_output .= '<script>' . $response['tracking_script'] . '</script>';
            }

        }
    }

    // Replace the form with the confirmation message
    add_filter('wpbs_form_outputter_form', function ($output) {
        global $form_output;
        return $form_output;
    });

}
add_action('init', 'wpbs_wc_process_response', 20);

/**
 * Handles the saving of booking data without redirecting back to the calendar and showing the confirmation message.
 * 
 * @param string $wc_order_id
 * 
 */
function wpbs_wc_save_payment($wc_order_id)
{
    // Get order
    $wc_order = wc_get_order($wc_order_id);
    
    // Get Payment Data
    foreach ($wc_order->get_items() as $orderItemId => $orderItem) {
        if ($orderItem->get_product_id() != wpbs_wc_get_booking_item_id()) {
            continue;
        }

        // Get transient with booking data
        $wpbs_payment_id = $orderItem->get_meta('_wpbs_payment_id');

    }

    // Exit if no payment data was sent.
    if (!isset($wpbs_payment_id)) {
        return false;
    }
    
    // Get booking data from transient
    $booking_data = get_transient($wpbs_payment_id);

    // Check if booking data exists
    if (empty($booking_data)) {
        return false;
    }


    if ($wc_order->get_status() != 'failed') {

        // Delete the transient to prevent double bookings
        delete_transient($wpbs_payment_id);

        if ($booking_data['payment_type'] == 'deposit') {

            // Get the order ID from the WC response
            $order_id = $booking_data['order_id'];

            // Get the order from the database
            $order = wpbs_get_payment($order_id);

            $details = $order->get('details');

            $details['part_payments']['final_payment'] = true;

            $details['raw']['id'] .= ', ' . '<a target="_blank" href="' . get_edit_post_link($wc_order_id) . '">#' . $wc_order_id . '</a>';
            $details['raw']['amount_received'] = $details['raw']['amount_received'] + $wc_order->get_total();

            $status = 'completed';

            $order_data = array(
                'order_status' => $status,
                'details' => $details,
            );

            wpbs_update_payment($order->get('id'), $order_data);
        } else {
            // Add the payment ID to the $post_data
            $booking_data['post_data']['wpbs-wc-order-id'] = $wc_order_id;

            // Finally submit the form, saving booking details, sending emails, etc
            $handler = new WPBS_Form_Handler($booking_data['post_data'], $booking_data['form_id'], $booking_data['form_args'], $booking_data['form_fields'], $booking_data['calendar_id']);
        }
    }
}
