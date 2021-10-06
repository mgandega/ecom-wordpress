<?php

//              nom du hook       nom de ma fonction
add_action('wp_enqueue_scripts', 'ajouter_css_js');

function ajouter_css_js()
{

    // css    
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', array('wp-bootstrap-starter-bootstrap-css'));

    // get_stylesheet_directory_uri() renvoie le répertoire du thème actif


    // js
    // wp_enqueue_script('id','chemin',array(ids à charger avant séparés par des virgules));
}

// Ajouter un nouvel emplacement de menu
register_nav_menus(
    array(
        'menu_footer' => 'Emplacement des liens administratifs'
    )
);

// Ajouter une nouvelle zone à widgets
add_action('widgets_init', 'ajout_sidebar');

function ajout_sidebar()
{

    register_sidebar(
        array(
            'name' => 'Sidebar de la page contact',
            'id' => 'sidebar_contact',
            'description' => "Déposer ici les widgets qui s'afficheront sur la page contact",
            'before_widget' => '<div>',
            'after_widget' => '</div>'
        )
    );
}

// Filtre pour modifier une valeur d'entrée
add_filter('get_the_archive_title', 'modifier_titre');

function modifier_titre($titre)
{

    if (is_post_type_archive()) {
        $titre = post_type_archive_title('', false);
    }

    return $titre;
}

add_shortcode('recents', 'liste_recente');

// [recents nb="2" type="voyage"]
// [recents nb="2" type="concert"]

function liste_recente($params = array(), $content = null)
{

    $nb = $params['nb'] ?? 5;
    $types = explode(',',$params['type']);

    query_posts(
        array(
            'orderby' => 'date',
            'order' => 'DESC',
            'showposts' => $nb,
            'post_type' => $types
        )
    );

    if (have_posts()) {

        $html = '<ul>';

        while (have_posts()) {
            the_post();
            $html .= '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
        }
        $html .= "</ul>";
    }
   
    wp_reset_query(); // détruit l'ancien pointeur de BDD 
    return $html;
}