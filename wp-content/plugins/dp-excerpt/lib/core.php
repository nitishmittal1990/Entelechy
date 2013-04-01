<?php
/**
 * The core functions file for the Remix framework. Functions defined here are generally
 * used across the entire framework to make various tasks faster. This file should be loaded
 * prior to any other files because its functions are needed to run the framework.
 *
 * @package DP Core
 * @subpackage Functions
 */

/**
 * Defines the theme prefix. This allows developers to infinitely change the theme. In theory,
 * one could use the Remix core to create their own theme or filter 'dp_prefix' with a 
 * plugin to make it easier to use hooks across multiple themes without having to figure out
 * each theme's hooks (assuming other themes used the same system).
 *
 * @since 0.7.0
 * @uses get_template() Defines the theme prefix, which is generally 'dp'.
 * @global object $dp The global Remix object.
 * @return string $dp->prefix The prefix of the theme.
 */
function dp_get_prefix() {
	global $dp;

	/* If the global prefix isn't set, define it. Plugin/theme authors may also define a custom prefix. */
	if ( empty( $dp->prefix ) )
		$dp->prefix = apply_filters( 'dp_prefix', get_template() );

	return $dp->prefix;
}

/**
 * Defines the theme textdomain. This allows the framework to recognize the proper textdomain 
 * of the theme. Theme developers building from the framework should use their template name 
 * (i.e., directory name) as their textdomain within template files.
 *
 * @since 0.7.0
 * @uses get_template() Defines the theme textdomain, which is generally 'dp'.
 * @global object $dp The global Remix object.
 * @return string $dp->textdomain The textdomain of the theme.
 */
function dp_get_textdomain() {
	global $dp;

	/* If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain. */
	if ( empty( $dp->textdomain ) )
		$dp->textdomain = apply_filters( dp_get_prefix() . '_textdomain', get_template() );

	return $dp->textdomain;
}

/**
 * Filters the 'load_textdomain_mofile' filter hook so that we can change the directory and file name 
 * of the mofile for translations.  This allows child themes to have a folder called /languages with translations
 * of their parent theme so that the translations aren't lost on a parent theme upgrade.
 *
 * @since 0.9.0
 * @param string $mofile File name of the .mo file.
 * @param string $domain The textdomain currently being filtered.
 */
function dp_load_textdomain( $mofile, $domain ) {

	/* If the $domain is for the parent theme, search for a $domain-$locale.mo file. */
	if ( $domain == dp_get_textdomain() ) {

		/* Check for a $domain-$locale.mo file in the parent and child theme root and /languages folder. */
		$locale = get_locale();
		$locate_mofile = locate_template( array( "languages/{$domain}-{$locale}.mo", "{$domain}-{$locale}.mo" ) );

		/* If a mofile was found based on the given format, set $mofile to that file name. */
		if ( !empty( $locate_mofile ) )
			$mofile = $locate_mofile;
	}

	/* Return the $mofile string. */
	return $mofile;
}

/**
 * Adds contextual action hooks to the theme.  This allows users to easily add context-based content 
 * without having to know how to use WordPress conditional tags.  The theme handles the logic.
 *
 * An example of a basic hook would be 'dp_header'.  The do_atomic() function extends that to 
 * give extra hooks such as 'dp_singular_header', 'dp_singular-post_header', and 
 * 'dp_singular-post-ID_header'.
 *
 * Major props to Ptah Dunbar for the do_atomic() function.
 * @link http://ptahdunbar.com/wordpress/smarter-hooks-context-sensitive-hooks
 *
 * @since 0.7.0
 * @uses dp_get_prefix() Gets the theme prefix.
 * @uses dp_get_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $arg,... Optional additional arguments which are passed on to the functions hooked to the action.
 */
function do_atomic( $tag = '', $arg = '' ) {
	if ( empty( $tag ) )
		return false;

	/* Get the theme prefix. */
	$pre = dp_get_prefix();

	/* Get the args passed into the function and remove $tag. */
	$args = func_get_args();
	array_splice( $args, 0, 1 );
	
	/* Do actions on the basic hook. */
	do_action_ref_array( "{$pre}_{$tag}", $args );

	/* Loop through context array and fire actions on a contextual scale. */
	foreach ( (array)dp_get_context() as $context )
		do_action_ref_array( "{$pre}_{$context}_{$tag}", $args );
}

/**
 * Adds contextual filter hooks to the theme.  This allows users to easily filter context-based content 
 * without having to know how to use WordPress conditional tags.  The theme handles the logic.
 *
 * An example of a basic hook would be 'dp_entry_meta'.  The apply_atomic() function extends 
 * that to give extra hooks such as 'dp_singular_entry_meta', 'dp_singular-post_entry_meta', 
 * and 'dp_singular-post-ID_entry_meta'.
 *
 * @since 0.7.0
 * @uses dp_get_prefix() Gets the theme prefix.
 * @uses dp_get_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $value The value on which the filters hooked to $tag are applied on.
 * @param mixed $var,... Additional variables passed to the functions hooked to $tag.
 * @return mixed $value The value after it has been filtered.
 */
function apply_atomic( $tag = '', $value = '' ) {
	if ( empty( $tag ) )
		return false;

	/* Get theme prefix. */
	$pre = dp_get_prefix();

	/* Get the args passed into the function and remove $tag. */
	$args = func_get_args();
	array_splice( $args, 0, 1 );

	/* Apply filters on the basic hook. */
	$value = $args[0] = apply_filters_ref_array( "{$pre}_{$tag}", $args );

	/* Loop through context array and apply filters on a contextual scale. */
	foreach ( (array)dp_get_context() as $context )
		$value = $args[0] = apply_filters_ref_array( "{$pre}_{$context}_{$tag}", $args );

	/* Return the final value once all filters have been applied. */
	return $value;
}

/**
 * Wraps the output of apply_atomic() in a call to do_shortcode(). This allows developers to use 
 * context-aware functionality alongside shortcodes. Rather than adding a lot of code to the 
 * function itself, developers can create individual functions to handle shortcodes.
 *
 * @since 0.7.0
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $value The value to be filtered.
 * @return mixed $value The value after it has been filtered.
 */
function apply_atomic_shortcode( $tag = '', $value = '' ) {
	return do_shortcode( apply_atomic( $tag, $value ) );
}

/**
 * Loads the Remix theme settings once and allows the input of the specific field the user would 
 * like to show.  Remix theme settings are added with 'autoload' set to 'yes', so the settings are 
 * only loaded once on each page load.
 *
 * @since 0.7.0
 * @uses get_option() Gets an option from the database.
 * @uses dp_get_prefix() Gets the prefix of the theme.
 * @global object $dp The global Remix object.
 * @param string $option The specific theme setting the user wants.
 * @return string|int|array $settings[$option] Specific setting asked for.
 */
function dp_get_setting( $option = '' ) {
	global $dp;

	if ( !$option )
		return false;

	if ( !isset( $dp->settings ) )
		$dp->settings = get_option( dp_get_prefix() . '_theme_settings' );

	if ( !is_array( $dp->settings ) || empty( $dp->settings[$option] ) )
		return false;

	if ( is_array( $dp->settings[$option] ) )
		return $dp->settings[$option];
	else
		return wp_kses_stripslashes( $dp->settings[$option] );
}

/**
 * Function for formatting a hook name if needed. It automatically adds the theme's prefix to 
 * the hook, and it will add a context (or any variable) if it's given.
 *
 * @since 0.7.0
 * @param string $tag The basic name of the hook (e.g., 'before_header').
 * @param string $context A specific context/value to be added to the hook.
 */
function dp_format_hook( $tag, $context = '' ) {
	return dp_get_prefix() . ( ( !empty( $context ) ) ? "_{$context}" : "" ). "_{$tag}";
}

function dp_theme_mode() {
	return 'guide';
}

?>