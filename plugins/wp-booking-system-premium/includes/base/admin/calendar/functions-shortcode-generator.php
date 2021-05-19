<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds the "Add Calendar" button to the text editor
 *
 */
function wpbs_add_calendar_shortcode_media_button() {

	if( ! function_exists( 'get_current_screen' ) )
		return;

	$screen = get_current_screen();

	if( is_null( $screen ) )
		return;

	/**
	 * Filter the post types where the shortcode media button should appear
	 *
	 * @param array
	 *
	 */

	$post_types = get_post_types(array('public' => true));
	
	$post_types = apply_filters( 'wpbs_add_calendar_shortcode_media_button_post_types', $post_types );

	if( ! in_array( $screen->post_type, $post_types ) )
	    return;

	echo '<a href="#" id="wpbs-shortcode-generator-button" class="button"><span class="dashicons dashicons-calendar-alt"></span>' . __( 'Add Calendar', 'wp-booking-system' ) . '</a>';

}
add_action( 'media_buttons', 'wpbs_add_calendar_shortcode_media_button', 20 );


/**
 * Adds the modal window triggered by the "Add Calendar" button
 *
 */
function wpbs_add_calendar_shortcode_modal() {

	if( ! function_exists( 'get_current_screen' ) )
		return;

	$screen = get_current_screen();

	if( is_null( $screen ) )
		return;

	/**
	 * Shortcode Generator Tabs
	 * 
	 */
	$tabs = array(
		'insert-calendar' 	=> __( 'Insert Calendar', 'wp-booking-system' ),
		'insert-overview-calendar' => __( 'Insert Overview Calendar', 'wp-booking-system' )
	);

	// Filter the tabs
	$tabs = apply_filters( 'wpbs_shortcode_generator_tabs', $tabs );

	/**
	 * Filter the post types where the shortcode modal generated by the shortcode media button should appear
	 *
	 * @param array
	 *
	 */

	$post_types = get_post_types(array('public' => true));

	$post_types = apply_filters( 'wpbs_add_calendar_shortcode_media_button_post_types', $post_types );

	if( ! in_array( $screen->post_type, $post_types ) )
	    return;
	
	include 'views/view-shortcode-generator.php';

}
add_action( 'admin_footer', 'wpbs_add_calendar_shortcode_modal' );