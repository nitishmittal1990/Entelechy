=== Metronet Profile Picture ===
Contributors: metronet, ronalfy
Tags: users, user, user profile
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 1.0.18
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Set a custom profile image for a user using the standard WordPress media upload tool.
== Description ==

Set a custom profile image for a user using the standard WordPress media upload tool.  

A template tag is supplied for outputting to a theme and the option to override a user's default avatar is also available.

This plugin is fully compatible with <a href="http://wordpress.org/extend/plugins/post-thumbnail-editor/">Post Thumbnail Editor</a> for cropping any uploaded images.

If you like this plugin, please leave a rating/review and mark the plugin as working.

== Installation ==

1. Upload `metronet-profile-picture` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php mt_profile_img() ?>` in your templates (arguments and usage are below)
4. Use the "Override Avatar" function to change your default avatar.

Arguments: 

`/**
* mt_profile_img
* 
* Adds a profile image
*
@param $user_id INT - The user ID for the user to retrieve the image for
@ param $args mixed
	size - string || array (see get_the_post_thumbnail)
	attr - string || array (see get_the_post_thumbnail)
	echo - bool (true or false) - whether to echo the image or return it
*/
`

Example Usage:
`
<?php
//Assuming $post is in scope
if (function_exists ( 'mt_profile_img' ) ) {
	$author_id=$post->post_author;
	mt_profile_img( $author_id, array(
		'size' => 'thumbnail',
		'attr' => array( 'alt' => 'Alternative Text' ),
		'echo' => true )
	);
}
?>
`
View the code on <a href="http://pastebin.com/Xaf8dJqQ">Pastebin</a>.

The `mt_profile_img` function internally uses the <a href="http://codex.wordpress.org/Function_Reference/get_the_post_thumbnail">get_the_post_thumbnail</a> function to retrieve the profile image.

Optionally, if you choose the "Override Avatar" function, you can use <a href="http://codex.wordpress.org/Function_Reference/get_avatar">get_avatar</a> to retrieve the profile image.

== Frequently Asked Questions ==

= How do you set a user profile image? =

1.  Visit the profile page you would like to edit.
2.  Click "Upload or Change Profile Picture"
3.  Upload a new image and select "Set profile image", which will save the image.

To override an avatar, select the "Override Avatar?" checkbox and save the profile page.

= What role does a user have to be to set a profile image? =

Author or greater.

= How do I create specific thumbnail sizes? =

Since the plugin uses the native uploader, you'll have to make use of <a href='http://codex.wordpress.org/Function_Reference/add_image_size'>add_image_size</a> in your theme.  You can then call `mt_profile_img` and pass in the custom image size.

= The image is cropped wrong.  How do I fix this? = 

We highly recommend the <a href='http://wordpress.org/extend/plugins/post-thumbnail-editor/'>Post Thumbnail Editor</a> plugin for cropping thumbnails, as you can custom-crop various image sizes without affecting other images.

= Does the plugin work with Multisite? =

Yes, but you'll have to set a new profile image per site.  This is currently a limitation of the way the plugin stores its data.  Ideas to overcome this are welcome.

== Screenshots ==

1. Profile page options.
2. Media upload dialog.
3. Post Thumbnail Editor compatibility.

== Changelog ==
= 1.0.18 = 
* Added basic multisite support

= 1.0.16 =
* Fixed a bug where only the profile image interface was showing for only authors and not editors and administrators. 

= 1.0.15 =
* Built-in support for <a href="http://wordpress.org/extend/plugins/post-thumbnail-editor/">Post Thumbnail Editor</a>
* Better integration with the new WP 3.5 media uploader
* Various bug fixes.

= 1.0.10 = 
* Usability enhancements.
* Stripping out useless code.
* Updating documentation

= 1.0.9 = 
* Adding support for the new 3.5 media uploader.

= 1.0.3 = 
* Bug fix:  Avatar classes in the comment section

= 1.0.2 =
* Bug fix:  Error being shown in comment section

= 1.0.1 =
* Bug fix:  Not able to "uncheck" Override Avatar.
* Bug fix:  Deleting profile image and not reverting to normal avatar.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==
= 1.0.18 =
Added basic multisite support

= 1.0.16 = 
Fixed a bug where only the profile image interface was showing for only authors and not editors and administrators. 

= 1.0.15 =
Built-in support for Post Thumbnail Editor.  Better integration with the new WP 3.5 media uploader. Various bug fixes.

= 1.0.10 =
3.5 media uploader support.  Usability enhancements. Code cleanup.

= 1.0.9 = 
3.5 media uploader support.

= 1.0.1 =
Several important bug fixes including the ability to uncheck the avatar override, and the behavior when someone deletes their profile picture.

= 1.0.0 =
Initial release.