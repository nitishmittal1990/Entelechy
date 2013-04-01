<?php
/*
	WordPress Menubar Plugin
	PHP script for the inove template

	Credits:
	iNove theme 1.4.6 by mg12 (http://www.neoease.com/)
	http://wordpress.org/extend/themes/inove
*/

global $wpm_html_inove;

$wrap_inove = '
<ul id="menus">
%items
<li><a class="lastmenu" href="javascript:void(0);"></a></li>
</ul>
';

$default_inove = '<li%class><a href="%url" %attr>%name</a>%list</li>';

$wpm_html_inove = array
(
'active'	=> 'current_page_item',
'nourl'		=> 'javascript:void(0);',
'list'		=> '<ul>%items</ul>',
'items'		=> array
	(
	'Menu'			=>  $wrap_inove,
	'Home'			=>  $default_inove,
	'FrontPage'		=>  '<li%class><a class="home" href="%url" %attr>%name</a>%list</li>',
	'Heading'		=>  '<li%class><a style="cursor:default;" %attr>%name</a>%list</li>',
	'Tag'			=>  $default_inove,
	'TagList'		=>  '<li%class><a style="cursor:default;" %attr>%name</a>%list</li>',
	'Category'		=>  $default_inove,
	'CategoryTree'	=>	'',
	'Page'			=>  $default_inove,
	'PageTree'		=>	'',
	'Post'			=>  $default_inove,
//	'SearchBox'		=>  '',
	'External'		=>  $default_inove,
	'Custom'		=>  '',
	),
);

function wpm_display_inove ($menu, $css)
{
	global $wpm_html_inove;

	$r = wpm_out41 ($menu->id, $wpm_html_inove, $css);
	echo $r['output'];
}
?>
