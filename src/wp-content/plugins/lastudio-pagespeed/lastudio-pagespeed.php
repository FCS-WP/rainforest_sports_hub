<?php
/**
 *
 * @link              https://la-studioweb.com/
 * @since             1.0.0
 * @package           LaStudio_Pagespeed
 *
 * @wordpress-plugin
 * Plugin Name:       LA-Studio PageSpeed
 * Plugin URI:        https://la-studioweb.com/plugins/lastudio-pagespeed/
 * Description:       LA-Studio PageSpeed eliminate render-blocking Javascript. This gives 2x-5x increase in page load speed, as well as in relevant Google page speed metrics. And this plugin improves your page speed, even on top of your existing optimizations
 * Version:           1.1.1
 * Requires at least: 5.0
 * Tested up to:      6.3
 * Requires PHP:      7.4
 * Author:            LA-Studio
 * Author URI:        https://la-studioweb.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lastudio-pagespeed
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LASTUDIO_PAGESPEED_VERSION', '1.1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lastudio-pagespeed-activator.php
 */
function activate_lastudio_pagespeed() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lastudio-pagespeed-activator.php';
	LaStudio_Pagespeed_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lastudio-pagespeed-deactivator.php
 */
function deactivate_lastudio_pagespeed() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lastudio-pagespeed-deactivator.php';
	LaStudio_Pagespeed_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lastudio_pagespeed' );
register_deactivation_hook( __FILE__, 'deactivate_lastudio_pagespeed' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lastudio-pagespeed.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lastudio_pagespeed() {

	$plugin = new LaStudio_Pagespeed();
	$plugin->run();

}
run_lastudio_pagespeed();