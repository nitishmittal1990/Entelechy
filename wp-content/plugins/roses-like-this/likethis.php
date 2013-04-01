<?php
/*
Plugin Name: Like This
Plugin URI: http://lifeasrose.ca/2011/03/wordpress-plugin-i-like-this/
Description: Integrates a "Like This" option for posts, similar to the facebook Like button.  For visitors who want to let the author know that they enjoyed the post, but don't want to go to the effort of commenting.
Version: 1.3
Author: Rose Pritchard
Author URI: http://lifeasrose.ca
License: GPL2

Copyright 2011  Rose Pritchard  (email : rose@r.osey.me)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function likeThis($post_id,$action = 'get') {

	if(!is_numeric($post_id)) {
		error_log("Error: Value submitted for post_id was not numeric");
		return;
	} //if

	switch($action) {
	
	case 'get':
		$data = get_post_meta($post_id, '_likes');
		
		if(!is_numeric($data[0])) {
			$data[0] = 0;
			add_post_meta($post_id, '_likes', '0', true);
		} //if
		
		return $data[0];
	break;
	
	
	case 'update':
		if(isset($_COOKIE["like_" . $post_id])) {
			return;
		} //if
		
		$currentValue = get_post_meta($post_id, '_likes');
		
		if(!is_numeric($currentValue[0])) {
			$currentValue[0] = 0;
			add_post_meta($post_id, '_likes', '1', true);
		} //if
		
		$currentValue[0]++;
		update_post_meta($post_id, '_likes', $currentValue[0]);
		
		setcookie("like_" . $post_id, $post_id,time()+(60*60*24*365));
	break;

	} //switch

} //likeThis

function printLikes($post_id) {
	$likes = likeThis($post_id);
	
	$who = ' people like ';
	
	if($likes == 1) {
		$who = ' person likes ';
	} //if
	
	if(isset($_COOKIE["like_" . $post_id])) {
	print '<a href="#" class="likeThis done" id="like-'.$post_id.'">'.$likes.$who.'this post</a>';
		return;
	} //if

	print '<a href="#" class="likeThis" id="like-'.$post_id.'">'.$likes.$who.'this post</a>';
} //printLikes


function setUpPostLikes($post_id) {
	if(!is_numeric($post_id)) {
		error_log("Error: Value submitted for post_id was not numeric");
		return;
	} //if
	
	
	add_post_meta($post_id, '_likes', '0', true);

} //setUpPost


function checkHeaders() {
	if(isset($_POST["likepost"])) {
		likeThis($_POST["likepost"],'update');
	} //if

} //checkHeaders


function jsIncludes() {
	wp_enqueue_script('jquery');
	
	wp_register_script('likesScript',
	WP_PLUGIN_URL . '/roses-like-this/likesScript.js' );
	wp_enqueue_script('likesScript',array('jquery'));

} //jsIncludes

add_action ('publish_post', 'setUpPostLikes');
add_action ('init', 'checkHeaders');
add_action ('get_header', 'jsIncludes');



/**
 * Popular Post Widget Class
 */
class MostLikedPosts extends WP_Widget {
	/** constructor */
		function __construct()
    	{
         parent::__construct( 'mostlikedposts', 'Most Liked Posts' );
	   }

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$numberOfPostsToShow = apply_filters('widget_numberOfPostsToShow',$instance['numberOfPostsToShow']);
		print $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title; 
			
			
global $wpdb;
 $querystr = "
    SELECT $wpdb->posts.* 
    FROM $wpdb->posts, $wpdb->postmeta
    WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
    AND $wpdb->postmeta.meta_key = '_likes' 
    AND $wpdb->posts.post_status = 'publish' 
    AND $wpdb->posts.post_type = 'post'
    ORDER BY $wpdb->postmeta.meta_value DESC
    LIMIT " . $numberOfPostsToShow;

 $pageposts = $wpdb->get_results($querystr, OBJECT);
  if ($pageposts):
  global $post;
  print "<ul>";
  foreach ($pageposts as $post):
  setup_postdata($post);
 ?>
  <li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
    <?php the_title(); ?></a> (<?php print get_post_meta(get_the_id(),"_likes",1);  ?> likes)</li>
     <?php endforeach;
   print "</ul>"; ?>
 <?php endif; 

			print $after_widget;
			
		}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
		if(is_numeric($new_instance['numberOfPostsToShow'])) { 
		 $instance['numberOfPostsToShow'] = strip_tags($new_instance['numberOfPostsToShow']);
		} else {
		 
		 $instance['numberOfPostsToShow'] = strip_tags("5");
		}
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
			$numberOfPostsToShow = esc_attr( $instance[ 'numberOfPostsToShow' ] );
		}
		else {
			$title = __( 'Most Liked Posts', 'text_domain' );
			$numberOfPostsToShow = __( '5', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		
		
		<p>
		<label for="<?php echo $this->get_field_id('numberOfPostsToShow'); ?>"><?php _e('Number of Posts to Show:'); ?></label> 
		<input class="shortfat" id="<?php echo $this->get_field_id('numberOfPostsToShow'); ?>" name="<?php echo $this->get_field_name('numberOfPostsToShow'); ?>" width="3" type="text" value="<?php echo $numberOfPostsToShow; ?>" />
		</p>
		<?php 
	}

} // class MostLikedPosts

add_action( 'widgets_init', create_function( '', 'return register_widget("MostLikedPosts");' ) );


?>