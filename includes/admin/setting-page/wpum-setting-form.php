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
	
	//all settings will reset as per default
	if(isset($_POST['wpd_ws_reset_settings']) && !empty($_POST['wpd_ws_reset_settings']) && $_POST['wpd_ws_reset_settings'] == esc_html__( 'Reset All Settings', 'wd-live-posts-update' )) { //check click of reset button
		
		wpum_ws_default_settings(); // set default settings
		
		echo '<div class="updated" id="message">
			<p><strong>'. esc_html__("All Settings Reset Successfully.",'wd-live-posts-update') .'</strong></p>
		</div>';
		
	}
	//check settings updated or not
	if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
		
		echo '<div class="updated" id="message">
			<p><strong>'. esc_html__("Changes Saved Successfully.",'wd-live-posts-update') .'</strong></p>
		</div>';
	}	
	?>

	<!-- . begining of wrap -->
	<div class="wrap">
		<?php 
			echo "<h2>" . esc_html__('Live Post Setting Page', 'wd-live-posts-update') . "</h2>";
		?>	
		<div class="wpd-ws-reset-setting">
			<form method="post" action="">
				<input id="wpd-ws-reset-all-options" type="submit" class="button-primary" name="wpd_ws_reset_settings" value="<?php echo esc_html__( 'Reset All Settings', 'wd-live-posts-update' ); ?>" />
			</form>
		</div>
			
		<!-- beginning of the plugin options form -->
		<form  method="post" action="options.php">		
		
			<?php
				settings_fields( 'wpum_plugin_options_group' );
				$wpd_ws_options = get_option( 'wpum_options_name' );
                //echo '<pre>'; var_dump($wpd_ws_options); echo '</pre>'; exit('here');
			?>
		<!-- beginning of the settings meta box -->	
			<div id="wpd-ws-settings" class="post-box-container">
			
				<div class="metabox-holder">	
			
					<div class="meta-box-sortables ui-sortable">
			
						<div id="settings" class="postbox">	
			
							<div class="handlediv" title="<?php echo esc_html__( 'Click to toggle', 'wd-live-posts-update' ) ?>"><br /></div>
			
								<!-- settings box title -->					
								<h3 class="hndle">					
									<span style="vertical-align: top;"><?php echo esc_html__( 'Live Blog Posts Setting Page', 'wd-live-posts-update' ) ?></span>					
								</h3>
			
								<div class="inside">			

									<table class="form-table wpd-ws-settings-box"> 
										<tbody>
							
											<tr>
												<td colspan="2">
													<input type="submit" class="button-primary wpd-ws-settings-save" name="wpd_ws_settings_save" class="" value="<?php echo esc_html__( 'Save Changes', 'wd-live-posts-update' ) ?>" />
												</td>
											</tr>
											<tr>
												<th scope="row">
													<label><strong><?php echo esc_html__( 'Select Post Type :', 'wd-live-posts-update' ) ?></strong></label>
												</th>
												<td>
												<?php

												$selected_post_type = isset($wpd_ws_options['custom_post_type_option']) ? $wpd_ws_options['custom_post_type_option'] : [];
												$post_types = get_post_types(['public' => true, 'show_in_nav_menus' => true], 'objects');
												//echo '<pre>'; var_dump(get_option('custom_post_type_option')); echo '</pre>'; exit('here');
												echo '<select name="wpum_options_name[custom_post_type_option][]" id="custom_post_type_option" multiple>';
												foreach ($post_types as $post_type) {
													if ($post_type->name !== 'page') {
														$selected = in_array($post_type->name, $selected_post_type) ? 'selected' : '';
                                                    	echo '<option value="' . esc_attr( $post_type->name ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $post_type->label ) . '</option>';
														
													}
												}
												echo '</select>';
												?>
												<br>
												<span class="description"><?php echo esc_html__( 'Select posts type which you want add live post', 'wd-live-posts-update' ) ?></span>
												</td>
											 </tr>

											<tr>
												<th scope="row">
													<label><strong><?php echo esc_html__( 'Live Post Area Title :', 'wd-live-posts-update' ) ?></strong></label>
												</th>
												<td><input type="text" id="wpd-ws-settings-title" name="wpum_options_name[title]" value="<?php echo esc_attr( $wpd_ws_options['title'] ); ?>" size="63" /><br />
													<span class="description"><?php echo esc_html__( 'Enter a live post upsate area heading.', 'wd-live-posts-update' ) ?></span>
												</td>
											</tr>

											<tr>
												<th scope="row">
													<label><strong><?php echo esc_html__( 'Recent Live Post widget Shortcode :', 'wd-live-posts-update' ) ?></strong></label>
												</th>
												<td><input type="text" id="wpd-ws-settings-shortcode" name="shortcode" value="[wpum_livepost_shortcode number_of_posts='5']" size="63" /><br />
													<span class="description"><?php echo esc_html__( 'Copy this shortcode for display of recent live posts.', 'wd-live-posts-update' ) ?></span>
												</td>
											</tr>
									
											<tr>
												<td colspan="2">
													<input type="submit" class="button-primary wpd-ws-settings-save" name="wpd_ws_settings_save" class="" value="<?php echo esc_html__( 'Save Changes', 'wd-live-posts-update' ) ?>" />
												</td>
											</tr>
									
							
										</tbody>
									</table>
						
							</div><!-- .inside -->
				
						</div><!-- #settings -->
			
					</div><!-- .meta-box-sortables ui-sortable -->
			
				</div><!-- .metabox-holder -->
			
			</div><!-- #wps-settings-general -->
			
		<!-- end of the settings meta box -->		

		</form><!-- end of the plugin options form -->
	
	</div><!-- .end of wrap -->