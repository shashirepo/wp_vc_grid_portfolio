<?php
/*
Plugin Name: VC Full Screen Portfolio
Plugin URI: https://openlearning.com
Description: An Amazing VC extension - FullScreen expanding portfolio to showcase your work in awesome way.
Version: 1.0
Author: Shashikant Vaishnav
Author URI: http://www.shashitechno.com
License: GPL2
/*

/*
Copyright 2014 Shashikant Vaishnav (email : shashikantvaishnaw@gmail.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (!is_plugin_active('js_composer/js_composer.php')) {
    return false;
}

add_action( 'plugins_loaded', 'vc_fp_start', 5 );
/**
 * Initialize the Better Font Awesome plugin.
 *
 * Start up Better Font Awesome early on the plugins_loaded hook, priority 5, in
 * order to load it before any other plugins that might also use the Better Font
 * Awesome Library.
 *
 * @since  0.9.5
 */
function vc_fp_start() {
    global $better_font_awesome;
    $better_font_awesome = VC_FPortfolio::get_instance();
}

Class VC_FPortfolio {

/**
* Plugin slug.
*
* @since  0.9.0
*
* @var    string
*/
const SLUG = 'vc-fportfolio';



    /**
     * Plugin display name.
     *
     * @since  0.9.0
     *
     * @var    string
     */
    private $plugin_display_name;

    /**
     * Plugin option name.
     *
     * @since  0.9.0
     *
     * @var    string
     */
    protected $option_name;

    /**
     * Plugin options.
     *
     * @since  0.9.0
     *
     * @var    string
     */
    protected $options;

    /**
     * Default options.
     *
     * Used for setting uninitialized plugin options.
     *
     * @since  0.9.0
     *
     * @var    array
     */
    protected $option_defaults = array(
        'version'            => 'latest',
        'minified'           => 1,
        'remove_existing_fa' => '',
    );

    /**
     * Instance of this class.
     *
     * @since  0.9.0
     *
     * @var    Better_Font_Awesome_Plugin
     */
    protected static $instance = null;


    /**
     * Returns the instance of this class, and initializes the instance if it
     * doesn't already exist.
     *
     * @return  Better_Font_Awesome  The BFA object.
     */
    public static function get_instance( $args = '' ) {
        static $instance = null;
        if ( null === $instance ) {
            $instance = new static( $args );
        }

        return $instance;
    }

 /**
     * Better Font Awesome Plugin constructor.
     *
     * @since  0.9.0
     */
    function __construct() {

        // Perform plugin initialization actions.
        $this->initialize();

        add_shortcode('vc_fportfolio', array($this, 'vc_fportfolio_display'));


        // Load the plugin text domain.
        add_action( 'init', array( $this, 'load_text_domain' ) );

    }

    /**
     * Do necessary initialization actions.
     *
     * @since  0.10.0
     */
    private function initialize() {
        

        if (!defined('FPORTFOLIO_PATH')) {
            define('FPORTFOLIO_PATH', WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/');
        }

        define('FPORTFOLIO_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
        define('FPORTFOLIO_VERSION_NUMBER', $this->fportfolio_plugin_version());

        if (!defined("IS_ADMIN")) {
            define("IS_ADMIN", is_admin());
        }

        add_action('wp_head', array($this, 'vc_fportfolio_xscript'));
        add_action('init', array($this, 'vc_fportfolio_integrateWithVC'));
        // Set display name.
        add_action('wp_print_scripts', array($this, 'vc_fportfolio_includecss'));
        add_action( 'wp_print_scripts', array($this, 'enqueue_scripts') );  
        $this->plugin_display_name = __( 'VC FullScreen Portfolio', 'vc-fportfolio' );

        // Set options name.
        $this->option_name = self::SLUG . '_options';

    }


public function load_text_domain() {
load_plugin_textdomain('vc-fportfolio', false, '/' . basename(dirname(__FILE__)) . '/languages/'); // load plugin
}

function vc_fportfolio_xscript()  {  if( !is_admin() ){ ?>

<script id="fullviewTmpl" type="text/x-jquery-tmpl"> 
            {{html bgimage}}
            <div class="full-view">
                <span class="full-view-exit">Exit full screen view</span>
                <div class="header">
                    <h2 class="title fportfolio-title">${title}</h2>
                    <div class="full-nav">
                        <span class="full-nav-prev">Previous</span>
                        <span class="full-nav-pages">
                            <span class="full-nav-current">${current}</span>/
                            <span class="full-nav-total">${total}</span>
                        </span>
                        <span class="full-nav-next">Next</span>
                    </div>
                    <p class="subline">${subline}</p>
                    <span class="loading-small"></span>
                </div>
                <div class="project-descr-full">
                    <div class="thumbs-wrapper"><div class="thumbs">{{html thumbs}}</div></div>
                    <div class="project-descr-full-wrapper">
                        <div class="project-descr-full-content">{{html description}}</div><!-- project-descr-full-content -->
                    </div>
                </div><!-- project-descr-full -->
            </div><!-- full-view -->
        </script>

<?php
    }
}



/**
 * Get the plugin version
 *
 * @since 1.0.0
 *
 * @return current plugin version.
 */
function fportfolio_plugin_version()
{
    if (!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $plugin_data = get_plugin_data(FPORTFOLIO_PATH . '/index.php');
    return $plugin_data['Version'];
}

function enqueue_scripts(){
            // Loads our scripts, only on the front end of the site
    if( !is_admin() ){
        wp_enqueue_script( 'vc-fportfolio-custom', FPORTFOLIO_URL.'/js/custom.js', array('jquery'), '1.0', TRUE);
        wp_enqueue_script( 'vc-fportfolio_main_1', FPORTFOLIO_URL.'/js/jquery.tmpl.min.js', array('jquery'), '1.0', TRUE );
        wp_enqueue_script( 'vc-fportfoli_main_2', FPORTFOLIO_URL.'/js/jquery.easing.1.3.js', array('jquery'), '1.0', TRUE );
        wp_enqueue_script( 'vc-fportfolio_main_3',  FPORTFOLIO_URL.'/js/jquery.mousewheel.js', array('jquery'), '1.0', TRUE );
        wp_enqueue_script( 'vc-fportfolio_main_4',  FPORTFOLIO_URL.'/js/jquery.jscrollpane.min.js', array('jquery'), '1.0', TRUE );
        wp_enqueue_script( 'vc-fportfolio_main_5', FPORTFOLIO_URL.'/js/jquery.masonry.min.js', array('jquery'), '1.0', TRUE);
        wp_enqueue_script( 'vc-fportfoli_main_6', FPORTFOLIO_URL.'/js/jquery.gpCarousel.js', array('jquery'), '1.0', TRUE);
    }
}




/**
 * The main grid css for effects.
 *
 * @since 1.0.0
 *
 * @return void.
 */
function vc_fportfolio_includecss() {
    if( !is_admin() ){
        wp_register_style('vc_fportfolio_main', FPORTFOLIO_URL . '/css/style.css');
        wp_register_style('vc_fportfolio_reset', FPORTFOLIO_URL . '/css/reset.css');
        wp_register_style('vc_fportfolio_scrollpane', FPORTFOLIO_URL . '/css/jquery.jscrollpane.css');
        wp_enqueue_style('vc_fportfolio_main');
        wp_enqueue_style('vc_fportfolio_reset');
        wp_enqueue_style('vc_fportfolio_scrollpane');
    }
}

  
/**
 * gives safe web fonts.
 *
 * @since 1.0.0
 *
 * @return void.
 */
function vc_fportfolio_safefonts() {
    return array('Georgia' => 'Georgia, serif',
                'Palatino Linotype' => 'Palatino Linotype, Book Antiqua, Palatino, serif',
                'Times New Roman' => 'Times New Roman, Times, serif',
                'MS Serif' => 'MS Serif, New York, sans-serif',
                'Garamond' => 'Garamond, serif',
                'Bookman Old Style' => 'Bookman Old Style, serif',
                'Verdana' => 'Verdana, Geneva, sans-serif',
                'Arial' => 'Arial, Helvetica, sans-serif',
                'Arial Black' => 'Arial Black, Gadget, sans-serif',
                'Arial Narrow' => 'Arial Narrow, sans-serif',
                'Symbol' => 'Symbol, sans-serif',
                'Impact' => 'Impact, Charcoal, sans-serif',
                'Tahoma' => 'Tahoma, Geneva, sans-serif',
                'Century Gothic' => 'Century Gothic, sans-serif',
                'Lucida Sans Unicode' => 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Trebuchet MS' => 'Trebuchet MS, Helvetica, sans-serif',
                'MS Sans Serif' => 'MS Sans Serif, Geneva, sans-serif',
                'Courier New' => 'Courier New, Courier, monospace',
                'Courier' => 'Courier, monospace',
                'Lucida Console' => 'Lucida Console, Monaco, monospace',
                'Comic Sans MS' => 'Comic Sans MS, cursive',
                );
}

function nl2p($str) {
    $arr=explode("\n",$str);
    $out='';

    for($i=0;$i<count($arr);$i++) {
        if(strlen(trim($arr[$i]))>0)
            $out.='<p>'.trim($arr[$i]).'</p>';
    }
    return $out;
}

/**
 * Prepare the shortcode.
 *
 * @since 1.0.0
 *
 * @return html output.
 */
function vc_fportfolio_display($atts, $content = null) // New function parameter $content is added!
{
    extract(shortcode_atts(array(
        'vc_fportfolio_bgimage' => '',
        'vc_fportfolio_thumbs' => '',
        'vc_fportfolio_type' => '',
        'vc_fportfolio_title' => '',
        'vc_fportfolio_font' => '',
    ), $atts));
    $content = $this->nl2p($content);
    $vc_fportfolio_title = str_replace('&amp;', '<span class="fancy">&amp;</span>', $vc_fportfolio_title);
    $vc_fportfolio_bgimage = wp_get_attachment_image_src(intval($vc_fportfolio_bgimage), 'full');
    $vc_fportfolio_bgimage = $vc_fportfolio_bgimage[0];
    $vc_fportfolio_thumbnails = array();
    $images = explode(',', $vc_fportfolio_thumbs);
    foreach ($images as $key => $image) {
        $image_array = wp_get_attachment_image_src(intval($image), array(260, 173));
        $vc_fportfolio_thumbnails[$key] = $image_array[0];
    }
    ob_start();
    include(FPORTFOLIO_PATH.'/templates/view.php');
    $output = ob_get_contents();
    ob_end_clean();
  
    return $output;
}
/**
 * Map the vc fields.
 *
 * @since 1.0.0
 *
 * @return void.
 */
function vc_fportfolio_integrateWithVC()
{
    $myFonts = $this->vc_fportfolio_safefonts();
    vc_map(array(
        "name" => __("Amazing FullScreen Portfolio"),
        "admin_enqueue_css" => array(
            FPORTFOLIO_URL . '/css/custom.css'
        ),
        "base" => "vc_fportfolio",
        "category" => __('Content'),
        "icon" => "icon-vc-fportfolio-page",
        "params" => array(
            array(
                "type" => 'attach_image',
                "heading" => __("Background Image", 'vc-fportfolio'),
                "param_name" => "vc_fportfolio_bgimage",
                "description" => __("Background Image for portfolio item, Upload an image with higher resolution for better view.", 'vc-fportfolio')
            ),
            array(
                "type" => "attach_images",
                "heading" => __("Portfolio Thumbnails", 'vc-fportfolio'),
                "param_name" => "vc_fportfolio_thumbs",
                "description" => __("Upload single / multiple thumbnails portfolio thumbnails", 'vc-fportfolio')
            ),
            array(
                "type" => "textfield",
                "heading" => __("Type / Category of your portfolio item", 'vc-fportfolio', 'vc-fportfolio'),
                "param_name" => "vc_fportfolio_type",
                "value" => '',
                "description" => __("Type / Category of your portfolio item", 'vc-fportfolio')
            ),
            array(
                "type" => "textfield",
                "heading" => __("Portfolio Title", 'vc-fportfolio'),
                "param_name" => "vc_fportfolio_title",
                "value" => '',
                "description" => __("Portfolio Item title, example - Logo Design for Codecanyon", 'vc-fportfolio')
            ),
            array(
                "heading" => __("Portfolio Title Font Style", 'vc-fportfolio'),
                "description" => __("Font style to apply on portfolio title", 'vc-fportfolio'),
                "param_name" => "vc_fportfolio_font",
                "value" => $myFonts,
                "type" => "dropdown"
            ),
            array(
                "type" => "textarea_html",
                "heading" => __('Description / Content for the portfolio item', 'vc-fportfolio'),
                "param_name" => "content",
                 "holder" => "p",
                "description" => __("The Description / Content for the portfolio item", 'vc-fportfolio'),
            ),
        )
    ));
}
}