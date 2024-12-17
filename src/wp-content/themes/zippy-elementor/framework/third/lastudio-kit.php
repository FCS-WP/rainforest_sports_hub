<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_filter('lastudio-kit/branding/name', 'bakerfresh_lakit_branding_name');
if(!function_exists('bakerfresh_lakit_branding_name')){
    function bakerfresh_lakit_branding_name( $name ){
        $name = esc_html__('Theme Options', 'bakerfresh');
        return $name;
    }
}

add_filter('lastudio-kit/branding/logo', 'bakerfresh_lakit_branding_logo');
if(!function_exists('bakerfresh_lakit_branding_logo')){
    function bakerfresh_lakit_branding_logo( $logo ){
        $logo = '';
        return $logo;
    }
}

add_filter('lastudio-kit/logo/attr/src', 'bakerfresh_lakit_logo_attr_src');
if(!function_exists('bakerfresh_lakit_logo_attr_src')){
    function bakerfresh_lakit_logo_attr_src( $src ){
        if(!$src){
	        $src = get_theme_mod('logo_default', get_theme_file_uri('/assets/images/logo.svg'));
        }
        return $src;
    }
}

add_filter('lastudio-kit/logo/attr/src2x', 'bakerfresh_lakit_logo_attr_src2x');
if(!function_exists('bakerfresh_lakit_logo_attr_src2x')){
    function bakerfresh_lakit_logo_attr_src2x( $src ){
        if(!$src){
	        $src = get_theme_mod('logo_transparency', '');
        }
        return $src;
    }
}

add_filter('lastudio-kit/logo/attr/width', 'bakerfresh_lakit_logo_attr_width');
if(!function_exists('bakerfresh_lakit_logo_attr_width')){
    function bakerfresh_lakit_logo_attr_width( $value ){
        if(!$value){
            $value = 229;
        }
        return $value;
    }
}

add_filter('lastudio-kit/logo/attr/height', 'bakerfresh_lakit_logo_attr_height');
if(!function_exists('bakerfresh_lakit_logo_attr_height')){
    function bakerfresh_lakit_logo_attr_height( $value ){
        if(!$value){
            $value = 62;
        }
        return $value;
    }
}

add_action('elementor/frontend/widget/before_render', 'bakerfresh_lakit_add_class_into_sidebar_widget');
if(!function_exists('bakerfresh_lakit_add_class_into_sidebar_widget')){
    function bakerfresh_lakit_add_class_into_sidebar_widget( $widget ){
        if('sidebar' == $widget->get_name()){
            $widget->add_render_attribute('_wrapper', 'class' , 'widget-area');
        }

    }
}

add_filter('lastudio-kit/products/control/grid_style', 'bakerfresh_lakit_add_product_grid_style');
if(!function_exists('bakerfresh_lakit_add_product_grid_style')){
    function bakerfresh_lakit_add_product_grid_style(){
        return [
            '1' => esc_html__('Type 1', 'bakerfresh'),
            '2' => esc_html__('Type 2', 'bakerfresh'),
            '3' => esc_html__('Type 3', 'bakerfresh'),
            '4' => esc_html__('Type 4', 'bakerfresh'),
            '5' => esc_html__('Type 5', 'bakerfresh'),
            '6' => esc_html__('Type 6', 'bakerfresh'),
            '7' => esc_html__('Type 7', 'bakerfresh'),
            '8' => esc_html__('Type 8', 'bakerfresh'),
        ];
    }
}
add_filter('lastudio-kit/products/control/list_style', 'bakerfresh_lakit_add_product_list_style');
if(!function_exists('bakerfresh_lakit_add_product_list_style')){
    function bakerfresh_lakit_add_product_list_style(){
        return [
            '1' => esc_html__('Type 1', 'bakerfresh'),
            'mini' => esc_html__('Mini', 'bakerfresh'),
        ];
    }
}

add_filter('lastudio-kit/products/box_selector', 'bakerfresh_lakit_product_change_box_selector');
if(!function_exists('bakerfresh_lakit_product_change_box_selector')){
    function bakerfresh_lakit_product_change_box_selector(){
        return '{{WRAPPER}} ul.products .product_item--inner';
    }
}

add_filter('lastudio-kit/posts/format-icon', 'bakerfreh_lakit_change_postformat_icon', 10, 2);
if(!function_exists('bakerfreh_lakit_change_postformat_icon')){
    function bakerfreh_lakit_change_postformat_icon( $icon, $type ){
        return $icon;
    }
}

/**
 * Modify Divider - Weight control
 */
add_action('elementor/element/lakit-portfolio/section_settings/before_section_end', function( $element ){
	$element->add_control(
		'enable_portfolio_lightbox',
		[
			'label'     => esc_html__( 'Enable Lightbox', 'lastudio-kit' ),
			'type'      => \Elementor\Controls_Manager::SWITCHER,
			'label_on'  => esc_html__( 'Yes', 'lastudio-kit' ),
			'label_off' => esc_html__( 'No', 'lastudio-kit' ),
			'default'   => '',
			'return_value' => 'enable-pf-lightbox',
			'prefix_class' => '',
		]
	);
}, 10 );

add_filter('pre_set_transient_wc_system_status_theme_info', function ($value, $transient){
	$value['has_outdated_templates'] = '';
	if( !empty($value['overrides']) ){
		$theme_name = wp_get_theme()->get_template() . '/';
		foreach ( $value['overrides'] as &$template ){
			if(str_starts_with($template['file'], $theme_name)){
				$template['version'] = $template['core_version'];
			}
		}
	}
	return $value;
}, 1000, 2);
add_filter('woocommerce_show_admin_notice', function ($show, $notice){
	if($notice === 'template_files'){
		$show = false;
	}
	return $show;
}, 1000, 2);