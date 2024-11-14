<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Settings Page
 *
 * Handle settings
 * 
 * @package WPUM Custom Plugin
 * @since 1.0.1
 */

global $wpum_plugin_model;

$model = $wpum_plugin_model;

	
	// Query recent posts
	$recent_posts = new WP_Query(array(
		'post_type' => CUSTOM_POST_TYPE_LISTS,
		'posts_per_page' => $number_of_posts,
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' => 'custom_checkbox_meta',
				'value' => '1',
				'compare' => '='
			)
		)
	));

	if ($recent_posts->have_posts()) {
		echo '<ul class="recent-posts-with-image wd_recent_posts_shortcode_wrap">';
		while ($recent_posts->have_posts()) {
			$recent_posts->the_post();
			echo '<li>';
			echo '<a class="content_cover" href="' . esc_url( get_the_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
			if (has_post_thumbnail()) {
				echo '<a class="image_cover" href="' . esc_url( get_the_permalink() ) . '">' . get_the_post_thumbnail(get_the_ID(), 'thumbnail') . '</a>';
			}
			echo '</li>';
		}
		echo '</ul>';
	} else {

		esc_html_e('No record found.', 'wd-live-posts-update');
	}
	wp_reset_postdata();
