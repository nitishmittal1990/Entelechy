jQuery(document).ready( function($) {
	// Toggle switcher for all postbox
	$(".toggel-all").click(function(){
		if($(".postbox").hasClass("closed")) {
			$(".postbox").removeClass("closed");
		} else {
			$(".postbox").addClass("closed");
		};
		postboxes.save_state(pagenow);
				
		return false;
	});
	postboxes.add_postbox_toggles(pagenow);
	
	// Reset confirm
	$('.reset').click(function(){
		if (confirm("Are you sure you want to reset to default options?")) { 
			return true;
		} else { 
			return false; 
		}
	});
});