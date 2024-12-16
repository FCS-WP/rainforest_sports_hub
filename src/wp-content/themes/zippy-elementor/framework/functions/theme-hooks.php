<?php
/**
 * This file includes helper functions used throughout the theme.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * ACTIONS
 */
add_action( 'wp_body_open',     'bakerfresh_add_pageloader_icon', 1);
/**
 * FILTERS
 */
add_filter( 'body_class',       'bakerfresh_body_classes' );
add_filter( 'excerpt_length',   'bakerfresh_change_excerpt_length');
