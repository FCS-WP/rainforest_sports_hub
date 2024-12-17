<?php 

function custom_sidebar_init() {
    register_sidebar(array(
        'name'          => __('Custom Sidebar', 'text_domain'),
        'id'            => 'custom-sidebar', 
        'description'   => __('A custom sidebar for widgets.', 'text_domain'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'custom_sidebar_init');