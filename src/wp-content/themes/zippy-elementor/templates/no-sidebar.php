<?php
/**
 * Template Name: Bakerfresh No Sidebar
 * Template Post Type: post, page, product
 *
 * This is the most generic template file in a WordPress theme and one of the
 * two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Bakerfresh WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_header();


if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
    while (have_posts()) :
        the_post();
        ?>

        <main <?php post_class('site-main'); ?> role="main">
            <?php if (apply_filters('bakerfresh/filter/enable_page_title', true)) : ?>
                <header class="page-header page-header--default">
                    <div class="container page-header-inner">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    </div>
                </header>
            <?php endif; ?>

            <div id="site-content-wrap" class="container">

                <div class="site-content--default">

                    <div class="page-content">

                        <?php

                        the_content();

                        if(is_singular('post')){
                            the_tags('<div class="post-tags"><span class="tag-links">' . __('Tagged ', 'bakerfresh'), null, '</span></div>');
                        }

                        wp_link_pages( array(
                            'before' => '<div class="clearfix"></div><div class="page-links">' . esc_html__( 'Pages:', 'bakerfresh' ),
                            'after'  => '</div>',
                        ) );
                        ?>

                    </div>

                    <?php

                    wp_reset_postdata();

                    if(comments_open() || get_comments_number()){
                        comments_template();
                    }
                    ?>

                </div>
            </div>

        </main>

    <?php
    endwhile;
}

get_footer();