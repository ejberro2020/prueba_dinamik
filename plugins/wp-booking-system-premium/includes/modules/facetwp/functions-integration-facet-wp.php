<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add WP Booking System as a Facet source
 *
 * @param array $sources
 *
 * @return array
 *
 */
function wpbs_facetwp_facet_sources($sources)
{
    $sources['wpbs'] = array(
        'label' => __('WP Booking System', 'wp-booking-system'),
        'choices' => [
            'wpbs_date' => __('Calendar Dates', 'wp-booking-system'),
        ],
        'weight' => 10,
    );

    return $sources;
}
add_filter('facetwp_facet_sources', 'wpbs_facetwp_facet_sources', 10, 1);

/**
 * Filter results
 *
 * @param bool|array $return
 * @param array $params
 *
 * @return bool|array
 *
 */

function wpbs_facetwp_facet_filter_posts($return, $params)
{

    // Check if Facet type is "Date Range" and source is "WP Booking System", and both fields are present
    if ($params['facet']['type'] != 'date_range' || $params['facet']['source'] != 'wpbs_date' || $params['facet']['fields'] != 'both') {
        return $return;
    }

    // Check if both start and end date are selected
    if (empty($params['selected_values'][0]) || empty($params['selected_values'][1])) {
        return 'continue';
    }

    // Search through calendars
    $date = DateTime::createFromFormat('Y-m-d', $params['selected_values'][0]);
    $start_date = $date->format('Ymd');

    // Format end date
    $date = DateTime::createFromFormat('Y-m-d', $params['selected_values'][1]);
    $end_date = $date->format('Ymd');

    $matches = [];

    // Get all calendars
    $calendars = wpbs_get_calendars(array('status' => 'active', 'orderby' => 'name', 'order' => 'asc'));

    foreach ($calendars as $calendar) {

        // Check if calendar has a page attached to it
        $calendar_link = wpbs_get_calendar_meta($calendar->get('id'), 'calendar_link_internal', true);

        if (empty($calendar_link)) {
            continue;
        }

        // Assume calendar dates are available
        $is_available = true;

        // Get non bookable legend items
        $non_bookable_legend_items = array();
        $legend_items = wpbs_get_legend_items(array('calendar_id' => $calendar->get('id')));

        $changeover_start = $changeover_end = false;

        foreach ($legend_items as $legend_item) {
            if ($legend_item->get('is_bookable') == 0) {
                $non_bookable_legend_items[] = $legend_item->get('id');
            }

            if ($legend_item->get('auto_pending') == 'changeover_start') {
                $changeover_start = $legend_item->get('id');
            }

            if ($legend_item->get('auto_pending') == 'changeover_end') {
                $changeover_end = $legend_item->get('id');
            }

        }

        $changeover_start_found = $changeover_end_found = false;

        // Loop through events
        $calendar_events = wpbs_get_events(array('calendar_id' => $calendar->get('id'))); // Calendar Events
        $ical_events = wpbs_get_ical_feeds_as_events($calendar->get('id'), array()); // iCalendar Events

        $events = array_merge($calendar_events, $ical_events);

        $sorted_events = array();
        foreach ($events as $event) {
            $sorted_events[$event->get('date_year') . str_pad($event->get('date_month'), 2, '0', STR_PAD_LEFT) . str_pad($event->get('date_day'), 2, '0', STR_PAD_LEFT)] = $event->get('legend_item_id');
        }

        ksort($sorted_events);

        foreach ($sorted_events as $event_date => $event_legent_item_id) {

            // If event date is outside search range, continue;
            if ($event_date < $start_date || $event_date > $end_date) {
                continue;
            }

            // Check if the event found is not bookable
            if (in_array($event_legent_item_id, $non_bookable_legend_items)) {
                $is_available = false;
                break;
            }

            // Check for changeovers. The rule is that if a start changeover exists in an array, we shouln't an end changeover

            // We found a starting changeover date
            if ($event_legent_item_id == $changeover_start) {
                $changeover_start_found = true;
            }

            // Now if we find an ending changeover date and a starting changeover date was previously found, we mark the date as not available.
            if ($event_legent_item_id == $changeover_end && $changeover_start_found === true) {
                $is_available = false;
                break;
            }

        }

        // If date is available, add it to $matches
        if ($is_available === true) {
            $matches[] = $calendar_link;
        }
    }

    if (!empty($matches)) {
        return $matches;
    }

    return $return;

}
add_filter('facetwp_facet_filter_posts', 'wpbs_facetwp_facet_filter_posts', 10, 2);

//Add current date to permalink

function wpbs_facetwp_query_string($permalink, $post, $leavename)
{
    // Check if a Date Facet is selected
    if (!isset($_GET['fwp_date']) && !isset($_POST['data']['http_params']['get']['fwp_date'])) {
        return $permalink;
    }
    
    $fwp_date = explode(',', (isset($_GET['fwp_date'])) ? $_GET['fwp_date'] : urldecode($_POST['data']['http_params']['get']['fwp_date']));
    
    $fwp_date = array_filter($fwp_date);

    //Check if start and end date are both present
    if (count($fwp_date) != 2) {
        return $permalink;
    }

    // Get start and end dates
    list($start_date, $end_date) = $fwp_date;

    $date = DateTime::createFromFormat("Y-m-d", $start_date);

    // Add them to the permalink
    $permalink = add_query_arg(array(
        'wpbs-start-month' => $date->format('n'),
        'wpbs-start-year' => $date->format('Y'),
        'wpbs-selection-start' => $start_date,
        'wpbs-selection-end' => $end_date,
    ), $permalink);

    return $permalink;

}
add_filter('post_link', 'wpbs_facetwp_query_string', 10, 3);
