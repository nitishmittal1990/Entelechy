<?php

function wpm_init_tree ()
{
	global $wpm_tree, $wpm_options;

	$wpm_tree = get_option ($wpm_options->option_name);
	if ($wpm_tree == false)
	{
		wpm_create_tree ();
		if (wpm_table_exists())  wpm_move_tree ();
	}
	if ($wpm_tree->version == '1.0')
	{
		wpm_update_1to2 ();
		$wpm_tree->version = '2.0';
		update_option ($wpm_options->option_name, $wpm_tree);
	}

	return true;
}

function wpm_readnode ($node_id)
{
	global $wpm_tree;
	
	$node = $wpm_tree->nodes[$node_id];
	return $node;
}

function wpm_create_tree ()
{
	global $wpm_tree, $wpm_options;

	$wpm_tree->version = '1.0';
	$wpm_tree->ffree = 1;
	$wpm_tree->nodes = array ();
	
	add_option ($wpm_options->option_name, $wpm_tree);

	return true;
}

function wpm_drop_tree ()
{
	global $wpm_tree, $wpm_options;

	delete_option ($wpm_options->option_name);
	$wpm_tree = null;
	
	return true;
}

function wpm_create_child ($parent_id, $node_values)
{
	$node = _wpm_create_node ($node_values);

	if ($parent_id > 0)	
		$node = _wpm_link_child ($parent_id, $node);
	
	return $node;
}

function wpm_move_child ($parent_id, $node_id)
{
	if (_wpm_is_descendant ($parent_id, $node_id))  return false;

	$node = _wpm_unlink_node ($node_id);

	if ($parent_id > 0)
		$node = _wpm_link_child ($parent_id, $node);

	return $node;
}

function wpm_create_after ($after_id, $node_values)
{
	$node = _wpm_create_node ($node_values);
	$node = _wpm_link_after ($after_id, $node);

	return $node;
}

function wpm_move_after ($after_id, $node_id)
{
	if (_wpm_is_descendant ($after_id, $node_id))  return false;

	$node = _wpm_unlink_node ($node_id);
	$node = _wpm_link_after ($after_id, $node);

	return $node;
}

function wpm_create_before ($before_id, $node_values)
{
	$node = _wpm_create_node ($node_values);
	$node = _wpm_link_before ($before_id, $node);
	
	return $node;
}

function wpm_move_before ($before_id, $node_id)
{
	if (_wpm_is_descendant ($before_id, $node_id))  return false;

	$node = _wpm_unlink_node ($node_id);
	$node = _wpm_link_before ($before_id, $node);

	return $node;
}

function wpm_read_node ($node_id)
{
	$node = wpm_readnode ($node_id);
	return $node;
}

function wpm_update_node ($node_id, $node_values)
{
	global $wpm_tree, $wpm_options;
	
	foreach (get_object_vars ($node_values) as $key => $value)
	{
		if (in_array ($key, array ('id', 'side', 'down')))  continue; 
		$wpm_tree->nodes[$node_id]->$key = $value;
	}

	if ($wpm_options->update_option)
		update_option ($wpm_options->option_name, $wpm_tree);

	return true;
}

function wpm_delete_node ($node_id, $safe=true)
{
	global $wpm_tree, $wpm_options;

	$node = wpm_read_node ($node_id);	
	if ($safe && $node->down)  return false;
	
	_wpm_unlink_node ($node_id);
	
	$wpm_tree->nodes[$node_id] = null;
	
	if ($wpm_options->update_option)
		update_option ($wpm_options->option_name, $wpm_tree);
	
	return true;
}

function wpm_find_node ($node_id, $field, $value)
{
	if ($node_id == 0)  return null;

	$node = wpm_read_node ($node_id);	
	if ($node->$field == $value)  return $node;
	
	if ($down = wpm_find_node ($node->down, $field, $value))  return $down;
	if ($side = wpm_find_node ($node->side, $field, $value))  return $side;
	
	return null;		
}

function wpm_swap_node ($node_id)
{
	$node = wpm_read_node ($node_id);
	if ($node->side == 0)  return false;
	
	$node = _wpm_unlink_node ($node_id);
	$node = _wpm_link_after ($node->side, $node);
	
	return true;
}

function _wpm_create_node ($node_values)
{
	global $wpm_tree, $wpm_options;

	$id = $wpm_tree->ffree++;
	
	foreach (get_object_vars ($node_values) as $key => $value)
		$wpm_tree->nodes[$id]->$key = $value;

	$wpm_tree->nodes[$id]->id = $id;
	$wpm_tree->nodes[$id]->side = 0;
	$wpm_tree->nodes[$id]->down = 0;
	
	if ($wpm_options->update_option)
		update_option ($wpm_options->option_name, $wpm_tree);

	$node = wpm_read_node ($id);
	return $node;
}

function _wpm_update_links ($node)
{
	global $wpm_tree, $wpm_options;

	$wpm_tree->nodes[$node->id]->side = $node->side;
	$wpm_tree->nodes[$node->id]->down = $node->down;
	
	if ($wpm_options->update_option)
		update_option ($wpm_options->option_name, $wpm_tree);

	return $node;
}

function _wpm_find_pointer ($node_id)
{
	global $wpm_tree;

	foreach ($wpm_tree->nodes as $item)
		if ($item->side == $node_id || $item->down == $node_id)  return $item;  
	
	return null;		
}

function _wpm_unlink_node ($node_id)
{
	$node = wpm_read_node ($node_id);
	$item = _wpm_find_pointer ($node->id);

	if ($item->side == $node->id)  $item->side = $node->side;  else
	if ($item->down == $node->id)  $item->down = $node->side;

	_wpm_update_links ($item);
	return $node;
}

function _wpm_link_child ($parent_id, $node)
{
	$parent = wpm_read_node ($parent_id);
	
	if ($parent->down == 0) 
	{
		$parent->down = $node->id;
		_wpm_update_links ($parent);
	}
	else
	{
		$item = wpm_read_node ($parent->down); 
		while ($item->side)
			$item = wpm_read_node ($item->side);

		$item->side = $node->id;
		_wpm_update_links ($item);
	}

	$node->side = 0;
	$node = _wpm_update_links ($node);
	
	return $node;
}

function _wpm_link_after ($after_id, $node)
{
	$after = wpm_read_node ($after_id);

	$node->side = $after->side;
	$node = _wpm_update_links ($node);
	
	$after->side = $node->id;
	_wpm_update_links ($after);

	return $node;
}

function _wpm_link_before ($before_id, $node)
{
	$item = _wpm_find_pointer ($before_id);

	if ($item->side == $before_id)  $item->side = $node->id;  else
	if ($item->down == $before_id)  $item->down = $node->id;
	
	_wpm_update_links ($item);

	$node->side = $before_id;
	$node = _wpm_update_links ($node);

	return $node;
}

function _wpm_is_descendant ($node_id, $parent_id, $level=0)
{
	if ($node_id == $parent_id)  return true;
	
	$item = wpm_read_node ($parent_id);
			
	if ($item->down)  
		if (_wpm_is_descendant ($node_id, $item->down, $level+1))  return true;

	if ($level && $item->side)  
		if (_wpm_is_descendant ($node_id, $item->side, $level))  return true;

	return false;
}

function wpm_dump_tree ()
{
	global $wpm_tree;

	printf ("<br>\n"); 
	foreach ((array)$wpm_tree->nodes as $n)
		if ($n->id)  printf ("%d %s %d %d<br>\n", $n->id, $n->name, $n->side, $n->down); 
}

function _wpm_cmpid ($a, $b)
{
	if ($a->id == $b->id)  return 0;
	return ($a->id < $b->id) ? -1 : 1;
}

function wpm_get_menus ()
{
	global $wpm_tree, $wpm_options;

	$menus = array ();
	foreach ((array)$wpm_tree->nodes as $item)
		if ($item->type == $wpm_options->menu_type)  $menus[] = $item;

	usort ($menus, '_wpm_cmpid');

	return $menus;
}

function wpm_get_menu ($menuname)
{
	global $wpm_tree, $wpm_options;

	foreach ((array)$wpm_tree->nodes as $item)
		if ($item->name == $menuname && $item->type == $wpm_options->menu_type)  return $item;

	return null;
}

function wpm_get_templates ()
{
	global $wpm_tree, $wpm_options;

	$menus = array ();
	$stored = array ();
	foreach ((array)$wpm_tree->nodes as $item)
		if ($item->type == $wpm_options->menu_type)
		{
			$template = $item->selection . $item->cssclass;
			if (!in_array ($template, $stored))
			{
				$menus[] = $item;
				$stored[] = $template;
			}
		}
	
	return $menus;
}

function wpm_move_tree ()
{
	$menus = wpm_old_get_menus ();
	foreach ($menus as $menu)
		wpm_move_item ($menu->id, 0);
}

function wpm_move_item ($old_item_id, $new_parent_id)
{
	if ($old_item_id == 0)  return false;
	$old_item = wpm_old_read_node ($old_item_id);

	$new_item = wpm_create_child ($new_parent_id, unserialize(serialize($old_item)));
	
	wpm_move_item ($old_item->down, $new_item->id);
	wpm_move_item ($old_item->side, $new_parent_id);

	return true;
}

function wpm_table_exists ()
{
	global $wpdb, $wpm_options;
	$table_name = $wpdb->prefix . $wpm_options->table_name;

	$sql = "SHOW TABLES LIKE '$table_name'";
	$tables = $wpdb->get_results ($sql);
	$count = count ($tables);
		
	return $count? true: false;
}

function wpm_old_get_menus ()
{
	global $wpdb, $wpm_options;
	$table_name = $wpdb->prefix . $wpm_options->table_name;

	$sql = "SELECT id, name FROM $table_name
		WHERE type = '$wpm_options->menu_type'";
		
	$menus = $wpdb->get_results ($sql);
	return $menus;
}

function wpm_old_read_node ($node_id)
{
	global $wpdb, $wpm_options;
	$table_name = $wpdb->prefix . $wpm_options->table_name;

	$sql = "SELECT * FROM $table_name WHERE id = '$node_id'";
	$node = $wpdb->get_row ($sql);

	return $node;
}

function wpm_update_1to2 ()
{
	global $wpm_tree;

	foreach ($wpm_tree->nodes as $item)
	{
		if ($item->type == 'CategoryTree' && empty ($item->selection))  $item->selection = 0;
		elseif ($item->type == 'PageTree' && empty ($item->selection))  $item->selection = 0;
	}
	
	return true;		
}

?>
