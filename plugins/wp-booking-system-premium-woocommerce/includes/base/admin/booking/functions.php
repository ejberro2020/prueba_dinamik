<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add payment details to the Booking Modal
 *
 * @param WPBS_Booking
 *
 */
function wpbs_wc_booking_modal_payment_tab_content($booking)
{
    
    $payment = wpbs_get_payment_by_booking_id($booking->get('id'));

    // Check if there is an order for this booking
    if (empty($payment)) {
        return false;
    }

    // Check if it's a WooCommerce order
    if ($payment->get('gateway') != 'woocommerce') {
        return false;
    }

    $order_details = $payment->get('details');
    $woocommerce_data = $order_details['raw'];

    // Payment Information
    $payment_information = array(
        array('label' => 'Order Status', 'value' => ucwords($payment->get('order_status'))),
        array('label' => 'Payment Gateway', 'value' => 'WooCommerce'),
        array('label' => 'Date', 'value' => date('j F Y, H:i:s', strtotime($payment->get('date_created')))),
        array('label' => 'ID', 'value' => '#' . $payment->get('id')),
        array('label' => 'WC Order ID', 'value' => $woocommerce_data['id']),
    );

    // Order Information

    $order_information = $payment->get_line_items();

    $order_information = apply_filters('wpbs_booking_details_order_information', $order_information, $payment);

    $amount_received = (isset($woocommerce_data['amount_received'])) ? ($woocommerce_data['amount_received']) : 0;

    $order_information[] = array('label' => 'Amount Received', 'value' => wpbs_get_formatted_price($amount_received, strtoupper($payment->get_currency())));

    // Include view file
    include WPBS_PLUGIN_DIR . '/includes/modules/pricing/booking/views/view-modal-payment-details-content.php';

}
add_action('wpbs_booking_modal_tab_content_payment', 'wpbs_wc_booking_modal_payment_tab_content', 10, 1);
