<?php

function ninja_pages_add_cats_box(){
	$options = get_option('ninja_pages_options');
	if( isset( $options['add_cats'] ) ) :
		if ( function_exists( 'register_taxonomy_for_object_type' ) ) :
			register_taxonomy_for_object_type('category', 'page');
		endif;
	endif;
}

function ninja_pages_add_tags_box(){
	$options = get_option('ninja_pages_options');
	if( isset( $options['add_tags'] ) ) :
		if ( function_exists( 'register_taxonomy_for_object_type' ) ) :
			register_taxonomy_for_object_type('post_tag', 'page');
		endif;
	endif;
}

function ninja_pages_add_excerpt_box(){
	$options = get_option('ninja_pages_options');
	if( isset( $options['add_excerpts'] ) ) :
		if ( function_exists( 'add_post_type_support' ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}
	endif;
}

function ninja_pages_add_children(){
	add_action('init', 'ninja_pages_save_postdata');
}

add_filter('excerpt_length', 'ninja_pages_adjust_excerpt_length', 999);
function ninja_pages_adjust_excerpt_length($length){
	$options = get_option('ninja_pages_options');
	if( isset( $options['excerpt_length'] ) && 'page' == get_post_type() ) :
		return $options['excerpt_length'];
	else :
		return $length;
	endif;
}

function ninja_pages_continue_reading_link() {
	$options = get_option('ninja_pages_options');
	return '<a class="ninja_pages_read_more"  href="'. esc_url( get_permalink() ) . '">' . $options['more_link'] . '</a>';
}

function ninja_pages_auto_excerpt_more( $more ) {
	$options = get_option('ninja_pages_options');
	if( isset( $options['more_link'] ) && 'page' == get_post_type() ) :
		return ninja_pages_continue_reading_link();
	else :
		return $more;
	endif;
}
add_filter( 'excerpt_more', 'ninja_pages_auto_excerpt_more', 999 );

function ninja_pages_display_terms( $taxonomy, $seperator ) {

	global $post;
	$terms = get_the_terms( $post->ID, $taxonomy );

	if ( $terms && ! is_wp_error( $terms ) ) :

		$content = '';
		$num = count( $terms );
		$i = 1;

		foreach ( $terms as $term ) {

			$link = get_term_link( (int)$term->term_id, $taxonomy );

			$content .= '<a href="' . $link . '">';
				$content .= $term->name;
			$content .= '</a>';

			if( $i < $num ) {
				$content .= $seperator . ' ';
			}

			$i++;
		}

		return $content;

	endif;

}

function ninja_pages_archives_include_pages( $query ) {
	global $wp_query;

	$options = get_option('ninja_pages_options');
	//Don't break admin or preview pages.
	if ( isset( $options['add_archives'] ) && $query->is_main_query() ) :

		if( is_category() || is_tag() ) :

			if ($query->is_feed) {
				// Do feed processing here.
			} else {
				$post_type = get_query_var( 'post_type' );

				if ( !empty( $post_type ) ) {
					$post_type = $post_type;
				} else {
					$post_type = array('post', 'page');
				}

				$query->set( 'post_type' , $post_type );
			}

		endif;

	endif;

	return $query;
}
add_action( 'pre_get_posts' , 'ninja_pages_archives_include_pages' );


// validate our options
function ninja_pages_options_validate($input) {
	$valid = array();
	if( isset( $input['add_cats'] ) ) :
		$valid['add_cats'] = 1;
	endif;
	if( isset( $input['add_tags'] ) ) :
		$valid['add_tags'] = 1;
	endif;
	if( isset( $input['add_excerpts'] ) ) :
		$valid['add_excerpts'] = 1;
	endif;
	if( isset( $input['add_archives'] ) ) :
		$valid['add_archives'] = 1;
	endif;
	if( isset( $input['excerpt_length'] ) ) :
		$valid['excerpt_length'] = preg_replace( '/[^0-9]/', '', $input['excerpt_length'] );
	endif;
	if( isset( $input['more_link'] ) ) :
		$valid['more_link'] = $input['more_link'];
	endif;
	if( isset( $input['display_children'] ) ) :
		$valid['display_children'] = 1;
	endif;
	if( isset( $input['num_children'] ) && !empty( $input['num_children'] ) ) :
		$valid['num_children'] = intval($input['num_children']);
	endif;
	if( isset( $input['orderby'] ) ) :
		$valid['orderby'] = $input['orderby'];
	endif;
	if( isset( $input['order'] ) ) :
		$valid['order'] = $input['order'];
	endif;

	return $valid;
}