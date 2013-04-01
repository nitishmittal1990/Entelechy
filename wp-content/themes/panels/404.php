<?php get_header(); ?>
<div class="contentheader"></div>
<div class="contentcontainer">
<div id="pagecontent">

<h1><?php _e('This page can not be found','panels')?></h1>

<p><?php _e('Sorry! The page you are looking for does not currently exist.','panels')?></p>


<p><?php get_search_form(); ?></p>


<p><a href="<?php echo site_url()?>" title="<?php _e('Return to the home page of this website','panels')?>"><?php _e('Return to the home page of this website','panels')?></a></p>


</div>
</div>
<div class="contentfooter"></div>
<?php get_footer(); ?>