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


function dcms_lista_ped_woo( $atts , $content ){
    
    
    /*$calendarios =  $wpdb->get_results("SELECT b.id, b.post_date, b.post_status  FROM $t_calendars c INNER JOIN $t_bookings b ON b.calendar_id = c.id INNER JOIN  $t_payments o ON b.id = o.booking_id"); WHERE key1.post_type = 'shop_order' */
    global $wpdb;
    $t_bookings = $wpdb->prefix . 'wpbs_bookings'; 
    $t_calendars = $wpdb->prefix . 'wpbs_calendars';
    $t_payments = $wpdb->prefix . 'wpbs_payments';
    $tabla_calendars_meta = $wpdb->prefix . 'wpbs_calendar_meta';  
    $t_pedidos = $wpdb->prefix . 'posts';  
    $t_order_items = $wpdb->prefix . 'woocommerce_order_items';
    $t_order_itemmeta = $wpdb->prefix . 'woocommerce_order_itemmeta';
    /*$results =  $wpdb->get_results("SELECT b.ID, b.post_title, d.meta_value as calendar_name, d1.meta_value as booking_start_date, d2.meta_value as booking_end_date, d3.meta_value as line_total $t_pedidos b
                                    INNER JOIN $t_order_items c ON b.ID = c.order_id
                                    LEFT JOIN $t_order_itemmeta d ON (c.order_item_id = d.order_item_id AND d.meta_key = '_calendar_name') 
                                    LEFT JOIN $t_order_itemmeta d1 ON (c.order_item_id = d1.order_item_id AND d1.meta_key = '_booking_start_date')
                                    LEFT JOIN $t_order_itemmeta d2 ON (c.order_item_id = d2.order_item_id AND d2.meta_key = '_booking_end_date')
                                    WHERE b.post_type = 'shop_order'
    "); */

    // Get 10 most recent order ids in date descending order.
    $args = array(
        
    );
    $orders = wc_get_orders( $args );
    $args1 = array(
        
    );
    

    ob_start();
    ?>
        <?php 
        echo 'Jalabola'; 
        echo '<pre>';
        //print_r($orden);
        echo '</pre><hr/>';
        
        foreach ($orders as $order ){

            $ID = $order->get_id();
            $status = $order->get_status();
            $billing_first_name = $order->get_billing_first_name();
            $orden = wc_get_order( $ID );
            echo '<p>' . $ID . '</p>';
            echo '<p>' . $status . '</p>';
            echo '<p>' . $billing_first_name . '</p>';
          //  echo '<p>' . $release->post_title . '</p>';
          //  echo '<p>' . $release->calendar_name . '</p>';
          //  echo '<p>' . $release->booking_start_date . '</p>';
          //  echo '<p>' . $release->booking_end_date . '</p>';
           // echo '<p>' . $release->line_total . '</p>';
           foreach ($orden->get_items() as $item_key => $item ){
            $item_id = $item->get_id();
            $item_name    = $item->get_name(); // Name of the product
            echo '<p>' . $item_id . '</p>';
            echo '<p>' . $item_name . '</p>';
             }
            echo '<hr>';
        
        }
        /*
        if ($orders->have_posts()){
            $orders->the_post();
            echo '<h1>' . get_total() . '</h1>'
        } 
        foreach ($orders->get_items() as $item_key => $item ){
            $item_id = $item->get_id();
            $item_name    = $item->get_name(); // Name of the product
            echo '<p>' . $item_id . '</p>';
            echo '<p>' . $item_name . '</p>';
        }
        */
        ?> 
        
      
    <?php
    return ob_get_clean();

}