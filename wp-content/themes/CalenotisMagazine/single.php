<?php get_header(); ?>

<div id="contentwrap">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post();?>
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<h2 class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry">
					<?php the_content(''); ?>
				</div>
            	<p class="postmetadata">Posted in <?php the_category(', '); ?>&nbsp; <?php the_tags(); ?></p>
			</div>

            <?php comments_template(true, ''); ?>

		<?php endwhile; ?>

	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
