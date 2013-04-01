/* Author : Anshul Sharma (contact@anshulsharma.in) */

jQuery(document).ready(function($) {
	// To remove the css class for CSS fallback strategy
	$("div.cgview ul li div").removeClass("cgnojs");
	
						
  $("div.cgview li").mouseenter(function(){
  	var d = this.getElementsByTagName("div");
	if($(d[0]).attr('class')=='cgback hover'){
		$(d[0]).stop(true,true).animate({height: '35%'},200);
		$(d[1]).stop(true,true).animate({height: '32%'},200);
	}
	});
  $("div.cgview li").mouseleave(function(){
  	var d = this.getElementsByTagName("div");
	if($(d[0]).attr('class')=='cgback hover'){
		$(d[0]).stop(true,true).animate({height: '0px'},200);
		$(d[1]).stop(true,true).animate({height: '0px'},200);
	}
	
	});
  /*Events for colorbox (Fix for small popup window in colorbox due to delay in load of images*/
  $(".cgpost").colorbox({onComplete:function(){$.colorbox.resize();}});  
  
  /* Pagination */
  $(document).ready(function(){
		if(paginateVal){
		 $('ul#cg-ul').easyPaginate({
			step:paginateVal,
			nextprev:false,
			controls:'cg-page-controls'
	  });
	  }
							 });
 
});

