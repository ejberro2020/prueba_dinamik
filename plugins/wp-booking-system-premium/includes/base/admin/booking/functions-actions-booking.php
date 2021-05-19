<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save booking data
 *
 */
function wpbs_save_booking_data($data)
{

    if (!isset($_POST['booking_id'])) {
        return false;
    }

    /**
     * Save Calendar Data
     */

    $booking_id = absint($_POST['booking_id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    // Get action
    $action = sanitize_text_field($_POST['booking_action']);

    // Set status
    if ($action == 'delete') {
        $status = 'trash';

        // Save current status
        wpbs_add_booking_meta($booking_id, 'before_trash_status', $booking->get('status'));

    } elseif ($action == 'restore') {

        // Get old status
        $status = (in_array(wpbs_get_booking_meta($booking_id, 'before_trash_status', true), array('pending', 'accepted'))) ? wpbs_get_booking_meta($booking_id, 'before_trash_status', true) : 'pending';

        // Delete it from the database
        wpbs_delete_booking_meta($booking_id, 'before_trash_status');

    } elseif ($action == 'accept') {

        do_action('wpbs_save_booking_data_accept_booking', $booking);

        $status = 'accepted';
    }

    // Prepare Data
    $booking_data = array(
        'status' => $status,
        'date_modified' => current_time('Y-m-d H:i:s'),
    );

    // Update Booking
    wpbs_update_booking($booking_id, $booking_data);

    /**
     * Send Email
     */

    $language = wpbs_get_booking_meta($booking_id, 'submitted_language', true);

    // Parse $_POST data
    parse_str($_POST['email_form_data'], $_POST['email_form_data']);

    // Check if we need to send an email
    if (isset($_POST['email_form_data']['booking_email_accept_booking_enable']) && !empty($_POST['email_form_data']['booking_email_accept_booking_enable'])) {

        $email_form_data = $_POST['email_form_data'];

        // Parse some form tags
        $email_tags = new WPBS_Email_Tags(wpbs_get_form($booking->get('form_id')), wpbs_get_calendar($booking->get('calendar_id')), $booking_id, $booking->get('fields'), $language, strtotime($booking->get('start_date')), strtotime($booking->get('end_date')));

        $email_form_data['booking_email_accept_booking_message'] = $email_tags->parse(nl2br($email_form_data['booking_email_accept_booking_message']));
        $email_form_data['booking_email_accept_booking_subject'] = $email_tags->parse($email_form_data['booking_email_accept_booking_subject']);

        // Send the email
        $mailer = new WPBS_Booking_Mailer($booking, $email_form_data);
        $mailer->prepare('accept_booking');
        $mailer->send();

    }

}
add_action('wpbs_save_calendar_data', 'wpbs_save_booking_data');

/**
 * Permanently Delete Booking
 *
 */
function wpbs_action_permanently_delete_booking()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_permanently_delete_booking')) {
        return;
    }

    if (empty($_GET['booking_id'])) {
        return;
    }

    if (empty($_GET['calendar_id'])) {
        return;
    }

    $booking_id = $_GET['booking_id'];

    $calendar_id = $_GET['calendar_id'];

    do_action('wpbs_permanently_delete_booking', $booking_id);

    // Delete Booking
    wpbs_delete_booking($booking_id);

    // Delete Booking Meta
    $booking_meta = wpbs_get_booking_meta($booking_id);
    if (!empty($booking_meta)) {
        foreach ($booking_meta as $key => $value) {
            wpbs_delete_booking_meta($booking_id, $key);
        }
    }

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'edit-calendar', 'calendar_id' => $calendar_id, 'wpbs_message' => 'booking_permanently_delete_success'), admin_url('admin.php')));
}
add_action('wpbs_action_permanently_delete_booking', 'wpbs_action_permanently_delete_booking');

/**
 * Export Bookings in CSV Format
 *
 */
function wpbs_action_export_bookings()
{
    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_export_bookings')) {
        return;
    }

    // Get the calendar ID
    $calendar_id = $_GET['calendar_id'];

    // Get the bookings
    $bookings = wpbs_get_bookings(array('status' => array('pending', 'accepted'), 'calendar_id' => $calendar_id));

    /**
     * Build the CSV header
     *
     */

    // Add some default fields
    $csv_header = array('Booking ID' => '-', 'Start Date' => '-', 'End Date' => '-', 'Date Created' => '-');

    // Assume no payment was made for bookings
    $payment_exists = false;

    $settings = get_option('wpbs_settings');

    // Loop through all bookings and get all the form fields - in case bookings were accepted from more than one form
    foreach ($bookings as $booking) {

        // Get fields
        $fields = $booking->get('fields');

        // Check if at least one payment was made, to include the Total field
        if ($payment_exists === false) {
            $payments = wpbs_get_payments(array('booking_id' => $booking->get('id')));
            if (!empty($payments)) {
                $payment_exists = true;
            }
        }

        // Loop through fields
        foreach ($fields as $field) {

            // Check for excluded fields
            if (in_array($field['type'], wpbs_get_excluded_fields(array('hidden')))) {
                continue;
            }

            // Check if a label exists
            if (empty($field['values']['default']['label'])) {
                continue;
            }

            // Add the label to the CSV header
            $csv_header[$field['values']['default']['label']] = '-';
        }

        $payment = wpbs_get_payment_by_booking_id($booking->get('id'));
        if (!empty($payment)) {
            $csv_header[$settings['payment_product_name']] = '-';
            foreach ($payment->get_line_items() as $line_item_key => $line_item) {

                if (in_array((string) $line_item_key, array('events', 'total'))) {
                    continue;
                }

                $csv_header[(isset($line_item['label_raw']) ? $line_item['label_raw'] : wpbs_format_html_string($line_item['label']))] = '-';
            }
        }
    }

    // If a payment method was found, add the Total Amount field to the header
    if ($payment_exists === true) {
        $csv_header['Total Amount'] = '-';
    }

    // This is where all the data will be;
    $csv_lines = array();

    // Add the CSV header
    foreach ($csv_header as $header_key => $header_value) {
        $csv_lines[0][$header_key] = $header_key;
    }

    // Loop through bookings again to get field data
    foreach ($bookings as $index => $booking) {

        $i = $index + 1;
        $csv_lines[$i] = $csv_header;

        $fields = $booking->get('fields');

        // Add standard fields
        $csv_lines[$i]['Booking ID'] = '#' . $booking->get('id');
        $csv_lines[$i]['Start Date'] = wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('start_date')));
        $csv_lines[$i]['End Date'] = wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('end_date')));
        $csv_lines[$i]['Date Created'] = wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('date_created')));

        // Loop through fields
        foreach ($fields as $field) {

            // Check for exluded fields
            if (in_array($field['type'], wpbs_get_excluded_fields(array('hidden')))) {
                continue;
            }

            // Check if key exists in header
            if (!array_key_exists($field['values']['default']['label'], $csv_lines[$i])) {
                continue;
            }

            // Get value
            $value = (isset($field['user_value'])) ? $field['user_value'] : '';

            // Handle Pricing options differently
            if (wpbs_form_field_is_product($field['type'])) {
                $value = wpbs_get_form_field_product_values($field);
            }

            // Format arrays
            $value = wpbs_get_field_display_user_value($value);

            // Payment methods
            if ($field['type'] == 'payment_method' && isset(wpbs_get_payment_methods()[$value])) {
                $value = wpbs_get_payment_methods()[$value];
            }

            // Add data to CSV
            $csv_lines[$i][$field['values']['default']['label']] = $value;
        }

        // Check if payments were found when building the headers
        if ($payment_exists === true) {

            // Get payment for current booking
            $payment = wpbs_get_payment_by_booking_id($booking->get('id'));
            if (empty($payment)) {
                continue;
            }

            $payment = wpbs_get_payment_by_booking_id($booking->get('id'));
            if (!is_null($payment)) {

                foreach ($payment->get_line_items() as $line_item_key => $line_item) {
                    if ($line_item_key == 'total') {
                        continue;
                    }
                    if ($line_item_key == 'events') {
                        $csv_lines[$i][$settings['payment_product_name']] = wpbs_format_html_string($line_item['value']);
                    } else {
                        $csv_lines[$i][(isset($line_item['label_raw']) ? $line_item['label_raw'] : wpbs_format_html_string($line_item['label']))] = wpbs_format_html_string($line_item['value']);
                    }
                }
            }

            // Add payment data to CSV
            $csv_lines[$i]['Total Amount'] = wpbs_get_formatted_price($payment->get_total(), $payment->get_currency());
        }

    }

    // Output headers so that the file is downloaded rather than displayed
    header('Content-type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="wpbs-bookings-export-calendar-' . $calendar_id . '-' . time() . '.csv"');

    // Do not cache the file
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM

    // Create a file pointer connected to the output stream
    $file = fopen('php://output', 'w');

    // Output each row of the data
    foreach ($csv_lines as $line) {
        $delimiter = apply_filters('wpbs_csv_delimiter', ',');
        fputcsv($file, $line, $delimiter);
    }

    exit();

}

add_action('wpbs_action_export_bookings', 'wpbs_action_export_bookings');