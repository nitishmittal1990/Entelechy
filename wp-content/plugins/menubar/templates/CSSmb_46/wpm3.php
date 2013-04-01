<?php
/* 
	WordPress Menubar Plugin
	PHP script for the CSSmb_46 template

	Credits:
	CSS Menu Builder
	free customizable css menus for everyone...
	http://www.cssmenubuilder.com/home
*/

global $wpm_html_c46;

$wrap_c46 = '
<div class="%menuclass-before"></div>
<div class="%menuclass">
<ul>%items</ul>
</div>
<div class="%menuclass-after"></div>
';

$default_c46 = '<li><a %class href="%url" %attr><span>%name</span></a></li>';
$heading_c46 = '<li><a %class style="cursor:default;" %attr><span>%name</span></a></li>';

$wpm_html_c46 = array
(
'active'	=> 'active',
'nourl'		=> '#',
'items'		=> array
	(
	'Menu'			=>  $wrap_c46,
	'Home'			=>  $default_c46,
	'FrontPage'		=>  $default_c46,
	'Heading'		=>   $heading_c46,
	'Tag'			=>  $default_c46,
	'TagList'		=>   $heading_c46,
	'Category'		=>  $default_c46,
	'CategoryTree'	=>	'',
	'Page'			=>  $default_c46,
	'PageTree'		=>	'',
	'Post'			=>  $default_c46,
//	'SearchBox'		=>  '',
	'External'		=>  $default_c46,
	'Custom'		=>  '',
	),
);

function wpm_display_CSSmb_46 ($menu, $css)
{
	global $wpm_html_c46;

	$r = wpm_out41 ($menu->id, $wpm_html_c46, $css);
	echo $r['output'];
}
?>
