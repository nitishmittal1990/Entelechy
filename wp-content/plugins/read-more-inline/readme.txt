=== Read More Inline ===
Contributors: stephenlgray
Tags: readmore, read more, quicktags, inline
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: trunk

Changes 'read more' quicktags into toggles so users can see extra content without leaving the page.

== Description ==

Allows you to use the 'read more' quicktag to hide the content following it and instead show a 'read more' link. When the user clicks the link, they see the rest of 
the content right after the link, without leaving the page.
It replaces the default behaviour (linking to a full version of the page/post) and is a way of keeping the initial page content short, 
but allowing users to read more if they want to, without having to leave the page.
It's superlightweight, works on the fly using jQuery and can be activated and deactivated without making any changes or leaving any trace in your Wordpress installation.


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `read-more-inline` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the 'read more' quicktag to a page part-way through the content. 
(There's a button for this: should be in the top row above the editor and it looks like a page break; if you hover over it, it will say Insert More Tag and if you click it, it will insert the required shortcode; or, insert `<!--more-->` in HTML view).

The content after the quicktag will be hidden initially, but the 'read more' link will toggle its visibility.
4. Add this to your page template: `<?php global $more; $more = FALSE; ?>` after `the_post()` but before `the_content()` in The Loop. See http://codex.wordpress.org/Customizing_the_Read_More#How_to_use_Read_More_in_Pages for more details. 


== Frequently Asked Questions ==

= Can I use this on posts? =

It's only for pages so shouldn't be used if you rely on 'read more' links to link to the full post when you're outputting excerpts, say on a homepage list of recent posts.
You'll need to add this line to your page template as well: `<?php global $more; $more = FALSE; ?>`
See http://codex.wordpress.org/Customizing_the_Read_More#How_to_use_Read_More_in_Pages for where and why.

= Can I customise the wording of the link text? =

Yes, if you modify the_content function in your templates e.g. `<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>`

== Screenshots ==


== Changelog ==
= 0.2 =
* Amended installation instructions to include template code changes necessary.
* Changed code to find original more link, instead of the span that `the_content()` replaces it with.
