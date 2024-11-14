<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
* Scripts Class
* Handles adding scripts functionality to the admin pages
* as well as the front pages.
*
* @package WPUM Custom Plugin
* @since 1.0.1
*/
class wpum_Plugin_Scripts_cls {
	
	function __construct() {

		 // Add hooks
		 $this->add_hooks();
		
	}
	
	/**
 	* Enqueue Scripts
	* Handles to enqueue scripts for front
	*
	* @package WPUM Custom Plugin
	* @since 1.0.1
	*/
	public function wpum_plugin_public_scripts() {

		global $wp_version;

		// Register & Enqueue ajax style
		wp_register_style( 'wpum-public-style', WPUM_PLUGIN_URL . 'assets/css/wpum-public.css', array(), WPUM_PLUGIN_VERSION );
		wp_enqueue_style( 'wpum-public-style' );
	    
		
		// Register & Enqueue ajax script
		wp_register_script( 'wpum-public-script', WPUM_PLUGIN_URL . 'assets/js/wpum-custom-public.js', array('jquery'), WPUM_PLUGIN_VERSION , true );
		wp_enqueue_script( 'wpum-public-script' );

		//localize script to pass some variable to javascript file from php file
		//pass ajax url to access wordpress ajax file at front side
		wp_localize_script( 'wpum-public-script', 'WP_Ajax', array ('ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) )));

		// Enqueue WordPress Core Scripts and Styles
        wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('thickbox');
        wp_enqueue_media();
		
		// Conditionally Load Color Picker
	    if ( $wp_version >= 3.5 ){
	        //Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
	        wp_enqueue_script( 'wp-color-picker' );
	    }
	    //If the WordPress version is less than 3.5 load the older farbtasic color picker.
	    else {
	        //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
	        wp_enqueue_script( 'farbtastic' );
	    }
	}

	/**
	* Enqueue Admin Scripts
	* Handles to enqueue scripts for front
	* 
	* @package WPUM Custom Plugin
 	* @since 1.0.1
	*/
	public function wpum_plugin_admin_scripts() {

		global $wp_version;

		// Register & Enqueue admin custom style
		wp_register_style('wpum-admin-css',  WPUM_PLUGIN_URL.'assets/css/wpum-admin.css', array(), WPUM_PLUGIN_VERSION );
		wp_enqueue_style('wpum-admin-css');	
		
		// Register & Enqueue ajax script
		wp_register_script( 'wpum-admin-script', WPUM_PLUGIN_URL . 'assets/js/wpum-custom-admin.js', array('jquery'), WPUM_PLUGIN_VERSION , true );
		wp_enqueue_script( 'wpum-admin-script' );

		//localize script to pass some variable to javascript file from php file
		//pass ajax url to access wordpress ajax file at front side
		wp_localize_script( 'wpum-admin-script', 'WP_Admin_Ajax', array ('ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) )));

		// Register & Enqueue select2 style
		wp_register_style('wpum-select2-css',  WPUM_PLUGIN_URL.'assets/css/select2.min.css', array(), WPUM_PLUGIN_VERSION );
		wp_enqueue_style('wpum-select2-css');

		// Register & Enqueue ajax script
		wp_register_script( 'wpum-select2-script', WPUM_PLUGIN_URL . 'assets/js/select2.min.js', array('jquery'), WPUM_PLUGIN_VERSION , true );
		wp_enqueue_script( 'wpum-select2-script' );

		// Enqueue WordPress Core Scripts and Styles
		wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_media();
        wp_enqueue_editor();

        // Load Color Picker Conditionally
        if ($wp_version >= 3.5) {
            wp_enqueue_script('wp-color-picker');
        } else {
            wp_enqueue_script('farbtastic');
        }
	}
	
	
	
	/**
	* Adding Hooks
	* Adding hooks for the styles and scripts.
	*
	* @package WPUM Custom Plugin
 	* @since 1.0.1
	*/
	public function add_hooks() {
		
		//add public style and scripts
		add_action('wp_enqueue_scripts', array ( $this, 'wpum_plugin_public_scripts'));

		//add admin style and scripts
		add_action('admin_enqueue_scripts', array ( $this, 'wpum_plugin_admin_scripts'));
		
	}
}