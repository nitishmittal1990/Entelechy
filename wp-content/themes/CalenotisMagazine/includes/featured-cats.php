<div class="side-widget">
<?php $featured_category1 = get_option('swt_featured_category1'); $featured_number1 = get_option('swt_featured_number1'); ?>
<?php if(($featured_category1 == "Choose a category:") || ($featured_number1 == '')) { ?>
<?php { /* nothing */ } ?>
<?php } else { ?>
<div class="feat-cat-entry">
<h3>Featured Posts</h3>
<?php
//insert your category name
$my_query = new WP_Query('category_name='. $featured_category1 . '&' . 'offset=' . '&' . 'showposts='. $featured_number1);
while ($my_query->have_posts()) : $my_query->the_post(); $do_not_duplicate = $post->ID; $the_post_ids = get_the_ID();
?>
<div class="feat-cat-meta post-<?php the_ID(); ?>">
<div class="fi"><?php get_the_image( array( 'custom_key' => array( 'fimg' ), 'default_size' => 'full', 'image_class' => 'alignleft', 'width' => '68', 'height' => '68' ) ); ?></div>
<h4><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
<div class="sep"></div>
<p><?php truncate_post(100,true); ?></p>
<div class="clearfix"></div>
</div><!-- FEAT META <?php the_ID(); ?> END -->
<?php endwhile;?>
</div><!-- FEAT CAT ENTRY END -->
<?php } ?>
</div><!-- FEATURED CATEGORY END -->
