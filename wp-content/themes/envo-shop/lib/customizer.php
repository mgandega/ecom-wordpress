<?php
/**
 * Envo Shop Theme Customizer
 *
 * @package Envo Shop
 */

$envo_shop_sections = array( 'info', 'demo' );

foreach( $envo_shop_sections as $section ){
    require get_template_directory() . '/lib/customizer/' . $section . '.php';
}

function envo_shop_customizer_scripts() {
    wp_enqueue_style( 'envo-shop-customize',get_template_directory_uri().'/lib/customizer/css/customize.css', '', 'screen' );
    wp_enqueue_script( 'envo-shop-customize', get_template_directory_uri() . '/lib/customizer/js/customize.js', array( 'jquery' ), '20170404', true );
}
add_action( 'customize_controls_enqueue_scripts', 'envo_shop_customizer_scripts' );
