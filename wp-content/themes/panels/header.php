<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html <?php language_attributes('xhtml'); ?> xmlns="http://www.w3.org/1999/xhtml">
<head>


<meta http-equiv="content-type" content="<?php bloginfo('html_type')?>;charset=<?php bloginfo('charset'); ?>"/>
	
<title><?php wp_title( '|', true, 'left' ); ?></title>

<link rel="profile" href=" http://gmpg.org/xfn/11" />

<?php wp_head()?>
</head>
<body <?php body_class()?>>

<div id="page">


<div id="header">
<div id="headerimage" style="width:<?php echo HEADER_IMAGE_WIDTH; ?>px; height:<?php echo HEADER_IMAGE_HEIGHT; ?>px;"><a href="<?php echo site_url()?>" title="<?php bloginfo('name')?>"><img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="<?php bloginfo('name')?>"/></a></div>
<div id="headerfooter"><h1><a href="<?php echo site_url()?>" title="<?php bloginfo('name')?>"><?php bloginfo('name')?></a></h1></div>
</div>

<div class="contentheader"></div><div class="contentcontainer"><div id="headermenu">
<?php wp_nav_menu( array( 'theme_location' => 'header-menu', 'depth' => '1','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>') ); ?>
</div></div><div class="contentfooter"></div>
