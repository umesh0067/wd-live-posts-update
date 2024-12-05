<?php
/**
* Plugin Name: WD Live Posts Update
* Plugin URI: https://profiles.wordpress.org/umeshladumor/
* Description: Live Post Updates is a powerful WordPress plugin designed to provide live updates for posts and custom post types. It includes functionality to mark posts as live, ensuring they appear in the live update area, and allows users to stop live updates as needed.
* Version: 1.0.2
* Author: Umesh Ladumor
* Author URI: https://github.com/umesh0067
* License:     GPLv2 or later
* License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
* Text Domain: wd-live-posts-update
* Domain Path: /languages
* 
* @package WD Live Posts
* @category Core
* @author Umesh Ladumor
* 
*/   


/**
* Basic plugin definitions 
* 
* @package WD Live Posts
* @since 1.0.1
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb;

/**
* Basic Plugin Definitions 
* 
* @package WD Live Posts
* @since 1.0.1
*/
if( !defined( 'WPUM_PLUGIN_VERSION' ) ) {
	define( 'WPUM_PLUGIN_VERSION', '1.0.1' ); //version of plugin
}
if( !defined( 'WPUM_PLUGIN_DIR' ) ) {
	define( 'WPUM_PLUGIN_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'WPUM_PLUGIN_TEXT_DOMAIN' )) {
	define( 'WPUM_PLUGIN_TEXT_DOMAIN', 'wd-live-posts-update' ); // text domain for languages
}
if( !defined( 'WPUM_PLUGIN_ADMIN' ) ) {
	define( 'WPUM_PLUGIN_ADMIN', WPUM_PLUGIN_DIR . '/includes/admin' ); // plugin admin dir
}
if( !defined( 'WPUM_PLUGIN_URL' ) ) {
	define( 'WPUM_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}

if( !defined( 'WPUM_PLUGIN_BASENAME' ) ) {
	define( 'WPUM_PLUGIN_BASENAME', basename( WPUM_PLUGIN_DIR ) ); //Plugin base name
}

/**
* Load Text Domain
* This gets the plugin ready for translation.
* 
* @package WD Live Posts
* @since 1.0.1
*/
function wpum_x_load_textdomain() {
	
 	// Set filter for plugin's languages directory
	$wpum_lang_dir	= dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$wpum_lang_dir	= apply_filters( 'wpum_ws_languages_directory', $wpum_lang_dir );
	
	// Traditional WordPress plugin locale filter
	$locale	= apply_filters( 'plugin_locale',  get_locale(), 'wpum-live-posts-update' );
	$mofile	= sprintf( '%1$s-%2$s.mo', 'wpum-live-posts-update', $locale );
	
	// Setup paths to current locale file
	$mofile_local	= $wpum_lang_dir . $mofile;
	$mofile_global	= WP_LANG_DIR . '/' . WPUM_PLUGIN_BASENAME . '/' . $mofile;
	
	if ( file_exists( $mofile_global ) ) {  // Look in global /wp-content/languages/wp-settings-widget folder
		load_textdomain( 'wpum-live-posts-update', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) {  // Look in local /wp-content/plugins/wp-settings-widget/languages/ folder
		load_textdomain( 'wpum-live-posts-update', $mofile_local );
	} else { // Load the default language files
		load_plugin_textdomain( 'wpum-live-posts-update', false, $wpum_lang_dir );
	}
  
}

/**
* Activation hook 
* Register plugin activation hook.
* 
* @package WD Live Posts
* @since 1.0.1
*/
register_activation_hook( __FILE__, 'wpum_plugin_activation_fn' );

/**
* Deactivation hook
* Register plugin deactivation hook.
* 
* @package WD Live Posts
* @since 1.0.1
*/
register_deactivation_hook( __FILE__, 'wpum_plugin_deactivate_fn' );

/**
* Plugin Setup Activation hook call back 
* Initial setup of the plugin setting default options 
* and database tables creations.
* 
* @package WD Live Posts
* @since 1.0.1
*/
function wpum_plugin_activation_fn() {
	
	global $wpdb;

	//if plugin is first time going to activated then set all default options
	$wpd_ws_options = get_option('wpum_options_name');
	
	if(empty($wpd_ws_options)) {
		
		wpum_ws_default_settings(); // set default settings
		
	}
}

/**
* Plugin Setup (On Deactivation)
* Does the drop tables in the database and
* delete  plugin options.
*
* @package WD Live Posts
* @since 1.0.1
*/
function wpum_plugin_deactivate_fn() {
	global $wpdb;
			
}

/**
 * Plugin default settings
 *  
 * @package WP Settings & Widget Page
 * @since 1.0.1
 */

 function wpum_ws_default_settings() {
	
	$options = array(
		'custom_post_type_option' => [],
		'title'		=>	esc_html__( 'LIVE UPDATE', 'wd-live-posts-update' ),
	);
					
	update_option('wpum_options_name', $options);
}


/**
* Load Plugin
* Handles to load plugin after
* dependent plugin is loaded
* successfully
* 
* @package WD Live Posts
* @since 1.0.1
*/
function wpum_plugin_loaded() {
 
	// load first plugin text domain
	wpum_x_load_textdomain();
}

//add action to load plugin
add_action( 'plugins_loaded', 'wpum_plugin_loaded' );

/**
* Initialize all global variables
* 
* @package WD Live Posts
* @since 1.0.1
*/
global $wpum_plugin_model, $wpum_plugin_scripts, $wpum_plugin_admin_side, $wpum_plugin_shortcode;

/**
* Includes
* Includes all the needed files for our plugin
*
* @package WD Live Posts
* @since 1.0.1
*/

//includes script class file
require_once ( WPUM_PLUGIN_DIR . '/includes/class-wpum-scripts.php');
$wpum_plugin_scripts = new wpum_Plugin_Scripts_cls();
$wpum_plugin_scripts->add_hooks();

//includes model class file
require_once ( WPUM_PLUGIN_DIR . '/includes/classs-wpum-model.php');
$wpum_plugin_model = new wpum_Plugin_Model_cls();


/**
* Includes all required files
* 
* @package WD Live Posts
* @since 1.0.1
*/

require_once ( WPUM_PLUGIN_ADMIN . '/class-admin-wpum.php');
$wpum_plugin_admin_side = new WPUM_Form_Admin_Pages();
$wpum_plugin_admin_side->add_hooks();


require_once ( WPUM_PLUGIN_DIR . '/includes/public/class-wpum-shortcode.php');
$wpum_plugin_shortcode = new WPUM_Shortcode_Public();
$wpum_plugin_shortcode->add_hooks();


//Metabox file to handle metaboxes
include_once( WPUM_PLUGIN_ADMIN . '/metabox/wpum-custom-meta-box.php' ); // meta box for deals option