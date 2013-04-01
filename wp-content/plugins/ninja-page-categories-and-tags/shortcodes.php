<?php
function ninjapages_child_pages( $content ) {

	global $post; // required

	$opt = get_option('ninja_pages_options');
	if( isset( $opt['display_children'] ) ) {
		$content = $content;
	} else {
		$content = '';
	}
	if( isset( $opt['num_children'] ) ) {
		$num = $opt['num_children'];
	} else {
		$num = get_option('posts_per_page');
	}
	if( isset( $opt['orderby'] ) ) {
		$orderby = $opt['orderby'];
	} else {
		$orderby = 'menu_order';
	}
	if( isset( $opt['order'] ) ) {
		$order = $opt['order'];
	} else {
		$order = 'ASC';
	}
	if( !is_archive() ) {

		$args = array(
			'post_type' => 'page',
			'post_parent' => $post->ID,
			'posts_per_page' => $num,
			'orderby' => $orderby,
			'order' => $order,
		);

		$children = new WP_Query( $args );

		//print_r($args);
		if ( $children ) {

			$content .= '<div id="ninja-children-wrap">';

			while( $children->have_posts()) : $children->the_post();

				$content .= '<div class="ninja-child-wrap">';
				if( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail() ) {
					$content .= '<div class="ninja-child-thumbnail">';
					$content .= get_the_post_thumbnail( $post->ID, 'thumbnail' );
					$content .= '</div>';
				}
				$content .= '<h3 class="ninja-child-title">';
					$content .= '<a href="' . get_permalink() . '">';
					$content .= get_the_title();
					$content .= '</a>';
				$content .= '</h3>';
				$content .= '<div class="ninja-child-entry">';
					$content .= get_the_excerpt();
				$content .= '</div>';
				$content .= '</div>';

			endwhile;

			$content .= '</div>';

		}

	}

	return $content;

}
add_shortcode('ninja_child_pages', 'ninjapages_child_pages');