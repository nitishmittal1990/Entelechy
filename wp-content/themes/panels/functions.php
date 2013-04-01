<?php

if (!is_admin())
        add_action('wp_enqueue_scripts', 'panels_js');
	function panels_js() {
        wp_enqueue_style( 'panels-style', get_stylesheet_uri() );
        if ( is_singular() && get_option( 'thread_comments' ) )
	wp_enqueue_script( 'comment-reply' );
}

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 200, 200, true );

function panels_main_image() {
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
	print "<img src='$main' alt='$the_title' />";
  endif;
}

function panels_menu() {
  register_nav_menus(
    array(
    'header-menu' => __( 'Header Menu', 'panels' ),
    'left-footer-menu' => __( 'Left Footer Menu', 'panels' ),
    'right-footer-menu' => __( 'Right Footer Menu', 'panels' )
	)
  );
}
add_action( 'init', 'panels_menu' );


	$custom_header_support = array(
		'default-image'          => get_template_directory_uri() . '/headers/001.jpg',
		'width' => apply_filters( 'panels_header_image_width', 770 ),
		'height' => apply_filters( 'panels_header_image_height', 180 ),
		'header-text'            => false,
	);	
	
	add_theme_support( 'custom-header', $custom_header_support );
	
	register_default_headers( array(
		'bluesky' => array (
                'url' => '%s/headers/001.jpg',
                'thumbnail_url' => '%s/headers/thumbnails/001_thumb.jpg',
                'description' => __( 'Rainbow Lake', 'panels' )
            ),
                'grass' => array (
                'url' => '%s/headers/002.jpg',
                'thumbnail_url' => '%s/headers/thumbnails/002_thumb.jpg',
                'description' => __( 'Green Fields', 'panels' )
            ),
            	'wave' => array (
                'url' => '%s/headers/003.jpg',
                'thumbnail_url' => '%s/headers/thumbnails/003_thumb.jpg',
                'description' => __( 'Haze', 'panels' )
            ),
	) );


add_theme_support( 'custom-background', array(
	'default-image' => get_stylesheet_directory_uri() . '/img/bg.jpg',
	'default-color' => 'FFFFFF'
) );


add_filter('the_title', 'panels_title');
function panels_title($title) {
if ($title == '') {
return 'Untitled Post';
} else {
return $title;
}
}

function panels_custom_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'panels_custom_excerpt_length', 999 );

function panels_replace_excerpt($content) {
       return str_replace(' [...]',
               '...',
               $content
       );
}
add_filter('the_excerpt', 'panels_replace_excerpt');


function panels_widgets_init() {

		register_sidebar( array(
			'name' => __( 'Footer Left', 'panels' ),
			'id' => 'footer-left',
			'description' => __( 'The left footer widget area.', 'panels' ),
			'before_widget' => '<div class="widgetheader"></div><div class="widgetcontainer">',
			'after_widget' => '</div><div class="widgetfooter"></div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>',
		) );
		
		register_sidebar( array(
			'name' => __( 'Footer Right', 'panels' ),
			'id' => 'footer-right',
			'description' => __( 'The right footer widget area.', 'panels' ),
			'before_widget' => '<div class="widgetheader"></div><div class="widgetcontainer">',
			'after_widget' => '</div><div class="widgetfooter"></div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>',
		) );
}




function panels_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'panels' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'panels' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'panels' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'panels' ), ' ' );
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
		<p><?php _e( 'Pingback:', 'panels' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'panels' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}


//Required by WordPress
	add_theme_support('automatic-feed-links');
	
	
	//CONTENT WIDTH
		if ( ! isset( $content_width ) ) $content_width = 770;


//LOCALIZATION
	
	//Enable localization
		load_theme_textdomain('panels',get_template_directory() . '/languages');
		
	
// filter function for wp_title
function panels_filter_wp_title( $old_title, $sep, $sep_location ){
		// add padding to the sep
		$ssep = ' ' . $sep . ' ';
			
		// find the type of index page this is
		if( is_category() ) 
				$insert = $ssep . __( 'Category', 'panels' );
		elseif( is_tag() ) 
				$insert = $ssep . __( 'Tag', 'panels' );
		elseif( is_author() ) 
				$insert = $ssep . __( 'Author', 'panels' );
		elseif( is_year() || is_month() || is_day() ) 
				$insert = $ssep . __( 'Archives', 'panels' );
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


add_filter( 'wp_title', 'panels_filter_wp_title', 10, 3 );
add_action( 'widgets_init', 'panels_widgets_init' );
?>