/*
 * 	Easy Paginate 1.0 - jQuery plugin
 *	written by Alen Grakalic	
 *	http://cssglobe.com/
 *
 *	Copyright (c) 2011 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
$j=jQuery.noConflict();
(function($j) {
		  
	$j.fn.easyPaginate = function(options){

		var defaults = {				
			step: 4,
			delay: 100,
			numeric: true,
			nextprev: true,
			auto:false,
			pause:4000,
			clickstop:true,
			controls: 'pagination',
			current: 'current' 
		}; 
		
		var options = $j.extend(defaults, options); 
		var step = options.step;
		var lower, upper;
		var children = $j(this).children();
		var cont = $j(this);
		var count = children.length;
		var obj, next, prev;		
		var page = 1;
		var timeout;
		var clicked = false;
		var oldh;
		
		function show(){
			clearTimeout(timeout);
			lower = ((page-1) * step);
			upper = lower+step;
			$j(cont).css("height",oldh);
			$j(children).each(function(i){
				var child = $j(this);				
				if(i>=lower && i<upper){child.fadeIn('fast');}
				else {child.fadeOut('fast');}
				if(options.nextprev){
					if(upper >= count) { next.fadeOut('fast'); } else { next.fadeIn('fast'); };
					if(lower >= 1) { prev.fadeIn('fast'); } else { prev.fadeOut('fast'); };
				};
			});	
			$j('li','#'+ options.controls).removeClass(options.current);
			$j('li[data-index="'+page+'"]','#'+ options.controls).addClass(options.current);
			
			if(options.auto){
				if(options.clickstop && clicked){}else{ timeout = setTimeout(auto,options.pause); };
			};
			setTimeout( function() {
				//$j(cont).css("height","100%");
				oldh = $j(cont).height();
			}, (1+count/50)*300);
		};
		
		function auto(){
			if(upper <= count){ page++; show(); }			
		};
		
		this.each(function(){ 
			
			obj = this;
			
			if(count>step){
				
				var pages = Math.floor(count/step);
				if((count/step) > pages) pages++;
				
				var ol = $j('<ol id="'+ options.controls +'"></ol>').insertAfter(obj);
				
				if(options.nextprev){
					prev = $j('<li class="prev">Previous</li>')
						.hide()
						.appendTo(ol)
						.click(function(){
							clicked = true;
							page--;
							show();
						});
				};
				
				if(options.numeric){
					for(var i=1;i<=pages;i++){
					$j('<li data-index="'+ i +'">'+ i +'</li>')
						.appendTo(ol)
						.click(function(){	
							clicked = true;
							page = $j(this).attr('data-index');
							show();
						});					
					};				
				};
				
				if(options.nextprev){
					next = $j('<li class="next">Next</li>')
						.hide()
						.appendTo(ol)
						.click(function(){
							clicked = true;			
							page++;
							show();
						});
				};
			
				show();
			};
		});	
		
	};	

})(jQuery);