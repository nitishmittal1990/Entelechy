
/* =====================================================================================
*
*  Add a new translation
*
*/

function translate_add(plug_param,dom_param,is_framework) {
	if (is_framework!="false") {
		var num = jQuery("#new_translation_frame option:selected").val() ;
		jQuery("#wait_translation_add_frame").show();
	} else {
		var num = jQuery("#new_translation option:selected").val() ;
		jQuery("#wait_translation_add").show();
	}	
	var arguments = {
		action: 'translate_add', 
		idLink : num,
		isFramework : is_framework,
		plugin : plug_param, 
		domain : dom_param
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_translation_add").fadeOut();
		jQuery("#wait_translation_add_frame").fadeOut();
		jQuery("#zone_edit").html(response);
		window.location = String(window.location).replace(/\#.*$/, "") + "#edit_translation";
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#zone_edit").html("Error 500: The ajax request is retried");
			translate_add(plug_param,dom_param,is_framework) ; 
		} else {
			jQuery("#zone_edit").html("Error "+x.status+": No data retrieved");
		}
	});    
}

/* =====================================================================================
*
*  Save the new translation
*
*/

function translate_create(plug_param,dom_param,is_framework, lang_param, nombre) {

	jQuery("#wait_translation_create").show();
	
	var result = new Array() ; 
	for (var i=0 ; i<nombre ; i++) {
		result[i] = jQuery("#trad"+i).val()  ;
	}
	
	var arguments = {
		action: 'translate_create', 
		idLink : result,
		isFramework : is_framework,
		name : jQuery("#nameAuthor").val(), 
		email : jQuery("#emailAuthor").val(), 
		lang : lang_param, 
		plugin : plug_param, 
		domain : dom_param
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_translation_create").fadeOut();
		jQuery("#zone_edit").html("");
		jQuery("#summary_of_translations").html(response);
		window.location = String(window.location).replace(/\#.*$/, "") + "#info";
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#summary_of_translations").html("Error 500: The ajax request is retried");
			translate_create(plug_param,dom_param,is_framework, lang_param, nombre) ; 
		} else {
			jQuery("#summary_of_translations").html("Error "+x.status+": No data retrieved");
		}
	});   
}

/* =====================================================================================
*
*  Modify a translation
*
*/

function modify_trans(plug_param,dom_param,is_framework,lang_param) {
	jQuery("#wait_translation_create").show();
	
	var arguments = {
		action: 'translate_modify', 
		isFramework : is_framework,
		lang : lang_param, 
		plugin : plug_param, 
		domain : dom_param
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_translation_create").fadeOut();
		jQuery("#zone_edit").html(response);
		window.location = String(window.location).replace(/\#.*$/, "") + "#edit_translation";
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#zone_edit").html("Error 500: The ajax request is retried");
			modify_trans(plug_param,dom_param,is_framework,lang_param) ; 
		} else {
			jQuery("#zone_edit").html("Error "+x.status+": No data retrieved");
		}
	});    
}

/* =====================================================================================
*
*  Save the modification of the translation
*
*/

function translate_save_after_modification (plug_param,dom_param,is_framework,lang_param, nombre) {

	jQuery("#wait_translation_modify").show();
	
	var result = new Array() ; 
	for (var i=0 ; i<nombre ; i++) {
		result[i] = jQuery("#trad"+i).val()  ;
	}
		
	var arguments = {
		action: 'translate_create', 
		idLink : result,
		isFramework : is_framework,
		name : jQuery("#nameAuthor").val(), 
		email : jQuery("#emailAuthor").val(), 
		lang : lang_param, 
		plugin : plug_param, 
		domain : dom_param
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_translation_modify").fadeOut();
		jQuery("#zone_edit").html("");
		jQuery("#summary_of_translations").html(response);
		window.location = String(window.location).replace(/\#.*$/, "") + "#info";
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#summary_of_translations").html("Error 500: The ajax request is retried");
			translate_save_after_modification (plug_param,dom_param,is_framework,lang_param, nombre) ; 
		} else {
			jQuery("#summary_of_translations").html("Error "+x.status+": No data retrieved");
		}
	});    
}

/* =====================================================================================
*
*  Send the modified translation
*
*/

function send_trans(plug_param,dom_param, is_framework, lang_param) {

	jQuery("#wait_translation_modify").show();
		
	var arguments = {
		action: 'send_translation', 
		lang : lang_param, 
		isFramework : is_framework,
		plugin : plug_param, 
		domain : dom_param
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_translation_modify").fadeOut();
		jQuery("#zone_edit").html(response);
		window.location = String(window.location).replace(/\#.*$/, "") + "#edit_translation";
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#zone_edit").html("Error 500: The ajax request is retried");
			send_trans(plug_param,dom_param, is_framework, lang_param)  ; 
		} else {
			jQuery("#zone_edit").html("Error "+x.status+": No data retrieved");
		}
	});    
}

/* =====================================================================================
*
*  Download a WP translation
*
*/

function download_trans() {

	var num = jQuery("#download_translation option:selected").val() ;
	jQuery("#wait_translation_download").show();
		
	var arguments = {
		action: 'download_translation', 
		lang : num, 
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_translation_download").fadeOut();
		jQuery("#download_zone").html(response);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#download_zone").html("Error 500: The ajax request is retried");
			download_trans()   ; 
		} else {
			jQuery("#download_zone").html("Error "+x.status+": No data retrieved");
		}
	});
}

/* =====================================================================================
*
*  Download a WP translation
*
*/

function download_trans_2(num) {
	jQuery("#wait_translation_download").show();
		
	var arguments = {
		action: 'download_translation', 
		lang : num, 
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_translation_download").fadeOut();
		jQuery("#download_zone").html(response);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#download_zone").html("Error 500: The ajax request is retried");
			download_trans_2(num)  ; 
		} else {
			jQuery("#download_zone").html("Error "+x.status+": No data retrieved");
		}
	});  
}

/* =====================================================================================
*
*  Download a WP translation
*
*/

function set_language() {
	jQuery("#wait_translation_set").show();
	var num = jQuery("#set_translation option:selected").val() ;

	var arguments = {
		action: 'set_translation', 
		lang : num, 
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		if (response=="") {
			location.reload();
		} else {
			jQuery("#wait_translation_set").fadeOut();
			jQuery("#set_trans_error").html(response);		
		}
		
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#set_trans_error").html("Error 500: The ajax request is retried");
			set_language() ; 
		} else {
			jQuery("#set_trans_error").html("Error "+x.status+": No data retrieved");
		}
	});     
}

/* =====================================================================================
*
*  Download a WP translation
*
*/

function get_languages() {
	jQuery("#wait_translation_get").show();

	var arguments = {
		action: 'update_languages_wp_init'
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
			jQuery("#info_get_trans").html(response);
			get_languages_2(0) ; 
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#info_get_trans").html("Error 500: The ajax request is retried");
			get_languages() ; 
		} else {
			jQuery("#info_get_trans").html("Error "+x.status+": No data retrieved");
		}
	});      
}

/* =====================================================================================
*
*  Download a WP translation
*
*/

function get_languages_2(numero) {

	var arguments = {
		action: 'update_languages_wp_list', 
		num: numero
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
			res = response.split(",") ; 
			progressBar_modifyProgression(Math.floor(res[1]/res[2]*100));
			progressBar_modifyText(res[0]+ " ("+Math.floor(res[1]/res[2]*100)+"%)");
			if (res[1]==res[2]) {
				jQuery("#info_get_trans").html("");
				download_trans_2("") ; // To refresh
			} else {
				get_languages_2(res[1]) ;
			}
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#info_get_trans").html("Error 500: The ajax request is retried");
			get_languages_2(numero) ; 
		} else {
			jQuery("#info_get_trans").html("Error "+x.status+": No data retrieved");
		}
	});        
}

/* =====================================================================================
*
*  Import a translation
*
*/

function importTranslation(path1, path2) {
	var arguments = {
		action: 'importTranslation', 
		path1: path1, 
		path2: path2
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		window.location = String(window.location);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			importTranslation(path1, path2) ; 
		} else {
			//nothing
		}
	});   
}


/* =====================================================================================
*
*  Delete a translation
*
*/

function deleteTranslation(path1) {
	var arguments = {
		action: 'deleteTranslation', 
		path1: path1
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		window.location = String(window.location);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			deleteTranslation(path1) ; 
		} else {
			//nothing
		}
	});     
}

/* =====================================================================================
*
*  See modification of a translation
*
*/

function seeTranslation(path1, path2) {
	var arguments = {
		action: 'seeTranslation', 
		path1: path1, 
		path2: path2
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#console_trans").html(response);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#console_trans").html("Error 500: The ajax request is retried");
			seeTranslation(path1, path2) ; 
		} else {
			jQuery("#console_trans").html("Error "+x.status+": No data retrieved");
		}
	});       
}

/* =====================================================================================
*
*  See modification of a translation
*
*/

function mergeTranslationDifferences(path1, path2) {
	var md5 = [] ; 
	jQuery('input:checkbox:checked').each(function(){
		if (this.name.indexOf("new_")==0) {
    		md5.push( this.name );
    	}
	});


	md5_to_replace = md5.join(',') ; 
	var arguments = {
		action: 'mergeTranslationDifferences', 
		md5: md5_to_replace, 
		path1: path1, 
		path2: path2
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		if (response=="ok") 
			window.location = String(window.location);
		else
			jQuery("#console_trans").html(response);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#console_trans").html("Error 500: The ajax request is retried");
			mergeTranslationDifferences(path1, path2) ; 
		} else {
			jQuery("#console_trans").html("Error "+x.status+": No data retrieved");
		}
	});       
}

/* =====================================================================================
*
*  Merge translation files
*
*/

function mergeTranslation(path1, path2) {
	var md5_to_replace = "" ; 
	
	var arguments = {
		action: 'mergeTranslationDifferences', 
		md5: md5_to_replace, 
		path1: path1, 
		path2: path2
	} 
	
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		if (response=="ok") 
			window.location = String(window.location);
		else
			jQuery("#console_trans").html(response);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#console_trans").html("Error 500: The ajax request is retried");
			mergeTranslation(path1, path2) ; 
		} else {
			jQuery("#console_trans").html("Error "+x.status+": No data retrieved");
		}
	});      
}