<?php
/*
Plugin Name: Category Grid View Gallery
Plugin URI: http://evilgenius.anshulsharma.in/cgview
Description: Display your blog posts differently. This plugin provides a new way to build your Portfolios and Photo Galleries. People who want to show their work using a gallery/portfolio dont have to exclusively install a plugin and upload images on it. Now, you can just upload your work as a blog post every now and then and this plugin will take care of the rest. Usage: Put the [cgview id='xxx'] where you want it to appear. Lots of customizations.
Version: 2.3.1
Author: Anshul Sharma
Author URI: http://anshulsharma.in/
*/

/* Copyright 2012  Anshul Sharma  (email : contact@anshulsharma.in)

This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('Sorry, Dude. You are not allowed to call this page directly.'); }



require_once 'includes/CatGridView.php';
if(is_admin()) {
require_once 'includes/Settings.php';
require_once 'includes/Options.php';
}
define('PLUGIN_AUTHOR', 'Anshul Sharma');
define('PLUGIN_VERSION', '2.3.1');
define('AUTHOR_URI','http://evilgenius.anshulsharma.in/');
define('PLUGIN_URI','http://evilgenius.anshulsharma.in/cgview');

class CatGrid{
    /* Get the parameters from shortcodes,
	set defaults and initialize the object
	Inspired by List category posts plugin by Fernando Briano */
    function cat_grid($atts, $content = null) {
            $atts = shortcode_atts(array(
                            'id' => '0',
                            'name' => '',
                            'orderby' => 'date',
                            'order' => 'desc',
                            'num' => '-1',
                            'excludeposts' => '0',
                            'offset' => '0',
							'tags' => '',
                            'size' => 'thumbnail',
							'quality' => '75',
							'showtitle' => 'hover',
							'lightbox' => '1',
							'paginate' => '0',
							'customfield' => '',
							'customfieldvalue' => '',
							'title' => ''
                    ), $atts);

       		global $cg_output;
            $cg_output = new CatGridView($atts);
	        return $cg_output->display();
			
		

    }

}

function cg_set_locations(){ 
 /*URL and DIR locations for backwards compatibility*/
 global $wp_content_url,$wp_content_dir,$wp_plugin_url,$wp_plugin_dir,$wpmu_plugin_url,$wpmu_plugin_dir,$cg_url,$cg_dir;
 if ( ! function_exists( 'is_ssl' ) ) {
  function is_ssl() {
   if ( isset($_SERVER['HTTPS']) ) {
    if ( 'on' == strtolower($_SERVER['HTTPS']) )
     return true;
    if ( '1' == $_SERVER['HTTPS'] )
     return true;
   } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
    return true;
   }
   return false;
  }
 }
 if ( version_compare( get_bloginfo( 'version' ) , '3.0' , '<' ) && is_ssl() ) {
  $wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
 
 } else {
  $wp_content_url = get_option( 'siteurl' );
 }
$wp_content_url .= '/wp-content';
$wp_content_dir = ABSPATH . 'wp-content';
$wp_plugin_url = $wp_content_url . '/plugins';
$wp_plugin_dir = $wp_content_dir . '/plugins';
$wpmu_plugin_url = $wp_content_url . '/mu-plugins';
$wpmu_plugin_dir = $wp_content_dir . '/mu-plugins';
$cg_url = $wp_plugin_url . '/category-grid-view-gallery';
$cg_dir = $wp_plugin_dir . '/category-grid-view-gallery';
define('CGVIEW_URL', $cg_url);
define('CGVIEW_DIR', $cg_dir);
}
 
/*URL and DIR locations end*/

function enqueue_cg_styles() {
	global $cg_url,$cg_dir;
	$cgStyleUrl = $cg_url . '/css/style.css';
        $cgStyleFile = $cg_dir . '/css/style.css';
        if ( file_exists($cgStyleFile) ) {
			if(!is_admin()){
            wp_register_style('CatGridStyleSheets', $cgStyleUrl);
            wp_enqueue_style( 'CatGridStyleSheets');
			}
        }

 } 

function enqueue_cg_scripts() {
	global $cg_url,$cg_dir,$cg_output;
	if(!is_admin()){
	    if ( file_exists($cg_dir . '/js/cgview.js') ) {
      		wp_enqueue_script( 'CatGridjs', $cg_url . '/js/cgview.js', array( 'jquery' ));
        }
		if ( file_exists($cg_dir . '/js/jquery.colorbox-min.js')) {
      		wp_enqueue_script( 'Colorbox', $cg_url . '/js/jquery.colorbox-min.js', array( 'jquery' ));
        }
		if ( file_exists($cg_dir . '/js/easypaginate.min.js')) {
      		wp_enqueue_script( 'EasyPaginate', $cg_url . '/js/easypaginate.min.js', array( 'jquery' ));
        }
	}
}     

add_action( 'wp_print_scripts', 'enqueue_cg_scripts' );
add_action( 'wp_print_styles', 'enqueue_cg_styles' );
add_action( 'init', 'cg_set_locations' );
add_action( 'wp_print_footer_scripts', 'cg_init_js' );

add_shortcode( 'cgview', array('CatGrid', 'cat_grid') );
