<?php
/**
 * Plugin Name: DP Excerpt
 * Plugin URI: http://dedepress.com/plugins/dp-excerpt/
 * Description: An advanced post excerpt plugin for WordPress. 
 * Version: 1.0
 * Author: Cloud Stone
 * Author URI: http://dedepress.com/
 * License: GPL V2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
 
if(!class_exists('DP_Panel')) {
	require_once('lib/core.php');
	require_once('lib/forms.php');
	require_once('lib/panel.php');
	require_once('lib/helpers.php');
	require_once('lib/debug.php');
}

load_plugin_textdomain( 'dp-excerpt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


class DP_Excerpt_Panel extends DP_Panel {

	function DP_Excerpt_Panel() {
		$this->menu_slug = 'dp-excerpt';
		$this->plugin_file = 'dp-excerpt/dp-excerpt.php';
		$this->textdomain = 'dp-excerpt';
		$this->wp_plugin_url = 'http://wordpress.org/extend/plugins/dp-excerpt/';
		$this->settings_url = admin_url( 'options-general.php?page=dp-excerpt' );
		
		$this->DP_Panel();
	}
	
	function add_menu_pages() {
		$this->page_hook = add_options_page('DP Excerpt', 'DP Excerpt', 'edit_plugins', 'dp-excerpt', array(&$this, 'menu_page'));
	}
	
	function add_meta_boxes() {
		add_meta_box('dp-excerpt-meta-box', __('DP Excerpt Settings', $this->textdomain), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		$this->add_default_meta_boxes(array('plugin-info', 'like-this', 'need-support', 'quick-preview'));
	}
	
	function defaults() {
		$textdomain = $this->textdomain;
	
		/* Remix Excerpt */
		$defaults['dp-excerpt-meta-box'] = array(
			array(
				'name' => 'dp_excerpt[cut_type]',
				'title' => __('Cut type', $textdomain),
				'type' => 'select',
				'value' => 'words',
				'options' => array('words' =>  __('Words', $textdomain), 'chars' => __('Characters', $textdomain))
			),
			array(
				'name' => 'dp_excerpt[length]',
				'title' => __('Excerpt Length', $textdomain),
				'type' => 'text',
				'value' => '55',
				'class' => 'small-text'
			),
			array(
				'name' => 'dp_excerpt[allowed_tags]',
				'title' => __('Allowed Tags', $textdomain),
				'type' => 'textarea',
				'value' => '<a><strong><em><span><abbr><b><i><s><del><ins><cite><code><sup><sub>'
			),
			array(
				'name' => 'dp_excerpt[shortcodes]',
				'title' => __('Shortcodes', $textdomain),
				'type' => 'checkbox',
				'label' => __('Display shortcodes on excerpt. (not recommended)', $textdomain)
			),
			array(
				'name' => 'dp_excerpt[ellipsis]',
				'title' => __('Ellipsis', $textdomain),
				'type' => 'text',
				'value' => '...'
			),
			array(
				'name' => 'dp_excerpt[more_text]',
				'title' => __('"Read more" Text', $textdomain),
				'type' => 'text',
				'value' => __('Read more &raquo;', $textdomain)
			),
			array(
				'name' => 'dp_excerpt[more_tag]',
				'title' => __('More Tag', $textdomain),
				'type' => 'checkbox',
				'label' => __('Whether to cut content if <code>&lt;!--more--&gt;</code> tag was used before the summary of a specified length.', $textdomain)
			),
			array(
				'name' => 'dp_excerpt[manual_excerpt]',
				'title' => __('Manual Excerpt', $textdomain),
				'type' => 'select',
				'value' => '1',
				'options' => array('0' => __('Don\'t use manual excerpt',$textdomain), '1' => __('Output full manual excerpt',$textdomain),'cut' => __('Still cut manual excerpt', $textdomain))
			)
		);
		
		return $defaults;
	}
	
}
dp_register_panel('DP_Excerpt_Panel');

add_filter('the_excerpt','do_shortcode');
add_filter('get_the_excerpt', 'dp_trim_excerpt'); 
remove_filter( 'get_the_excerpt', 'wp_trim_excerpt'  );

/**
 * The excerpt filter for the_excerpt() function
 *
 * @since 1.0
 *
 * @param array $args Arguments for the excerpt.
 * @return string The excerpt.
 */
function dp_trim_excerpt($args) {
	$settings = get_option('dp_excerpt');
	
	return dp_excerpt(false, $settings);
}


/**
 * Generates a better excerpt from the content
 *
 * @since 1.0
 *
 * @param string $text The excerpt. If set to empty an excerpt is generated.
 * @return string The excerpt.
 */
function dp_excerpt($text = null, $args = array()) {
	global $post, $more;
	
	$defaults = array(
		'length' => apply_filters('excerpt_length', 55),
		'allowed_tags' => '',
		'shortcodes' => false,
		'cut_type' => 'words', // words or chars
		'more_link_text' => 'Read more &raquo;',
		'ellipsis' => '...',
		'more_tag' => true,
		'manual_excerpt' => true // 
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	if($more_tag)
		$more = false;
	else
		$more = true;
	
	// Clean text for post excerpt on admin edit view
	if(is_admin()) {
		$shortcodes = false;
		$more_link_text = false;
		$allowed_tags = '';
	}
	
	// If $text is not specified, get content from each post;
	if (!$text) 
		$text = get_the_content('');

	if($manual_excerpt && $post->post_excerpt)
		$text = $post->post_excerpt;
		
	$plain_text = preg_replace('/[\n\r\t]+/', ' ', strip_tags(strip_shortcodes($text)));
		
	$text = strip_tags($text, $allowed_tags);
	if(!$shortcodes)
		$text = strip_shortcodes($text);
	
	$i = 0;
	$check = $length;
	$more_link_text_tag = '';
	
	if(!$post->post_excerpt || $manual_excerpt === 'cut') {
	// Use words
	if($cut_type == 'words') {
		if (str_word_count($plain_text) > $length ) {
			$text = str_replace('%%more_link_text%%', '', $text);
			$true_text = '';
			
			while($i < $check) {
				$words = preg_split("/[\n\r\t ]+/", $text, $length+1, PREG_SPLIT_NO_EMPTY);
				array_pop($words);
				$true_text = implode(' ', $words);
					
				if(!$allowed_tags)
					break;
					
				$i = str_word_count(strip_tags(strip_shortcodes($true_text)));
				$length += $check-$i;
			}
			
			if($allowed_tags)
				$text = force_balance_tags($true_text);
				
			$text = $true_text;
		}
	} 
	 // Use characters
	else {
		if(mb_strwidth($plain_text) > $length) {
			
			$text = str_replace('%%more_link_text%%', '', $text);
			if($allowed_tags) {
				while($i < $check) {
					$i = mb_strwidth(preg_replace('/[\n\r\t]+/', ' ', strip_tags(strip_shortcodes(mb_strimwidth($text, 0, $length)))));
					$length += $check-$i;
				}
			}
			
			$text = mb_strimwidth($text, 0, $length);
			if($allowed_tags)
				$text = force_balance_tags($text);
		}
	}
	}
	
	if(mb_strwidth(strip_tags(strip_shortcodes($post->post_content))) > mb_strwidth(strip_tags(strip_shortcodes($text)))) {
		$more_link_text_tag = '%%more_link_text%%';
		$text .= $more_link_text_tag;
	}
	
	$text = wpautop($text);
	if($shortcodes)
		$text = shortcode_unautop($text);
	$text = strip_tags($text, $allowed_tags);
	
	if($more_link_text && $more_link_text_tag) {
		$more_link_text = apply_filters('excerpt_more', $ellipsis . ' <a class="more-link" href="'. get_permalink($post->ID) . '">' . $more_link_text . '</a>');
		$text = str_replace('%%more_link_text%%', $more_link_text, $text);
	} else {
		$text = str_replace('%%more_link_text%%', '', $text);
	}

	$text = str_replace(']]>', ']]&gt;', $text);
		
	return apply_filters('dp_excerpt', $text);
}