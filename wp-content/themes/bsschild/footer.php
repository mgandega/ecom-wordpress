</div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col">
            <?php
               wp_nav_menu(
                   array(
                    'theme_location' => 'menu_footer',
                    'menu_class' => 'd-flex justify-content-center menu_footer'
                   )
               ) ;         

            ?>
        </div>
    </div>
</div>

<div class="container-fluid bg-dark text-light text-center">
    <div class="row">
        <div class="col py-3">&copy; <?php echo date('Y') ?> - <?php bloginfo() ?> - Tous droits réservés</div>
    </div>
</div>

<?php

wp_footer();