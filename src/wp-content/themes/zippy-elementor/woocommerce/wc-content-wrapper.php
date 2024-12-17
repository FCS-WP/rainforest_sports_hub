<?php
/**
 * After Container template.
 *
 * @package Astrids WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(is_product()){
    $page_title = single_post_title('', false);
}
else{
    $page_title = woocommerce_page_title(false);
}
?>

<main <?php post_class('site-main'); ?> role="main">
    <?php if (apply_filters('bakerfresh/filter/enable_page_title', true) && !empty($page_title)) : ?>
        <header class="page-header page-header--default">
            <div class="container page-header-inner">
                <?php
                echo sprintf('<h1 class="entry-title">%1$s</h1>', esc_html($page_title));
                ?>
            </div>
        </header>
    <?php endif; ?>

    <div id="site-content-wrap" class="container">

        <?php get_sidebar(); ?>

        <div class="site-content--default">

            <div class="page-content wc-page-content<?php echo is_product() ? ' single-product-article': '' ?>">