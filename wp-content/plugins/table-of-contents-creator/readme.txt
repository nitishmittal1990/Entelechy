=== Table of Contents Creator ===
Contributors: Mark Beljaars
Tags: navigation, links, SEO, table of contents, site map
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: 1.6.4.1

Table of Contents Creator automatically generates a highly customizable dynamic site wide table of contents that is always up-to-date.

== Description ==

Table of Contents Creator (TOCC) automatically generates a highly customizable dynamic site wide table of contents that is always up-to-date. All entries are navigable making your site very SEO friendly. TOCC can be configured to display static pages, blog entries and forum comments. Another great feature of TOCC is the ability  to include anchor tags marked with a special class. This feature allows links to articles, downloads or even other sites to appear within the table of contents as if they are part of your site's navigation.

[youtube http://www.youtube.com/watch?v=B4l4yMS7w3E]

To generate a table of contents, simply include the `<!-- toc-creator -->` tag on any page, or use the handy page creation feature located on the plugin admin page.

Note that the table of contents is automatically generated every time it is displayed and as such it is always up to date! New comments, pages, blogs and blog categories will automatically appear as soon as they are created.

== Screenshots ==

1. An example table of contents split into separate lists using the professional icon theme.
1. An example table of contents with help, square icon theme and drop down menu shown.
1. An example table of contents without icons, shown as a single list, no expansion, no summary, expandable help icon and no options menu.
1. An example table of contents using the haddrawn icon theme, no dates or author and category summaries only.
1. An snippet of the configuration page. Additional help is displayed by clicking on the option title. Note that there are literally dozens of configuration options used to tweak TOCC to match the style and look of any blog. TOCC is however configured with default options that will suit the needs of most users.

== New Features ==

Version 1.6 is now faster, uses less memory, is more robust and above all is extremely flexible. New features may now be added in a future release with minimal risk. Please refer to the change log for further details.

The following new features have been added. This release is all about giving more control to your visitors...

* Hierarchial categories are now supported. 
* Child elements can now be dynamically expanded or collapsed by a vistor. 
* The table of contents can now be dynamically sorted in several different orders by a visitor.
* All summaries can be dynamically shown or hidden by a visitor.
* Page help is now available to users by clicking the help icon.

== Installation ==

1. Upload the TOCC plugin to your `/wp-content/plugins/` directory and activate it.
1. Edit the TOCC settings using the admin page located under settings.
1. Place `<!-- toc-creator -->` anywhere on a page or use the page generator on the admin page. Use the HTML editor when placing the tag.

== Frequently Asked Questions ==

= When I add a new page or post, do I need to tell TOCC? =

No. TOCC will automatically add new pages, blog categories and entries, forum comments and links to the table of contents. A new table of contents is generated every time the page is displayed.

= Where does the Summarizer get its text from? =

The Summarizer is a great feature of TOCC that automatically produces a short summary of each page or post. It first attempts to retrieve the summary from the All in One SEO Plugin. If none was found, it then looks inside a custom META tag, then the post excerpt, then the post content. If all this fails, a message is displayed indicating that no description exists for this page or post. The summary is then stripped of any HTML tags and truncated to a configurable length before it is displayed.

= Why is it that some static pages don't display in the table of contents? =

Private pages are not included in the table of contents. Furthermore, you can inhibit pages from displaying using the admin settings page.

= What is an anchor link? =

Anchor links are `<a>` tags that have been marked with a special class. For example, TOCC may be configured so that any anchor tags that include the text `class="tocc"` will be included as a link in the table of contents. Note that the class identifier can be changed via the admin settings page. This feature may be used to include pages on other websites, download links, PDFs and so on. 

= Can I edit the way the table of contents is displayed? =

Definitely! There are many options on the admin settings page that allow you to individualise the way the the TOC is displayed. TOCC also ships with a tocc.css file that can be customised to display different icons, text colors and so on. All individual elements within the TOC can be formatted within the css file.

= Can I display more than one TOC with different options? =

Yes you can, however you need to override the options using the trigger tag (AKA short tag). For example, `<!-- toc-creator show_menu=yes|icon_set=2|show_static=|@blog_exclude=5,20,3,15,85 -->` will display a TOC with an options menu, the square icon set, no static pages and multiple excluded blog categories. The blog and post category IDs are shown within the TOC settings page. All TOC option names are listed in an array at the top of the source code. 

The option syntax is as follows: text to the left of the equals sign is the option name, text to the right is the value (leave blank to disable the option), `|` separates options, and `@` is used to indicate to the preprocessor that the option value is actually a comma separated array list.

= I don't want to allow my visitors to sort by author. How do I do this? =

All pull-down menu entry texts are configured within the general options pane within the plugin settings page. To disable any option within the menu, simply delete the text after the comma. Do not remove the comma. 

= Some of my pages are place holders only. How do I stop TOCC from adding a link to these pages? =

There are two ways to do this. First, you can exclude pages using the TOCC settings page. This will also exclude all children of the page and therefore may not suite all users. The other way is to allow TOCC to display the page, but not include a link to the page. This is done by setting a short tag override. To determine the ID of all pages you do not want to show links for, open the TOCC settings page and expand the Static Page Options pane. All pages should be listed here with their ID in brackets. To exclude links, modify the TOCC short tag as per the following example: `<!-- toc-creator @link_exclude=749,765 -->`. This will not display links for pages with IDs of 749 and 765.

= How do I add the TOC to the 404 page? =

Within the Wordpress theme editor, add the following line within the 404.php file... `<?php if(function_exists('tocc_show')) {tocc_show();} ?>`. Note that this line can also be added to any page, post or template file.

= I note that there is a page and category exclude option, but what if I only want to include a couple of pages or categories? =

If you have many pages and categories, and you only want to include a couple of these in the TOC, it may be tedious to individually exclude all other categories or pages. Instead, you could use the `@page_include` and `@blog_include` short code overrides. For example `<!-- toc-creator @page_include=13,187|@blog_include=8 -->` will only display pages with IDs 13 and 187 and categories with ID 8 only.

= Is it possible to exclude specific posts from the TOC? =

Yes. Use the `@blog_postExclude` short code override. For example, `<!-- toc-creator @blog_postExclude=23,31,45|show_postIDs=yes -->` will exclude posts with IDs 23, 31 and 45. Post IDs can be determined by temporarily enabling the `show_posIDs` short code override. This will show the post ID next to each post in the TOC. Remove this override once you have determined the IDs of the posts to exclude.

== Changelog ==

= 1.6.4.1 =

* Add Persian translation by [HamidReza Kazemi](http://www.u2u.ir/ "HamidReza Kazemi"). Thank you Hamid.
* Reduced file size of all icons (by around half) and standardised all icon sets using GIF alpha format. This will increase page load time.
* Added dynamic removal of non UTF-8 characters from the summary. This was causing some sites to not correctly validate as XHTML.

= 1.6.4 =

* Added table of contents sidebar widget. All widget options are set through the TOCC plugin settings page, however several options have been hardcoded as some options will not work within a widget.
* Add French translation by [Txia](http://www.lyfoung.com/ "Txia"). Thank you Txia.
* Replaced "show help" option with a selection to display the help text always, never, by default or hidden by default. Shown and hidden by default options are supplemented  
 with a help icon to toggle the help text on and off.
* Now automatically clears wp-supercache cache when TOCC options are updated.
* Added new short code overrides called @blog_include and @page_include. These allow you to specifically identify which pages and categories are to be included in the TOC.
* Added new short code override called @blog_postExclude to allow specific posts to be excluded from the TOC. Also added a new short code override called show_postIDs to allow post IDs to be temporarily displayed on the TOC.

= 1.6.3.1 =

* Removed self_URL function and now instead use the post permalink as this was incompatible with some servers. 
* TOCC url parameters now append to existing page parameters if they already exist (for example, if using default Wordpress permalink settings).

= 1.6.3 =

* Added new option to hide summaries for parent (pages with children), children (non-root pages), none or all pages and posts.
* Added new option to display the page or post comment count next to each page or post listing.
* Added new option to inhibit listing of posts under categories and instead show all posts as one large list.
* Added new option to displat a help icon. When enabled, an icon will be displayed in the top right corner. Clicking the icon will display user definable help text.
* Added new option to allow user definable text to be prefixed to each category heading.
* Added new option called "link_exclude" settable via a short-tag overide to exclude links from user selectable pages. See FAQs for more information.
* The summarizer now uses category descriptions to generate summaries for categories.
* Fixed compatility issues with Forum Server version 1.4.
* Prefixed all TOCC classes with "tocc_". Tocc used common class names such as "footer" and "date". This conflicted with some themes. The prefix should make all css classes unique, and as such the table of contents should now appear the same across different sites.
* Modified self_URL function to work with both IIS and Apache. The previous function worked with Apache only. 

= 1.6.2.2 =

* Fixed issue with automatic page creator adding the incorrect short code.

= 1.6.2.1 =

* Relaxed reliance on PHP5 by removing function reference parameters with default values. The plugin should now work with PHP4.
* Updated jquery library to version 1.4.2.
* Fixed root node identification, allowing users to style the root item with CSS.
* Fixed bug that caused the expand icon to show for static pages that had no children.

= 1.6.2 =

* Added CSS classes to page, anchor link, category and post titles and all root items allowing users to be easily override formatting.
* The Summarizer can now use the anchor title for link summaries.
* If the summary text is longer than the allowable character limit, the summarizer now adds ellipses to the end of the last word (it no longer truncates in the middle of a word). This results in a neater looking summary.
* It is now possible to override any TOC option using parameters attached to the trigger tag. This allows multiple TOC lists to be displayed with different options (eg excluded blog cats, different icon sets etc). 
* Now dynamically removes open `<p>` tags before inserting the table of contents to ensure the TOCC plugin remain XHTML compliant.
* Added option to allow sort by author (dynamically or by default).
* Individual menu pull-down options can now be disabled by deleting the text after the comma within the Option Menu Item Texts setting.
* Implemented hierarchial page and blog tree walking using a recursive algorithm for greater efficiency and reliability.

= 1.6.1 =

* If no author URL is configured in the WP user section, the author link will default to the author post summary page.
* Add Spanish (castilian) translation by [Fernando J. Echevarrieta (echeva)](http://3d.echeva.com/ "Fernando J. Echevarrieta (echeva)").
* Use print_r method for linkTitle or linkPerm to convert an object into a string

= 1.6.0 =

* Hierarchial blog categories are now supported. An option has been included to display categories as a flat list as per previous versions.
* New feature to dynamically display or hide child elements similar to the way Windows Explorer works.
* New options menu allowing a visitor to hide/expand child elements, show/hide all summaries and sort the table of contents in several different orders.
* Added ability to display page author and/or page date for static pages.
* Updated Italian translation to include Summarizer options thanks to [Carlo Politi](http://www.carlopoliti.net/ "Carlo Politi").
* Stripped Worpress caption tags and plugin square bracket activation tags from page and post summaries. This was causing Summarizer display porblems with caption tags and the contact form plugin.
* Moved all tocc options into a single associative array resulting in smaller and easier to follow code with less calls to the option table.
* The php code has now been fully commented.
* The code used to generate the TOC HTML code has been completely rewritten. The resulting HTML is now a lot neater and no longer uses div elements. All list item types are now fully customizable using CSS. The lists are also now truely hierarchial.
* Added a new classes to items with children, the active page item, date text, author text, expandable items and option menu items allowing greater flexibility when custom styling with css.
* CSS file has been completely rewritten so that it is easier to override and customize.
* Many function parameters are now passed by reference thus reducing overall memory requirements and CPU usage.
* All formatting now moved from source code to the css file.
* Added nonce and admin check to all TOCC administration panel settings changes (for security purposes).

= 1.5.1 =

* Removed reliance on PHP5. Four lines of code accessed objects in a way that was not compatible with PHP4.
* Added space between Summarizer option span elements to fix rendering issue with some browsers.
* Added mozilla inline block replacement to fix rendering issues in Firefox 2.0 and lower.
* Replaced text Summarizer icons with mostly transparent icons to best suit more background colors.

= 1.5 =

* Brand new feature added - The Summarizer. This new feature allow you to display post or page summaries under each table of content item. The summaries are automatically generated using All in One SEO tags, post exceprts, body content or META tags. Summaries are displayed or hidden using fancy jQuery scroll effects. The Summarizer is also uber configurable. Readers may hide or display summaries using themed icons. This feature suggested and championed by [Scott](http://ontologicalwar.com/ "Scott").
* Fixed a small issue that stopped table of contents from correctly validating as XHTML.
* Added an "Are you sure?" warning when the restore defautls button is selected.
* Dynamically add toc.css and java script headers to pages with table of contents tag only (instead of all pages).
* TOCC pages generated with the create page option are now add to the end of an ordered page list (position 9999). This ensures that the page is displayed last in most theme menus. Comments are also now closed by default.

= 1.4.4.1 =

* Modified css file to remove CSS2 style bullets when displaying icons.

= 1.4.4 =

* Fixed bug in tocc_plugin_options() function that stopped the admin page loading on some systems. Thank you very much [Michael E. Chancey Jr.](http://michael.chanceyjr.com "Michael E. Chancey Jr.") for pointing out the bug and offering a fix.
* Added "Settings" link to plugin menu.

= 1.4.3 =

* Fixed fault causing author acknowledgment to dispay at full text height.
* Optimized use of Wordpress actions. The plugin now loads the localization file only when needed and all global code has been removed (except for action hooks).
* Resolved issue where default options would periodically not translate on new plugin install.
* Added special debug mode (activated by typing DEBUG_ON in TOC Title) to output all option settings as a hidden comment on the TOC page.
* Updated admin settings page to appear more "Wordpress" like.

= 1.4.2.1 =

* Fixed bug where options default to "blank" on initial install.

= 1.4.2 =
 
* Post anchor links now link to post permalink instead of the post number.
* Added Italian language file by [Carlo Politi](http://www.carlopoliti.net/ "Carlo Politi").
* Added Professional icon set (recommended by Bryan Brazil).
* Changed IDs to CLASSES to ensure XHTML 1.0 Strict (thanks Bryan).
* Added an uninstall script to remove all options when the plugin is deleted.

= 1.4.1 =

* Removed translational strings from add_option_page function as I suspect this may cause a race condition on initial activation (ie after an upgrade) that may every now and again causes a momentary server failure message. The message goes away after refreshing the page.
* Added CSS2 bullet clearing when displaying icons.

= 1.4 =

* Added option to select one of five different icon sets. Icons sets now include none, handdrawn (original), blue, square and bling. None selected by default, except when upgrading and show icons checkbox was selected. In this case, handdrawn is automatically selected to ensure that existing site maps retain the current look.
* The settings page now utilises collapsible menues as the number of options was becoming too large and overwhelming to display all at once.
* Added option (off by default) to show all entries as list items if not displaying icons. Currently only blog posts and forum comments are displayed as list items. Enabling this option allows easier integration into some themes.
* Fixed display issues with IE6. Found bug in IE6 render engine that stopped the icons from being displayed if the list item consists entirely of an anchor tag. Added a space to the end of all list items fixes this bug. Thank's Microsoft, can I have mny day back now?
* Added option to display pages, posts and forum comments as separate lists each with individual headings. Heading texts can also be set.
* Added option to display a "more" prompt at the end of the blog post and forum comments if not all of the posts or comments are shown. When clicked, the link will display a page of all posts or forum comments.

= 1.3.2 =

* Modified internal English strings be i18n Wordpress translation compatible.
* Added translation "please help" message to the settings page.

= 1.3.1 =

* Post count now pulled from database as $cat->count does not match the number of posts pulled from the database in some (rare) cases.
* Modified post retrieval query slightly as it sometimes got confused with posts that have multiple categories.

= 1.3 =

* get_posts() method used to retrieve wordpress blog posts sometimes return incorrect (empty) results. I have therefore implemented a new method to directly retrieve posts from the database. Retrieval falls back to get_post if no posts were found using the database method.
* Removed bullets from forum comments and blog posts when displaying icons.
* Hardcoded title formatting removed and added by default to the title. A blank title tag is now also allowed.
* Additional desscriptions added to settings page to guide users on the use of the `create page` and `update options` buttons.
* Added options to show blog and forum last update date and author.
* Added date format specifier for blog and forum last update dates.
* Added forum and blog author prefix text setting (for blogs using a language other than English)
* Added forum and blog warning text setting (for blogs using a language other than English)
* Added optional formatting to CSS file (such as removing bullets, adding anchor decoration etc). Instructions included in the CSS file.
* Added ability to determine previous version during upgrade. This will allow future versions to tweak existing settings to match the look and functionality of previous versions.

= 1.2.1 =

* Renamed function display_posts to tocc_display_posts and moved out of hook function as it conflicted with a function of the same name when display category posts. This update is a must for anyone using version 1.1 or 1.2 of this plugin!

= 1.2 =

* Modified default settings to disable icons and show all blog entries (looks more professional).
* Now shows blog entries as list items if icons are not selected. This provides better intergration with existing site themes and looks better when icons are not displayed.
* Now uses configurable blog title if not displaying static pages. The blog title may be left blank.
* Removed excessive use of nested &ltdiv&gt tag. This may have caused an issue with oulder browsers.
* Now ensure blog posts are sorted by date rather than rely on Wordpress defaults (as this may change in later Wordpress versions).
* Split the show posts/comments warning option into individual forum and blog sections.
* New option (on by default) to show total number of posts in each category.

= 1.1 =

* Posts will now be displayed if there is no static post page. Prior to this release, a static blog page was required.
* Now shows blog entries if blog page or static pages are excluded from the table of contents.
* Now show forum comments if forum page or static pages are excluded from the table of contents.
* Added option to remove author acknowledgment.
* Now displays a message indicating the last number of posts if the number of posts exceeds the selectable maximum number of displayed posts.
* Now displays a message indicating the latest number of forum comments.
* Fixed incorrect `<div>` nesting when static pages are excluded. 
* Fixed bug where anchor links would be displayed even though static pages were disabled.
* Fixed bug where forum page would always default back to the first page in the settings editor.
* Now allow extended help to be displayed if the option is disabled. This my be help the user to determine why the option is disabled in the first place.

= 1.0 =

* Initial public release.

== Wish List ==

* Add option to only show sub pages of the current page within the TOC. This will be handy for using the sidebar widget as a dynamic menu.
* Add option for maximum number of links per page (split list over multiple pages) - toc pagination.
* Allow sort by user defined tag.
* Allow text instead of icons for summary (eg `show` or `hide`) if no icon summary selected.
* Integrate FAQs and preprocessor help with the Wordpress Help system (the help pulldown on the settings page).


