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
    $t_bookings = $wpdb->prefix . 'wpbs_bookings'; 
    $t_calendars = $wpdb->prefix . 'wpbs_calendars';
    $t_payments = $wpdb->prefix . 'wpbs_payments';
    $tabla_calendars_meta = $wpdb->prefix . 'wpbs_calendar_meta';    
    $calendarios =  $wpdb->get_results("SELECT b.id, b.start_date, b.end_date, b.date_created, b.status, c.name, o.order_id   FROM $t_calendars c INNER JOIN $t_bookings b ON b.calendar_id = c.id INNER JOIN  $t_payments o ON b.id = o.booking_id");

    
    $arrayPHP = "Eres el amo";
    ob_start();
    ?>



        <p>Date: <input type="text" id="datepicker"></p>
        <br>
        <table class="table table-primary table-info table-hover">
        <thead class="table-primary">
            <tr>
            <th scope="col">#</th>
            <th scope="col">Cabaña</th>
            <th scope="col">F Reserva</th>    
            <th scope="col">F Inicio</th>
            <th scope="col">F Fin</th>
            <th scope="col">status</th>
            <th scope="col"># Orden Pago</th>
            </tr>
        </thead>
        <tbody> 
            <?php
                foreach ($calendarios as $calendario){
                    $id = (int)$calendario->id;
                    $name = esc_textarea($calendario->name);
                    $f_reserva = $calendario->date_created;
                    $f_inicio = $calendario->start_date;
                    $f_fin = $calendario->end_date;
                    $status = esc_textarea($calendario->status);
                    $order_id = (int)$calendario->order_id;
                    echo "<tr class='table-light'>";
                    echo "<td>$id</td>
                          <td>$name</td>
                          <td>$f_reserva</td>   
                          <td>$f_inicio</td>     
                          <td>$f_fin</td>
                          <td>$status</td>
                          <td>$order_id</td>
                          </tr>";
                }
            ?>
            </tbody>
            </table>
            <?php $array_php = array('29-5-2021','30-5-2021','31-5-2021'); ?>
            <script>
            
                var array_js = ;
                
            
                
            </script>
      
    <?php
    return ob_get_clean();

}