<?php 
/*
Plugin Name: Read More Inline
Description: Allow users to toggle content following the 'more' link on a page.
Author: Stephen Gray
Author URI: http://www.pobo.org
Version: 0.2
*/

function pobo_rmi_oninit(){
    if(!is_admin()){
	    	$pluginUrl = plugin_dir_url(__FILE__);
			//include javascript to toggle content after the 'more' link
	        wp_register_script(
				'pobo_rmi', 
	            $pluginUrl . "js/pobo_rmi.js",
	            array('jquery'));
	        wp_enqueue_script('pobo_rmi');
	}
  
}

add_action('init', pobo_rmi_oninit);


/**
 * This filter gets the content after the 'more' link
 * and places it in a div with a class of 'readmoreinline',
 * which the javascript a) hides and b) adds a toggle to,
 * so clicking the 'more' link alternately shows and hides the extra content.
 * <br>
 * @param $link
 * @return $link with new class
 */
function pobo_rmi_morelink_filter($link){
	global $post;
	$my_id = $post->ID;
	 //$spanId = "more-" . $post->ID;
		$post_object= get_post($my_id);
		$content = $post_object->post_content;
        // grab only the stuff after 'more'
        $debris = explode('<!--more-->', $content);

	$link.='</p><div class="readmoreinline">'.$debris[1].'</div>';
	return $link;
}

add_filter('the_content_more_link', pobo_rmi_morelink_filter, 999);

?>
