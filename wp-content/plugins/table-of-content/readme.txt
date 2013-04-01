=== Table of Content ===
Contributors: Ulrich Kautz
Tags: toc, index, pageindex, siteindex, sitemap, table of contents
Requires at least: 2.7
Tested up to: 3.1.0
Stable tag: 0.6.1

A Table of Content (TOC) generator

== Description ==

The Plugin generates a TOC for a page or an article or just a part of either. The TOC is a Multi-Level List with links to "anchors" on the page. Therefore it parses the given page (or the part of page you want it to parse) and looks for headlines (h1, h2, h3, ...) in it. From the found it buils the TOC. It also upgrades your page contents with a top-navigation after each found headline ..

The most likely usecase for this Plugin is a huge page or article which you dont want to put into multiple pages.

== Installation ==

Either:
* Download .zip file and extract into your "wp-content/plugins" directory.
or 
* Use the Wordpress installer to install the plugin

Then
* Activate the plugin through the 'Plugins' menu in WordPress
* Put in your Non-Visuale editor a `[table-of-content]` before the text and `[/table-of-content]` after the text. All Text in between will be used to generate the TOC.
* You can set an optional title above your TOC via parameter: `[table-of-content title="This is the TOC"]`


= CAUTION =

Dont use a structure like h2, h1, h3, h1 .. it doesnt have to be ordered, but at least decrementing! 

* OK: h1, h2, h3, h1, h2, h2, h1, h1
* NOT OK: h2, h4, h1

== Frequently Asked Questions ==

= Can i disable the "^" markers in the headlines ? =

Yes, via stylesheet, like so:

    .pni-content .toplink { display: none ! important }

= Can i change the list number style ? =

Yes, also via stylesheet. For using decimals in all instances (level1, level2, ..):

    .pni-navigation ol li { list-style-type: decimal ! important }

Or to use discs (non-numeric), change the style like so:

    .pni-navigation ol li { list-style-type: disc ! important }

See http://www.w3schools.com/css/pr_list-style-type.asp for more info

= Can i use this plugin in with add_filter ? =

Yes. The following example shows how you can add the TOC as a filter to the_content, which will auto-use the TOC in any of your posts. Therefore add the following at the end of the `functions.php` file of your theme:

    if ( function_exists( 'toc_filter' ) && ! function_exists( 'theme_toc_filter' ) ) {
        function theme_toc_filter( $content = "" ) {
            return toc_filter( $content );
        }
        add_filter( 'the_content', 'theme_toc_filter' );
    }

If you use other content modifying plugins and your short-codes get messed up, try to adjust the priority higher (eg: `add_filter( 'the_content', 'theme_toc_filter', 999 )` or even higher).

If you need to disable the TOC in a single page of your theme, you can define the 'DISABLE_TOC' constant with a true value:

    define( 'DISABLE_TOC', true );

== Upgrade Notice ==

= 0.6.6 =

* You can now use the add_filter method with this plugin. Also a type in a stylesheet class has been fixed (pni-navigtion -> pni-navigation).

== Changelog ==

= 0.6.6, 2011-03-06 =

* Added filter support
* Upgraded readme.txt according to wordpress codex guidelines

= 0.6.5, 2010-10-28 =

* Using site_url() instead of get_option( 'siteurl' ) which does not respect https settings.

= 0.6.4, 2010-10-26 =

* Fix wrong method name

= 0.6.3, 2010-10-25 =

* Silly me forgot to add the admin.php file ..

= 0.6.2, 2010-10-23 =

* Removed typo..

= 0.6.1, 2010-10-23 =

* Adminmenu for setting title (above TOC) and wheter use ul or ol lists

= 0.6.0, 2010-10-22 =

* Added "title" option to shortcode

= 0.5.2, 2010-07-04 =

* Fix for strange PHP unicode + regex problem (maybe for PHP versions compiled without unicode support, if that's possible) thanks to WJM

= 0.5.1, 2010-07-04 =

* Fix for missing closing li tag, when decreasing the level of indention (http://wordpress.pastebin.com/tJrb2g38)

= 0.5, 2010-06-21 =

* Named anchors are now persistent (can be linked from outside)

= 0.4.2, 2010-05-17 =

* Fix for unclosed tokens (http://wordpress.org/support/topic/399878?replies=1)

= 0.4, 2009-09-17 =

* Ooops, rollback changes from 0.3 .. 
