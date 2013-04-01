<?php
/**
 * Helper functions to increase productivity
 *
 * @package DP Core
 * @subpackage Functions
 */

/**
 * Check a string within a string, this function will be useful 
 * when strpos() doesn't determine correctly.
 *
 * @since 1.0
 * @param string $a Finding in this string.
 * @param string $b Finding this string.
 */
function in_str($string, $find) {
	$check = explode($find, $string);
	return (count($check) > 1);
}

function is_url($url) {
    $info = parse_url($url);
    return ($info['scheme']=='http' || $info['scheme']=='https') && $info['host'] != "";
} 

function is_image($handler) {
    $ext = preg_match('/\.([^.]+)$/', $file, $matches) ? strtolower($matches[1]) : false;
	$image_exts = array('jpg', 'jpeg', 'gif', 'png');
	
	return in_array($ext, $image_exts);
}  

/**
 * Check if an array is associative.
 *
 * @since 1.0
 * @param array $arr
 * @return bool
 */
function is_assoc($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * Sanitize a string for filed name.
 *
 * Keys are used as internal identifiers. Lowercase alphanumeric characters and underscores are allowed.
 *
 * @since 1.0
 *
 * @param string $name String name
 * @return string Sanitized name
 */
function sanitize_field_name($name) {
	$raw_name = $name;
	$name = strtolower( $name );
	$name = preg_replace( '/[^a-z0-9_\[\]]/', '_', $name );
	return apply_filters( 'sanitize_field_name', $name, $raw_name );
}

function sanitize_field_value($value) {
	$raw_value = $value;
	$value = strtolower( $value );
	$value = preg_replace( '/[^a-z0-9_]/', '_', $value );
	return apply_filters( 'sanitize_field_value', $value, $raw_value );
}

/**
 * Sanitize a string for field id or class.
 *
 * Keys are used as internal identifiers. Lowercase alphanumeric characters and dashes are allowed.
 *
 * @since 1.0
 *
 * @param string $id String id
 * @return string Sanitized id
 */
function sanitize_field_id($id) {
	$raw_id = $id;
	$id = strtolower( $id );
	$id = preg_replace( '/[^a-z0-9\-]/', '-', $id );
	return apply_filters( 'sanitize_field_id', $id, $raw_id );
}