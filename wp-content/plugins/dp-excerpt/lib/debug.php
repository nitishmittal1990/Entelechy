<?php
/*
These functions for developer quickly.
*/

add_action('dp_prepend_body', 'dp_debugging');
function dp_debugging() {
	$name = 'name[name1][name2]';
	$value = array(
		'name1' => array(
			'name2' => 'Google'
		)
	);
	
	echo $value['name1']['name2'];
	
	$name = str_replace(']', '', $name);
	$name = str_replace('[', ',', $name);
	$name = explode(',', $name);
	pre_print_r($name);
	echo $value[$name[1]][$name[2]];
	
	global $dp, $wp_query;
	global $_wp_additional_image_sizes;
	$term = $wp_query->get_queried_object();
	$r = $_wp_additional_image_sizes;
	
	$r = dp_timthumb_url(array('src' => 'image.gif', 'w' => '100'));
	
	pre_print_r($r);

	$args = array(
	/*
	'tax_query' => array(
		'relation' => 'OR',
		array(
			'taxonomy' => 'post_tag',
			'field' => 'slug',
			'terms' => array('1-column', '2-columns'),
			'operator' => 'AND'
		),
		array(
			'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => array('hosting')
		)
	),*/
	'posts_per_page' => 5
	
);
query_posts( $args );

	while(have_posts()) {
		the_post();
		the_title();
		echo '<br / >';
	}
	wp_reset_query();
}



Class DP_Debug {
	/**
	 * PHP4 constructor method.  This simply provides backwards compatibility for users with setups
	 * on older versions of PHP.  Once WordPress no longer supports PHP4, this method will be removed.
	 *
	 * @since 0.9.0
	 */
	function DP_Debug() {
		$this->__construct();
	}
	
	/**
	 * Constructor method for the DP class.  This method adds other methods of the class to 
	 * specific hooks within WordPress.  It controls the load order of the required files for running 
	 * the framework.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		add_action('dp_append_body', array(&$this, 'current_template_file'), 9999);
		
		// add_action('admin_footer', array(&$this, 'current_template_file'), 10);
		
		// add_action('deprecated_function_run', array(&$this, 'deprecated_function_run') );
		
		// wp_enqueue_style('dp-debug', trailingslashit(REMIX_DEBUG_URL) . 'debug.css');
		// wp_enqueue_script('dp-debug', trailingslashit(REMIX_DEBUG_URL) . 'debug.js', array('jquery'), '', true);
	}
	
	function deprecated_function_run($function, $replacement, $version) {
		global $dp;
		$dp->depracated[] = '123';
		
		echo $dp;
	}
	
	
		
	function _pre_print_r($r, $clean = true) {
		$r =  print_r($r, 1);
		if($clean)
			$r =  esc_html($r);
			
		return '<pre>'.$r.'</pre>';
	}
	
	function current_template_file() {
		global $wpdb;
	
		global $dp, $dp_actions;
		global $wp_query, $post, $wp_actions, $_wp_registered_nav_menus;
		
		global $wp_filter, $wp_actions, $merged_filters, $wp_current_filter;
		
		$all_options = wp_load_alloptions();


		
		echo '<div id="debug"><div id="debug-header"><span id="debug-toggle">Toggle</span>DP Debug</div> <div id="debug-content">';
		
		
		// echo $this->_pre_print_r($_SERVER);
		// echo basename( dirname( __FILE__ ) );
		
		// echo $this->_pre_print_r($dp);
		// echo $this->_pre_print_r($wp_actions);
		
		global $wp_post_types, $wp_taxonomies;
		
		
		echo '<div class="toggle-box">';
		echo '<div class="toggle-handler">is_paged()</div>';
		$is_paged = is_paged() ? 'true' : 'false';
		echo '<div class="toggle-content">is_paged : ' . $is_paged . '</div>';
		echo '</div>';
		
		echo '<div class="toggle-box">';
		echo '<div class="toggle-handler">global $wp_post_types</div>';
		echo '<div class="toggle-content">' . $this->_pre_print_r($wp_post_types) . '</div>';
		echo '</div>';
		
		echo '<div class="toggle-box">';
		echo '<div class="toggle-handler">global $wp_taxonomies</div>';
		echo '<div class="toggle-content">' . $this->_pre_print_r($wp_taxonomies) . '</div>';
		echo '</div>';
		
		echo '<div class="toggle-box">';
		echo '<div class="toggle-handler">global $dp</div>';
		echo '<div class="toggle-content">' . $this->_pre_print_r($dp) . '</div>';
		echo '</div>';
		
		echo '<div class="toggle-box">';
		echo '<div class="toggle-handler">global $wp_actions</div>';
		echo '<div class="toggle-content">' . $this->_pre_print_r($wp_actions) . '</div>';
		echo '</div>';
		
		echo '<div class="toggle-box">';
		echo '<div class="toggle-handler">global $wp_query</div>';
		echo '<div class="toggle-content">' . $this->_pre_print_r($wp_query) . '</div>';
		echo '</div>';
		
		echo '<div class="toggle-box">';
		echo '<div class="toggle-handler">global $post</div>';
		echo '<div class="toggle-content">' . $this->_pre_print_r($post) . '</div>';
		echo '</div>';
		
		echo '<div class="toggle-box">';
		echo '<div class="toggle-handler">global $all_options</div>';
		echo '<div class="toggle-content">' . $this->_pre_print_r($all_options) . '</div>';
		echo '</div>';
		
		global $l10n;
		echo '<div class="toggle-box">';
		echo '<div class="toggle-handler">global $i10n</div>';
		echo '<div class="toggle-content">' . $this->_pre_print_r($l10n['dp']) . '</div>';
		echo '</div>';
		
		echo '</div></div>';
	}
	
	function dp_admin_page_info() {
	global $pagenow, $typenow, $plugin_page, $page_hook, $current_screen, $screen_layout_columns;
	$screen = get_current_screen();
	echo '<div class="dev">
			<table class="widefat post fixed" cellspacing="0">
				<thead>
				<tr>
				<th>type</th>
				<th>value</th>
				</tr>
				<thead>
				<tbody>';
	echo '<tr><td>@global string $pagenow</td> <td><pre>'.$pagenow.'</pre></td></tr>';
	echo '<tr><td>@global string $typenow</td> <td><pre>'.$pagenow.'</pre></td></tr>';
	echo '<tr><td>@global string $plugin_page</td> <td><pre>'.$pagenow.'</pre></td></tr>';
	echo '<tr><td>@global string $page_hook</td> <td><pre>'.$page_hook.'</pre></td></tr>';
	echo '<tr><td>@global string $screen_layout_columns</td> <td><pre>'.$screen_layout_columns.'</pre></td></tr>';
	echo '<tr><td>@global object $current_screen</td> <td><pre>'.print_r($current_screen,1).'</pre></td></tr>';
	echo '<tr><td>@uses get_current_screen()</td> <td><pre>'.print_r($screen,1).'</pre></td></tr>';
	echo '</tbody></table></div>';
	
	
	global $menu, $submenu, $wp_registered_sidebars, $wp_registered_widgets;
	echo '<div class="dev">
			<table class="widefat post fixed" cellspacing="0">
				<thead>
				<tr>
				<th>type</th>
				<th>value</th>
				</tr>
				<thead>
				<tbody>';
	echo '<tr><td>@global array $menu</td> <td><pre>'.print_r($menu,1).'</pre></td></tr>';
	echo '<tr><td>@global araay $submenu</td> <td><pre>'.print_r($submenu,1).'</pre></td></tr>';
	echo '<tr><td>@global array $wp_registered_sidebars</td> <td><pre>'.esc_html(print_r($wp_registered_sidebars, 1)).'</pre></td></tr>';
	echo '<tr><td>@global array $wp_registered_widgets</td> <td><pre>'.esc_html(print_r($wp_registered_widgets, 1)).'</pre></td></tr>';
	echo '</tbody></table></div>';
}
	
	function dp_admin_dev_css() { ?>
	<style type="text/css">
		.dev{margin:20px;display:noned;}

	</style>
	<?php }
}

// if(defined('REMIX_DEBUG')) $debug = new DP_Debug;
	
/**
 * View all defined constants 
 *
 */
function dp_defined_constants() {
	pre_print_r(@get_defined_constants());
} 
 
/**
 * Saved queries for analysis
 *
 */
function dp_saved_queries() {
    global $wpdb;
	pre_print_r($wpdb->queries);
} 
 
function pre_print_r($r, $clean = true) {
	$r =  print_r($r, 1);
	if($clean)
		$r =  esc_html($r);
			
	echo '<pre>'.$r.'</pre>';
}

function list_hooked_functions($tag=false){
 global $wp_filter;
 if ($tag) {
  $hook[$tag]=$wp_filter[$tag];
  if (!is_array($hook[$tag])) {
  trigger_error("Nothing found for '$tag' hook", E_USER_WARNING);
  return;
  }
 }
 else {
  $hook=$wp_filter;
  ksort($hook);
 }
 echo '<pre>';
 foreach($hook as $tag => $priority){
  echo "<br />&gt;&gt;&gt;&gt;&gt;\t<strong>$tag</strong><br />";
  ksort($priority);
  foreach($priority as $priority => $function){
  echo $priority;
  foreach($function as $name => $properties) echo "\t$name<br />";
  }
 }
 echo '</pre>';
 return;
}


add_filter('body_class','dp_browser_class');
function dp_browser_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	else $classes[] = 'unknown';

	if($is_iphone) $classes[] = 'iphone';
	return $classes;
}



function list_hooks( $filter = false ){
	global $wp_filter;
	
	$hooks = $wp_filter;
	ksort( $hooks );

	foreach( $hooks as $tag => $hook )
	    if ( false === $filter || false !== strpos( $tag, $filter ) )
			dump_hook($tag, $hook);
}

function list_live_hooks( $hook = false ) {
    if ( false === $hook )
		$hook = 'all';

    add_action( $hook, 'list_hook_details', -1 );
}

function list_hook_details( $input = NULL ) {
    global $wp_filter;
	
    $tag = current_filter();
    if( isset( $wp_filter[$tag] ) )
		dump_hook( $tag, $wp_filter[$tag] );

	return $input;
}

function dump_hook( $tag, $hook ) {
    ksort($hook);

    echo "<pre>&gt;&gt;&gt;&gt;&gt;\t<strong>$tag</strong><br />";
    
    foreach( $hook as $priority => $functions ) {

	echo $priority;

	foreach( $functions as $function )
	    if( $function['function'] != 'list_hook_details' ) {
		
		echo "\t";

		if( is_string( $function['function'] ) )
		    echo $function['function'];

		elseif( is_string( $function['function'][0] ) )
		     echo $function['function'][0] . ' -> ' . $function['function'][1];

		elseif( is_object( $function['function'][0] ) )
		    echo "(object) " . get_class( $function['function'][0] ) . ' -> ' . $function['function'][1];

		else
		    print_r($function);

		echo ' (' . $function['accepted_args'] . ') <br />';
		}
    }

    echo '</pre>';
}