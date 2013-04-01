<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.nivo.slider.pack.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.tooltip.min.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
jQuery(window).load(function() {
	$('#slider').nivoSlider({
		effect:'random', //Specify sets like: 'fold,fade,sliceDown'
		slices:20,
		animSpeed:900,
		pauseTime:4000,
		startSlide:0, //Set starting Slide (0 index)
		directionNav:false, //Next & Prev
		directionNavHide:false, //Only show on hover
		controlNav:true, //1,2,3...
        controlNavThumbs:false, //Use thumbnails for Control Nav
        controlNavThumbsFromRel:false, //Use image rel for thumbs
		keyboardNav:true, //Use left & right arrows
		pauseOnHover:true, //Stop animation while hovering
		manualAdvance:false, //Force manual transitions
		captionOpacity:0.8 //Universal caption opacity
	});
});

/*]]>*/
</script>
<div id="sliderw">
<div id="slider">
<?php
$slidecat = get_option('swt_slide_category');
$slidecount = get_option('swt_slide_count');
$my_query = new WP_Query('category_name= '. $slidecat .'&showposts='.$slidecount.'');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
<a href="<?php the_permalink() ?>"><img src="<?php echo get_post_meta($post->ID, 'slide', $single = true); ?>" alt="<?php the_title() ?>"
title="<span class='tit'><?php the_title(); ?></span> <br /><span class='desc'> <?php truncate_post(150, true); ?></span>" /></a>
<?php endwhile; ?>
</div>
</div>
<div style="clear:both"></div>

