<?php get_header(); ?>
	<div id="maincontent">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); //The Loop?>
		<div <?php post_class()?>>
			<h1><?php the_title()?></h1>
			
			
<?php the_content()?>
			
			<div class="postfooter">
			<a href="<?php the_permalink(); ?>"><?php the_date()?></a> <?php wp_link_pages( array( 'before' => __('Pages', 'appliance'), 'after' =>'' ) ); ?><br/>
			<?php _e('Categories','appliance');?>: <?php the_category(', '); ?> <?php if(has_tag()){the_tags( _e('Tags','appliance') . ': ', ', ');}?>
			</div>
			
		
		</div>
		
		
<?php comments_template( '', true ); ?>
		<?php endwhile;endif;?>
	</div>
	
<div class="divider"></div>
<div id="postnavigation">
<div id="previousposts"><?php previous_post_link('%link'); ?></div>
<div id="nextposts"><?php next_post_link('%link'); ?></div>
</div>

<?php get_footer(); ?>