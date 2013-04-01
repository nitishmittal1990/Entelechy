jQuery.fn.jcSimpleTOC = function(settings) {
                  settings = jQuery.extend({
                                tocTitle:"Table of contents",
                                scrollSpeed:1000,
                                itemTextLength: 40,
                                tocDefaultHeight: 300,
                                useFancyScroll:true,
                                position:'left', // bottomleft,bottomcenter,bottomright,topleft,topright,sticky,left,right
                                autoMinimize:false,
                                scrollingElement:'html,body', // this could be #ID or .class
                                tagsForTOC:'h1,h2,h3,h4',
                                imgPath:'',
                                easingType:'linear',
                                mainTag:'div.entry-content'
                             },settings);
                  jQuery("<div id='jcorgtoc' style='height:"+settings.tocDefaultHeight+"px;overflow:auto'><h1 class='tochead'>"+settings.tocTitle+"</h1><br></div>").appendTo('body');
                  jQuery("<div id='jcorgtocminimize' style='display:none'><img src='"+settings.imgPath+"/toc.png' title='Table of Contents'></div>").appendTo('body');
                  var h1=0,h2=0,h3=0,h4=0,index = 0;

                  var windowCenter = Math.max(0, ((jQuery(window).width() - jQuery("#jcorgtoc").outerWidth()) / 2) + jQuery(window).scrollLeft()) + "px";
                  var stickyleft = Math.max(0, (this.offset().left -10 - jQuery("#jcorgtoc").outerWidth() + jQuery(window).scrollLeft())) + "px";
                  var stickylefticon = Math.max(0, (this.offset().left - 150 + jQuery(window).scrollLeft())) + "px";
                  var windowCenterVertical = Math.max(0, ((jQuery(window).height() - jQuery("#jcorgtoc").outerHeight()) / 2) + jQuery(window).scrollTop()) + "px";

                  if(settings.position == 'bottomleft') 
                    jQuery("#jcorgtoc,#jcorgtocminimize").css({"left":'-5px','bottom':'-5px'});
                  else if(settings.position == 'bottomcenter') 
                    jQuery("#jcorgtoc,#jcorgtocminimize").css({"left":windowCenter,'bottom':'-5px'});
                  else if(settings.position == 'bottomright') 
                    jQuery("#jcorgtoc,#jcorgtocminimize").css({"right":'-5px','bottom':'-5px'});
                  else if(settings.position == 'right') 
                    jQuery("#jcorgtoc,#jcorgtocminimize").css({"right":'-5px','top':windowCenterVertical});
                  else if(settings.position == 'left') 
                    jQuery("#jcorgtoc,#jcorgtocminimize").css({"left":'-5px','top':windowCenterVertical});
                  else if(settings.position == 'topright') 
                    jQuery("#jcorgtoc,#jcorgtocminimize").css({"right":'-5px','top':'-5px'});
                  else if(settings.position == 'topleft') 
                    jQuery("#jcorgtoc,#jcorgtocminimize").css({"left":'-5px','top':'-5px'});
                  else if(settings.position == 'sticky') {
                    jQuery("#jcorgtoc,#jcorgtocminimize").css({"left":stickyleft,'top':windowCenterVertical});
                  }
                  else               
                    jQuery("#jcorgtoc,#jcorgtocminimize").css({"left":'-5px','bottom':'-5px'});

                  jQuery(this).children('h1,h2,h3,h4').each(function(i,o){
                    index = (o.nodeName.toUpperCase() == "H1"?  h1++: 
                              (o.nodeName.toUpperCase() == "H2"? h2++: 
                                (o.nodeName.toUpperCase() == "H3" ? h3++: 
                                   (o.nodeName.toUpperCase() == "H4" ? h4++ : 
                                      0
                                    ))));
                                    
                    jQuery("<div class='"+o.nodeName.toLowerCase()+"' node='"+o.nodeName+"' index='"+index+"' id='jcorg-"+o.nodeName.toLowerCase()+index+"'>&raquo; "+(o.innerHTML).substr(0,settings.itemTextLength) + ((o.innerHTML.length>settings.itemTextLength)?'..':'')+"</div>")
                            .click(function(){
                              jQuery(settings.scrollingElement).animate({scrollTop:jQuery(o).offset().top+'px'},settings.scrollSpeed,settings.easingType);
                              jQuery(".jcorgtocactive").removeClass('jcorgtocactive');
                              jQuery('#'+this.id).addClass('jcorgtocactive');
                            })
                            .appendTo("#jcorgtoc");
                  });
                  if(settings.useFancyScroll) {
                    if(jQuery("#jcorgtoc").outerHeight() > settings.tocDefaultHeight) {
                        jQuery('#jcorgtoc').jScrollPane();
                    }
                  }
                  if(settings.autoMinimize) {
                      jQuery("#jcorgtocminimize").show('slow');
                      jQuery("#jcorgtoc").hide('slow');
                      jQuery("#jcorgtocminimize").hover(function(){
                                                            jQuery("#jcorgtocminimize").hide();
                                                            jQuery("#jcorgtoc").show();                
                                                        });
                      jQuery("#jcorgtoc").mouseleave(function(){
                          jQuery("#jcorgtocminimize").show('slow');
                          jQuery("#jcorgtoc").hide();
                      })
                  }
        };