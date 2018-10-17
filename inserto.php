<?php
/**
 * Plugin Name: Inserto 
 * Description: Allows you to insert code or text in the header or footer
 * Version: 2.1.2
 * Author: Newemage
 * Author URI: https://newemage.com
 * License: GPLv2 or later
 */
/*
  @package 
  Copyright (C) 2013 - 2018
  by nodws.com follow me @nodws 

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

define('SHFS_PLUGIN_DIR',str_replace('\\','/',dirname(__FILE__)));

if ( !class_exists( 'HeaderAndFooterScripts' ) ) {

	class HeaderAndFooterScripts {

		function __construct() {

			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			add_action( 'wp_head', array( &$this, 'wp_head' ) );
			add_action( 'wp_footer', array( &$this, 'wp_footer' ) );

		}


		function admin_init() {

			register_setting( 'header-and-footer-scripts', 'shfs_insert_header', 'trim' );
			register_setting( 'header-and-footer-scripts', 'shfs_insert_footer', 'trim' );

		
			foreach ( get_post_types( '', 'names' ) as $type ) {
				add_meta_box('shfs_all_post_meta', 'Insert Script', 'shfs_meta_setup', $type, 'normal', 'low');
			}

			add_action('save_post','shfs_post_meta_save');
		}

		function admin_menu() {
			$page = add_submenu_page( 'options-general.php', 'Inserto', 'Insert Scripts', 'manage_options', __FILE__, array( &$this, 'shfs_options_panel' ) );
			}

		function wp_head() {
			$meta = get_option( 'shfs_insert_header', '' );
			if ( $meta != '' ) {
				echo $meta, "\n";
			}

		}

		function wp_footer() {
			if ( !is_admin() && !is_feed() && !is_robots() && !is_trackback() ) {
				$text = get_option( 'shfs_insert_footer', '' );
				$text = convert_smilies( $text );
				$text = do_shortcode( $text );

				if ( $text != '' ) {
					echo $text, "\n";
				}
			}

$shfs_post_meta = get_post_meta( get_the_ID(), '_inpost_head_script' , TRUE );
			if ( $shfs_post_meta != '' ) {
				echo $shfs_post_meta['synth_header_script'], "\n";
			}
      
		}

		function shfs_options_panel() {
				// Load options page
				require_once(SHFS_PLUGIN_DIR . '/inc/options.php');
		}
	}

	function shfs_meta_setup() {
		global $post;

	
		$meta = get_post_meta($post->ID,'_inpost_head_script',TRUE);


		include_once(SHFS_PLUGIN_DIR . '/inc/meta.php');


		echo '<input type="hidden" name="shfs_post_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
	}

	function shfs_post_meta_save($post_id) {

		if ( ! isset( $_POST['shfs_post_meta_noncename'] )
			|| !wp_verify_nonce($_POST['shfs_post_meta_noncename'],__FILE__)) return $post_id;


		if ( $_POST['post_type'] == 'page' ) {

			if (!current_user_can('edit_page', $post_id)) 
				return $post_id;

		} else {

			if (!current_user_can('edit_post', $post_id)) 
				return $post_id;

		}

		$current_data = get_post_meta($post_id, '_inpost_head_script', TRUE);

		$new_data = $_POST['_inpost_head_script'];

		shfs_post_meta_clean($new_data);

		if ($current_data) {

			if (is_null($new_data)) delete_post_meta($post_id,'_inpost_head_script');

			else update_post_meta($post_id,'_inpost_head_script',$new_data);

		} elseif (!is_null($new_data)) {

			add_post_meta($post_id,'_inpost_head_script',$new_data,TRUE);

		}

		return $post_id;
	}

	function shfs_post_meta_clean(&$arr) {

		if (is_array($arr)) {

			foreach ($arr as $i => $v) {

				if (is_array($arr[$i])) {
					shfs_post_meta_clean($arr[$i]);

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

	$shfs_header_and_footer_scripts = new HeaderAndFooterScripts();
}
