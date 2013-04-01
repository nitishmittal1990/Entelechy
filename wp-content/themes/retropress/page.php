<?php 
	global $SMTheme;
	
	get_header();
?>
			
			
			<?php get_template_part('theloop'); ?>
			
			<?php comments_template(); ?>
			
			<?php get_template_part('navigation'); ?>

<?php
	get_footer();
?>