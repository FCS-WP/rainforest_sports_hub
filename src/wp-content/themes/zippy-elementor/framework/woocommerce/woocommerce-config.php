<?php
/**
 * Perform all main WooCommerce configurations for this theme
 *
 * @package Bakerfresh WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!class_exists('Bakerfresh_WooCommerce_Config')){

    class Bakerfresh_WooCommerce_Config {

        /**
         * Main Class Constructor
         *
         * @since 1.0.0
         */
        public function __construct() {

            // Include helper functions
            require_once get_theme_file_path('/framework/woocommerce/woocommerce-helpers.php');
            require_once get_theme_file_path('/framework/woocommerce/woocommerce-compare.php');
            require_once get_theme_file_path('/framework/woocommerce/woocommerce-wishlist.php');

            add_filter('bakerfresh/filter/sidebar_primary_name', array( $this, 'set_sidebar_for_shop'), 20 );

            add_action('init', array( $this, 'set_cookie_default' ), 2 );
            add_action('init', array( $this, 'custom_handling_empty_cart' ), 1 );

            add_filter('loop_shop_per_page', array( $this, 'change_per_page_default'), 10 );

            // Remove WooCommerce default style
            add_filter( 'woocommerce_enqueue_styles', array($this, 'remove_woo_scripts') );

            // Load theme CSS
            add_action( 'wp_enqueue_scripts', array( $this, 'theme_css' ), 20 );

            // Load theme js
            add_action( 'wp_enqueue_scripts', array( $this, 'theme_js' ), 20 );

            // register sidebar widget areas
            add_action( 'widgets_init', array( $this, 'register_sidebars' ) );

            /**
             * Hooks in plugins
             */
            add_filter('woocommerce_show_page_title', '__return_false');
            add_action('init', array( $this, 'disable_plugin_hooks'));

            add_filter('template_include', array( $this, 'load_quickview_template'), 20 );

            /**
             * Hooks in plugins
             * WC_Vendors
             */
            if(class_exists('WC_Vendors', false)){
                // Add sold by to product loop before add to cart
                if ( WC_Vendors::$pv_options->get_option( 'sold_by' ) ) {
                    remove_action( 'woocommerce_after_shop_loop_item', array('WCV_Vendor_Shop', 'template_loop_sold_by'), 9 );
                    add_action( 'woocommerce_shop_loop_item_title', array('WCV_Vendor_Shop', 'template_loop_sold_by'), 10 );
                }
            }

            /**
             * Hooks in plugins
             */

            /**
             * Remove default wrappers and add new ones
             */
            remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
            remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
            remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
            add_action( 'woocommerce_before_main_content', array( $this, 'content_wrapper' ), 10 );
            add_action( 'woocommerce_after_main_content', array( $this, 'content_wrapper_end' ), 10 );


            /**
             * For Shop Page & Taxonomies
             */

            add_filter('woocommerce_post_class', array( $this, 'add_class_to_product_loop'), 30, 2 );

            add_action('woocommerce_before_subcategory_title', function (){ echo '<figure>'; }, 9);
            add_action('woocommerce_before_subcategory_title', function (){ echo '</figure>'; }, 11);

            add_action('woocommerce_before_shop_loop',  [ $this, 'setup_toolbar' ] , -999 );

            add_action('woocommerce_before_shop_loop',  [ $this, 'add_toolbar_open' ] , 15 );
            add_action('woocommerce_before_shop_loop',  [ $this, 'add_toolbar_close' ] , 35 );

            add_filter('woocommerce_loop_add_to_cart_args', array( $this, 'woocommerce_loop_add_to_cart_args'), 10, 2 );

            add_action('lastudio-kit/products/before_render', [ $this, 'add_extra_hook_to_product_item' ] );

            /**
             * For details page
             */

            add_filter('woocommerce_gallery_image_size', function(){ return 'shop_single'; } );
            add_action('woocommerce_before_add_to_cart_button', function(){ echo '<div class="wrap-cart-cta">'; }, 100);
            add_action('woocommerce_after_add_to_cart_button', function(){ echo '</div>'; }, 0);
            add_action('woocommerce_after_add_to_cart_button', array( $this , 'add_hidden_button_to_to_cart_form' ) );

            add_action('woocommerce_after_add_to_cart_button', array( $this , 'add_wishlist_btn' ), 50 );
            add_action('woocommerce_after_add_to_cart_button', array( $this , 'add_compare_btn' ), 55 );


            add_action('woocommerce_before_single_product_summary', array($this, 'wrapper_before_product_main'), -101);
            add_action('woocommerce_before_single_product_summary', array($this, 'wrapper_before_product_main_image'), -100);
            add_action('woocommerce_before_single_product_summary', array($this, 'wrapper_after_product_main_image'), 100);
            add_action('woocommerce_after_single_product_summary', array($this, 'wrapper_after_product_main'), -100);

            add_filter('woocommerce_product_tabs', array( $this, 'add_custom_tabs'));


            /**
             * For Cart
             */
            remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');

            /**
             * Catalog Mode
             */
            if( bakerfresh_string_to_bool( bakerfresh_get_theme_mod('catalog_mode_price') ) ){
                // In Loop
                add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );
                // In Single
                remove_action('woocommerce_single_product_summary','woocommerce_template_single_add_to_cart',30);
                // In Page
                add_action( 'wp', array( $this, 'set_page_when_active_catalog_mode' ) );

                if( bakerfresh_string_to_bool( bakerfresh_get_theme_mod('catalog_mode_price') ) ){
                    //remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price');
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price');
                    add_filter('woocommerce_catalog_orderby', array( $this, 'remove_sortby_price_in_toolbar_when_active_catalog' ));
                    add_filter('woocommerce_default_catalog_orderby_options', array( $this, 'remove_sortby_price_in_toolbar_when_active_catalog' ));
                }
            }
        }

        public function register_sidebars(){
            $heading = 'h4';
            $heading = apply_filters( 'bakerfresh/filter/sidebar_heading', $heading );

            // Shop Sidebar
            register_sidebar( array(
                'name'			=> esc_html__( 'Sidebar - Shop', 'bakerfresh' ),
                'id'			=> 'sidebar-shop',
                'description'	=> esc_html__( 'Widgets in this area will be displayed in the shop page.', 'bakerfresh' ),
                'before_widget'	=> '<div id="%1$s" class="widget %2$s">',
                'after_widget'	=> '</div>',
                'before_title'	=> '<'. $heading .' class="widget-title"><span>',
                'after_title'	=> '</span></'. $heading .'>',
            ) );

            register_sidebar( array(
                'name'			=> esc_html_x( 'Sidebar - Filter', 'admin-view',  'bakerfresh' ),
                'id'            => 'sidebar-shop-filter',
                'before_widget'	=> '<div id="%1$s" class="widget %2$s">',
                'after_widget'	=> '</div>',
                'before_title'	=> '<'. $heading .' class="widget-title"><span>',
                'after_title'	=> '</span></'. $heading .'>',
            ) );
        }

        /**
         * Removes WooCommerce scripts.
         *
         * @access public
         * @since 1.0
         * @param array $scripts The WooCommerce scripts.
         * @return array
         */
        public function remove_woo_scripts($scripts) {
            if (isset($scripts['woocommerce-layout'])) {
                unset($scripts['woocommerce-layout']);
            }
            if (isset($scripts['woocommerce-smallscreen'])) {
                unset($scripts['woocommerce-smallscreen']);
            }
            if (isset($scripts['woocommerce-general'])) {
                unset($scripts['woocommerce-general']);
            }
            return $scripts;
        }

        public function theme_css(){

        }

        public function theme_js(){
            $theme_version = defined('WP_DEBUG') && WP_DEBUG ? time() : BAKERFRESH_THEME_VERSION;
            $ext = apply_filters('bakerfresh/use_minify_js_file', false) || ( defined('WP_DEBUG') && WP_DEBUG ) ? '' : '.min';

            wp_register_script('bakerfresh-woocommerce', get_theme_file_uri( '/assets/js/lib/woocommerce'. $ext .'.js' ), array('jquery'), $theme_version, true);
            wp_add_inline_script('wc-single-product', $this->product_image_flexslider_vars(), 'before');
        }

        public function product_image_flexslider_vars(){
            return "try{ wc_single_product_params.flexslider.directionNav=true; wc_single_product_params.flexslider.before = function(slider){ jQuery(document).trigger('lastudiokit/woocommerce/single/init_product_slider', [slider]); } }catch(ex){}";
        }

        /**
         * Content wrapper.
         */
        public function content_wrapper() {
            get_template_part( 'woocommerce/wc-content-wrapper' );
        }

        /**
         * Content wrapper end.
         */
        public function content_wrapper_end() {
            get_template_part( 'woocommerce/wc-content-wrapper-end' );
        }

        /**
         *
         * Override the sidebar for shop
         *
         * @param $sidebar
         * @return mixed
         */

        public function set_sidebar_for_shop( $sidebar ) {

            if( is_woocommerce() || is_account_page() || is_checkout() || is_cart() || is_wc_endpoint_url() ){
                $sidebar = 'sidebar-shop';
            }

            return $sidebar;
        }

        public function woocommerce_loop_add_to_cart_args( $args, $product) {
            if(isset($args['attributes'])){
                $args['attributes']['data-product_title'] = $product->get_title();
            }
            if(isset($args['class'])){
                $args['class'] = $args['class'] . ($product->is_purchasable() && $product->is_in_stock() ? '' : ' add_to_cart_button');
            }
            return $args;
        }

        public function add_extra_hook_to_product_item(){
            add_action('woocommerce_shop_loop_item_title', [ $this, 'add_category_into_product_loop' ], 5);
            add_action('woocommerce_before_shop_loop_item_title', [ $this, 'add_custom_badge_into_product_loop' ], 9);

            add_action('lastudio-kit/products/action/shop_loop_item_action_top', [ $this, 'add_quick_view_btn' ], 10);
            add_action('lastudio-kit/products/action/shop_loop_item_action_top', [ $this, 'add_wishlist_btn' ], 15);
            add_action('lastudio-kit/products/action/shop_loop_item_action_top', [ $this, 'add_compare_btn' ], 20);

            add_action('lastudio-kit/products/action/shop_loop_item_action', [ $this, 'add_quick_view_btn' ], 10);
            add_action('lastudio-kit/products/action/shop_loop_item_action', [ $this, 'add_wishlist_btn' ], 15);
            add_action('lastudio-kit/products/action/shop_loop_item_action', [ $this, 'add_compare_btn' ], 20);

            add_action('woocommerce_after_shop_loop_item_title', [ $this, 'add_attribute_into_product_loop' ] , 11);
            add_action('woocommerce_after_shop_loop_item_title', array( $this, 'add_excerpt_into_product_loop' ), 15 );

        }

        public function add_category_into_product_loop(){
            return;
            global $product;
            echo wc_get_product_category_list( $product->get_id(), '', '<div class="product_item--category-link">', '</div>' );
        }

        public function add_custom_badge_into_product_loop(){
            global $product;
            $availability = $product->get_availability();
            if(!empty($availability['class']) && $availability['class'] == 'out-of-stock' && !empty($availability['availability'])){
                printf('<span class="la-custom-badge badge-out-of-stock">%s</span>', esc_html($availability['availability']));
            }
            $product_badges = bakerfresh_get_post_meta($product->get_id(), 'product_badges');
            if(empty($product_badges)){
                return;
            }
            $_tmp_badges = array();
            foreach($product_badges as $badge){
                if(!empty($badge['text'])){
                    $_tmp_badges[] = $badge;
                }
            }
            if(empty($_tmp_badges)){
                return;
            }
            foreach($_tmp_badges as $i => $badge){
                $attribute = array();
                if(!empty($badge['bg'])){
                    $attribute[] = 'background-color:' . esc_attr($badge['bg']);
                }
                if(!empty($badge['color'])){
                    $attribute[] = 'color:' . esc_attr($badge['color']);
                }
                $el_class = ($i%2==0) ? 'odd' : 'even';
                if(!empty($badge['el_class'])){
                    $el_class .= ' ';
                    $el_class .= $badge['el_class'];
                }

                echo sprintf(
                    '<span class="la-custom-badge %1$s" style="%3$s"><span>%2$s</span></span>',
                    esc_attr($el_class),
                    esc_html($badge['text']),
                    (!empty($attribute) ? esc_attr(implode(';', $attribute)) : '')
                );
            }
        }

        public function add_attribute_into_product_loop(){
            if(class_exists('LaStudio_Swatch', false)){
                global $product;
                $swatches_instance = new LaStudio_Swatch();
                $swatches_instance->render_attribute_in_product_list_loop($product);
            }
        }

        public function add_excerpt_into_product_loop(){

            $layout = wc_get_loop_prop('lakit_layout');
            $preset = wc_get_loop_prop('lakit_preset');
            $type = wc_get_loop_prop('lakit_type');

            if($type == 'current_query' || $layout == 'list' || ( $layout == 'grid' && $preset == 8 ) ){
                echo '<div class="item--excerpt">';
                echo wp_trim_words( get_the_excerpt(), 10 );
                echo '</div>';
            }
        }

        public function add_quick_view_btn(){
	        if( bakerfresh_string_to_bool( get_theme_mod('woocommerce_show_quickview_btn') ) ) {
                global $product;
                printf(
                    '<a class="%s" href="%s" data-href="%s" title="%s"><span class="labtn-icon labtn-icon-quickview"></span><span class="labtn-text">%s</span></a>',
                    'quickview button la-quickview-button',
                    esc_url(get_the_permalink($product->get_id())),
                    esc_url(add_query_arg('product_quickview', $product->get_id(), get_the_permalink($product->get_id()))),
                    esc_attr_x('Quick View', 'front-view', 'bakerfresh'),
                    esc_attr_x('Quick View', 'front-view', 'bakerfresh')
                );
            }
        }

        public function add_compare_btn(){
            global $yith_woocompare, $product;
            if( bakerfresh_string_to_bool( get_theme_mod('woocommerce_show_compare_btn') ) ) {
                if ( !empty($yith_woocompare->obj) ) {

                    $action_add = 'yith-woocompare-add-product';

                    $css_class = 'add_compare button';

                    if( $yith_woocompare->obj instanceof YITH_Woocompare_Frontend ){
                        $action_add = $yith_woocompare->obj->action_add;
                        if(!empty($yith_woocompare->obj->products_list) && in_array($product->get_id(), $yith_woocompare->obj->products_list)){
                            $css_class .= ' added';
                        }
                    }
                    $url_args = array('action' => $action_add, 'id' => $product->get_id());
                    $url = apply_filters('yith_woocompare_add_product_url', wp_nonce_url(add_query_arg($url_args), $action_add));

                    printf(
                        '<a class="%s" href="%s" title="%s" rel="nofollow" data-product_title="%s" data-product_id="%s"><span class="labtn-icon labtn-icon-compare"></span><span class="labtn-text">%s</span></a>',
                        esc_attr($css_class),
                        esc_url($url),
                        esc_attr_x('Add to compare','front-view', 'bakerfresh'),
                        esc_attr($product->get_title()),
                        esc_attr($product->get_id()),
                        esc_attr_x('Add to compare','front-view', 'bakerfresh')
                    );
                }
                else{
                    $css_class = 'add_compare button la-core-compare';
                    $url = $product->get_permalink();
                    $text = esc_html_x('Add to compare','front-view', 'bakerfresh');
                    printf(
                        '<a class="%s" href="%s" title="%s" rel="nofollow" data-product_title="%s" data-product_id="%s"><span class="labtn-icon labtn-icon-compare"></span><span class="labtn-text">%s</span></a>',
                        esc_attr($css_class),
                        esc_url($url),
                        esc_attr($text),
                        esc_attr($product->get_title()),
                        esc_attr($product->get_id()),
                        esc_attr($text)
                    );
                }
            }
        }

        public function add_wishlist_btn(){

	        if( bakerfresh_string_to_bool( get_theme_mod('woocommerce_show_wishlist_btn') ) ) {
                global $product;
                if (function_exists('YITH_WCWL')) {
                    $default_wishlists = is_user_logged_in() ? YITH_WCWL()->get_wishlists(array('is_default' => true)) : false;
                    if (!empty($default_wishlists)) {
                        $default_wishlist = $default_wishlists[0]['ID'];
                    }
                    else {
                        $default_wishlist = false;
                    }

                    if (YITH_WCWL()->is_product_in_wishlist($product->get_id(), $default_wishlist)) {
                        $text = esc_html_x('View Wishlist', 'front-view', 'bakerfresh');
                        $class = 'add_wishlist la-yith-wishlist button added';
                        $url = YITH_WCWL()->get_wishlist_url('');
                    }
                    else {
                        $text = esc_html_x('Add to Wishlist', 'front-view', 'bakerfresh');
                        $class = 'add_wishlist la-yith-wishlist button';
                        $url = add_query_arg('add_to_wishlist', $product->get_id(), YITH_WCWL()->get_wishlist_url(''));
                    }

                    printf(
                        '<a class="%s" href="%s" title="%s" rel="nofollow" data-product_title="%s" data-product_id="%s"><span class="labtn-icon labtn-icon-wishlist"></span><span class="labtn-text">%s</span></a>',
                        esc_attr($class),
                        esc_url($url),
                        esc_attr($text),
                        esc_attr($product->get_title()),
                        esc_attr($product->get_id()),
                        esc_attr($text)
                    );
                }

                elseif(class_exists('TInvWL_Public_AddToWishlist', false)){
                    $wishlist = TInvWL_Public_AddToWishlist::instance();
                    $user_wishlist = $wishlist->user_wishlist($product);
                    if(isset($user_wishlist[0], $user_wishlist[0]['in']) && $user_wishlist[0]['in']){
                        $class = 'add_wishlist button la-ti-wishlist added';
                        $url = tinv_url_wishlist_default();
                        $text = esc_html_x('View Wishlist', 'front-view', 'bakerfresh');
                    }
                    else{
                        $class = 'add_wishlist button la-ti-wishlist';
                        $url = $product->get_permalink();
                        $text = esc_html_x('Add to wishlist', 'front-view', 'bakerfresh');
                    }
                    printf(
                        '<a class="%s" href="%s" title="%s" rel="nofollow" data-product_title="%s" data-product_id="%s"><span class="labtn-icon labtn-icon-wishlist"></span><span class="labtn-text">%s</span></a>',
                        esc_attr($class),
                        esc_url($url),
                        esc_attr($text),
                        esc_attr($product->get_title()),
                        esc_attr($product->get_id()),
                        esc_attr($text)
                    );
                }

                else{

                    if(Bakerfresh_WooCommerce_Wishlist::is_product_in_wishlist($product->get_id())){
                        $class = 'add_wishlist button la-core-wishlist added';
                        $url = bakerfresh_get_wishlist_url();
                        $text = esc_html_x('View Wishlist', 'front-view', 'bakerfresh');
                    }
                    else{
                        $class = 'add_wishlist button la-core-wishlist';
                        $url = $product->get_permalink();
                        $text = esc_html_x('Add to wishlist', 'front-view', 'bakerfresh');
                    }

                    printf(
                        '<a class="%s" href="%s" title="%s" rel="nofollow" data-product_title="%s" data-product_id="%s"><span class="labtn-icon labtn-icon-wishlist"></span><span class="labtn-text">%s</span></a>',
                        esc_attr($class),
                        esc_url($url),
                        esc_attr($text),
                        esc_attr($product->get_title()),
                        esc_attr($product->get_id()),
                        esc_attr($text)
                    );
                }
            }
        }

        public function add_class_to_product_loop( $classes, $product ) {

            if($product->is_type( 'variable' )){
                if($product->child_is_in_stock()){
                    $classes[] = 'child-instock';
                }
            }
            return $classes;
        }


        public function custom_handling_empty_cart(){
            if (isset($_REQUEST['clear-cart'])) {
                WC()->cart->empty_cart();
            }
        }

        public function change_per_page_default($cols){
            $per_page_array = bakerfresh_woo_get_product_per_page_array();
            $per_page = bakerfresh_woo_get_product_per_page();
            if(!empty($per_page_array) && ( in_array($per_page, $per_page_array) || count($per_page_array) == 1  )){
                $cols = $per_page;
            }
            else{
                $cols = $per_page;
            }
            return $cols;
        }

        public function set_cookie_default(){
            if (isset($_GET['per_page'])) {
                add_filter('bakerfresh/filter/get_product_per_page', array( $this, 'get_parameter_per_page'));
            }
        }

        public function get_parameter_per_page($per_page) {
            if (isset($_GET['per_page']) && ($_per_page = $_GET['per_page'])) {
                $param_allow = bakerfresh_woo_get_product_per_page_array();
                if(!empty($param_allow) && in_array($_per_page, $param_allow)){
                    $per_page = $_per_page;
                }
            }
            return $per_page;
        }

        public function disable_plugin_hooks() {
            global $yith_woocompare;
            if(function_exists('YITH_WCWL_Init')){
                $yith_wcwl_obj = YITH_WCWL_Init();
                remove_action('wp_head', array($yith_wcwl_obj, 'add_button'));
            }
            if( !empty($yith_woocompare->obj) && ($yith_woocompare->obj instanceof YITH_Woocompare_Frontend ) ){
                remove_action('woocommerce_single_product_summary', array($yith_woocompare->obj, 'add_compare_link'), 35);
                remove_action('woocommerce_after_shop_loop_item', array($yith_woocompare->obj, 'add_compare_link'), 20);
            }
        }

        public function load_quickview_template( $template ){
            if(is_singular('product') && isset($_GET['product_quickview'])){
                $file     = locate_template( array(
                    'woocommerce/single-quickview.php'
                ) );
                if($file){
                    return $file;
                }
            }
            return $template;
        }

        public function add_hidden_button_to_to_cart_form(){
            global $product;
            if($product->is_type('simple')){
                echo '<input type="hidden" name="add-to-cart" value="'.esc_attr($product->get_id()).'"/>';
            }
        }

        public function add_custom_tabs( $tabs ){

            if(bakerfresh_string_to_bool(bakerfresh_get_option('woo_enable_custom_tab'))){
                $custom_tabs = bakerfresh_get_option('woo_custom_tabs');
                if(!empty($custom_tabs) && is_array($custom_tabs)){
                    foreach ($custom_tabs as $k => $custom_tab){
                        if(!empty($custom_tab['title']) && !empty($custom_tab['content'])){
                            $tabs['lasf_tab_' . $k] = array(
                                'title' => esc_html($custom_tab['title']),
                                'priority' => 50 + ($k * 5),
                                'custom_content' => $custom_tab['content'],
                                'el_class'  => isset($custom_tab['el_class']) ? $custom_tab['el_class'] : '',
                                'callback' => array( $this, 'callback_custom_tab_content')
                            );
                        }
                    }
                }
            }

            return $tabs;
        }

        public function callback_custom_tab_content( $tab_key, $tab_instance ){
            if(!empty($tab_instance['custom_content'])){
                echo wp_kses_post( bakerfresh_transfer_text_to_format($tab_instance['custom_content'], true) );
            }
        }

        public function wrapper_before_product_main_image(){
            echo '<div class="woocommerce-product-gallery-outer layout-type-1">';
        }

        public function wrapper_after_product_main_image(){
            echo '</div>';
        }

        public function wrapper_before_product_main(){
            echo '<div class="product--inner">';
        }

        public function wrapper_after_product_main(){
            echo '</div>';
        }

        /*
         * Catalog Mode
         */
        public function set_page_when_active_catalog_mode(){
            wp_reset_postdata();
            if (is_cart() || is_checkout()) {
                wp_redirect(wc_get_page_permalink('shop'));
                exit;
            }
        }
        public function remove_sortby_price_in_toolbar_when_active_catalog( $array ){
            if( isset($array['price']) ){
                unset( $array['price'] );
            }
            if( isset($array['price-desc']) ){
                unset( $array['price-desc'] );
            }
            return $array;
        }

        private function get_product_per_page_option(){
            $per_page_array = apply_filters('bakerfresh/filter/get_product_per_page_array', bakerfresh_get_theme_mod('product_per_page_allow', ''));
            if(!empty($per_page_array)){
                $per_page_array = explode(',', $per_page_array);
                $per_page_array = array_map('trim', $per_page_array);
                $per_page_array = array_map('absint', $per_page_array);
                asort($per_page_array);
                return $per_page_array;
            }
            else{
                return array();
            }
        }
        private function get_product_per_row_option(){
            $per_page_array = apply_filters('bakerfresh/filter/get_product_per_row_array', bakerfresh_get_theme_mod('product_per_row_allow', ''));
            if(!empty($per_page_array)){
                $per_page_array = explode(',', $per_page_array);
                $per_page_array = array_map('trim', $per_page_array);
                $per_page_array = array_map('absint', $per_page_array);
                asort($per_page_array);
                return $per_page_array;
            }
            else{
                return array();
            }
        }
        private function get_current_product_per_page(){
            return apply_filters('bakerfresh/filter/get_product_per_page', bakerfresh_get_theme_mod('product_per_page_default', 9));
        }
        public function add_toolbar_open(){
            if(wc_get_loop_prop('lakit_loop_allow_extra_filters')){
                echo '<div class="wc-toolbar-container">';
                echo '<div class="wc-toolbar wc-toolbar-top">';
            }
        }

        public function add_toolbar_close(){
            if(wc_get_loop_prop('lakit_loop_allow_extra_filters')){
                $view_mode = apply_filters('bakerfresh/filter/catalog_view_mode', 'grid');
                $woocommerce_toggle_grid_list = true;
                $active_shop_filter = true;
                $per_page_array = $this->get_product_per_page_option();
                $per_row_array = $this->get_product_per_row_option();
                $per_page =  $this->get_current_product_per_page();
                $current_url = add_query_arg(null, null);
                $current_url = remove_query_arg(array('page', 'paged', 'mode_view', 'la_doing_ajax'), $current_url);
                $current_url = preg_replace('/\/page\/\d+/', '', $current_url);

                if(!empty($per_row_array)){
                    echo '<div class="lasf-custom-dropdown wc-view-item">';
                    echo sprintf('<button><span data-text="%1$s">%1$s</span></button>', esc_html__('View', 'bakerfresh'));
                    echo '<ul>';
                    foreach ($per_row_array as $val){
                        echo sprintf('<li><a data-col="%3$s" href="javascript:;">%1$s %2$s</a></li>', esc_html__('View', 'bakerfresh'), ($val < 10 ? '0' . $val : $val), esc_html($val));
                    }
                    echo '</ul>';
                    echo '</div>';
                }
                if(!empty($per_page_array)){
                    echo '<div class="lasf-custom-dropdown wc-view-count">';
                    echo sprintf('<button><span data-text="%1$s">%1$s %2$s</span></button>', esc_html__('Show', 'bakerfresh'), esc_html($per_page));
                    echo '<ul>';
                    foreach ($per_page_array as $val){
                        echo sprintf('<li %4$s><a href="%3$s">%1$s %2$s</a></li>',
                            esc_html__('Show', 'bakerfresh'),
                            esc_html($val),
                            esc_url(add_query_arg('per_page', $val, $current_url)),
                            ( $per_page === $val ? 'class="active"' : '' )
                        );
                    }
                    echo '</ul>';
                    echo '</div>';
                }
                if($active_shop_filter && is_active_sidebar('sidebar-shop-filter')){
                    echo sprintf('<div class="lasf-custom-dropdown wc-custom-filters"><button class="btn-advanced-shop-filter"><span>%1$s</span></button></div>', esc_html__('Filters', 'bakerfresh'));
                }
                if( bakerfresh_string_to_bool($woocommerce_toggle_grid_list) ):
                    ?>
                    <div class="wc-view-toggle">
                        <button data-view_mode="list"<?php if ($view_mode == 'list') echo ' class="active"';?>><i title="<?php echo esc_attr__('List view', 'bakerfresh') ?>" class="lastudioicon-list-bullet-2"></i></button>
                        <button data-view_mode="grid"<?php if ($view_mode == 'grid')  echo ' class="active"'; ?>><i title="<?php echo esc_attr__('Grid view', 'bakerfresh') ?>" class="lastudioicon-microsoft"></i></button>
                    </div>
                <?php
                endif;

                    echo '</div>';

                if (bakerfresh_string_to_bool($active_shop_filter) && is_active_sidebar('sidebar-shop-filter')):
                ?>
                    <div class="clearfix"></div>
                    <div class="la-advanced-product-filters widget-area clearfix" data-id="shop-advance-filter">
                        <div class="sidebar-inner">
                            <div class="sidebar-inner--filters">
                                <?php dynamic_sidebar('sidebar-shop-filter'); ?>
                            </div>
                            <?php if( is_filtered() || (!is_filtered() && is_product_taxonomy() && isset($_GET['la_doing_ajax'])) ) : ?>
                                <div class="la-advanced-product-filters-result">
                                    <?php
                                    $base_filter = bakerfresh_get_base_shop_url();
                                    if(isset($_GET['la_preset'])){
                                        $base_filter = add_query_arg('la_preset', $_GET['la_preset'], $base_filter);
                                    }
                                    ?>
                                    <a class="reset-all-shop-filter" href="<?php echo esc_url($base_filter) ?>"><i class="lastudioicon-e-remove"></i><span><?php echo esc_html__('Clear All Filter', 'bakerfresh'); ?></span></a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a class="close-advanced-product-filters" href="javascript:;" rel="nofollow"><i class="lastudioicon-e-remove"></i></a>
                    </div>
                <?php
                endif;

                echo '</div>';
            }
        }

        public function setup_toolbar(){
            if(empty(wc_get_loop_prop('lakit_loop_before')) && (is_shop() || is_product_taxonomy())){
                wc_set_loop_prop('lakit_loop_allow_extra_filters', true);
            }
        }

    }

}

new Bakerfresh_WooCommerce_Config();