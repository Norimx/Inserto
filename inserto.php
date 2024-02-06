<?php
/**
 * Plugin Name: Inserto 
 * Description: Allows you to insert code or text in the header or footer
 * Version: 2.6.1
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

			register_setting( 'inserto-scripts', 'inserto_admin',['sanitize_callback'=>'base64_encode']);
			register_setting( 'inserto-scripts', 'inserto_header',['sanitize_callback'=>'base64_encode']);
			register_setting( 'inserto-scripts', 'inserto_footer',['sanitize_callback'=>'base64_encode']);
            register_setting( 'inserto-scripts', 'inserto_short',['sanitize_callback'=>'base64_encode']);
		
			foreach ( get_post_types( '', 'names' ) as $type ) {
				add_meta_box('inserto_all_post_meta', 'Insert Script', 'inserto_meta_setup', $type, 'normal', 'low');
			}

			add_action('save_post','inserto_meta_save');
		}

		function admin_menu() {
			$page = add_submenu_page( 'options-general.php', 'Inserto', 'Inserto', 'manage_options', __FILE__, array( &$this, 'inserto_options_panel' ) );
			add_submenu_page( 'index.php', 'Inserto', 'Inserto', 'manage_options', __FILE__, array( &$this, 'inserto_options_panel' ) );
			}

		function wp_head() {
			$text = is64(get_option( 'inserto_header', '' ));
			if ( $text != '' ) 
				echo "\n\n<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  START OF HEAD  ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n {$text} \n\n<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  END OF HEAD  ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n";
			

		}

		function wp_footer() {
            global $post;
			$text = is64(get_option( 'inserto_footer', '' ));
			if ( $text != '' && !is_admin() && !is_feed() && !is_robots() && !is_trackback() ) {
				$text = do_shortcode( $text );

				echo "\n\n<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  START OF FOOT  ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n {$text} \n\n<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  END OF FOOT  ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n";
			 
			}

		
             if($post)  { 
			$post_meta = get_post_meta($post->ID);
			$text_meta =$post_meta['_inpost_head_script'][0];// get_post_meta($post->ID, '_inpost_head_script' , TRUE );
		 
			if ( !empty($text_meta)) 
			{	$text =   base64_decode($text_meta);
				echo "\n\n<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  START OF META  ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n {$text} \n\n<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  END OF META  ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n";
			}
             }//is post
      
		}
		function admin_footer() {
				$text = is64(get_option( 'inserto_admin' ));
			if ( $text != '' )
			echo $text."\n";
			?><style>#inserto_all_post_meta{
				max-height:40px;
				overflow:hidden;
				transition:all 0.1s
		  }
		  #inserto_all_post_meta:hover{
				max-height:600px
		  }</style>
			<script>
				setTimeout(_=>{
					if(!$)
					var $ = jQuery, ihref= $('#menu-dashboard a[href*="inserto"]').attr('href');
					$('[data-plugin*="inserto"] .row-actions').append(` | <a href="${ihref}">Opciones</a>`);
					
                    					
                    if($('.button[href*="connect-and-activate"]').length>0){
                    $('.button[href*="connect-and-activate"]').after('<a href="./admin.php?page=elementor-license&mode=manually">Manually</a>');}
				},800);
			</script>
			<?
		}


		function inserto_options_panel() {
				// Load options page
				require_once(INSERTO_PLUGIN_DIR . '/options.php');
		}
	}

	function inserto_meta_setup() {

		include_once(INSERTO_PLUGIN_DIR . '/meta.php');


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

	//	inserto_meta_clean($new_data);
		$new_data = base64_encode($new_data);
		if (!empty($current_data)) {
            update_post_meta($post_id,'_inpost_head_script',$new_data);

		}elseif (!empty($current_data) && empty($new_data)) {

			update_post_meta($post_id,'_inpost_head_script','');

		}
        elseif (!empty($new_data)) {

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
		return $arr;
	}

	$inserto = new Inserto();
}


	add_shortcode( 'inserto', function(){
		$text = is64(is64(get_option( 'inserto_short' )));
		$text = do_shortcode( $text );
		echo "\n\n<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  START OF SHORT  ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n {$text} \n\n<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  END OF SHORT  ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n";

	 
	} );

	add_shortcode('credits',function($atts){
	    $a = shortcode_atts( array(
            'copy' => '',
            'msj'  =>  ''
        ), $atts );
		$a['copy'] = isset($a['copy']) ? $a['copy']: 'Todos los derechos reservados'; 
		$a['msj'] = isset($a['msj']) ?$a['msj'] : 'Diseño web por'; 
		return date('Y')."© {$a['copy']} &mdash; {$a['msj']} <a href=https://newemage.com.mx target=_b>Newemage</a>";
		  });

function is64($str64){
 
		$str = base64_decode($str64, true);
		
    	if ($str !== false && base64_encode($str64) === $str) {
         $str64 = $str;
    } elseif($str !== false ){
        $str64 =base64_decode($str64);
    }
	return $str64;
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

add_action('elementor/editor/wp_head',function(){ ?>
	 <style>
		#elementor-panel-state-loading{
			display:none !important
		}
	 </style>
	<? });