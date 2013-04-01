<?php
/*
Plugin Name: Category Pages & Posts Shortcodes
Plugin URI: http://wordpress.org/extend/plugins/page2cat/
Description: Display posts/pages content (or lists of posts) with handy shortcodes, and map categories to pages directly in the admin area.
Version: 3.0.6
Author: SWERgroup
Author URI: http://swergroup.com/
License: GPL2
*/

/*  
    SomeRight 2012+  Paolo Tresso / SWERgroup  (email : plugins@swergroup.com)
    Rewrite of pixline's "Category Page" plugin, GPL2 (2007)
    Plugin based on Empty Plugin Template 0.1.1.2 (http://1manfactory.com/ept)

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
?><?php

// some definition we will use
define( 'SWER_PUGIN_NAME', 'Category Pages & Posts Shortcodes');
define( 'SWER_PLUGIN_DIRECTORY', 'aptools');
define( 'SWER_CURRENT_VERSION', '3.0.6' );
define( 'SWER_LOGPATH', str_replace('\\', '/', WP_CONTENT_DIR).'/swer-logs/');
define( 'SWER_I18N_DOMAIN', 'aptools' );

// load language files
function aptools_set_lang_file() {
	# set the language file
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
		if (@file_exists($moFile) && is_readable($moFile)) {
			load_textdomain(SWER_I18N_DOMAIN, $moFile);
		}

	}
}
aptools_set_lang_file();


register_activation_hook(__FILE__, 'aptools_activate');
register_deactivation_hook(__FILE__, 'aptools_deactivate');
register_uninstall_hook(__FILE__, 'aptools_uninstall');

// activating the default values
function aptools_activate() {
    // <3.0 cleaning
    
    delete_option('pixline_aptools_version');
	delete_option('p2c_use_empty');
    delete_option('p2c_show_used_pages');
    delete_option('p2c_catlist_limit');
    delete_option('p2c_catlist_title');
    delete_option('p2c_excerpt_settings');
    delete_option('p2c_post_settings');
    delete_option('p2c_excerpt_length');
    delete_option('p2c_use_thumbnail');
    delete_option('p2c_img_class');
    delete_option('p2c_title_class');
    delete_option('p2c_thumbnail_size');
    delete_option('p2c_use_img');
    
    $default_options = array(
        'version'   =>  SWER_CURRENT_VERSION,
        'template'  => array(),
        'shortcode' => array()
    );
        
	add_option('aptools_options', json_encode($default_options) );
}

// deactivating
function aptools_deactivate() {
	// needed for proper deletion of every option
	delete_option('aptools_options');
}

// uninstalling
function aptools_uninstall() {
	# delete all data stored
	delete_option('aptools_options');
	// delete log files and folder only if needed
	if (function_exists('aptools_deleteLogFolder')) aptools_deleteLogFolder();
}


include('page2cat_class.php');

?>