<?php

/**
 * Plugin Name: AAAA Ombres et lumières, vente de photos
 * Plugin URI: http://www.ombres-et-lumieres.eu
 * Description: Ombres et lumières est un site destiné à vous offrir un voyage au sein de mon univers photographique. Au fil du temps, j' ai abordé de nombreux styles photographiques, le dernier en date est plutôt orienté paysages urbains et voyages
 * Version: 1.0
 * Author: CodeWP Assistant
 * Author URI: https://codewp.ai
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

// Define constants for plugin paths
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));


//require_once(MY_PLUGIN_PATH . 'settings/settings.php');
//require_once(MY_PLUGIN_PATH . 'settings/datas-photos.php');
//require_once(MY_PLUGIN_PATH . 'utilities/utilities.php');
require_once(MY_PLUGIN_PATH . 'settings/menu.php');

class Cwpai_Script_Loader {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'cwpai_enqueue_scripts' ) );
        add_action('admin_enqueue_scripts', array($this,'cwpai_enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this,'cwpai_enqueue_admin_scripts'));
    }

    public function cwpai_enqueue_scripts() {
      
      //  if(get_current_screen()->base === 'toplevel_page_ombres-et-lumieres') {
       wp_enqueue_media();
       
        // Enqueue JavaScript
        wp_enqueue_script(
            'ombres-et-lumieres',
           MY_PLUGIN_PATH . 'js/ombres-et-lumieres.js',
           ['jquery'], // Dependancies, like jQuery if needed
            '1.0.0', // Version number
            true // Load in footer
        );
        wp_localize_script('ombres-et-lumieres-js', 'OmbresEtLumieresAjax', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]);
    //  }

        // Enqueue CSS
        wp_enqueue_style(
            'ombres-et-lumieres-css',
            plugin_dir_url( __FILE__ ) . 'css/style.css',
            array(), // Dependancies
            '1.0.0' // Version number
        );
    }
    
    public function cwpai_enqueue_admin_styles() {
        wp_enqueue_style(
            'cwpai_admin_css',
             MY_PLUGIN_PATH . 'css/style.css'
         );
    }
    
    public function cwpai_enqueue_admin_scripts() {
        wp_enqueue_script(
            'cwpai_admin_js', 
            MY_PLUGIN_PATH . 'js/admin-script.js'
        );
    }
    
  
}
new Cwpai_Script_Loader();