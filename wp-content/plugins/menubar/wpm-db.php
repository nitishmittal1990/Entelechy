<?php

$wpm_type_list = array (
'Home' 			=> 'Home'			. __(': the main page of your blog', 'wpm'),
'FrontPage' 	=> 'FrontPage'		. __(': the front page of your site', 'wpm'),
'Heading' 		=> 'Heading'		. __(': a non clickable item', 'wpm'), 
'Tag' 			=> 'Tag'			. __(': a tag archive', 'wpm'), 
'TagList' 		=> 'TagList'		. __(': the tag archive list', 'wpm'), 
'Category' 		=> 'Category'		. __(': a category archive', 'wpm'), 
'CategoryTree' 	=> 'CategoryTree'	. __(': a category archive, with subcategories', 'wpm'), 
'Page' 			=> 'Page'			. __(': a static page', 'wpm'),
'PageTree' 		=> 'PageTree'		. __(': a static page, with subpages', 'wpm'),
'Post' 			=> 'Post'			. __(': a single post', 'wpm'),
'SearchBox' 	=> 'SearchBox'		. __(': a search box', 'wpm'),
'External' 		=> 'External'		. __(': any static URL', 'wpm'),
'PHP'	 		=> 'PHP'			. __(': any PHP generated label and URL', 'wpm'),
'Custom' 		=> 'Custom'			. __(': your custom HTML', 'wpm'),
);

$wpm_type_fields = array (
'Home' 			=> array ('cssclass', 'attributes'),
'FrontPage' 	=> array ('cssclass', 'attributes'),
'Heading' 		=> array ('cssclass', 'attributes'),
'Tag' 			=> array ('tag', 'cssclass', 'attributes'),
'TagList' 		=> array ('exclude', 'cssclass', 'attributes'),
'Category' 		=> array ('category', 'cssclass', 'attributes'),
'CategoryTree' 	=> array ('category', 'depth', 'exclude', 'headings', 'cssclass', 'attributes'),
'Page' 			=> array ('page', 'cssclass', 'attributes'),
'PageTree' 		=> array ('page', 'depth', 'exclude', 'headings', 'cssclass', 'attributes'),
'Post' 			=> array ('postid', 'cssclass', 'attributes'),
'SearchBox' 	=> array ('button', 'cssclass', 'attributes'),
'External' 		=> array ('url', 'cssclass', 'attributes'),
'PHP'	 		=> array ('phpcode', 'cssclass', 'attributes'),
'Custom' 		=> array ('html', 'cssclass', 'attributes'),
);

$wpm_field_name = array (
'name' 			=> __('Name', 'wpm'), 
'imageurl' 		=> __('Image', 'wpm'), 
'type' 			=> __('Type', 'wpm'), 
'selection' 	=> __('Selection', 'wpm'), 
'cssclass' 		=> __('CSS class', 'wpm'), 
'attributes'	=> __('Attributes', 'wpm'), 
'depth' 		=> __('Depth', 'wpm'), 
'exclude' 		=> __('Exclude', 'wpm'), 
'headings' 		=> __('Headings', 'wpm'), 
'sortby' 		=> __('Sort by', 'wpm'), 
);

$wpm_field_type = array (
'name' 			=> 'string',
'imageurl' 		=> 'string',
'type' 			=> 'string',
'selection' 	=> 'string',
'cssclass' 		=> 'string', 
'attributes'	=> 'string',
'depth' 		=> 'string',
'exclude' 		=> 'array',
'headings' 		=> 'array',
'sortby' 		=> 'string',
);

function wpm_label ($item)
{
	switch ($item->type)
	{
	case 'PHP':
		$out = eval ($item->selection);
		$label = is_array($out)? $out[0]: $item->name;
		if ($label)  return $label;
	}

	return wpm_display_name ($item);
}

function wpm_display_name ($item)
{
	if ($item->name)  return $item->name;	
	$selection = $item->selection;
	
	switch ($item->type)
	{
	case 'Home':			return __('Blog');
	case 'FrontPage':		return __('Start');
	case 'Heading':			return $item->type. $item->id;

	case 'Tag':				return wpm_tag_name ($selection);
	case 'TagList':			return __('Tags');
	case 'Category':		return get_cat_name ($selection);
	case 'CategoryTree':	return $selection? get_cat_name ($selection): __('Categories');
	case 'Page':			return get_the_title ($selection);
	case 'PageTree':		return $selection? get_the_title ($selection): __('Pages');
	
	case 'Post':			return get_the_title ($selection);
	case 'SearchBox':		return '';
	case 'External':		return $item->type. $item->id;
	case 'PHP':				return $item->type. $item->id;
	case 'Custom':			return $item->type. $item->id;
	
	default:				return '';
	}
}

function wpm_url ($item, $nourl)
{
	switch ($item->type)
	{
	case 'Home':
		$sof = get_option ('show_on_front');
		$pfp = get_option ('page_for_posts');

		if ($sof == 'page') 
			$url = $pfp? get_page_link ($pfp): $nourl;
		else
			$url = get_bloginfo ('url', 'display');
		return $url;

	case 'FrontPage':		return get_bloginfo ('url', 'display');

	case 'Tag':				return get_tag_link ($item->selection);
	case 'Category':		return get_category_link ($item->selection);
	case 'Page':			return get_page_link ($item->selection);
	
	case 'Post':			return get_permalink ($item->selection);
	case 'External':		return $item->selection;
	case 'PHP':				$out = eval ($item->selection);
							return is_array($out)? $out[1]: $out;
	}

	return $nourl;
}

function wpm_template ($item, $html, $url)
{
	switch ($item->type)
	{
	case 'Menu':			return $html['items'][$item->type];
	case 'Home':			return $html['items'][$item->type];
	case 'FrontPage':		return $html['items'][$item->type];
	case 'Heading':			return $html['items'][$item->type];

	case 'Tag':				return $html['items'][$item->type];
	case 'TagList':			return '';
	case 'Category':		return $html['items'][$item->type];
	case 'CategoryTree':	return '';
	case 'Page':			return $html['items'][$item->type];
	case 'PageTree':		return '';

	case 'Post':			return $html['items'][$item->type];
	case 'SearchBox':		return $html['items'][$item->type];
	case 'External':		return $html['items'][$item->type];
	case 'PHP':				return $url? $html['items']['External']: '';
	case 'Custom':			return $item->selection;

	default:				return '';
	}
}

function wpm_hilight ($item)
{
	global $wp_query;
	
	switch ($item->type) 
	{
	case 'Home':			
		if (is_home())  return true;
		return false;

	case 'FrontPage':
		$sof = get_option ('show_on_front');
		$pof = get_option ('page_on_front');

		if ($sof == 'page' and $pof)
		{
			if (is_page($pof))  return true;
		}
		else
		{
			if (is_home())  return true;
		}
		return false;

	case 'Tag':
		$q_obj = $wp_query->get_queried_object ();
		if ($wp_query->is_tag 
			&& $q_obj->term_id == $item->selection)  return true;
		return false;

	case 'Category':	
		if (is_category($item->selection) or (is_single() and in_category($item->selection))) 
			return true;
		else foreach ((array) get_term_children ($item->selection, 'category') as $child)
			if (is_category($child) or (is_single() and in_category($child))) 
				return true;
		return false;

	case 'Page':
		if (is_page($item->selection))  return true;
		return false;

	case 'Post':
		if (is_single($item->selection))  return true;
		return false;

	case 'External':
		if (wpm_this_url ($item->selection))  return true;
		return false;

	case 'PHP':
		$out = eval ($item->selection);
		$url = is_array($out)? $out[1]: $out;
		if (wpm_this_url ($url))  return true;
		return false;
	}

	return false;
}

function wpm_display_selection ($item)
{
	$selection = $item->selection;

	switch ($item->type)
	{
	case 'Home':			return array ();
	case 'FrontPage':		return array ();
	case 'Heading':			return array ();

	case 'Tag':				return array ('Tag', $selection);
	case 'TagList':			return array ();
	case 'Category':		return array ('Category', $selection);
	case 'CategoryTree':	return array ('Category', $selection? $selection: 'all');
	case 'Page':			return array ('Page', $selection);
	case 'PageTree':		return array ('Page', $selection? $selection: 'all');

	case 'Post':			return array ('Post ID', $selection);
	case 'SearchBox':		return array ('Button', $selection);
	case 'External':		return array ('URL', $selection);
	case 'PHP':				return array ('PHP code', $selection);
	case 'Custom':			return array ('HTML', htmlspecialchars ($selection));
	
	default:				return array ();
	}
}

function wpm_display_fields ($item)
{
	global $wpm_type_fields, $wpm_field_name, $wpm_field_type;

	foreach ($wpm_type_fields[$item->type] as $field)
	if ($item->$field)
	{
		switch ($wpm_field_type[$field])
		{
		case 'array':
			$value = implode (',', $item->$field);
			break;
		default:
			$value = $item->$field; 	
			break;
		}
		
		$out .= "<strong>$wpm_field_name[$field]</strong> $value ";
	}

	return $out;
}

function wpm_get_tags ()
{
	global $wpdb, $wpm_tags;
	if ($wpm_tags === null)
	{
		$sql = "SELECT t.term_id, t.name, tt.count
			FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt
			ON t.term_id = tt.term_id
			WHERE tt.taxonomy = 'post_tag' ORDER BY t.name ASC";		

		$wpm_tags = $wpdb->get_results ($sql);
	}
	return $wpm_tags;
}

function wpm_tag_name ($id)
{
	$tags = wpm_get_tags ();
	foreach ($tags as $tag)
		if ($tag->term_id == $id)  return $tag->name;
	return null;	
}

function wpm_get_cats ()
{
	global $wpdb, $wpm_cats;
	if ($wpm_cats === null)
	{
		$sql = "SELECT t.term_id, t.name, tt.parent 
			FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt 
			ON t.term_id = tt.term_id
			WHERE tt.taxonomy = 'category' ORDER BY t.name ASC";		

		$wpm_cats = $wpdb->get_results ($sql);
	}
	return $wpm_cats;
}

function wpm_get_pages ()
{
	global $wpdb, $wpm_pages;
	if ($wpm_pages === null)
	{
		$sql = "SELECT ID, post_title, post_parent FROM $wpdb->posts 
			WHERE post_type = 'page' AND (post_status = 'publish' OR post_status = 'private') 
			ORDER BY menu_order, post_title ASC";

		$wpm_pages = $wpdb->get_results ($sql);
	}
	return $wpm_pages;
}

function wpm_this_url ($url)
{
	global $wp_rewrite;

	$protocol = ($_SERVER["HTTPS"] == 'on')? 'https': 'http';

	if ($wp_rewrite->using_permalinks ())
	{
		list ($url) = explode ('?', $url);
		list ($path) = explode ('?', $_SERVER['REQUEST_URI']);
	}
	else
	{
		$path = $_SERVER['REQUEST_URI'];
	}

	if ($url == $protocol. '://'. $_SERVER['SERVER_NAME']. $path)  return true;
	return false;
}
?>
