<?php
/*
Load scripts for sedlex plugins
*/ 

error_reporting(0);

define( 'WP_PLUGIN_DIR', dirname(dirname(__FILE__)) . '/../' );
define( 'WP_CONTENT_DIR', dirname(dirname(__FILE__)) . '/../../' );

$load = preg_replace( '/[^A-Za-z0-9]+/i', '', $_GET['load'] );

if ( empty($load) )
	exit;
if (!is_file(WP_CONTENT_DIR."/sedlex/inline_scripts/".$load.".js"))
	exit ; 

$compress = ( isset($_GET['c']) && $_GET['c'] );
$force_gzip = ( $compress && 'gzip' == $_GET['c'] );

$expires_offset = 31536000;

$out = @file_get_contents(WP_CONTENT_DIR."/sedlex/inline_scripts/".$load.".js") ; 

header('Content-Type: application/x-javascript; charset=UTF-8');
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");

if ( $compress && ! ini_get('zlib.output_compression') && 'ob_gzhandler' != ini_get('output_handler') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
	header('Vary: Accept-Encoding'); // Handle proxies
	if ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'deflate') && function_exists('gzdeflate') && ! $force_gzip ) {
		header('Content-Encoding: deflate');
		$out = gzdeflate( $out, 3 );
	} elseif ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip') && function_exists('gzencode') ) {
		header('Content-Encoding: gzip');
		$out = gzencode( $out, 3 );
	}
}

echo $out;
exit;


?>