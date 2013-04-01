<?php

// pluginname Category Search
// shortname CategorySearch
// dashname category-search

/*
Plugin Name: Category Search
Version: 1.0.2
Plugin URI: http://www.prelovac.com/vladimir/wordpress-plugins/category-search
Author: Vladimir Prelovac
Author URI: http://www.prelovac.com/vladimir
Description: Adds the option to display category names in search results. 


*/



// Avoid name collisions.
if ( !class_exists('CategorySearch') ) :

class CategorySearch {
	
	// Name for our options in the DB
	var $CategorySearch_DB_option = 'CategorySearch_options';
	var $CategorySearch_options; 
	
	// Initialize WordPress hooks
	function CategorySearch() {	
		//add_filter('the_content',  array(&$this, 'content_filter'), 10);	
		// Add Options Page
		add_action('admin_menu',  array(&$this, 'admin_menu'));
	}
/*

function Show2()
{
	global $wpdb;
	
	if (is_search()
	&& !is_paged() // not the second or subsequent page of a previously-counted search
	&& !is_admin() // not using the administration console	
	&& ($_SERVER['HTTP_REFERER']) // proper referrer (otherwise could be search engine, cache...)
	) {
		
		
		// Get all details of this search
		// search string is the raw query
		$search_string = $wp_query->query_vars['s'];
		 
		if (get_magic_quotes_gpc()) {
			$search_string = stripslashes($search_string);
		}
		$search_string = trim($search_string); 
		
		// search terms is the words in the query
		$search_terms = $search_string;
		$search_terms = preg_replace('/[," ]+/', ' ', $search_terms);
		$search_terms = trim($search_terms); 
		//echo "sstring: $search_string sterms: $search_terms";
		
		
	$query="SELECT wp_terms.name, wp_terms.term_id FROM wp_terms LEFT JOIN wp_term_taxonomy ON wp_terms.term_id = wp_term_taxonomy.term_id WHERE wp_term_taxonomy.taxonomy = 'category'";
	$categories = $wpdb->get_results($query);
	foreach ($categories as $cat) 
	{
		$name= $cat->name;	
	
	} 	
	
}
}
*/


function Show($args = '') {
		
		global $wpdb, $wp_query;
	

	
	if (is_search()
	&& !is_paged() // not the second or subsequent page of a previously-counted search
	&& !is_admin() // not using the administration console	
	&& ($_SERVER['HTTP_REFERER']) // proper referrer (otherwise could be search engine, cache...)
	) {
		
		
		// Get all details of this search
		// search string is the raw query
		$search_string = $wp_query->query_vars['s'];
		 
		if (get_magic_quotes_gpc()) {
			$search_string = stripslashes($search_string);
		}
		$search_string = trim($search_string); 
		
		
		$search_terms = $search_string;
		$search_terms = preg_replace('/[," ]+/', ' ', $search_terms);
		$search_terms = trim($search_terms); 
		
		
		
		
		
	$defaults = array(
		'show_option_all' => '', 'orderby' => 'name',
		'order' => 'ASC', 'show_last_update' => 0,
		'style' => 'list', 'show_count' => 0,
		'hide_empty' => 1, 'use_desc_for_title' => 1,
		'child_of' => 0, 'feed' => '', 'feed_type' => '',
		'feed_image' => '', 'exclude' => '',
		'hierarchical' => true, 'title_li' => __('Categories'),
		'echo' => 1, 'depth' => 0
	);

	$r = wp_parse_args( $args, $defaults );

	if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
		$r['pad_counts'] = true;
	}

	if ( isset( $r['show_date'] ) ) {
		$r['include_last_update_time'] = $r['show_date'];
	}

	extract( $r );

	$categories = get_categories($r);


	$output = '';
	$count=0;
	
	$options = $this->get_options();

	//	$post=$options['post']=='on'?'checked':'';
		$before_search=$options['before_search'];
		$after_search=$options['after_search'];
		$before_list=$options['before_list'];
		$after_list=$options['after_list'];
		
	
	
	if ( empty($categories) ) {
		
			$output = '';		
	} else {		
		
		foreach ($categories as $cat) {
			
				
			if (strpos(strtolower($cat->name),strtolower($search_string))!== false)
			{
				$name=attribute_escape( $cat->name); 
				$link=get_category_link( $cat->term_id );
				$desc=$category->description;
				$output.=$before_list.'<a href="'.$link.'">'.$name.'</a>'.$after_list;
				$count++;
			}
		}
		
	}
	

		if ($count)
			echo $before_search.$output.$after_search;
	}
}

	// Hook the options mage
	function admin_menu() {
	//	add_management_page('Category Search Options', 'Category Search', 8, basename(__FILE__), array(&$this, 'handle_options'));
		add_options_page('Category Search Options', 'Category Search', 8, basename(__FILE__), array(&$this, 'handle_options'));
	
	} 
	
	// Handle our options
	function get_options() {	 

        $existing_options = get_option($this->CategorySearch_DB_option);

        if (!empty($existing_options)) {
            foreach ($existing_options as $key => $option)
                $options[$key] = $option;
        }            
        return $options;
	}

	// Set up everything
	function install() {
		$CategorySearch_options = $this->get_options();		
		if (!$CategorySearch_options)
		{
			// set default values 
			
			$options = array();			
			$options['before_search']="<div class='cat-search-menu'><strong>Matching Categories</strong><br><ul>";
			$options['after_search']="</ul></div>";
			$options['before_list']='<li class="cat-search-item">';
			$options['after_list']='</li>';
		
			update_option($this->CategorySearch_DB_option, $options);
		}
	}
	
	function handle_options()
	{
		if ( isset($_POST['submitted']) ) {
			$options = array();
			//print_r($_POST);
			
			$options['before_search']=stripslashes( $_POST['before_search']);
			$options['after_search']=stripslashes($_POST['after_search']);
			$options['before_list']=stripslashes($_POST['before_list']);
			$options['after_list']=stripslashes($_POST['after_list']);
			
		
			update_option($this->CategorySearch_DB_option, $options);
			echo '<div class="updated fade"><p>Plugin settings saved.</p></div>';
		}

		$action_url = $_SERVER['REQUEST_URI'];		
		
		$options = $this->get_options();

	//	$post=$options['post']=='on'?'checked':'';
		$before_search=$options['before_search'];
		$after_search=$options['after_search'];
		$before_list=$options['before_list'];
		$after_list=$options['after_list'];
		
			
		$imgpath=trailingslashit(get_option('siteurl')). 'wp-content/plugins/category-search/i';	
		
		echo <<<END

<div class="wrap" style="max-width:950px !important;">
	<h2>Category Search</h2>
				
	<div id="poststuff" style="margin-top:10px;">

	 <div id="sideblock" style="float:right;width:220px;margin-left:10px;"> 
		 <h3>Information</h3>
		 <div id="dbx-content" style="text-decoration:none;">
			 <img src="$imgpath/home.png"><a href="http://www.prelovac.com/vladimir/wordpress-plugins/category-search"> Category Search Home</a><br /><br />
			 <img src="$imgpath/idea.png"><a href="http://www.prelovac.com/vladimir/wordpress-plugins/category-search#comments"> Suggest a Feature</a><br /><br />
			 <img src="$imgpath/more.png"><a href="http://www.prelovac.com/vladimir/wordpress-plugins"> My WordPress Plugins</a><br /><br />
			 <br />
		
			 <p align="center">
			 <img src="$imgpath/p1.png"></p>
			
			 <p> <img src="$imgpath/help.png"><a href="http://www.prelovac.com/vladimir/services"> Need a WordPress Expert?</a></p>
 		</div>
 	</div>

	 <div id="mainblock" style="width:710px">
	 
		<div class="dbx-content">
		 	<form name="CategorySearch" action="$action_url" method="post">
					<input type="hidden" name="submitted" value="1" /> 
					<h3>Options</h3>
					
					<p>Category Search can be customized to suit your needs.</p>
					
					
					<h4>Before Search results</h4>	
					<textarea name="before_search"  rows="2" cols="50">$before_search</textarea> 
					<br>
					
					<h4>Before Search item</h4>	
					<textarea name="before_list"  rows="2" cols="50">$before_list</textarea>
					<br>
					<h4>After Search item</h4>	
					<textarea name="after_list"  rows="2" cols="50">$after_list</textarea>
					
					<br>
					<h4>After Search results</h4>	
					<textarea name="after_search"  rows="2" cols="50">$after_search</textarea>
					
					<div class="submit"><input type="submit" name="Submit" value="Update" /></div>
			</form>
		</div>
		
		<br/><br/><h3>&nbsp;</h3>	
	 </div>

	</div>
	
<h5>WordPress plugin by <a href="http://www.prelovac.com/vladimir/">Vladimir Prelovac</a></h5>
</div>
END;
	}
	
	

}

endif; 

if ( class_exists('CategorySearch') ) :
	
	$CategorySearch = new CategorySearch();
	if (isset($CategorySearch)) {
		register_activation_hook( __FILE__, array(&$CategorySearch, 'install') );
	}
endif;

?>