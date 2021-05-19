<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function wpbs_action_ajax_open_booking_details()
{

    // Nonce
    check_ajax_referer('wpbs_open_booking_details', 'wpbs_token');

    if (!isset($_POST['id'])) {
        return false;
    }

    $booking_id = absint($_POST['id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return;
    }

    // If booking is unread, make it read
    if ($booking->get('is_read') == 0) {
        $booking_data = array(
            'is_read' => 1,
        );
        wpbs_update_booking($booking_id, $booking_data);
    }

    // Get modal content
    $booking_display = new WPBS_Booking_Details_Outputter($booking);
    $booking_display->display();

    wp_die();

}
add_action('wp_ajax_wpbs_open_booking_details', 'wpbs_action_ajax_open_booking_details');

function wpbs_action_ajax_booking_email_customer()
{
    // Nonce
    check_ajax_referer('wpbs_booking_email_customer', 'wpbs_token');

    if (!isset($_POST['id'])) {
        return false;
    }

    $booking_id = absint($_POST['id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return;
    }
    parse_str($_POST['form_data'], $_POST['form_data']);

    $email_form_data = $_POST['form_data'];

    $language = wpbs_get_booking_meta($booking_id, 'submitted_language', true);

    // Parse some form tags
    $email_tags = new WPBS_Email_Tags(wpbs_get_form($booking->get('form_id')), wpbs_get_calendar($booking->get('calendar_id')), $booking_id, $booking->get('fields'), $language, strtotime($booking->get('start_date')), strtotime($booking->get('end_date')));

    $email_form_data['booking_email_customer_message'] = $email_tags->parse(nl2br($email_form_data['booking_email_customer_message']));
    $email_form_data['booking_email_customer_subject'] = $email_tags->parse($email_form_data['booking_email_customer_subject']);

    // Send the email
    $mailer = new WPBS_Booking_Mailer($booking, $email_form_data);
    $mailer->prepare('customer');
    $mailer->send();

    echo __('Email successfully sent.', 'wp-booking-system');

    wp_die();

}
add_action('wp_ajax_wpbs_booking_email_customer', 'wpbs_action_ajax_booking_email_customer');

/**
 * Edit Booking Details
 *
 */
function wpbs_action_ajax_wpbs_edit_booking_details()
{

    // Verify nonce
    if (empty($_POST['token']) || !wp_verify_nonce($_POST['token'], 'wpbs_edit_booking')) {
        return;
    }

    parse_str($_POST['form_data'], $data);

    if (!isset($data['booking_id'])) {
        return false;
    }

    $booking_id = absint($data['booking_id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return;
    }

    // Update Booking Details (Form Fields)
    if ($_POST['type'] == 'booking_details') {

        $fields = $booking->get('fields');

        foreach ($fields as &$field) {
            if (isset($data['wpbs-edit-booking-field-' . $field['id']]) && !empty(isset($data['wpbs-edit-booking-field-' . $field['id']]))) {
                if (wpbs_form_field_is_product($field['type'])) {
                    list($price, $value) = explode('|', $field['user_value']);
                    $field['user_value'] = $price . '|' . $data['wpbs-edit-booking-field-' . $field['id']];
                } else {
                    $field['user_value'] = esc_attr($data['wpbs-edit-booking-field-' . $field['id']]);
                }

            }
        }

        $booking_data = array('fields' => $fields);

        wpbs_update_booking($booking_id, $booking_data);

    }

    // Update Booking Data (Dates)
    if ($_POST['type'] == 'booking_data') {

        $start_date = DateTime::createFromFormat('Y-m-d', $data['wpbs-edit-booking-field-start_date']);
        $end_date = DateTime::createFromFormat('Y-m-d', $data['wpbs-edit-booking-field-end_date']);

        // Check if dates are in valid order
        if (!$start_date || !$end_date || $start_date > $end_date) {
            $response = array(
                'start_date' => wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('start_date'))),
                'end_date' => wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('end_date'))),
            );
        } else {
            $booking_data = array(
                'start_date' => wpbs_date_i18n('Y-m-d 00:00:00', $start_date->getTimestamp()),
                'end_date' => wpbs_date_i18n('Y-m-d 00:00:00', $end_date->getTimestamp()),
            );

            wpbs_update_booking($booking_id, $booking_data);

            $response = array(
                'start_date' => wpbs_date_i18n(get_option('date_format'), strtotime($booking_data['start_date'])),
                'end_date' => wpbs_date_i18n(get_option('date_format'), strtotime($booking_data['end_date'])),
            );

            

            $payment = wpbs_get_payment_by_booking_id($booking_id);
            $payment_details = $payment->get('details');
            $prices = $payment->get('prices');

            $selection_style = isset($prices['selection_style']) ? $prices['selection_style'] : 'split';

            $date_difference = ($end_date->diff($start_date));
            $booking_length = $selection_style == 'normal' ? $date_difference->days + 1 : $date_difference->days;
            
            $prices['quantity'] = $booking_length;

            $payment_details['price'] = $prices;
            wpbs_update_payment($payment->get('id'), array('details' => $payment_details));


        }

        $editable_crons = array(
            'reminder_email_date' => 'wpbs_er_reminder_email',
            'follow_up_email_date' => 'wpbs_er_follow_up_email',
            'payment_email_date' => 'wpbs_part_payments_payment_reminder_email',
        );

        foreach ($editable_crons as $field_name => $cron_name) {

            if (isset($data['wpbs-edit-booking-field-' . $field_name])) {
                $new_cron_date = DateTime::createFromFormat('Y-m-d H:i:s', $data['wpbs-edit-booking-field-' . $field_name] . ' 00:00:00');

                $crons = _get_cron_array();
                foreach ($crons as $timestamp => $cron) {
                    foreach ($cron[$cron_name] as $job_id => $job) {
                        if ($job['args'][2] == $booking_id) {

                            if ($timestamp != $new_cron_date->getTimestamp()) {
                                unset($crons[$timestamp][$cron_name][$job_id]);
                                $crons[$new_cron_date->getTimestamp()][$cron_name][$job_id] = $job;

                                // Clean cron
                                foreach ($crons as $timestamp => $cron) {
                                    if (isset($cron[$cron_name]) && empty($cron[$cron_name])) {
                                        unset($crons[$timestamp][$cron_name]);
                                    }
                                    if (empty($crons[$timestamp])) {
                                        unset($crons[$timestamp]);

                                    }
                                }

                                $response[$field_name] = wpbs_date_i18n(get_option('date_format'), $new_cron_date->getTimestamp());

                                _set_cron_array($crons);
                                break;
                            }
                        }
                    }
                }
            }
        }

        echo json_encode($response);

    }

    if ($_POST['type'] == 'payment_details') {

        $response = [];

        $currency = $data['currency'];

        $payment = wpbs_get_payment_by_booking_id($booking_id);
        $payment_details = $payment->get('details');
        $prices = $payment->get('prices');

        foreach ($data['wpbs-edit-booking-pricing-field'] as $field_key => $field_values) {

            foreach ($field_values as $field_id => $field_value) {

                $field_value = floatval($field_value);
                $field_value = round($field_value, 2);

                if (empty($field_value)) {
                    $field_value = 0;
                }

                $quantity = $prices['quantity'];

                switch ($field_key) {
                    case 'event':
                        $prices['events']['price'] = $field_value;
                        break;
                    case 'extra':
                        if (isset($prices['extras'][$field_id])) {
                            $prices['extras'][$field_id]['total'] = $field_value;
                            if ($prices['extras'][$field_id]['addition'] == 'per_booking') {
                                $prices['extras'][$field_id]['price'] = $field_value;
                            } else {
                                $prices['extras'][$field_id]['price'] = round($field_value / $quantity, 2);
                            }
                        }
                        break;
                    case 'coupon':
                        if (isset($prices['coupon'])) {
                            $prices['coupon']['value'] = $field_value;
                        }
                        break;
                    case 'discount':
                        if (isset($prices['discount'][$field_id])) {
                            $prices['discount'][$field_id]['value'] = $field_value;
                        }
                        break;
                    case 'tax':
                        if (isset($prices['taxes'][$field_id])) {
                            $prices['taxes'][$field_id]['value'] = $field_value;
                        }
                        break;
                    case 'total':
                        $prices['total'] = $field_value;
                        break;
                    case 'first_payment':
                        if (isset($prices['part_payments']['first_payment'])) {
                            $prices['part_payments']['first_payment'] = $field_value;
                        }
                        break;
                    case 'second_payment':
                        if (isset($prices['part_payments']['second_payment'])) {
                            $prices['part_payments']['second_payment'] = $field_value;
                        }
                        break;
                }

                $response[$field_key . '-' . $field_id] = wpbs_get_formatted_price($field_value, $currency);

            }
        }

        $payment_details['price'] = $prices;

        wpbs_update_payment($payment->get('id'), array('details' => $payment_details));

        echo json_encode($response);
    }

    wp_die();

}
add_action('wp_ajax_wpbs_edit_booking_details', 'wpbs_action_ajax_wpbs_edit_booking_details');

/**
 * Add Booking Notes
 *
 */
function wpbs_action_ajax_booking_add_note()
{
    // Nonce
    check_ajax_referer('wpbs_booking_notes', 'wpbs_token');

    if (!isset($_POST['booking_id'])) {
        return false;
    }

    $booking_id = absint($_POST['booking_id']);

    $note = sanitize_textarea_field($_POST['note']);

    if (empty($note)) {
        return false;
    }

    $booking_notes = wpbs_get_booking_meta($booking_id, 'booking_notes', true);

    if (empty($booking_notes)) {
        $booking_notes = array();
    }

    $timestamp = current_time('timestamp');

    $booking_notes[] = array(
        'timestamp' => $timestamp,
        'note' => $note,
    );

    wpbs_update_booking_meta($booking_id, 'booking_notes', $booking_notes);

    echo '
    <div class="wpbs-booking-details-modal-note">
        <p>' . nl2br($note) . '</p>
        <div class="wpbs-booking-details-modal-note-footer">
            <span class="wpbs-booking-details-modal-note-date-added">
                <strong>' . __('Added on', 'wp-booking-system') . ':</strong>
                ' . date(get_option('date_format') . ' ' . get_option('time_format'), $timestamp) . '
            </span>
            <a href="#" data-booking-note="' . end(array_keys($booking_notes)) . '" data-booking-id="' . $booking_id . '" class="wpbs-booking-details-modal-note-remove">' . __('delete note', 'wp-booking-system') . '</a>
        </div>
    </div>
    ';

    wp_die();
}
add_action('wp_ajax_wpbs_booking_add_note', 'wpbs_action_ajax_booking_add_note');

/**
 * Delete Booking Notes
 *
 */
function wpbs_action_ajax_booking_delete_note()
{
    // Nonce
    check_ajax_referer('wpbs_booking_notes', 'wpbs_token');

    if (!isset($_POST['booking_id'])) {
        return false;
    }

    $booking_id = absint($_POST['booking_id']);

    $note_id = absint($_POST['note_id']);

    $booking_notes = wpbs_get_booking_meta($booking_id, 'booking_notes', true);

    unset($booking_notes[$note_id]);

    wpbs_update_booking_meta($booking_id, 'booking_notes', $booking_notes);

    wp_die();
}
add_action('wp_ajax_wpbs_booking_delete_note', 'wpbs_action_ajax_booking_delete_note');

/**
 * Fix html entities in line items
 */
function wpbs_format_html_string($string)
{
    // Remove quantity from labels
    $string = preg_replace('/<span class="wpbs-line-item-quantity\b[^>]*>(.*?)<\/span>/i', '', $string);
    $string = strip_tags($string);
    $string = str_replace('&times;', 'x', $string);
    return $string;
}

/**
 * Save "Hide past bookings" option
 *
 */
function wpbs_action_ajax_booking_remember_hide_past_option()
{
    // Nonce
    check_ajax_referer('wpbs_remember_hide_past_option', 'wpbs_token');

    update_option('wpbs_remember_hide_past_bookings_option', ($_POST['remember'] == 'true' ? true : false));

    wp_die();
}
add_action('wp_ajax_wpbs_booking_remember_hide_past_option', 'wpbs_action_ajax_booking_remember_hide_past_option');
