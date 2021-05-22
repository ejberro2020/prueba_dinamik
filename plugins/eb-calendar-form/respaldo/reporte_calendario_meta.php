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
    $calendarios =  $wpdb->get_results("SELECT c.id, c.name, c.date_created, c.date_modified, c.status, c.ical_hash, m.meta_value FROM $tabla_calendars c JOIN $tabla_calendars_meta m ON c.id = m.calendar_id WHERE m.meta_key = 'default_price' ");
    ob_start();
    ?>


        <table class="table table-primary table-info table-hover">
        <thead class="table-primary">
            <tr>
            <th scope="col">#</th>
            <th scope="col">Nombre</th>
            <th scope="col">Precio</th>    
            <th scope="col">F Creacion</th>
            <th scope="col">F Modificacion</th>
            <th scope="col">status</th>
            <th scope="col">ical_hash</th>
            </tr>
        </thead>
        <tbody> 
            <?php
                foreach ($calendarios as $calendario){
                    $id = (int)$calendario->id;
                    $name = esc_textarea($calendario->name);
                    $default_price = (int)$calendario->meta_value;
                    $date_created = $calendario->date_created;
                    $date_modified = $calendario->date_modified;
                    $status = esc_textarea($calendario->status);
                    $ical_hash = esc_textarea($calendario->ical_hash);
                    echo "<tr class='table-light'>";
                    echo "<td>$id</td>
                          <td>$name</td>
                          <td>$default_price</td>   
                          <td>$date_created</td>     
                          <td>$date_modified</td>
                          <td>$status</td>
                          <td>$ical_hash</td>
                          </tr>";
                }
            ?>
            </tbody>
            </table>
            
      
    <?php
    return ob_get_clean();


}