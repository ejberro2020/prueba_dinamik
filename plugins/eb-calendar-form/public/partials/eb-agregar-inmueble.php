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

function dcms_agregar_inmueble( $atts , $content ){

    ob_start();
    ?>
    <div class="container">
        <div class="row">
            <div class="col-6">
                <form name="insertInmueble" id="insertInmueble" action="<?php echo esc_url( admin_url('admin-post.php')) ?>"  method="post">
                    <div class="mb-3">
                        <label for="tituloInmueble1" class="form-label">Titulo del Inmueble</label>
                        <input type="text" name="tituloInmueble" class="form-control" placeholder="Agregar Nombre del Inmueble" id="tituloInmueble1">
                        
                    </div>
                    <div class="mb-3">
                        <label for="descripcionInmueble1" class="form-label">Descripción del Inmueble</label>
                        <textarea name="descripcionInmueble" class="form-control" id="descripcionInmueble1" placeholder="Agregar Descripción del Inmueble" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="defaultprice1" class="form-label">Precio Default del Inmueble</label>
                        <input type="number" name="defaultprice" class="form-control" id="defaultprice1" placeholder="Agregar Costo Diario del Inmueble">
                    </div>
                
                    <button type="submit" class="btn btn-primary">Insertar Inmueble</button>

                    <input type="hidden" name="action" value="inmuebleform">
            
                </form>
            </div>
        </div>
    </div>
    <?php
     return ob_get_clean();
   
        
    
}