<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *get_template_part( 'eb-vista-shortcode' );
 * @link       www.hexacom.com
 * @since      1.0.0
 *
 * @package    Eb_Calendar_Form
 * @subpackage Eb_Calendar_Form/public/partials
 */


function dcms_mensaje( $atts , $content ){
     
    global $wpdb;
    $tabla_calendars = $wpdb->prefix . 'wpbs_calendars';
    $tabla_calendars_meta = $wpdb->prefix . 'wpbs_calendar_meta';
    $tabla_wpbs_bookings = $wpdb->prefix . 'wpbs_bookings';
    $tabla_wp_wc_order_stats = $wpdb->prefix . 'wp_wc_order_stats';
    $tabla_woocommerce_order_items = $wpdb->prefix . 'woocommerce_order_items'; 
    $tabla_woocommerce_order_itemmeta = $wpdb->prefix . 'woocommerce_order_itemmeta';    

    /*$query =  $wpdb->get_results(" SELECT JSON OBJECT ( 'order_id', o.order_id,  'status', o.status   ) AS dato FROM $tabla_wp_wc_order_stats o WHERE o.order_id = 34 "); */

    $query = $wpdb->prepare( "SELECT fields FROM $tabla_wpbs_bookings WHERE id = 1" );    

    $string = $wpdb->get_var( $query );
    $someArray = json_decode( $string, true );
    print_r($someArray);



    /* CODIGO FUENTE PARA MOSTRAR ARRAY DE DATOS ORIGEN JSON
    <?php 
        $project_id = $_SESSION['project_id']; $query = $wpdb->prepare( "SELECT reward_details FROM wpxa_orocox_rewards WHERE project_id = %d", $project_id );

            $string = $wpdb->get_var( $query );

            $someArray = json_decode( $string, true );

                $count = count( $someArray['reward_title'] );
                for ( $i = 0; $i < $count; $i++ ) { ?>

            <div class="panel panel-default">
            <div class="panel-body">
                <?php echo $someArray["reward_amount"][$i]; ?>
                <?php echo $someArray["reward_title"][$i]; ?>
                <?php echo $someArray["reward_description"][$i]; ?>
            </div>
            </div>

            <?php } ?>
    */


}