=== Menubar ===
Contributors: dontdream
Tags: menu, menubar, navigation, dropdown, suckerfish, superfish
Requires at least: 2.6
Tested up to: 3.4.2
Stable tag: 5.0

Single and multi-level menus for your WordPress site, styled with customizable menu templates.

== Description ==

With Menubar you can easily build and manage single and multi-level menus for your WordPress site.

* Build your menus selecting item types among Home, FrontPage, Heading, Tag, TagList, Category, CategoryTree, Page, PageTree, Post, SearchBox, External, PHP, Custom, in any combination

* Display a menu inserting in your theme, e.g. at the end of *header.php*, the line
<br>**&lt;?php do_action ('wp_menubar', 'your_menu_name_here'); ?&gt;**

* Use the *Menubar* widget to display your menus in a widget ready area or sidebar

* To style your menus, select one of the default Menubar templates, customize your selected template, or use the CSS from your theme

* Customized Menubar templates can be moved into an independent folder, so you can upgrade to a new Menubar version without losing your changes

== Installation ==

See the full [Menubar 5.0 Documentation](http://www.dontdream.it/menubar).

== Changelog ==

= 5.0 =
* All the default Menubar templates are now included in the plugin folder, so you don't have to download them separately.
= 4.10 =
* Fixed a compatibility bug with the qTranslate plugin
= 4.9 =
* Enhanced the PHP item type, so you can dynamically generate both label and link of a menu item
* Fixed a bug that prevented highlighting in the External and PHP types when a querystring was present
= 4.8 =
* Added the PHP item type, so you can build your menu items dynamically with PHP code
= 4.7 =
* Added the Exclude field to PageTree and CategoryTree types, so you can prevent one or more elements from being displayed 
* Added the Headings field to PageTree and CategoryTree types, so you can make one or more elements non clickable 
= 4.6 =
* Added the Tag and TagList types (only with new template versions)
* Added the Depth field to PageTree and CategoryTree types, so you can choose how many tree levels to display 
= 4.5 =
* Added support for icons in menu items (only with new templates)
* Fixed a bug affecting single and double quote characters that were incorrectly escaped 
= 4.4 =
* Improved performance moving the Menubar data from a DB table to a serialized option, so just a single DB call per page is needed
= 4.3 =
* Fixed a bug affecting the *Add Menu Item* and *Edit Menu Item* forms in IE6 and IE7
= 4.2 =
* Added support for the wp-config.php FORCE\_SSL\_ADMIN option
* Added support for the new Superfish template
= 4.1 =
* Improvements to the new template structure - older templates are supported as well
* Added the Custom type to insert custom HTML in your menu (only with new templates)
* The name of a menu item is automatically generated when the *Name* field is left blank (only with new templates)
= 4.0 =
* New template structure for better customization - old templates are supported as well
* Added the SearchBox type to integrate a search box in your menubar (only with new templates)
* Moved the *Menubar* admin page under the *Appearance* admin menu
= 3.6 =
* Improved the first installation procedure with more user-friendly messages
= 3.5 =
* Added the *Menubar* widget to display menus in a widget ready area (only with WordPress 2.8 or higher)
= 3.4 =
* Improved compatibility with the qTranslate plugin
= 3.3 =
* Fixed a bug affecting menu names containing a space, and a bug in a special menu reordering case
= 3.2 =
* Added more options to easily rearrange the order of your menu items
= 3.1 =
* Menubar templates are now stored in the *menubar-templates* folder, so future automatic upgrades won't overwrite your template customizations
= 3.0 =
* First version hosted in the WordPress Plugin Directory
