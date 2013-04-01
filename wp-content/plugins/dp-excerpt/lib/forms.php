<?php
/**
 * API for generating administration forms.
 *
 * @package DP Core
 * @subpackage Admin
 * @since DP Core 0.1
 * @author Cloud Stone <cloud@dedepress.com>
 * @copyright Copyright (c) 2010 - 2011, Cloud Stone & dedepress.com
 * @link http://dedepress.com/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Output form table with the given fields
 */
function dp_form_table($fields = array(), $field_args_callback = '') {
	echo '<table class="form-table"><tbody>';
	
	foreach ($fields as $field) {
		if(!is_array($field))
			continue;
		
		$type = !empty($field['type']) ? $field['type'] : '';
		$name = !empty($field['name']) ? $field['name'] : '';
		
		$types = array('text', 'textarea', 'radio', 'select', 'multiselect', 'checkbox', 'checkboxes');
		
		// if callback is set
		if(!empty($field['callback']))
			call_user_func($field['callback'], $field);
			
		// Handle outputs for form elements
		elseif(in_array($type, $types)) {
			if(!empty($field_args_callback) && is_callable($field_args_callback))
				$field = call_user_func($field_args_callback, $field);
				
			if(!empty($field['to_array'])) {
				$to_array = $field['to_array'];
				$field['name'] = "{$to_array}[{$name}]";
				$field['id'] = "$to_array-$name";
			}
			
			dp_form_row($field);
		}
		
		// type = description
		elseif($type == 'description' && !empty($field['value']))
			echo '<tr><td colspan="2"><span class="description">'.$field['value'].'</span></td></tr>';
		
		// type = custom
		elseif($type == 'custom' && !empty($field['value']))
			echo $field['value'];
	}
	echo '</tbody></table>';
}

function dp_form_widget($fields = array(), $field_args_callback = '') {
	
	foreach ($fields as $field) {
		if(!is_array($field))
			continue;
		
		$type = !empty($field['type']) ? $field['type'] : '';
		$name = !empty($field['name']) ? $field['name'] : '';
		
		$types = array('text', 'textarea', 'radio', 'select', 'multiselect', 'checkbox', 'checkboxes');
		
		// if callback is set
		if(!empty($field['callback']))
			call_user_func($field['callback'], $field);
			
		// Handle outputs for form elements
		elseif(in_array($type, $types)) {
			if(!empty($field_args_callback) && is_callable($field_args_callback))
				$field = call_user_func($field_args_callback, $field);
				
			if(!empty($field['to_array'])) {
				$to_array = $field['to_array'];
				$field['name'] = "{$to_array}[{$name}]";
				$field['id'] = "$to_array-$name";
			}
			
			$field['before'] = '<p>';
			$field['after'] = '</p>';
			$field['before_title'] = '';
			$field['after_title'] = '';
			
			dp_form_row($field);
		}
		
		// type = description
		elseif($type == 'description' && !empty($field['value']))
			echo '<tr><td colspan="2"><span class="description">'.$field['value'].'</span></td></tr>';
		
		// type = custom
		elseif($type == 'custom' && !empty($field['value']))
			echo '<tr><td colspan="2">'.$field['value'].'</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Extra output for the form field
 */
function dp_form_row($args = '') {
	$defaults = array(
		'before' => '<tr>',
		'before_title' => '<th scope="row">',
		'title' => '',
		'after_title' => '</th><td>',
		'after' => '</td></tr>',
		'label_for' => '',
		'tip' => '',
		'req' => '',
		'desc' => '',
		'prepend' => '',
		'append' => '',
	);
	
	$args = wp_parse_args( $args, $defaults ); 
	extract($args);
	
	if(empty($label_for) && !empty($id))
		$label_for = ' for="'.sanitize_field_id($id).'"';
	
	echo $before;
	
	/* Title */
	if($args['type'] == 'radio' || $args['type'] == 'checkbox' || $args['type'] == 'checkboxes')
		$title = $args['title'];
	else
		$title = '<label'.$label_for.'>'.$args['title'].'</label> ';
	
	/* Tip */	
	if($tip)
		$tip = ' <span class="tip">(?)</span><div style="display:none;">'.$tip.'</div>';
	
	/* Required */
	$req = '';	
	if($args['req'] === true || $args['req'] === 1)
		$req = '*';
	elseif(isset($args['req']))
		$req = $args['req'];
	if(!empty($req))
		$req = ' <span class="required">'.$req.'</span>';
	
	/* Output */
	echo $before_title . $title . $req . $tip . $after_title;
	
	if(!empty($args['prepend']))
		echo $args['prepend'] . ' ';
	
	dp_form_field($args);
		
	if(!empty($args['append']))
		echo ' '.$args['append'];
		
	if(!empty($desc))
		echo ' <span class="description">'.$desc.'</span>';
		
	echo $after;
}

/**
 * Generate form elements with the given args
 */
function dp_form_field($args = '') {
	if(empty($args['type']))
		return;

	$defaults = array(
		'name' => '',
		'value' => '',
		'class' => '',
		'id' => '',
		'options' => '',
		'sep' => '',
		'label' => '',
		'label_for' => '',
		'style' => ''
	);
	
	if($args['type'] == 'text')
		$defaults['class'] = 'regular-text';
	if($args['type'] == 'textarea')
		$defaults['class'] = 'large-text';
	if($args['type'] == 'multiselect')
		$defaults['style'] = 'height:8em;';
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	if(!empty($class)) 
		$class = ' class="'.$class.'"';
	if(empty($id) && !empty($name))
		$id = $name;
	if(empty($label_for) && !empty($id))
		$label_for = ' for="'.sanitize_field_id($id).'"';
	if(!empty($id))
		$id = ' id="'.sanitize_field_id($id).'"';
	if(!empty($style))
		$style = ' style="'.$style.'"';
	
	$output = false;
	
	/* type = text */
	if($type == 'text') {
		$type = ' type="'.$type.'"';
		if(!empty($value))
			$value = ' value="' . esc_attr(stripslashes($value)) . '"';
		if(!empty($name))
			$name = ' name="'.$name.'"';

		$output = "<input{$type}{$name}{$value}{$id}{$class}{$style}>";
	} 
	
	/* type = textarea */
	elseif($type == 'textarea') {
		$value = esc_attr(stripslashes($value));
		if(empty($cols))
			$cols = '10';
		if(empty($rows))
			$rows = '6';
		$cols = ' cols="' . $cols . '"';
		$rows = ' rows="' . $rows . '"';
			
		if(!empty($name))
			$name = ' name="'.$name.'"';
		
		$output = "<textarea{$name}{$id}{$class}{$style}{$rows}{$cols}>{$value}</textarea>";
	}
	
	/* type = radio */
	elseif($type == 'radio' && is_array($options)) {
	
		foreach ($options as $option => $label) {
			if(!is_assoc($options))
				$option = $label;
				
			$output[] = '<label'.$label_for.'><input name="'.$name.'" type="radio" value="'.$option.'"'.checked($option, $value, false).' />'.$label.'</label>';
		}
	
		$output = implode( ($sep ? $sep : '<br />'), $output);
	}
	
	/* type = select */
	elseif($type == 'select' && is_array($options)) {
		$name = !empty($name) ? 'name="'.$name.'"' : '';
	
		$output .= "<select{$id}{$class}{$name}{$style}>";

		if(is_assoc($options)) {
			foreach ($options as $option => $label) {
				$output .= '<option value="'.$option.'"'.selected($option, $value, false).'>'.$label.'</option>';
			}
		} else {
			foreach ($options as $option => $label) {
				$output .= '<option value="'.$label.'"'.selected($label, $value, false).'>'.$label.'</option>';
			}
		}
		
		$output .= '</select> ';
	}
	
	/* type = multiselect */
	elseif($type == 'multiselect' && is_array($options)) {
		$output .= '<select multiple="multiple" name="'.$name.'[]"' . $id . $class . $style . '>';
		foreach ($options as $option => $label) { 
			if(!is_assoc($options))
				$option = $label;

				$selected = (is_array($value) && in_array($option, $value)) ? ' selected="selected"' : '';
				
			$output .= '<option value="'.$option.'"'.$selected.'>'.$label.'</option>';
		} 
		$output .= '</select>';
	}
	
	/* type = checkbox */
	elseif($type == 'checkbox') {
		$output .= '<label'.$label_for.'><input'.$id.' name="'.$name.'" type="checkbox" value="1"'.checked($value, true, false).' /> '.$args['label'].'</label> ';
	}
	
	/* type = checkboxes */
	elseif($type == 'checkboxes' && is_array($options)) {
		
		foreach ($options as $option => $label) {
	
			if(!is_assoc($options))
				$option = $label;
				
			$checked = (is_array($value) && in_array($option, $value)) ? ' checked="checked"' : '';
				
			$output[] = '<label><input name="'.$name.'[]" type="checkbox" value="'.$option.'"'.$checked.' /> '.$label.'</label>';
		}
		
		$output = implode($args['sep'] ? $args['sep'] : '<br />', $output);
	}
	
	echo $output;
}