<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display the final payment of the part payments checkout form.
 *
 * @param string $output
 * @param WPBS_Payment $payment
 * @param string $language
 *
 * @return string
 *
 */
function wpbs_final_payment_woocommerce($output, $payment, $language)
{

    // Get total amount
    $total = $payment->get_total_second_payment();

    // Get plugin settings
    $settings = get_option('wpbs_settings', array());
    
    /**
     * Save booking data for later use
     *
     */

    $booking = wpbs_get_booking($payment->get('booking_id'));
    $post_data = array();
    $post_data['calendar']['start_date'] = strtotime($booking->get('start_date')) * 1000;
    $post_data['calendar']['end_date'] = strtotime($booking->get('end_date')) * 1000;
    $post_data['form']['id'] = $booking->get('form_id');

    $booking_data = array(
        'order_id' => $payment->get('id'),
        'post_data' => $post_data,
        'form_fields' => $booking->get('fields'),
        'language' => $language,
        'calendar_id' => $booking->get('calendar_id'),
        'return_url' => add_query_arg(array('wpbs-wc-deposit-confirmation' => 1, 'wpbs-payment-id' => $payment->get('order_id')), get_permalink()),
        'amount_to_pay' => $total,
        'payment_type' => 'deposit'
    );

    
    // Generate an unique ID for the transient id
    $booking_data_transient_id = 'wpbs-woocommerce-' . current_time('timestamp') . '-' . $payment->get('id');

    // Save all the booking details for later use, if the payment is accepted
    set_transient($booking_data_transient_id, $booking_data, DAY_IN_SECONDS * 7);

    wpbs_wc_add_to_cart($booking_data_transient_id);

    /**
     * Prepare Response
     *
     */

    $output = '';

    $output .= '<div class="wpbs-payment-confirmation-woocommerce-form">';

    if (wpbs_part_payments_enabled() == true && $payment->is_part_payment()) {
        $output .= '<label>' . wpbs_get_payment_default_string('amount_billed', $language) . '</label><input class="wpbs-payment-confirmation-woocommerce-input" type="text" value="' . wpbs_get_formatted_price($total, $payment->get_currency()) . '" readonly>';
    }

    $output .= '<div id="wpbs-woocommerce-payment-button"><a href="' . wc_get_checkout_url() . '"><span>' . wpbs_wc_get_button_label($language) . '</span></a></div>';

    $output .= '</div>';

    return $output;

}
add_filter('wpbs_final_payment_woocommerce', 'wpbs_final_payment_woocommerce', 10, 3);

/**
 * Process the final payment of the part payments checkout form.
 *
 * @param array $post_data
 * @param array $payment
 *
 */
function wpbs_save_final_payment_woocommerce()
{

    // Check if we're on the woocommerce confirmation page
    if (!isset($_GET['wpbs-wc-deposit-confirmation'])) {
        return false;
    }

    // Check if woocommerce is enabled
    if (!isset($_GET['wpbs-wc-order-id'])) {
        return false;
    }

    $wc_order_id = absint($_GET['wpbs-wc-order-id']);

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

    $language = $booking_data['language'];

    // Get the order ID from the WC response
    $order_id = $booking_data['order_id'];

    // Get the order from the database
    $payment = wpbs_get_payment($order_id);

    if (is_null($payment)) {
        return false;
    }

    // Get site options
    $settings = get_option('wpbs_settings', array());

    // Get price
    $details = $payment->get('details');

    // Check if final payment wasn't made yet
    if ($payment->is_final_payment_paid()) {
        return false;
    }

    global $form_output;

    $form_output = '';

    // If the payment failed
    if ($wc_order->get_status() == 'failed') {
        $form_output .= '<p class="wpbs-payment-error-message">The payment was declined. Your credit card or bank account was not charged.</p>';
        $status = 'error';
        // If the payment succeeded
    } else {

        // Delete the transient to prevent double bookings
        delete_transient($wpbs_payment_id);

        $details['part_payments']['final_payment'] = true;

        $details['raw']['id'] .= ', ' . '<a target="_blank" href="' . get_edit_post_link($wc_order_id) . '">#' . $wc_order_id . '</a>';
        $details['raw']['amount_received'] = $details['raw']['amount_received'] + $wc_order->get_total();

        $status = 'completed';

        // Create the output containing the confirmation message and the tracking script
        $confirmation_message = (!empty($settings['payment_part_payments_confirmation_translation_' . $language])) ? $settings['payment_part_payments_confirmation_translation_' . $language] : (!empty($settings['payment_part_payments_confirmation']) ? $settings['payment_part_payments_confirmation'] : __('The form was successfully submitted.', 'wp-booking-system'));

        $form_output .= apply_filters('the_content', $confirmation_message);
    }

    $payment_data = array(
        'order_status' => $status,
        'details' => $details,
    );

    // Save Order
    wpbs_update_payment($payment->get('id'), $payment_data);

    // Replace the form with the confirmation message
    add_filter('wpbs_final_payment_output', function ($output) {
        global $form_output;
        return '<div class="wpbs-main-wrapper"><div class="wpbs-payment-confirmation wpbs-final-payment-confirmation">' . $form_output . '</div></div>';
    });

}
add_action('init', 'wpbs_save_final_payment_woocommerce', 10, 2);
