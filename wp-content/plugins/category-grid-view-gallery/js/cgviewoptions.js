/* Author : Anshul Sharma (contact@anshulsharma.in) */
$j = jQuery.noConflict();
jQuery(document).ready(function($j) {

$j(function() {
         $j("a#settings").click(function() {
            $j("div#settings").show();
			$j("div#cg_sc").hide();
			$j("a[id='settings']").addClass("active");
			$j("a[id='shortcode']").removeClass("active");
            return false;
         });
	
      });   
$j(function() {
         $j("a#shortcode").click(function() {
            $j("div#settings").hide();
			$j("div#cg_sc").show();
			$j("a[id='settings']").removeClass("active");
			$j("a[id='shortcode']").addClass("active");
            return false;
         });
	
      }); 

$j(function() {
  $j( "input[type=radio]" ).fix_radios();
  $j("input[name='color_scheme']").change(function(){
    if ($j("input[name='color_scheme']:checked").val() == 'light'){
      alert($j("input[name='color_scheme']:checked").val());
        } else {alert($j("input[name='color_scheme']:checked").val()); }
      });
});

$j('form[name=cg_sc] input[name=submit_shortcode]').click(function(){
var newtext = "[cgview id=1";
	if (document.cg_sc.id.value != "") {
		newtext = "[cgview id=" + document.cg_sc.id.value ;
		}
	if (document.cg_sc.name.value != "") {
		newtext += " name=" + document.cg_sc.name.value ;
		}
	if (document.cg_sc.num.value != "") {
		newtext += " num=" + document.cg_sc.num.value ;
		}
	if (document.cg_sc.offset.value != "") {
		newtext += " offset=" + document.cg_sc.offset.value ;
		}
	if (document.cg_sc.orderby_init.value != "1") {
		newtext += " orderby=" + document.cg_sc.orderby.value ;
		}
	if (document.cg_sc.order_init.value != "1") {
		newtext += " order=" + $j("input[name='order']:checked").val() ;
		}
	if (document.cg_sc.tags.value != "") {
		newtext += " tags=" + document.cg_sc.tags.value ;
		}
	if (document.cg_sc.excludeposts.value != "") {
		newtext += " excludeposts=" + document.cg_sc.excludeposts.value ;
		}
	if ((document.cg_sc.sizew.value != "") && (document.cg_sc.sizeh.value != "")) {
		newtext += " size=" + document.cg_sc.sizew.value + "x" + document.cg_sc.sizeh.value;
		}
	if (document.cg_sc.quality.value != "") {
		newtext += " quality=" + document.cg_sc.quality.value ;
		}
	if (document.cg_sc.customfield.value != "") {
		newtext += " customfield=" + document.cg_sc.customfield.value ;
		}
	if (document.cg_sc.title_init.value != "1") {
		newtext += " showtitle=" + document.cg_sc.showtitle.value ;
		}
	if (document.cg_sc.paginate.value != "") {
		newtext += " paginate=" + document.cg_sc.paginate.value ;
		}
	if (!document.cg_sc.lightbox.checked) {
		newtext += " lightbox=0" ;
		}
	if (document.cg_sc.title.value != "") {
		newtext += " title=" + document.cg_sc.title.value ;
		}
	newtext += "]";
	document.cg_sc.cgview_shortcode.value = newtext;
}); 

$j('input[name=reset_shortcode]').click(function(){
document.cg_sc.id.value = "";
document.cg_sc.name.value= "";
document.cg_sc.num.value = "";
document.cg_sc.offset.value= "";
document.cg_sc.orderby.value = "date";
document.cg_sc.orderby_init.value= "1";
$j('input:radio[name=order]')[0].checked = true;
document.cg_sc.order_init.value= "1";
document.cg_sc.tags.value = "";
document.cg_sc.sizew.value = "";
document.cg_sc.sizeh.value = "";
document.cg_sc.sizes.value = "thumbnail";
document.cg_sc.excludeposts.value = "";
document.cg_sc.quality.value = "";
document.cg_sc.customfield.value = "";
document.cg_sc.cgview_shortcode.value = "[cgview id=1]";
document.cg_sc.showtitle.value = "hover";
document.cg_sc.title_init.value= "1";
document.cg_sc.paginate.value = "";
document.cg_sc.lightbox.checked = true;
document.cg_sc.title.value = "";
}); 

$j('form[name=cg_sc] select[name=orderby]').change(function(){
   document.cg_sc.orderby_init.value="0";
      });
$j("form[name=cg_sc] input[name='order']").change(function(){
   document.cg_sc.order_init.value="0";
      });
$j('form[name=cg_sc] select[name=showtitle]').change(function(){
   document.cg_sc.title_init.value="0";
      });

$j('form[name=cg_sc] select[name=sizes]').change(function(){
	var imgsize = document.cg_sc.sizes.value
	switch (imgsize){
		case "thumbnail":	$j('input:text[name=sizew]').val("140");
							$j('input:text[name=sizeh]').val("140");
							break;
		case "medium":		$j('input:text[name=sizew]').val("180");
							$j('input:text[name=sizeh]').val("180");
							break;
		case "large":		$j('input:text[name=sizew]').val("300");
							$j('input:text[name=sizeh]').val("300");
							break;
		default :	$j('input:text[name=sizew]').val("");
							$j('input:text[name=sizeh]').val("");
	}
      });
 
});

