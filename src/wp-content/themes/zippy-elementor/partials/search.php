<?php
/**
 * The template for displaying search results.
 *
 * @package Bakerfresh
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<main class="site-main" role="main">

    <?php if (apply_filters('bakerfresh/filter/enable_page_title', true)) : ?>
        <header class="page-header page-header--default">
            <div class="container page-header-inner">
                <h1 class="entry-title">
                    <?php esc_html_e('Search results for: ', 'bakerfresh'); ?>
                    <span><?php echo get_search_query(); ?></span>
                </h1>
            </div>
        </header>
    <?php endif; ?>


    <div id="site-content-wrap" class="container">
        <?php get_sidebar(); ?>
        <div class="site-content--default">
            <?php

            if ( have_posts() ) {

                echo '<div id="blog-entries">';

                // Loop through posts
                while ( have_posts() ) {

                    the_post();

                    get_template_part( 'partials/default/content', get_post_type() );

                }

                echo '</div>';

                // Display post pagination
                the_posts_pagination();

                wp_reset_postdata();

            }
            else{
                get_template_part( 'partials/default/none');
            }

            ?>
        </div>
    </div>

</main>
