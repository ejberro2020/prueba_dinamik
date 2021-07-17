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

function dcms_detalle_ped_woo( $atts , $content ){
       
    if(isset($_GET['pedido'])){
        $ID = $_GET['pedido'];
    }
    else{
        $ID = '';
    }

    $orden = wc_get_order( $ID );
    $order_data = $orden->get_data();
                    $status = $order_data['status'];
                    $total = $order_data['total'];
                    $billing_first_name = $order_data['billing']['first_name'];
    //var_dump($order_data);            
    foreach ($orden->get_items() as $item_key => $item ){
        var_dump($item);                
        $item_name    = $item->get_name(); // Name of the product
        
    }
    ob_start();
    ?>
    <div class="container">
        <div class="row">
            <div class="col-6">
                
                    <div class="mb-3">
                        <label for="tituloInmueble1" class="form-label"><?php echo $item_name; ?></label>
                        <input type="text" name="tituloInmueble" class="form-control" placeholder="Agregar Nombre del Inmueble" id="tituloInmueble1">
                        
                    </div>
                    <div class="mb-3">
                        <label for="tituloInmueble1" class="form-label"><?php echo $billing_first_name; ?></label>
                        <br>
                        <label for="tituloInmueble1" class="form-label"><?php echo $total; ?></label>
                        
                    </div>

                    
            
                </form>
            </div>
        </div>
    </div>
    <?php
     return ob_get_clean();
   
        
    
}