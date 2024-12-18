<?php
/*
Plugin Name: Slider Revolution Lottie AddOn
Plugin URI: http://www.themepunch.com/
Description: Add cool 2d animations directly from After Effects using lottie
Author: ThemePunch
Version: 3.0.8
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-lottie-admin'
	'rs-lottie-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_LOTTIE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_LOTTIE_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));
define('RS_LOTTIE_REVISION', '3.0.8');

require_once(RS_LOTTIE_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everyting by calling the following function *
**/
function rs_lottie_init(){

	new RsLottieBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_lottie_init');
register_activation_hook( __FILE__, 'rs_lottie_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnLottieBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnLottieBase','get_help'),10,1);

?>