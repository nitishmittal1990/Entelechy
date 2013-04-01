<?php get_header(); ?>

<?php if (get_option('swt_slider') == 'Hide') { ?>
<?php { echo ''; } ?>
<?php } else { include(TEMPLATEPATH . '/includes/slide.php'); } ?>

<div id="contentwrap">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post();
        $c++; // increment the counter
         if( $c % 2 != 0) {
      	   $extra_class = 'leftpost';
           } else {
           $extra_class = 'rightpost';
           }
        ?>
			<div <?php post_class($extra_class) ?> id="post-<?php the_ID(); ?>">

               <?php if ( function_exists( 'get_the_image' ) ) {
                get_the_image( array( 'custom_key' => array( 'post_thumbnail' ), 'default_size' => 'full', 'image_class' => 'aligncenter', 'width' => '270', 'height' => '150' ) ); } ?>
				<h2 class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                <div class="meta">
                 Posted in <?php the_category(', ') ?> |
                 <?php comments_popup_link('No Comments', '1 Comment', '% Comments', 'comm'); ?>
                </div>

				<div class="entry">
					<?php the_content(''); ?>
				</div>

            	<p class="postmetadata"><a class="more-link" href="<?php the_permalink() ?>#more">Read More</a></p>
			</div>


            <?php if(++$counter % 2 == 0) : ?>
           <div class="clearp"></div>
          <?php endif; ?>

		<?php endwhile; ?>

		<div class="navigation">
        <?php
            include('includes/wp-pagenavi.php');
            if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
        ?>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php get_search_form(); ?>

	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
