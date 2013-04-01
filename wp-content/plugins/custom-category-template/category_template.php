<?php
/*
Plugin Name: Custom Category Template
Plugin URI: http://en.bainternet.info
Description: This plugin lets you select a specific template for a category, just like pages
Version: 0.4
Author: Bainternet
Author URI: http://en.bainternet.info
*/
/*  Copyright 2012 Ohad Raz aKa BaInternet  (email : admin@bainternet.info)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,this
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/* Disallow direct access to the plugin file */
if (basename($_SERVER['PHP_SELF']) == basename (__FILE__)) {
	die('Sorry, but you cannot access this page directly.');
}

if (!class_exists('Custom_Category_Template')){
	/**
	 *  @author Ohad Raz <admin@bainternet.info>
	 *  @access public
	 *  @version 0.1
	 *  
	 */
	class Custom_Category_Template{
		
		/**
		 *  class constructor
		 *  
		 *  @since 0.1
		 *  @author Ohad Raz <admin@bainternet.info>
		 *  @access public
		 *  
		 *  @return void
		 */
		public function __construct()
		{
			//do the template selection
			add_filter( 'category_template', array($this,'get_custom_category_template' ));
			//add extra fields to category NEW/EDIT form hook
			add_action ( 'edit_category_form_fields', array($this,'category_template_meta_box'));
			add_action( 'category_add_form_fields', array( &$this, 'category_template_meta_box') );
			
			
			// save extra category extra fields hook
			add_action( 'created_category', array( &$this, 'save_category_template' ));
			add_action ( 'edited_category', array($this,'save_category_template'));
			//plugin row links
			add_filter( 'plugin_row_meta', array($this,'_my_plugin_links'), 10, 2 );
			//extra action on constructor
			do_action('Custom_Category_Template_constructor',$this);
		}

		
		/**
		 * category_template_meta_box add extra fields to category edit form callback function
		 * 
		 *  @since 0.1
		 *  @author Ohad Raz <admin@bainternet.info>
		 *  @access public
		 *  
		 *  @param  (object) $tag  
		 *  
		 *  @return void
		 * 
		 */
		public function category_template_meta_box( $tag ) {
		    $t_id = $tag->term_id;
		    $cat_meta = get_option( "category_templates");
		    $template = isset($cat_meta[$t_id]) ? $cat_meta[$t_id] : false;
			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="cat_Image_url"><?php _e('Category Template'); ?></label></th>
				<td>
					<select name="cat_template" id="cat_template">
						<option value='default'><?php _e('Default Template'); ?></option>
						<?php page_template_dropdown($template); ?>
					</select>
					<br />
			            <span class="description"><?php _e('Select a specific template for this category'); ?></span>
			    </td>
			</tr>
			<?php
			do_action('Custom_Category_Template_ADD_FIELDS',$tag);
		}


		/**
		 * save_category_template save extra category extra fields callback function
		 *  
		 *  @since 0.1
		 *  @author Ohad Raz <admin@bainternet.info>
		 *  @access public
		 *  
		 *  @param  int $term_id 
		 *  
		 *  @return void
		 */
		public function save_category_template( $term_id ) {
		    if ( isset( $_POST['cat_template'] )) {
		        $cat_meta = get_option( "category_templates");
		        $cat_meta[$term_id] = $_POST['cat_template'];
		        update_option( "category_templates", $cat_meta );
		        do_action('Custom_Category_Template_SAVE_FIELDS',$term_id);
		    }
		}

		/**
		 * get_custom_category_template handle category template picking
		 * 
		 *  @since 0.1
		 *  @author Ohad Raz <admin@bainternet.info>
		 *  @access public
		 *  
		 *  @param  string $category_template 
		 *  
		 *  @return string category template
		 */
		function get_custom_category_template( $category_template ) {
			$cat_ID = absint( get_query_var('cat') );
			$cat_meta = get_option('category_templates');
			if (isset($cat_meta[$cat_ID]) && $cat_meta[$cat_ID] != 'default' ){
				$temp = locate_template($cat_meta[$cat_ID]);
				if (!empty($temp))
					return apply_filters("Custom_Category_Template_found",$temp);
			}
		    return $category_template;
		}

		/**
		 * _my_plugin_links
		 * @since 0.1
		 * @author Ohad Raz <admin@bainternet.info>
		 * @param  array $links 
		 * @param  File $file  
		 * @return array      
		 */
		public function _my_plugin_links($links, $file) {
		    $plugin = plugin_basename(__FILE__); 
		    if ($file == $plugin) // only for this plugin
		            return array_merge( $links,
		        array( '<a href="http://en.bainternet.info/category/plugins">' . __('Other Plugins by this author' ) . '</a>' ),
		        array( '<a href="http://wordpress.org/support/plugin/custom-category-template">' . __('Plugin Support') . '</a>' ),
		        array( '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K4MMGF5X3TM5L" target="_blank">' . __('Donate') . '</a>' )
		    );
		    return $links;
		}
	}//end class
}//end if

$cat_template = new Custom_Category_Template();