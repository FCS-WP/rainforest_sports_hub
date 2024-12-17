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
class LaStudio_Pagespeed {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      LaStudio_Pagespeed_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The request type
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      void    $request    Detected the request type
	 */
	protected $is_method;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'LASTUDIO_PAGESPEED_VERSION' ) ) {
			$this->version = LASTUDIO_PAGESPEED_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'lastudio-pagespeed';

		$this->load_dependencies();
		$this->set_locale();

		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - LaStudio_Pagespeed_Loader. Orchestrates the hooks of the plugin.
	 * - LaStudio_Pagespeed_i18n. Defines internationalization functionality.
	 * - LaStudio_Pagespeed_Admin. Defines all hooks for the admin area.
	 * - LaStudio_Pagespeed_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lastudio-pagespeed-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lastudio-pagespeed-i18n.php';

		/**
		 * The class responsible for looking for global method
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lastudio-pagespeed-is-method.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-lastudio-pagespeed-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-lastudio-pagespeed-public.php';

		/**
		 * Updater
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lastudio-pagespeed-update.php';

		$this->loader = new LaStudio_Pagespeed_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the LaStudio_Pagespeed_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new LaStudio_Pagespeed_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

//		$plugin_admin = new LaStudio_Pagespeed_Admin( $this->get_plugin_name(), $this->get_version() );
//
//		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
//		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

        $this->loader->add_action( 'plugins_loaded', $this, 'plugin_loaded_hooks', PHP_INT_MAX );

		$plugin_public = new LaStudio_Pagespeed_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action('LaStudio_PageSpeed/filter/frontend_html_output', $plugin_public, 'frontend_rewrite');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    LaStudio_Pagespeed_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    private function define_plugin_loaded_hooks(){
        if (defined('LSCWP_V') && version_compare(constant('LSCWP_V'), '3.2.3.1 ', '>=')) {
            add_filter('litespeed_buffer_after', [$this, 'frontend_rewrite'], PHP_INT_MAX);
        }
        elseif ( defined('WP_ROCKET_VERSION') ) {
            if(!defined('SiteGround_Optimizer\VERSION')){
                add_filter('rocket_buffer', [$this, 'frontend_rewrite'], PHP_INT_MAX);
            }
            else{
                add_action('init', [$this, 'buffer_start'], -3);
            }
            add_filter('rocket_exclude_js', [$this, 'rocket_exclude_js'], PHP_INT_MAX);
        }
        elseif ( defined('W3TC') ) {
            add_action('init', [$this, 'w3tc_ob_callback'], 1001);
        }
        elseif (class_exists('Swift_Performance') && @$_SERVER['HTTP_X_PREBUILD']) {
            // We disable async-scripts options because it tries to override (document|window).(add|remove)EventListener
            // We anyway optimize loading scripts
            add_filter('swift_performance_option_async-scripts', [$this, 'return_false'], PHP_INT_MAX);
            add_action('swift_performance_buffer', [$this, 'swift_performance_rewrite'], PHP_INT_MAX);
        }
        else {
            add_action('init', [$this, 'buffer_start'], -10);
        }
    }


    public function exclude_js_array($excluded_js) {
        // regexps !
        return array_merge((array) $excluded_js, [
            'LaStudioPageSpeedConfigs=',
            'LaStudioPageSpeedConfigs\.',
            // 'nolastudiopagespeed=1',
        ]);
    }

    public function exclude_js_string($excluded_js) {
        if (is_array($excluded_js)) {
            return $this->exclude_js_array($excluded_js);
        }
        // strings !
        return $excluded_js . "," . join(',', [
                'LaStudioPageSpeedConfigs=',
                'LaStudioPageSpeedConfigs.',
                // 'nolastudiopagespeed=1',
            ]) . ",";
    }

    public function plugin_loaded_hooks(){
        $this->define_plugin_loaded_hooks();
    }

	protected function check_can_rewrite(){

		$canRewrite = true;

		/** don't run if is logged user */
        if(is_user_logged_in()){
            return false;
        }

		if(isset($_GET['nolastudiopagespeed'])){
			return false;
		}
		/**
		 * Divi builder detected
		 */
		if (!empty($_GET['et_fb']) || ( isset($_GET['PageSpeed']) && $_GET['PageSpeed'] === 'off' )) {
			return false;
		}

		if(defined('NITROPACK_VERSION')){
			return false;
		}

		if (defined('PHASTPRESS_VERSION')) {
			$phastpress_settings = @json_decode(get_option('phastpress2-settings'), true);
			if ($phastpress_settings && @$phastpress_settings['scripts-defer']) {
				return false;
			}
		}

		if (defined('WP_ROCKET_VERSION')) {
			$wp_rocket_settings = get_option('wp_rocket_settings');
			if (@$wp_rocket_settings['delay_js']) {
				return false;
			}
		}

		$request = new LaStudio_Pagespeed_Is_Method();

		if (!$request->is_frontend()) {
			$canRewrite = false;
		}

		if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
			$canRewrite = false;
		}

		if (function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()) {
			$canRewrite = false;
		}

		if (class_exists('Elementor\Plugin') && ( Elementor\Plugin::instance()->editor->is_edit_mode() || Elementor\Plugin::instance()->preview->is_preview_mode() || isset($_GET['render_mode']) ) ) {
			$canRewrite = false;
		}

		if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_active()) {
			$canRewrite = false;
		}

		if (function_exists('vc_is_inline') && vc_is_inline()) {
			$canRewrite = false;
		}

		if (class_exists('Fusion_App') && ($instance = \Fusion_App::get_instance()) && $instance->is_builder) {
			$canRewrite = false;
		}

		if( isset($_GET['SuperSocializerAuth']) ){
			$canRewrite = false;
		}

		return apply_filters('LaStudio_Pagespeed/filter/check_can_rewrite', $canRewrite);

	}

	public function buffer_start(){
		ob_start( [$this, 'frontend_rewrite'] );
	}

	public function w3tc_ob_callback(){
		if(isset($GLOBALS[ '_w3tc_ob_callbacks' ][ 'minify' ])){
			// store original minify callback
			$pagecache_bk = $GLOBALS[ '_w3tc_ob_callbacks' ][ 'minify' ];
			$GLOBALS[ '_w3tc_ob_callbacks' ][ 'minify' ] = function ( $buffer ) use ( $pagecache_bk ) {
				// run original minify callback
				$buffer = call_user_func( $pagecache_bk, $buffer );
				$buffer = $this->frontend_rewrite($buffer);
				return $buffer;
			};
		}
		else{
			$this->buffer_start();
		}
	}

	public function frontend_rewrite( $output ){
		if(!$this->check_can_rewrite()){
			return $output;
		}

        if(false !== strpos( $output, '<?xml' )){
            return $output;
        }

		$output = apply_filters('LaStudio_PageSpeed/filter/frontend_html_output', $output);
		$output = preg_replace('/<\/html>/i', "</html>\n<!-- Optimized with LaStudio PageSpeed https://la-studioweb.com/ -->", $output);

		return $output;
	}

    public function swift_performance_rewrite($buffer) {
        static $calledOnce = false;
        // !!!! swift_performance_buffer gets called twice !!!
        if (!$calledOnce) {
            $calledOnce = true;
            return $buffer;
        }
        return $this->rewrite($buffer);
    }

    public function return_false(){
	    return false;
    }

    public function rocket_exclude_js( $rocket_exclude_js ){
        if( ($key = array_search('/wp-includes/js/dist/i18n.min.js', $rocket_exclude_js)) !== false){
            unset($rocket_exclude_js[$key]);
        }
        return $rocket_exclude_js;
    }

}
