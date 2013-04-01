<?php

if ( ( $pagenow == "themes.php" ) && current_user_can('administrator') && isset( $_GET['activated'] ) && ( $_GET['activated']=='true' ) ) {
	if (!include_once( get_template_directory() . '/inc/activation.php' )) {
		$stylesheet = get_option( 'theme_switched' );
		switch_theme( $stylesheet, $stylesheet );
		if ( file_exists( get_template_directory() . '/inc/activation.php' ) ) {
			$error_message[]="PHP hasn't access to file /inc/activation.php. Check the file permissions and try again please.";
		} else {
			$error_message[]="File /inc/activation.php doesn't exists. Try to upload the theme again.";
		}
		$message="<h2>The theme wasn't activated by following reasons:</h2><ul><li>".implode("</li><li>",$error_message)."</li></ul>"."Your theme was switched back to ".$stylesheet.".";
		wp_die($message,'',array('back_link'=>"/wp-admin/themes.php"));
	}
}



if (!session_id()) {
	session_start();
}
if (!include_once (get_template_directory()."/inc/library.php") )wp_die("Cannot include file /inc/library.php.");
$settingsfile='settings';
$defparamsfile="defaults";
$default='global|slider|layout|seo|translations';
	$rightargs = array(
		'before_widget' => '<div class="widget %2$s"><div class="inner">',
        'after_widget' => '</div></div>',
        'before_title' => '<div class="caption"><h3>',
        'after_title' => '</h3></div>'
	);

if ( function_exists('register_sidebar') ) {
	$sidebar='default';
    register_sidebar(array(
        'name' => 'Right Sidebar',
        'id' => 'right_sidebar',
        'description' =>'The right sidebar widget area',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="inner">',
        'after_widget' => '</div></div>',
        'before_title' => '<div class="caption"><h3>',
        'after_title' => '</h3></div>'
    ));
	register_sidebar(array(
        'name' => 'Left Sidebar',
        'id' => 'left_sidebar',
        'description' =>'The left sidebar widget area',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="inner">',
        'after_widget' => '</div></div>',
        'before_title' => '<div class="caption"><h3>',
        'after_title' => '</h3></div>'
    ));
	
	$$sidebar='bottom';
	register_sidebar(array(
        'name' => 'Footer 1',
        'id' => 'footer_1',
        'description' => 'The primary sidebar widget area',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="inner">',
        'after_widget' => '</div></div>',
        'before_title' => '<div class="caption"><h3>',
        'after_title' => '</h3></div>'
    ));
	register_sidebar(array(
        'name' => 'Footer 2',
        'id' => 'footer_2',
        'description' => 'The primary sidebar widget area',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="inner">',
        'after_widget' => '</div></div>',
        'before_title' => '<div class="caption"><h3>',
        'after_title' => '</h3></div>'
    ));
	register_sidebar(array(
        'name' => 'Footer 3',
        'id' => 'footer_3',
        'description' => 'The primary sidebar widget area',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="inner">',
        'after_widget' => '</div></div>',
        'before_title' => '<div class="caption"><h3>',
        'after_title' => '</h3></div>'
    ));
	register_sidebar(array(
        'name' => 'Tabs',
        'id' => 'tabs_sidebar',
        'description' => 'The primary sidebar widget area',
        'before_widget' => '<div id="%1$s" class="tab_widget %2$s"><div class="inner">',
        'after_widget' => '</div></div>',
        'before_title' => '<span class="scaption">',
        'after_title' => '</span>'
    ));
}
$settings=$default;
$SMTheme=new SMTheme;
include_once get_template_directory()."/inc/widgets/facebook.php";
include_once get_template_directory()."/inc/widgets/banners.php";
include_once get_template_directory()."/inc/widgets/comments.php";
include_once get_template_directory()."/inc/widgets/posts.php";
include_once get_template_directory()."/inc/widgets/social-profiles.php";
include_once get_template_directory()."/inc/widgets/video.php";
include_once get_template_directory()."/inc/widgets/flickr.php";
include_once get_template_directory()."/inc/widgets/tabs.php";
if ($_SESSION['commentinput']=='') {
	$_SESSION['commentinput']=substr(md5(rand(1,234234)),0,5);
}
if (isset($_POST[$_SESSION['commentinput']])) {
	$_POST['comment']=$_POST[$_SESSION['commentinput']];
}
if (function_exists('add_theme_support')) {
	add_theme_support('automatic-feed-links');
    add_theme_support('menus');
	add_theme_support( 'post-thumbnails' ); 
	set_post_thumbnail_size( $SMTheme->get( 'layout', 'imgwidth' ), $SMTheme->get( 'layout', 'imgheight' ) , true );
}

if ( function_exists( 'register_nav_menu' ) ) {
	register_nav_menu( 'sec-menu', 'Top Menu' );
    register_nav_menu( 'main-menu', 'Main Menu' );
	
	
}

	if ( current_user_can('administrator') ) {
        include_once (get_template_directory()."/inc/administrator.php");
        $APage = new AdminPage();
    }


	$SMTheme->prepare_func('right_sidebar', 'show_right_sidebar_widgets');
	$SMTheme->prepare_func('footer_1', 'show_footer_1_widgets');
	$SMTheme->prepare_func('footer_2', 'show_footer_2_widgets');
	$SMTheme->prepare_func('footer_3', 'show_footer_3_widgets');
	$SMTheme->prepare_func('tabs_sidebar', 'show_tabs_sidebar_widgets');
	
	
	
	function show_tabs_sidebar_widgets() {
		$rightargs=array( 'before_widget' => '<div class="tab_widget widget_categories"><div class="inner">',
        'after_widget' => '</div></div>',
        'before_title' => '<span class="caption">',
        'after_title' => '</span>');
		$instance['hierarchical']='1';
		the_widget('WP_Widget_Categories', $instance, $rightargs);
		$rightargs=array( 'before_widget' => '<div class="tab_widget widget_recent_entries"><div class="inner">',
        'after_widget' => '</div></div>',
        'before_title' => '<span class="caption">',
        'after_title' => '</span>');
		unset($instance);
		the_widget('WP_Widget_Recent_Posts', $instance, $rightargs);
	}
	
	function show_right_sidebar_widgets() {
		global $rightargs;
		$rightargs['before_widget'] = '<div class="widget widget_search"><div class="inner">';
		the_widget('WP_Widget_Search', $instance, $rightargs);
		$rightargs['before_widget'] = '<div class="widget widget_posts"><div class="inner">';
		$instance['display_featured_image']=1;
		$instance['title']='Recent Posts';
		$instance['display_title']=1;
		$instance['excerpt_length']=120;
		the_widget('Posts', $instance, $rightargs);
		$rightargs['before_widget'] = '<div class="widget widget_comments"><div class="inner">';
		$instance = array(
			'title' => 'Recent Comments',
			'comments_number' => '5',
			'display_author' => 'true',
			'display_comment' => 'true',
			'display_avatar' => 'true',
			'read_more_text' => '&raquo;',
			'comment_length' => '26',
			'avatar_size' => '32',
			'avatar_align' => 'alignleft'
		);
		the_widget('Comments', $instance, $rightargs);
		unset($instance);
		$instance = array(
		'width' =>'272',
        'title' => 'Video',
        'videos' => array(
				array(
					'title' => 'The Mountain', 
					'url' => 'http://vimeo.com/22439234/', 
					'type' => 'vimeo', 
					'videoid' => '22439234'
				),
				array(
					'title' => 'Amazing nature scenery', 
					'url' => 'http://www.youtube.com/watch?v=6v2L2UGZJAM', 
					'type' => 'youtube', 
					'videoid' => '6v2L2UGZJAM'
				)
			)
		);
		$rightargs['before_widget'] = '<div class="widget widget_video"><div class="inner">';
		the_widget('VideoFeed', $instance, $rightargs);
		unset($instance);
		$rightargs['before_widget'] = '<div class="widget widget_banners"><div class="inner">';
		$instance = array(
			'randomize' => '',
			'count' => '3',
			'title' => '',
			'banners' => array(
				'<a href="#"><img src="' . get_template_directory_uri()   . '/images/smt/banner260.gif" alt="" title="" /></a>',
				'<a href="#"><img src="' . get_template_directory_uri()   . '/images/smt/banner125.gif" alt="" title="" /></a>',
				'<a href="#"><img src="' . get_template_directory_uri()   . '/images/smt/banner125.gif" alt="" title="" /></a>'
			)
		);
		the_widget('Banners', $instance, $rightargs);
		unset($instance);
		$instance = array(
			'title' => 'Flickr',
			'userid' => '80789124@N02',
			'width' => '55'
		);
		$rightargs['before_widget'] = '<div class="widget widget_flickr"><div class="inner">';
		the_widget('Flickr', $instance, $rightargs);
		unset($instance);
		$rightargs['before_widget'] = '<div class="widget widget_tag_cloud"><div class="inner">';
		the_widget('WP_Widget_Tag_Cloud', $instance, $rightargs);
		$instance = array(
			'effect' => 'fadeIn'
		);
		$rightargs['before_widget'] = '<div class="widget widget_tabs"><div class="inner">';
		the_widget('Tabs', $instance, $rightargs);
	}
function show_footer_1_widgets() {
		global $footerargs;
		$instance = array(
			'title' => 'Calendar'
		);
		the_widget('WP_Widget_Calendar', $instance, $footerargs);
		
	}
	function show_footer_2_widgets() {
		global $footerargs;
		the_widget('WP_Widget_Tag_Cloud', $instance, $footerargs);
	}
	function show_footer_3_widgets() {
		global $footerargs;
		$instance= array(
			'width' =>'32',
			'title' => 'Social Profiles',
			'profiles' => array(
			array('id'=>'twitter', 'title' => 'Twitter', 'url' => 'http://twitter.com/', 'button' => get_template_directory_uri() . '/images/social-profiles/twitter.png'),
			array('id'=>'facebook','title' => 'Facebook', 'url' => 'http://facebook.com/', 'button' => get_template_directory_uri() . '/images/social-profiles/facebook.png'),
			array('id'=>'gplus','title' => 'Google Plus', 'url' => 'https://plus.google.com/', 'button' => get_template_directory_uri() . '/images/social-profiles/gplus.png'),
			array('id'=>'linkedin','title' => 'LinkedIn', 'url' => 'http://www.linkedin.com/', 'button' => get_template_directory_uri() . '/images/social-profiles/linkedin.png'),
			array('id'=>'email','title' => 'Email', 'url' => 'mailto:your@email.com', 'button' => get_template_directory_uri() . '/images/social-profiles/email.png')
			)
		);
		$footerargs['before_widget'] = '<div class="widget_social_profiles">';
		the_widget('SocialProfiles', $instance, $footerargs);
		$footerargs['before_widget'] = '<div>';
		unset($instance);
		$instance['title']='SMThemes';
		$instance['text']='Smart free Wordpress themes on SMT Framework';
		the_widget('WP_Widget_Text', $instance, $footerargs);
		
	}
	
	function smtheme_excerpt($args='', $postid=''){
		global $post, $SMTheme;
			if ((int)$postid==0)$p=$post;
			else $p=get_post($postid);
			parse_str($args, $i);
			$echo = isset($i['echo'])?true:false;
			if ( isset($i['maxchar']) ) {
				$maxchar=(int)trim($i['maxchar']);
				$content = $p->post_content;
				$content = apply_filters('the_content', $content);
			} else {
				if ( $p->post_excerpt ) {
					$content = $p->post_excerpt;
				} else {
					$content = $p->post_content;
					$content = apply_filters('the_content', $content);
					$maxchar=($SMTheme->get( 'layout','cuttxton' ))?$SMTheme->get( 'layout','cuttxt' ):0;
					$maxchar=(strpos($content, '<!--more-->'))?strpos(preg_replace('/<.*?>/', '', preg_replace('/<!--more-->/','@smt_readmore_tag',$content)), '@smt_readmore_tag'):$maxchar;
				}
			}
			if (!$maxchar||strlen(preg_replace('/<.*?>/', '', $content)) <= $maxchar) {
				if ($echo) print $content;
				else return $content;
			} else {
				preg_match_all('/(<.+?>)?([^<>]*)/s', $content, $lines, PREG_SET_ORDER);
				$total_length=0;
				$open_tags = array();
                $truncate = '';
				foreach ($lines as $line_matchings) {
                    if (!empty($line_matchings[1])) {
                        if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                            $pos = array_search($tag_matchings[1], $open_tags);
                            if ($pos !== false) {
                                unset($open_tags[$pos]);
                            }
                        } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                            array_unshift($open_tags, strtolower($tag_matchings[1]));
                        }
                        $truncate .= $line_matchings[1];
                    }
                    $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
					
                    if ($total_length+$content_length > $maxchar) {
						
                        $left = $maxchar - $total_length;
                        $entities_length = 0;
                        if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                            foreach ($entities[0] as $entity) {
                                if ($entity[1]+1-$entities_length <= $left) {
                                    $left--;
                                    $entities_length += strlen($entity[0]);
                                } else {
                                    break;
                                }
                            }
                        }
                        $truncate .= preg_replace('/(.*)\.[^\.]*$/s', "$1",mb_substr($line_matchings[2], 0, $left+$entities_length, 'utf-8'))."...";
                        break;
                    } else {
                        $truncate .= $line_matchings[2];
                        $total_length += $content_length;
                    }
                    if($total_length>= $maxchar) {
                        break;
                    }
                }
				
				foreach ($open_tags as $tag) {
                    $truncate .= '</' . $tag . '>';
                }
				$truncate=preg_replace('/<p([^>])*>(&nbsp;)?<\/p>/', '', $truncate);
				if ($echo) return print $truncate;
				else return $truncate;
			}
		return;
	}  
	function smt_menu($a) {
		$a=preg_replace("/<ul\sclass='children'>/", "<ul class='children'$2><div class='transparent'></div><div class='inner'>", $a);
		$a=preg_replace('/<ul\sclass="sub-menu">/', "<ul class='sub-menu'><div class='transparent'></div><div class='inner'>", $a);
		$a=preg_replace("/<\/ul>/", "</div></ul>", $a);
		return $a;
	}
	add_filter('wp_list_categories', 'smt_menu');
	add_filter('wp_list_pages', 'smt_menu');
	add_filter('wp_nav_menu_items', 'smt_menu');
	function block_main_menu() {
	global $SMTheme;
		?>
		 <div class="menu-primary-container">
			<ul class="menus menu-primary">
                <li <?php if(is_home() || is_front_page()) { ?>class="current_page_item"<?php } ?>><a href="<?php echo home_url(); ?>/"><?php echo $SMTheme->_(  'homelink' );?></a></li>
				<?php wp_list_categories('title_li=&'); ?>
			</ul>
		</div>
		<?php
	}
	
	function block_sec_menu() {
		?><div class='menu-topmenu-container'><ul class="menus">
				<?php wp_list_pages('title_li=&'); ?>
			</ul></div>
		<?php
	}
	function block_sec_menu_mobile() {
		?>
		<script>
			jQuery(document).ready(function(){
				jQuery('.menu-topmenu-container li').each(function() {
					jQuery('<option />', {
					'value':jQuery(this).find('a').attr('href'),
					'text':jQuery(this).find('a').html()
					}).appendTo(jQuery('#mobile-sec-menu'));
				});
			});
		</script>
		<?php
	}
	function block_main_menu_mobile() {
		?>
		<script>
			jQuery(document).ready(function(){
				jQuery('.menu-primary li').each(function() {
					jQuery('<option />', {
					'value':jQuery(this).find('a').attr('href'),
					'text':jQuery(this).find('a').html()
					}).appendTo(jQuery('#mobile-main-menu'));
				});
			});
		</script>
		<?php
	}
	
	function smt_mobile_menu($menu_name) {
		echo '<select class=\'mobile-menu\' id=\'mobile-'.$menu_name.'\'>';
		echo '<option value=\'#\'>Go to ...</option>';
		 if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
			if ($menu_items = wp_get_nav_menu_items($menu->term_id)) {
				$sub=array();
				foreach ($menu_items as $key => $menu_item ) {
					if ($menu_item->menu_item_parent==0) $sub[$menu_item->ID]=''; else $sub[$menu_item->ID]=$sub[$menu_item->menu_item_parent].'- ';
					echo '<option value=\''.$menu_item->url . '\'>' .$sub[$menu_item->ID]. $menu_item->title . '</option>';
				}
			} else {
				$func='block_'.preg_replace('/-/', '_', $menu_name).'_mobile';
				if (is_callable($func)) {
					$func();
				}
			}
		} else {
			$func='block_'.preg_replace('/-/', '_', $menu_name).'_mobile';
			if (is_callable($func)) {
				$func();
			}
		}
		echo '</select>';
	}
	
	
	
	
	
	
function addGMap($atts, $content = null) {

        extract(shortcode_atts(array( "addr" => '', "mzoom" => '16' ), $atts));
		$id='map_canvas'.mktime().rand(0,1000);
		return "
		<div class='googlemap'><div id='".$id."' style='width: 100%; height: 300px;'></div></div>
		<script>jQuery(function(){loadGMap('".$addr."', '".$id."', ".$mzoom.", '".$content."')});</script>
		";

}
add_shortcode('gmap', 'addGMap');

	
function addYouTube($atts, $content = null) {
        extract(shortcode_atts(array( "id" => '' ), $atts));
        return '<p style="text-align:center;display:block;overflow:hidden;clear:left">
        <a href="http://www.youtube.com/watch?v='.$id.'" target="_blank" alt="'.$id.'" class="youtube">
        <img src="http://img.youtube.com/vi/'.$id.'/0.jpg" width="90%" height="" />
        </a></p><script>loadYouTube();</script>';
}
add_shortcode('youtube', 'addYouTube');

function addVimeo($atts, $content = null) {
        extract(shortcode_atts(array( "id" => '' ), $atts));
		$videoinf = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$id.".php"));
        return '<p style="text-align:center;display:block;overflow:hidden;">
        <a href="http://vimeo.com/'.$id.'" target="_blank" alt="'.$id.'" class="vimeo">
        <img alt="" src="'.$videoinf[0]['thumbnail_large'].'" width="90%">
        </a></p><script>loadVimeo();</script>';
}
add_shortcode('vimeo', 'addVimeo');

function addTooltips($atts, $content = "") {
        extract(shortcode_atts(array( "tiptext" => '' ), $atts));
        return '<span class="tooltip" title="'.$tiptext.'">'.$content.'<span>'.$tiptext.'</span></span>';
}
add_shortcode('tooltip', 'addTooltips');

function add_smpanel() {
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
   if ( get_user_option('rich_editing') == 'true') {
     add_filter('mce_external_plugins', 'add_smpanel_tinymce_plugin');
     add_filter('mce_buttons_3', 'register_smpanel');
   }
}

add_action('init', 'add_smpanel');
function register_smpanel($buttons) {
   array_push($buttons, "youtube","vimeo","|","btns","cols","tooltips","highlights", "gmap");
   return $buttons;
}

function add_smpanel_tinymce_plugin($plugin_array) {
   $plugin_array['smpanel'] = get_template_directory_uri() .'/js/editor_plugin.js';
   return $plugin_array;
}

function my_refresh_mce($ver) {
  $ver += 3;
  return $ver;
}
add_editor_style( 'css/editor.css' );
add_filter( 'tiny_mce_version', 'my_refresh_mce');




if ( ! function_exists('tdav_css') ) {
	function tdav_css($wp) {
		$wp .= ','.get_template_directory_uri().'/css/shortcode.css ';
	return $wp;
	}
}
add_filter( 'mce_css', 'tdav_css' );
function smtheme_topic_count_text($count) {
	global $SMTheme;
	return sprintf( _n($SMTheme->_( 'altposts' ), $SMTheme->_( 'altpostss' ), $count), number_format_i18n( $count ) );
}
function smtheme_tag_cloud() {
	$args['topic_count_text_callback']='smtheme_topic_count_text';
	return $args;
}
add_filter('widget_tag_cloud_args', 'smtheme_tag_cloud');

add_filter( 'gettext', 'theme_change_comment_field_names', 20, 3 );
/**
 * Change comment form default field names.
 *
 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
 */
function theme_change_comment_field_names( $translated_text, $text, $domain ) {
global $SMTheme;
        switch ( $translated_text ) {

            case 'View all posts filed under %s' :

                $translated_text = $SMTheme->_( 'altcats' );
                break;

        }
    return $translated_text;
}



add_action('admin_init', 'single_content_layout', 1); 

function single_content_layout() {  
    add_meta_box( 'single_content_layout', 'Content layout', 'single_content_layout_func', 'post', 'side', 'high'  );  
	add_meta_box( 'single_content_layout', 'Content layout', 'single_content_layout_func', 'page', 'side', 'high'  );  
}

function single_content_layout_func($post) {
	global $APage;
		
		$layouts=$APage->PageOptions['layout']['content']['pagelayout']['params'];
		$selected = get_post_meta($post->ID, 'single_layout', 1);
	?>
		<select name='single_layout' style='width:100%'>
			<option value='0'>Default</option>
			<?php
			
			foreach ( $layouts as $key=>$value ) {
				echo "<option value='".$key."' ".selected( $selected, $key ).">".$value."</option>";
			}
			?>
		</select>
	<?php
}

add_action('save_post', 'single_content_layout_update', 0);  
function single_content_layout_update( $post_id ){  
	
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; 
	
    if ( !current_user_can('edit_post', $post_id) ) return false; 
	
	if( !isset($_POST['single_layout']) ) return false;
	
	$_POST['single_layout'] = (int)$_POST['single_layout'];
    update_post_meta($post_id, 'single_layout', $_POST['single_layout']);
	
	
    return $post_id;  
}  

?>