<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Pages Class
 *
 * Handles generic Admin functionailties
 *
 * @package WPUM Custom Plugin
 * @since 1.0.1
 */

class WPUM_Form_Admin_Pages {

	public $model, $scripts;

	public function __construct()	{		

		global $wpum_plugin_model, $wpum_plugin_scripts;
		$this->model = $wpum_plugin_model;
		$this->scripts = $wpum_plugin_scripts;
	}

	/**
	 * Create menu page
	 * Adding required menu pages and submenu pages
	 * to manage the plugin functionality
	 * 
	 * @package WPUM Custom Plugin
	 * @since 1.0.1
	 */
	
	public function wpum_plugin_add_menu_page() {
		
		$wpd_ws_setting = add_menu_page( esc_html__( 'Live Post Setting', 'wd-live-posts-update' ), esc_html__( 'Live Post Setting', 'wd-live-posts-update' ), 'manage_options', 'plugin-setting-page', array($this, 'wpum_ws_settings') );
		
		//add_action( "admin_head-$wpd_ws_setting", array( $this->scripts, 'wpd_ws_settings_scripts' ) );
	}

	/**
	 * Register Settings
	 *
	 * @package WPUM Custom Plugin
	 * @since 1.0.1
	 */

	public function wpd_ws_admin_init() {
		
		register_setting( 'wpum_plugin_options_group', 'wpum_options_name', array($this, 'wpum_validate_options') );
		
	}

	public function wpum_validate_options( $input ) {

		// sanitize text input (strip html tags, and escape characters)
		$input['title']	=  $this->model->wpum_mb_escape_slashes_deep( $input['title'] );
		
		return $input;
	}
	
	/**
	 * Includes Plugin Settings
	 * 
	 * Including File for plugin settings
	 *
	 * @package WPUM Custom Plugin
	 * @since 1.0.1
	 */
	public function wpum_ws_settings() {
		
		include_once( WPUM_PLUGIN_ADMIN . '/setting-page/wpum-setting-form.php' );
		
	}
	
	/**
	 * Function to replace the shortcode in the text widget
	 *
	 * @package WPUM Custom Plugin
	 * @since 1.0.1
	 */
	public function wpd_ws_widget_shortcode_replace( $text ) {
		
		//$text = do_shortcode('[gallery]');
		$text = do_shortcode($text);
		
		return $text;
	}
	
	/**
	 * Adding Hooks
	 *
	 * @package WPUM Custom Plugin
	 * @since 1.0.1
	 */

	public function add_hooks() {
		
		add_action( 'admin_menu', array( $this, 'wpum_plugin_add_menu_page' ) );
			
		add_action( 'admin_init', array($this, 'wpd_ws_admin_init'));
		
		// Filter to replace the shortcode in the text widget
		//add_filter('widget_text', array( $this, 'wpd_ws_widget_shortcode_replace') );
	}

}
