<?php

function agregar_css_js(){
    wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_style( 'bootstrapYeti', get_template_directory_uri() . '/css/bootstrap.min.css', array());
    wp_enqueue_script( 'popper', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array ( 'jquery' ));
}
add_action( 'wp_enqueue_scripts', 'agregar_css_js' );