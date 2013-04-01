<?php
/*
Plugin Name: Ninja Pages, Categories, and Tags
Plugin URI: http://plugins.wpninjas.net/?p=72
Description: A simple plugin that allows the user to assign categories and tags to pages.
Version: 1.3.2
Author: The WP Ninjas
Author URI: http://www.wpninjas.net
*/

/**
 * Define constants
 **/
define('NINJA_PAGES_DIR', WP_PLUGIN_DIR.'/ninja-page-categories-and-tags/');
define('NINJA_PAGES_URL', WP_PLUGIN_URL.'/ninja-page-categories-and-tags/');

/**
 * Include core files
 **/
require_once( NINJA_PAGES_DIR . 'basic-functions.php' );

function ninja_pages_load_lang() {
	$lang_dir = NINJA_PAGES_DIR . 'lang/';
	load_plugin_textdomain( 'ninja-pages', false, $lang_dir );
}

if ( is_admin() ) {
	add_action('admin_menu', 'ninja_pages_create_settings_menu');
	add_action('admin_init', 'ninja_pages_add_cats_box');
	add_action('admin_init', 'ninja_pages_add_tags_box');
	add_action('admin_init', 'ninja_pages_add_excerpt_box');
	// add the admin settings and such
	add_action('admin_init', 'ninja_pages_admin_init');
} else {
	add_action('init', 'ninja_pages_init');
	$options = get_option('ninja_pages_options');
	if( isset( $options['display_children'] ) ) :
		add_filter( 'the_content', 'ninjapages_child_pages' );
	endif;
}

function ninja_pages_admin_init(){
	include( NINJA_PAGES_DIR . 'basic-settings.php' );
	wp_register_style( 'ninja_pages_admin_css', NINJA_PAGES_URL . 'css/ninja_pages_admin.css' );
	if ( !current_theme_supports( 'post-thumbnails' ) ) {
		//add_theme_support( 'post-thumbnails' );
		//echo 'test';
		//die();
	}
}

function ninja_pages_init(){
	if ( !current_theme_supports( 'post-thumbnails' ) ) {
		//add_theme_support( 'post-thumbnails' );
		//echo 'test';
		//die();
	}
	include( NINJA_PAGES_DIR . 'shortcodes.php' );
	add_action('init', 'ninja_pages_load_lang');
	add_action('after_setup_theme', 'ninja_pages_post_thumbnails');
}

function ninja_pages_create_settings_menu() {
	$page = add_options_page(
		'Ninja Pages',
		'Ninja Pages',
		'manage_options',
		'ninja-pages',
		'ninja_pages_plugin_options'
	);
	add_action( 'admin_print_styles-' . $page, 'ninja_pages_admin_styles' );
}

function ninja_pages_admin_styles() {
       wp_enqueue_style( 'ninja_pages_admin_css' );
   }

function ninja_pages_plugin_options() {

	global $wpdb;

	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e( 'Ninja Pages, Categories, and Tags', 'ninja_pages' ); ?></h2>
		<div class="wrap-left">
		<form method="post" action="options.php">
			<?php settings_fields( 'ninja_pages_options' ); ?>
			<?php do_settings_sections( 'ninja_pages_options' ); ?>
			<br />
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Options', 'ninja-pages' ); ?>" />
		</form>

		<?php //do_settings_sections( 'ninja_pages_details' ); ?>
		</div>
		<div class="wrap-right">
			<img src="<?php echo NINJA_PAGES_URL;?>images/wpnj-logo-wt.png" width="263px" height="45px" />
			<h2>Check out other great plugins from the WP Ninjas...</h2>
			<ul>
				<li><a href="http://wpninjas.net/?p=562" target="_blank">Ninja Forms</a></li>
				<li><a href="http://wpninjas.net/?p=1131" target="_blank">Ninja Announcements</a></li>
			</ul>
		</div>
	</div>


<?php
}
add_action('after_setup_theme', 'ninja_pages_post_thumbnails');
function ninja_pages_post_thumbnails() {
	if ( !current_theme_supports( 'post-thumbnails' ) ) {
		add_theme_support( 'post-thumbnails' );
	}
}