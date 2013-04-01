<?php

function wpm_out41 ($node_id, $html, $css)
{
	if ($node_id == 0)  return array ('output' => '', 'hilight' => false);

	wpm_append_nodes ($node_id);
	$item = wpm_readnode ($node_id);

	$itemdown = wpm_out41 ($item->down, $html, $css);

	$process = true;
	$active = $html['active'];
	$home = get_bloginfo ('url', 'display');
	$name = __(wpm_label ($item));
	$url = wpm_url ($item, $html['nourl']);
	$template = wpm_template ($item, $html, $url);
	$attributes = $item->attributes? __($item->attributes): "";
	$selection = $item->selection? __($item->selection): "";
	$menuclass = $css? substr($css, 0, -4): $item->selection;
	$class = $item->cssclass? " class=\"$item->cssclass\"": "";
	$selected = $item->cssclass? " class=\"$item->cssclass $active\"": " class=\"$active\"";
	
	if ($itemdown['hilight'])  $class = $selected;
	else if (wpm_hilight ($item))  $class = $selected;

	$items = $itemdown['output'];

	if ($process)
	{
		$pattern = array ('%attr', '%class', '%home', '%id', '%imageurl', '%items', 
			'%menuclass', '%name', '%selection', '%url',
			'%list', '%submit', '%image');
		$replacement = array ($attributes, $class, $home, $item->id, $item->imageurl, $items, 
			$menuclass, $name, $selection, $url);

		$list = $items? str_replace ($pattern, $replacement, $html['list']): '';
		$submit = $selection? str_replace ($pattern, $replacement, $html['submit']): '';
		$image = $item->imageurl? str_replace ($pattern, $replacement, $html['image']): '';
		
		$replacement[] = $list;
		$replacement[] = $submit;
		$replacement[] = $image;
		
		$output = str_replace ($pattern, $replacement, $template);
	}

	$itemside = wpm_out41 ($item->side, $html, $css);
	$output .= $itemside['output'];
	$hilight = ($class == $selected) || $itemside['hilight'];

	return array ('output' => $output, 'hilight' => $hilight);
}

function wpm_append_nodes ($id)
{
	global $wpm_options;
	
	$wpm_options->update_option = false;

	$item = wpm_readnode ($id);
	switch ($item->type) 
	{
	case 'TagList':
		$item->down = 0;
		_wpm_update_links ($item);
		
		$node->type = 'Heading';
		$node->name = wpm_display_name ($item);
		wpm_update_node ($item->id, $node);

		$tags = wpm_get_tags ();
		foreach ($tags as $tag)
		{
			if (in_array ($tag->term_id, (array)$item->exclude)) continue;
			$node->type = 'Tag';
			$node->name = $tag->name;
			$node->selection = $tag->term_id;
			wpm_create_child ($item->id, $node);
		}
		break;
	
	case 'CategoryTree':
		$item->down = 0;
		_wpm_update_links ($item);

		$node->type = (!$item->selection || 
			in_array ($item->selection, (array)$item->headings))? 'Heading': 'Category';
		$node->name = wpm_display_name ($item);
		wpm_update_node ($item->id, $node);

		$cats = wpm_get_cats ();
		$newparents = array ($item->selection);
		
		while (++$level)
		{
			if ($item->depth > 0 && $level > $item->depth)  break;
			if (count ($newparents) == 0)  break;
			
			$parents = $newparents;
			$newparents = array ();

			foreach ($cats as $cat)
			{
				if (in_array ($cat->term_id, (array)$item->exclude))  continue;
				if (in_array ($cat->parent, $parents))
				{
					$newparents[] = $cat->term_id;
					
					$node->type = in_array ($cat->term_id, (array)$item->headings)? 'Heading': 'Category';
					$node->name = get_cat_name ($cat->term_id);
					$node->selection = $cat->term_id;
					
					$parent = wpm_find_node ($item->id, 'selection', $cat->parent);
					wpm_create_child ($parent->id, $node);
				}
			}
		}
		break;
		
	case 'PageTree':
		$item->down = 0;
		_wpm_update_links ($item);

		$node->type = (!$item->selection || 
			in_array ($item->selection, (array)$item->headings))? 'Heading': 'Page';
		$node->name = wpm_display_name ($item);
		wpm_update_node ($item->id, $node);

		$pages = wpm_get_pages ();
		$newparents = array ($item->selection);

		while (++$level)
		{
			if ($item->depth > 0 && $level > $item->depth)  break;
			if (count ($newparents) == 0)  break;
			
			$parents = $newparents;
			$newparents = array ();

			foreach ($pages as $page)
			{
				if (in_array ($page->ID, (array)$item->exclude))  continue;
				if (in_array ($page->post_parent, $parents))
				{
					$newparents[] = $page->ID;
					
					$node->type = in_array ($page->ID, (array)$item->headings)? 'Heading': 'Page';
					$node->name = get_the_title ($page->ID);
					$node->selection = $page->ID;
					
					$parent = wpm_find_node ($item->id, 'selection', $page->post_parent);
					wpm_create_child ($parent->id, $node);
				}
			}
		}
		break;
	}

	$wpm_options->update_option = true;
}

function wpm_output ($node_id, $html, $css)
{
	if ($node_id == 0)  return array ('output' => '', 'hilite' => false);
	$item = wpm_readnode ($node_id);

	$itemdown = wpm_output ($item->down, $html, $css);

	$name = $item->name? __($item->name): "";
	$attributes = $item->attributes? __($item->attributes): "";
	$selection = $item->selection? __($item->selection): "";
	$class = $item->cssclass? " class=\"$item->cssclass\"": "";
	$selected = $item->cssclass?
			 " class=\"$item->cssclass {$html['active']}\"": " class=\"{$html['active']}\"";

	if ($itemdown['hilite'])  $class = $selected;
	$list = $itemdown['output']? 
			preg_replace ('/%items/', $itemdown['output'], $html['list']): '';

	switch ($item->type) 
	{
	case 'Menu':
		$menuclass = $css? substr($css, 0, -4): $item->selection;

		$out = preg_replace (array ('/%id/', '/%menuclass/', '/%list/'),
					array ($item->id, $menuclass, $list), $html);

		echo $out['script'];
		echo $out['menu'];
		return;

	case 'Home':
		$sof = get_option ('show_on_front');
		$pfp = get_option ('page_for_posts');

		if (is_home()) $class = $selected;
		if ($sof == 'page') 
			$url = $pfp? get_page_link ($pfp): '';
		else
			$url = get_bloginfo ('url', 'display');
		break;

	case 'FrontPage':
		$sof = get_option ('show_on_front');
		$pof = get_option ('page_on_front');

		if ($sof == 'page' and $pof)
		{
			if (is_page($pof)) $class = $selected;
		}
		else
		{
			if (is_home()) $class = $selected;
		}
		$url = get_bloginfo ('url', 'display');
		break;

	case 'Heading':
		$url = '*';
		break;

	case 'Category':	
		if (is_category($item->selection) or (is_single() and in_category($item->selection))) 
			$class = $selected; 
		else foreach ((array) get_term_children ($item->selection, 'category') as $child)
			if (is_category($child) or (is_single() and in_category($child))) 
				$class = $selected;

		$url = get_category_link ($item->selection);
		break;

	case 'CategoryTree':
		if (is_category($item->selection) or (is_single() and in_category($item->selection))) 
			$class = $selected; 
		else foreach ((array) get_term_children ($item->selection, 'category') as $child)
			if (is_category($child) or (is_single() and in_category($child))) 
				$class = $selected;

		$clist = wp_list_categories ('echo=0&title_li=&child_of='.$item->selection); 
		$list = ($clist != '<li>'. __('No categories'). '</li>')?
			preg_replace ('/%items/', $clist, $html['list']): '';

		$url = $item->selection? get_category_link ($item->selection): '*';
		break;

	case 'Page':
		if (is_page($item->selection)) $class = $selected;
		$url = get_page_link ($item->selection);
		break;

	case 'PageTree':
		if (wpm_is_descendant($item->selection)) $class = $selected;
		
		$plist = wp_list_pages ('echo=0&title_li=&child_of='.$item->selection);
		$list = $plist?
			preg_replace ('/%items/', $plist, $html['list']): '';

		$url = $item->selection? get_page_link ($item->selection): '*';
		break;

	case 'Post':
		if (is_single($item->selection)) $class = $selected;
		$url = get_permalink ($item->selection);
		break;

	case 'SearchBox':
		$submit = $selection? 
			preg_replace (array ('/%id/', '/%selection/'), 
					array ($item->id, $selection), $html['submit']): '';
		
		$out = preg_replace (array ('/%id/', '/%class/', '/%home/', '/%name/', '/%submit/'),
					array ($item->id, $class, get_bloginfo ('url', 'display'), $name, $submit), $html);

		$output = $out['search'];
		$url = '';
		break;

	case 'External':
		$url = $item->selection;
		break;

	case 'Custom':
		$output = $item->selection;
		$url = '';
		break;

	default:
		$url = '';
		break;
	}

	if ($url)
	{
		$out = preg_replace (array ('/%class/', '/%url/', '/%attr/', '/%name/', '/%list/'),
					array ($class, $url, $attributes, $name, $list), $html);

		$output = ($url != '*')? $out['item']: $out['noclick'];
	}

	$itemside = wpm_output ($item->side, $html, $css);
	$output .= $itemside['output'];
	$hilite = ($class == $selected) || $itemside['hilite'];

	return array ('output' => $output, 'hilite' => $hilite);
}

function wpm_menu ($node_id, $level, $css, $ul, $li)
{
	if ($node_id == 0)  return array ('output' => '', 'hilite' => false);
	$item = wpm_readnode ($node_id);

	$itemdown = wpm_menu ($item->down, $level, $css, $ul, $li);
	
	$name = $item->name? __($item->name): "";
	$attributes = $item->attributes? __($item->attributes): "";
	$class = $item->cssclass? " class=\"$item->cssclass\"": "";
	$selected = $item->cssclass?
			 " class=\"$item->cssclass active selected\"": " class=\"active selected\"";

	if ($itemdown['hilite'])  $class = $selected;
	if ($itemdown['output'])
		$itemdown['output'] = sprintf ($ul, $itemdown['output']);
 
	switch ($item->type) 
	{
	case 'Menu':
		$mid = 'wpmenu' . $item->id;
		$mclass = $css? substr($css, 0, -4): $item->selection;

		$javascript = '
<script type="text/javascript">
// <![CDATA[
'.$mid.'Hover = function() {
	var wpmEls = document.getElementById("'.$mid.'").getElementsByTagName("li");
	for (var i=0; i<wpmEls.length; i++) {
		wpmEls[i].onmouseover=function() {
			this.className+=" wpmhover";
		}
		wpmEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" wpmhover\\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", '.$mid.'Hover);
// ]]>
</script>
';
		echo "<div class=\"$mclass-before\"></div>\n";
		echo "<div id=\"$mid\" class=\"$mclass\">";
		echo $javascript;
		echo $itemdown['output'];
		echo "</div>\n";
		echo "<div class=\"$mclass-after\"></div>\n";
		return;

	case 'Home':
		$sof = get_option ('show_on_front');
		$pfp = get_option ('page_for_posts');

		if (is_home()) $class = $selected;
		if ($sof == 'page') 
			$url = $pfp? get_page_link ($pfp): '';
		else
			$url = get_bloginfo ('url', 'display');
		break;

	case 'FrontPage':
		$sof = get_option ('show_on_front');
		$pof = get_option ('page_on_front');

		if ($sof == 'page' and $pof)
		{
			if (is_page($pof)) $class = $selected;
		}
		else
		{
			if (is_home()) $class = $selected;
		}
		$url = get_bloginfo ('url', 'display');
		break;

	case 'Heading':
		$url = '*';
		break;

	case 'Category':	
		if (is_category($item->selection) or (is_single() and in_category($item->selection))) 
			$class = $selected; 
		else foreach ((array) get_term_children ($item->selection, 'category') as $child)
			if (is_category($child) or (is_single() and in_category($child))) 
				$class = $selected;

		$url = get_category_link ($item->selection);
		break;

	case 'CategoryTree':
		if (is_category($item->selection) or (is_single() and in_category($item->selection))) 
			$class = $selected; 
		else foreach ((array) get_term_children ($item->selection, 'category') as $child)
			if (is_category($child) or (is_single() and in_category($child))) 
				$class = $selected;

		$url = get_category_link ($item->selection);
		$href = $item->selection? "href=\"$url\"": "style=\"cursor:default;\"";
		
		$list = wp_list_categories ('echo=0&title_li=&child_of='.$item->selection); 
		if ($list != '<li>'. __('No categories'). '</li>')
			$ulist = sprintf ($ul, $list);
 
		$output = sprintf ($li, $class, " $href $attributes", $name, $ulist);

		$url = '';
		break;

	case 'Page':
		if (is_page($item->selection)) $class = $selected;
		$url = get_page_link ($item->selection);
		break;

	case 'PageTree':
		if (wpm_is_descendant($item->selection)) $class = $selected;
		
		$url = get_page_link ($item->selection);
		$href = $item->selection? "href=\"$url\"": "style=\"cursor:default;\"";
		
		$list = wp_list_pages ('echo=0&title_li=&child_of='.$item->selection);
		if ($list != '')
			$ulist = sprintf ($ul, $list);

		$output = sprintf ($li, $class, " $href $attributes", $name, $ulist);
 
		$url = '';
		break;

	case 'Post':
		if (is_single($item->selection)) $class = $selected;
		$url = get_permalink ($item->selection);
		break;

	case 'External':
		$url = $item->selection;
		break;

	default:
		$url = '';
		break;
	}

	if ($url)
	{
		$href = ($url != '*')? "href=\"$url\"": "style=\"cursor:default;\"";

		$output = sprintf ($li, $class, " $href $attributes", $name, $itemdown['output']);
	}

	$itemside = wpm_menu ($item->side, $level, $css, $ul, $li);
	$output .= $itemside['output'];
	$hilite = ($class == $selected) || $itemside['hilite'];

	return array ('output' => $output, 'hilite' => $hilite);
}
?>
