
<?php
/**
 * Plugin Name: EB PRUEBAS
 */


 $args = array(
     // Argumentos para tu consulta.
 );
  
 // Consulta personalizada.
 $query = new WP_Query( $args );
  
 // Verificar si obtuvimos resultados.
 if ( $query->have_posts() ) {
  
     // Obtener los datos mediante un bucle.
     while ( $query->have_posts() ) {
  
         $query->the_post();
  
         // Trabajar con los resultados de la consulta.
  
     }
  
 }
  
 // Restaurar datos originales de la entrada.
 wp_reset_postdata();
  
 ?>