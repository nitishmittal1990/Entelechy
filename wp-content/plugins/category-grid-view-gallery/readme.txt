=== Category Grid View Gallery ===
Contributors: Anshul Sharma 
Donate link: http://www.evilgenius.anshulsharma.in/donate-me/
Tags: category,grid,portfolio,gallery,shortcode,lightbox,featured
Requires at least: 2.0.2
Tested up to: 3.4.2
Stable tag: trunk

Generates a grid view of the Images grabbed from posts of a category and displays the image and titles using a shortcode. Awesome for portfolios.

== Description ==

This plugin provides a new way to build your Portfolios and Photo Galleries. People who want to show their work using a gallery/portfolio dont have to exclusively install a plugin and upload images on it. Now, you can just upload your work as a blog post every now and then and this plugin will take care of the rest.A new way to display your posts,highly customizable. Some of its features include:

*   Uses the [cgview] shortcode. 

**New in version 2.3**

*   New and improved lightbox. Now you can open complete posts inside the lightbox.
*   Choose whether or not to show the comments with the post.

*   Admin Settings panel and a Shortcode Generator.
*   Pagination
*   Option of choosing Featured image/First Image from post as thumnail.
*   Select post based on Custom Field Key and/or Custom Field Key Value.
*   Upload and specify your own  default image for posts which do not have an image.
*   Light and Dark Themes.
*   Option to pick alternate title for thumbnail from custom fields instead of post titles.
*   Easily integrated into any template with a shortcode and a template tag.
*   Flexible. Through shortcode, user can control which category to show, the number of posts to show, the posts to exclude, the size of the thumbnail, whether or not to show the title, how to order them, quality of thumbnails, show posts only with specific tags and many more.
*   Show posts from more than one categories.
*   The thumbnails are generated on-the-fly and dynamically resized. It also has caching feature to reduce the load on the server.
*   Option to show the posts in a popup light box (Colorbox) on click. Posts loaded through AJAX.  
*   A very light weight jquery animated plugin.
*   If javascript is disabled/not loaded, the plugin falls back to a pure css animated gallery.
*   If no image is present a default image is shown.
*   Show anywhere in your Post or Page using a shortcode  
    
*   USAGE: Perfect for making a Photogallery or Portfolio with description written along with the image in a blog post. Also a unique way of displaying your FEATURED posts on your home page.

Check out the Demo <a href=\"http://test.anshulsharma.in\" target=\"_blank\" title=\"Demo CGView\">HERE</a> to see it in action.

For a full list of options and support, visit <a href=\"http://evilgenius.anshulsharma.in/cgview\" target=\"_blank\" title=\"Anshul Sharma\"> Authors website</a>

For suggestions/feedback and requesting new features <a href=\"http://www.evilgenius.anshulsharma.in/cgview\" target=\"_blank\">visit here</a>


Future Features planned :

*   Support for displaying pages.
*   Image Slider Gallery (Auto roll)
*   A widget for the sidebar  


== Installation ==

*   Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page. 
*   Optionally, directly install the plugin from the wordpress interface by using \"Add New\" option from the Plugin menu and then activate it.  
    
*   USAGE :* [cgview num=9 id=4 size=250x150]*

use the above shortcode to display the gallery anywhere in your content.

For a full list of options and support, visit <a href=\"http://www.evilgenius.anshulsharma.in/\" target=\"_blank\" title=\"Anshul Sharma\"> Authors website</a>
== Frequently Asked Questions ==

**Q. I cannot see the thumbnail images. Why is it so?**  
A. It may be because the cache folder used to store the generated thumbnails is not writable on your system.The Plugin uses the systems temporary directory for cache.
Also, if your post contains an image that is NOT hosted on the same server, then the thumbnail will not be generated. This is for security reasons. If your post contains an image from an external source, you can first download the image, and then host it on your server and replace it in the post.
You can also attach it as a featured image keeping your post unchanged and then choose "featured image" as the image source from Settings panel.

for more support visit the <a href=\"http://www.evilgenius.anshulsharma.in/cgview\" target=\"_blank\">plugin homepage</a>
== Screenshots ==
1. The shortcodes, how to use them.

2. The plugin in action

3. Post opened inside a lightbox

== Changelog ==
**2.3.1**

- Fixed minor bug : Image quality can now be changed.


**2.3**

- Fixed issue of shortcodes and other plugins not working inside lightbox
- Updated lightbox to display full fledged posts
- Fixed minor bugs

**2.2.4**

- Fixed issue with wordpress query not excluding posts.

**2.2.3**

- Re-Configured to use system cache folder.
- Updated Timthumb script.
- A major update to follow. Im sick of timthumb.

**2.2.1**

- Removed W3C validation errors.
- bug fixed with CGView query breaking posts loop


**2.2**

- Fixed jquery conflict with other lightbox plugins.


**2.0.0**

- Admin Panel and Shortcode generator added.
- Pagination
- Option of choosing Featured image/First Image from post as thumnail.
- Select post based on Custom Field Key and/or Custom Field Key Value.
- Upload and specify your own  default image for posts which do not have an image.
- Light and Dark Themes.
- Option to pick alternate title for thumbnail from custom fields instead of post titles.

**0.2.0**

- Timthumb security vulnerablility fixed With the latest version of TimThumb script.
 

**0.1.1**

- Links Updated

**0.1.0**

- Fixed the issue of colorbox opening too small when images in the content load slowly.

 

**Beta**