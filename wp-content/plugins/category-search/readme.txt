=== Category Search ===
Contributors: freediver
Donate link: https://www.networkforgood.org/donation/MakeDonation.aspx?ORGID2=520781390
Tags:  search, category, categories
Requires at least: 2.0
Tested up to: 3.3
Stable tag: trunk

Category Search adds the option to display category names in search results. Useful when you have a lot of categories.


== Description == 

Category Search adds the option to display category names in search results. Useful when you have a lot of categories and you want to display them as a part of search results. 


Plugin by <a href="http://www.prelovac.com/vladimir">Vladimir Prelovac</a>. Looking for <a href="http://www.prelovac.com/vladimir/services">WordPress Services</a>?

== Installation ==

1. Upload the whole plugin folder to your /wp-content/plugins/ folder.
2. Go to the Plugins page and activate the plugin.
3. Use the Options page to change your options
4. Use the following code in your theme template most probably Search Results (search.php)

<?php if (class_exists('CategorySearch')) $CategorySearch->Show(); ?>

Also, CategorySearch is fully compatible with [wp_list_categories() ](http://codex.wordpress.org/Template_Tags/wp_list_categories  "wp_list_categories()") 

This mean you can further tweak the category search results by adding additional parameters. Example:

$CategorySearch->Show('orderby=count');


== Screenshots ==

1. Matching categories displayed

== License ==

This file is part of Category Search.

Category Search is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

Category Search is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Category Search. If not, see <http://www.gnu.org/licenses/>.


== Frequently Asked Questions ==

= How do I correctly use this plugin? =

In order for category search results to show you need to add the follwoing code to your theme search.php template. 

<?php if (class_exists('CategorySearch')) $CategorySearch->Show(); ?>

= Can I suggest an feature for the plugin? =

Of course, visit <a href="http://www.prelovac.com/vladimir/wordpress-plugins/category-search#comments">Category Search Home Page</a>

= I love your work, are you available for hire? =

Yes I am, visit my <a href="http://www.prelovac.com/vladimir/services">WordPress Services</a> page to find out more.