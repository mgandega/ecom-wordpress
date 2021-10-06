<?php

// ce fichier sera utilisé pour la page dont le role est d'être la page d'accueil

get_header();

if( have_posts()):
    
    while(have_posts()): the_post();

        get_template_part('partials/home','carrousel'); // inclure le fichier partials/home-carrousel php
        get_template_part('partials/home','3txt');
        get_template_part('partials/home','parallaxe');
        get_template_part('partials/home','texte');
        get_template_part('partials/home','vignettes');
    endwhile;


endif;


get_footer();