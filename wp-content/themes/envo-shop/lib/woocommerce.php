<?php
if (!function_exists('envo_shop_cart_link')) {

    function envo_shop_cart_link() {
        ?>	
        <a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" data-tooltip="<?php esc_attr_e('Cart', 'envo-shop'); ?>" title="<?php esc_attr_e('Cart', 'envo-shop'); ?>">
            <i class="la la-shopping-bag"><span class="count"><?php echo wp_kses_data(WC()->cart->get_cart_contents_count()); ?></span></i>
            <div class="amount-cart hidden-xs"><?php echo wp_kses_data(WC()->cart->get_cart_subtotal()); ?></div> 
        </a>
        <?php
    }

}

if (!function_exists('envo_shop_header_cart')) {

    function envo_shop_header_cart() {
        if (get_theme_mod('woo_header_cart', 1) == 1) {
            ?>
            <div class="header-cart">
                <div class="header-cart-block">
                    <div class="header-cart-inner">
                        <?php envo_shop_cart_link(); ?>
                        <ul class="site-header-cart menu list-unstyled text-center">
                            <li>
                                <?php the_widget('WC_Widget_Cart', 'title='); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
        }
    }

}

if (!function_exists('envo_shop_header_add_to_cart_fragment')) {
    add_filter('woocommerce_add_to_cart_fragments', 'envo_shop_header_add_to_cart_fragment');

    function envo_shop_header_add_to_cart_fragment($fragments) {
        ob_start();

        envo_shop_cart_link();

        $fragments['a.cart-contents'] = ob_get_clean();

        return $fragments;
    }

}

if (!function_exists('envo_shop_my_account')) {

    function envo_shop_my_account() {
        if (get_theme_mod('woo_account', 1) == 1) {
            ?>
            <div class="header-my-account">
                <div class="header-login"> 
                    <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" data-tooltip="<?php esc_attr_e('My Account', 'envo-shop'); ?>" title="<?php esc_attr_e('My Account', 'envo-shop'); ?>">
                        <i class="la la-user"></i>
                    </a>
                </div>
            </div>
            <?php
        }
    }

}
add_action('woocommerce_before_add_to_cart_quantity', 'envo_shop_display_quantity_minus');

function envo_shop_display_quantity_minus() {
    global $product;
    if ( ($product->get_stock_quantity() > 1 && !$product->managing_stock() ) || !$product->is_sold_individually( ) ) {
        echo '<button type="button" class="minus" >-</button>';
    }
}

add_action('woocommerce_after_add_to_cart_quantity', 'envo_shop_display_quantity_plus');

function envo_shop_display_quantity_plus() {
    global $product;
    if ( ($product->get_stock_quantity() > 1 && !$product->managing_stock() ) || !$product->is_sold_individually( ) ) {
        echo '<button type="button" class="plus" >+</button>';
    }
}

if (!function_exists('envo_shop_head_wishlist')) {

    function envo_shop_head_wishlist() {
        if (function_exists('YITH_WCWL')) {
            $wishlist_url = YITH_WCWL()->get_wishlist_url();
            ?>
            <div class="header-wishlist">
                <a href="<?php echo esc_url($wishlist_url); ?>" data-tooltip="<?php esc_attr_e('Wishlist', 'envo-shop'); ?>" title="<?php esc_attr_e('Wishlist', 'envo-shop'); ?>">
                    <i class="lar la-heart"></i>
                </a>
            </div>
            <?php
        }
    }

}

if (!function_exists('envo_shop_head_compare')) {

    function envo_shop_head_compare() {
        if (function_exists('yith_woocompare_constructor')) {
            global $yith_woocompare;
            ?>
            <div class="header-compare product">
                <a class="compare added" rel="nofollow" href="<?php echo esc_url($yith_woocompare->obj->view_table_url()); ?>" data-tooltip="<?php esc_attr_e('Compare', 'envo-shop'); ?>" title="<?php esc_attr_e('Compare', 'envo-shop'); ?>">
                    <i class="la la-sync"></i>
                </a>
            </div>
            <?php
        }
    }

}

remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title');
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_title', 7);

if (!function_exists('envo_shop_compare_wishlist_buttons')) {

    function envo_shop_compare_wishlist_buttons() {
        $double = '';
        if (function_exists('yith_woocompare_constructor') && function_exists('YITH_WCWL')) {
            $double = ' d-compare-wishlist';
        }
        ?>
        <div class="product-compare-wishlist<?php echo esc_attr($double); ?>">
            <?php
            if (function_exists('yith_woocompare_constructor')) {
                global $product, $yith_woocompare;
                $product_id = !is_null($product) ? yit_get_prop($product, 'id', true) : 0;
                // return if product doesn't exist
                if (empty($product_id) || apply_filters('yith_woocompare_remove_compare_link_by_cat', false, $product_id))
                    return;
                $url = is_admin() ? "#" : $yith_woocompare->obj->add_product_url($product_id);
                ?>
                <div class="product-compare">
                    <a class="compare" rel="nofollow" data-product_id="<?php echo absint($product_id); ?>" href="<?php echo esc_url($url); ?>" title="<?php esc_attr_e('Compare', 'envo-shop'); ?>">
                        <i class="la la-sync"></i>
                        <?php esc_html_e('Compare', 'envo-shop'); ?>
                    </a>
                </div>
                <?php
            }
            if (function_exists('YITH_WCWL')) {
                ?>
                <div class="product-wishlist">
                    <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]') ?>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    add_action('woocommerce_after_shop_loop_item', 'envo_shop_compare_wishlist_buttons', 15);
}

