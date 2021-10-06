<?php if (is_active_sidebar('envo-shop-top-bar-area')) { ?>
    <div class="top-bar-section container-fluid">
        <div class="<?php echo esc_attr(get_theme_mod('top_bar_content_width', 'container')); ?>">
            <div class="row">
                <?php dynamic_sidebar('envo-shop-top-bar-area'); ?>
            </div>
        </div>
    </div>
<?php } ?>
<div class="site-header container-fluid">
    <div class="<?php echo esc_attr(get_theme_mod('header_content_width', 'container')); ?>" >
        <div class="heading-row row" >
            <div class="site-heading <?php echo esc_attr(class_exists('WooCommerce') == true ? 'col-md-3' : 'col-md-6' ); ?> hidden-xs" >
                <?php envo_shop_title_logo(); ?>
            </div>
            <div class="search-heading col-md-6 col-xs-12">
                <?php if (class_exists('WooCommerce')) { ?>
                    <div class="header-search-form">
                        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                            <input type="hidden" name="post_type" value="product" />
                            <input class="header-search-input" name="s" type="text" placeholder="<?php esc_attr_e('Search products...', 'envo-shop'); ?>"/>
                            <select class="header-search-select" name="product_cat">
                                <option value=""><?php esc_html_e('All Categories', 'envo-shop'); ?></option> 
                                <?php
                                $categories = get_categories('taxonomy=product_cat');
                                foreach ($categories as $category) {
                                    $option = '<option value="' . esc_attr($category->category_nicename) . '">';
                                    $option .= esc_html($category->cat_name);
                                    $option .= ' (' . absint($category->category_count) . ')';
                                    $option .= '</option>';
                                    echo $option; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                }
                                ?>
                            </select>
                            <button class="header-search-button" type="submit"><i class="la la-search" aria-hidden="true"></i></button>
                        </form>
                    </div>
                <?php } ?>
                <?php if (is_active_sidebar('envo-shop-header-area')) { ?>
                    <div class="site-heading-sidebar" >
                        <?php dynamic_sidebar('envo-shop-header-area'); ?>
                    </div>
                <?php } ?>
            </div>
            <?php if (class_exists('WooCommerce')) { ?>
                <div class="header-right col-md-3 hidden-xs" >
                    <?php envo_shop_header_cart(); ?>
                    <?php envo_shop_my_account(); ?>
                    <?php envo_shop_head_wishlist(); ?>
                    <?php envo_shop_head_compare(); ?>
                </div>	
            <?php } ?>
        </div>
    </div>
</div>
<?php do_action('envo_shop_before_menu'); ?> 
<div class="main-menu">
    <nav id="site-navigation" class="navbar navbar-default">     
        <div class="container">   
            <div class="navbar-header">
                <div class="site-heading mobile-heading visible-xs" >
                    <?php envo_shop_title_logo('p'); ?>
                </div>
                <?php if (function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled('main_menu')) : ?>
                <?php else : ?>
                    <span class="navbar-brand brand-absolute visible-xs"><?php esc_html_e('Menu', 'envo-shop'); ?></span>
                    <?php if (function_exists('envo_shop_header_cart') && class_exists('WooCommerce')) { ?>
                        <div class="mobile-cart visible-xs" >
                            <?php envo_shop_header_cart(); ?>
                        </div>	
                    <?php } ?>
                    <?php if (function_exists('envo_shop_my_account') && class_exists('WooCommerce')) { ?>
                        <div class="mobile-account visible-xs" >
                            <?php envo_shop_my_account(); ?>
                        </div>
                    <?php } ?>
                    <?php if (function_exists('envo_shop_head_wishlist') && class_exists('WooCommerce')) { ?>
                        <div class="mobile-wishlist visible-xs" >
                            <?php envo_shop_head_wishlist(); ?>
                        </div>
                    <?php } ?>
                    <?php if (function_exists('envo_shop_head_compare') && class_exists('WooCommerce')) { ?>
                        <div class="mobile-compare visible-xs" >
                            <?php envo_shop_head_compare(); ?>
                        </div>
                    <?php } ?>
                    <a href="#" id="main-menu-panel" class="open-panel" data-panel="main-menu-panel">
                        <span></span>
                        <span></span>
                        <span></span>
                    </a>
                <?php endif; ?>
            </div>
            <?php
            envo_shop_categories_menu();
            
            wp_nav_menu(array(
                'theme_location' => 'main_menu',
                'depth' => 5,
                'container_id' => 'my-menu',
                'container' => 'div',
                'container_class' => 'menu-container',
                'menu_class' => 'nav navbar-nav navbar-left',
                'fallback_cb' => 'Envo_Shop_WP_Bootstrap_Navwalker::fallback',
                'walker' => new Envo_Shop_WP_Bootstrap_Navwalker(),
            ));
            wp_nav_menu(array(
                'theme_location' => 'main_menu_right',
                'depth' => 1,
                'container_id' => 'my-menu-right',
                'container' => 'div',
                'container_class' => 'menu-container',
                'menu_class' => 'nav navbar-nav navbar-right',
                'fallback_cb' => 'Envo_Shop_WP_Bootstrap_Navwalker::fallback',
                'walker' => new Envo_Shop_WP_Bootstrap_Navwalker(),
            ));
            ?>
        </div>
        <?php do_action('envo_shop_menu'); ?>
    </nav> 
</div>
<?php do_action('envo_shop_after_menu'); ?>
