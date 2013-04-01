<?php
/*
Plugin Name: Post Images HTML Resizer
Plugin URI: http://www.meow.fr/post-images-html-resizer
Description: Rewrite the HTML of the images, using the correct sizes.
Version: 0.1
Author: Jordy Theiller
Author URI: http://www.meow.fr
License: GPL2
*/

/*  Copyright 2011  

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Guess the wp-content and plugin urls/paths
*/
// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


if (!class_exists('PostImagesHtmlResizer')) {
    class PostImagesHtmlResizer {
        //This is where the class variables go, don't forget to use @var to tell what they're for
        /**
        * @var string The options string name for this plugin
        */
        var $optionsName = 'post-images-html-resizer_options';
        
        /**
        * @var string $localizationDomain Domain used for localization
        */
        var $localizationDomain = "post-images-html-resizer";
        
        /**
        * @var string $pluginurl The path to this plugin
        */ 
        var $thispluginurl = 'http://wordpress.org/extend/plugins/post-images-html-resizer/';
        /**
        * @var string $pluginurlpath The path to this plugin
        */
        var $thispluginpath = '';
            
        /**
        * @var array $options Stores the options for this plugin
        */
        var $options = array();
        
        //Class Functions
        /**
        * PHP 4 Compatible Constructor
        */
        function PostImagesHtmlResizer(){$this->__construct();}
        
        /**
        * PHP 5 Constructor
        */        
        function __construct(){
        	if ( ! function_exists( 'admin_url' ) )
				return false;
        
            //Language Setup
            $locale = get_locale();
            $mo = dirname(__FILE__) . "/languages/" . $this->localizationDomain . "-".$locale.".mo";
            load_textdomain($this->localizationDomain, $mo);

            //"Constants" setup
            $this->thispluginurl = PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)).'/';
            $this->thispluginpath = PLUGIN_PATH . '/' . dirname(plugin_basename(__FILE__)).'/';
            
            //Initialize the options
            //This is REQUIRED to initialize the options when the plugin is loaded!
            $this->getOptions();
            
            //Actions        
            add_action( "admin_menu", array( &$this, "admin_menu_link" ) );
			add_action( "admin_menu", array( &$this, 'add_admin_menu' ) );
            add_action( "admin_action_post_images_html_resizer", array( &$this, 'bulk_action_handler' ) );
            
            
            //Filters
			add_filter( "post_row_actions", array(&$this, "add_media_row_action" ), 10, 2 );
        }
        
        function html_resize() {
        
        	if ( ! empty( $_POST['post-images-html-resizer'] ) || ! empty( $_REQUEST['ids'] ) ) {
        		if ( !current_user_can( 'manage_options' ) )
					wp_die( __( 'Cheatin&#8217; uh?' ) );
				if ( ! empty( $_REQUEST['ids'] ) ) {
					$posts = array_map( 'intval', explode( ',', trim( $_REQUEST['ids'], ',' ) ) );
				}
				else {
					wp_die("No IDs.");
				}
				
				
				// WHAT IS THE IMAGE SIZE?
				$image_size_class = 'size-full';
				$image_size = 'large';
				if ($this->options['post-images-html-resizer_size'] != null)
					$image_size = $this->options['post-images-html-resizer_size'];
				if ($image_size = 'large')
					$image_size_class = 'size-full';
				else
					$image_size_class = '';
				
				foreach ($posts as &$id) {
					$count_errors = 0;
					$count_modified = 0;
					$count = 0;
					$post = get_post($id);
					print("<h2>" . $post->post_title . "</h2>");
					print("<ul>");
					preg_match_all('%(\[caption id="[^<>]*" align="([a-z]{0,16})" width="[0-9]{0,6}" caption="([^<>]*)"\])?<a href="[^<>]*"><img[^<>]*class="[^<>]*(wp-image-([0-9]{1,20})[ ]*(aligncenter|alignleft|alignright|))[^<>]*" [^<>]*alt="([^<>"]*)"[^<>]*/></a>(\[/caption\])?%', $post->post_content, $result, PREG_SET_ORDER);
					for ($matchi = 0; $matchi < count($result); $matchi++) {
						$has_caption = $result[$matchi][2] != "" ? true: false;
						$caption_align = $result[$matchi][2];
						$caption_content = $result[$matchi][3];
						$image_id = $result[$matchi][5];
						$image_align = $result[$matchi][6];
						$image_alt = $result[$matchi][7];
						$url = wp_get_attachment_url( $image_id );
						$att = wp_get_attachment_image_src( $image_id, 'large' );
						$src = $att[0];
						$width = $att[1];
						$height = $att[2];
						if (!$att) {
							print("<li><b>IMG ID</b> " . $image_id . " - <span style='color: red;'>NOT AVAILABLE</span></li>");
							$count_errors++;
						} else {
							//print("<li><b>IMG ID</b> " . $image_id . " - CAPTION: " . ($caption ? "YES": "NO") . 
							//	", W: " . $att[1] . ", H: " . $att[2] . "</li>");
							$caption = sprintf("[caption id=\"%s\" align=\"%s\" width=\"%s\" caption=\"%s\"]", 
								"attachment_" . $image_id, $caption_align, $width, $caption_content);
							$image = sprintf("<a href=\"%s\"><img class=\"%s\" src=\"%s\" alt=\"%s\" width=\"%s\" height=\"%s\"/></a>",
								$url, $image_size_class . " wp-image-" . $image_id . (strlen($image_align) > 0 ? " ". $image_align : ""), 
								$src, $image_alt, $width, $height);
							$html = sprintf("%s%s%s", $has_caption ? $caption : "", $image, $has_caption ? "[/caption]" : "");
							//print("<p>ORIGINAL:<br />" . htmlentities($result[$matchi][0]) . "</p>");
							//print("<p>REFRESHED:<br />" . htmlentities($html) . "</p>");
							if (strcmp($result[$matchi][0], $html) != 0) {
								$post->post_content = str_replace($result[$matchi][0], $html, $post->post_content);
								$count_modified++;
							}
						}
						$count++;
					}
					wp_update_post($post) or die($post->errstr);
					print("</ul>");
					
					if ($count_errors > 0)
						printf("%d / %d image(s) were modified but %d haven't any metadata information (Wordpress DB).",
							$count_modified, $count, $count_errors);
					else
						printf("%d / %d image(s) were modified.",
							$count_modified, $count);
					
        		}
        		print("<p><a href=\"javascript:history.go(-1)\">Go back</a></p>");
        	}
        }
        
		/**
        * Register the management page.
        * @return void
        */
		function add_admin_menu() {
			$this->menu_id = add_management_page( __( 'Rewrite IMGs', 'post-images-html-resizer' ), 
				__( '', '' ), 'manage_options', 'post-images-html-resizer', array(&$this, 'html_resize') );		
		}
        
		/**
        * Retrieves the actions.
        * @return array
        */
		function add_media_row_action( $actions, $post ) {
			//if ( 'image/' != substr( $post->post_mime_type, 0, 6 ) )
			//	return $actions;
			$url = wp_nonce_url( admin_url( 'tools.php?page=post-images-html-resizer&goback=1&ids=' . $post->ID ), 'post-images-html-resizer' );
			$actions['post_images_html_resizer'] = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( __( "Rewrite IMGs", 'post-images-html-resizer' ) ) . '">' . __( 'Rewrite IMGs', 'post-images-html-resizer' ) . '</a>';
			return $actions;
		}
		
        /**
        * Retrieves the plugin options from the database.
        * @return array
        */
        function getOptions() {
            //Don't forget to set up the default options
            if (!$theOptions = get_option($this->optionsName)) {
                $theOptions = array('default'=>'options');
                update_option($this->optionsName, $theOptions);
            }
            $this->options = $theOptions;
            
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //There is no return here, because you should use the $this->options variable!!!
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }
        /**
        * Saves the admin options to the database.
        */
        function saveAdminOptions(){
            return update_option($this->optionsName, $this->options);
        }
        
        /**
        * @desc Adds the options subpanel
        */
        function admin_menu_link() {
            //If you change this from add_options_page, MAKE SURE you change the filter_plugin_actions function (below) to
            //reflect the page filename (ie - options-general.php) of the page your plugin is under!
            add_options_page('Post Images HTML Resizer', 'Post Images HTML Resizer', 10, basename(__FILE__), array(&$this,'admin_options_page'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );
        }
        
        /**
        * @desc Adds the Settings link to the plugin activate/deactivate page
        */
        function filter_plugin_actions($links, $file) {
           //If your plugin is under a different top-level menu than Settiongs (IE - you changed the function above to something other than add_options_page)
           //Then you're going to want to change options-general.php below to the name of your top-level page
           $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
           array_unshift( $links, $settings_link ); // before other links

           return $links;
        }
        
        /**
        * Adds settings/options page
        */
        function admin_options_page() { 
            if($_POST['post-images-html-resizer_save']) {
                if (! wp_verify_nonce($_POST['_wpnonce'], 'post-images-html-resizer-update-options') ) 
                	die('Whoops! There was a problem with the data you posted. Please go back and try again.'); 
                $this->options['post-images-html-resizer_size'] = $_POST['post-images-html-resizer_size'];                                          
                $this->saveAdminOptions();
                echo '<div class="updated"><p>Modifications saved.</p></div>';
            }
            $thumbnail = ($this->options['post-images-html-resizer_size'] == 'thumbnail' ? " selected" : "");
            $medium = ($this->options['post-images-html-resizer_size'] == 'medium' ? " selected" : "");
            $large = ($this->options['post-images-html-resizer_size'] == 'large' ? " selected" : "");
			?>                                   
                <div class="wrap">
                <h2>Post Images HTML Resizer</h2>
                <form method="post" id="post-images-html-resizer_options">
                <?php wp_nonce_field('post-images-html-resizer-update-options'); ?>
                    <table width="100%" cellspacing="2" cellpadding="5" class="form-table"> 
                        <tr valign="top"> 
                            <th width="33%" scope="row"><?php _e('Resize using settings from:', $this->localizationDomain); ?></th> 
                            <td>
                            	<select name="post-images-html-resizer_size" id="post-images-html-resizer_size">
                            		<?php print("<option value=\"thumbnail\"" . $thumbnail . ">Thumbnail</option>"); ?>
                            		<?php print("<option value=\"medium\"" . $medium . ">Medium</option>"); ?>
                            		<?php print("<option value=\"large\"" . $large . ">Large</option>"); ?>
                            	</select>
                        </td> 
                        </tr>
                        <tr>
                            <th colspan=2><input type="submit" name="post-images-html-resizer_save" value="Save" /></th>
                        </tr>
                    </table>
                </form>
                <?php
        }     
        
  } //End Class
} //End if class exists statement

//instantiate the class
if (class_exists('PostImagesHtmlResizer')) {
    $postImagesHtmlResizer_var = new PostImagesHtmlResizer();
}
?>