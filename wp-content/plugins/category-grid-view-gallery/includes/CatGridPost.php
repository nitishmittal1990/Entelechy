<?php
/**
The file to load the post content in Colorbox. DO NOT REMOVE wp-config.php include..
it is needed for using wordpress functions.
Author : Anshul Sharma (contact@anshulsharma.in)
 */
if(!function_exists('get_post'))
{
require_once("../../../../wp-load.php");

}
$theID =$_GET["ID"];

//Meta information about the post
function cg_posted_on() {
	$theID =$_GET["ID"];
	printf( __( '<span>Posted on <a href="%1$s" title="%2$s" rel="bookmark"><time class="cg-entry-date" datetime="%3$s" pubdate>%4$s</time></a> by <a href="%5$s" title="%6$s" rel="author">%7$s</a></span>', 'cgview' ),
		esc_url(get_permalink()),
		esc_attr( get_the_time()),
		esc_attr( get_the_date('c') ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		sprintf( esc_attr__( 'View all posts by %s', 'cgview' ), get_the_author()),
		esc_html( get_the_author() )
	);
}
?>
<?php
/**
 * The Post template.
 */
?><!DOCTYPE html>

<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	//wp_title( '|', true, 'right' );
	echo get_the_title($theID).' | ';
	// Add the blog name.
	bloginfo( 'name' );
	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'cgview' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $cg_url.'/css/cgview-lightbox.css' ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" /> 

<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	if( !is_admin()){
	wp_deregister_script('jquery');
	}
	wp_head();
?>

</head>

<body>
<div id="cg-page">
	<div id="cg-main"  style="width:<?php echo get_cg_option('lightbox_width'); ?>px;">
       <div id="cg-primary">
			<div id="cg-content" role="main">
            	<?php query_posts('p='.$theID); ?>
				<?php while (have_posts()) : the_post(); ?>
                <article id="cg-post-<?php echo $theID; ?>" <?php post_class('',$theID); ?>>
                	<div id="cg-header-wrap">
                        <header class="cg-header">
                            <h1 class="cg-title"><a href="<?php echo get_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'cgview' ), get_the_title()) ?>" rel="bookmark"><?php the_title(); ?></a></h1>
                
                            <div class="cg-entry-meta">
                                <?php cg_posted_on(); ?>
                                <?php if ( comments_open() && ! post_password_required() ) : ?>
                                 | <span class="cg-leave-reply">
                                <?php comments_popup_link(  __( 'Reply', 'cgview' ), _x( '1 Comment', 'comments number', 'cgview' ), _x( '% Comments', 'comments number', 'cgview' ) ); ?>
                                </span>
                            <?php endif; ?>
                
                            </div><!-- .entry-meta -->
                
                

                        </header><!-- .cg-header -->
                      </div><!-- #cg-header-wrap -->
            
                     <?php if ( is_search() ) : // Only display Excerpts for Search ?>
                    <div class="cg-entry-summary"  style=" <?php if (get_cg_option('lightbox_height'))echo 'height:'.get_cg_option('lightbox_height').'px;' ?>">
                        <?php the_excerpt(); ?>
                    </div>
                    <?php else : ?>
                    
                    <div class="cg-entry-content" style=" <?php if (get_cg_option('lightbox_height'))echo 'height:'.get_cg_option('lightbox_height').'px;' ?>">
                        <?php echo the_content(); ?>
                        <?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'cgview' ) . '</span>', 'after' => '</div>' ) ); ?>
                    </div><!-- .entry-content -->
                    <!-- <?php endif; ?> -->
            
                    <footer class="cg-entry-meta">
                        <?php $show_sep = false; ?>
                        <?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
                        <?php
                            /* translators: used between list items, there is a space after the comma */
                            $categories_list = get_the_category_list( __( ', ', 'cgview' ) );
                            if ( $categories_list ):
                        ?>
                        <span class="cg-cat-links">
                            <?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'cgview' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
                            $show_sep = true; ?>
                        </span>
                        <?php endif; // End if categories ?>
                        
                        <?php
                            /* translators: used between list items, there is a space after the comma */
                            $tags_list = get_the_tag_list( '', __( ', ', 'cgview' ) );
                            if ( $tags_list ):
							?>
                            <span class="cg-sep"> | </span>
                        <span class="cg-tag-links">
                            <?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'cgview' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );
                            $show_sep = true; ?>
                        </span>
                        <?php endif; // End if $tags_list ?>
                        
                        <?php endif; // End if 'post' == get_post_type() ?>
            
                        <?php if ( comments_open() ) : ?>
                        <span class="cg-sep"> | </span>
                        <span class="cg-comments-link"><?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'cgview' ) . '</span>', __( '<b>1</b> Reply', 'cgview' ), __( '<b>%</b> Replies', 'cgview' ) ); ?></span>
                        <?php endif; // End if comments_open() ?>
            
                        <?php edit_post_link( __( 'Edit', 'cgview' ), '<span class="cg-sep"> | </span><span class="cg-edit-link">', '</span>' ); ?>
                    </footer><!-- #cg-entry-meta -->
                </article><!-- #post-<?php $theID; ?> -->
            				<?php if (get_cg_option('load_comments')) {
                                 comments_template( '', true ); 
                             }
							 ?>

			<?php endwhile;?>
			</div><!-- #content -->
		</div><!-- #primary -->
</div><!-- #main -->

</body>
</html>

