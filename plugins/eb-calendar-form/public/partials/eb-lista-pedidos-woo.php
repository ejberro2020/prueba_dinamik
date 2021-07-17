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
    
    // Get 10 most recent order ids in date descending order.
    $args = array(
        
    );

    $orders = wc_get_orders( $args );
   // print_r($orders);
    ob_start();
    
    ?>
        
        <table class="table table-primary table-info table-hover">
        <thead class="table-primary">
            <tr>
            <th scope="col" class="text-center">#</th>
            <th scope="col" class="text-center">Nombre</th>
            <th scope="col" class="text-center">Apellido</th>
            <th scope="col" class="text-center">Fecha de Orden</th>
            <th scope="col" class="text-center">Articulo</th>
            <th scope="col" class="text-center">Monto</th>
            <th scope="col" class="text-center">Status</th>
            <th scope="col" class="text-center">Accion</th>
            </tr>
        </thead>
        <tbody> 
            <?php
                
                foreach ($orders as $order ){

                    $ID = $order->get_id();
                    $status = $order->get_status();
                    $billing_first_name = $order->get_billing_first_name();
                    $billing_last_name = $order->get_billing_last_name();
                    $fecha_creacion = $order->get_date_created()->format('d-m-Y');
                    $billing_status = $order->get_status();
                    $slug = "detalle-pedido";   
                    $enlace = esc_url(post_permalink(187) . '?pedido='. $ID) ;
                    $orden = wc_get_order( $ID );
                    foreach ($orden->get_items() as $item_key => $item ){
                        
                        $item_name    = $item->get_name(); // Name of the product
                        $billing_shipping_total = $order->get_total();
                        
                        $billing_shipping_total2 = number_format($billing_shipping_total);
                    }
                    echo "<tr class='table-light'>";
                    echo "<td class='text-center'>$ID</td>
                          <td>$billing_first_name</td>
                          <td>$billing_last_name</td>
                          <td class='text-center'>$fecha_creacion</td>
                          <td class='text-center'>$item_name</td>
                          <td class='text-end'>$billing_shipping_total2</td>    
                          <td class='text-center'>$billing_status</td>
                          <td class='text-center'><a href=$enlace>Home</a></td>   
                          
                          </tr>";
                }
            ?> 
            </tbody>
            </table>
        
        
      
    <?php
    return ob_get_clean();

}