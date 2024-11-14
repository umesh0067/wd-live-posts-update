<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Scripts Class
 *
 * Handles adding scripts functionality to the admin pages
 * as well as the front pages.
 *
 * @package WPUM Custom Plugin
 * @since 1.0.1
 */
class WPUM_Shortcode_Public {
	
	public function __construct(){
		
	}
	
	/**
	 * Replace Shortcode with Custom Content
	 *
	 * @param $atts this will handles to various attributes which are passed in shortcodes
	 * @param $content this will return the your replaced content
	 * 
	 * @package WPUM Custom Plugin
	 * @since 1.0.1
	 */
	
	public function wpum_livepost_shortcode( $atts, $content ) {
		
		//content to replace with your content and with attributes
		$atts = shortcode_atts( array(	
				'number_of_posts' => '',
		), $atts );

		$number_of_posts = (intval($atts['number_of_posts'])) ? intval($atts['number_of_posts']) : 5;
		
		ob_start();

		include_once( WPUM_PLUGIN_DIR . '/includes/public/shortcodes/shortcode-live-posts-listing.php' );

		return ob_get_clean();
		
	}
	
	/**
	 * Adding Hooks
	 *
	 * @package WPUM Custom Plugin
	 * @since 1.0.1
	 */
	public function add_hooks() {
		
		//replace shortcodes with custom content or HTML
		add_shortcode('wpum_livepost_shortcode', array($this, 'wpum_livepost_shortcode'));
		
	}
}