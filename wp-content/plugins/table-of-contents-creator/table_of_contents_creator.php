<?php

/*
Plugin Name: Table of Contents Creator
Plugin URI: http://markbeljaars.com/plugins/TOCC-plugin/
Description: Table of Contents Creator (TOCC) automatically generates a dynamic site wide table of contents that is always up-to-date. All entries are navigable making your site very SEO friendly. TOCC can be configured to display static pages, blog entries and forum comments. Another great feature of TOCC is the ability to include anchor tags on any page marked with a special class. This feature allows links to articles, downloads or even other sites to appear within the table of contents as if they are part of your site's navigation. To generate a table of contents, simply include the <code>&lt;!-- toc-creator --&gt;</code> tag on any page, or use the handy page creation feature located on the plugin admin page.
Version: 1.6.4.1
Author: Mark Beljaars
Author URI: http://markbeljaars.com
*/

/*
Copyright (C) 2010 Mark Beljaars, markbeljaars.com
 
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License along with this program.  If not, see http://www.gnu.org/licenses/.
*/

define('TOCC_VERSION', '1.6.4.1'); // used for upgrade options and display purposes
define('TOCC_PLUGIN_PATH', 'wp-content/plugins/' . basename(dirname(__FILE__)));
define('TOCC_FULL_PATH', get_bloginfo('wpurl') . '/' . constant('TOCC_PLUGIN_PATH') . '/');
define('TOCC_TAG', 'toc-creator'); // trigger tag - may be changed to anything
define('TOCC_CSS_FILE', 'tocc.css'); // css file name
define('TOCC_JS_FILE', 'js/tocc.js'); // javascript file name
define('TOCC_ICON_THEMES', 'None,Professional,Square,Bling,Blue,Handdrawn'); // all icon sets
define('TOCC_ALLPOSTS_PAGEID', 'all_posts'); // URL page parameter value to show all posts
define('TOCC_ALLCOMMENTS_PAGEID', 'all_comments'); // URL page parameter value to show all comments
define('TOCC_NUMBER_SENTINEL', '{x}');
define('TOCC_NOOPTION_SENTINEL', 'none');

// hooks required for all users
add_action('plugins_loaded', 'tocc_loaded_hook'); // set defaults on initial load
add_filter('the_content', 'tocc_display_hook'); // check each page for the tocc tag
add_filter('query_vars', 'tocc_parameters_hook'); // enable URL parameters

// the following hooks are only required to be loaded if the current user is a site administrator
if (is_admin()) {
	add_action('admin_menu', 'tocc_menu_hook'); // insert TOCC admin settings into the menu
	add_filter('plugin_action_links', 'tocc_filter_settings_option', 10, 2); // add settings in plugin page
	register_uninstall_hook(__FILE__, 'tocc_uninstall_hook'); // remove options on uninstall
}

/************************
*** HOOKS AND FILTERS ***
************************/

// Initiates after all plugins have been loaded.
function tocc_loaded_hook() {
	
	// text domain must be loaded here to allow default settings to be translated
	load_plugin_textdomain('tocc', constant('TOCC_PLUGIN_PATH'), basename(dirname(__FILE__)));
	
	// this array defines default settings for all options
	global $tocc_defaults;
	$tocc_defaults = array (
		'toc_title' => '<h1>' . __('Table of Contents', 'tocc') . '</h1>',
		'date_format' => __('F j, Y, H:i', 'tocc'),
		'author_prefix' => __('by', 'tocc') . ':',
		'more_prompt' => '<strong>' . __('More...', 'tocc') . '</strong>',
		'page_title' => '<h2>' . __('Static Pages', 'tocc') . '</h2>',
		'icon_set' => 1,
		'separate_lists' => 'checked',
		'show_menu' => 'checked',
		'menu_texts' => __('Options,Collapse All,Expand All,Hide Summaries,Show Summaries,Sort by Menu Order,Sort Alphabetically,Sort by Post Date,Sort by Author', 'tocc'),
		'menu_borderColor' => '#cacaca',
		'show_helptext' => 2,
		'help_tooltip' => __('Click to display page help!', 'tocc'),
		'help_text' => __('Click on the arrow icon beside each category to display or hide all items within the chosen category. Summaries of each page may be hidden or shown by clicking the plus icon next each item. Use the Options menu to expand or contract all items, display or hide summaries or sort the table of contents.', 'tocc'),
		'sort_order' => 0,
		'allow_expand' => 'checked',
		'expand_hideByDef' => '',
		'show_warn' => 'checked',
		'show_ack' => 'checked',
		'show_sum' => 'checked',
		'sum_extractFrom' => 0,
		'sum_customMeta' => 'description',
		'sum_charLimit' => 160,
		'sum_showEllipses' => 'checked',
		'sum_hideByDef' => 0,
		'sum_allowHide' => '',
		'show_static' => 'checked',
		'page_depth' => -1,
		'link_class' => '',
		'page_author' => '',
		'page_date' => 'checked',
		'page_comments' => '',
		'page_exclude' => array (),
		'page_include' => array (),
		'show_blog' => 'checked',
		'blog_title' => '<h2>' . __('Blog Posts', 'tocc') . '</h2>',
		'blog_author' => 'checked',
		'blog_date' => 'checked',
		'blog_count' => -1,
		'blog_comments' => '',
		'blog_nocats' => '',
		'blog_flat' => '',
		'blog_warn' => '',
		'blog_warntext' => sprintf(__('(%s posts shown)', 'tocc'), constant('TOCC_NUMBER_SENTINEL')),
		'blog_more' => 'checked',
		'blog_info' => 'checked',
		'blog_exclude' => array (),
		'blog_postExclude' => array (),
		'blog_include' => array (),
		'blog_cattext' => __('Category: ', 'tocc'),
		'show_postIDs' => '',
		'show_forum' => '',
		'forum_title' => '<h2>' . __('Forum Comments', 'tocc') . '</h2>',
		'forum_page' => '0',
		'forum_author' => 'checked',
		'forum_date' => 'checked',
		'forum_count' => 10,
		'forum_warn' => '',
		'forum_warntext' => sprintf(__('(%s comments shown)', 'tocc'), constant('TOCC_NUMBER_SENTINEL')),
		'forum_more' => 'checked',
		'link_exclude' => array (),
		'toc_page' => __('Site Map', 'tocc'),
		'toc_slug' => __('site-map', 'tocc'),
	);

	// Creates a TOCC option array with default settings. If the option array already exists, the following line
	// exits with no action. If new options are added to the option array, the existing TOCC options array must
	// be manipulated by the check_upgrade() function to ensure the correct defaults are loaded for the new 
	// options.
	add_option('tocc_options', $tocc_defaults);
	
	// this function ensures that no existing options are lost during the upgrade process
	tocc_check_upgrade();
	
	// register the sidebar widget
	register_sidebar_widget(__('Table of Contents', 'tocc'), 'tocc_widget_hook');
}

// Executes before the files are deleted when the plugin is uninstalled. Note that this function does not run
// if the plugin is de-activated.
function tocc_uninstall_hook() {
	// play nice and delete all options from the wp_options database
	delete_option('tocc_options');
	delete_option('tocc_debug_mode');
	delete_option('tocc_version');
}

// Executes when the admin menu is being displayed.
function tocc_menu_hook() {
	// add tocc settings to the admin menu
	add_options_page('Table of Contents Creator', 'Table of Contents Creator', 8, __FILE__, 'tocc_plugin_options');
}

// Executes before each page is displayed.
function tocc_display_hook($content = '') {

	// check if the TOCC tag exists and extract all option overrides
   if(preg_match_all('#<!--\s*' . constant('TOCC_TAG') . '\s*(.*?)\s*-->#xi', $content, $matches, PREG_SET_ORDER)) {

		// To comply with XHTML standards, we must remove any open paragraph tags. Wordpress annoyingly automatically adds paragraph tags
		// by default, so we need to make sure we remove them.
		$hasParagraph = preg_match('#<p>\s*<!--\s*' . constant('TOCC_TAG') . '.* -->\s*</p>#', $content);

   	// replace all instances of the TOCC tag enduring only the first instance includes the javascript to mod the header
   	$dontModHeader = false;
   	foreach($matches as $match) {

   		// override temporary options - it is possible to include temporary option overrides by appending parameters after the TOCC
   		// tag. Any option can be overridden allowing TOCC to be used multiple times within a website to display different output. 
	   	$tocc_options = get_option('tocc_options');
			if($match[1]) {
				foreach(explode('|',$match[1]) as $command) {
					$option = explode('=', $command);
					if($option) {
						if(substr($option[0], 0, 1) == "@") {
							$tocc_options[substr($option[0], 1)] = explode(',', $option[1]);
						}
						else {
							$tocc_options[$option[0]] = $option[1];
						}
					}
				}
			}

			// replace the tag with the table of contents
			$content = preg_replace('#' . ($hasParagraph ? '<p>\s*' : '') . '<!--\s*' . constant('TOCC_TAG') . '.* -->' 
						. ($hasParagraph ? '\s*</p>' : '') . '#', tocc_build_toc($tocc_options, $dontModHeader), $content, 1);
						
			// only include JavaScript to modify the header once
			$dontModHeader = true;
		}
   }

	return $content;
}

// Executes if the TOCC widget is enabled.
function tocc_widget_hook($args) {
	extract($args);

	$tocc_options = get_option('tocc_options');
	$title = $tocc_options['toc_title'] ? strip_tags($tocc_options['toc_title']) : __('Table of Contents', 'tocc');

	// set some default options - this is a temporary solution as eventually these will be settable via the widget
	$tocc_options['show_helptext'] = '';
	$tocc_options['show_menu'] = '';
	$tocc_options['separate_lists'] = 'checked';
	$tocc_options['allow_expand'] = 'checked';
	$tocc_options['expand_hideByDef'] = 'checked';
	$tocc_options['toc_title'] = '';
	
	echo $before_widget . $before_title . $title . $after_title
		. '<div class="textwidget">' . tocc_build_toc($tocc_options) . '</div>'
		. $after_widget;
}

// Executes each time a plugin links are displayed in the admin plugin page.
function tocc_filter_settings_option($links, $file) {
	// only add settings link to the TOCC plugin
	if ($file == plugin_basename(__FILE__)) {

		// load text domain to allow "settings" text to be translatable
		load_plugin_textdomain('tocc', constant('TOCC_PLUGIN_PATH'), basename(dirname(__FILE__)));

		// add settings link to the start of the plugin links
		$settings_link = '<a href="options-general.php?page=table-of-contents-creator/table_of_contents_creator.php">' 
							. __('Settings', 'tocc') . '</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}

// Let Wordpress know about the various URL parameters required by the plugin
function tocc_parameters_hook($qvars) {
	$qvars[] = 'toc_page'; // [blog|forum] displays blog posts or forum comments only
	$qvars[] = 'toc_order'; // [0|1|2|3] sets the table of content sort order
	return $qvars;
}

/**********************************
*** BUILD THE TABLE OF CONTENTS ***
**********************************/

// Simple function to show the table of contents. This function may be used within tempate functions or within pages or posts.
function tocc_show() {
	$tocc_options = get_option('tocc_options');
	echo tocc_build_toc($tocc_options);
}

// Build the table of contents. This is done by first displaying all pages if static pages are enabled. If the page
// containing the blog is found and separate lists are not selected, then the blog posts are shown before the remaining
// pages. Likewise for comments. If separate lists are enabled, the blog and forum contents are simply added to the
// end. This function returns a string containing the HTML code required to render the table of contents.
function tocc_build_toc(&$tocc_options, $dontModHeader = false) {
	global $post;
	
	// allow marked texts to be translatable
	load_plugin_textdomain('tocc', constant('TOCC_PLUGIN_PATH'), basename(dirname(__FILE__)));

	// determine icon set (theme) used throughout the table of contents and set to blank if not required
	$iconThemeNames = explode(',', constant('TOCC_ICON_THEMES'));
	$iconSet = $tocc_options['icon_set'] > 0 ? strtolower($iconThemeNames[$tocc_options['icon_set']]) : '';

	// add comment text to HTML page
	$tocc = PHP_EOL . PHP_EOL . '<!-- CREATED BY "TABLE OF CONTENTS GENERATOR v' . constant('TOCC_VERSION') 
			. '" BY MARK BELJAARS -->' . PHP_EOL 
			. '<div id="tocc"' . ($iconSet || $tocc_options['allow_expand'] ? ' class="' 
				. ($iconSet ? 'tocc_has_icons tocc_' . $iconSet : '') . ($tocc_options['allow_expand'] ? ' tocc_has_expansion' : '') . '"' : '') 
			. '>' . PHP_EOL;
	
	// dump all current option settings if debugging is enabled
	if (get_option('tocc_debug_mode')) {
		$tocc .= '<!-- TOCC DEBUG INFORMATION FOLLOWS...' . PHP_EOL;
		foreach ($tocc_options AS $option => $value) {
			$tocc .= '   ' . $option . ' => ' . $value . PHP_EOL;
			if (is_array($value)) {
				foreach ($value as $key => $arrvalue) {
					$tocc .= '      ' . $key . ' => ' . $arrvalue . PHP_EOL;
				}
			}
		}
		$tocc .= '-->' . PHP_EOL;
	}

	// insert javascript for dynamic CSS and javascript include header entry
	if(!$dontModHeader) {
		$tocc .= '<script type="text/javascript">' . PHP_EOL 
				. '<!--' . PHP_EOL 
				. 'var headID = document.getElementsByTagName("head")[0];' . PHP_EOL 
				. 'var cssNode = document.createElement("link");' . PHP_EOL 
				. 'cssNode.type = "text/css";' . PHP_EOL 
				. 'cssNode.rel = "stylesheet";' . PHP_EOL 
				. 'cssNode.href = "' . constant('TOCC_FULL_PATH') . constant('TOCC_CSS_FILE') . '";' . PHP_EOL 
				. 'cssNode.media = "screen";' . PHP_EOL 
				. 'headID.appendChild(cssNode);' . PHP_EOL
			 	. 'var jqScript = document.createElement("script");' . PHP_EOL 
				. 'jqScript.type = "text/javascript";' . PHP_EOL 
				. 'jqScript.src = "' . constant('TOCC_FULL_PATH') . 'js/jquery-1.4.2.min.js";' . PHP_EOL 
				. 'headID.appendChild(jqScript);' . PHP_EOL 
				. 'var toccScript = document.createElement("script");' . PHP_EOL 
				. 'toccScript.type = "text/javascript";' . PHP_EOL 
				. 'toccScript.src = "' . constant('TOCC_FULL_PATH') . constant('TOCC_JS_FILE') . '";' . PHP_EOL 
				. 'headID.appendChild(toccScript);' . PHP_EOL
				. '-->' . PHP_EOL 
				. '</script>' . PHP_EOL;
	}

	// display the help text and icon
	if($tocc_options['show_helptext']) {
		$tocc .= (($tocc_options['show_helptext'] > 1) // show icon
					? '&nbsp;<a href="#" title="' . $tocc_options['help_tooltip'] .'" class="tocc_help_icon"></a>' : '')
				. '<p class="tocc_help_text"' 
					. (($tocc_options['show_helptext'] == 2) ? ' style="display:none;"' : '') // hide text by default
				. '>' . $tocc_options['help_text'] . '<br /><br /></p>' . PHP_EOL; 
	}

	// display options menu
	$menuTexts = explode(',', $tocc_options['menu_texts']);
	if($tocc_options['show_menu'] && $menuTexts[0]) {
		$pagePermalink = get_permalink($post->ID) . (strpos(get_permalink($post->ID), '?') === false ? '?' : '&');
		$tocc .= '<span class="tocc_options">'
			. '<a href="#" class="tocc_options_header">' . $menuTexts[0] . ' <small>&#9660;</small></a>&nbsp;&nbsp;'
			. '<ul class="tocc_options_menu" style="border-color:' . $tocc_options['menu_borderColor'] . ';">'
			. ($tocc_options['allow_expand'] 
				? ($menuTexts[1] ? '<li><a href="#" class="tocc_exp_hide_all">' . $menuTexts[1] . '</a></li>' : '')
				. ($menuTexts[2] ? '<li><a href="#" class="tocc_exp_show_all">' . $menuTexts[2] . '</a></li>' : '')
				. ($menuTexts[1] || $menuTexts[2]
					? '<li class="tocc_separator" style="border-color:' . $tocc_options['menu_borderColor'] . ';"></li>' : '')
				: '')
			. ($tocc_options['show_sum'] 
				? ($menuTexts[3] ? '<li><a href="#" class="tocc_summ_hide_all">' . $menuTexts[3] . '</a></li>' : '')
				. ($menuTexts[4] ? '<li><a href="#" class="tocc_summ_show_all">' . $menuTexts[4] . '</a></li>' : '')
				. ($menuTexts[3] || $menuTexts[4] 
					? '<li class="tocc_separator" style="border-color:' . $tocc_options['menu_borderColor'] . ';"></li>' : '')
				: '')
			. ($menuTexts[5] ? '<li><a href="' . $pagePermalink . 'toc_order=0">' . $menuTexts[5] . '</a></li>' : '')
			. ($menuTexts[6] ? '<li><a href="' . $pagePermalink . 'toc_order=1">' . $menuTexts[6] . '</a></li>' : '')
			. ($menuTexts[7] ? '<li><a href="' . $pagePermalink . 'toc_order=2">' . $menuTexts[7] . '</a></li>' : '')
			. ($menuTexts[8] ? '<li><a href="' . $pagePermalink . 'toc_order=3">' . $menuTexts[8] . '</a></li>' : '')
			. '</ul></span>'
			. '<br />' . PHP_EOL;
	}

	// display the table of contents title
	$tocc .= $tocc_options['toc_title'] . PHP_EOL;
	
	// detect if the table of contents had any URL page parameters set
	global $wp_query;

	// temporarily modify the sort order if option override variables set
	if (isset($wp_query->query_vars['toc_order'])) {
		$tocc_options['sort_order'] = (int)$wp_query->query_vars['toc_order'];
	}

	// determine page to display
	if (isset($wp_query->query_vars['toc_page'])) {
		
		// display all blog posts
		if ($tocc_options['show_blog'] && $wp_query->query_vars['toc_page'] == constant('TOCC_ALLPOSTS_PAGEID')) {
			$tocc .= $tocc_options['blog_title'] . PHP_EOL 
					. tocc_display_posts(-1, false, 0, $tocc_options);
		}
		
		// display all forum comments
		elseif ($tocc_options['show_forum'] && $wp_query->query_vars['toc_page'] == constant('TOCC_ALLCOMMENTS_PAGEID')) {
			$tocc .= $tocc_options['forum_title'] . PHP_EOL 
					. tocc_display_forum(-1, false, $tocc_options);
		}
		
	//	no URL page parameters set, so display the normal table of contents
	} else {
		
		// get page for posts from wordpress settings (this is not a TOCC setting)
		$page_for_posts = get_option('page_for_posts');

		// add static pages to the table of contents
		if ($tocc_options['show_static']) {
			$tocc .= ($tocc_options['separate_lists'] ? $tocc_options['page_title'] . PHP_EOL : '')
					. tocc_display_pages($tocc_options, $page_for_posts) . PHP_EOL;
		}

		// display blog posts - note that if the single list option is set, blog posts will be displayed by the static page call above
		if ($tocc_options['show_blog'] && (!$tocc_options['show_static'] || !$page_for_posts || $tocc_options['separate_lists'])) {
			// only some posts shown warning text
			$warnText = $tocc_options['blog_warn'] && $tocc_options['blog_count'] > 0 ? 
							'<span class="tocc_misctext">' 
								. str_replace(constant('TOCC_NUMBER_SENTINEL'), $tocc_options['blog_count'], $tocc_options['blog_warntext']) 
								. '</span>'
							: '';
							
			if ($tocc_options['separate_lists']) {
				// display blog title
				$tocc .= PHP_EOL . ($tocc_options['show_static'] ? '<br />' : '') . $tocc_options['blog_title'] 
						. $warnText. PHP_EOL
						. tocc_display_posts($tocc_options['blog_count'], false, 0, $tocc_options) . PHP_EOL;
				
			} else {
				$tocc .= '<ul>'. PHP_EOL;
				// display blog page as a page
				if($page_for_posts) {
					$post_page = get_page($page_for_posts);
					$tocc .= tocc_create_link($post_page, 'page', 'has_child', true, true, get_permalink($post_page), $post_page->post_title
									, ($tocc_options['page_author'] ? $post_page->post_author : '') 
									, ($tocc_options['page_date'] ? $post_page->post_date : '')
									, $tocc_options['show_sum'], $tocc_options, $warnText);
				// display home as a blog page
				} else {
					$tocc .= tocc_create_link($page_for_posts, 'page', 'has_child', true, true, get_bloginfo('url'), strip_tags($tocc_options['blog_title'])
									, false, false, false, $tocc_options, $warnText);
				}
				// list blog categories and posts
				$tocc .= PHP_EOL . ($tocc_options['show_static'] ? '<br />' : '')
						. tocc_display_posts($tocc_options['blog_count'], true, 1, $tocc_options)
						. '</li></ul>' . PHP_EOL;
			}
		}

		// display forum comments - note that if the single list option is set, comments will be displayed by the static page call above
		if ($tocc_options['show_forum'] && function_exists('latest_activity') && (!$tocc_options['show_static'] || $tocc_options['separate_lists'])) {
			if ($tocc_options['separate_lists']) {
				// display forum title
				$tocc .= PHP_EOL . ($tocc_options['show_static'] || $tocc_options['show_blog'] ? '<br />' : '') . $tocc_options['forum_title'] . PHP_EOL;
				
			} else {
				$warnText = $tocc_options['forum_warn'] && $tocc_options['forum_count'] > 0 ? 
								str_replace(constant('TOCC_NUMBER_SENTINEL'), $tocc_options['forum_count'], $tocc_options['forum_warntext']) : '';
				$tocc .= '<ul>'. PHP_EOL;
				// display forum title
				$forum_page = get_page($tocc_options['forum_page']);
				$tocc .= tocc_create_link($forum_page, 'page', 'has_child', true, true, get_permalink($forum_page), $forum_page->post_title
								, ($tocc_options['page_author'] ? $forum_page->post_author : '') 
								, ($tocc_options['page_date'] ? $forum_page->post_date : '')
								, $tocc_options['show_sum'], $tocc_options, $warnText);
			}
			// insert the forum comments
			$tocc .= tocc_display_forum($tocc_options['forum_count'], !$tocc_options['separate_lists'], $tocc_options)
					. ($tocc_options['separate_lists'] ? '' : '</li></ul>' . PHP_EOL);
		}
	}
	
	// display author credit
	/*if ($tocc_options['show_ack']) {
		$tocc .= '<br />' . PHP_EOL . PHP_EOL 
				. '<p class="tocc_footer">' 
				. __('Generated by', 'tocc') . ' Table of Contents Creator v' . constant('TOCC_VERSION') . '<br />' 
				. __('by', 'tocc') . ' <a href="http://markbeljaars.com/">Mark Beljaars</a>' 
				. '</p>' . PHP_EOL;
	}*/	/*Swena*/
	
	$tocc .= '</div>';
	return $tocc;
}

// Display all pages. This is a recursive function that walks the page hierarchy displaying all pages in hierarchial order.
function tocc_display_pages(&$tocc_options, $pageForPosts) {

	// set exluded pages if included pages option set
	if($tocc_options['page_include']) {
		$pages = get_pages('depth=-1');
		$tocc_options['page_exclude'] = array();
		foreach($pages as $page) {
			if(!in_array($page->ID, $tocc_options['page_include'])) {
				$tocc_options['page_exclude'][] = $page->ID;
			}
		}
	}	

	$content = '';
	return _tocc_display_pages($tocc_options, $pageForPosts, $content);
}

function _tocc_display_pages(&$tocc_options, &$pageForPosts, &$content, $parentID = 0, $depth = 0) {

	// retrieve a list of pages with the given parent ID
	$pages = get_pages('child_of=' . $parentID . '&parent=' . $parentID . '&hierarchical=1&sort_column=' 
					. ($tocc_options['sort_order'] == 0 ? 'menu_order&sort_order=ASC'
			 		: ($tocc_options['sort_order'] == 1 ? 'post_title&sort_order=ASC'
			 		: ($tocc_options['sort_order'] == 2 ? 'post_date&sort_order=DESC' 
			 		: 'post_author&sort_order=ASC')))
				. ($tocc_options['page_exclude'] ? '&exclude=' . implode(',', $tocc_options['page_exclude']) : '')
				);
				 
	// exit if no pages found
	if(!$pages) { return $content; }

	// create an unordered list of pages
	$content .= '<ul' 
					. ($depth > 0 ? ' class="tocc_expandable"'
					. ($tocc_options['expand_hideByDef'] && $tocc_options['allow_expand'] ? ' style="display:none;"' : '') : '')
				. '>' . PHP_EOL;
	
	// list all pages
	global $wpdb;
	$depth++;
	foreach($pages as $pagg) {

		// retrieve blog posts and categories if blog page found and not displaying separate lists
		$blog = $tocc_options['show_blog'] && !$tocc_options['separate_lists'] && $pagg->ID == $pageForPosts 
					? tocc_display_posts($tocc_options['blog_count'], true, $depth, $tocc_options) : '';
					
		// retrieve forum comments if forum page found and not displaying separate lists
		$forum = $tocc_options['show_forum'] && !$tocc_options['separate_lists'] && function_exists('latest_activity') 
					&& $pagg->ID == $tocc_options['forum_page'] ? tocc_display_forum($tocc_options['forum_count'], true, $tocc_options) : '';
		
		// get marked anchor tags from the page
		$anchors = $tocc_options['link_class'] ? tocc_display_anchors($pagg, $tocc_options) : '';

		// display the page link
		$hasChild = (($depth <= $tocc_options['page_depth'] || $tocc_options['page_depth'] < 0) 
					&& get_pages('child_of=' . $pagg->ID)) || $anchors || $blog || $forum;
		$class = ($hasChild ? 'tocc_has_child' : '') . ($GLOBALS['post'] ? ($GLOBALS['post']->ID == $pagg->ID ? ' tocc_active' : '') : '');

		// retrieve a count of the page comments
		$commentCount =  $tocc_options['page_comments'] 
			? $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1' AND comment_post_ID = '$pagg->ID';") : 0;

		$content .= tocc_create_link($pagg, 'page', $class, $depth == 1, $hasChild, get_permalink($pagg->ID), $pagg->post_title
						, ($tocc_options['page_author'] ? $pagg->post_author : ''), ($tocc_options['page_date'] ? $pagg->post_date : '')
						, $tocc_options['show_sum'], $tocc_options
						, ($blog && $tocc_options['blog_warn'] && $tocc_options['blog_count'] > 0 ? 
								'<span class="tocc_misctext">' 
									. str_replace(constant('TOCC_NUMBER_SENTINEL'), $tocc_options['blog_count'], $tocc_options['blog_warntext']) 
								. '</span>' : 
						  ($forum && $tocc_options['forum_warn'] && $tocc_options['forum_count'] > 0 ? 
								'<span class="tocc_misctext">' 
									. str_replace(constant('TOCC_NUMBER_SENTINEL'), $tocc_options['forum_count'], $tocc_options['forum_warntext']) 
								. '</span>' :
						  ($commentCount ?
								'<span class="tocc_comment_count">(' . $commentCount . ')</span>' :
							''))))
						. $anchors . $blog . $forum;

		// include any children
		if($depth <= $tocc_options['page_depth'] || $tocc_options['page_depth'] < 0) {
			_tocc_display_pages($tocc_options, $pageForPosts, $content, $pagg->ID, $depth);
		}
		$content .= '</li>' . PHP_EOL;

	}
	$depth--;
	$content .= '</ul>' . PHP_EOL;
	return $content;
}

// Display all anchor links marked with the TOCC class in the table of contents.
function tocc_display_anchors(&$pagg, &$tocc_options) {
	
	// export anchor links into an array using this regex expression
	preg_match_all('#<a\s+
		(?:(?= [^>]* href\s*=\s*["\']   (?P<href>  [^"\']*) ["\'])|)
		(?:(?= [^>]* class\s*=\s*["\']  (?P<class> [^"\']*) ["\'])|)
		(?:(?= [^>]* title\s*=\s*["\']  (?P<title> [^"\']*) ["\'])|)
		[^>]*class\s*=\s*["\'][^"\']*' . $tocc_options['link_class'] . '[^>]*>
		(?P<text>[^<]*)
		</a>
		#xi', $pagg->post_content, $matches, PREG_SET_ORDER);
	if (!$matches) { return ''; }

	// sort the results
	if($tocc_options['sort_order'] == 1) {
		tocc_multiArraySort($matches, 'text');
	}

	// display the anchors in an unsorted list
	$content = '<ul class="tocc_expandable"' 
					. ($tocc_options['expand_hideByDef'] && $tocc_options['allow_expand'] ? ' style="display:none;"' : '') 
				. '>' . PHP_EOL;

	foreach ($matches as $match) {
		$content .= tocc_create_link($pagg, 'link', false, false, false, $match['href'], $match['text'], false, false, $match['title'] != ''
							, $tocc_options , false, $match['title']) 
						. '</li>' . PHP_EOL;
	}
	
	$content .= $content ? '</ul>' . PHP_EOL : '';
	return $content;
}

// Display all blog categories and posts
function tocc_display_posts($postCount, $allowHideRoot, $depth, &$tocc_options) {
	global $post;

	// set exluded categories if included categories option set
	if($tocc_options['blog_include']) {
		$cats = get_categories();
		$tocc_options['blog_exclude'] = array();
		foreach($cats as $cat) {
			if(!in_array($cat->cat_ID, $tocc_options['blog_include'])) {
				$tocc_options['blog_exclude'][] = $cat->cat_ID;
			}
		}
	}	

	// create the category and post list
	$content .= '<ul' . ($depth > 0 ? ' class="tocc_expandable"'
				. ($tocc_options['expand_hideByDef'] && $tocc_options['allow_expand'] ? ' style="display:none;"' : '') : '')
				. '>' . PHP_EOL;

	// walk the category tree
	if(!$tocc_options['blog_nocats']) {
		$content = _tocc_display_posts($postCount, $allowHideRoot, $depth, $tocc_options, $content);
	}

	// show all posts in one big list
	else {
		$posts = get_posts('numberposts=' . $postCount . '&orderby=' 
				 . ($tocc_options['sort_order'] == 0 || $tocc_options['sort_order'] == 2 ? 'date&order=DESC'
				 : ($tocc_options['sort_order'] == 1 ? 'title&order=ASC' : 'author&order=ASC')));	

		global $wpdb;
		foreach ($posts as $post) {

			// retrieve a count of the post comments
			if(!$tocc_options['blog_postExclude'] || !in_array($post->ID, $tocc_options['blog_postExclude'])) {
				$commentCount =  $tocc_options['blog_comments'] 
					? $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1' AND comment_post_ID = '$post->ID';") : 0;
				$content .= tocc_create_link($post, 'blog_post', false, false, false, get_permalink($post->ID), 
										$post->post_title . ($tocc_options['show_postIDs'] ? ' (ID: ' . $post->ID . ')' : '')
								, ($tocc_options['blog_author'] ? $post->post_author : false) , ($tocc_options['blog_date'] ? $post->post_date : false)
								, $tocc_options['show_sum'], $tocc_options
								, ($commentCount ? '<span class="tocc_comment_count">(' . $commentCount . ')</span>' : ''))
							. '</li>' . PHP_EOL;
			}
		}		
	}

	// add more prompt
	$content .=  $tocc_options['blog_more'] && $postCount >= 0 ? '<li class="tocc_more">' 
				. '<a href="' . get_permalink($post->ID) . (strpos(get_permalink($post->ID), '?') === false ? '?' : '&') 
					. 'toc_page=' . constant('TOCC_ALLPOSTS_PAGEID') . '">' 
					. $tocc_options['more_prompt'] . '</a>' 
				. '</li>' . PHP_EOL : '';

	$content .= '</ul>';
	return $content;
}

function _tocc_display_posts($postCount, $allowHideRoot, $depth, &$tocc_options, &$content, $parentID = 0) {

	// retrieve a list of categories with the given parent ID
	$categories = get_categories(($tocc_options['blog_flat'] ? 'hierarchical=0' : 'child_of=' . $parentID . '&parent=' . $parentID . '&hierarchical=1') 
					. '&orderby=' . ($tocc_options['sort_order'] == 0 ? 'id' : 'name')
					. ($tocc_options['blog_exclude'] ? '&exclude=' . implode(',', $tocc_options['blog_exclude']) : ''));
	// exit if no categories found
	if(!$categories) { return $content; }

	// list all categories
	$depth++;
	foreach($categories as $cat) {
	
		// extract the posts belonging to the current parent
		$posts = get_posts('numberposts=-1&category=' . $cat->cat_ID . '&orderby=' 
				 . ($tocc_options['sort_order'] == 0 || $tocc_options['sort_order'] == 2 ? 'date&order=DESC'
				 : ($tocc_options['sort_order'] == 1 ? 'title&order=ASC' : 'author&order=ASC')));	

		$catSumm = $tocc_options['show_sum'] ? strip_tags(category_description($cat->cat_ID)) : '';
		
		$content .= tocc_create_link($cat, 'blog_cat', false, $depth == 1, true, get_category_link($cat->cat_ID),
				$tocc_options['blog_cattext'] . $cat->cat_name, false, false, $catSumm, $tocc_options
					, ($tocc_options['blog_info'] ? '<span class="tocc_post_count">(' . count($posts) . ')</span>' : '')
					, $catSumm)
			. '<ul class="tocc_expandable"' 
				. ($tocc_options['expand_hideByDef'] && $tocc_options['allow_expand'] ? ' style="display:none;"' : '')
			. '>' . PHP_EOL;

		// display the posts - the loop will only display posts that are immediate siblings of the category (not children). The get_posts function
		// does not include an option to do this natively
		if($posts) {
			$count = 0;
			global $wpdb;
			foreach ($posts as $post) {
				if(in_category($cat, $post) && (!$tocc_options['blog_postExclude'] || !in_array($post->ID, $tocc_options['blog_postExclude']))) {
	
					// retrieve a count of the post comments
					$commentCount =  $tocc_options['blog_comments'] 
						? $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1' AND comment_post_ID = '$post->ID';") : 0;

					$content .= tocc_create_link($post, 'blog_post', false, false, false, get_permalink($post->ID),
										$post->post_title . ($tocc_options['show_postIDs'] ? ' (ID: ' . $post->ID . ')' : '')
									, ($tocc_options['blog_author'] ? $post->post_author : false) , ($tocc_options['blog_date'] ? $post->post_date : false)
									, $tocc_options['show_sum'], $tocc_options
									, ($commentCount ? '<span class="tocc_comment_count">(' . $commentCount . ')</span>' : ''))
								. '</li>' . PHP_EOL;
					$count++;
					if($postCount > 0 && $count >= $postCount) break;
				}
			}
		}
		
		if(!$tocc_options['blog_flat']) {
			_tocc_display_posts($postCount, $allowHideRoot, $depth, $tocc_options, $content, $cat->cat_ID);
		}

		$content .= '</ul>' . PHP_EOL 
					. '</li>' . PHP_EOL;
	}
	$depth--;
	return $content;
}
	
// Display forum comments
function tocc_display_forum($forumCount, $allowHideRoot, &$tocc_options) {
	global $post;
	
	// extract forum comments into a single string
	ob_start();
	latest_activity($forum_count > 0 ? $forum_count : 100000);
	$posts = ob_get_contents();
	ob_end_clean();

	// separate each comment from the captured string and create a comment array
	preg_match_all('#<li[^>]*>(.*?)</li>#xi', $posts, $matches, PREG_SET_ORDER);
	$comments = array();
	foreach ($matches as $match) {
		preg_match_all('#>(.*?)</a>#xi', $match[1], $text, PREG_SET_ORDER);
		preg_match_all('#(.*?)</a>#xi', $match[1], $anchor, PREG_SET_ORDER);
		preg_match_all('#</a><br\s*/>\s*(.*?)\s*<small>#xi', $match[1], $author, PREG_SET_ORDER);
		if(!$author) { // backwards compatibility with older verions of forum server
			preg_match_all('#</a>\s*by:\s*(.*?)<br\s*/>#xi', $match[1], $author, PREG_SET_ORDER);
		}
		preg_match_all('#<small>(.*?)</small>#xi', $match[1], $date, PREG_SET_ORDER);
		$comments[] = array('text'=>$text[0][1], 'anchor'=>$anchor[0][0], 'author'=>$author[0][1], 'date'=>$date[0][1]);
	}

	// sort the results
	tocc_multiArraySort($comments, ($tocc_options['sort_order'] == 0 || $tocc_options['sort_order'] == 2 ? 'date' 
			: ($tocc_options['sort_order'] == 1 ? 'text' : 'author')));
	
	// display all comments
	$content = '<ul' . ($allowHideRoot ? ' class="tocc_expandable"' 
					. ($tocc_options['expand_hideByDef'] && $tocc_options['allow_expand'] ? ' style="display:none;"' : '') 
				: '') . '>';
	foreach ($comments as $comment) {
		$content .= '<li class="tocc_forum">' . $comment['anchor']
					. ($tocc_options['forum_author'] ? '<span class="tocc_author"> ' . $tocc_options['author_prefix'] . ' ' . $comment['author'] . '</span>' : '') 
					. ($tocc_options['forum_date'] ? '<p class="tocc_date">' . date_i18n($tocc_options['date_format'], strtotime($comment['date'])) . '</p>' : '')
					. '</li>';
	}

	// add more prompt
	$content .= $tocc_options['forum_more'] && $forumCount >= 0 ? '<li class="tocc_more">' 
				. '<a href="' . get_permalink($post->ID) . (strpos(get_permalink($post->ID), '?') === false ? '?' : '&') 
					. 'toc_page=' . constant('TOCC_ALLCOMMENTS_PAGEID') . '">' 
					. $tocc_options['more_prompt'] . '</a>' 
				. '</li>' . PHP_EOL : '';
	
	$content .= '</ul>';
	return $content;
}

/***********************
*** HELPER FUNCTIONS ***
***********************/

// Create a page, post or forum link in the table of contents.
function tocc_create_link(&$post, $type, $class, $isRoot, $hasChild, $linkPerm, $linkTitle, $authorID, $linkDate, $showSumm, &$tocc_options
				, $additionalText = false, $summText = false) {
	$author = $authorID ? get_userdata($authorID) : '';
	$excludeLink = $post && is_array($tocc_options['link_exclude']) ? in_array($post->ID, $tocc_options['link_exclude']) : false;
	//note that the list entry is not closed.
	$content = '<li class="tocc_' . $type . ' ' . $class . '">'
				. ($hasChild && $tocc_options['allow_expand'] ? '<span class="tocc_expand_icon' 
					. ($tocc_options['expand_hideByDef'] && $tocc_options['allow_expand'] ? ' tocc_expand_up' : '') . '"></span>' : '')
				. ($excludeLink ? '' : '<a href="' . print_r($linkPerm, TRUE) . '" class="tocc_' . $type . '_title' 
					. ($isRoot ? ' tocc_root': '') .'">')
				. print_r($linkTitle, TRUE)
				. ($excludeLink ? '' : '</a>') . '&nbsp;' 
				. ($author ? '<span class="tocc_author">' . $tocc_options['author_prefix'] . ' <a href="' 
					. ($author->user_url ? $author->user_url : get_bloginfo('url') . '?author=' . $authorID) . '" rel="nofollow">' 
					. $author->display_name . '</a></span>' : '')
				. ($additionalText ? '&nbsp;' . $additionalText : '')
				. ($tocc_options['sum_allowHide'] && $showSumm ? '<span class="tocc_summ_icon"></span>' : '') 
				. ($linkDate ? '<p class="tocc_date">' . date_i18n($tocc_options['date_format'], strtotime($linkDate)) . '</p>' : '')
				. ($showSumm ? ($summText ? tocc_show_summary($summText, $isRoot, $hasChild, $tocc_options) : tocc_generate_summary($post, $isRoot, $hasChild, $tocc_options)) : '');

	return $content;
}

// Generate page or post comment.
function tocc_generate_summary(&$post, $isRoot, $hasChild, &$tocc_options) {
	
	$summary = '';
	switch ($tocc_options['sum_extractFrom']) {

		//auto
		case 0: 
			// all in one seo
			$summary = get_post_meta($post->ID, "_aioseop_description", true);
			// Custome Meta
			if (!$summary) {
				$summary = get_post_meta($post->ID, $tocc_options['sum_customMeta'], true);
			}
			// post exceprt
			$thePost = get_post($post->ID);
			if (!$summary && $thePost) {
				$summary = strip_tags($thePost->post_excerpt);
			}
			// post content
			if (!$summary && $thePost) {
				$summary = strip_tags($thePost->post_content);
			}
			break;

		// all in one
		case 1: 
			$summary = get_post_meta($post->ID, "_aioseop_description", true);
			break;
			
		// post excerpt
		case 2:
			$thePost = get_post($post->ID);
			if ($thePost) {
				$summary = strip_tags($thePost->post_excerpt);
			}
			break;
			
		// post body
		case 3:
			$thePost = get_post($post->ID);
			if ($thePost) {
				$summary = strip_tags($thePost->post_content);
			}
			break;
			
		// custom meta
		case 4:
			$summary = get_post_meta($post->ID, $tocc_options['sum_customMeta'], true);
			break;
		
		// default
		default :
			$summary = 'Oops. I don\'t know how to use that option!.';
	}
	
	// extract caption and square bracket trigger tags
	$summary = preg_replace('|\[(.+?)\](.+?\[/\\1\])?|s', '', $summary);
	if (!$summary) {
		$summary = __('No description found for this item.', 'tocc');
	}

	// remove invalid utf-8 characters - this requires the iconv module to be loaded
	if(defined('ICONV_VERSION')) {
		$summary = iconv("UTF-8","UTF-8//IGNORE", $summary);
	}
	
	return tocc_show_summary($summary, $isRoot, $hasChild, $tocc_options);
}

// Trim text to a specific length and show as a summary
function tocc_show_summary($text, $isRoot, $hasChild, &$tocc_options) {

	// don't trim if no limit set or text is already shorter than the limit
	if ($tocc_options['sum_charLimit'] > 0 && strlen($text) > $tocc_options['sum_charLimit']) {
		$text = substr($text, 0, $tocc_options['sum_charLimit']);
		// display an ellipse
		if ($tocc_options['sum_showEllipses']) {
			// add elipses to the end of the last whole word
			$words = split('[ ]+', $text);
			if(strlen(end($words)) < 10) {
				array_pop($words);
			}
			$text = implode(' ', $words);
			$text .= '&hellip;';
		}
	}

	return '<p class="tocc_summ_body"' 
				. ($tocc_options['sum_hideByDef'] == 1 || ($hasChild && $tocc_options['sum_hideByDef'] == 2) || (!$isRoot && $tocc_options['sum_hideByDef'] == 3) 
					? ' style="display:none;"' : '') 
				. '>' 
			. $text
		 	. '</p>';
}

// Handle seamless upgrade from previous versions
function tocc_check_upgrade() {
	
	if (get_option('tocc_version')) {
		
		// retrieve the version prior to update and convert into a number
		$old_ver = explode('.', get_option('tocc_version') . '.0.0.0');
		$old_ver = $old_ver[0] * 1000000 + $old_ver[1] * 1000 + $old_ver[2];
		
		// manipulate options to ensure the latest version is compatible with previous versions
		if (get_option('tocc_version') != constant('TOCC_VERSION')) {
			
			if ($old_ver < 1003000) {
				// add header tags to title - prior to version 1.3 the header tags were hard coded
				$title = get_option('tocc_toc_title');
				if ($title && substr($title, 0, 1) != '<') {
					update_option('tocc_toc_title', '<h1>' . $title . '</h1>');
				}
			}
			
			if ($old_ver < 1004000) {
				// set icon theme to handrawn - versions prior to 1.4 had a show icons setting and the only icon theme was "handdrawn"
				if (get_option('tocc_show_icons')) {
					update_option('tocc_icon_set', 'Handdrawn');
					delete_option('tocc_show_icons');
				}
				
				// add formatting tags to blog title - formatting tags were not required prior to version 1.4 
				$title = get_option('tocc_blog_title');
				if ($title && substr($title, 0, 1) != '<') {
					update_option('tocc_blog_title', '<h2>' . $title . '</h2>');
				}
			}
			
			if ($old_ver < 1006000) {
				$tocc_options = $GLOBALS['tocc_defaults'];
				$tocc_options['author_prefix'] = get_option('tocc_blog_authorprefix');
				
				// turn icon set into a number
				$iconTheme = get_option('tocc_icon_set');
				$iconThemeNames = explode(',', constant('TOCC_ICON_THEMES'));
				for ($i = 0; $i < count($iconThemeNames); $i++) {
					if($iconThemeNames[$i] == $iconTheme) {
						$tocc_options['icon_set'] = $i;
						delete_option('tocc_icon_set');
						break;
					}
				}
				
				// check for old tocc options and if found add the value to the options array
				foreach (array_keys($tocc_options) as $option) {
					// only update default value with values from options that actually exist
					$value = get_option('tocc_' . $option);
					if(delete_option('tocc_' . $option)) {
						$tocc_options[$option] = $value;
					}
				}
				
				// remove left over options that no longer exist
				delete_option('tocc_all_listitems');
				delete_option('tocc_blog_authorprefix');
				delete_option('tocc_forum_authorprefix');
				delete_option('tocc_sum_showHideAll');
				delete_option('tocc_sum_showAllText');
				delete_option('tocc_sum_hideAllText');
				
				// update options
				update_option('tocc_options', $tocc_options);
			}
			
			if ($old_ver < 1006002) {
				// add new options
		   	$tocc_options = get_option('tocc_options');
				$tocc_options['menu_texts'] = $GLOBALS['tocc_defaults']['menu_texts'];
				update_option('tocc_options', $tocc_options);
			}

			if ($old_ver < 1006003) {
				// add new options
		   	$tocc_options = get_option('tocc_options');
				$tocc_options['show_help'] = $GLOBALS['tocc_defaults']['show_help'];
				$tocc_options['help_tooltip'] = $GLOBALS['tocc_defaults']['help_tooltip'];
				$tocc_options['help_text'] = $GLOBALS['tocc_defaults']['help_text'];
				update_option('tocc_options', $tocc_options);
			}
						
			if ($old_ver < 1006004) {
		   	$tocc_options = get_option('tocc_options');
		   	if($tocc_options['show_help']) {
					$tocc_options['show_helptext'] = $GLOBALS['tocc_defaults']['show_helptext'];
				} else {
					$tocc_options['show_helptext'] = 0;
				}
				update_option('tocc_options', $tocc_options);
			}

			// update version so this code doesn't run again
			update_option('tocc_version', constant('TOCC_VERSION'));
		}

	// set version on plugin install
	} else {
		update_option('tocc_version', constant('TOCC_VERSION'));
	}
}

// Sort multi-dimensional associate array on given key
function tocc_multiArraySort(&$object, $key) {
	for ($i = count($object) - 1; $i >= 0; $i--) {
		$swapped = false;
		for ($j = 0; $j < $i; $j++) {
			if ($object[$j][$key] > $object[$j + 1][$key]) {
				$tmp = $object[$j];
				$object[$j] = $object[$j + 1];
				$object[$j + 1] = $tmp;
				$swapped = true;
			}
		}
		if (!$swapped) return;
	}
}

/*******************************
*** ADMINISTRATION FUNCTIONS ***
*******************************/

// Option section header.
function tocc_option_header($heading_text, $heading_name, $is_hidden = true) {
	
	// show section if was previously showing before page was updated
	echo '<input type="hidden" id="' . $heading_name . '_display" name="' . $heading_name . '_display" value="' 
			. $_POST[$heading_name . '_display'] . '">' . PHP_EOL;
	if ($_POST[$heading_name . '_display']) {
		$is_hidden = $_POST[$heading_name . '_display'] == 'none';
	}
	
	// create option section
	echo '<div id="normal-sortables" class="meta-box-sortables">' . PHP_EOL
		. '<div class="postbox">' . PHP_EOL
		. '<div class="handlediv" title="' . __('Click to display or hide section options.', 'tocc') 
			. '" onclick="toggleVisibility(\'' . $heading_name . '\');">' . '<br />' 
		. '</div>' . PHP_EOL
		. '<h3 class="hndle" title="' 
			. __('Click to display or hide section options.', 'tocc') . '" onclick="toggleVisibility(\'' . $heading_name . '\');" >' 
		. '<span>' . $heading_text . '</span>' . PHP_EOL
		. '</h3><div class="inside" id="' . $heading_name . '" style="display:' . ($is_hidden ? 'none' : 'block') . ';">' . PHP_EOL;
}

// Option creator. This function can create several different types of options including check boxes, selectors,
// and text boxes.
function tocc_option(&$tocc_options, $option_text, $tooltip_text, $option_name, $element_type, $other_option = false
	, $disabled = false, $element_index = 'post_title', $element_ID = 'ID') {
	
	// create help link used by all options
	$helpLink = $tooltip_text ? '<span title="' . __('Click for Help!', 'tocc') . '" ' 
				 . 'onclick="toggleVisibility(\'' . $option_name . '_tip\');" ' 
				 . 'onmouseover="this.style.textDecoration = \'underline\'" ' 
				 . 'onmouseout="this.style.textDecoration = \'none\'" ' 
				 . 'style="cursor:pointer;"' . '>' : '';
	
	// create label used by all options
	$label = '<label for="' . $option_name . '" class="selectit" style="vertical-align:center;margin-left:' 
			 	. ($element_type != 'checkbox' ? '2' : '0') . 'px;' 
			 . ($disabled ? 'color: grey;' : '') . '"' . '>';
	
	// Wordpress style formatting
	echo '<p class="meta-options">';

	$index = 0;
	switch ($element_type) {

		// text box	
		case 'text' :
			echo $label . $helpLink . $option_text . ($tooltip_text ? '</span>' : '') . ':&nbsp;&nbsp;' . '</label>' 
				. '<input name="' . $option_name . '" type="text" size="' . $other_option . '" ' . 'value="' 
				. htmlspecialchars($tocc_options[$option_name]) . '"' . ($disabled ? ' disabled="true"' : '') . ' />';
			break;

		// text area
		case 'textarea' :
			$size = explode(',', $other_option);
			echo $label . $helpLink . $option_text . ($tooltip_text ? '</span>' : '') . ':<br/>' . '</label>' 
				. '<textarea name="' . $option_name . '" cols="' . $size[0] . '" rows="' . $size[1] . '"' 
				. ($disabled ? ' disabled="true"' : '') . '>'
				. htmlspecialchars($tocc_options[$option_name])
				. '</textarea>';
			break;

		// selector
		case 'select' :
			echo $label . $helpLink . $option_text . ($tooltip_text ? '</span>' : '') . ':&nbsp;&nbsp;' . '</label>' 
				. '<select name="' . $option_name . '"' . ($disabled ? ' disabled="true"' : '') . '>';
			// create each option
			foreach ($other_option as $opts) {
				if($element_index) {
					// associative array or object - use value and key pairing
					echo '<option value="' . $opts-> {$element_ID} . '"' 
						. ($tocc_options[$option_name] == $opts-> {$element_ID} ? ' selected="selected"' : '') . '>' 
						. $opts-> {$element_index} . '</option>';
				} else {
					// flat array - use value and index
					echo '<option value="' . $index . '"' 
						. ($tocc_options[$option_name] == $index ? ' selected="selected"' : '') . '>' 
						. $opts . '</option>';
				}
				$index++;
			}
			echo '</select>';
			break;
			
		// checkbox
		case 'checkbox' :
		
			if (!is_array($other_option)) {
				// single checkbox
				echo '<input name="' . $option_name . '" type="checkbox" id="' . $option_name . '"' 
					. ($tocc_options[$option_name] ? ' checked="checked"' : '') 
					. ($disabled ? ' disabled="true"' : '') . ' />' 
					. '&nbsp;&nbsp;' 
					. ($disabled ? $label : '') . $helpLink . $option_text 
					. ($disabled ? '</label>' : '') . ($tooltip_text ? '</span>' : '');
			} else {
				// array of checkboxes
				echo $label . '<strong>' . $helpLink . $option_text . ':' . ($tooltip_text ? '</span>' : '') . '</strong>' 
					. '</label>'
					. '<div style="border-color:#CEE1EF; border-style:solid; border-width:1px; height:auto; margin:5px 0px 5px 0px; overflow:auto;' 
						. 'padding:0.5em 0.5em;-moz-border-radius: 0 0 3px 3px;-webkit-border-bottom-left-radius: 3px;' 
						. '-webkit-border-bottom-right-radius: 3px;-khtml-border-bottom-left-radius: 3px;-khtml-border-bottom-right-radius: 3px;' 
						. 'border-bottom-left-radius: 3px;border-bottom-right-radius: 3px;">';
				// create each option
				foreach ($other_option as $opts) {
					echo '<input type="checkbox" name="' . $option_name . '[]" value="' . $opts-> {$element_ID} . '"' 
						. ($tocc_options[$option_name] && in_array($opts->{$element_ID}, $tocc_options[$option_name]) ? ' checked="checked"' : '') 
						. ($disabled ? ' disabled="true"' : '') . '>' . PHP_EOL . '&nbsp;&nbsp;' . $opts-> {$element_index} 
						. ' (ID: ' . $opts-> {$element_ID} . ')'
						. '<br /></input>';
				}
				echo '</div>';
			}
			break;
	}
	echo '</p>' . PHP_EOL;

	// display hidden tooltip text
	if ($tooltip_text) {
		echo '<div id="' . $option_name . '_tip" style="display:none;margin-left:8px;color:#21759b;">' . $tooltip_text 
			. '<br />&nbsp;<br /></div>' . PHP_EOL;
	}
}

// Option section footer.
function tocc_option_end() {
	echo '</div>' . PHP_EOL 
		. '</div>' . PHP_EOL 
		. '</div>' . PHP_EOL;
}

// Construct the admin settings page
function tocc_plugin_options() {

	// text domain loaded here to allow setting headers and tooltip text to be translated
	load_plugin_textdomain('tocc', constant('TOCC_PLUGIN_PATH'), basename(dirname(__FILE__)));
	
	// summarizer methods
	$tocc_summ_methods = __('Auto,All in One SEO,Post Excerpt,Page or Post Body,Custom META', 'tocc');
	$tocc_summHide_methods = __('None,All,Parent,Children', 'tocc');

	// sort methods
	$tocc_sort_methods = __('Menu Order,Alphabetically,Post Date,Author', 'tocc');
	
	// help methods
	$tocc_help_methods = __('Never,Always,Hidden by Default (with help icon),Shown by Default (with help icon)', 'tocc');

	// retrieve current options
	$tocc_options = get_option('tocc_options');

	// add javascript to show and hide option sections
	echo '<script type="text/javascript">' . PHP_EOL 
			. '<!--' . PHP_EOL . 'function toggleVisibility(id) { ' . PHP_EOL 
			. '   var e = document.getElementById(id);' . PHP_EOL 
			. '   if(e.style.display == "block")' . PHP_EOL 
			. '      e.style.display = "none";' . PHP_EOL 
			. '   else' . PHP_EOL 
			. '      e.style.display = "block";' . PHP_EOL 
			. '	document.getElementById(id.concat("_display")).value = e.style.display;' . PHP_EOL 
			. '}' . PHP_EOL 
			. '-->' . PHP_EOL
			. '</script>' . PHP_EOL;
	
	// security checks
	if(is_admin()) {
	
		// restore defaults button pressed
		if (isset ($_POST['set_defaults']) && check_admin_referer('tocc-change-options-nonce')) {
			$tocc_options = $GLOBALS['tocc_defaults'];
			update_option('tocc_options', $tocc_options);
			echo '<div id="message" class="updated fade"><p><strong>' . __('Default options loaded!', 'tocc') . '</strong></p></div>';
		}
	
		// create new page button pressed
		elseif (isset ($_POST['create_page']) && check_admin_referer('tocc-change-options-nonce')) {
		
			// update page and title options
			$tocc_options['toc_page'] = stripslashes((string) $_POST['toc_page']);;
			$tocc_options['toc_slug'] = stripslashes((string) $_POST['toc_slug']);
			update_option('tocc_options', $tocc_options);
		
			// insert a new table of contents page
			$postID = wp_insert_post(array (
				'post_title' => $tocc_options['toc_page'],
				'post_content' => '<!-- ' . constant('TOCC_TAG') . ' -->',
				'post_status' => 'publish',
				'post_author' => 1,
				'post_name' => $tocc_options['toc_slug'],
				'post_type' => 'page',
				'menu_order' => 9999,
				'post_excerpt' => __('This page contains the site table of contents. Use it to quickly find content on this website.', 'tocc'),
				'comment_status' => 'closed'
			));
		
			// display success or failure message
			echo '<div id="message" class="updated fade"><p><strong>';
			if ($postID) {
				echo '(ID ' . $postID . ')' . __('TOC page was successfully created.', 'tocc');
			} else {
				echo __('TOC page could not be created!', 'tocc');
			}
			echo '</strong></p></div>';
		}
	
		// update button pressed
		elseif (isset ($_POST['info_update']) && check_admin_referer('tocc-change-options-nonce')) {
		
			// turn on or off debugging - ignore any other option changes if debugging status modified		
			if ($_POST['toc_title'] == 'DEBUG_ON') {
				update_option('tocc_debug_mode', 'checked');
			}
			elseif ($_POST['toc_title'] == 'DEBUG_OFF') {
				delete_option('tocc_debug_mode');

			// update all options
			} else {
				// use the defaults array as a source for option names
				$tocc_options = $GLOBALS['tocc_defaults'];
				foreach (array_keys($tocc_options) as $option) {
					// do not strip slashed from array options as this breaks the array
					$tocc_options[$option] = (!is_array($_POST[$option]) ? stripslashes($_POST[$option]) : $_POST[$option]);
				}
				update_option('tocc_options', $tocc_options);
			}
		
			// show success message
			echo '<div id="message" class="updated fade"><p><strong>' . __('Configuration updated!', 'tocc') . '</strong></p>';
			// ask to turn back on author ack as an act of kindness
			if (!$tocc_options['show_ack']) {
				echo '<p>' . __('The author of this software has spent uncountable hours of his own time to develop and test this plugin only to offer it free to you. Please consider leaving the author acknowledgment enabled as a small token of your appreciation. If not, please vote for this plugin on the <a href="http://wordpress.org/extend/plugins/table-of-contents-creator/">Wordpress Table of Content Creator plugin home page</a>. Thank you.', 'tocc') . '</p>';
			}
			echo '</div>';
			
			// if WP-SuperCache is installed, automatically clear cached pages
			if(function_exists('wp_cache_clean_cache')) {
				global $file_prefix;
				wp_cache_clean_cache($file_prefix);
			}
			
		}
	}

	// introduction
	echo '<div class="wrap"><h2>Table of Contents Creator v' . constant('TOCC_VERSION') . '</h2>' . PHP_EOL 
		. '<div id="poststuff" class="metabox-holder">' . PHP_EOL 
		. '<form method="post" action="" name="dofollow">' . PHP_EOL 
		. '<p>' . __('The TOC Creator Plugin for Wordpress automatically generates a site wide table of content for your blog. The  TOC will always remain up date and is a SEO freindly way of linking all pages on your site.', 'tocc') . '</p>' . PHP_EOL 
		. '<p>';
	
	printf(__('To use the plugin, insert the trigger text <code>&lt;&#45;&#45; %s &#45;&#45;&gt;</code> into an existing page. The trigger will be auomatically replaced with a complete site wide table of content. You may also use the <code>create page</code> button below.', 'tocc'), (htmlspecialchars(constant('TOCC_TAG'))));
	
	echo '</p>' . PHP_EOL . '<h4>' . __('Click on the menu title to expand the option section then click on the option titles to get help!', 'tocc') . '</h4>' . PHP_EOL;
	
	// general options
	tocc_option_header(__('General Options', 'tocc'), 'tocc_general', false);
	tocc_option($tocc_options, __('Table of Contents Title', 'tocc'), __('This text entered here will be displayed at the top of the table of content page. Leave blank to display no title. Include any required formatting tags.', 'tocc'), 'toc_title', 'text', '40');
	tocc_option($tocc_options, __('Icon Theme', 'tocc'), __('Select the icon theme used to display next to each table of contents entry. Icon themes are a set of icons that are related in look and style. Icons are used to differentiate pages, links, blog categories, blog entries and form comments. Select <em>none</em> to inhibit the displaying of icons.', 'tocc'), 'icon_set', 'select', explode(',', constant('TOCC_ICON_THEMES')), 0, '');
	tocc_option($tocc_options, __('Default Sort Order', 'tocc'), __('Selects the default sort order for pages and posts. The sort order can be overridden by a vistor using the options menu.', 'tocc'), 'sort_order', 'select', explode(',', $tocc_sort_methods), 0, '');
	tocc_option($tocc_options, __('Date Format', 'tocc'), __('Used to set the date display format when showing blog or forum comment dates. See <a href="http://php.net/manual/en/function.date.php" target="_none">http://php.net/manual/en/function.date.php</a> for format string details.', 'tocc'), 'date_format', 'text', '40');
	tocc_option($tocc_options, __('Show Separate Lists', 'tocc'), __('Displays the page list, blog posts and forum comments as separate lists under a configurable title. See the settings below for <em>Page Title</em>, <em>Blog Title</em> and <em>Forum Comments Title</em>. The title strings may contain HTML formatting tags.', 'tocc'), 'separate_lists', 'checkbox');
	tocc_option($tocc_options, __('Show Options Menu', 'tocc'), __('Displays a menu at the top of the table of contents page allowing vistors to format the table of contents. Options include expand and contract child elements, show or hide summaries and sort the table of contents.', 'tocc'), 'show_menu', 'checkbox');
	tocc_option($tocc_options, __('Option Menu Item Texts', 'tocc'), __('This is the text used for each option menu item. It is settable so that the menu can be easily modified to reflect the blogs host language. Ensure that the menu items appear in the same order. Commas are used to delimit each menu item and should therefore not be used in a menu item description. Leave the item text blank (no text between the commas) to disable specific options.', 'tocc'), 'menu_texts', 'textarea', '65,2');
	tocc_option($tocc_options, __('Options Menu Border Color', 'tocc'), __('A small options menu is displayed to each visitor allowing them to sort the table of contents, expand or collapse child elements and display or hide summaries. This option defines the color used for the menu border and separator bars. HTML color codes can be found <a href="http://html-color-codes.info/">here</a>. Don\'t forget to include the leading hash.', 'tocc'), 'menu_borderColor', 'text', '10');
	tocc_option($tocc_options, __('Show Help', 'tocc'), __('Selects whether to display help instructions to the user.', 'tocc'), 'show_helptext', 'select', explode(',', $tocc_help_methods), 0, '');
	tocc_option($tocc_options, __('Help Icon ToolTip', 'tocc'), __('Tooltip text to be displayed when the user hovers the mouse over the help icon. Applicable only if help icon is selected in above option.', 'tocc'), 'help_tooltip', 'text', 40);
	tocc_option($tocc_options, __('Help Text', 'tocc'), __('Help text to display below the page title. Not applicable if show help is set to never.', 'tocc'), 'help_text', 'textarea', '65,4');
	tocc_option($tocc_options, __('Allow Children to be Expanded and Collapsed', 'tocc'), __('Allows parent elements, such as a page with sub pages or a category with posts, to expand or collapse the child elements similar to a tree view in Windows Explorer.', 'tocc'), 'allow_expand', 'checkbox');
	tocc_option($tocc_options, __('Hide Children by Default', 'tocc'), __('Hides all children by default. This option will only be used if the above option is selected.', 'tocc'), 'expand_hideByDef', 'checkbox');
	tocc_option($tocc_options, __('Author Prefix', 'tocc'), __('The text to be displayed before the authors name.', 'tocc'), 'author_prefix', 'text', '5');
	tocc_option($tocc_options, __('More Prompt', 'tocc'), __('If enabled, the <em>more prompt</em> is displayed at the bottom of the blog posts and forum comments lists if not all posts or comments are displayed. clicking the link will display a new page showing all available posts or comments. This enables a smaller subset of the posts and/or comments to be displayed on the main site-map allowing greater readability.', 'tocc'), 'more_prompt', 'text', '40');
	tocc_option($tocc_options, __('Show Author Credit', 'tocc'), __('Displays a small unobtrusive acknowledgement at the bottom of the table of contents. Please consider leaving the acknowledgement enabled as a token of appreciation for the hours spent developing this plugin.', 'tocc'), 'show_ack', 'checkbox');
	tocc_option_end();
	
	// summarizer options
	tocc_option_header(__('Summarizer Options', 'tocc'), 'tocc_summarizer');
	tocc_option($tocc_options, __('Show Summary', 'tocc'), __('If enabled, a small summary is automatically created for each page or post. The summary may be hidden by default allowing visitors to dynamically show or hide summaries as required. Summaries may be extracted from All in One SEO descriptions, post excerpts, post or page body text or custom META tags.', 'tocc'), 'show_sum', 'checkbox');
	tocc_option($tocc_options, __('Extract Summary From', 'tocc'), __('Determines where the page or post summary is to extracted from. If Auto is selected, a number of different methods are examined in hierarchial order to obtain the first non-blank description. Other valid options include All in One SEO description tags (requires the All in One SEO plugin to be installed), Post Excerpt, Page or Post Body and Custom META. The option below can be used to configure the custom META tag.', 'tocc'), 'sum_extractFrom', 'select', explode(',', $tocc_summ_methods), 0, '');
	tocc_option($tocc_options, __('Custom Summary Extraction META', 'tocc'), __('Defines the custom META tag if selected above. Wordpress allows custom fields to be added to a page or post. The custom fields are displayed as META tags in the post or page header and can be used by search engines. If you do not use the All in One SEO plugin, it is a good idea to create a custom Description META and include a brief description of the post in the value field.', 'tocc'), 'sum_customMeta', 'text', '40');
	tocc_option($tocc_options, __('Summary Length Limit', 'tocc'), __('Sets the maximum number of characters to display for each summary. Set to 0 to disable this option. It is a good idea to keep this short to reduce page load times if the table of contents contains many pages or posts.', 'tocc'), 'sum_charLimit', 'text', '5');
	tocc_option($tocc_options, __('Display Ellipses if Summary Exceeds Limit', 'tocc'), __('If set, ellipses (...) will be displayed if the extracted summary exceeds the above summary length limit.', 'tocc'), 'sum_showEllipses', 'checkbox');
	tocc_option($tocc_options, __('Hide Summaries by Default', 'tocc'), __('Summaries may be hidden or shown by default. Selecting None will display all summaries. Select All to hide all summaries. Select Parent to hide the summaries of all parent pages (those with children). Select Children to hide the summaries of all children pages.', 'tocc'), 'sum_hideByDef', 'select', explode(',', $tocc_summHide_methods), 0, '');
	tocc_option($tocc_options, __('Allow Individual Summaries to be Shown or Hidden', 'tocc'), __('If selected, visitors will have the option of displaying or hiding summaries for individual posts or pages. A small icon will be displayed next to the post or page.', 'tocc'), 'sum_allowHide', 'checkbox');
	tocc_option_end();
	
	// static page options
	tocc_option_header(__('Static Page Options', 'tocc'), 'tocc_static_page');
	tocc_option($tocc_options, __('Show Static Pages', 'tocc'), __('Show static pages within the table of contents. Note that the pages will be sorted in menu order. Use the <strong>order</strong> attribute of each page (settable via page edit) to set the menu order.', 'tocc'), 'show_static', 'checkbox');
	tocc_option($tocc_options, __('Page Title', 'tocc'), __('This text is used as the page list heading. The page list heading is only displayed if the separate page, blog post and forum comment lists option is checked. See <em>General Settings</em> for more information.', 'tocc'), 'page_title', 'text', '40');
	tocc_option($tocc_options, __('Page Depth', 'tocc'), __('Determines the page depth to display. For example if set to 1, all parent pages will be shown and the immediate children of each parent page will also be shown. Set to 0 to show parent pages only.', 'tocc'), 'page_depth', 'text', '5');
	tocc_option($tocc_options, __('Link Class', 'tocc'), __('If set, any anchor tags assigned to this class will be shown as a list item under the page in which the link was found. To use, add class="<em>linkclass</em>" to each &lt;a&gt; tag you wish to display in the table of contents. Leave blank to disable this option.', 'tocc'), 'link_class', 'text', '40');
	tocc_option($tocc_options, __('Show Page Author', 'tocc'), __('Displays the author of the page.', 'tocc'), 'page_author', 'checkbox', '');
	tocc_option($tocc_options, __('Show Page Date', 'tocc'), __('Displays the creation date of the page.', 'tocc'), 'page_date', 'checkbox', '');
	tocc_option($tocc_options, __('Show Page Comment Count', 'tocc'), __('Displays the number of page comments next to each page listing.', 'tocc'), 'page_comments', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Exclude Pages', 'tocc'), __('Select one or more pages to exclude from the table of content. By default, all pages are enabled.', 'tocc'), 'page_exclude', 'checkbox', get_pages('depth=-1&sort_column=menu_order'));
	tocc_option_end();
	
	// blog options
	$no_blog = !is_blog_installed();
	tocc_option_header(__('Blog Post Options', 'tocc'), 'tocc_blog_post');
	tocc_option($tocc_options, __('Show Blog Posts', 'tocc'), __('Show blog posts within the table of contents. Blog entries will automatically be separated by category.', 'tocc'), 'show_blog', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Blog Title', 'tocc'), __('This is a mock page name used for sites that do not have a static post page. See the <em>Wordpress Settings/Reading</em> option for more details. The blog title is also used if static pages are not displayed or page lists, blog posts and forum comments are separately displayed.', 'tocc'), 'blog_title', 'text', '40', $no_blog);
	tocc_option($tocc_options, __('Show Posts Total', 'tocc'), __('Displays the total number of posts in brackets for each category next to each category listing.', 'tocc'), 'blog_info', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Category Prefix', 'tocc'), __('Displays this text before each blog category. Leave blank if you do not wish to display a prefix. The prefix may include HTML code.', 'tocc'), 'blog_cattext', 'text', '40', $no_blog);
	tocc_option($tocc_options, __('Number of Blog Posts', 'tocc'), __('Determines the maximum number of blog posts to display in each category. Set to -1 to display all posts.', 'tocc'), 'blog_count', 'text', '5', $no_blog);
	tocc_option($tocc_options, __('Don\'t Show Categories', 'tocc'), __('If selected, all posts will be shown in one large list without being separated into individual categories. Use this option if your blog does not make use of post categories.', 'tocc'), 'blog_nocats', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Show as Flat List', 'tocc'), __('If selected the blog categories will not be displayed as an indented hierarchial list, but instead as a flat list.', 'tocc'), 'blog_flat', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Show More Prompt', 'tocc'), __('Displays a link at the bottom of the posts list to display more posts. The link will only be displayed if the current number of posts is limited (see the option above). The link text is set in the <em>general options</em> section.', 'tocc'), 'blog_more', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Show Posts Warning', 'tocc'), __('Displays a message after the blog page name to alert the reader that only the latest <em>n</em> posts are shown. This text is not shown if all posts are displayed (by entering 0 in the "Number of Blog Entries" setting above).', 'tocc'), 'blog_warn', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Posts Warning Text', 'tocc'), sprintf(__('The message to display if the post warning is enabled. Use %s as a place holder for the number of blog entries being displayed.', 'tocc'), ('<code>' . constant('TOCC_NUMBER_SENTINEL') . '</code>')), 'blog_warntext', 'text', '40', $no_blog);
	tocc_option($tocc_options, __('Show Post Author', 'tocc'), __('Displays the author of the post.', 'tocc'), 'blog_author', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Show Post Date', 'tocc'), __('Displays the creation date of the post.', 'tocc'), 'blog_date', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Show Post Comment Count', 'tocc'), __('Displays the number of post comments next to each post listing.', 'tocc'), 'blog_comments', 'checkbox', '', $no_blog);
	tocc_option($tocc_options, __('Exclude Categories', 'tocc'), __('Select one or more categories to exclude from the table of content. By default, all categories are enabled.', 'tocc'), 'blog_exclude', 'checkbox', $categories = get_categories(), $no_blog, 'cat_name', 'cat_ID');
	tocc_option_end();
	
	// forum options
	$no_forum = !function_exists('latest_activity');
	tocc_option_header(__('Forum Comment Options', 'tocc'), 'tocc_forum_comment');
	echo '<p><em>' . __('Note that currently this option only works with Forum Server and WP-Forum.', 'tocc') . '<br /></em></p>';
	tocc_option($tocc_options, __('Show Forum Comments', 'tocc'), __('Show forum comments within the table of contents. The latest forum comments will be shown first. Note that currently this option only works with <em>Forum Server</em> and <em>WP-Forum</em>.', 'tocc'), 'show_forum', 'checkbox', '', $no_forum);
	tocc_option($tocc_options, __('Forum Title', 'tocc'), __('This text is used as the forum list heading. The forum list heading is only displayed if the option is checked to separate the page, blog post and forum comment lists. See <em>General Settings</em> for more information.', 'tocc'), 'forum_title', 'text', '40', $no_forum);
	tocc_option($tocc_options, __('Forum Page Name', 'tocc'), __('This is the name of the WordPress page that is used as a place holder to display the forum. The page name is required so that the plugin can display the forum comments underneath the correct page name. The forum page typically consists only of a tag such as &lt;!--VASTHTML--&gt;.', 'tocc'), 'forum_page', 'select', get_pages('depth=-1&sort_column=menu_order'), $no_forum);
	tocc_option($tocc_options, __('Number of Forum Comments', 'tocc'), __('Determines the maximum number of forum comments to display. The latest comments will be displayed first. Set to -1 or less to display all comments (not recommended).', 'tocc'), 'forum_count', 'text', '5', $no_forum);
	tocc_option($tocc_options, __('Show More Prompt', 'tocc'), __('Displays a link at the bottom of the comments list to display more comments. The link will only be displayed if the current number of comments is limited (see the option above). The link text is set in the <em>general options</em> section.', 'tocc'), 'forum_more', 'checkbox', '', $no_forum);
	tocc_option($tocc_options, __('Show Comments Warning', 'tocc'), __('Displays a message after the forum page name to alert the reader that only the latest <em>n</em> comments are shown. This text is not shown if all comments are displayed (by entering 0 in the "Number of Forum Comments" setting above).', 'tocc'), 'forum_warn', 'checkbox', '', $no_forum);
	tocc_option($tocc_options, __('Comment Warning Text', 'tocc'), sprintf(__('The message to display if the comment warning is enabled. Use %s as a place holder for the number of comment entries being displayed.', 'tocc'), ('<code>' . constant('TOCC_NUMBER_SENTINEL') . '</code>')), 'forum_warntext', 'text', '40', $no_forum);
	tocc_option($tocc_options, __('Show Comment Author', 'tocc'), __('Displays the author of the comment.', 'tocc'), 'forum_author', 'checkbox', '', $no_forum);
	tocc_option($tocc_options, __('Show Comment Date', 'tocc'), __('Displays the creation date of the forum comment.', 'tocc'), 'forum_date', 'checkbox', '', $no_forum);
	tocc_option_end();
	
	// update options and restore defaults buttons
	echo '<h4>' . __('Update Options', 'tocc') . '</h4>' . PHP_EOL
		. '<p>' . __('In order to save any changes, you must press the <em>Update Options</em> button before leaving this page or using the create page facility below.', 'tocc') . '</p>' . PHP_EOL
		. '<input type="submit" name="info_update" value="' . __('Update Options', 'tocc') . ' &raquo;" />' 
		. '&nbsp;&nbsp' 
		. '<input type="submit" style="background-Color:#E0FFFF;" onmouseover="this.style.backgroundColor=\'red\'" ' 
			. 'onmouseout="this.style.backgroundColor=\'#E0FFFF\'" onclick="return confirm(\'' 
			. __('Click OK to reset to defaults. Any settings will be lost!', 'tocc') . '\');" ' 
			. 'name="set_defaults" value="' . __('Load Default Options', 'tocc') . ' &raquo;" />' . PHP_EOL;
	
	// create new page options
	echo PHP_EOL . '<h4>' . __('Create Page', 'tocc') . '</h4>' . PHP_EOL 
		. '<p>' . __('For your convenience, the plugin can also create a new table of content page for you. Simply fill in the title and slug (path) details and press the <em>Create Page</em> button to create the page. The trigger text will be added automatically to the new page.', 'tocc') . '</p>' . PHP_EOL
		. '<p><strong>' . __('Please note:', 'tocc') . ' </strong> ' 
			. __('If you have changed any of the default settings, make sure you have pressed the <em>Update Options</em> button above before creating the new page.', 'tocc') . '</p>' . PHP_EOL;
	tocc_option($tocc_options, __('Page Title', 'tocc'), __('The table of content page name. The newly created page will be called this name. Ensure that there are no current pages of the same name.', 'tocc'), 'toc_page', 'text', '40');
	tocc_option($tocc_options, __('Page Slug', 'tocc'), __('The page slug is used to determine the address of the page. It is typically the same name as the page name, <strong>however</strong> the page slug cannot contain spaces. It is common practice to replace a space with a dash.', 'tocc'), 'toc_slug', 'text', '40');
	echo '<input type="submit" name="create_page" value="' . __('Create Page', 'tocc') . ' &raquo" />' . PHP_EOL;
	
	// acknowledgments
	echo PHP_EOL . '<h4>' . __('Acknowledgments', 'tocc') . '</h4>' . PHP_EOL
		. '<p>' . __('I wish to personally acknowledge the following people for their valuable contributions:', 'tocc') . '</p>' . PHP_EOL 
		. '<div class="inside">'  . PHP_EOL
		. '<a href="http://www.carlopoliti.net/">Carlo Politi</a> - ' . __('Italian translation', 'tocc') . '<br />' . PHP_EOL
		. '<a href="http://3d.echeva.com/">Fernando J. Echevarrieta (echeva)</a> - ' . __('Spanish (castilian) translation', 'tocc') . '<br />' . PHP_EOL
		. '<a href="http://ontologicalwar.com/">Scott</a> - ' . __('Suggested and helped test automated summary creation', 'tocc') . '<br />' . PHP_EOL
		. '<a href="http://photomentors.com/">Steadman Uhlich</a> - ' . __('Major version pre-release validator', 'tocc') . '<br />' . PHP_EOL
		. '<a href="http://www.lyfoung.com/">Txia</a> - ' . __('French translation', 'tocc') . '<br />' . PHP_EOL
		. '<a href="http://www.u2u.ir/">HamidReza Kazemi</a> - ' . __('Persian translation', 'tocc') . PHP_EOL
		. '</div>' . PHP_EOL;
	
	// create a nonce for security purposes
	wp_nonce_field('tocc-change-options-nonce');
	
	echo '</form></div></div>' . PHP_EOL;
}
?>
