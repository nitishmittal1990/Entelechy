<?php
/*
Plugin Name: Excerpt Character Limiter
Plugin URI: http://www.trapella.it/2010/07/06/wordpress-excerpt-character-limit/
Description: Adds a character limit to post excerpt panels. Simple but effective!
Author: Matteo Trapella (Habla Webdesign)
Version: 1.0
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
	    $characters = $_POST['excerpt_limiter_characters'];  
	    update_option('excerpt_limiter_characters', $characters);  
	?>  
	<div class="updated"><p><strong><?php _e('Options saved.', 'Excerpt_Character_Limiter'  ); ?></strong></p></div>  
	<?php  
    else:  
        $characters = get_option('excerpt_limiter_characters'); 
	endif;
	?>        
	          
	          
	          
		<div class="wrap">  
		    <?php    echo "<h2>" . __( 'Excerpt Character Limiter Settings', 'Excerpt_Character_Limiter' ) . "</h2>"; ?>  
		    
		    <form name="excerpt_limiter_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
	  		  	<input type="hidden" name="excerpt_limiter_hidden" value="Y" /> 

		        <p><?php _e('Character Limit: ', 'Excerpt_Character_Limiter' ); ?>
		        	<input type="text" name="excerpt_limiter_characters" value="<?php echo $characters; ?>" size="20">
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
?>
	<script src="http://www.google.com/jsapi" type="text/javascript"></script>
	<script type="text/javascript" charset="utf-8">
		google.load("jquery", "1.3");
	</script>

	<script type="text/javascript">
       $(document).ready(function()
       {	
			var TEXTAREA = '#postexcerpt textarea';
			var TITLEBAR = '#postexcerpt h3.hndle span';
			var originalTitle = $(TITLEBAR).text();
			var charLimit = <?php echo get_option('excerpt_limiter_characters') ?>;
			
			function limitExcerptChars(maxLength)
			{
				var text = $(TEXTAREA).val(); 
				var textLength = text.length;
				
				if(textLength>maxLength)
				{
					$(TITLEBAR).html(originalTitle+' (0 <?php _e("characters remaining","Excerpt_Character_Limiter"); ?>)');
					$(TEXTAREA).val(text.substr(0,maxLength));
					return false;
				}
				else
				{
					$(TITLEBAR).html(originalTitle+' ('+ (maxLength-textLength) +' <?php _e("characters remaining","Excerpt_Character_Limiter"); ?>)');
					return true;
				}
			}
			
			$("#postexcerpt h3.hndle span").html(originalTitle+' ('+charLimit+' <?php _e("characters remaining","Excerpt_Character_Limiter"); ?>)');
			$('#postexcerpt textarea').keyup(function()
			{	
				limitExcerptChars(charLimit);
			})
		
			
       });
	</script>
<?php
}

?>