
<script type='text/javascript'>
jQuery(document).ready(function($) {
  $("#reset").submit(function() {
    return confirm("You are about to delete all your menus.\n 'Cancel' to stop, 'OK' to delete.");
  });
});
</script>

<?php

include_once ('wpm-tree.php');

function wpm_get_default_menu ()
{
	$menus = wpm_get_menus ();
	return $menus[0]->id;
}

function wpm_list_menu_items ($menuid)
{
	global $wpdb, $wpm_options, $wpm_error;

	$menu = wpm_read_node ($menuid);
?>

<div class="wrap">
<p>
<?php _e('To display this menu, insert the following line in your theme, e.g. at the end of <em>header.php</em>:', 'wpm'); ?>
<br />
<strong><?php echo "&lt;?php do_action('wp_menubar','$menu->name'); ?&gt;"; ?></strong>
</p>
</div>

<div class="wrap">

<h2><?php printf(__('Menu Items of: %s', 'wpm'), $menu->name); ?></h2>

<table class="widefat">

	<thead>
	<tr>
      <th colspan="2" style="text-align: center;"><?php _e('Order', 'wpm') ?></th>
	  <th scope="col"><?php _e('Name', 'wpm') ?></th>
	  <th scope="col"><?php _e('Type', 'wpm') ?></th>
      <th scope="col"><?php _e('Fields', 'wpm') ?></th>
      <th colspan="2" style="text-align: center;"><?php _e('Action', 'wpm') ?></th>
	</tr>
	</thead>
	
	<tbody id="the-list">
	
<?php if ($menu->down)  wpm_print_tree ($menu->id, $menu->down, 0, 0, ''); ?>
	
	</tbody>
</table>

<?php if ($wpm_error == 'ghostchildren')
	_e('<strong style="color:red;">* Children items of CategoryTree, PageTree and TagList types are ignored</strong>', 'wpm');
?>

</div>

<?php		
}

function wpm_print_tree ($menuid, $item_id, $prev_id, $level, $class)
{
	global $wpm_options, $wpm_error;

	$item = wpm_read_node ($item_id);
	$next_id = $item->side;

	$menu = wpm_read_node ($menuid);
	
	$class = ($class == "") ? "alternate" : "";

	$url = $wpm_options->menubar_url;

	$url_up		= $wpm_options->form_action . 
		'&amp;action=swap&amp;menuid=' . $menuid . '&amp;itemid=' . $prev_id;

	$url_down 	= $wpm_options->form_action . 
		'&amp;action=swap&amp;menuid=' . $menuid . '&amp;itemid=' . $item->id;

	$url_edit	= $wpm_options->form_action . 
		'&amp;action=edit&amp;menuid=' . $menuid . '&amp;itemid=' . $item->id;
 
	$url_delete	= $wpm_options->form_action . 
		'&amp;action=delete&amp;menuid=' . $menuid . '&amp;itemid=' . $item->id; 

	$up   = $prev_id? "<a href='$url_up' class='edit' title='".__('move up','wpm')."'>
				<img src='$url/up.gif' /></a>": "";
				
	$down = $next_id? "<a href='$url_down' class='edit' title='".__('move down','wpm')."'>
				<img src='$url/down.gif' /></a>": "";

	$image = ($menu->features['images'] == true && $item->imageurl)? 
				"<img src=\"$item->imageurl\" height=\"16\" width=\"16\" />": '';
	
	$edit = "<a href='$url_edit' class='edit'>" . __('Edit', 'wpm') . "</a>";
	
	$delete = "<a href='" . wp_nonce_url ($url_delete, 'delete_' . $item->id) . 
		"' class='delete'>" . __('Delete', 'wpm') . "</a>";

	$name = wpm_display_name ($item);
	if ($item->down && in_array ($item->type, array ('CategoryTree', 'PageTree', 'TagList')))
	{
		$name = "<strong style=\"color:red;\">$name *</strong>";
		$wpm_error = 'ghostchildren';
	}

	echo "<tr class=\"$class\">
		<td align='center'>$up</td>
		<td align='center'>$down</td>
		<td>" . str_repeat("&#8212; ", $level) . "$image $name</td>
		<td>$item->type</td>
		<td>";

	$sel = wpm_display_selection ($item);
	if ($sel[1])  echo "<strong>{$sel[0]}</strong> {$sel[1]} ";

	echo wpm_display_fields ($item);
	
	echo "</td>
		<td align='center'>$edit</td>
		<td align='center'>$delete</td>
		</tr>\n";
		
	if ($item->down)  $class = wpm_print_tree ($menuid, $item->down, 0, $level+1, $class);
	if ($item->side)  $class = wpm_print_tree ($menuid, $item->side, $item_id, $level, $class);
		
	return $class;
}

function wpm_menu_dropdown ($menuid)
{
	$menus = wpm_get_menus ();

	$out = "<select name='menuid' style='width: 10em;' >\n";

	foreach ($menus as $menu) :
		$selected = ($menu->id == $menuid)? 'selected' : ''; 
		$out .= "<option value='$menu->id' $selected> $menu->name </option>\n";
	endforeach;

	$out .= "</select>\n";
	echo $out;

	return true;
}

function wpm_template_dropdown ($active_template, $echo=true)
{
	global $wpm_options;

	$templates = array();
	$root = $wpm_options->templates_dir;

	$folders = @ dir ($root);
	if ($folders)
	{
		while (($folder = $folders->read()) !== false)
		{
			if (substr ($folder, 0, 1) == '.')  continue;
			if (is_dir ("$root/$folder"))
			{
				$found = 0;
				$cfiles = array();

				$files = @ dir ("$root/$folder");
				if ($files)
				{
					while (($file = $files->read()) !== false)
					{
						if (substr ($file, 0, 1) == '.')  continue;
						elseif ($file == $wpm_options->php_file)  $found = 1;
						elseif (substr ($file, -4) == '.css')  $cfiles[] = $file;
					}
				}
				
				if ($found) 
				{
					$templates[] = wpm_2to1 ($folder, '');
					foreach ($cfiles as $cfile)
						$templates[] = wpm_2to1 ($folder, $cfile);
				}
			}
		}
	}

	if (count ($templates) == 0)  return false;

	sort ($templates);

	$out = "<select name='template' >\n";

	foreach ($templates as $template) :
		$selected = ($template == $active_template)? 'selected' : ''; 
		$out .= "<option value='" . $template . "' $selected> $template </option>\n";
	endforeach;

	$out .= "</select>\n";
	if ($echo)  echo $out;

	return true;
}

function wpm_2to1 ($folder, $cfile)
{
	if ($cfile)  return "$folder " . __('with','wpm') . " $cfile";
	return "$folder " . __('without','wpm') . " CSS";
}

function wpm_1to2 ($template)
{
	$list = array();
	
	$pieces = explode (" ", $template);
	$list[0] = $pieces[0];
	$list[1] = array_pop ($pieces);
	if ($list[1] == 'CSS') $list[1] = '';

	return $list;
}

function wpm_check_templates ()
{
	global $wpm_options;

	$root = $wpm_options->templates_dir;
	
	if (!file_exists ("$root"))  return 1;
	
	$tpfound = wpm_template_dropdown ('', false);
	if (!$tpfound)  return 2;

	return 0;
}

function wpm_get_vars ($vars)
{
	foreach ($vars as $var)
	{
		global $$var;

		if (empty ($_POST["$var"]))
			$$var = empty ($_GET["$var"])? '': $_GET["$var"];
		else
			$$var = $_POST["$var"];
		
		$$var = stripslashes_deep ($$var);
		unset ($_POST["$var"]);
	}
}

function wpm_get_fields ()
{
	$exclude = array ('_wp_http_referer', '_wpnonce'); 
	
	foreach ($_POST as $key => $value)
	{
		if (in_array ($key, $exclude))  continue;
		$fields->$key = stripslashes_deep ($value);
	}

	switch ($fields->type)
	{
	case 'Home':
	case 'FrontPage':
	case 'Heading':
		$fields->selection = '';
		break;
	case 'TagList':
		$fields->selection = '';
		if (empty ($fields->exclude))  $fields->exclude = array();
		break;
	case 'PageTree':
	case 'CategoryTree':
		if (empty ($fields->exclude))  $fields->exclude = array();
		if (empty ($fields->headings))  $fields->headings = array();
		break;
	}
	
	return $fields;
}

function wpm_check_item ($order, $orderid, $fields)
{
}

wpm_get_vars (array ('submit', 'action', 'itemid', 'order', 'orderid', 
					 'menuid', 'menuname', 'template'));

$msg = 0;

switch ($submit)
{
case __('Reset Menubar', 'wpm'):  

	wpm_drop_tree ();
	wpm_create_tree ();
	$msg = 6; 

break;
case __('Select Menu', 'wpm'):  

break;
case __('Delete Menu', 'wpm'):  

	if (wpm_delete_node ($menuid))
	{
		$menuid = wpm_get_default_menu ();
		$msg = 7; 
	}
	else
		$msg = 8; 

break;
case __('Edit Menu', 'wpm'):  

	$action = 'editmenu';
	$wpm_menu = wpm_read_node ($menuid);
	include ('wpm-edit-menu.php');
	include ('admin-footer.php');
	exit;

break;
case __('Update Menu', 'wpm'):  

	check_admin_referer ('updatemenu_' . $menuid);

	$wpm_menu = new stdClass;
	$wpm_menu->name = $menuname;
	$list = wpm_1to2 ($template);
	$wpm_menu->selection = $list[0];
	$wpm_menu->cssclass = $list[1];

	wpm_include ($wpm_menu->selection, '');
	$features = 'wpm_features_' . $wpm_menu->selection;
	
	global $$features;
	$wpm_menu->features = $$features;
	
	if (wpm_update_node ($menuid, $wpm_menu))
		$msg = 9; 
	else
		$msg = 10; 

break;
case __('Add New Menu', 'wpm'):  

	$wpm_menu = null;
	include ('wpm-edit-menu.php');
	include ('admin-footer.php');
	exit;

break;
case __('Add Menu', 'wpm'):  

	check_admin_referer ('addmenu');

	$wpm_menu = new stdClass;
	$wpm_menu->name = $menuname;
	$wpm_menu->type = $wpm_options->menu_type;
	$list = wpm_1to2 ($template);
	$wpm_menu->selection = $list[0];
	$wpm_menu->cssclass = $list[1];

	wpm_include ($wpm_menu->selection, '');
	$features = 'wpm_features_' . $wpm_menu->selection;
	
	global $$features;
	$wpm_menu->features = $$features;
	
	$wpm_menu = wpm_create_child (0, $wpm_menu);
	
	if ($menuid = $wpm_menu->id)
		$msg = 11; 
	else
		$msg = 12; 

break;
}

switch ($action)
{
case 'swap':

	wpm_swap_node ($itemid);

break;
case 'add':

	check_admin_referer ('add');

	$wpm_item = wpm_get_fields ();	
	if ($wpm_item->selection == null)
	{
		if ($wpm_item->type == 'Page')		$wpm_item->type = 'PageTree';
		if ($wpm_item->type == 'Category')	$wpm_item->type = 'CategoryTree';
		if ($wpm_item->type == 'Tag')		break;
	}

	if ($order == 0)
		$created = wpm_create_child ($menuid, $wpm_item);
	else switch ($order)
	{
	case '1':  $created = wpm_create_before ($orderid, $wpm_item); break;
	case '2':  $created = wpm_create_child ($orderid, $wpm_item); break;
	case '3':  $created = wpm_create_after ($orderid, $wpm_item); break;
	default:   $created = 0; break;
	}

	if ($created)
		$msg = 1; 
	else
		$msg = 4; 

break;
case 'delete':

	check_admin_referer ('delete_' . $itemid);

	if (wpm_delete_node ($itemid))
		$msg = 2; 
	else
		$msg = 14;
		
break;
case 'edit':
	
	$item = wpm_read_node ($itemid);
	include_once ('wpm-edit.php');
	wpm_item_form ('edit', $menuid, $item);
	include ('admin-footer.php');
	exit;

break;
case 'update':

	check_admin_referer ('update_' . $itemid);

	$wpm_item = wpm_get_fields ();
	if ($wpm_item->selection == null)
	{
		if ($wpm_item->type == 'Page')		$wpm_item->type = 'PageTree';
		if ($wpm_item->type == 'Category')	$wpm_item->type = 'CategoryTree';
		if ($wpm_item->type == 'Tag')		break;
	}

	switch ($order)
	{
	case '1':  wpm_move_before ($orderid, $itemid); break;
	case '2':  wpm_move_child ($orderid, $itemid); break;
	case '3':  wpm_move_after ($orderid, $itemid); break;
	}

	$updated = wpm_update_node ($itemid, $wpm_item);
	
	if ($updated)
		$msg = 3; 
	else
		$msg = 5; 

break;
}

$messages[1] = __('Menu item added.', 'wpm');
$messages[2] = __('Menu item deleted.', 'wpm');
$messages[3] = __('Menu item updated.', 'wpm');
$messages[4] = __('Menu item not added.', 'wpm');
$messages[5] = __('Menu item not updated.', 'wpm');
$messages[6] = __('Menubar cleared.', 'wpm');
$messages[7] = __('Menu deleted.', 'wpm');
$messages[8] = __('Error: menu is not empty!', 'wpm');
$messages[9] = __('Menu updated.', 'wpm');
$messages[10] = __('Menu not updated.', 'wpm');
$messages[11] = __('Menu added.', 'wpm');
$messages[12] = __('Error: duplicate or null menu name!', 'wpm');
$messages[13] = __('Please add your first menu.', 'wpm');
$messages[14] = __('Error: item has sub-items!', 'wpm');
$messages[15] = __('Menubar templates folder <em>wp-content/plugins/'. MENUBAR_TEMPLATES. '</em> not found.', 'wpm');
$messages[16] = __('No Menubar templates found in <em>wp-content/plugins/'. MENUBAR_TEMPLATES. '</em>.', 'wpm');

if (!$menuid)  $menuid = wpm_get_default_menu ();
if (!$menuid)  $msg = 13;

$missingtp = wpm_check_templates (); 
if ($missingtp)  $msg = $missingtp + 14;

?>

<div class="wrap">
<div id="icon-plugins" class="icon32">
<br/>
</div>
<h2><?php echo "Menubar $wpm_options->wpm_version"; ?></h2>
<br/>

<?php if ($msg) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$msg]; ?></p></div>
<?php endif; ?>

<?php if (!$missingtp) { ?>

<form name="viewmenu" id="viewmenu" method="post" action="<?php echo $wpm_options->form_action; ?>">
	<fieldset>
	
	<?php if ($menuid) {
		wpm_menu_dropdown ($menuid); ?>
		<input type="submit" name="submit" value="<?php _e('Select Menu', 'wpm'); ?>" class="button" /> 
		<input type="submit" name="submit" value="<?php _e('Edit Menu', 'wpm'); ?>" class="button" /> 
		<input type="submit" name="submit" value="<?php _e('Delete Menu', 'wpm'); ?>" class="button delete" />
	<?php } ?>
	
	<input type="submit" name="submit" value="<?php _e('Add New Menu', 'wpm'); ?>" class="button" /> 
	</fieldset>
</form>

<?php } ?>

</div>

<?php

	if (!$missingtp)
	if ($menuid)
	{
		wpm_list_menu_items ($menuid); 

		include_once ('wpm-edit.php');
		wpm_item_form ('create', $menuid);
	}

	$heading = __('Reset Menubar', 'wpm');
	$form = '<form name="reset" id="reset" method="post" action="'. $wpm_options->form_action. '">';

?>

<hr />
<div class="wrap">
<h2><?php echo $heading; ?></h2>

<?php echo $form; ?>
<p class="submit">
<input type="submit" name="submit" value="<?php _e('Reset Menubar', 'wpm'); ?>"  />
<strong>
<?php _e('Clean up the Menubar data', 'wpm'); ?>
</strong>
</p>
</form>

</div>
<hr />
