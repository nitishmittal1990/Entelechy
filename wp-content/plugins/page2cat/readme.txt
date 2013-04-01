=== Category Pages & Posts Shortcodes ===
Contributors: swergroup, pixline
Donate link: http://swergroup.com
Tags: category, categories, pages, posts, page, post, integration, shortcode, shortcodes, list, archives
Requires at least: 3.4.2
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Useful shortcodes to display a post or page content or a list of posts.

== Description ==

Category Pages & Posts Shortcodes is a complete rewrite of the "Category Page" plugin.
It offers useful shortcodes to display a post or page content, or a list of posts.
Also it allow exclusive mutual bind between a Category and a Page, in order to display the page content as "header" of category archives. 

You can safely use shortcodes inside a post or a page, our you can embed them in your theme 
using the [do_shortcode](http://codex.wordpress.org/Function_Reference/do_shortcode) WordPress function like that:

`<?php do_shortcode('[showsingle pageid="<id_of_page>"]'); ?>`

NOTE: This plugin requires WordPress 3.4.x, and will **break** your current ~2.5 setup.
It also won't be compatible with [Category Page Extender](http://categorypageextender.wordpress.com) anymore. 
On activation, it will clean every option set by the previous versions. Please test it offline first.

= [showsingle pageid="" postid="" showheader="" header="" headerclass="" wrapper="" wrapperclass=""] =

This shortcode will show a single post or page. The only required argument is either *postid* or *pageid*.

* **pageid** - ID of the page you want to display (either this or postid)
* **postid** - ID of the post you want to display (either this or pageid)
* **showheader** - if *"true"*, show the page title
* **header** - level of title HTML header (from 1 to 6, 2 default)
* **headerclass** - header custom CSS class (default: `aptools-single-header`)
* **wrapper** - if *"true"*, wraps the whole output with `<div class="aptools-wrapper"></div>`
* **wrapperclass** - wrapper custom CSS class (default: `aptools-wrapper`)

= [showlist catid="" lenght="" listclass="" excerpt="" wrapper="" wrapperclass=""] =

This shortcode will show a list of posts from you. Required argument is catid.

* **catid** - ID (**not** slug, nor name) of the category you want to list
* **lenght** - how many posts listed (default: 10. don't set it too high..)
* **listclass** - list element's custom CSS class (default: `aptools-list`) 
* **excerpt** - if *true* shows excerpt alongside title
* **wrapper** - if *"true"*, wraps the whole output with `<div class="aptools-wrapper"></div>`
* **wrapperclass** - wrapper custom CSS class (default: `aptools-wrapper`)

= [showauto] =

This shortcode works only on category archives templates: it will display on each category the linked page content.
You can set up the link either on the page edit admin area or in the category edit area. 
Please note: a single page can be linked this way to a single category only. 
If you need more flexible options you should take advantage of [WordPress template hierarchy](http://codex.wordpress.org/Template_Hierarchy): 
you can create a `category-<category_name>.php` file with a `[showsingle]` shortcode. 


GPL2(C) 2008+ [SWER Sviluppo siti internet Torino](http://swergroup.com/sviluppo/siti-internet-torino/)

[Git source code on Bitbucket](http://dev.swergroup.com/pages-and-posts-shortcodes)

[Support Forum on wordpress.org](http://wordpress.org/support/plugin/page2cat)

== Installation ==

1. Download the plugin, unzip, upload folder to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Look for the documentation on the plugin page and learn how to use it.
1. Enjoy!

== Frequently Asked Questions ==

= How to show all the posts of a certain category in one page? =

`[showlist catid="<category_ID>" length="<number_of_posts>"]`

where category_ID is your category number and number_of_posts is a number higher than your category posts count.

= How can I modify my theme? =

http://wordpress.org/support/topic/how-to-place-this-plugin-in-my-theme

= Is this plugin supported? =

We'll try our best to support it on the [support forum](http://wordpress.org/support/plugin/page2cat).
If you rely on this plugin for commercial purposes please get in touch with our [helpdesk](http://swergroup.zendesk.com).

= I used to see [page|menu|box] Category Page, where is it? =

It probably isn't here. WordPress changed a lot since 2007. 

You should be able to solve with shortcodes quite everything you were used to. If you can't manage it drop us a line in the support forum and we'll try to find a solution.

= Why you didn't update Category Page and rewrote it instead? =

The original concept and codebase were obsolete, with a lot of logic flaws. We just couldn't avoid it.

= Where is the last pre 3.0 version? =

Last pre-3.0 version is 2.6.3, [SVN r367559](http://plugins.trac.wordpress.org/browser/page2cat?rev=367559)

You can download it by SVN client: 

`svn -r 367559 checkout http://plugins.svn.wordpress.org/page2cat/trunk/ page2cat-2.6.3`

== Screenshots ==

1. Sample page inclusion as an archive page header via [showsingle]

== Changelog ==

= 3.0.6 =
* (11/11/2012) FIX Warning on admin Page area. 
* (11/11/2012) FIX Better headers and descriptions
* (11/11/2012) FIX [showauto] formatting and styles

= 3.0.5 =
* (11/11/2012) Fix error on the admin Page area. 

= 3.0.3 =
* (10/11/2012) Better descriptions in edit-page and edit-category forms
* (10/11/2012) Fix links

= 3.0.2 =
* (04/11/2012) showlist query fix.

= 3.0.1 =
* (03/11/2012) *[showlist]* shortcode fix, category › page link restored.

= 3.0 =
* (30/10/2012) Complete rewrite for WordPress 3.4+

== Upgrade Notice ==

= 3.0.6 =
* (11/11/2012) FIX warning on admin page area, [showauto] formatting, headers and descriptions.

= 3.0.5 =
* (11/11/2012) Fix error on the admin Page area. 

= 3.0.3 =
CAUTION: This plugin requires WordPress 3.4.x, and will **break** your WP 2.5 setup.
- Better descriptions in edit-page and edit-category forms
- Fix links

= 3.0.2 = 
CAUTION: This plugin requires WordPress 3.4.x, and will **break** your WP 2.5 setup.
FIX showlist category query.

= 3.0 =
This plugin requires WordPress 3.4.x, and will **break** your WP 2.5 setup.
It also won't be compatible with [Category Page Extender](http://categorypageextender.wordpress.com) anymore. 
On activation, it will clean every option set by the previous versions. Please test it offline first.

