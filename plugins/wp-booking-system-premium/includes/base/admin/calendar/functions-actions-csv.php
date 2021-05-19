<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Validates and handles the resetting of the Calendar's ical_hash
 *
 */
function wpbs_action_csv_export()
{

    // Verify for nonce
    if (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_csv_export')) {
        return;
    }

    // Verify the calendar id
    if (empty($_POST['calendar_id'])) {
        wpbs_admin_notices()->register_notice('csv_export_calendar_id_missing', '<p>' . __('Invalid Calendar ID.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('csv_export_calendar_id_missing');
        return;
    }

    // Get the calendar
    $calendar_id = absint($_POST['calendar_id']);

    // Verify for legend items
    if (empty($_POST['csv-export-legend-items'])) {
        wpbs_admin_notices()->register_notice('csv_export_legends_missing', '<p>' . __('Please select at least one legend item to export.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('csv_export_legends_missing');
        return;
    }

    $selected_legend_items = array();

    // Get legent item names
    foreach ($_POST['csv-export-legend-items'] as $export_legend_item) {
        $legend_item = wpbs_get_legend_item($export_legend_item);
        $selected_legend_items[$export_legend_item] = $legend_item->get('name');
    }

    // Get events
    $events = wpbs_get_events(array('calendar_id' => $calendar_id, 'orderby' => 'date_year, date_month, date_day', 'order' => 'ASC'));

    // Include iCalendar events as well?
    if(isset($_POST['csv-icalendar-events']) && $_POST['csv-icalendar-events'] == 'yes'){
        $events = array_merge($events, wpbs_get_ical_feeds_as_events( $calendar_id, $events ));
    }

    //CSV Header
    if ($_POST['csv-export-format'] == 'groupped_date') {
        $csv_header = array('Date' => '-', 'Legend' => '-', 'Description' => '-');
    } else {
        $csv_header = array('Year' => '-', 'Month' => '-', 'Day' => '-', 'Legend' => '-', 'Description' => '-');
    }

    // Set Start Date
    if(isset($_POST['wpbs-export-csv-start-date']) && !empty($_POST['wpbs-export-csv-start-date'])){
        $start_date = DateTime::createFromFormat('Y-m-d', $_POST['wpbs-export-csv-start-date']);
    }

    // Set End Date
    if(isset($_POST['wpbs-export-csv-end-date']) && !empty($_POST['wpbs-export-csv-end-date'])){
        $end_date = DateTime::createFromFormat('Y-m-d', $_POST['wpbs-export-csv-end-date']);
    }

    $csv_lines = array();

    // Add the CSV header
    foreach ($csv_header as $header_key => $header_value) {
        $csv_lines[0][$header_key] = $header_key;
    }

    $i = 1;

    // Loop through events
    foreach ($events as $event) {

        // Check if legend item is correct
        if (!array_key_exists($event->get('legend_item_id'), $selected_legend_items)) {
            continue;
        }

        $event_date = new DateTime();
        $event_date->setDate($event->get('date_year'), $event->get('date_month'), $event->get('date_day'));

        // Check start date
        if(isset($start_date) && $event_date < $start_date){
            continue;
        }

        // Check end date 
        if(isset($end_date) && $event_date > $end_date){
            continue;
        }

        $csv_lines[$i] = $csv_header;

        // Add event to CSV
        if ($_POST['csv-export-format'] == 'groupped_date') {
            $csv_lines[$i] = array(
                'Date' => $event_date->format(get_option('date_format')),
                'Legend' => $selected_legend_items[$event->get('legend_item_id')],
                'Description' => $event->get('description'),
            );
        } else {
            $csv_lines[$i] = array(
                'Year' => $event_date->format('Y'),
                'Month' => $event_date->format('n'),
                'Day' => $event_date->format('j'),
                'Legend' => $selected_legend_items[$event->get('legend_item_id')],
                'Description' => $event->get('description'),
            );
        }

        $i++;

    }

    if($i === 1){
        wpbs_admin_notices()->register_notice('csv_export_file_empty', '<p>' . __('No events matched your criteria.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('csv_export_file_empty');
        return;
    }

    // Output headers so that the file is downloaded rather than displayed
    header('Content-type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="wpbs-dates-export-calendar-' . $calendar_id . '-' . time() . '.csv"');

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
add_action('wpbs_action_csv_export', 'wpbs_action_csv_export');
