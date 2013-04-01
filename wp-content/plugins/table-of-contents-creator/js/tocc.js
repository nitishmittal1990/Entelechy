/* Table of Contents Creator v1.6.3 */

jQuery(document).ready(
	function(){
		
		jQuery(".tocc_options_header").click(
			function(){
				jQuery(".tocc_options_menu").slideToggle("fast");
				return false;
			}
		);

		jQuery(".tocc_summ_hide_all").click(
			function(){
				jQuery(".tocc_summ_body").slideUp("slow");
				jQuery(document).find(".tocc_options_menu").slideUp("fast");
				return false;
			}
		);
		
		jQuery(".tocc_summ_show_all").click(
			function(){
				jQuery(".tocc_summ_body").slideDown("slow");
				jQuery(document).find(".tocc_options_menu").slideUp("fast");
				return false;
			}
		);

		jQuery(".tocc_exp_hide_all").click(
			function(){
				jQuery(document).find(".tocc_expandable").slideUp("fast");
				jQuery(document).find(".tocc_options_menu").slideUp("fast");
				jQuery(document).find(".tocc_expand_icon").addClass("tocc_expand_up");
				return false;
			}
		);
		
		jQuery(".tocc_exp_show_all").click(
			function(){
				jQuery(document).find(".tocc_expandable").slideDown("fast");
				jQuery(document).find(".tocc_options_menu").slideUp("fast");
				jQuery(document).find(".tocc_expand_icon").removeClass("tocc_expand_up");
				return false;
			}
		);

		jQuery(".tocc_summ_icon").click(
			function(){
				jQuery(this).parent().find(".tocc_summ_body:first").slideToggle("slow");
				return false;
			}
		);
		
		jQuery(".tocc_summ_icon").hover(
			function(){
				if(jQuery(this).parent().find(".tocc_summ_body:first").css("display") == "none") {
					jQuery(this).addClass("tocc_summ_down");
				}
				else {
					jQuery(this).addClass("tocc_summ_up");
				}
			}
			,function(){
					jQuery(this).removeClass("tocc_summ_up tocc_summ_down");
			}
		);

		jQuery(".tocc_expand_icon").click(
			function(){
				jQuery(this).parent().find("ul:first").slideToggle("fast");
				jQuery(this).toggleClass("tocc_expand_up");
				return false;
			}
		);
		
		jQuery(".tocc_expand_icon").hover(
			function(){
				jQuery(this).addClass("tocc_hover");
			}
			,function(){
					jQuery(this).removeClass("tocc_hover");
			}
		);

		jQuery(".tocc_help_icon").click(
			function(){
				jQuery(this).parent().find(".tocc_help_text:first").slideToggle("fast");
				return false;
			}
		);
		
	}
);

