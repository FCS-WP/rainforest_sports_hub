<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://la-studioweb.com/
 * @since      1.0.0
 *
 * @package    LaStudio_Pagespeed
 * @subpackage LaStudio_Pagespeed/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    LaStudio_Pagespeed
 * @subpackage LaStudio_Pagespeed/public
 * @author     LA-Studio <info@la-studioweb.com>
 */
class LaStudio_Pagespeed_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in LaStudio_Pagespeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The LaStudio_Pagespeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lastudio-pagespeed-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in LaStudio_Pagespeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The LaStudio_Pagespeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lastudio-pagespeed-public.js', array( 'jquery' ), $this->version, false );

	}

	public function get_default_attributes(){
		$attributes = [];
		$attributes[] = 'data-no-optimize="1"';
        $atts = join(' ', $attributes);
        if(!empty($atts)){
            $atts = ' ' . $atts;
        }
        return $atts;
	}

	public function get_extra_attributes(){
		$attributes = [];
		/* does not optimize script tag if server using cloudflare */
		if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$attributes[] = 'data-cfasync="false"';
		}
		$atts = join(' ', $attributes);
		if(!empty($atts)){
            $atts = ' ' . $atts;
        }
		return $atts;
	}

	protected function get_lazyload_script_source(){
		$source = '';
		return $source;
	}

	protected function get_primary_script_source(){
		$content = file_get_contents(plugin_dir_path( __FILE__ ) . 'js/lastudio-pagespeed-public.js');

		$config = [
			'delay' => 15000,
			'version' => $this->version,
			'nativelazyoad' => true,
			'e_animation' => class_exists('Elementor\Plugin') ? true : false,
			'detected' => true
		];

		$extra = 'var LaStudioPageSpeedConfigs=' . json_encode($config) . ';';
		$content = $extra . $content;

		$source = sprintf(
			'<script%1$s%2$s>%3$s</script>',
			$this->get_default_attributes(),
			$this->get_extra_attributes(),
			$content
		);
		return $source;
	}

	public function frontend_rewrite( $buffer ){

        preg_match('/<head.*\b>/', $buffer, $found_head_tag);

        if(empty($found_head_tag)){
            return $buffer;
        }

        /* Start including snippet */

        $main_script = $this->get_primary_script_source();
        $buffer = preg_replace('/<\/body>/i', $main_script . '</body>', $buffer);
        /* End including snippet */

        $REPLACEMENTS = [];
        $searchOffset = 0;
        while (preg_match('/<script\b[^>]*?>/is', $buffer, $matches, PREG_OFFSET_CAPTURE, $searchOffset)) {
            $offset = $matches[0][1];
            $searchOffset = $offset + 1;
            if (preg_match('/<\/\s*script>/is', $buffer, $endMatches, PREG_OFFSET_CAPTURE, $matches[0][1])) {
                $len = $endMatches[0][1] - $matches[0][1] + strlen($endMatches[0][0]);
                $everything = substr($buffer, $matches[0][1], $len);
                $tag = $matches[0][0];
                $closingTag = $endMatches[0][0];

                $hasSrc = preg_match('/\s+src=/i', $tag);
                $hasType = preg_match('/\s+type=/i', $tag);
                $shouldReplace = !$hasType || preg_match('/\s+type=([\'"])((application|text)\/(javascript|ecmascript|html|template)|module)\1/i', $tag);
                $noOptimize = preg_match('/data-no-optimize/i', $tag);
                if ($shouldReplace && !$hasSrc && !$noOptimize) {
                    // inline script
                    $content = substr($buffer, $matches[0][1] + strlen($matches[0][0]), $endMatches[0][1] - $matches[0][1] - strlen($matches[0][0]));
                    if (apply_filters('LaStudio_Pagespeed/filter/exclude_js', false, $content)) {
                        $replacement = preg_replace('/^<script\b/i', '<script data-no-optimize="1"', $everything);
                        $buffer = substr_replace($buffer, $replacement, $offset, $len);
                        continue;
                    }
                    $replacement = $tag . "LASTUDIOPAGESPEED[" . count($REPLACEMENTS) . "]LASTUDIOPAGESPEED" . $closingTag;
                    $REPLACEMENTS[] = $content;
                    $buffer = substr_replace($buffer, $replacement, $offset, $len);
                    continue;
                }
            }
        }

        $buffer = preg_replace_callback('/<script\b[^>]*?>/i', function ($matches) {
            list($tag) = $matches;

            $EXTRA = $this->get_extra_attributes();

            $result = $tag;
            if (!preg_match('/\s+data-src=/i', $result)
                && !preg_match('/(data-no-optimize="(true|yes|1)")/i', $result)
                && !preg_match('/data-rocketlazyloadscript=/i', $result)) {

                $src = preg_match('/\s+src=([\'"])(.*?)\1/i', $result, $matches)
                    ? $matches[2]
                    : null;
                if (!$src) {
                    // trying to detect src without quotes
                    $src = preg_match('/\s+src=([\/\w\-\.\~\:\[\]\@\!\$\?\&\#\(\)\*\+\,\;\=\%]+)/i', $result, $matches)
                        ? $matches[1]
                        : null;
                }
                $hasType = preg_match('/\s+type=/i', $result);
                $isJavascript = !$hasType
                    || preg_match('/\s+type=([\'"])((application|text)\/(javascript|ecmascript)|module)\1/i', $result)
                    || preg_match('/\s+type=((application|text)\/(javascript|ecmascript)|module)/i', $result);
                if ($isJavascript) {
                    if ($src) {
                        if (apply_filters('LaStudio_Pagespeed/filter/exclude_js', false, $src)) {
                            return $result;
                        }
                        $result = preg_replace('/\s+src=/i', " data-src=", $result);
//                        $result = preg_replace('/\s+async\b/i', " data-async", $result);
                    }
                    if ($hasType) {
                        $result = preg_replace('/\s+type=([\'"])module\1/i', " type=\"javascript/blocked\" data-laps-module ", $result);
                        $result = preg_replace('/\s+type=module\b/i', " type=\"javascript/blocked\" data-laps-module ", $result);
                        $result = preg_replace('/\s+type=([\'"])(application|text)\/(javascript|ecmascript)\1/i', " type=\"javascript/blocked\"", $result);
                        $result = preg_replace('/\s+type=(application|text)\/(javascript|ecmascript)\b/i', " type=\"javascript/blocked\"", $result);
                    } else {
                        $result = preg_replace('/<script/i', "<script type=\"javascript/blocked\"", $result);
                    }
                    $result = preg_replace('/<script/i', "<script {$EXTRA}data-laps-action=\"reorder\"", $result);
                }
            }
            return preg_replace('/\s*(data-no-optimize="(true|yes|1)")/i', '', $result);
        }, $buffer);

        $buffer = preg_replace_callback('/LASTUDIOPAGESPEED\[(\d+)\]LASTUDIOPAGESPEED/', function ($matches) use (&$REPLACEMENTS) {
            return $REPLACEMENTS[(int)$matches[1]];
        }, $buffer);

        $buffer = str_replace('<html', '<html data-ps="yes"', $buffer);

        return $buffer;
    }

}
