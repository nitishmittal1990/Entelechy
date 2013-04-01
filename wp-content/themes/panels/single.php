<?php get_header(); ?>


<div class="contentheader"></div>
<div class="contentcontainer">


	<div id="pagecontent">
	
	
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<div <?php post_class()?>>
			<h1><?php the_title()?></h1>
			
			
<?php the_content()?>
			
			<div class="postfooter">
			<?php the_date()?> <a href="<?php the_permalink(); ?>"><?php _e('Permalink','panels');?></a> <?php wp_link_pages( array( 'before' => __('Pages', 'panels'), 'after' =>'' ) ); ?><br/>
			<?php _e('Categories','panels');?>: <?php the_category(', '); ?> <?php if(has_tag()){the_tags( _e('Tags','panels') . ': ', ', ');}?>
			</div>
			
		
		</div>
		
		
<?php comments_template( '', true ); ?>
		<?php endwhile;endif;?>
	</div>
	
</div>
<div class="contentfooter"></div>
	
	
<div class="contentheader"></div>
<div class="contentcontainer">
<div id="postnavigation">
<div id="previousposts"><?php previous_post_link('%link'); ?></div>
<div id="nextposts"><?php next_post_link('%link'); ?></div>
</div>
</div>
<div class="contentfooter"></div>
<?php get_footer(); ?>