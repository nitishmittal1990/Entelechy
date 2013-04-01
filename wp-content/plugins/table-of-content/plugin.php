<?php
/**
 * @package Table of Content
 * @author Ulrich Kautz
 * @version 0.6.6
 */
/*
Plugin Name: Table of Content
Plugin URI: http://blog.foaa.de/plugins/table-of-content
Description: The Plugin generates a TOC for a page or an article or just a part of either. The TOC is a Multi-Level List with links to "anchors" on the page. Therefore it parses the given page (or the part of page you want it to parse) and looks for headlines (h1, h2, h3, ...) in it. From the found it buils the TOC. It also upgrades your page contents with a top-navigation after each found headline .. [table-of-content] text text text [/table-of-content]
Version: 0.6.5
Author URI: http://fortrabbit.de
Thanks to: Jeffrey for the shortcode-patch
*/
include_once( 'includes.php' );

// announce shorttag
add_shortcode( 'table-of-content', 'toc_wrapper' );

// announce the menu item for admin..
add_action( 'admin_menu', 'toc_init_admin_menu' );


$TableOfContent = new TableOfContent();
$toc_top_counter = 0;
$toc_has_been_applied = false;
function toc_wrapper( $args, $content = "" ) {
	return _toc_apply( $args, $content );
}


function toc_filter( $content = "" ) {
	if ( defined( 'DISABLE_TOC' ) && DISABLE_TOC === true )
		return $content;
	return _toc_apply( array(), $content );
}

function _toc_apply( $args, $content ) {
	global $TableOfContent, $toc_top_counter, $toc_has_been_applied;
	
	if ( $toc_has_been_applied === true )
		return $content;
	$toc_has_been_applied = true;
	
	// return nada if no content provided
	//	Monday, May 17 2010
	if ( empty( $content ) )
		return "";
	
	// read options
	$options = toc_get_options();
	
	// generate the parsed content
	$res = $TableOfContent->parse_contents( $content, array(
		'top_suffix'	=> $toc_top_counter,
		'top_prefix'	=> $toc_top_counter,
		'list_type'		=> $options[ 'toc_list_style' ]
	) );
	
	// parse/set default args
	if ( $args == null )
		$args = array();
	$args[ 'title' ] = $options[ 'toc_title' ];
	$args[ 'title-tag' ] = $options[ 'toc_title_tag' ];
	if ( @empty( $args[ 'title-tag' ] ) )
		$args[ 'title-tag' ] = 'h5';
	
	// having title ?
	$title = '';
	if ( ! @empty( $args[ 'title' ] ) ) {
		
		// switch tag ?
		$tag = $args[ 'title-tag' ];
		
		// build title
		$title = '<'. $tag. ' class="pni-title">'.
			htmlentities( $args[ 'title' ] ).
			'</'. $tag. '>'
		;
	}
	
	// finalize the "new" content
	$parsed = '<div class="pni-navigation pni-navigtion"><a name="pni-top'. $toc_top_counter. '"></a>'. $title. $res->navigation. '</div><div class="pni-content">'. $res->content. '</div>';
	
	// increment the top suffix for next usage
	$toc_top_counter++;
	
	// return parsed ..
	return $parsed;
}

function toc_init_admin_menu() {
	$path = WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__).'/');
	add_options_page( 'Table of content', 'T.O.C.', 'manage_options', $path. '/admin.php', '' );
}

?>
