<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://la-studioweb.com/
 * @since      1.0.0
 *
 * @package    LaStudio_Pagespeed
 * @subpackage LaStudio_Pagespeed/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    LaStudio_Pagespeed
 * @subpackage LaStudio_Pagespeed/includes
 * @author     LA-Studio <info@la-studioweb.com>
 */
class LaStudio_Pagespeed_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'lastudio-pagespeed',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
