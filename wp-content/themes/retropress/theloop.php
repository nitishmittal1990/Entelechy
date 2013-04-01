<?php 
	global $post, $query_string, $SMTheme;
	query_posts($query_string);
	$i=1;
	if (have_posts()) :  
	
	if (!isset($_GET['ajaxpage'])) {?>
 <div class='articles'>
	<?php }
	while (have_posts()) : the_post(); 
	?>
		<div class='one-post'>
		<div id="post-<?php the_ID(); ?>" <?php post_class("post-caption"); ?>>
			<?php if (!is_single()&&!is_page()) { ?>
			<h2><a href="<?php the_permalink(); ?>" title="<?php printf( $SMTheme->_( 'permalink' ), the_title_attribute( 'echo=0' ) ); ?>" class='post_ttl'><?php the_title(); ?></a></h2>
			<?php } else { ?>
				<h1><?php the_title(); ?></h1>
			<?php } ?>
			<p><?php if (!is_page()) {?><span class='post-date'><span class='day'><?php echo get_the_date('d'); ?></span><br /><span class='month'><?php echo get_the_date('M'); ?></span></span> <img alt="" src="<?php echo get_template_directory_uri(); ?>/images/smt/category.png">Category:&nbsp;<?php the_category(',&nbsp;'); }?>
			<?php if(comments_open( get_the_ID() ))  {
                    ?><img alt="" src="<?php echo get_template_directory_uri(); ?>/images/smt/comments.png">Comments:&nbsp;<span class="meta_comments"><?php comments_popup_link( $SMTheme->_( 'noresponses' ), $SMTheme->_( 'oneresponse' ), $SMTheme->_( 'multiresponse' ) ); ?></span><?php
                }
                ?><?php edit_post_link( $SMTheme->_( 'edit' ), '&nbsp;&nbsp;|&nbsp;&nbsp;<span class="edit-link">', '</span>' ); ?></p>
		</div>
		<div class='post-body'>
			<?php
                if(has_post_thumbnail())  {
                    ?><?php if (!is_single()) { ?><a href="<?php the_permalink(); ?>" title="<?php printf( $SMTheme->_( 'permalink' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_post_thumbnail(
						array($SMTheme->get( 'layout', 'imgwidth' ), $SMTheme->get( 'layout', 'imgheight' )),
                        array("class" => $SMTheme->get( 'layout','imgpos' ) . " featured_image")
                    ); ?></a><?php } else { ?>
						<?php the_post_thumbnail(
						array(278, 173),
                        array("class" => $SMTheme->get( 'layout','imgpos' ) . " featured_image")
                    ); ?>
					<?php }
                }
				
				if (!is_single()&&!is_page()) {
					smtheme_excerpt('echo=1');
					?><a href='<?php the_permalink(); ?>' class='readmore'><?php echo $SMTheme->_( 'readmore' ); ?></a><?php
				} else {
					the_content('');
					
				}
            ?>
			<?php wp_link_pages(); ?>
		</div>
		</div>
	<?php endwhile; ?>
	
	<?php if (!isset($_GET['ajaxpage'])) {?>
 </div>
	<?php } ?>
	
	
<?php endif; ?>