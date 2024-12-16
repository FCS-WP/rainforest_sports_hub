<?php

/*
 * Define Variables
 */
if (!defined('THEME_DIR'))
    define('THEME_DIR', get_template_directory());
if (!defined('THEME_URL'))
    define('THEME_URL', get_template_directory_uri());


/*
 * Include framework files
 */
foreach (glob(THEME_DIR.'-child' . "/includes/*.php") as $file_name) {
    require_once ( $file_name );
}

function show_random_slide() {
    $randomNumber = rand(1, 3); 
    $shortcode = '[smartslider3 slider="' . $randomNumber . '"]'; 
    return do_shortcode($shortcode);
}
add_shortcode('random_slide', 'show_random_slide');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);




