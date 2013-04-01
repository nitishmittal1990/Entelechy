<?php
/* 
	WordPress Menubar Plugin
	PHP script for the Basic_46 template
*/

global $wpm_html_b46;

$wrap_b46 = '
<div class="%menuclass-before"></div>
<div class="%menuclass">
%items
</div>
<div class="%menuclass-after"></div>
';

$default_b46 = "<a %class href=\"%url\" %attr>%name</a>\n";
$heading_b46 = "<a %class style=\"cursor:default;\" %attr>%name</a>\n";

$wpm_html_b46 = array
(
'active'	=> 'selected',
'nourl'		=> '#',
'items'		=> array
	(
	'Menu'			=>  $wrap_b46,
	'Home'			=>  $default_b46,
	'FrontPage'		=>  $default_b46,
	'Heading'		=>   $heading_b46,
	'Tag'			=>  $default_b46,
	'TagList'		=>   $heading_b46,
	'Category'		=>  $default_b46,
	'CategoryTree'	=>	'',
	'Page'			=>  $default_b46,
	'PageTree'		=>	'',
	'Post'			=>  $default_b46,
//	'SearchBox'		=>  '',
	'External'		=>  $default_b46,
	'Custom'		=>  '',
	),
);

function wpm_display_Basic_46 ($menu, $css)
{
	global $wpm_html_b46;

	$r = wpm_out41 ($menu->id, $wpm_html_b46, $css);
	echo $r['output'];
}
?>
