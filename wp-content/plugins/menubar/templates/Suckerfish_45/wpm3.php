<?php
/* 
	WordPress Menubar Plugin
	PHP script for the Suckerfish_45 template

	Credits:
	Son of Suckerfish Dropdowns
	By Patrick Griffiths and Dan Webb
	http://www.htmldog.com/articles/suckerfish/dropdowns/
*/

global $wpm_html_S45;
global $wpm_features_Suckerfish_45;

$wrap_S45 = '
<script type="text/javascript">
// <![CDATA[
	wpm%id_Hover = function() {
	var wpmEls = document.getElementById("wpm%id").getElementsByTagName("li");
	for (var i=0; i<wpmEls.length; i++) {
		wpmEls[i].onmouseover = function() {
			this.className += " wpmhover";
		}
		wpmEls[i].onmouseout = function() {
			this.className = this.className.replace(new RegExp(" wpmhover\\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", wpm%id_Hover);
// ]]>
</script>

<div class="%menuclass-before"></div>
<div id="wpm%id" class="%menuclass">
%list
</div>
<div class="%menuclass-after"></div>
';

$search_S45 = '
<li%class><form method="get" action="%home">
<input id="wpm%id-text" type="text" name="s" value="%name"
onblur="if (this.value == \'\') {this.value = \'%name\';}"
onfocus="if (this.value == \'%name\') {this.value = \'\';}" />
%submit
</form></li>
';

$default_S45 = '<li%class><a href="%url" %attr>%image%name</a>%list</li>';

$wpm_html_S45 = array
(
'active'	=> 'selected',
'nourl'		=> '#',
'image'		=> '<img src="%imageurl" height="16" width="16" alt="" />',
'list'		=> '<ul>%items</ul>',
'submit' 	=> '<input id="wpm%id-submit" type="submit" value="%selection" />',
'items'		=> array
	(
	'Menu'			=>  $wrap_S45,
	'Home'			=>  $default_S45,
	'FrontPage'		=>  $default_S45,
	'Heading'		=>  '<li%class><a style="cursor:default;" %attr>%image%name</a>%list</li>',
	'Tag'			=>  $default_S45,
	'TagList'		=>  '<li%class><a style="cursor:default;" %attr>%image%name</a>%list</li>',
	'Category'		=>  $default_S45,
	'CategoryTree'	=>	'',
	'Page'			=>  $default_S45,
	'PageTree'		=>	'',
	'Post'			=>  $default_S45,
	'SearchBox'		=>  $search_S45,
	'External'		=>  $default_S45,
	'Custom'		=>  '',
	),
);

$wpm_features_Suckerfish_45 = array ('images' => 'yes');

function wpm_display_Suckerfish_45 ($menu, $css)
{
	global $wpm_html_S45;

	$r = wpm_out41 ($menu->id, $wpm_html_S45, $css);
	echo $r['output'];
}
?>
