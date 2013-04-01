<?php

if (!is_admin())
        add_action('wp_enqueue_scripts', 'appliance_js');
	function appliance_js() {
        wp_enqueue_script( 'mootools', get_template_directory_uri() . '/js/mootools.js' );      
        wp_enqueue_script( 'appliance-script', get_template_directory_uri() . '/js/script.js' );
        wp_enqueue_script( 'mootools-more', get_template_directory_uri() . '/js/mootools-more.js' );
        wp_enqueue_style( 'appliance-style', get_stylesheet_uri() );
        if ( is_singular() && get_option( 'thread_comments' ) )
	wp_enqueue_script( 'comment-reply' );
}

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 214, 120, true );

function appliance_main_image() {
$files = get_children('post_parent='.get_the_ID().'&post_type=attachment
&post_mime_type=image&order=desc');
  if($files) :
    $keys = array_reverse(array_keys($files));
    $j=0;
    $num = $keys[$j];
    $image=wp_get_attachment_image($num, 'large', true);
    $imagepieces = explode('"', $image);
    $imagepath = $imagepieces[1];
    $main=wp_get_attachment_url($num);
	$template=get_template_directory();
	$the_title= the_title_attribute( 'echo=0');
	print "<img src='$main' alt='$the_title' class='frame' />";
  endif;
}

function appliance_menu() {
  register_nav_menus(
    array( 'footer-menu' => __( 'Footer Menu', 'appliance' ) )
  );
}
add_action( 'init', 'appliance_menu' );

function appliance_custom_excerpt_length( $length ) {
	return 15;
}
add_filter( 'excerpt_length', 'appliance_custom_excerpt_length', 999 );

function appliance_replace_excerpt($content) {
       return str_replace(' [...]',
               '...',
               $content
       );
}
add_filter('the_excerpt', 'appliance_replace_excerpt');

add_filter('the_title', 'appliance_title');
function appliance_title($title) {
if ($title == '') {
return 'This Post Has No Title';
} else {
return $title;
}
}


function appliance_widgets_init() {
		// Footer Menu
		register_sidebar( array(
			'name' => __( 'Footer Widget Area', 'appliance' ),
			'id' => 'primary-widget-area',
			'description' => __( 'The footer widget area.  Please note that you need to specify a title for each widget, in order for it to appear.  In particular, you need to type in a title for the Search Widget. ', 'appliance' ),
			'before_widget' => '',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2><div class="content">',
		) );
}


function appliance_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'appliance' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'appliance' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'appliance' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'appliance' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'appliance' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'appliance' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}


//Required by WordPress
	add_theme_support('automatic-feed-links');
	
	
	//CONTENT WIDTH
		if ( ! isset( $content_width ) ) $content_width = 1000;


//LOCALIZATION
	
	//Enable localization
		load_theme_textdomain('appliance',get_template_directory() . '/languages');
		
	
// filter function for wp_title
function appliance_filter_wp_title( $old_title, $sep, $sep_location ){
		// add padding to the sep
		$ssep = ' ' . $sep . ' ';
			
		// find the type of index page this is
		if( is_category() ) 
				$insert = $ssep . __( 'Category', 'appliance' );
		elseif( is_tag() ) 
				$insert = $ssep . __( 'Tag', 'appliance' );
		elseif( is_author() ) 
				$insert = $ssep . __( 'Author', 'appliance' );
		elseif( is_year() || is_month() || is_day() ) 
				$insert = $ssep . __( 'Archives', 'appliance' );
		else 
				$insert = NULL;
			
		// get the page number we're on (index)
		if( get_query_var( 'paged' ) )
				$num = $ssep . 'page ' . get_query_var( 'paged' );
			
		// get the page number we're on (multipage post)
		elseif( get_query_var( 'page' ) )
				$num = $ssep . 'page ' . get_query_var( 'page' );
			
		// else
		else $num = NULL;
			
		// concoct and return new title
return get_bloginfo( 'name' ) . $insert . $old_title . $num;
}

add_filter( 'wp_title', 'appliance_filter_wp_title', 10, 3 );
add_action( 'widgets_init', 'appliance_widgets_init' );
?>