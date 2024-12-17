<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://la-studioweb.com/
 * @since      1.0.0
 *
 * @package    LaStudio_Pagespeed
 * @subpackage LaStudio_Pagespeed/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    LaStudio_Pagespeed
 * @subpackage LaStudio_Pagespeed/includes
 * @author     LA-Studio <info@la-studioweb.com>
 */
class LaStudio_Pagespeed_Is_Method {

	public function __construct() {
	}

	/**
	 * What type of request is this?
	 *
	 * @since 1.0.0
	 * @param  string $type admin, ajax, cron, cli, amp or frontend.
	 * @return bool
	 */
	public function request( string $type ) {
		switch ( $type ) {
			case 'backend':
				return $this->is_admin_backend();

			case 'ajax':
				return $this->is_ajax();

			case 'installing_wp':
				return $this->is_installing_wp();

			case 'rest':
				return $this->is_rest();

			case 'cron':
				return $this->is_cron();

			case 'frontend':
				return $this->is_frontend();

			case 'cli':
				return $this->is_cli();

			case 'amp':
				return $this->is_amp();

			default:
				_doing_it_wrong( __METHOD__, esc_html( sprintf( __('Unknown request type: %s', 'lastudio-pagespeed'), $type ) ), '1.0.0' );

				return false;
		}
	}

	/**
	 * Is installing WP
	 *
	 * @return bool
	 */
	public function is_installing_wp() {
		return defined( 'WP_INSTALLING' );
	}

	/**
	 * Is admin
	 *
	 * @return bool
	 */
	public function is_admin_backend() {
		return is_user_logged_in() && is_admin();
	}

	/**
	 * Is ajax
	 *
	 * @return bool
	 */
	public function is_ajax() {
		return ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || defined( 'DOING_AJAX' );
	}

	/**
	 * Is rest
	 *
	 * @return bool
	 */
	public function is_rest() {
		if (defined('REST_REQUEST')) {
			return true;
		}
		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		return  isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], $rest_prefix) !== false;
	}

	/**
	 * Is cron
	 *
	 * @return bool
	 */
	public function is_cron() {
		return ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) || defined( 'DOING_CRON' );
	}

	/**
	 * Is frontend
	 *
	 * @return bool
	 */
	public function is_frontend() {
		return ( ! $this->is_admin_backend() && ! $this->is_ajax() ) && ! $this->is_cron() && ! $this->is_rest();
	}

	/**
	 * Is cli
	 *
	 * @return bool
	 */
	public function is_cli() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Is AMP
	 *
	 * @return bool
	 */
	public function is_amp() {
		return ((function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) || (function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()));
	}

	/**
	 * Whether given user is an administrator.
	 *
	 * @param WP_User|null $user The given user.
	 * @return bool
	 */
	public static function is_user_admin( WP_User $user = null ) { //phpcs:ignore
		if ( is_null( $user ) ) {
			$user = wp_get_current_user();
		}

		if ( ! $user instanceof WP_User ) {
			_doing_it_wrong( __METHOD__, 'To check if the user is admin is required a WP_User object.', '1.0.0' );
		}

		return is_multisite() ? user_can( $user, 'manage_network' ) : user_can( $user, 'manage_options' );
	}
}
