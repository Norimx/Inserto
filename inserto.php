<?php
/**
 * Plugin Name: Inserto 
 * Description: Allows you to insert code or text in the header or footer
 * Version: 2.5.9
 * Author: Newemage
 * Author URI: https://newemage.com
 * License: GPLv2 or later
 */
/*
  @package 
  Copyright (C) 2013 - 2028 

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('INSERTO_PLUGIN_DIR',str_replace('\\','/',dirname(__FILE__)));

if ( !class_exists( 'Inserto' ) ) {

	class Inserto {

		function __construct() {

			add_action( 'wp_head', array( &$this, 'wp_head' ) );
			add_action( 'wp_footer', array( &$this, 'wp_footer' ) );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			add_action( 'admin_footer', array( &$this, 'admin_footer' ) );
			add_action( 'login_footer', array( &$this, 'admin_footer' ) );

		}


		function admin_init() {

			register_setting( 'header-and-footer-scripts', 'inserto_admin', 'trim' );
			register_setting( 'header-and-footer-scripts', 'inserto_header', 'trim' );
			register_setting( 'header-and-footer-scripts', 'inserto_footer', 'trim' );

		
			foreach ( get_post_types( '', 'names' ) as $type ) {
				add_meta_box('inserto_all_post_meta', 'Insert Script', 'inserto_meta_setup', $type, 'normal', 'low');
			}

			add_action('save_post','inserto_meta_save');

			add_shortcode( 'inserto', function(){
				$text = get_option( 'inserto_short', '' );
				$text = do_shortcode( $text );

				echo $text."\n";
			} );
		}

		function admin_menu() {
			$page = add_submenu_page( 'options-general.php', 'Inserto', 'Inserto', 'manage_options', __FILE__, array( &$this, 'inserto_options_panel' ) );
			add_submenu_page( 'index.php', 'Inserto', 'Inserto', 'manage_options', __FILE__, array( &$this, 'inserto_options_panel' ) );
			}

		function wp_head() {
			$meta = get_option( 'inserto_header', '' );
			if ( $meta != '' ) 
				echo $meta."\n";
			

		}

		function wp_footer() {

			$text = get_option( 'inserto_footer', '' );
			if ( $text != '' && !is_admin() && !is_feed() && !is_robots() && !is_trackback() ) {
				$text = do_shortcode( $text );

				echo $text."\n";
			}

		

			$text_meta = get_post_meta( get_the_ID(), '_inpost_head_script' , TRUE );
			if ( $text_meta != '' ) 
				echo $text_meta['synth_header_script']."\n";
			
      
		}
		function admin_footer() {
				$text = get_option( 'inserto_admin', '' );
			if ( $text != '' )
			echo $text."\n";

		}


		function inserto_options_panel() {
				// Load options page
				require_once(INSERTO_PLUGIN_DIR . '/inc/options.php');
		}
	}

	function inserto_meta_setup() {
		global $post;

	
		$meta = get_post_meta($post->ID,'_inpost_head_script',TRUE);


		include_once(INSERTO_PLUGIN_DIR . '/inc/meta.php');


		echo '<input type="hidden" name="inserto_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
	}

	function inserto_meta_save($post_id) {

		if ( ! isset( $_POST['inserto_meta_noncename'] )
			|| !wp_verify_nonce($_POST['inserto_meta_noncename'],__FILE__)) return $post_id;


		if ( $_POST['post_type'] == 'page' ) {

			if (!current_user_can('edit_page', $post_id)) 
				return $post_id;

		} else {

			if (!current_user_can('edit_post', $post_id)) 
				return $post_id;

		}

		$current_data = get_post_meta($post_id, '_inpost_head_script', TRUE);

		$new_data = $_POST['_inpost_head_script'];

		inserto_meta_clean($new_data);

		if ($current_data) {

			if (is_null($new_data)) delete_post_meta($post_id,'_inpost_head_script');

			else update_post_meta($post_id,'_inpost_head_script',$new_data);

		} elseif (!is_null($new_data)) {

			add_post_meta($post_id,'_inpost_head_script',$new_data,TRUE);

		}

		return $post_id;
	}

	function inserto_meta_clean(&$arr) {

		if (is_array($arr)) {

			foreach ($arr as $i => $v) {

				if (is_array($arr[$i])) {
					inserto_meta_clean($arr[$i]);

					if (!count($arr[$i])) {
						unset($arr[$i]);
					}

				} else {

					if (trim($arr[$i]) == '') {
						unset($arr[$i]);
					}
				}
			}

			if (!count($arr)) {
				$arr = NULL;
			}
		}
	}

	$inserto = new Inserto();
}

if(isset($_GET['page']) && strstr($_GET['page'],'inserto'))
add_action('admin_enqueue_scripts', 'codemirror_enqueue_scripts');
 
function codemirror_enqueue_scripts($hook) {
  $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/html'));
  wp_localize_script('jquery', 'cm_settings', $cm_settings);
 
  wp_enqueue_script('wp-theme-plugin-editor');
  wp_enqueue_style('wp-codemirror');
}

add_filter('body_class','add_role_to_body');
function add_role_to_body($classes) {
$current_user = new WP_User(get_current_user_id());
$user_role = array_shift($current_user->roles);
$classes[] = 'role-'. $user_role;
$slug = trim(str_replace('/','_', parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ),'_');
$classes[] = 'slug-'. $slug;
return $classes;
}
add_filter('admin_body_class','add_role_to_admbody');
function add_role_to_admbody($classes) {
$current_user = new WP_User(get_current_user_id());
$user_role = array_shift($current_user->roles);
$new_classes = 'role-'. $user_role; 
return $classes . ' ' . $new_classes  . ' ';
}
