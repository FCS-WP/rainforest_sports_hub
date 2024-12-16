<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$header_nav_menu = wp_nav_menu( [
    'theme_location' => 'main-nav',
    'echo' => false,
] );
?>
<header id="site-header" class="site-header site-header--default" role="banner">
    <div class="container">
        <div class="site-branding">
            <?php
            if ( has_custom_logo() ) {
                the_custom_logo();
            }
            else {
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Home', 'bakerfresh' ); ?>" rel="home">
                    <img src="<?php echo esc_url(get_theme_mod('logo_default', get_theme_file_uri('/assets/images/logo.svg'))) ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" width="229" height="62"/>
                </a>
            <?php } ?>
        </div>

        <nav class="site-navigation" role="navigation">
            <button type="button" class="site-nav-toggleicon"><i class="lastudioicon-menu-8-1"></i></button>
            <?php
            wp_nav_menu( [
                'theme_location' => 'main-nav',
                'container'      => false,
            ] );
            ?>
        </nav>
    </div>
</header>