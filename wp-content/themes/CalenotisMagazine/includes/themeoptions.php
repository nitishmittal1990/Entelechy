<?php
$themename = "SWT Theme";
$shortname = "swt";
$himg_dir = get_bloginfo('template_directory');
$sfirst_img = $himg_dir . '/images/howtoslide.png';
$mx_categories_obj = get_categories('hide_empty=0');
$mx_categories = array();
foreach ($mx_categories_obj as $mx_cat) {
	$mx_categories[$mx_cat->cat_ID] = $mx_cat->cat_name;
}
$categories_tmp = array_unshift($mx_categories, "Select a category:","Uncategorized" );
$number_entries = array("Select a Number:","1","2","3","4","5","6","7","8","9","10", "12","14", "16", "18", "20" );
$options = array (


array( "name" => $themename." Options",
       "type" => "title"),

    array( "name" => "Slider Settings",
           "type" => "section"),
    array( "type" => "open"),

	array(  "name" => "Enable/Disable Content Slider",
			"desc" => "To have content slider working, you'll have to enable it first, then select sliding category and number of slides. Under post, thats under selected sliding category, enter new custom field 'slide' (without '' quotes), and then on the right side in the 'Value' leave the link to the image. Check <a href='$sfirst_img' target='_blank'>this image</a> to make sure you're doing good! Images should be 956x352px exactly! <b><u>PLEASE FOLLOW README IN THEME'S FOLDER!</u></b> Its all explained, how to set featured images, change the logo, and everything needed!",
            "id" => $shortname."_slider",
            "type" => "select",
            "std" => "Display",
            "options" => array("Display", "Hide")),

	array( 	"name" => "Sliding category",
			"desc" => "Select the category that will be displayed in the slider.",
			"id" => $shortname."_slide_category",
			"std" => "Uncategorized",
			"type" => "select",
			"options" => $mx_categories),

	array(	"name" => "Number of slides",
			"desc" => "Select the number of slides.",
			"id" => $shortname."_slide_count",
			"std" => "1",
			"type" => "select",
			"options" => $number_entries),


    array( "type" => "close"),

    array( "name" => "Latest Posts From Categories",
           "type" => "section"),
    array( "type" => "open"),


    array(  "name" => "Enable/Disable Latest Posts",
			"desc" => 'With this option, you can show your visitors newest articles from some category. You need to use post thumbnails (check the Readme) for images to show.',
            "id" => $shortname."_fcats",
            "type" => "select",
            "std" => "Display",
            "options" => array("Display", "Hide")),


 	array(	"name" => "Choose Which <strong>Category</strong> To Feature On 1st Block?",
			"id" => $shortname."_featured_category1",
            "type" => "select",
            "std" => "Select A Category",
			"options" => $mx_categories),

			array(	"name" => "Choose How Many <strong>Post</strong> To Display On 1st Block?",
			"id" => $shortname."_featured_number1",
            "type" => "select",
            "std" => "Select A Number",
			"options" => $number_entries),

    array( "type" => "close"),

	array(  "name" => "Banner Management",
           "type" => "section"),
    array( "type" => "open"),

   	array(  "name" => "Enable/Disable 125x125 banners",
            "id" => $shortname."_banners",
            "type" => "select",
            "std" => "Display",
            "options" => array("Display", "Hide")),

	array("name" => "Banner-1 Image",
			"desc" => "Enter your 125x125 banner image url here.",
            "id" => $shortname."_banner1calen",
            "std" => "Banner-1 image",
            "type" => "text"),

	array("name" => "Banner-1 Url",
			"desc" => "Enter the banner-1 url here.",
            "id" => $shortname."_link1",
            "std" => "http://www.simplewpthemes.com",
            "type" => "text"),

	array("name" => "Banner-2 Image",
			"desc" => "Enter your 125x125 banner image url here.",
            "id" => $shortname."_banner2calen",
            "std" => "Banner-2 image",
            "type" => "text"),

	array("name" => "Banner-2 Url",
			"desc" => "Enter the banner-2 url here.",
            "id" => $shortname."_link2",
            "std" => "http://www.sremskioglasi.com",
            "type" => "text"),

	array("name" => "Banner-3 Image",
			"desc" => "Enter your 125x125 banner image url here.",
            "id" => $shortname."_banner3calen",
            "std" => "Banner-3 image",
            "type" => "text"),

	array("name" => "Banner-3 Url",
			"desc" => "Enter the banner-3 url here.",
            "id" => $shortname."_link3",
            "std" => "http://www.lacarak.net/forum",
            "type" => "text"),

	array("name" => "Banner-4 Image",
			"desc" => "Enter your 125x125 banner image url here.",
            "id" => $shortname."_banner4calen",
            "std" => "Banner-4 image",
            "type" => "text"),

	array("name" => "Banner-4 Url",
			"desc" => "Enter the banner-4 url here.",
            "id" => $shortname."_link4",
            "std" => "http://www.lacarak.net",
            "type" => "text"),

    array( "type" => "close"),


array( "name" => "Additional Settings",
       "type" => "section"),
array( "type" => "open"),


 	array(  "name" => "Menu settings",
            "type" => "heading",
            "desc" => "Here you can exclude some of the links in the menus of theme.",
         ),

	array(  "name" => "Exclude pages",
			"desc" => "You can exclude some of the links in the pages menu (comma separated if more than one!).",
            "id" => $shortname."_pages",
            "std" => "",
            "type" => "text"),

	array(  "name" => "Miscellaneous",
            "type" => "heading",
            "desc" => "Here you can exclude some of the links in the menus of theme.",
         ),

	array(  "name" => "Enable/Disable social bookmark",
			"desc" => "With this option enabled, people can bookmark your site on their social profiles (like delico.us or similiar), and that's good for SEO :).",
            "id" => $shortname."_social",
            "type" => "select",
            "std" => "Display",
            "options" => array("Display", "Hide")),

 
       array("name" => "Custom Google Analytics Tracking Code",
            "desc" => "Enter your tracking code here for Google Analytics",
            "id" => $shortname."_custom_analytics_code",
            "type" => "textarea",
            "std" => "")
);

function mytheme_add_admin() {

global $themename, $shortname, $options;

if ( $_GET['page'] == basename(__FILE__) ) {

	if ( 'save' == $_REQUEST['action'] ) {

		foreach ($options as $value) {
		update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

foreach ($options as $value) {
	if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

	header("Location: admin.php?page=themeoptions.php&saved=true");
die;

}
else if( 'reset' == $_REQUEST['action'] ) {

	foreach ($options as $value) {
		delete_option( $value['id'] ); }

	header("Location: admin.php?page=themeoptions.php&reset=true");
die;

}
}

      add_theme_page($themename." Options", "$themename Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');
}

function mytheme_add_init() {

$file_dir=get_bloginfo('template_directory');
wp_enqueue_style("functions", $file_dir."/includes/admin/functions.css", false, "1.0", "all");
wp_enqueue_script("rm_script", $file_dir."/includes/admin/rm_script.js", false, "1.0");

}
function mytheme_admin() {

global $themename, $shortname, $options;
$i=0;

if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';

?>
<div class="wrap rm_wrap">
<h2><?php echo $themename; ?> Settings</h2>

<div class="rm_opts">
<form method="post">
<?php foreach ($options as $value) {
switch ( $value['type'] ) {

case "open":
?>

<?php break;

case "close":
?>

</div>
</div>
<br />


<?php break;

case "title":
?>
<p>To easily use the <?php echo $themename;?>, you can use the menus below. <b>Check the README.TXT for instructions!</b></p>


<?php break;

case 'text':
?>

<div class="rm_input rm_text">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" />
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>

 </div>
<?php
break;

case 'textarea':
?>

<div class="rm_input rm_textarea">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>

 </div>

<?php
break;

case 'select':
?>

<div class="rm_input rm_select">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
		<option <?php if (get_settings( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?>
</select>

	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
</div>
<?php
break;

case "checkbox":
?>

<div class="rm_input rm_checkbox">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />


	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 </div>
<?php break;
case "section":

$i++;

?>

<div class="rm_section">
<div class="rm_title"><h3><img src="<?php bloginfo('template_directory')?>/includes/admin/images/trans.gif" class="inactive" alt="""><?php echo $value['name']; ?></h3><span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="Save changes" />
</span><div class="clearfix"></div></div>
<div class="rm_options">


<?php break;

}
}
?>

<input type="hidden" name="action" value="save" />
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
 </div>


<?php
}
?>
<?php
add_action('admin_init', 'mytheme_add_init');
add_action('admin_menu', 'mytheme_add_admin');
?>