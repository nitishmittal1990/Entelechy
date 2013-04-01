<?php get_header() ?>
	<div id="contentwrap">

		<?php if(have_posts()): ?>
		<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
		<?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle">Archive for the '<?php single_cat_title(); ?>' Category</h2>
		<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2 class="pagetitle">Posts Tagged With '<?php single_tag_title(); ?>'</h2>
		<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
		<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
		<?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle">Author Archive</h2>
		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle">Blog Archives</h2>
		<?php } ?>
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

	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
