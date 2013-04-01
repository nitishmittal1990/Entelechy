<?php
/**
 * API for creating administration panel
 *
 * @package DP Core
 * @subpackage Admin
 * @since DP Core 1.0
 * @author Cloud Stone <cloud@dedepress.com>
 * @copyright Copyright (c) 2011, Cloud Stone & dedepress.com
 * @link http://dedepress.com/themes/dp/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
abstract class DP_Panel {
	protected $prefix;
	protected $textdomain;
	
	protected $layout_like;
	protected $layout_columns;
	
	protected $menu_slug;
	protected $plugin_file;
	
	
	/*======================================================================*/
	/*	Registration Component
	/*======================================================================*/

	private static $registered = array();
	
	static function register( $class ) {
		if ( isset( self::$registered[$class] ) )
			return false;
			
		self::$registered[$class] = $class;
		
		add_action('_admin_menu', array(__CLASS__, '_register_panel'));
		
		return true;
	}
	
	static function unregister( $class ) {
		if ( ! isset( self::$registered[$class] ) )
			return false;

		unset( self::$registered[$class] );

		return true;
	}
	
	static function _register_panel() {
		foreach(self::$registered as $class) {
			new $class();
		}
	}
	
	/*======================================================================*/
	/*	Main Method
	/*======================================================================*/
	
	/**
	 * PHP4 constructor
	 */
	function DP_Panel() {
		$this->__construct();
	}
	
	/*
	 * PHP5 constructor
	 */
	function __construct() {
		global $plugin_page;
	
		$this->prefix = !empty($this->prefix) ? $this->preifx : dp_get_prefix();
		$this->textdomain = !empty($this->textdomain) ? $this->textdomain : dp_get_textdomain();
		
		$this->layout_columns = !empty($this->layout_columns) ? $this->layout_columns : 2;
		$this->layout_like = !empty($this->layout_like) ? $this->layout_like : 'post_new';
		
		$this->donate_url = !empty($this->donate_url) ? $this->donate_url : 'http://dedepress.com/donate/';
		$this->support_url = !empty($this->support_url) ? $this->support_url : 'http://dedepress.com/support/';
		$this->translating_url = !empty($this->translating_url) ? $this->translating_url : 'http://dedepress.com/';
		
		load_plugin_textdomain( 'dp-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		add_action('admin_menu', array(&$this, 'update'), 2);
		
		/* Add menu pages and meta boxes */
		add_action('admin_menu', array(&$this, 'add_menu_pages'));
		add_action('admin_menu', array(&$this, 'add_meta_boxes'));
		
		/* Set screen layout columns */
		add_action('admin_init', array(&$this, 'screen_options'), 0 ); // for wp 3.1 or higher
		
		// Print default scripts and styles
		add_action( 'admin_print_scripts', array( &$this,'default_scripts' ) );
		add_action( 'admin_print_styles', array( &$this,'default_styles' ) );

		// Add admin notices
		add_action('admin_notices', array(&$this, 'admin_notices'));
		
		// Filtering pluginn action links and plugin row meta
		add_filter( 'plugin_action_links', array(&$this, 'plugin_action_links'),  10, 2 );
		add_filter( 'plugin_row_meta', array(&$this, 'plugin_row_meta'),  10, 2 );
	}
	
	function add_menu_pages() {
		
	}
	
	function add_meta_boxes() {
	
	}
	
	function add_default_meta_boxes($meta_boxes = array()) {
		global $plugin_page;
		$page_hook = get_plugin_page_hookname( $plugin_page, '' );
		
		if(in_array('plugin-info', $meta_boxes))
			add_meta_box('plugin-info-meta-box', __('Plugin info', 'dp-core'), array(&$this, 'plugin_info_meta_box'), $page_hook, 'side' );
		
		if(in_array('like-this', $meta_boxes))
			add_meta_box('like-this-meta-box', __('Like This?', 'dp-core'), array(&$this, 'like_this_meta_box'), $page_hook, 'side' );
		
		if(in_array('need-support', $meta_boxes))
			add_meta_box('need-support-meta-box', __('Need Support?', 'dp-core'), array(&$this, 'need_support_meta_box'), $page_hook, 'side' );
		
		if(in_array('quick-preview', $meta_boxes))
			add_meta_box('quick-preview-meta-box', __('Quick Preview', 'dp-core'), array(&$this, 'quick_preview_meta_box'), $page_hook, 'side' );
	}
	
	/*======================================================================*/
	/*	Default Meta boxes 
	/*======================================================================*/
	
	function plugin_info_meta_box() {
		$plugin_data = get_plugin_data( trailingslashit(WP_PLUGIN_DIR) . $this->plugin_file, false);

		echo '<p>' . __('Name:', 'dp-core') . ' <a target="_blank" href="'.$plugin_data['PluginURI'].'"><strong>' . $plugin_data['Name'] . '</strong></a></p>';
		echo '<p>' . __('Version:', 'dp-core') . ' ' .$plugin_data['Version'] . '</p>';
		echo '<p>' . __('Author:', 'dp-core') . ' <a href="'.$plugin_data['AuthorURI'].'">' . $plugin_data['Author'] . '</a></p>';
		echo '<p>' . __('Description:', 'dp-core') . ' '. $plugin_data['Description'] . '</span></p>';
	}
	
	function like_this_meta_box() {
		echo '<p>' . __('We spend a lot of effort on Free WordPress development. Any help would be highly appreciated. Thanks!', 'dp-core') . '</p>';
		echo '<ul>';
		
		$plugin_data = get_plugin_data( trailingslashit(WP_PLUGIN_DIR) . $this->plugin_file, false);
		
		echo '<li class="link-it"><a href="' . $plugin_data['PluginURI']. '">' . __('Link to it so others can find out about it', 'dp-core') . '</a></li>';

		if( !empty($this->wp_plugin_url) )
			echo '<li class="rating-it"><a href="' . $this->wp_plugin_url . '">' . __('Give it a good rating on WordPress.org', 'dp-core') . '</a></li>';
		
		if( !empty($this->donate_url) )
			echo '<li class="donate-it"><a href="' . $this->donate_url. '">' . __('Donate something to our team', 'dp-core') . '</a></li>';
		
		if( !empty($this->translating_url) )
			echo '<li class="trans-it"><a href="' . $this->translating_url. '">' . __('Help us translating it', 'dp-core') . '</a></li>';
			
		echo '</ul>';
	}
	
	function need_support_meta_box() {
		echo '<p>';
		echo sprintf(__('If you have any problems or ideas for improvements or enhancements, please use the <a href="%s">Our Support Forums</a>.', 'dp-core'), $this->support_url );
		echo '</p>';
	}
	
	function quick_preview_meta_box() {
		$output = '<p>'. __('Here are the links for the different types of pages, so you can preview changes quickly.', 'dp-core') . '</p>';
	
		// Singular Page
		$post_types = get_post_types(array('public' => true), 'objects');
		foreach($post_types as $type => $obj) {
			$posts = get_posts('numberposts=1&post_type='.$type);
			$output .= '<li><strong>Singular: '.$obj->labels->singular_name.'</strong><br />';
			
			
			if(isset($posts[0])) {
				$title = get_the_title($posts[0]->ID);
				if(empty($title))
					$title = __('No Title', 'dp-core');
				
				$output .= '<a href="'.get_permalink($posts[0]->ID).'">'.$title.'</a>';
			} else
				$output .= 'No posts found in this post type, <a href="'. admin_url( 'post-new.php?post_type='.$type) .'">Add New?</a>';
				
			$output .= '</li>';
		}
	
		// Post type archive
		$post_types = get_post_types(array( 'has_archive' => true ), 'objects');
		if(function_exists('get_post_type_archive_link')) {
			foreach($post_types as $type => $obj) {
				$output .= '<li><strong>'.__('Post tyle archive:', 'dp-core').$obj->labels->singular_name.'</strong><a href="'.get_post_type_archive_link($type).'">'.$obj->labels->singular_name.'</a></li>';
			}
		}
	
		// Taxonomy archive
		$taxonomies = get_taxonomies(array( 'show_ui' => true ), 'objects');
		foreach($taxonomies as $tax => $obj) {
			$terms = get_terms($tax, 'number=1');
			$output .= '<li><strong>Taxonomy: '.$obj->labels->singular_name.'</strong><br />';
			if(isset($terms[0]))
				$output .= ' <a href="'.get_term_link($terms[0], $tax).'">'.$terms[0]->name.'</a></li>';
			else
				$output .= 'No terms found in this taxonomy, <a href="'.admin_url( 'edit-tags.php?taxonomy='.$tax).'">Add New?</a></li>';
		}
	
		// Date Archive 
		foreach(array('daily'=> __('Daily', 'dp-core'), 'weekly' => __('Weekly', 'dp-core'), 'monthly' => __('Monthly', 'dp-core'), 'yearly' => __('Yearly', 'dp-core')) as $type => $title) {
			$output .= wp_get_archives('before=<strong>'.__('Date:', 'dp-core'). ' ' .$title.'</strong><br />&type='.$type.'&limit=1&echo=0');
		}
		
		// Author archive
		$output .= '<li><strong>'.__('Author Page', 'dp-core'). '</strong><br />'. wp_list_authors('exclude_admin=0&number=1&style=none&echo=0').'</li>';
	
		// Search 
		$output .= '<li><strong>'.__('Search Page', 'dp-core'). '</strong><br /><a href="'.get_bloginfo('url').'/?s=hello">'.__('Search Page', 'dp-core'). '</a></li>';
	
		// 404
		$output .= '<li><strong>'.__('404 Page', 'dp-core'). '</strong><br /><a href="'.get_bloginfo('url').'/404/'.'">'.__('404 Page', 'dp-core').'</a></li>';
		
		$output = str_replace('<a href', '<a target="_blank" href', $output);
		
		echo '<div class="quick-preview"><ul>'.$output.'</ul></div>';
	}
	
	
	/**
	 * Default meta box callback function
	 * @since 0.7
	 */
	function meta_box($object, $box) {
		$defaults = $this->defaults();		
		if(!isset($defaults[$box['id']]))
			return;
		$defaults = $defaults[$box['id']];

		foreach($defaults as $field) {
			if(!empty($field['name'])) {
				
			$name = $field['name'];
			$name = str_replace('[]', '', $name);
			$name = str_replace(']', '', $name);
			$name = explode('[', $name);
			$name_depth = count($name);
			
			$option = get_option($name[0]);
			
			$value = $option;
			unset($name[0]);
			foreach($name as $n) {
				if( empty($value[$n]) ) {
					$value = '';
					break;
				}	
						
				$value = $value[$n];
			}
			
			$field['value'] = $value;
			}
			$new_fields[] = $field;
		}
		
		dp_form_table($new_fields);
	}
	
	/**
	 * Handle form data.
	 * @since 0.7
	 */
	function update() {
		$defaults = $this->get_defaults();

		if( !is_array($defaults) || empty($defaults) || !isset($_GET['page']) || $_GET['page'] != $this->menu_slug)
			return;
			
		// Save the settings when user click "save" Button
		if (isset($_POST['save'])) {
			foreach($defaults as $option_name => $option_value) {
				if(empty($_POST[$option_name]))
					$_POST[$option_name] = '';
				
				update_option($option_name, $_POST[$option_name]);
			}
			
			wp_redirect( admin_url( 'admin.php?page='.$this->menu_slug.'&updated=true' ) );
			exit();
		} 
		
		// Reset settings to defaults when user click "reset" button or settings is empty.
		elseif(isset($_POST['reset'])) {
			foreach($defaults as $option_name => $option_value) {
				update_option($option_name, $option_value);
			}
			
			wp_redirect( admin_url( 'admin.php?page='.$this->menu_slug.'&reset=true' ) );
			exit();
		}
	}
	
	/**
	 * Generate a standard admin notice
	 * @since 0.7
	 */
	function admin_notices() {
		global $parent_file;
		
		if ( !isset($_GET['page']) || $_GET['page'] != $this->menu_slug )
			return;
		
		if (isset($_GET['updated']) && $_GET['updated'] == 'true' && $parent_file != 'options-general.php')
			echo '<div id="message" class="updated"><p><strong>' . __('Settings Saved.', 'dp-core') . '</strong></p></div>';
		
		elseif (isset($_GET['reset']) && ( $_GET['reset'] == 'true' ))
			echo '<div id="message" class="updated"><p><strong>' . __('Settings Reset.', 'dp-core') . '</strong></p></div>';
	}
	
	/*======================================================================*/
	/*	Plugin filters
	/*======================================================================*/
	function plugin_action_links( $actions, $plugin_file ) {
			if ( $plugin_file == $this->plugin_file && $this->settings_url)
				$actions[] = '<a href="'.$this->settings_url.'">' . __('Settings', 'dp-core') .'</a>';
			
			return $actions;
		}
	
	function plugin_row_meta( $plugin_meta, $plugin_file ){
			if ( $plugin_file == $this->plugin_file ) {
				$plugin_meta[] = '<a href="'.$this->donate_url.'">' . __('Donate', 'dp-core') .'</a>';
				$plugin_meta[] = '<a href="'.$this->support_url.'">' . __('Support', 'dp-core') .'</a>';
			}

			return $plugin_meta;
		}
	
	
	/*======================================================================*/
	/*	Theme filters
	/*======================================================================*/
	function theme_action_links($links) {
		 $links[] = '<a href="'.admin_url('options.php').'">' . __('Settings') .'</a>';
		return $links;
	}
	
	
	/*======================================================================*/
	/*	Screen functions
	/*======================================================================*/
	
	function screen_icon() {
		global $plugin_page, $parent_file;
		echo '<a target="_blank" href="http://dedepress.org">';
		screen_icon('plugins');
		echo '</a>';
	}
	
	function screen_layout_columns($columns, $screen) {
			
		$columns[$screen] = $this->layout_columns;
		
		return $columns;
	}
	
	function screen_options() {
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2 ));
	}
	
	function submenus() {
		global $plugin_page, $submenu, $parent_file; 
		$i = 0;
		
		if(!isset($submenu[$parent_file]) || !is_array($submenu[$parent_file]))
			return;
		
		echo '<ul class="subsubsub">';
		foreach($submenu[$parent_file] as $sub) {
			echo '<li>';
			if($i > 0) echo " | ";
			$i++;
			$class = '';
			if($sub[2] == $plugin_page)
				$class = ' class="current"';
			echo '<a'.$class.' href="'.esc_url(admin_url('admin.php?page=' . $sub[2])).'">'.$sub[0].'</a></li>'; 
		}
		echo '</ul>';
	}
	
	/*
	 * Get default settings from default fields array.
	 *
	 * @since 0.7
	 * @param array Fields array.
	 */
	function get_defaults($fields = array()) {
		if(!$fields)
			$fields = $this->defaults();
		
		if(empty($fields) || !is_array($fields))
			return;
			
		$defaults = array();
		
		foreach($fields as $box_id => $box_fields) {
		foreach($box_fields as $field) {
			if( !empty($field['name']) ) {
				$name = $field['name'];
				$name = str_replace('[]', '', $name);
				$name = str_replace(']', '', $name);
				$name = explode('[', $name);
				$name_depth = count($name);
				
				$value = !empty($field['value']) ? $field['value'] : '';
				
				if($name_depth == 1)
					$defaults[$name[0]] = $value;
				elseif($name_depth == 2)
					$defaults[$name[0]][$name[1]] = $value;
				elseif($name_depth == 3)
					$defaults[$name[0]][$name[1]][$name[2]] = $value;
				elseif($name_depth == 4)
					$defaults[$name[0]][$name[1]][$name[2]][$name[3]] = $value;
				elseif($name_depth == 5)
					$defaults[$name[0]][$name[1]][$name[2]][$name[3]][$name[4]] = $value;
			}
		}
		}
		
		return $defaults;
	}
	
	function get_settings() {
		global $plugin_page;
	
		// get option name from the name of plugin_page	
		$option_name = str_replace('-','_',$plugin_page);
	
		// get options from option name
		if(empty($settings))
			$settings = get_option($option_name);
		
			if(!empty($field['name'])) {
			$name = $field['name'];
			if(!empty($field['to_array'])) { 
				$to_array = $field['to_array'];
				$field['value'] = !empty($settings[$to_array][$name]) ? $settings[$to_array][$name] : '';
			} else {
				$field['value'] = !empty($settings[$name]) ? $settings[$name] : '';
			}
		}
	
		return $field;
		
	}
	
	function menu_page() {  
		global $parent_file, $plugin_page, $page_hook, $typenow, $hook_suffix, $pagenow, $current_screen, $wp_current_screen_options, $screen_layout_columns; ?>
		<div class="wrap dp-panel">
		
			<form method="post" action="">
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
		
				<?php $this->screen_icon(); ?>

				<h2>
					<?php echo get_admin_page_title(); ?>
					<input type="submit" name="save" value="<?php _e('Save Changes', 'dp-core'); ?>" class="button-primary">
					<input type="submit" value="<?php _e('Reset Settings', 'dp-core'); ?>" name="reset" class="reset button-highlighted">
					<input type="button" class="button toggel-all add-new-h2" value="<?php _e('Toggle Boxes', 'dp-core'); ?>" />
				</h2>
		
				<?php // $this->submenus(); ?>
		
				<br class="clear" />
				
				<?php if($this->layout_like == 'dashboard') { // Layout like WP Dashboard ?>
				
				<?php
					$hide2 = $hide3 = $hide4 = '';
					switch ( $screen_layout_columns ) {
					case 4:
						$width = 'width:24.5%;';
					break;
					case 3:
						$width = 'width:32.67%;';
						$hide4 = 'display:none;';
					break;
					case 2:
						$width = 'width:49%;';
						$hide3 = $hide4 = 'display:none;';
					break;
					default:
						$width = 'width:98%;';
						$hide2 = $hide3 = $hide4 = 'display:none;';
					}
				?>
		
				<div id="dashboard-widgets" class="metabox-holder">
					
					<div class="postbox-container" style="<?php echo $width; ?>">
						<?php do_meta_boxes($page_hook, 'normal', null); ?>
						<p>
							<input type="submit" name="save" value="Save Changes" class="button-primary">
							<input type="submit" value="Reset Settings" name="reset" class="reset button-highlighted">
						</p>
					</div><!-- end .postbox-container -->
				
					<div class="postbox-container" style="<?php echo $hide2.$width; ?>">
						<?php do_meta_boxes($page_hook, 'side', null); ?>
					</div><!-- end .postbox-container -->
					
					<div class="postbox-container" style="<?php echo $hide3.$width; ?>">
						<?php do_meta_boxes($page_hook, 'column3', null); ?>
					</div><!-- end .postbox-container -->
					
					<div class="postbox-container" style="<?php echo $hide4.$width; ?>">
						<?php do_meta_boxes($page_hook, 'column4', null); ?>
					</div><!-- end .postbox-container -->
		
				</div><!-- end .metabox-holder -->
				
				<br class="clear" />
		
				<?php } else { // Default layout, like post new ?>
				
				<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
					
					<div class="inner-sidebar">
						<?php do_meta_boxes($page_hook, 'side', null); ?>
					</div><!-- end .innser-sidebar -->
			
					<div id="post-body">
						<div id="post-body-content">
							<?php do_meta_boxes($page_hook, 'normal', null); ?>
							<input type="submit" name="save" value="<?php _e('Save Changes', 'dp-core'); ?>" class="button-primary">
							<input type="submit" value="<?php _e('Reset Settings', 'dp-core'); ?>" name="reset" class="reset button-highlighted"><br/><br/><br/>
						</div><!-- end #post-body-conent -->
					</div><!-- end #post-body -->
			
					<br class="clear" />
					
				</div><!-- end .metabox-holder -->
		
				<?php } // end if($this->layout_like == 'dashboard') ?>
				
				<span class="pickcolor"></span>
				<div id="colorpicker" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
				
			</form>
			
		</div><!-- end .wrap -->
	<?php }
	
		function defaults() {
			$defaults = array();
			return $defaults;
		}
	
		/**
		 * Add default scripts.
		 *
		 * @since 1.0
		 */
		function default_scripts() {
			if (!isset($_GET['page']) || $_GET['page'] != $this->menu_slug)
				return;
			
			wp_enqueue_script('postbox');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('dp-admin', plugin_dir_url(__FILE__)  . 'js/admin.js', array('jquery'), '', true);
		}
	
		/**
		* Add default styles
		*
		* @since 1.0
		*/
		function default_styles() {
			if (!isset($_GET['page']) || $_GET['page'] != $this->menu_slug)
				return;
			
			wp_enqueue_style('thickbox');
			wp_enqueue_style('dp-admin', plugin_dir_url(__FILE__). 'css/admin.css', false);
		}
}

/**
 * Register a admin panel
 *
 * Registers a DP_Panel admin panel
 *
 * @since 1.0
 * @see DP_Panel
 * @param string $widget_class The name of a class that extends DP_Panel
 */
function dp_register_panel($panel_class) {
	DP_Panel::register($panel_class);
}

/**
 * Unregister a admin panel
 *
 * Unregisters a DP_Panel admin panel. Useful for unregistering default panels.
 *
 * @since 1.0
 * @see DP_Panel
 * @param string $panel_class The name of a class that extends DP_Panel
 */
function dp_unregister_panel($panel_class) {
	DP_Panel::unregister($panel_class);
}

/**
 * API for creating Post Meta Box
 */
abstract class DP_Post_Meta_Box {
	protected $name;
	protected $title;
	
	protected $nonce;
	protected $nonce_action;
	
	function DP_Post_Meta_Box() {
		$this->__construct();
	}
	
	function __construct() {
		if(!$this->name)
			return;

		$this->nonce = !empty($this->nonce) ? $this->nonce : $this->name.'_nonce';
		$this->nonce_action = !empty($this->nonce_action) ? $this->nonce_action : plugin_basename(__FILE__);
		
		add_action( 'save_post', array(&$this, 'handle'), 10, 2);
		add_action( 'admin_menu', array(&$this, 'add_meta_boxes') );
	}
	
	function add_meta_boxes() {
		foreach(get_post_types(array( 'show_ui' => true ), 'objects') as $type => $obj) {
			add_meta_box($this->name, $this->title, array(&$this, 'meta_box'), $type, 'normal', 'high');
		}
	}
	
	function meta_box($object, $box) {
		global $post;
		$defaults = $this->metadata();
		if(empty($defaults) || !is_array($defaults))
			return;
		
		$fields = array();
		foreach($defaults as $field) {
			global $post;
	
			if(!empty($field['name'])) {
				if(!empty($field['to_array'])) { 
					$to_array = $field['to_array'];
					$meta = get_post_meta($post->ID, $to_array[$field['name']], true);
					$field['value'] = !empty($meta) ? $meta : '';
				} else {
					$meta = get_post_meta($post->ID, $field['name'], true);
					$field['value'] = !empty($meta) ? $meta : '';
				}
			}
		
			$fields[] = $field;
		}
	
		wp_nonce_field( $this->nonce_action, $this->nonce );
		echo dp_form_table($fields);
		
	}
	
	function handle($post_id, $post) {
		if (!isset( $_POST[$this->nonce] ) || !wp_verify_nonce( $_POST[$this->nonce], $this->nonce_action ))
			return $post_id;

		$metadata = $this->metadata();
		
		if(empty($metadata))
			return;

		foreach ( $metadata as $meta ) {
			$meta_value = get_post_meta( $post_id, $meta['name'], true );
		
			$new_meta_value = $_POST[$meta['name']];
			
			if(is_array($new_meta_value))
				$new_meta_value = array_filter($new_meta_value);

			if ( $new_meta_value && empty($meta_value) )
				add_post_meta( $post_id, $meta['name'], $new_meta_value, true );

			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $post_id, $meta['name'], $new_meta_value );

			elseif ( empty($new_meta_value) && $meta_value )
				delete_post_meta( $post_id, $meta['name'], $meta_value );
		}
	}
	
	function metadata($type = '') {
		$metadata = array();
		return $metadata;
	}
}

/**
 * API for creating Term Meta Box
 */
abstract class DP_Term_Meta_Box {
	protected $name;
	protected $title;
	
	protected $nonce;
	protected $nonce_action;
	
	function DP_Post_Meta_Box() {
		$this->__construct();
	}
	
	function __construct() {
		if(!$this->name)
			return;

		$this->nonce = !empty($this->nonce) ? $this->nonce : $this->name.'_nonce';
		$this->nonce_action = !empty($this->nonce_action) ? $this->nonce_action : plugin_basename(__FILE__);
		
		add_action('admin_menu', array(&$this, 'edit_term_form_action'));
		add_action('edit_term', array(&$this, 'handle'), 10, 3);
	}
	
	function edit_term_form_action() {
		foreach (get_taxonomies(array('show_ui' => true)) as $tax_name) { 
			add_action($tax_name . '_edit_form', array(&$this, 'meta_box'), 10, 2);
		}
	}
	
	function meta_box($term, $taxonomy) {
		if($this->title)
			echo '<h3>'.$this->title.'</h3>';
		
		$defaults = $this->metadata();
		$fields = array();
		foreach($defaults as $field) {
			global $post;
	
			if(!empty($field['name'])) {
				if(!empty($field['to_array'])) { 
					$to_array = $field['to_array'];
					$meta = get_term_meta($term->term_id, $to_array[$field['name']], true);
					$field['value'] = !empty($meta) ? $meta : '';
				} else {
					$meta = get_term_meta($term->term_id, $field['name'], true);
					$field['value'] = !empty($meta) ? $meta : '';
				}
			}
		
			$fields[] = $field;
		}
	
		wp_nonce_field( $this->nonce_action, $this->nonce );
		echo dp_form_table($fields);
	}
	
	function handle($term_id, $tt_id, $taxonomy) {
		if (!isset( $_POST[$this->nonce] ) || !wp_verify_nonce( $_POST[$this->nonce], $this->nonce_action ))
			return $term_id;

		$metadata = $this->metadata();

		foreach ( $metadata as $meta ) {
			$meta_value = get_term_meta( $term_id, $meta['name'], true );
			$new_meta_value = $_POST[$meta['name']];
			
			if(is_array($new_meta_value))
				$new_meta_value = array_filter($new_meta_value);

			if ( $new_meta_value && empty($meta_value) )
				add_term_meta( $term_id, $meta['name'], $new_meta_value, true );
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_term_meta( $term_id, $meta['name'], $new_meta_value );
			elseif ( empty($new_meta_value) && $meta_value )
				delete_term_meta( $term_id, $meta['name'], $meta_value );
		}
	}
	
	function metadata($type = '') {
		return $metadata;
	}
}

/**
 * Add sub menu page to the dp plugins main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 1.0
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 */
function add_dp_plugin_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	global $menu;
	
	/* If the top level menu page 'DP' doesn't exist, add it first. */
	if(!array_key_exists('58.99999-dp', $menu)) {
		/* Add a new menu separator */
		$menu['58.997'] = array( '', 'edit_themes', 'separator', '', 'wp-menu-separator' );
		/* Add a top level menu page */
		add_menu_page('DP Plugins', 'DP Plugins', 'edit_plugins', 'dp-plugins', 'dp_themes_page', trailingslashit(REMIX_ADMIN_URL). 'images/dp-icon-16.png', '58.99999-dp');
		add_submenu_page('dp-plugins', 'DP Plugins', 'DP Plugins', 'edit_plugins', 'dp-plugins', 'dp_plugins_page');
	}
	
	return add_submenu_page( 'dp-plugins', $page_title, $menu_title, $capability, $menu_slug, $function );
}

function dp_plugins_page() {
	echo 'DP Plugins Home';
}

function dp_themes_page() {
	echo 'DP Themes Home';
}

