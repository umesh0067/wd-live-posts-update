<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

$selected_post_type = isset(get_option( 'wpum_options_name' )['custom_post_type_option']) ? get_option( 'wpum_options_name' )['custom_post_type_option'] : 'post';


// define custom post types variable.
define( 'WPUM_POST_TYPE_LISTS', $selected_post_type );

/**
 * Custom Meta Box Class
 *
 * The Meta Box Class is used by including it in your plugin files and using its methods to 
 * create custom meta boxes for custom post types. It is meant to be very simple and 
 * straightforward. For name spacing purposes, All Types metabox ( meaning you can do anything with it )
 * is used. 
 *
 * @package WPUM Custom Plugin
 */


// Add live tag before title for archive, search, single, main query and home and search
function wpum_live_update_badge_before_title_fn( $title, $id ) {
    $CheckStopLive = wp_strip_all_tags( get_post_meta($id, 'custom_stop_live_meta', true) );
    
    if ( !is_admin() && !$CheckStopLive ) {
        if ((is_archive() || is_home() || is_search() || is_main_query() || is_single()) && get_post_meta($id, 'custom_checkbox_meta', true)) {
            $badge = '<div class="liveCapsule iflx-box mA"><div class="liveIndicator"> <div class="livenow livenowAnim"></div></div><strong class="indicatorText">LIVE</strong></div>';
            $title = $badge . $title;
        }
    }

    return $title;
}
add_filter('the_title', 'wpum_live_update_badge_before_title_fn', 10, 2 );



// Add custom metabox
function wpum_add_meta_boxes() {
    // add active live tag
    add_meta_box(
        'custom_checkbox_meta_box',
        'Live Post Control',
        'wpum_render_checkbox_meta_box',
        WPUM_POST_TYPE_LISTS,
        'normal',
        'high'
    );

    // add custom meta boxes
    add_meta_box(
        'custom_meta_box',
        'Live Blog Update Area',
        'wpum_render_meta_box',
        WPUM_POST_TYPE_LISTS,
        'normal',
        'high'
    );

}
add_action('add_meta_boxes', 'wpum_add_meta_boxes');


// checkbox active live post metabox fn
function wpum_render_checkbox_meta_box($post) {
    wp_nonce_field('custom_checkbox_meta_box_nonce', 'custom_checkbox_meta_box_nonce_field');
    
    $is_active = get_post_meta($post->ID, 'custom_checkbox_meta', true);
    $is_stopped = get_post_meta($post->ID, 'custom_stop_live_meta', true);

    $lv_keywords = get_post_meta($post->ID, '_wd_keywords_live_meta', true);

    ?>
    <div class="wd_live_checkboxes_wrap">
        <label for="custom_checkbox_meta">
            <input type="checkbox" id="custom_checkbox_meta" name="custom_checkbox_meta" value="1" <?php checked($is_active, '1'); ?> />
            Active Live Post
        </label>
        <label for="custom_stop_live_meta">
            <input type="checkbox" id="custom_stop_live_meta" name="custom_stop_live_meta" value="1" <?php checked($is_stopped, '1'); ?> />
            Stop Live Post
        </label>
    </div>
    <div class="wd_live_checkboxes_wrap wd_live_posts_keywords">
        <label for="keywords">Keywords</label>
        <textarea placeholder="Enter keywords with comma(,) seperate." cols="55" rows="5" name="wd_keywords_live_meta"> <?php echo esc_attr( $lv_keywords ); ?> </textarea>
    </div>
    <?php
}




function wpum_render_meta_box($post) {

    wp_nonce_field('custom_meta_box_nonce', 'custom_meta_box_nonce_field');
    
    $meta = get_post_meta($post->ID, 'custom_meta', true);

    echo '<div class="add_btn_cover">';
        echo '<button type="button" id="add_repeater" class="um_add_update_btn">Add Update</button>';
    echo '</div>';

    echo '<div id="custom_meta_box_wrapper" class="live_posts_update_main">';
    
    if ($meta && is_array($meta)) {
        foreach ($meta as $key => $value) {
            ?>
            <div class="custom_meta_box_item live_posts_update_wrap">
                <div class="um_live_post_field um_lv_headline">
                    <label>Headline:</label>
                    <input type="text" name="custom_meta[<?php echo esc_attr( $key ); ?>][headline]" value="<?php echo esc_attr($value['headline']); ?>" />
                </div>
                <div class="um_live_post_field um_lv_date_published">
                    <label>Date Published:</label>
                    <div class="date_ui_flex">
                    <input type="datetime-local" name="custom_meta[<?php echo esc_attr( $key ); ?>][date_published]" value="<?php echo esc_attr($value['date_published']); ?>" />
                    <!-- <a id="update_date_now" class="update_date_now" href="javascript:void(0)">Update Date</a>
                    <a id="clear_date_now" class="clear_date_now" href="javascript:void(0)">Clear</a> -->
                    </div> 
                </div>
                <div class="um_live_post_field um_lv_article_body">
                    <label>Article Body:</label>
                    <?php
                    $editor_id = 'article_body_' . $key;
                    $editor_settings = array('textarea_name' => "custom_meta[$key][article_body]", 'editor_height' => 150);    
                    wp_editor($value['article_body'], $editor_id, $editor_settings);
                    ?>
                </div>
                <div class="um_live_post_field um_lv_remove_btn">
                    <button type="button" class="remove_repeater">Remove</button>
                </div>
            </div>
            <?php
        }
    }
    
    echo '</div>';
    
}


function wpum_live_save_meta_box($post_id) {

    // Add nonce for security and authentication.
    // $nonce_name   = isset( $_POST['custom_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_nonce'] ) ) : '';
    // $nonce_action = 'custom_nonce_action';
        
    // Security checks
    if ( ! isset( $_POST['custom_meta_box_nonce_field'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['custom_meta_box_nonce_field'] ) ), 'custom_meta_box_nonce') ) {
		return;
	}
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Get the current date and time
    $current_date_time = current_time('Y-m-d H:i:s');
	
	$wd_custom_meta = isset($_POST['custom_meta']) && is_array($_POST['custom_meta']) ? wp_unslash($_POST['custom_meta']) : [];

    // Check if custom_meta data is set
    if (!empty($wd_custom_meta) ) {
        $custom_meta = array_map(function($value) use ($current_date_time) {
            // Check if the date_published field is empty and set it to current date and time if it is
            if (empty($value['date_published'])) {
                $value['date_published'] = $current_date_time;
            }
            return [
                'headline' => sanitize_text_field($value['headline']),
                'date_published' => sanitize_text_field($value['date_published']),
                'article_body' => $value['article_body'],
            ];
        }, $wd_custom_meta);
        
        update_post_meta($post_id, 'custom_meta', $custom_meta);
    } else {
        delete_post_meta($post_id, 'custom_meta');
    }


    // Active and stop live posts tag update meta data
    if (isset($_POST['custom_checkbox_meta_box_nonce_field']) && wp_verify_nonce( sanitize_text_field ( wp_unslash( $_POST['custom_checkbox_meta_box_nonce_field'] ) ), 'custom_checkbox_meta_box_nonce')) {
        $is_active = isset($_POST['custom_checkbox_meta']) ? '1' : '';
        update_post_meta($post_id, 'custom_checkbox_meta', $is_active);

        $is_stopped = isset($_POST['custom_stop_live_meta']) ? '1' : '';
        update_post_meta($post_id, 'custom_stop_live_meta', $is_stopped);

        $save_keywords = isset( $_POST['wd_keywords_live_meta'] ) ? sanitize_text_field( wp_unslash( $_POST['wd_keywords_live_meta'] ) ) : '';
        update_post_meta($post_id, '_wd_keywords_live_meta', $save_keywords);

    }

}
add_action('save_post', 'wpum_live_save_meta_box');




// display meta data
function wpum_append_meta_box_content($content) {
    global $post;
    if (is_singular(WPUM_POST_TYPE_LISTS) && in_the_loop() && is_main_query()) {
        $meta = get_post_meta(get_the_ID(), 'custom_meta', true);
        $CheckStopLive = get_post_meta(get_the_ID(), 'custom_stop_live_meta', true);
        $livePost_area_Title = (get_option( 'wpum_options_name' )['title']) ? get_option( 'wpum_options_name' )['title'] : 'LIVE UPDATE';
        $lv_keywords = get_post_meta(get_the_ID(), '_wd_keywords_live_meta', true);
        $disClass = '';
        if($CheckStopLive){
            $disClass = 'disable_update';
        }
        ob_start();
        ?>
        <div class="live-blog-updates <?php echo esc_attr( $disClass ); ?>">
            <?php
            if ($meta && is_array($meta)) {

                ?>
                <div class="liveHead flx-box-sb mA">
                    <h2>
                        <div class="liveCapsule iflx-box mA big">
                            <div class="liveIndicator"> 
                                <div class="livenow livenowAnim"></div>
                            </div>
                            <strong class="indicatorText"><?php echo esc_attr( $livePost_area_Title ); ?></strong>
                        </div>
                    </h2>
                    <div class="updateInfo_details"><span>Update Just now</span><i class="refreshUpdate"></i></div>
                </div>

                <?php
                // Sort the updates by date_published in descending order
                usort($meta, function($a, $b) {
                    return strtotime($b['date_published']) - strtotime($a['date_published']);
                });

                // Prepare an array to hold the live updates for JSON-LD
                $live_updates_json_ld = [];

                foreach ($meta as $value) {
                     // Get the date_published field value
                    $date_published = !empty($value['date_published']) ? $value['date_published'] : get_the_modified_date('Y-m-d H:i:s');
                    // Convert date_published to a timestamp
                    $date_published_timestamp = strtotime($date_published);
                    // Calculate the difference between the current time and the date_published
                    $now_timestamp = current_datetime()->format('Y-m-d H:i:s');
                    $time_difference = strtotime($now_timestamp) - $date_published_timestamp;


                    // Determine the format for the date display
                    if ($time_difference < 7 * 24 * 60 * 60) { // Less than 7 days
                        if ($time_difference >= 24 * 60 * 60) {
                            $days = floor($time_difference / (24 * 60 * 60));
                            $formatted_date = $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
                        } elseif ($time_difference >= 60 * 60) {
                            $hours = floor($time_difference / (60 * 60));
                            $formatted_date = $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
                        } elseif ($time_difference >= 60) {
                            $minutes = floor($time_difference / 60);
                            $formatted_date = $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
                        } else {
                            $formatted_date = 'just now';
                        }
                    } else {
                        // Create a DateTime object from the timestamp
						$date = new DateTime();
						$date->setTimestamp($date_published_timestamp);

						// Set the timezone to the site's timezone
						$date->setTimezone(new DateTimeZone(wp_timezone_string()));

						// Format the date
						$formatted_date = $date->format('g:i A \I\S\T  • j M Y');
                    }

                    ?>
                    <div class="live-blog-update">
                        <time><?php echo esc_attr( $formatted_date ); ?></time>
                        <h4 class="lv_headline"><?php echo esc_html( wp_strip_all_tags( $value['headline'] ) ); ?></h4>
                        <div class="lv_article_body"><?php echo esc_html( wp_strip_all_tags( $value['article_body'] ) ); ?></div>
                    </div>
                    <?php

                    $live_updates_json_ld[] = [
                        "@type" => "BlogPosting",
                        "headline" => $value['headline'],
                        "articleBody" => wp_strip_all_tags( $value['article_body'] ),
                        "datePublished" => $date_published,
                        // Add more parameters as needed
                        "author" => [
                            "@type" => "Person",
                            "name" => get_the_author_meta('display_name', $post->post_author),
                        ],
                        "publisher" => [
                            "@type" => "Organization",
                            "name" => get_bloginfo('name'),
                            "logo" => [
                                "@type" => "ImageObject",
                                "url" => get_site_icon_url(),
                            ],
                        ],
                        "mainEntityOfPage" => [
                            "@type" => "WebPage",
                            "@id" => get_permalink(),
                        ],
                        "url" => get_permalink(),
                    ];
                }

                // Check if the post is not marked as "Stop Live Post"
                if (!$CheckStopLive) {

                    $AuthorName = get_the_author_meta('display_name', $post->post_author);

                    // Create the LiveBlogPosting JSON-LD
                    $live_blog_json_ld = [
                        "@context" => "https://schema.org",
                        "@type" => "LiveBlogPosting",
                        "headline" => wp_strip_all_tags( get_the_title() ),
                        "datePublished" => get_the_date('c'),
                        "dateModified" => get_the_modified_date('c'),
                        "liveBlogUpdate" => $live_updates_json_ld,
                        "keywords" => $lv_keywords,
                        "author" => [
                            "@type" => "Person",
                            "name" => $AuthorName,
                        ],
                        "publisher" => [
                            "@type" => "Organization",
                            "name" => get_bloginfo('name'),
                            "logo" => [
                                "@type" => "ImageObject",
                                "url" => get_site_icon_url(),
                            ],
                        ],
                        "mainEntityOfPage" => [
                            "@type" => "WebPage",
                            "@id" => get_permalink(),
                        ],
                        "url" => get_permalink(),
                    ];

                    // Output the JSON-LD script
                    ?>
                    <script type="application/ld+json">
                        <?php echo esc_js( wp_json_encode( $live_blog_json_ld ) ); ?>
                    </script>
                    <?php
                }
            }
            ?>
        </div>

        <?php

        $custom_content = ob_get_clean();
        $content .= $custom_content;
    }

    return $content;
}
add_filter('the_content', 'wpum_append_meta_box_content');





// Register Recent Posts with Image Widget
function wpum_recent_posts_with_image_widget() {
    register_widget('WDUM_posts_Update_Widget');
}
add_action('widgets_init', 'wpum_recent_posts_with_image_widget');


class WDUM_Posts_Update_Widget extends WP_Widget {
    // Constructor
    function __construct() {
        parent::__construct(
            'WDUM_posts_Update_Widget', // Base ID
            __('WD Live Posts with Image', 'wd-live-posts-update'), // Name
            array('description' => __('A widget to display recent posts with their featured images', 'wd-live-posts-update'))
        );
    }

    // Widget front-end
    public function widget($args, $instance) {
        echo esc_attr( $args['before_widget'] );
        if (!empty($instance['title'])) {
            echo esc_attr( $args['before_title'] ) . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . esc_attr( $args['after_title'] );
        }

        // Query recent posts
        $recent_posts = new WP_Query(array(
            'post_type' => WPUM_POST_TYPE_LISTS,
            'posts_per_page' => $instance['number_of_posts'],
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
            echo '<ul class="recent-posts-with-image">';
            while ($recent_posts->have_posts()) {
                $recent_posts->the_post();
                echo '<li>';
                echo '<a class="content_cover" href="' . esc_url( get_the_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
                if (has_post_thumbnail()) {
                    echo '<a class="image_cover" href="' . esc_url( get_the_permalink() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'thumbnail' ) . '</a>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        wp_reset_postdata();

        echo wp_kses_post( $args['after_widget'] );
    }

    // Widget backend
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $number_of_posts = !empty($instance['number_of_posts']) ? $instance['number_of_posts'] : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wd-live-posts-update' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('number_of_posts') ); ?>"><?php esc_html_e('Number of posts:', 'wd-live-posts-update'); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('number_of_posts') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number_of_posts') ); ?>" type="number" value="<?php echo esc_attr($number_of_posts); ?>">
        </p>
        <?php
    }

    // Update widget
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['number_of_posts'] = (!empty($new_instance['number_of_posts'])) ? intval($new_instance['number_of_posts']) : 5;
        return $instance;
    }
}