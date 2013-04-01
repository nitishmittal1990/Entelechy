<div id="footer">

<div id="footerleft"> 

<?php wp_nav_menu( array( 'theme_location' => 'left-footer-menu', 'depth' => '3','items_wrap' => '<div class="widgetheader"></div><div class="widgetcontainer"><ul id="%1$s" class="%2$s">%3$s</ul></div><div class="widgetfooter"></div>') ); ?>

<?php if ( ! dynamic_sidebar( 'footer-left' ) ) : ?>


<div class="widgetheader"></div>
<div class="widgetcontainer">
<h2><?php _e('Categories','panels');?></h2>
<ul>
<?php wp_list_categories('title_li=&show_count=0'); ?>
</ul>
</div>
<div class="widgetfooter"></div>


<div class="widgetheader"></div>
<div class="widgetcontainer">
<h2><?php _e('Archives','panels');?></h2>
<ul>
<?php wp_get_archives('type=monthly'); ?>
</ul>  
</div>
<div class="widgetfooter"></div>

<?php endif; // end footer widget area ?>
</div>


<div id="footerright"> 


<?php wp_nav_menu( array( 'theme_location' => 'right-footer-menu', 'depth' => '3','items_wrap' => '<div class="widgetheader"></div><div class="widgetcontainer"><ul id="%1$s" class="%2$s">%3$s</ul></div><div class="widgetfooter"></div>') ); ?>

<?php if ( ! dynamic_sidebar( 'footer-right' ) ) : ?>

<div class="widgetheader"></div>
<div class="widgetcontainer">
<h2><?php _e('Pages','panels');?></h2>
<ul>
<?php wp_list_pages(); ?>
</ul>
</div>
<div class="widgetfooter"></div>


<div class="widgetheader"></div>
<div class="widgetcontainer">
<h2><?php _e('Links','panels');?></h2>
<ul>
<?php wp_list_bookmarks('categorize=0'); ?>
</ul>
</div>
<div class="widgetfooter"></div>

<?php endif; // end footer widget area ?>
</div>





<div id="footertext">
<p>Copyright &copy; <a title="<?php bloginfo('title')?>" href="<?php echo site_url()?>"><?php bloginfo('title')?></a> - <?php printf( __( 'Powered by <a href="http://wordpress.org" title="%1$s">%2$s</a> and <a href="http://www.kettlethemes.co.uk" title="%3$s">%4$s</a>', 'panels' ), esc_attr('WordPress'), esc_attr( 'WordPress'), esc_attr('Kettle Themes'), esc_attr( 'Kettle Themes' )); ?>.</p>
<div id="tagline"><?php bloginfo('description'); ?></div>
</div>






</div>
</div>

<?php wp_footer()?>
</body>
</html>