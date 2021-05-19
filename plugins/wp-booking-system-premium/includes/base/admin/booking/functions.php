<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the Booking admin area
 *
 */
function wpbs_include_files_admin_booking()
{

    // Get legend admin dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include the bookings outputter
    if (file_exists($dir_path . 'class-bookings-outputter.php')) {
        include $dir_path . 'class-bookings-outputter.php';
    }

    // Include the booking detail outputter
    if (file_exists($dir_path . 'class-booking-detail-outputter.php')) {
        include $dir_path . 'class-booking-detail-outputter.php';
    }

    // Include booking mailer
    if (file_exists($dir_path . 'class-booking-mailer.php')) {
        include $dir_path . 'class-booking-mailer.php';
    }

    // Include the ajax functions
    if (file_exists($dir_path . 'functions-ajax.php')) {
        include $dir_path . 'functions-ajax.php';
    }

    // Include the saving functions
    if (file_exists($dir_path . 'functions-actions-booking.php')) {
        include $dir_path . 'functions-actions-booking.php';
    }

}
add_action('wpbs_include_files', 'wpbs_include_files_admin_booking');

/**
 * Add Bookings Count to Admin Menu
 *
 */
function wpbs_add_menu_booking_count()
{
    global $menu;

    $count = 0;

    // Change filter to get count only from available calendars
    remove_filter('wpbs_get_calendars', 'wpbs_get_calendars_user_capabilities');
    add_filter('wpbs_get_calendars', 'wpbs_get_calendars_user_capabilities_global', 10, 3);

    $calendars = wpbs_get_calendars();

    // Reset filters
    add_filter('wpbs_get_calendars', 'wpbs_get_calendars_user_capabilities', 10, 3);
    remove_filter('wpbs_get_calendars', 'wpbs_get_calendars_user_capabilities_global');

    foreach ($calendars as $calendar) {
        $bookings = wpbs_get_bookings(array('calendar_id' => $calendar->get('id'), 'is_read' => 0));
        $count += count($bookings);
    }

    if ($count == 0) {
        return;
    }

    foreach ($menu as $menu_key => $item) {
        if ($item[2] == 'wp-booking-system') {
            break;
        }

    }

    $menu[$menu_key][0] .= " <span class='update-plugins count-" . $count . "'><span class='plugin-count'>" . $count . '</span></span>';
}
add_filter('admin_menu', 'wpbs_add_menu_booking_count', 100);

/**
 * Add Bookings Count to Admin Bar
 *
 */
function wpbs_add_admin_bar_booking_count($wp_admin_bar)
{
    global $wp_admin_bar;

    $count = 0;

    // Change filter to get count only from available calendars
    remove_filter('wpbs_get_calendars', 'wpbs_get_calendars_user_capabilities');
    add_filter('wpbs_get_calendars', 'wpbs_get_calendars_user_capabilities_global', 10, 3);

    $calendars = wpbs_get_calendars();

    // Reset filters
    add_filter('wpbs_get_calendars', 'wpbs_get_calendars_user_capabilities', 10, 3);
    remove_filter('wpbs_get_calendars', 'wpbs_get_calendars_user_capabilities_global');

    if (count($calendars) == 0) {
        return false;
    }

    foreach ($calendars as $i => $calendar) {
        $bookings = wpbs_get_bookings(array('calendar_id' => $calendar->get('id'), 'is_read' => 0));
        $count += count($bookings);
        $calendars[$i]->bookings = count($bookings);
    }

    $args = array(
        'id' => 'wp-booking-system-admin',
        'href' => admin_url('admin.php?page=wpbs-calendars'),
        'parent' => 'root-default',
    );

    if ($count == 0) {
        $label = __('No New Bookings', 'wp-booking-system');
        $count_label = '';
    } elseif ($count == 1) {
        $label = __('New Booking', 'wp-booking-system');
        $count_label = '<span class="wpbs-admin-bar-bookings-count">' . $count . '</span>';
    } else {
        $label = __('New Bookings', 'wp-booking-system');
        $count_label = '<span class="wpbs-admin-bar-bookings-count">' . $count . '</span>';
    }

    $args['meta']['title'] = $label;

    $args['title'] = '<span class="ab-icon"></span><span class="wpbs-admin-bar-bookings-count-wrap count-' . $count . '">' . $count_label . ' ' . $label . '</span>';

    $wp_admin_bar->add_menu($args);

    foreach ($calendars as $calendar) {
        
        $title = $calendar->get_name();

        if($calendar->bookings > 0){
            $title .= ' <span class="wpbs-admin-bar-bookings-count">' . $calendar->bookings . '</span>';
        }
        
        $wp_admin_bar->add_node(array(
            'id' => 'wp-booking-system-admin-calendar-' . $calendar->get('id'),
            'href' => admin_url('admin.php?page=wpbs-calendars&subpage=edit-calendar&calendar_id=' . $calendar->get('id')),
            'parent' => 'wp-booking-system-admin',
            'title' => $title
        ));
    }
}
add_action('wp_before_admin_bar_render', 'wpbs_add_admin_bar_booking_count', 1);

/**
 * Add Admin Bar style on Front End
 * 
 */
function wpbs_add_admin_bar_booking_count_style()
{

    if (!is_admin_bar_showing()) {
        return false;
    }

    echo '<style type="text/css">
         #wp-admin-bar-wp-booking-system-admin a .wpbs-admin-bar-bookings-count {display: inline-block; vertical-align: top; box-sizing: border-box !important; margin: 1px 3px -1px 2px !important; padding: 0 5px !important; min-width: 18px; height: 18px !important; border-radius: 9px !important; background-color: #ca4a1f; color: #fff; top: 6px; font-size: 11px !important; line-height: 1.6 !important; text-align: center; z-index: 26; position: relative !important;}
         #wp-admin-bar-wp-booking-system-admin a .ab-icon:before {content: "\f508"; top: 3px;}
         #wp-admin-bar-wp-booking-system-admin ul li a span {top:3px !important;}
         @media screen and (max-width: 782px){
            #wpadminbar ul#wp-admin-bar-root-default #wp-admin-bar-wp-booking-system-admin {display: block !important;}
            #wpadminbar ul#wp-admin-bar-root-default #wp-admin-bar-wp-booking-system-admin .wpbs-admin-bar-bookings-count-wrap {font-size: 0; line-height: 0px;}
            #wpadminbar ul#wp-admin-bar-root-default #wp-admin-bar-wp-booking-system-admin a {display: inline-block; padding-right:5px;}
            #wpadminbar ul#wp-admin-bar-root-default #wp-admin-bar-wp-booking-system-admin .wpbs-admin-bar-bookings-count {height: 16px !important; min-height: 16px !important; min-width: 16px !important; font-size: 9px !important; line-height: 15px !important; margin: 0 0 0 -15px !important;}
        }
      </style>';

}

add_action('wp_head', 'wpbs_add_admin_bar_booking_count_style');

/**
 * Show the email body from the Email Logs section.
 * 
 */
function wpbs_action_email_logs(){

    if(!isset($_GET['booking_id'])){
        return false;
    }

    if(!isset($_GET['email_log_id'])){
        return false;
    }

    $logs = wpbs_get_booking_meta($_GET['booking_id'], 'email_log');

    if(!isset($logs[$_GET['email_log_id']])){
        return false;
    }

    echo $logs[$_GET['email_log_id']]['message'];

    exit;
}

add_action( 'wpbs_action_email_logs', 'wpbs_action_email_logs', 50 );