<?php
/*
Plugin Name: ZippySG Core
Plugin URI: https://zippy.sg/
Description: Support change default URL Admin, provide Advanced Analytics Woocommrece, Remove thirt party default of Wordpress, Setting SMTP Mail Server, Optime Wordpress Core...
Version: 4.0 Author: Zippy SG
Author URI: https://zippy.sg/
License: GNU General Public
License v3.0 License
URI: https://zippy.sg/
Domain Path: /languages

Copyright 2024

*/

namespace Zippy_Core;


defined('ABSPATH') or die('°_°’');

/* ------------------------------------------
 // Constants
 ------------------------------------------------------------------------ */
/* Set plugin version constant. */

if (!defined('ZIPPY_CORE_VERSION')) {
	define('ZIPPY_CORE_VERSION', '4.0');
}

/* Set plugin name. */

if (!defined('ZIPPY_CORE_NAME')) {
	define('ZIPPY_CORE_NAME', 'ZippySG Core');
}

if (!defined('ZIPPY_CORE_PREFIX')) {
	define('ZIPPY_CORE_PREFIX', 'zippysg_core');
}

if (!defined('ZIPPY_CORE_BASENAME')) {
	define('ZIPPY_CORE_BASENAME', plugin_basename(__FILE__));
}

/* Set constant path to the plugin directory. */

if (!defined('ZIPPY_CORE_DIR_PATH')) {
	define('ZIPPY_CORE_DIR_PATH', plugin_dir_path(__FILE__));
}

/* Set constant url to the plugin directory. */

if (!defined('ZIPPY_CORE_URL')) {
	define('ZIPPY_CORE_URL', plugin_dir_url(__FILE__));
}

define( 'MY_PLUGIN_SLUG', 'my-plugin-slug' );

/* ------------------------------------------
// i18n
---------------------------- --------------------------------------------- */

load_plugin_textdomain('zippy-sg-core', false, basename(dirname(__FILE__)) . '/languages');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


/* ------------------------------------------
// Includes
 --------------------------- --------------------------------------------- */
require ZIPPY_CORE_DIR_PATH . '/includes/autoload.php';
require ZIPPY_CORE_DIR_PATH . '/vendor/autoload.php';


use	Zippy_Core\Src\Admin\Zippy_Admin_Setting;

use	Zippy_Core\Src\Admin\Zippy_Admin_Url;

use Zippy_Core\Src\Core\Zippy_Core;

use Zippy_Core\Src\User\Zippy_MPDA_Consent;

use Zippy_Core\Src\User\Zippy_User_Account_Expiry;

use Zippy_Core\Src\Analytics\Zippy_Analytics;

use Zippy_Core\Src\Woocommerce\Zippy_Woocommerce;

use Zippy_Core\Src\Woocommerce\Zippy_Postal_code;


/**
 *
 * Init Zippy Core
 */

Zippy_Admin_Setting::get_instance();

Zippy_Core::get_instance();

Zippy_Admin_Url::get_instance();

Zippy_MPDA_Consent::get_instance();

Zippy_User_Account_Expiry::get_instance();

Zippy_Analytics::get_instance();

Zippy_Woocommerce::get_instance();

Zippy_Postal_code::get_instance();
