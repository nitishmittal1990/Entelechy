<?php get_header(); ?>


<?php if (have_posts()) : ?><?php while (have_posts()) : the_post(); ?>




<div class="postcontent">
<div class="postimage"><a href="<?php the_permalink()?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_post_thumbnail(); ?></a></div>
<div class="posttextcontent">
<h3><a href="<?php the_permalink()?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title_attribute(); ?></a></h3>
<?php the_excerpt(); ?>
</div>
<div class="postdate">
<div class="postday"><?php echo get_the_date('jS'); ?></div>
<div class="postmonth"><?php echo get_the_date('M'); ?></div>
<div class="postyear"><?php echo get_the_date('y'); ?></div>
</div>
<div class="postnavigation">
<div class="postreadmore"><a href="<?php the_permalink()?>" title="<?php _e('Read more on','panels');?> <?php the_title_attribute(); ?>"><?php _e('Read more','panels');?></a></div>
<div class="postcomments"><a href="<?php the_permalink()?>#comments" title="<?php comments_number( '0 Comments', '1 Comment', '% Comments' ); ?>"><?php comments_number( '0 Comments', '1 Comment', '% Comments' ); ?></a></div>
</div>
</div>


<?php endwhile; ?>



<div class="contentheader"></div>
<div class="contentcontainer">
<div id="postnavigation">
<div id="previousposts"><?php next_posts_link( __( 'Previous Posts', 'panels' ) ); ?></div>
<div id="nextposts"><?php previous_posts_link( __( 'Next Posts', 'panels' ) ); ?></div>
</div>
</div>
<div class="contentfooter"></div>



		
<?php else : ?>
<div class="contentheader"></div>
<div class="contentcontainer">
<div id="pagecontent">
<h1><?php _e('No WordPress posts found','panels')?></h1>
<p><?php _e('There are no WordPress posts to display here.','panels')?></p>
</div>
</div>
<div class="contentfooter"></div>
<?php endif; ?>




<?php get_footer(); ?>