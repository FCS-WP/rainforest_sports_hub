<?php
/**
 * This file includes helper functions used throughout the theme.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Return theme settings
 */

if ( ! function_exists( 'bakerfresh_get_theme_mod' ) ) {

    function bakerfresh_get_theme_mod( $key = '', $default = '' ) {
        $mods = get_theme_mods();

        if ( empty( $mods ) || $key == '' ) {
            $value = $default;
        } else {
            $value = ! empty( $mods[ $key ] ) ? $mods[ $key ] : $default;
        }

        return apply_filters( 'bakerfresh/filter/bakerfresh_get_theme_mode', $value, $key, $default, $mods );
    }

}

if ( ! function_exists( 'bakerfresh_get_option' ) ) {

    function bakerfresh_get_option( $key = '', $default = '' ) {
        $theme_options = get_option('bakerfresh_options', array());

        if(empty($theme_options) || $key == ''){
            $value = $default;
        }
        else{
            $value = !empty($theme_options[$key]) ? $theme_options[$key] : $default;
        }

        return apply_filters( 'bakerfresh/filter/get_option', $value, $key, $default, $theme_options);
    }

}

if ( ! function_exists( 'bakerfresh_get_post_meta' ) ) {
    function bakerfresh_get_post_meta( $object_id, $sub_key = '', $meta_key = '', $single = true ) {

        if (!is_numeric($object_id)) {
            return false;
        }

        if (empty($meta_key)) {
            $meta_key = '_bakerfresh_post_options';
        }

        $object_value = get_post_meta($object_id, $meta_key, $single);

        if(!empty($sub_key)){
            if( $single ) {
                if(isset($object_value[$sub_key])){
                    return $object_value[$sub_key];
                }
                else{
                    return false;
                }
            }
            else{
                $tmp = array();
                if( ! empty( $object_value ) ) {
                    foreach( $object_value as $k => $v ){
                        $tmp[] = (isset($v[$sub_key])) ? $v[$sub_key] : '';
                    }
                }
                return $tmp;
            }
        }
        else{
            return $object_value;
        }
    }
}

if ( ! function_exists( 'bakerfresh_get_term_meta' ) ) {
    function bakerfresh_get_term_meta( $object_id, $sub_key = '', $meta_key = '', $single = true ) {

        if (!is_numeric($object_id)) {
            return false;
        }

        if (empty($meta_key)) {
            $meta_key = '_bakerfresh_term_options';
        }

        $object_value = get_term_meta($object_id, $meta_key, $single);

        if(!empty($sub_key)){
            if( $single ) {
                if(isset($object_value[$sub_key])){
                    return $object_value[$sub_key];
                }
                else{
                    return false;
                }
            }
            else{
                $tmp = array();
                if(!empty($object_value)){
                    foreach( $object_value as $k => $v ){
                        $tmp[] = (isset($v[$sub_key])) ? $v[$sub_key] : '';
                    }
                }
                return $tmp;
            }
        }
        else{
            return $object_value;
        }
        
    }
}

/**
 * Return correct schema markup
 */

if ( ! function_exists( 'bakerfresh_get_schema_markup' ) ) {

    function bakerfresh_get_schema_markup( $location, $original_render = false ) {

        // Return if disable
        if ( ! get_theme_mod( 'schema_markup' ) ) {
            return null;
        }

        // Default
        $schema = $itemprop = $itemtype = '';

        // HTML
        if ( 'html' == $location ) {
            $schema = 'itemscope itemtype="//schema.org/WebPage"';
        }

        // Header
        elseif ( 'header' == $location ) {
            $schema = 'itemscope="itemscope" itemtype="//schema.org/WPHeader"';
        }

        // Logo
        elseif ( 'logo' == $location ) {
            $schema = 'itemscope itemtype="//schema.org/Brand"';
        }

        // Navigation
        elseif ( 'site_navigation' == $location ) {
            $schema = 'itemscope="itemscope" itemtype="//schema.org/SiteNavigationElement"';
        }

        // Main
        elseif ( 'main' == $location ) {
            $itemtype = '//schema.org/WebPageElement';
            $itemprop = 'mainContentOfPage';
            if ( is_singular( 'post' ) ) {
                $itemprop = '';
                $itemtype = '//schema.org/Blog';
            }
        }

        // Breadcrumb
        elseif ( 'breadcrumb' == $location ) {
            $schema = 'itemscope itemtype="//schema.org/BreadcrumbList"';
        }

        // Breadcrumb list
        elseif ( 'breadcrumb_list' == $location ) {
            $schema = 'itemprop="itemListElement" itemscope itemtype="//schema.org/ListItem"';
        }

        // Breadcrumb itemprop
        elseif ( 'breadcrumb_itemprop' == $location ) {
            $schema = 'itemprop="breadcrumb"';
        }

        // Sidebar
        elseif ( 'sidebar' == $location ) {
            $schema = 'itemscope="itemscope" itemtype="//schema.org/WPSideBar"';
        }

        // Footer widgets
        elseif ( 'footer' == $location ) {
            $schema = 'itemscope="itemscope" itemtype="//schema.org/WPFooter"';
        }

        // Headings
        elseif ( 'headline' == $location ) {
            $schema = 'itemprop="headline"';
        }

        // Posts
        elseif ( 'entry_content' == $location ) {
            $schema = 'itemprop="text"';
        }

        // Publish date
        elseif ( 'publish_date' == $location ) {
            $schema = 'itemprop="datePublished"';
        }

        // Author name
        elseif ( 'author_name' == $location ) {
            $schema = 'itemprop="name"';
        }

        // Author link
        elseif ( 'author_link' == $location ) {
            $schema = 'itemprop="author" itemscope="itemscope" itemtype="//schema.org/Person"';
        }

        // Item
        elseif ( 'item' == $location ) {
            $schema = 'itemprop="item"';
        }

        // Url
        elseif ( 'url' == $location ) {
            $schema = 'itemprop="url"';
        }

        // Position
        elseif ( 'position' == $location ) {
            $schema = 'itemprop="position"';
        }

        // Image
        elseif ( 'image' == $location ) {
            $schema = 'itemprop="image"';
        }

        // Name
        elseif ( 'name' == $location ) {
            $schema = 'itemprop="name"';
        }
        else{
            if($original_render){
                $schema = $location;
            }
        }
        return ' ' . apply_filters( 'bakerfresh_schema_markup', $schema, $location );

    }

}

if ( ! function_exists( 'bakerfresh_schema_markup' ) ) {

    function bakerfresh_schema_markup( $location ) {

        echo bakerfresh_get_schema_markup( $location );

    }

}



/**
 * Sanitize HTML output
 * @since 1.0.0
 */

if( !function_exists('bakerfresh_render_variable') ) {
    function bakerfresh_render_variable( $variable ) {
        return $variable;
    }
}

if ( ! function_exists( 'bakerfresh_array_filter_recursive' ) ) {

    function bakerfresh_array_filter_recursive($array, $callback = null, $remove_empty_arrays = true) {
        if(!is_scalar($array)){
            foreach ($array as $key => & $value) { // mind the reference
                if (is_array($value)) {
                    $value = bakerfresh_array_filter_recursive($value, $callback, $remove_empty_arrays);
                    if ($remove_empty_arrays && !(bool) $value) {
                        unset($array[$key]);
                    }
                }
                else {
                    if (!is_null($callback) && !call_user_func($callback, $value, $key)) {
                        unset($array[$key]);
                    }
                    elseif ($value == '' || $key == 'unit') {
                        unset($array[$key]);
                    }
                }
            }
            unset($value); // kill the reference
        }
        return $array;
    }

}

/**
 * @param $content
 * @param bool $autop
 * @return string
 */

if ( ! function_exists( 'bakerfresh_transfer_text_to_format' ) ) {
    function bakerfresh_transfer_text_to_format ( $content, $autop = false ) {
        if ( $autop ) {
            $content = preg_replace( '/<\/?p\>/', "\n", $content );
            $content = preg_replace( '/<p[^>]*><\\/p[^>]*>/', "", $content );
            $content = wpautop( $content . "\n" );
        }
        return do_shortcode( shortcode_unautop( $content ) );
    }
}

/**
 * Comments and pingbacks
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bakerfresh_comment' ) ) {

    function bakerfresh_comment( $comment, $args, $depth ) {

        switch ( $comment->comment_type ) :
            case 'pingback' :
            case 'trackback' :
                // Display trackbacks differently than normal comments.
                ?>

                <div <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

                <div id="comment-<?php comment_ID(); ?>" class="comment-container">
                    <p><?php esc_html_e( 'Pingback:', 'bakerfresh' ); ?> <span><span<?php bakerfresh_schema_markup( 'author_name' ); ?>><?php comment_author_link(); ?></span></span> <?php edit_comment_link( esc_html__( '(Edit)', 'bakerfresh' ), '<span class="edit-link">', '</span>' ); ?></p>
                </div>

                <?php
                break;
            default :
                // Proceed with normal comments.
                global $post;
                ?>

            <div id="comment-<?php comment_ID(); ?>" class="comment-container">

                <div <?php comment_class( 'comment-body' ); ?>>

                    <?php echo get_avatar( $comment, apply_filters( 'bakerfresh_comment_avatar_size', 150 ) ); ?>

                    <div class="comment-content-outer">

                        <div class="comment-author">
                            <h3 class="comment-link"><?php printf( esc_html__( '%s ', 'bakerfresh' ), sprintf( '%s', get_comment_author_link() ) ); ?></h3>
                            <span class="comment-meta commentmetadata">
		                    	<span class="comment-date"><?php comment_date('j M Y'); ?></span>
		                    </span>
                        </div>

                        <div class="comment-entry">
                            <?php if ( '0' == $comment->comment_approved ) : ?>
                                <p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'bakerfresh' ); ?></p>
                            <?php endif; ?>

                            <div class="comment-content">
                                <?php comment_text(); ?>
                            </div>

                        </div>
                        <span class="comment-meta commentmetadata">
                            <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                            <?php edit_comment_link(__('edit', 'bakerfresh' )); ?>
                        </span>
                    </div>

                </div><!-- #comment-## -->

                <?php
                break;
        endswitch; // end comment_type check
    }

}

/**
 * Comment fields
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bakerfresh_modify_comment_form_fields' ) ) {

    function bakerfresh_modify_comment_form_fields( $fields ) {

        $commenter = wp_get_current_commenter();
        $req       = get_option( 'require_name_email' );

        $fields['author'] 	= '<div class="comment-form-author"><input type="text" name="author" id="author" value="'. esc_attr( $commenter['comment_author'] ) .'" placeholder="'. esc_attr__( 'Name (required)', 'bakerfresh' ) .'" size="22" tabindex="101"'. ( $req ? ' aria-required="true"' : '' ) .' class="input-name" /></div>';

        $fields['email'] 	= '<div class="comment-form-email"><input type="text" name="email" id="email" value="'. esc_attr( $commenter['comment_author_email'] ) .'" placeholder="'. esc_attr__( 'Email', 'bakerfresh' ) .'" size="22" tabindex="102"'. ( $req ? ' aria-required="true"' : '' ) .' class="input-email" /></div>';

        $fields['url'] 		= '<div class="comment-form-url"><input type="text" name="url" id="url" value="'. esc_attr( $commenter['comment_author_url'] ) .'" placeholder="'. esc_attr__( 'Website', 'bakerfresh' ) .'" size="22" tabindex="103" class="input-website" /></div>';

        return $fields;

    }

    add_filter( 'comment_form_default_fields', 'bakerfresh_modify_comment_form_fields' );

}

/**
 * String to boolean
 */
if(!function_exists('bakerfresh_string_to_bool')){
    function bakerfresh_string_to_bool( $string ){
        return is_bool( $string ) ? $string : ( 'yes' === $string || 'on' === $string || 1 === $string || 'true' === $string || '1' === $string );
    }
}

if(!function_exists('bakerfresh_entry_meta_item_category_list')){
    function bakerfresh_entry_meta_item_category_list($before = '', $after = '', $separator = ', ', $parents = '', $post_id = false){
        add_filter('get_the_terms', 'bakerfresh_exclude_demo_term_in_category');
        $categories_list = get_the_category_list('{{_}}', $parents, $post_id );
        bakerfresh_deactive_filter('get_the_terms', 'bakerfresh_exclude_demo_term_in_category');
        if ( $categories_list ) {
            printf(
                '%3$s<span class="screen-reader-text">%1$s </span><span>%2$s</span>%4$s',
                esc_html_x('Posted in', 'front-view', 'bakerfresh'),
                str_replace('{{_}}', $separator, $categories_list),
                $before,
                $after
            );
        }
    }
}

if(!function_exists('bakerfresh_exclude_demo_term_in_category')){
    function bakerfresh_exclude_demo_term_in_category( $term ){
        return apply_filters('bakerfresh/post_category_excluded', $term);
    }
}

if(!function_exists('bakerfresh_deactive_filter')){
    function bakerfresh_deactive_filter( $tag, $function_to_remove, $priority = 10) {
        return call_user_func('remove_filter', $tag, $function_to_remove, $priority );
    }
}



/**
 * Store current post ID
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bakerfresh_post_id' ) ) {

    function bakerfresh_post_id() {

        // Default value
        $id = '';

        // If singular get_the_ID
        if ( is_singular() ) {
            $id = get_the_ID();
        }

        // Get ID of WooCommerce product archive
        elseif ( function_exists('is_shop') && is_shop() ) {
            $shop_id = wc_get_page_id( 'shop' );
            if ( isset( $shop_id ) ) {
                $id = $shop_id;
            }
        }

        // Posts page
        elseif ( is_home() && $page_for_posts = get_option( 'page_for_posts' ) ) {
            $id = $page_for_posts;
        }

        // Apply filters
        $id = apply_filters( 'bakerfresh/filter/current_post_id', $id );

        // Sanitize
        $id = $id ? $id : '';

        // Return ID
        return $id;

    }

}



if (!function_exists('bakerfresh_wpml_object_id')) {
    function bakerfresh_wpml_object_id( $element_id, $element_type = 'post', $return_original_if_missing = false, $ulanguage_code = null ) {
        if ( function_exists( 'wpml_object_id_filter' ) ) {
            return wpml_object_id_filter( $element_id, $element_type, $return_original_if_missing, $ulanguage_code );
        }
        elseif ( function_exists( 'icl_object_id' ) ) {
            return icl_object_id( $element_id, $element_type, $return_original_if_missing, $ulanguage_code );
        }
        else {
            return $element_id;
        }
    }
}

if(!function_exists('bakerfresh_is_blog')){
    function bakerfresh_is_blog(){
        return (is_home() || is_tag() || is_category() || is_date() || is_year() || is_month() || is_author()) ? true : false;
    }
}

if(!function_exists('bakerfresh_get_wishlist_url')){
    function bakerfresh_get_wishlist_url(){
        $wishlist_page_id = get_theme_mod('wishlist_page', 0);
        return (!empty($wishlist_page_id) ? get_the_permalink($wishlist_page_id) : esc_url(home_url('/wishlist/')));
    }
}

if(!function_exists('bakerfresh_get_compare_url')){
    function bakerfresh_get_compare_url(){
        $compare_page_id = get_theme_mod('compare_page', 0);
        return (!empty($compare_page_id) ? get_the_permalink($compare_page_id) : esc_url(home_url('/compare/')));
    }
}