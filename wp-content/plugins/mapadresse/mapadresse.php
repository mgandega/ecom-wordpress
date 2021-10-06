<?php

/*
Plugin Name: Map Adresse
Author: Cheikhbi
Version: 1.0
Description: Permet de générer une carte à partir d'une adresse
*/

// Ajouter le css et le js
add_action('wp_enqueue_scripts','mapadresse_css_js');

function mapadresse_css_js(){

    // https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.js
    // https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.css

    wp_enqueue_style('leaflet','https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.css');
    wp_enqueue_style('mapadresse', plugin_dir_url(__FILE__) . 'css/mapadresse.css', array('leaflet'));
    // plugin_dir_url(__FILE__) renvoie le dossier du plugin


    wp_enqueue_script('leaflet','https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.js');
    wp_enqueue_script('mapadresse',plugin_dir_url(__FILE__) . 'js/mapadresse.js', array('leaflet') );

}


// Enregistrement d'un nouveau shortcode

add_action('init','register_mapadresse_shortcode');

function register_mapadresse_shortcode(){

    add_shortcode('mapadresse','generate_map');

}

function generate_map($params = array(), $content = null){

    // $zoom = (isset($params['zoom'])) ? $params['zoom'] : 15;
    $zoom = $params['zoom'] ?? 15; // 15 sera la valeur de zoom par défaut si non précisée
    $adresse = $params['adresse'];

    if(!empty($adresse)){       
        $html = '<div class="mapadresse" data-adresse="'.$adresse.'" data-zoom="'.$zoom.'"></div>';  
        return $html;      
    }

}