<?php get_header(); ?>


<?php if (have_posts()) : ?>
<div id="maincontent">
<h1><?php printf( __( 'Search Results for: %s', 'appliance' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
</div>

<?php while (have_posts()) : the_post(); ?>


<div class="postbg">
<div class="postimage"><a href="<?php the_permalink()?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_post_thumbnail(); ?></a></div>
<div class="postcontent">
<h3><a href="<?php the_permalink()?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title()?></a></h3>
<div class="posttext"><?php the_excerpt(); ?></div>
</div>
<div class="postreadmore"><h5><a href="<?php the_permalink()?>" title="<?php _e('Read more on','appliance');?> <?php the_title_attribute(); ?>" rel="bookmark"><?php _e('Read more','appliance');?></a></h5></div>
<div class="smldivider"></div>
<div class="postcats"></div>
<div class="postcomments"><a href="<?php the_permalink()?>#comments" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php printf( _n( '1 comment', '%1$s comments', get_comments_number(), 'appliance' ), number_format_i18n( get_comments_number() ) ); ?></a></div>
</div>


<?php endwhile; ?>


</div>
</div>

<div class="divider"></div>

<div id="postnavigation">
<div id="previousposts"><?php next_posts_link( __( 'Previous Entries', 'appliance' ) ); ?></div>
<div id="nextposts"><?php previous_posts_link( __( 'Next Entries', 'appliance' ) ); ?></div>
</div>
		
<?php else : ?>
<div id="maincontent">
<h1><?php _e('No posts found','appliance')?></h1>
<p><?php _e('There are no posts to display here.','appliance')?></p>
</div>
<?php endif; ?>




<?php get_footer(); ?>