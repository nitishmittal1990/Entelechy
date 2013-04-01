<?php
/* 
	WordPress Menubar Plugin
	PHP script for the LC1_46 template

	Credits:
	Internet Broadcast template
	By Luka Cvrk
	http://www.solucija.com/
*/

global $wpm_html_l46;

$wrap_l46 = '
<div class="%menuclass-before"></div>
<div class="%menuclass">
<ul>%items</ul>
</div>
<div class="%menuclass-after"></div>
';

$default_l46 = '<li><a %class href="%url" %attr>%name</a></li>';
$heading_l46 = '<li><a %class style="cursor:default;" %attr>%name</a></li>';

$wpm_html_l46 = array
(
'active'	=> 'selected',
'nourl'		=> '#',
'items'		=> array
	(
	'Menu'			=>  $wrap_l46,
	'Home'			=>  $default_l46,
	'FrontPage'		=>  $default_l46,
	'Heading'		=>   $heading_l46,
	'Tag'			=>  $default_l46,
	'TagList'		=>   $heading_l46,
	'Category'		=>  $default_l46,
	'CategoryTree'	=>	'',
	'Page'			=>  $default_l46,
	'PageTree'		=>	'',
	'Post'			=>  $default_l46,
//	'SearchBox'		=>  '',
	'External'		=>  $default_l46,
	'Custom'		=>  '',
	),
);

function wpm_display_LC1_46 ($menu, $css)
{
	global $wpm_html_l46;

	$r = wpm_out41 ($menu->id, $wpm_html_l46, $css);
	echo $r['output'];
}
?>
