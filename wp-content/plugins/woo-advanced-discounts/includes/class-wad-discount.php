<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-wad-discount
 *
 * @author HL
 */
class WAD_Discount {

    public $id;
    public $settings;
    public $products_list;
    public $products;
    public $title;
    public $rules_verified;
    public $is_applicable;
    public $evaluable_per_product;

    public function __construct($discount_id) {
        if ($discount_id) {
            $this->id = $discount_id;
            $this->settings = get_post_meta($discount_id, "o-discount", true);
            $this->title = get_the_title($discount_id);
            $this->rules_verified=false;
            $this->is_applicable=false;
            $this->evaluable_per_product=false;
            if(empty($this->settings["rules"]))
            {
                $this->rules_verified=true;
                $this->is_applicable=true;
            }


            if (!$this->settings)
                return;

            $list_id = false;
            $products_actions = wad_get_product_based_actions();
            if (in_array($this->settings["action"], $products_actions))
                $list_id = $this->settings["products-list"];

            if ($list_id) {
                $this->products_list = new WAD_Products_List($list_id);
            }
            $this->wad_update_products_list();
        }
    }

    /**
     * Register the discount custom post type
     */
    public function register_cpt_discount() {

        $labels = array(
            'name' => __('Discount', 'woo-advanced-discounts'),
            'singular_name' => __('Discount', 'woo-advanced-discounts'),
            'add_new' => __('New discount', 'woo-advanced-discounts'),
            'add_new_item' => __('New discount', 'woo-advanced-discounts'),
            'edit_item' => __('Edit discount', 'woo-advanced-discounts'),
            'new_item' => __('New discount', 'woo-advanced-discounts'),
            'view_item' => __('View discount', 'woo-advanced-discounts'),
            //        'search_items' => __('Search a group', 'woo-advanced-discounts'),
            'not_found' => __('No discount found', 'woo-advanced-discounts'),
            'not_found_in_trash' => __('No discount in the trash', 'woo-advanced-discounts'),
            'menu_name' => __('Discounts', 'woo-advanced-discounts'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'description' => 'Discounts',
            'supports' => array('title'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => false,
            'can_export' => true,
            'menu_icon' => WAD_URL . 'admin/images/WAD-logo.svg',
        );

        register_post_type('o-discount', $args);
    }

    /**
     * Adds the metabox for the discount CPT
     */
    public function get_discount_metabox() {

        $screens = array('o-discount');

        foreach ($screens as $screen) {

            add_meta_box(
                    'o-discount-settings-box', __('Discount settings', 'woo-advanced-discounts'), array($this, 'get_discount_settings_page'), $screen
            );
        }
    }

    /**
     * Discount CPT metabox callback
     */
    public function get_discount_settings_page() {
        $raw_wp_language = get_bloginfo("language");
        $formatted_wp_language = substr($raw_wp_language, 0, strpos($raw_wp_language, "-"));
        $url = admin_url( 'post-new.php?post_type=o-list');
        $html = "<a href='".$url."'>here</a>";
        $date_formatted = get_option('date_format');
        $time_formatted = get_option('time_format');
        $formatted_date = $date_formatted . ' ' . $time_formatted;
        ?>
        <script type="text/javascript">
            var lang_wordpress = '<?PHP echo $formatted_wp_language; ?>';
            var formatted_date = '<?PHP echo $formatted_date; ?>';
            jQuery(document).ready(function () {
                if (jQuery('body').hasClass('post-type-o-discount')){
                if (jQuery('tr.product-action-row select#products-list').val() === null ){
                    jQuery("<p><?php _e("You haven't created a products list. You need one in order to apply a discount on multiple products. You can create one $html","woo-advanced-discounts"); ?></p>").css('color','red').insertAfter( 'tr.product-action-row select#products-list');
                }
        }
            });

        </script>
        <div class='block-form'>
            <?php
            $timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format' );
            $begin = array(
                'type' => 'sectionbegin',
                'id' => 'wad-datasource-container',
            );
            $start_date = array(
                'title' => __('Start date', 'woo-advanced-discounts'),
                'name' => 'o-discount[start-date]',
                'type' => 'text',
                'class' => 'o-date',
//                'custom_attributes' => array("required" => ""),
                'desc' => __('Date from which the discount is applied. Not mandatory. </br></br>Local time: ', 'woo-advanced-discounts').'<strong>'.date_i18n( $timezone_format ).'</strong>',
                'default' => '',
            );

            $end_date = array(
                'title' => __('End date', 'woo-advanced-discounts'),
                'name' => 'o-discount[end-date]',
                'type' => 'text',
                'class' => 'o-date',
//                'custom_attributes' => array("required" => ""),
                'desc' => __('Date when the discount ends.  Not mandatory. </br></br>Local time: ', 'woo-advanced-discounts').'<strong>'.date_i18n( $timezone_format ).'</strong>',
                'default' => '',
            );

            $discount_actions=$this->get_discounts_actions();
            $actions_custom_attributes=array();
            $free_version_actions=array("percentage-off-pprice", "fixed-amount-off-pprice","percentage-off-osubtotal","fixed-amount-off-osubtotal");
            foreach ($discount_actions as $action_key=>$action)
            {
                if(!in_array($action_key, $free_version_actions))
                    $actions_custom_attributes[$action_key]=array("disabled" => "disabled");
            }

            $action_label = array(
                'title' => __('Action', 'woo-advanced-discounts'),
                'name' => 'o-discount[action]',
                'type' => 'select',
                'class' => 'discount-action',
                'desc' => __('Type of discount to apply.', 'woo-advanced-discounts'),
                'default' => '',
                'options' => $this->get_discounts_actions(),
                'options_custom_attributes' => $actions_custom_attributes,
            );

            $product_lists = new WAD_Products_List(false);

            $products_action = array(
                'title' => __('Products list', 'woo-advanced-discounts'),
                'id' => 'products-list',
                'name' => 'o-discount[products-list]',
                'type' => 'select',
                'row_class' => 'product-action-row',
                'desc' => __('List of products the selected action applies on', 'woo-advanced-discounts'),
                'default' => '',
                'options' => $product_lists->get_all(),
                'required' => true
            );

            $group_by_product = array(
                'title' => __('Evaluate per product', 'woo-advanced-discounts'),
                'name' => 'o-discount[calculate-per-product]',
                'type' => 'select',
                'row_class' => 'product-action-row',
                'desc' => __('Run the calculations of each product in the list independantly.', 'woo-advanced-discounts') . "<br><strong style='color: red;'>Beta.</strong>",
                'default' => 'no',
                'options' => array("yes" => "Yes", "no" => "No"),
            );

            $disable_on_product_pages = array(
                'title' => __('Disable on products and shop pages', 'woo-advanced-discounts'),
                'id' => 'products-list',
                'name' => 'o-discount[disable-on-product-pages]',
                'type' => 'radio',
                'row_class' => 'product-action-row',
                'desc' => __('Disables the display of discounted prices on all pages except cart and checkout', 'woo-advanced-discounts'),
                'default' => 'no',
                'options' => array(
                    "yes" => "Yes",
                    "no" => "No",
                )
            );

            $percentage_or_fixed_amount = array(
                'title' => __('Percentage / Fixed amount', 'woo-advanced-discounts'),
                'name' => 'o-discount[percentage-or-fixed-amount]',
                'type' => 'number',
                'id' => 'percentage-amount',
                'custom_attributes' => array("step" => "any"),
                'row_class' => 'percentage-row',
                'desc' => __('Percentage or fixed amount to apply.', 'woo-advanced-discounts'),
                'default' => '',
                'required' => true
            );

            $relationship = array(
                'title' => __('Rules groups relationship', 'woo-advanced-discounts'),
                'name' => 'o-discount[relationship]',
                'type' => 'radio',
                'desc' => __('AND: All groups rules must be verified to have the discount action applied.', 'woo-advanced-discounts') . "<br" . __('OR: AT least one group rules must be verified to have the discount action applied.', 'woo-advanced-discounts'),
                'default' => 'AND',
                'options' => array(
                    "AND" => "AND",
                    "OR" => "OR",
                )
            );

            $rules = array(
                'title' => __('Rules', 'woo-advanced-discounts'),
                'desc' => __('Allows you to define which rules should be checked in order to apply the discount. Not mandatory.', 'woo-advanced-discounts'),
                'name' => 'o-discount[rules]',
                'type' => 'custom',
                'callback' => array($this, "get_discount_rules_callback"),
            );

            $end = array('type' => 'sectionend');
            $settings = array(
                $begin,
                $start_date,
                $end_date,
                $relationship,
                $rules,
                $action_label,
                $percentage_or_fixed_amount,
                //$group_by_product,
                $products_action,
                $disable_on_product_pages,
                $end
            );
            echo o_admin_fields($settings);
            ?>
        </div>

        <?php
        global $o_row_templates;
        ?>
        <script>
            var o_rows_tpl =<?php echo json_encode($o_row_templates); ?>;
        </script>
        <?php
        return;
    }

    private function get_rule_tpl($conditions, $default_condition = false, $default_operator = "", $default_value = "") {
        ob_start();
        $operators = $this->get_operator_fields_match($default_condition, $default_operator);
        $value_field = $this->get_value_fields_match($default_condition, $default_value);
        ?>
        <tr data-id="rule_{rule-group}">
            <td class="param">
                <select class="select wad-pricing-group-param" name="o-discount[rules][{rule-group}][{rule-index}][condition]" data-group="{rule-group}" data-rule="{rule-index}">
                    <?php
                    foreach ($conditions as $condition_key => $condition_val) {
                        if ($condition_key == $default_condition) {
                            ?><option value='<?php echo $condition_key; ?>' selected="selected"><?php echo $condition_val; ?></option><?php
                        } else {
                            ?><option value='<?php echo $condition_key; ?>'><?php echo $condition_val; ?></option><?php
                        }
                    }
                    ?>
                    <option value="email-domain-name" disabled><?php _e("If Customer email domain name (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="previous-order-count" disabled><?php _e("If Previous orders count (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="total-spent-on-shop" disabled><?php _e("If Total spent in shop (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="previously-ordered-products-count" disabled><?php _e("If Previously ordered products count (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="previously-ordered-products-in-list" disabled><?php _e("If Previously ordered products (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="order-subtotal-inc-taxes" disabled><?php _e("If Order subtotal (inc. taxes) (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="different-order-item-count" disabled><?php _e("If Different Order items count (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="order-products" disabled><?php _e("If Order products (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="customer-reviewed-product" disabled><?php _e("If Customer reviewed any product (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="payment-gateway" disabled><?php _e("If Payment gateway (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="billing-country" disabled><?php _e("If Customer billing country (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="billing-state" disabled><?php _e("If Billing state (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="shipping-country" disabled><?php _e("If Shipping country (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="shipping-state" disabled><?php _e("If Shipping state (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="shipping-method" disabled><?php _e("If Shipping method (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="customer-subscribed-mailchimp" disabled><?php _e("If Customer subscribed to Mailchimp list (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="customer-subscribed-sendinblue" disabled><?php _e("If Customer subscribed to a Sendinblue list (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="customer-subscribed-newsletter-plugin" disabled><?php _e("If Customer subscribed to a NewsletterPlugin list (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="customer-following-affiliation-link" disabled><?php _e("If Customer is following an affiliation link (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="customer-group" disabled><?php _e("If Customer belongs to specified groups (Pro feature)", "woo-advanced-discounts"); ?></option>
                    <option value="shop-currency" disabled><?php _e("If shop currency (Pro feature)", "woo-advanced-discounts"); ?></option>
                </select>
            </td>
            <td class="operator">
                <?php echo $operators; ?>
            </td>
            <td class="value">
                <?php echo $value_field; ?>
            </td>
            <td class="add">
                <a class="wad-add-rule button" data-group='{rule-group}'><?php echo __("and", "woo-advanced-discounts"); ?></a>
            </td>
            <td class="remove">
                <a class="wad-remove-rule acf-button-remove"></a>
            </td>
        </tr>
        <?php
        $rule_tpl = ob_get_contents();
        ob_end_clean();
        return $rule_tpl;
    }

    private function get_value_fields_match($condition = false, $selected_value = "") {
        $selected_value_arr = array();
        $selected_value_str = "";
        if (is_array($selected_value))
            $selected_value_arr = $selected_value;
        else
            $selected_value_str = $selected_value;

        $field_name = "o-discount[rules][{rule-group}][{rule-index}][value]";
        $roles = wad_get_existing_user_roles();
        $roles_select = get_wad_html_select($field_name . "[]", false, "", $roles, $selected_value_arr, true, true);
        $users = wad_get_existing_users();
        $users_select = get_wad_html_select($field_name . "[]", false, "", $users, $selected_value_arr, true, true);

        $text = '<input type="number" name="' . $field_name . '" value="' . $selected_value_str . '" required>';

        $values_match = apply_filters("wad_fields_values_match", array(
            "customer-role" => $roles_select,
            "customer" => $users_select,
            "order-subtotal" => $text,
            "order-item-count" => $text,
                ), $condition, $selected_value);

        if (isset($values_match[$condition]))
            return $values_match[$condition];
        else
            return $values_match;
    }

    private function get_operator_fields_match($condition = false, $selected_value = "") {
        $field_name = "o-discount[rules][{rule-group}][{rule-index}][operator]";
        $arrays_operators = array(
            "IN" => __("IN", "woo-advanced-discounts"),
            "NOT IN" => __("NOT IN", "woo-advanced-discounts"),
        );
        $arrays_operators_select = get_wad_html_select($field_name, false, "", $arrays_operators, $selected_value);

        $number_operators = array(
            "<" => __("is less than", "woo-advanced-discounts"),
            "<=" => __("is less or equal to", "woo-advanced-discounts"),
            "==" => __("equals", "woo-advanced-discounts"),
            ">" => __("is more than", "woo-advanced-discounts"),
            ">=" => __("is more or equal to", "woo-advanced-discounts"),
            "%" => __("is a multiple of", "woo-advanced-discounts"),
        );
        $number_operators_select = get_wad_html_select($field_name, false, "", $number_operators, $selected_value);
        $operators_match = apply_filters("wad_operators_fields_match", array(
            "customer-role" => $arrays_operators_select,
            "customer" => $arrays_operators_select,
            "order-subtotal" => $number_operators_select,
            "order-item-count" => $number_operators_select,
                ), $condition, $selected_value);

        if (isset($operators_match[$condition]))
            return $operators_match[$condition];
        else
            return $operators_match;
    }

    function get_discount_rules_callback() {

        $conditions = $this->get_discounts_conditions();
        $first_rule = $this->get_rule_tpl($conditions, "customer-role");
//        $rule_tpl = $this->get_rule_tpl($conditions);
        $values_match = $this->get_value_fields_match(-1);
        $operators_match = $this->get_operator_fields_match(-1);
        ?>
        <script>
            var wad_values_matches =<?php echo json_encode($values_match); ?>;
            var wad_operators_matches =<?php echo json_encode($operators_match); ?>;
        </script>
        <div class='wad-rules-table-container'>
            <textarea id='wad-rule-tpl' style='display: none;'>
                <?php echo $first_rule; ?>
            </textarea>
            <textarea id='wad-first-rule-tpl' style='display: none;'>
                <?php echo $first_rule; ?>
            </textarea>
            <?php
            $discount_id = get_the_ID();
            $metas = get_post_meta($discount_id, 'o-discount', true);
            $all_rules = array();
            if (isset($metas['rules']))
                $all_rules = $metas['rules'];

            if (is_array($all_rules) && !empty($all_rules)) {
                $rule_group = 0;
                foreach ($all_rules as $rules) {
                    $rule_index = 0;
                    ?>
                    <table class="wad-rules-table widefat wad-rules-table">
                        <tbody>
                            <?php
                            foreach ($rules as $rule_arr) {
                                $rule_arr["condition"] = get_proper_value($rule_arr, "condition");
                                $rule_arr["operator"] = get_proper_value($rule_arr, "operator");
                                $rule_arr["value"] = get_proper_value($rule_arr, "value");
                                $rule_html = $this->get_rule_tpl($conditions, $rule_arr["condition"], $rule_arr["operator"], $rule_arr["value"]);
                                $r1 = str_replace("{rule-group}", $rule_group, $rule_html);
                                $r2 = str_replace("{rule-index}", $rule_index, $r1);
                                echo $r2;
                                $rule_index++;
                            }
                            $rule_group++;
                            ?>
                        </tbody>
                    </table>
                    <?php
                }
            }
            ?>

        </div>
        <a class="button wad-add-group mg-top"><?php _e("Add rules group", "woo-advanced-discounts"); ?></a>
        <?php
    }

    /**
     * Saves the discount data
     * @param type $post_id
     */
    public function save_discount($post_id) {
        $meta_key = "o-discount";
        if (isset($_POST[$meta_key])) {
            update_post_meta($post_id, $meta_key, $_POST[$meta_key]);
        }
    }

    function get_cart_items($item_id = false) {
        global $woocommerce;
        return $woocommerce->cart->get_cart();
    }

    /**
     * Returns the total to widthdraw on cart or taxes
     * @global Array $wad_discounts
     * @global Object $woocommerce
     * @param Bool $on_taxes Whether to return the total on taxes or on the cart
     * @return Float
     */
    private function get_total_cart_discount($on_taxes = false) {
        global $wad_discounts;
        global $wad_settings;
        global $woocommerce;
        //Real price are not get using global so we get subtotal directly within this function which is call on the hook woocommerce_cart_calculate_fees
        $cart_total = wad_get_cart_total();
        $display_individual_discounts = get_proper_value($wad_settings, "individual-cart-discounts", 1);

        $discounts = $wad_discounts;
        $to_widthdraw = 0;
        $to_widthdraw_on_taxes = 0;

        $taxable = wc_tax_enabled();
        $prices_inclusing_taxes = get_option('woocommerce_prices_include_tax') == 'yes' ? true : false;
        if ($taxable && $prices_inclusing_taxes)
            $taxable = false;

        foreach ($discounts["order"] as $discount_id => $discount) {
            $taxable=false;
            if ($discount->is_applicable()) {

                $percentage = $discount->settings["percentage-or-fixed-amount"] * 100 / $cart_total;
                $to_widthdraw_on_taxes+=$percentage;

                $discount_ttc = $discount->get_discount_amount($cart_total);
                if ($display_individual_discounts)
                    $woocommerce->cart->add_fee($discount->title, (-1 * $discount_ttc), $taxable, '');
                $to_widthdraw+=$discount_ttc;

                //We save the discount in the session to use it later when completing the payment
                if (!in_array($discount_id, $_SESSION["active_discounts"]) && wad_is_checkout())
                    array_push($_SESSION["active_discounts"], $discount_id);
            }
        }

        if ($on_taxes)
            return $to_widthdraw_on_taxes / 100;
        else
            return $to_widthdraw;
    }

    /**
     * Returns the product price in the cart
     * @global type $woocommerce
     * @param type $product_id
     * @return type
     */
    function get_cart_item_price($product_id) {

        $product = wc_get_product($product_id);
        if (WC()->cart->get_tax_price_display_mode() == 'excl') {
            $price = wc_get_price_excluding_tax($product);
        } else {
            $price = wc_get_price_including_tax($product);
        }

        return $price;
    }

    function woocommerce_custom_surcharge() {
        global $woocommerce;
        global $wad_settings;
        global $wad_cart_discounts;

        $display_individual_discounts = get_proper_value($wad_settings, "individual-cart-discounts", 1);

        if (!defined('WAD_INITIALIZED') || (is_admin() && !is_ajax()))
            return;

        $discount_ttc = $this->get_total_cart_discount() * -1;
        $discount_ht = $discount_ttc / (1 + $this->get_total_cart_discount(true));
        $taxable = wc_tax_enabled();

        if ($discount_ht) {
            if (!$display_individual_discounts)
                $woocommerce->cart->add_fee(__('Reductions on cart', 'woo-advanced-discounts'), $discount_ht, $taxable, '');
            $wad_cart_discounts = $discount_ttc;
        }
    }

    function get_discount_amount($amount) {
        $to_widthdraw = 0;
        if (in_array($this->settings["action"], array("percentage-off-pprice", "percentage-off-osubtotal")))
            $to_widthdraw = floatval ($amount) * floatval ($this->settings["percentage-or-fixed-amount"]) / 100;
        //Fixed discount
        else if (in_array($this->settings["action"], array("fixed-amount-off-pprice", "fixed-amount-off-osubtotal"))) {
            $to_widthdraw = $this->settings["percentage-or-fixed-amount"];
        } else if ($this->settings["action"] == "fixed-pprice")
            $to_widthdraw = floatval($amount) - floatval($this->settings["percentage-or-fixed-amount"]);
        //We save the discount in the session to use it later when completing the payment
        $decimals = wc_get_price_decimals();
	    return wc_round_discount( $to_widthdraw, $decimals );
    }

    private function get_product_subtotal($_product, $quantity, $cart) {

        $price = $_product->get_price();
        $taxable = $_product->is_taxable();

        // Taxable
        if ($taxable) {

            if ($cart->get_tax_price_display_mode() == 'excl') {
                $row_price = wc_get_price_excluding_tax( $_product, array( 'qty' => $quantity ) );
                $product_subtotal = wc_price($row_price);

                if ($cart->prices_include_tax && $cart->tax_total > 0) {
                    $product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            } else {
                $row_price = wc_get_price_including_tax( $_product, array( 'qty' => $quantity ) );
                $product_subtotal = wc_price($row_price);

                if (!$cart->prices_include_tax && $cart->tax_total > 0) {
                    $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                }
            }

            // Non-taxable
        } else {

            $row_price = $price * $quantity;
            $product_subtotal = wc_price($row_price);
        }

        return $product_subtotal;
    }

    function is_rule_valid($rule, $product_id = false) {
        $is_valid = false;
        $condition = $this->get_evaluable_condition($rule, $product_id);
        $value = get_proper_value($rule, "value");

        //We check if the condition is IN or NOT IN the value
//        $array_operators=array("IN", "NOT IN");
        if ( $rule["condition"] == "customer") {
//        if(in_array($rule["operator"], $array_operators))
            if (!is_array($value)) {
                $error_msg = __("Discount", "woo-advanced-discounts") . " #$this->id: " . __("Rule ", "woo-advanced-discounts") . $rule["condition"] . __(" requires at least one parameter selected in the values", "woo-advanced-discounts");
                echo $error_msg . "<br>";
                $is_valid = false;
            } else {
                $is_valid = in_array($condition, $value);
                if ($rule["operator"] == "NOT IN") {
                    $is_valid = (!$is_valid);
                }
            }

        } else if($rule["condition"] == "customer-role") {
            if(is_array($condition))
                $is_valid = array_intersect($condition, $value);
            else
                $is_valid = in_array($condition, $value);
            if ($rule["operator"] == "NOT IN")
                $is_valid = (!$is_valid);
            //Checks if the a products is in a list
        } else {
            $operator = isset($rule["operator"])?$rule["operator"]:"";
            if ($operator == "%"){//Modulo evaluation
                $expression_to_eval = function($condition,$operator,$value){if($condition % $value==0) return true; else return false;};
                $is_valid = $expression_to_eval($condition,$operator,$value);
            }
            else{
                $expression_to_eval = wad_evaluate_conditions($condition,$operator,$value);
                $is_valid = $expression_to_eval;
            }
        }
        return apply_filters('wad_is_rule_valid', $is_valid, $rule, $this);
    }

    private function get_evaluable_condition($rule, $product_id) {
        $condition = $rule["condition"];
        $evaluable_condition = false;

        switch ($condition) {
            case "customer-role":
                global $wad_user_role;
                $evaluable_condition = $wad_user_role;
                break;
            case "customer":
                if (is_user_logged_in())
                    $evaluable_condition = get_current_user_id();
                break;
            case "order-subtotal":
                global $wad_cart_total_without_taxes;
                $evaluable_condition = $wad_cart_total_without_taxes;
                break;
            case "order-item-count":
                $evaluable_condition = wad_get_cart_products_count();
                break;
            default :
                $evaluable_condition = apply_filters("wad_get_evaluable_condition", false, $rule, $product_id); //false;
                break;
        }

        return $evaluable_condition;
    }

    function is_applicable($product_id = false) {
        $is_valid = true;
        if($this->rules_verified!==true)
        {
            if (!isset($this->settings["rules"]) || !is_array($this->settings["rules"])) {
                $this->settings["rules"] = array();
            }
            foreach ($this->settings["rules"] as $group) {
                foreach ($group as $rule) {
                    $is_valid = $this->is_rule_valid($rule, $product_id);
                    //Group is not valid
                    if (!$is_valid) {
                        break;
                    }
                }
                //If one rule of the group is not valid in a AND case, then the group is not valid
                if ($this->settings["relationship"] == "AND" && !$is_valid) {
                    break;
                }
                //If one group is valid in a OR case, then the discount is valid
                if ($this->settings["relationship"] == "OR" && $is_valid)
                    break;
            }

            if($this->evaluable_per_product==FALSE)
            {
                $this->rules_verified=true;
                $this->is_applicable=$is_valid;
            }

        }
        else
            $is_valid=  $this->is_applicable;
        return apply_filters('wad_is_applicable', $is_valid, $this, $product_id);
    }

    function get_discounts_conditions() {
        return apply_filters('wad_get_discounts_conditions', array(
            "customer-role" => __("If Customer role", "woo-advanced-discounts"),
            "customer" => __("If Customer", "woo-advanced-discounts"),
            "order-subtotal" => __("If Order subtotal", "woo-advanced-discounts"),
            "order-item-count" => __("If Order items count", "woo-advanced-discounts"),
        ));
    }

    function get_discounts_actions() {
        return array(
            "percentage-off-pprice" => __("Percentage off product price", "woo-advanced-discounts"),
            "fixed-amount-off-pprice" => __("Fixed amount off product price", "woo-advanced-discounts"),
            "percentage-off-osubtotal" => __("Percentage off order subtotal", "woo-advanced-discounts"),
            "fixed-amount-off-osubtotal" => __("Fixed amount off order subtotal", "woo-advanced-discounts"),
            "fixed-pprice" => __("Fixed product price (Pro feature)", "woo-advanced-discounts"),
            "percentage-off-cprice" => __("Percentage off cheapest product price in cart (Pro feature)", "woo-advanced-discounts"),
            "fixed-amount-off-cprice" => __("Fixed amount off cheapest product price in cart (Pro feature)", "woo-advanced-discounts"),
            "percentage-off-lsubtotal" => __("Percentage off product with lowest subtotal in cart (Pro feature)", "woo-advanced-discounts"),
            "fixed-amount-off-lsubtotal" => __("Fixed amount off product with lowest subtotal in cart (Pro feature)", "woo-advanced-discounts"),
            "percentage-off-osubtotal-inc-taxes" => __("Percentage off order subtotal (inc. taxes) (Pro feature)", "woo-advanced-discounts"),
            "fixed-amount-off-osubtotal-inc-taxes" => __("Fixed amount off order subtotal (inc. taxes) (Pro feature)", "woo-advanced-discounts"),
            "percentage-off-shipping-fee" => __("Percentage off shipping fees (Pro feature)", "woo-advanced-discounts"),
            "fixed-amount-off-shipping-fee" => __("Fixed amount off shipping fees (Pro feature)", "woo-advanced-discounts"),
            "fixed-shipping-fee" => __("Fixed shipping fees (Pro feature)", "woo-advanced-discounts"),
            "free-gift" => __("Free gift (Pro feature)", "woo-advanced-discounts"),
        );
    }

    public function get_sale_price($sale_price, $product, $include_quantity_based_pricing=true) {
        //We're still in the init_globals() so we don't need to run yet
        if (!defined('WAD_INITIALIZED') )
            return $sale_price;
        global $wad_discounts;

        if (isset($product->aelia_cs_conversion_in_progress) && !empty($product->aelia_cs_conversion_in_progress))
            return $sale_price;

        if (is_admin() && !is_ajax() /* || empty($sale_price) */)
            return $sale_price;

        $pid = wad_get_product_id_to_use($product);

        global $previous_value;
        if (empty($sale_price))
        {
            global $wad_ignore_product_prices_calculations;
            $previous_value=$wad_ignore_product_prices_calculations;
            $wad_ignore_product_prices_calculations=TRUE;
        }

        foreach ($wad_discounts["product"] as $discount_id => $discount_obj) {
            $disable_on_products_pages = get_proper_value($discount_obj->settings, "disable-on-product-pages", "no");
            //Even If the discount is disabled on the shop pages, we force it to be enabled in the minicart even if this minicart is on the shop pages
            if($disable_on_products_pages && did_action('woocommerce_before_mini_cart_contents') && !did_action('woocommerce_after_mini_cart'))
                $disable_on_products_pages=false;
//            if ($disable_on_products_pages == "yes" && (is_singular("product") || is_shop() || is_product_category() || is_front_page()))
            if($disable_on_products_pages == "yes" && (!is_cart() && !is_checkout()))
                continue;
            if ( $discount_obj->is_applicable( $pid ) ) {
                $list_products = $discount_obj->products_list->get_products();
                if( is_array( $list_products ) && in_array( $pid, $list_products ) ) {
                    $sale_price = floatval( $sale_price ) - $discount_obj->get_discount_amount(floatval( $sale_price ) );
                    //We save the discount in the session to use it later when completing the payment
                    if ( ! in_array( $discount_id, $_SESSION["active_discounts"] ) )
                        array_push( $_SESSION["active_discounts"], $discount_id );
                }
            }
        }

        if ( is_bool($previous_value) ){
            $wad_ignore_product_prices_calculations=$previous_value;
        }

        //We ignore the inclusion of the quantity based pricing (QBP) on the product page where the product price is displayed.
        //The price is still ok with the QBP included in the mini cart and cart widget
        if ( $include_quantity_based_pricing && ( is_cart() || is_checkout() || did_action('woocommerce_before_mini_cart_contents') ) )
            $sale_price = $this->apply_quantity_based_discount_if_needed($product, $sale_price);
            // If product's sale price changed, we must update the product too,
            // so that other parties can access it
                $product->sale_price = $sale_price;
        return $sale_price;
    }

    private function apply_quantity_based_discount_if_needed($product, $normal_price) {
        global $wad_cart_total_without_taxes;
        global $wad_cart_total_inc_taxes;
        global $woocommerce;
        //We check if there is a quantity based discount for this product
        $product_type=$product->get_type();
        $id_to_check = $product->get_id();



        if($product_type=="variation")
        {
            $parent_product=$product->get_parent_id();
            $quantity_pricing = get_post_meta($parent_product, "o-discount", true);
        }
        else
        {
            $quantity_pricing = get_post_meta($id_to_check, "o-discount", true);
        }

        if ( empty($quantity_pricing) || !isset($quantity_pricing["enable"]) )
        {
            return $normal_price;
        }

        $products_qties = $this->get_cart_item_quantities();
        $rules_type = get_proper_value($quantity_pricing, "rules-type", "intervals");

        if ( !isset($products_qties[$id_to_check]) )
        {
            return $normal_price;
        }

        $original_normal_price = $normal_price;

        //We do this to avoid warning when doing calculations below
        if($normal_price==="")
            $normal_price=0;

        if (isset($quantity_pricing["rules"]) && $rules_type == "intervals") {
            foreach ($quantity_pricing["rules"] as $rule) {
                //if ($rule["min"] <= $products_qties[$id_to_check] && $products_qties[$id_to_check] <= $rule["max"]) {
                if (
                        ($rule["min"] === "" && $products_qties[$id_to_check] <= $rule["max"]) || ($rule["min"] === "" && $rule["max"] === "") || ($rule["min"] <= $products_qties[$id_to_check] && $rule["max"] === "") || ($rule["min"] <= $products_qties[$id_to_check] && $products_qties[$id_to_check] <= $rule["max"])
                ) {
                    if ($quantity_pricing["type"] == "fixed")
                        $normal_price-=$rule["discount"];
                    else if ($quantity_pricing["type"] == "percentage")
                        $normal_price-=($normal_price * $rule["discount"]) / 100;
                    break;
                }
            }
        } else if (isset($quantity_pricing["rules-by-step"]) && $rules_type == "steps") {
            foreach ($quantity_pricing["rules-by-step"] as $rule) {
                if ($products_qties[$id_to_check] % $rule["every"] == 0) {
                    if ($quantity_pricing["type"] == "fixed")
                        $normal_price-=$rule["discount"];
                    else if ($quantity_pricing["type"] == "percentage")
                        $normal_price-=($normal_price * $rule["discount"]) / 100;
                    break;
                }
            }
        }
        $wad_cart_total_without_taxes = $woocommerce->cart->subtotal_ex_tax;
        if( version_compare( WC()->version , "3.2.1", "<" ) )
                $taxes=$woocommerce->cart->taxes;
        else
            $taxes=$woocommerce->cart->get_cart_contents_taxes();
        $wad_cart_total_inc_taxes = $woocommerce->cart->subtotal_ex_tax + array_sum($taxes);
        if(isset($woocommerce->cart->tax_total) && $woocommerce->cart->tax_total>0 && empty($taxes))
        {
            $wad_cart_total_inc_taxes+=$woocommerce->cart->tax_total;
        }

        //We do this so there is no price strikedthrough on the product page if there is no discount applied.
        if($normal_price==0 && $original_normal_price=="")
            $normal_price=$original_normal_price;
        return $normal_price;
    }

    function save_used_discounts($order_id) {
        if (isset($_SESSION["active_discounts"]) && !empty($_SESSION["active_discounts"])) {
            $used_discounts = array_unique($_SESSION["active_discounts"]);
            foreach ($used_discounts as $discount_id) {
                add_post_meta($order_id, "wad_used_discount", $discount_id);
                $discout_obj = new WAD_Discount($discount_id);
                add_post_meta($order_id, "wad_used_discount_settings_$discount_id", $discout_obj->settings);
            }
            unset($_SESSION["active_discounts"]);
        }
    }

    /**
     * Adds the Custom column to the default products list to help identify which ones are custom
     * @param array $defaults Default columns
     * @return array
     */
    function get_columns($defaults) {
        $defaults['wad_start_date'] = __('Start Date', 'woo-advanced-discounts');
        $defaults['wad_end_date'] = __('End Date', 'woo-advanced-discounts');
        $defaults['wad_active'] = __('Active', 'woo-advanced-discounts');
        return $defaults;
    }

    /**
     * Sets the Custom column value on the products list to help identify which ones are custom
     * @param type $column_name Column name
     * @param type $id Product ID
     */
    function get_columns_values($column_name, $id) {
        //global $wad_discounts;
        //var_dump($wad_discounts);
        $wad_discounts = wad_get_active_discounts();
        if ($column_name === 'wad_active') {
            $class = "";
//            $order_discounts_ids = array_map(create_function('$o', 'return $o->id;'), $wad_discounts["order"]);
//            $products_discounts_ids = array_map(create_function('$o', 'return $o->id;'), $wad_discounts["product"]);
            if (in_array($id, $wad_discounts))
                $class = "active";
            echo "<span class='wad-discount-status $class'></span>";
        }
        else if ($column_name === "wad_start_date") {
            $discount = new WAD_Discount($id);
            if (!$discount->settings) {
                echo "-";
                return;
            }
            $date_formatted = mysql2date(get_option('date_format'), $discount->settings["start-date"], false);
            $time_formatted = mysql2date(get_option('time_format'), $discount->settings["start-date"], false);
            $formatted_date = $date_formatted . ' ' . $time_formatted;
            echo $formatted_date;
        } else if ($column_name === "wad_end_date") {
            $discount = new WAD_Discount($id);
            if (!$discount->settings) {
                echo "-";
                return;
            }
            $date_formatted = mysql2date(get_option('date_format'), $discount->settings["end-date"], false);
            $time_formatted = mysql2date(get_option('time_format'), $discount->settings["end-date"], false);
            $formatted_date = $date_formatted . ' ' . $time_formatted;
            echo $formatted_date;
        }
    }

    /**
     * Adds new tabs in the product page
     */
    function get_product_tab_label($tabs) {
        if(!is_array($tabs))
            return;

        $tabs['wad_quantity_pricing'] = array(
            'label' => __('Quantity Based Pricing', 'woo-advanced-discounts'),
            'target' => 'wad_quantity_pricing_data',
            'class' => array(),
        );
        return $tabs;
    }

    function get_product_tab_data() {
        $begin = array(
            'type' => 'sectionbegin',
            'id' => 'wad-quantity-pricing-rules',
        );

        $discount_enabled = array(
            'title' => __('Enabled', 'woo-advanced-discounts'),
            'name' => 'o-discount[enable]',
            'type' => 'checkbox',
            'value' => 1,
            'desc' => __('Enable/Disable this feature', 'woo-advanced-discounts'),
            'default' => 0
        );

        $discount_type = array(
            'title' => __('Discount type', 'woo-advanced-discounts'),
            'name' => 'o-discount[type]',
            'type' => 'radio',
            'options' => array(
                "percentage" => __("Percentage off product price", "woo-advanced-discounts"),
                "fixed" => __("Fixed amount off product price", "woo-advanced-discounts"),
            ),
            'default' => 'percentage',
            'desc' => __('Apply a percentage or a fixed amount discount', 'woo-advanced-discounts'),
        );

        $rules_types = array(
            'title' => __('Rules type', 'woo-advanced-discounts'),
            'name' => 'o-discount[rules-type]',
            'type' => 'radio',
            'options' => array(
                "intervals" => __("Intervals", "woo-advanced-discounts"),
                "steps" => __("Steps", "woo-advanced-discounts"),
            ),
            'default' => 'intervals',
            'desc' => __('If Intervals, the intervals rules will be used.<br>If Steps, the steps rules will be used.', 'woo-advanced-discounts'),
        );

        $min = array(
            'title' => __('Min', 'woo-advanced-discounts'),
            'name' => 'min',
            'type' => 'number',
            'default' => '',
        );

        $max = array(
            'title' => __('Max', 'woo-advanced-discounts'),
            'name' => 'max',
            'type' => 'number',
            'default' => '',
        );

        $discount = array(
            'title' => __('Discount', 'woo-advanced-discounts'),
            'name' => 'discount',
            'type' => 'number',
            'custom_attributes' => array("step" => "any"),
            'default' => '',
        );

        $discount_rules = array(
            'title' => __('Intervals rules', 'woo-advanced-discounts'),
            'desc' => __('If quantity ordered between Min and Max, then the discount specified will be applied. <br>Leave Min or Max empty for any value (joker).', 'woo-advanced-discounts'),
            'name' => 'o-discount[rules]',
            'type' => 'repeatable-fields',
            'id' => 'intervals_rules',
            'fields' => array($min, $max, $discount),
        );

        $every = array(
            'title' => __('Every X items', 'woo-advanced-discounts'),
            'name' => 'every',
            'type' => 'number',
            'default' => '',
        );

        $discount_rules_steps = array(
            'title' => __('Steps Rules', 'woo-advanced-discounts'),
            'desc' => __('If quantity ordered is a multiple of the step, then the discount specified will be applied.', 'woo-advanced-discounts'),
            'name' => 'o-discount[rules-by-step]',
            'type' => 'repeatable-fields',
            'id' => 'steps_rules',
            'fields' => array($every, $discount),
        );

        $end = array('type' => 'sectionend');
        $settings = array(
            $begin,
            $discount_enabled,
            $discount_type,
            $rules_types,
            $discount_rules,
            $discount_rules_steps,
            $end
        );
        ?>
        <div id="wad_quantity_pricing_data" class="panel woocommerce_options_panel wpc-sh-triggerable">
            <?php
            echo o_admin_fields($settings);
            ?>
        </div>
        <?php
        global $o_row_templates;
        ?>
        <script>
            var o_rows_tpl =<?php echo json_encode($o_row_templates); ?>;
        </script>
        <?php
    }

    function get_quantity_pricing_tables() {
        $product_id = get_the_ID();
        $product_obj = wc_get_product($product_id);
        $quantity_pricing = get_post_meta($product_id, "o-discount", true);
        $rules_type = get_proper_value($quantity_pricing, "rules-type", "intervals");

        ob_start();

        if (isset($quantity_pricing["enable"]) && (isset($quantity_pricing["rules"]) || isset($quantity_pricing["rules-by-step"]))) {
            ?>
            <h3><?php _e("Quantity based pricing table", "woo-advanced-discounts"); ?></h3>

            <?php
            if ($rules_type == "intervals") {
                if ($product_obj->get_type() == "variable") {
                    $available_variations = $product_obj->get_available_variations();
                    foreach ($available_variations as $variation) {
                        $product_price = $variation["display_price"];
                        $this->get_quantity_pricing_table($variation["variation_id"], $quantity_pricing, $product_price);
                    }
                } else {
                    $product_price=$product_obj->get_price();
                    $this->get_quantity_pricing_table($product_id, $quantity_pricing, $product_price, true);
                }
            } else if ($rules_type == "steps") {

                if ($product_obj->get_type() == "variable") {
                    $available_variations = $product_obj->get_available_variations();
                    foreach ($available_variations as $variation) {
                        $product_price = $variation["display_price"];
                        $this->get_steps_quantity_pricing_table($variation["variation_id"], $quantity_pricing, $product_price);
                    }
                } else {
                    $product_price=$product_obj->get_price();
                    $this->get_steps_quantity_pricing_table($product_id, $quantity_pricing, $product_price, true);
                }
            }
        }
        $table=ob_get_clean();
        echo apply_filters('wad_get_quantity_pricing_tables', $table, $product_id, $product_obj);
    }

    private function get_steps_quantity_pricing_table($product_id, $quantity_pricing, $product_price, $display = false) {
        $style = "";
        if (!$display)
            $style = "display: none;";
        ?>
        <table class="wad-qty-pricing-table" data-id="<?php echo $product_id; ?>" style="<?php echo $style; ?>">
            <thead>
                <tr>
                    <th><?php _e("Every multiple of", "woo-advanced-discounts"); ?></th>
                    <th><?php _e("Unit Price", "woo-advanced-discounts"); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($quantity_pricing["rules-by-step"] as $rule) {
                    if ($quantity_pricing["type"] == "fixed") {
                        $price = $product_price - $rule["discount"];
                    } else if ($quantity_pricing["type"] == "percentage") {
                        $price = $product_price - ($product_price * $rule["discount"]) / 100;
                    }
                    ?>
                    <tr>
                        <td><?php echo $rule["every"]; ?></td>
                        <td><?php echo wc_price($price); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    }

    private function get_quantity_pricing_table($product_id, $quantity_pricing, $product_price, $display = false) {
        $style = "";
        if (!$display)
            $style = "display: none;";
        ?>
        <table class="wad-qty-pricing-table" data-id="<?php echo $product_id; ?>" style="<?php echo $style; ?>">
            <thead>
                <tr>
                    <th><?php _e("Min", "woo-advanced-discounts"); ?></th>
                    <th><?php _e("Max", "woo-advanced-discounts"); ?></th>
                    <th><?php _e("Unit Price", "woo-advanced-discounts"); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $price = $product_price;
                foreach ($quantity_pricing["rules"] as $rule) {
                    if ($quantity_pricing["type"] == "fixed") {
                        $price = $product_price - $rule["discount"];
                    } else if ($quantity_pricing["type"] == "percentage") {
                        $price = $product_price - ($product_price * $rule["discount"]) / 100;
                    }
                    ?>
                    <tr>
                        <td><?php echo $rule["min"]; ?></td>
                        <td><?php if(empty($rule["max"])) _e('And more.', 'woo-advanced-discounts'); else echo $rule["max"]; ?></td>
                        <td><?php echo wc_price($price); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    }

    function get_cart_item_quantities() {
        global $woocommerce;
        $item_qties = array();
        if(isset($woocommerce->cart->cart_contents) && is_array( $woocommerce->cart->cart_contents))
        {
            foreach ($woocommerce->cart->cart_contents as $cart_item) {
                if (!empty($cart_item["variation_id"]))
                    $item_qties[$cart_item["variation_id"]] = $cart_item["quantity"];
                else
                    $item_qties[$cart_item["product_id"]] = $cart_item["quantity"];
            }
        }
        return $item_qties;
    }

    function initialize_used_discounts_array() {
        //We hide the notices that this triggers when the debug mode is on.
        $default_error_level = error_reporting();
        error_reporting(0);
        if (!is_admin() && !wad_is_checkout())
            $_SESSION["active_discounts"] = array();
        error_reporting($default_error_level);
    }

    function get_variations_prices($prices, $product) {
        foreach ($prices["regular_price"] as $variation_id => $variation_price) {
            $variation = wc_get_product($variation_id);

            $variation_sale_price = $prices["sale_price"][$variation_id];
            $prices["sale_price"][$variation_id] = $this->get_sale_price($variation_sale_price, $variation);

            $variation_price = $prices["price"][$variation_id];
            $prices["price"][$variation_id] = $this->get_sale_price($variation_price, $variation);
        }
        asort($prices["price"]);
        asort($prices["regular_price"]);
        asort($prices["sale_price"]);

        return $prices;
    }

    function get_cart_subtotal($subtotal){
        $new_subtotal = 0;
        $items = WC()->cart->get_cart_contents();
        if ( !is_cart() && !wad_is_checkout() ){
            foreach($items as $item => $values) {
                $product_obj = $values['data'];
                $product_id = $values['product_id'];
                if ("variation" == $product_obj->get_type()){
                    $product_id = $values['variation_id'];
                }
                $price=$product_obj->get_price();

                $quantity = $values['quantity'];
                $new_subtotal += $price * $quantity;
            }
            $subtotal = wc_price($new_subtotal);
        }
        return $subtotal;
    }

//    public function update_items_prices() {
//        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
//            $product_data = $cart_item[ 'data' ];
//            $product_price = $product_data->get_regular_price();
//            $product_sale_price = $product_data->get_sale_price();
//            if (!$product_sale_price) {
//                $sale_price = $this->apply_quantity_based_discount_if_needed( $product_data, $product_sale_price );
//            } else {
//                $sale_price = $this->apply_quantity_based_discount_if_needed( $product_data, $product_price );
//            }
//        }
//    }

    public function wad_update_products_list() {
        global $wad_last_products_fetch;
        if(!$wad_last_products_fetch)
            $wad_last_products_fetch = wad_get_cart_products ();
    }

    public function get_loop_data($wp_query) {
        global $wad_last_products_fetch;
        global $post;

        if ( is_a( $post, 'WP_Post' ) &&
    					(
    						has_shortcode( $post->post_content, 'products' )
    						|| has_shortcode( $post->post_content, 'sale_products' )
    						|| has_shortcode( $post->post_content, 'best_selling_products' )
    						|| has_shortcode( $post->post_content, 'recent_products' )
    						|| has_shortcode( $post->post_content, 'product_attribute' )
    						|| has_shortcode( $post->post_content, 'top_rated_products' )
    						|| has_shortcode( $post->post_content, 'product_categories' )
    					)
    			)
    			{
    	          return;
    			}

        if(empty($wp_query))
            global $wp_query;


        if (is_cart() || is_checkout()) {
            $cart_products = wad_get_cart_products();
            if ($cart_products)
                $wad_last_products_fetch = $cart_products;
        }
        else {
            if (isset($wp_query->query["post_type"]))
                $query_post_types = $wp_query->query["post_type"];
            else
                $query_post_types = array("product");
            if (
                    !empty($query_post_types) && (
                    (is_array($query_post_types) && in_array("product", $query_post_types))
                    ||(!is_array($query_post_types) && strpos($query_post_types, "product") !== false)
                    ||(is_array($query_post_types) && (
                            is_product_category()
                            || is_product_tag())
                            || is_product_taxonomy()
                    ))
            ) {
                $wad_last_products_fetch = array_map(function($o){ return $o->ID;}, $wp_query->posts);
            }
        }
    }

    public function get_mini_cart_loop_data()
    {
        global $wad_last_products_fetch;
        $wad_last_products_fetch = wad_get_cart_products();
    }

    public static function prepare_product_template_loop_data( $template_name, $template_path, $located, $args ) {
  			global $wad_last_products_fetch;

  			if ( 'single-product/related.php' == $template_name ) {
  				$wad_last_products_fetch = array_map(
  					function( $o ) {
  						return $o->get_id();
  					},
  					$args['related_products']
  				);
  			} elseif ( 'single-product/up-sells.php' == $template_name ) {
  					$wad_last_products_fetch = array_map(
  						function( $o ){
  							return $o->get_id();
  						},
  						$args['upsells']
  					);
  			} elseif ( 'cart/cross-sells.php' == $template_name ) {
  					$wad_last_products_fetch = array_map(
  						function( $o ){ return $o->get_id();
  						},
  						$args['cross_sells']
  					);
  			}
  	}

    public function shortcode_products_query( $args, $attribute, $type ) {
  			  global $wad_discounts;
  			  $wad_on_sale_products = array();

  				if( isset( $attribute['ids'] ) && ! empty( $attribute['ids'] ) ) {
  						return $args;
  				}

  			  if( 'sale_products' === $type ) {
  			          foreach ( $wad_discounts['product'] as $discount_obj ) {

  			                  $product_list         = $discount_obj->products_list->get_products(true);
  			                  $discounted_products  = wad_filter_on_sale_products( $product_list, $discount_obj );
  			                  $wad_on_sale_products = array_merge( $wad_on_sale_products, $discounted_products );
  			          }
  			  }

  				if( empty( $wad_on_sale_products ) ) {
  						return $args;
  				}

  				$args['post__in'] = array_merge( $args['post__in'], $wad_on_sale_products );

  				return $args;
  	}

    public function shortcode_products_query_results( $results, $wc_shortcode ){
  			global $wad_last_products_fetch;
        $woo_shortcode_list = array(
					'products',
					'sale_products',
					'best_selling_products',
					'recent_products',
					'product_attribute',
					'top_rated_products',
					'product_categories'
				);

				if ( in_array( $wc_shortcode->get_type(), $woo_shortcode_list ) ) {
            $wad_last_products_fetch = $results->ids;
  			}

  			return $results;
  	}

    function update_product_lists(){
        global $wad_last_products_fetch;
        $wad_last_products_fetch = wad_get_cart_products();
    }



}
