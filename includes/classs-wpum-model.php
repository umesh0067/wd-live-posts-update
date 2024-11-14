<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
* Plugin Model Class
* Handles generic functionailties
*
* @package WPUM Custom Plugin
* @since 1.0.1
*/

class wpum_Plugin_Model_cls {
 	 	
 	//class constructor
	public function __construct()	{		

	}
		
	/**
	* Escape Tags & Slashes
	* Handles escapping the slashes and tags
	*
	* @package WPUM Custom Plugin
	* @since 1.0.1
	*/
	   
	public function wpum_escape_attr($data){

		return esc_attr(stripslashes($data));
	}
	 
	/**
	* Stripslashes 
  	* It will strip slashes from the content
	*
	* @package WPUM Custom Plugin
	* @since 1.0.1
	*/
	   
	public function wpum_mb_escape_slashes_deep( $data = array(), $flag = false ){
	
		//return stripslashes_deep($data);
		if($flag != true) {
			$data = $this->wpum_mb_nohtml_kses($data);
		}

		$data = stripslashes_deep($data);
		
		return $data;
	}
	 
	/**
	* Strip Html Tags  
	* It will sanitize text input (strip html tags, and escape characters)
	* 
	* @package WPUM Custom Plugin
	* @since 1.0.1
	*/
	public function wpum_mb_nohtml_kses($data = array()) {
		
		if ( is_array($data) ) {
			
			$data = array_map(array($this, 'wpum_mb_nohtml_kses'), $data);
			
		} elseif ( is_string( $data ) ) {
			
			$data = wp_filter_nohtml_kses($data);
		}
		
		return $data;
	}	
}