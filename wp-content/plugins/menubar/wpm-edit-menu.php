<?php

if ($action == 'editmenu') {
	$heading = __('Edit Menu', 'wpm');
	$submit_text = __('Update Menu', 'wpm');
	$form = '<form name="update" id="update" method="post" action="'. $wpm_options->form_action. '">';
	$action = 'updatemenu';
	$nonce_action = 'updatemenu_' . $wpm_menu->id;
} else {
	$heading = __('Add New Menu', 'wpm');
	$submit_text = __('Add Menu', 'wpm');
	$form = '<form name="add" id="add" method="post" action="'. $wpm_options->form_action. '">';
	$action = 'addmenu';
	$nonce_action = 'addmenu';
}
?>

<div class="wrap">
<h2><?php echo $heading ?></h2>
<div id="ajax-response"></div>
<?php echo $form ?>
<input type="hidden" name="action" value="<?php echo $action; ?>" />
<input type="hidden" name="menuid" value="<?php echo $wpm_menu->id; ?>" />
<?php wp_nonce_field($nonce_action); ?>

<table class="editform" width="100%" cellspacing="2" cellpadding="5">
	<tr>
		<th scope="row" valign="top">	<label for="name"><?php _e('Name:', 'wpm'); ?></label> </th>
		<td> <input name="menuname" id="menuname" type="text" value="<?php echo attribute_escape($wpm_menu->name); ?>" size="10" />
		</td>
	</tr>
	<tr id="select6" >
		<th scope="row" valign="top"> <label for="template">  <?php _e('Template:', 'wpm'); ?></label> </th>
		<td>
		<?php wpm_template_dropdown (wpm_2to1 ($wpm_menu->selection, $wpm_menu->cssclass)); ?>
		<?php _e('(select a menu template and stylesheet)', 'wpm'); ?>
		</td>
	</tr>
</table>

<p class="submit"> <input type="submit" name="submit" value="<?php echo $submit_text ?>" /> </p>

</form>
</div>
