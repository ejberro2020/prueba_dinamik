<?php

function captura_valores_agregar_inmueble(){
    $inmueble = array(
        'post_title' => wp_strip_all_tags( $_POST['tituloInmueble'] ),
        'post_content' => $_POST['descripcionInmueble'],
        'post_type' =>  'product',
        'post_name' =>  sanitize_title($_POST['tituloInmueble']),
        'post_status' => 'pending',
        'post_author' => $user_id,
        'post_excerpt' => ''   

    );

    global $wpdb;
    $tabla_post = $wpdb->prefix . 'posts';
    $tabla_calendars = $wpdb->prefix . 'wpbs_calendars';
    $tabla_legend_items = $wpdb->prefix . 'wpbs_legend_items';
    $tabla_calendar_meta = $wpdb->prefix . 'wpbs_calendar_meta';
    $id_user_activo = get_current_user_id();
    
   

    // AÑADIR FOTOS (SEPARAR FOTOS DE CADA CLIENTE)
    $nuevo_inmueble = wp_insert_post( $inmueble );

    // CREAR EL CALENDARIO
    function wpbs_generate_hash1()
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chars_length = strlen($chars);
        $hash = '';
    
        for ($i = 0; $i < 19; $i++) {
                $hash .= $chars[rand(0, $chars_length - 1)];
        }
    
        return $hash . uniqid();
    
    }    

    // Prepare calendar data to be inserted
    $calendar_data = array(
        'name' 		    => sanitize_text_field( $_POST['tituloInmueble'] ),
        'date_created'  => current_time( 'Y-m-d H:i:s' ),
        'date_modified' => current_time( 'Y-m-d H:i:s' ),
        'status'		=> 'active',
        'ical_hash'		=> wpbs_generate_hash1()
    );

    // Insert calendar into the database
              
    $wpdb->insert( $tabla_calendars, $calendar_data );

    // Insertar Leyenda del Calendario

    $buscar_el =  wp_strip_all_tags( $_POST['tituloInmueble'] );

    $datos_post =  $wpdb->get_results("SELECT id AS max_id FROM $tabla_calendars WHERE name = '$buscar_el'");

    $id2 = $datos_post[0]->max_id;

    $legend_data1 = array(
        'type'  => 'single',
        'name'  => 'Available',
        'color' => '["#ddffcc"]',
        //'color_text'		=> '',
        'is_default'		=> '1',
        'is_visible'		=> '1',
        'is_bookable'		=> '1',
        'auto_pending'		=> '',
        'calendar_id'		=> $id2
    );
    $legend_data2 = array(
        'type'  => 'single',
        'name'  => 'Booked',
        'color' => '["#ffc0bd"]',
        //'color_text'		=> '',
        'is_default'		=> '0',
        'is_visible'		=> '1',
        'is_bookable'		=> '0',
        'auto_pending'		=> 'booked',
        'calendar_id'		=> $id2
    );

    $legend_data3 = array(
        'type'  => 'split',
        'name'  => 'Changeover 1',
        'color' => 	'["#ddffcc","#ffc0bd"]',
        //'color_text'		=> '',
        'is_default'		=> '0',
        'is_visible'		=> '0',
        'is_bookable'		=> '1',
        'auto_pending'		=> 'changeover_start',
        'calendar_id'		=> $id2
    );
    $legend_data4 = array(
        'type'  => 'split',
        'name'  => 'Changeover 2',
        'color' => 	'["#ffc0bd","#ddffcc"]',
        //'color_text'		=> '',
        'is_default'		=> '0',
        'is_visible'		=> '0',
        'is_bookable'		=> '1',
        'auto_pending'		=> 'changeover_end',
        'calendar_id'		=> $id2
    );

    // Insert calendar into the database
              
    $wpdb->insert( $tabla_legend_items, $legend_data1 );
    $wpdb->insert( $tabla_legend_items, $legend_data2 );
    $wpdb->insert( $tabla_legend_items, $legend_data3 );
    $wpdb->insert( $tabla_legend_items, $legend_data4 );

    // Insertar los meta del Calendario
    $calendar_meta_data1 = array(
        'calendar_id'  => $id2,
        'meta_key'  => 'default_price',
        'meta_value' => $_POST['defaultprice'],

    );
    $calendar_meta_data2 = array(
        'calendar_id'  => $id2,
        'meta_key'  => 'calendar_name_translation_es',
        'meta_value' => '',

    );
    $calendar_meta_data3 = array(
        'calendar_id'  => $id2,
        'meta_key'  => 'calendar_link_type',
        'meta_value' => 'internal',

    );
    $calendar_meta_data4 = array(
        'calendar_id'  => $id2,
        'meta_key'  => 'user_permission',
        'meta_value' => $id_user_activo,

    );
    $wpdb->insert( $tabla_calendar_meta, $calendar_meta_data1 );
    $wpdb->insert( $tabla_calendar_meta, $calendar_meta_data2 );
    $wpdb->insert( $tabla_calendar_meta, $calendar_meta_data3 );
    $wpdb->insert( $tabla_calendar_meta, $calendar_meta_data4 );


    // ENLAZAR CALENDARIO Y EL PRODUCTO
    //Asignar Calendario al Producto Woocommerce

    $buscar_el =  "product";
    
    $datos_post =  $wpdb->get_results("SELECT MAX(ID) AS max_id FROM $tabla_post WHERE post_author = '$id_user_activo' AND post_type = '$buscar_el'");
  
    
    $id3 = $datos_post[0]->max_id;

    $inmueble2 = array(
        'ID' => $id3,
        'post_excerpt' => '[wpbs id="'.$id2.'" title="yes" legend="yes" legend_position="side" display="1" year="0" month="0" language="auto" start="1" dropdown="yes" jump="no" history="1" tooltip="1" highlighttoday="no" weeknumbers="no" form_id="1" auto_pending="yes" selection_type="multiple" selection_style="normal" minimum_days="1" maximum_days="27" booking_start_day="0" booking_end_day="0" show_date_selection="yes"]'   

    );

    wp_update_post($inmueble2, true);
}    

add_action( 'admin_post_inmuebleform', 'captura_valores_agregar_inmueble' );



