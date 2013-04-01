<?php
register_setting(
	'ninja_pages_options',
	'ninja_pages_options',
	'ninja_pages_options_validate'
);

add_settings_section(
	'ninja_pages_section_pages',
	__( 'Basic Settings', 'ninja-pages' ),
	'ninja_pages_section_cats_desc',
	'ninja_pages_options'
);
function ninja_pages_section_cats_desc() {
	echo '<p>' . __( 'Use these options to enhance your pages.', 'ninja-pages' ) . '</p>';
}

add_settings_field(
	'ninja_pages_add_cats',
	__( 'Page Editing Options', 'ninja-pages' ),
	'ninja_pages_add_cats',
	'ninja_pages_options',
	'ninja_pages_section_pages'
);
function ninja_pages_add_cats() {
	$options = get_option('ninja_pages_options');
	if( isset( $options['add_cats'] ) ) {
		$add_cats = $options['add_cats'];
	} else {
		$add_cats = '';
	}
	if( isset( $options['add_tags'] ) ) {
		$add_tags = $options['add_tags'];
	} else {
		$add_tags = '';
	}
	if( isset( $options['add_excerpts'] ) ) {
		$add_excerpts = $options['add_excerpts'];
	} else {
		$add_excerpts = '';
	} ?>
	<fieldset>
		<input id="ninja_pages_add_cats" name="ninja_pages_options[add_cats]" type="checkbox" value="1" <?php checked( $add_cats ); ?> />
		<label><?php _e( 'Add Categories to Pages.', 'ninja-pages' ); ?></label><br />
		<input id="ninja_pages_add_tags" name="ninja_pages_options[add_tags]" type="checkbox" value="1" <?php checked( $add_tags ); ?> />
		<label><?php _e( 'Add Tags to Pages.', 'ninja-pages' ); ?></label><br />
		<input id="ninja_pages_add_excerpts" name="ninja_pages_options[add_excerpts]" type="checkbox" value="1" <?php checked( $add_excerpts ); ?> />
		<label><?php _e( 'Add Excerpts to Pages.', 'ninja-pages' ); ?></label><br />
	</fieldset>
<?php }

add_settings_field(
	'ninja_pages_add_archives',
	__( 'Page Display Options', 'ninja-pages' ),
	'ninja_pages_add_archives',
	'ninja_pages_options',
	'ninja_pages_section_pages'
);
function ninja_pages_add_archives() {
	$options = get_option('ninja_pages_options');
	if( isset( $options['excerpt_length'] ) ) {
		$excerpt_length = $options['excerpt_length'];
	} else {
		$excerpt_length = '';
	}
	if( isset( $options['more_link'] ) ) {
		$more_link = $options['more_link'];
	} else {
		$more_link = '';
	}
	if( isset( $options['add_archives'] ) ) {
		$add_archives = $options['add_archives'];
	} else {
		$add_archives = '';
	} ?>
	<input id="ninja_pages_excerpt_length" name="ninja_pages_options[excerpt_length]" type="text" size="2" value="<?php echo $excerpt_length; ?>" />
	<label><?php _e( 'Word Length of Excerpt.', 'ninja-pages' ); ?></label><br />
	<input id="ninja_pages_more_link" name="ninja_pages_options[more_link]" type="text" size="20" value="<?php echo $more_link; ?>" />
	<label><?php _e( 'Text for the Read More Link.', 'ninja-pages' ); ?></label><br />
	<input id="ninja_pages_add_archives" name="ninja_pages_options[add_archives]" type="checkbox" value="1" <?php checked( $add_archives ); ?> />
	<label><?php _e( 'Add Pages to Category & Tag Archive Pages.', 'ninja-pages' ); ?></label><br />
<?php }

add_settings_field(
	'ninja_pages_child_options',
	__( 'Child Page Display Options', 'ninja-pages' ),
	'ninja_pages_child_options',
	'ninja_pages_options',
	'ninja_pages_section_pages'
);
function ninja_pages_child_options() {
	$options = get_option('ninja_pages_options');
	if( isset( $options['display_children'] ) ) {
		$widget_cats = $options['display_children'];
	} else {
		$widget_cats = '';
	}
	if( isset( $options['widget_tags'] ) ) {
		$widget_tags = $options['widget_tags'];
	} else {
		$widget_tags = '';
	}
	if( isset( $options['num_children'] ) ) {
		$num_children = $options['num_children'];
	} else {
		$num_children = '';
	}
	if( isset( $options['orderby'] ) ) {
		$orderby = $options['orderby'];
	} else {
		$orderby = '';
	}
	if( isset( $options['order'] ) ) {
		$order = $options['order'];
	} else {
		$order = '';
	}
	?>
	<input id="ninja_pages_display_children" name="ninja_pages_options[display_children]" type="checkbox" value="1" <?php checked( $widget_cats ); ?> />
	<label><?php _e( 'Display immediate children on all Parent Pages. If you do not want this on all pages you can use the [ninja_child_pages] shortcode on any page you do want children to appear.', 'ninja-pages' ); ?></label><br />
	<input id="ninja_pages_number_children" name="ninja_pages_options[num_children]" type="text" size="2" value="<?php echo $num_children; ?>" />
	<label><?php _e( 'Number of Child Pages to display. Use -1 to display all Child Pages.', 'ninja-pages' ); ?></label><br />
	<label><?php _e( 'How would you like to order your Child Pages?', 'ninja-pages' ); ?></label><br />
	<span>by</span> <select name="ninja_pages_options[orderby]">
	  <option value="menu_order" <?php selected( $orderby, 'menu_order' ); ?>>Menu Order</option>
	  <option value="title" <?php selected( $orderby, 'title' ); ?>>Title</option>
	</select><br />
	<span>in</span> <select name="ninja_pages_options[order]">
	  <option value="ASC" <?php selected( $order, 'ASC' ); ?>>Ascending Order</option>
	  <option value="DESC" <?php selected( $order, 'DESC' ); ?>>Descending Order</option>
	</select>
<?php }
/*
add_settings_section(
	'ninja_pages_section_details',
	'Additional Resources',
	'ninja_pages_section_details_info',
	'ninja_pages_details'
);
function ninja_pages_section_details_info() {
	echo '<p>Here is some helpful info.</p>';
	echo '<ul>
			<li>Page Archive Shortcode. Displays direct children of parent page</li>
			<li>Page Archive Filter for when you want the same behaviour for all content. Displays direct children of parent page</li>
			<li>Post Archive Filter for when you want to displays posts that share a category with the current page</li>
			<li>Action Hooks and Filters for all Output</li>
		</ul>';
}
*/