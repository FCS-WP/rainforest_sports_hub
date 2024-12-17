<?php
/**
 * This file includes helper functions used throughout the theme.
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if(!function_exists('bakerfresh_add_meta_into_head_tag')){
    function bakerfresh_add_meta_into_head_tag(){
        do_action('bakerfresh/action/head');
    }
}

/**
 * Adds classes to the body tag
 *
 * @since 1.0.0
 */
if (!function_exists('bakerfresh_body_classes')) {
    function bakerfresh_body_classes($classes) {
        $classes[] = is_rtl() ? 'rtl' : 'ltr';
        $classes[] = 'bakerfresh-body';
        $classes[] = 'lastudio-bakerfresh';

        $sidebar = apply_filters('bakerfresh/filter/sidebar_primary_name', 'sidebar');

        if(!is_active_sidebar($sidebar) || is_page_template(['templates/no-sidebar.php', 'templates/fullwidth.php'])){
            $classes[] = 'site-no-sidebar';
        }

        if (is_singular('page')) {
            global $post;
            if (strpos($post->post_content, 'la_wishlist') !== false) {
                $classes[] = 'woocommerce-page';
                $classes[] = 'woocommerce-page-wishlist';
            }
            if (strpos($post->post_content, 'la_compare') !== false) {
                $classes[] = 'woocommerce-page';
                $classes[] = 'woocommerce-compare';
            }
        }

        $classes[] = 'body-loading';
	    if( bakerfresh_string_to_bool( get_theme_mod('page_preloader') ) ){
            $classes[] = 'site-loading';
            $classes[] = 'active_page_loading';
        }
        // Return classes
        return $classes;
    }
}

if(!function_exists('bakerfresh_add_pageloader_icon')){
    function bakerfresh_add_pageloader_icon(){
        if( bakerfresh_string_to_bool( get_theme_mod('page_preloader') ) ){
            $loading_style = get_theme_mod('page_preloader_type', 1);
            if($loading_style == 'custom'){
                if(($img = get_theme_mod('page_preloader_custom')) && !empty($img) ){
                    add_filter('bakerfresh/filter/enable_image_lazyload', '__return_false', 10000);
                    add_filter('wp_lazy_loading_enabled', '__return_false', 10000);
                    echo '<div class="la-image-loading spinner-custom"><div class="content"><div class="la-loader"><img src="'.esc_url($img).'" width="50" height="50" alt="'.esc_attr(get_bloginfo('display')).'"/></div><div class="la-loader-ss"></div></div></div>';
                    bakerfresh_deactive_filter('bakerfresh/filter/enable_image_lazyload', '__return_false', 10000);
                    bakerfresh_deactive_filter('wp_lazy_loading_enabled', '__return_false', 10000);
                }
                else{
                    echo '<div class="la-image-loading"><div class="content"><div class="la-loader spinner1"></div><div class="la-loader-ss"></div></div></div>';
                }
            }
            else{
                echo '<div class="la-image-loading"><div class="content"><div class="la-loader spinner'.esc_attr($loading_style).'"><div class="dot1"></div><div class="dot2"></div><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div><div class="cube1"></div><div class="cube2"></div><div class="cube3"></div><div class="cube4"></div></div><div class="la-loader-ss"></div></div></div>';
            }
        }
    }
}

if(!function_exists('bakerfresh_change_excerpt_length')){
    function bakerfresh_change_excerpt_length( $length ){
        $excerpt_length = 20;
        if($excerpt_length > 0){
            return $excerpt_length;
        }
        return $length;
    }
}