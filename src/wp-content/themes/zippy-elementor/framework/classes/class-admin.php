<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

class Bakerfresh_Admin {

    public function __construct(){
        $this->load_config();
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts') );
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_scripts') );
        add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
    }

    private function load_config(){
        require_once get_theme_file_path('/framework/configs/options.php');
        require_once get_theme_file_path('/framework/configs/metaboxes.php');
    }

    public function admin_scripts( $hook ){
        $ext = defined('WP_DEBUG') && WP_DEBUG ? '' : '.min';
        wp_enqueue_style('bakerfresh-admin-css', get_theme_file_uri( '/assets/css/admin'.$ext.'.css' ));
        wp_enqueue_script('bakerfresh-admin-theme', get_theme_file_uri( '/assets/js/admin'.$ext.'.js' ), array( 'jquery'), false, true );

        if(!class_exists('LASF', false)) {
            wp_enqueue_style( 'bakerfresh-fonts', Bakerfresh_Theme_Class::enqueue_google_fonts_url() , array(), null );
        }

        $body_font_family = get_theme_mod('body_font_family');
        if(!empty($body_font_family)){
            wp_add_inline_style('bakerfresh-admin-css', '.block-editor .editor-styles-wrapper .editor-block-list__block{ font-family: '.$body_font_family.' }');
        }

        wp_localize_script( 'bakerfresh-admin-theme', 'bakerfresh_admin_vars', array(
            'ajaxurl'       => admin_url( 'admin-ajax.php' ),
            'admin_load_nonce' => wp_create_nonce( 'admin_load_nonce' ),
            'widget_info'   => sprintf( '<div id="la_pb_widget_area_create"><p>%1$s.</p><p><label>%2$s <input id="la_pb_new_widget_area_name" value="" /></label><button class="button button-primary la_pb_create_widget_area">%3$s</button></p><p class="la_pb_widget_area_result"></p></div>',
                esc_html__( 'Here you can create new widget areas for use in the Sidebar module', 'bakerfresh' ),
                esc_html__( 'Widget Name', 'bakerfresh' ),
                esc_html__( 'Create', 'bakerfresh' )
            ),
            'confirm_delete_string' => esc_html__( 'Are you sure?', 'bakerfresh' ),
            'delete_string' => esc_html__( 'Delete', 'bakerfresh' ),
            'edit_post_link' => admin_url('post.php?post={post_id}&action=elementor')
        ) );
    }

    public function customize_scripts(){
        $theme_version = defined('WP_DEBUG') && WP_DEBUG ? time() : BAKERFRESH_THEME_VERSION;
        $dependency = array(
            'jquery',
            'customize-base',
            'customize-controls',
        );
        wp_enqueue_script( 'bakerfresh-customize-admin', get_theme_file_uri('/assets/js/customizer.js'), $dependency, $theme_version, true );
    }

    public function customize_preview_init(){
        $theme_version = defined('WP_DEBUG') && WP_DEBUG ? time() : BAKERFRESH_THEME_VERSION;
        $dependency = array(
            'jquery',
            'customize-preview',
        );
        wp_enqueue_script('bakerfresh-customize-preview', get_theme_file_uri( '/assets/js/customizer-preview.js' ), $dependency, $theme_version, true);
    }

}