<?php
/*
Plugin Name: Excerpt Character Limiter
Plugin URI: http://www.trapella.it/2010/english/wordpress-excerpt-character-limit
Description: Adds a character limit to post excerpt panels. Simple but effective!
Author: Matteo Trapella (Habla Webdesign)
Version: 1.1
Author URI: http://www.trapella.it/
*/


add_action('init', 'Excerpt_Character_Limiter_init');
add_action('admin_menu', 'Excerpt_Character_Limiter_options');
add_action('admin_head', 'Excerpt_Character_Limiter');


function Excerpt_Character_Limiter_init() 
{
    load_plugin_textdomain('Excerpt_Character_Limiter','wp-content/plugins/excerpt-character-limiter/languages');
}

function Excerpt_Character_Limiter_options() {  
    add_options_page("Excerpt Character Limiter", "Excerpt Character Limiter", 1, "Excerpt-Character-Limiter", "Excerpt_Character_Limiter_admin");  
}  
  
function Excerpt_Character_Limiter_admin() 
{ 
	if (!current_user_can('manage_options'))  {
	    wp_die( __('You do not have sufficient permissions to access this page.') );
	}	
	  
	if($_POST['excerpt_limiter_hidden'] == 'Y'):
	    $excerpt_characters = $_POST['excerpt_limiter_characters'];  
	    $title_characters = $_POST['title_limiter_characters'];  
	    update_option('excerpt_limiter_characters', $excerpt_characters);  
	    update_option('title_limiter_characters', $title_characters);  
	?>  
	<div class="updated"><p><strong><?php _e('Options saved.', 'Excerpt_Character_Limiter'  ); ?></strong></p></div>  
	<?php  
    else:  
        $excerpt_characters = get_option('excerpt_limiter_characters'); 
        $title_characters = get_option('title_limiter_characters'); 
	endif;
	?>        
	          
	          
	          
		<div class="wrap">  
		    <?php    echo "<h2>" . __( 'Excerpt Character Limiter Settings', 'Excerpt_Character_Limiter' ) . "</h2>"; ?>  
		    
		    <form name="excerpt_limiter_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
	  		  	<input type="hidden" name="excerpt_limiter_hidden" value="Y" /> 
				<p id="support">
					<?php _e('Do you like this plugin? support it by following me on Twitter or Facebook!', 'Excerpt_Character_Limiter' ); ?><br/>
					<a id="twitter" href="http://twitter.com/HablaWebdesign" target="_blank">Twitter</a>
					<a id="facebook" href="http://www.facebook.com/pages/Habla-Webdesign/340284405384" target="_blank">Facebook</a>
				</p>     	  		  	
		        <p><?php _e('Excerpt character Limit: ', 'Excerpt_Character_Limiter' ); ?>
		        	<input type="text" name="excerpt_limiter_characters" value="<?php echo $excerpt_characters; ?>" size="20">
		        	<small><?php _e('(Leave this field empty for unlimited characters)', 'Excerpt_Character_Limiter' ); ?></small>
		        </p> 
		        
		        <p><?php _e('Title character Limit: ', 'Excerpt_Character_Limiter' ); ?>
		        	<input type="text" name="title_limiter_characters" value="<?php echo $title_characters; ?>" size="20">
		        	<small><?php _e('(Leave this field empty for unlimited characters)', 'Excerpt_Character_Limiter' ); ?></small>
		        </p> 
		    	
		        <p class="submit">  
			        <input type="submit" name="Submit" value="<?php _e('Update Options', 'Excerpt_Character_Limiter' ) ?>" />  
		        </p>  
		    </form>  
		</div>

<?php
}  



function Excerpt_Character_Limiter() {
	$plugin_directory = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));	
?>
	<style>
		#support
		{
			font-size: 12px;
			font-weight: bold;
			background-color: #FFF;
			border: solid #AAA 3px;
			padding: 10px;
			margin-right: 20px;
			margin-bottom: 30px;
			width: 350px;
			height: 90px;
		}
		#twitter
		{
			margin-top: 5px;
			margin-right: 5px;
			width: 50px;
			height: 50px;
			float: left;
			text-indent: -99999px;
			background: url(<?php echo $plugin_directory?>images/Twitter.png) no-repeat 0px 0px;
		}
		#facebook
		{
			margin-top: 5px;
			margin-right: 5px;
			width: 50px;
			height: 50px;
			float: left;
			text-indent: -99999px;
			background: url(<?php echo $plugin_directory?>images/Facebook.png) no-repeat 0px 0px;
		}
	</style>
	<script type="text/javascript">
     jQuery(function($) 
     {
			var EXCERPT_INPUT = '#postexcerpt textarea';
			var EXCERPT_FEEDBACK = '#postexcerpt h3.hndle span';
			$(EXCERPT_FEEDBACK).append('<span class="ecl"></span>');
			EXCERPT_FEEDBACK = '#postexcerpt h3.hndle span span.ecl';
			
			var TITLE_INPUT = '#titlewrap #title';
			var TITLE_FEEDBACK = '#titlewrap';
			$(TITLE_FEEDBACK).append('<span class="ecl"></span>');
			TITLE_FEEDBACK = '#titlewrap span.ecl';
			
			
			var excerpt_charLimit = <?php if(get_option('excerpt_limiter_characters') != null)
			{
				echo get_option('excerpt_limiter_characters');
			}
			else
			{
				echo '0';
			}
			?>;
			var title_charLimit = <?php if(get_option('title_limiter_characters') != null)
			{
				echo get_option('title_limiter_characters');
			}
			else
			{
				echo '0';
			}
			?>;
			
			
			if(excerpt_charLimit>0)
			{
				$(EXCERPT_FEEDBACK).html(' ('+excerpt_charLimit+' <?php _e("characters remaining","Excerpt_Character_Limiter"); ?>)');
				$(EXCERPT_INPUT).keyup(function()
				{	
					limitExcerptChars(EXCERPT_INPUT, EXCERPT_FEEDBACK, excerpt_charLimit);
				})
			}
			
			if(title_charLimit>0)
			{
				$(TITLE_FEEDBACK).html(' ('+title_charLimit+' <?php _e("characters remaining","Excerpt_Character_Limiter"); ?>)');			
				$(TITLE_INPUT).keyup(function()
				{	
					 limitExcerptChars(TITLE_INPUT, TITLE_FEEDBACK, title_charLimit);
				})
			}
		
						
			
			
			function limitExcerptChars(source, feedback, maxLength)
			{
				var text = $(source).val(); 
				var textLength = text.length;
				var originalFeedback = $(feedback).html();
				if(textLength>maxLength)
				{
					$(feedback).html('(0 <?php _e("characters remaining","Excerpt_Character_Limiter"); ?>)');
					$(source).val(text.substr(0,maxLength));
					return false;
				}
				else
				{
					$(feedback).html('('+ (maxLength-textLength) +' <?php _e("characters remaining","Excerpt_Character_Limiter"); ?>)');
					return true;
				}
			}
			

       });
	</script>
<?php
}

?>