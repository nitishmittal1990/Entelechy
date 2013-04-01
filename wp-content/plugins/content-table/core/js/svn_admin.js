
/* =====================================================================================
*
*  Display a popup with all the information on which files has changed
*
*/

function showSvnPopup(md5, plugin) {
	jQuery("#wait_popup_"+md5).show();
	var arguments = {
		action: 'svn_show_popup', 
		plugin : plugin
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery('body').append(response);
		jQuery("#wait_popup_"+md5).hide();
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery('body').append("Error 500: The ajax request is retried");
			showSvnPopup(md5, plugin) ; 
		} else {
			jQuery('body').append("Error "+x.status+": No data retrieved");
		}
	});
}

/* =====================================================================================
*
*  Launch the execution of either the update or the checkout of the SVN repository
*
*/

function svnExecute(sens, plugin, random) {
	
	jQuery("#confirm_to_svn1").hide() ; 
	jQuery("#confirm_to_svn2").hide() ; 
	
	jQuery('.toModify'+random).attr('disabled', true);
	jQuery('.toPut'+random).attr('disabled', true);
	jQuery('.toDelete'+random).attr('disabled', true);
	jQuery('.toPutFolder'+random).attr('disabled', true);
	jQuery('.toDeleteFolder'+random).attr('disabled', true);
	
	list = new Array() ; 	
	
	tick = jQuery('.toModify'+random) ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'modify')) ; 
		}
	}
	tick = jQuery('.toPut'+random) ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'add')) ; 
		}
	}
	tick = jQuery('.toDelete'+random) ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'delete')) ; 
		}
	}
	tick = jQuery('.toPutFolder'+random) ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'add_folder')) ; 
		}
	}
	tick = jQuery('.toDeleteFolder'+random) ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'delete_folder')) ; 
		}
	}
	
	if (sens=="toRepo") {
		jQuery("#wait_svn1").show();
		
		arguments = {
			action: 'svn_to_repo', 
			plugin: plugin, 
			comment: jQuery("#svn_comment").val(), 
			files: list
		} 

		//POST the data and append the results to the results div
		jQuery.post(ajaxurl, arguments, function(response) {
			jQuery("#wait_svn").hide();
			jQuery("#console_svn").html(response);
		}).error(function(x,e) { 
			if (x.status==0){
				//Offline
			} else if (x.status==500){
				jQuery("#console_svn").html("Error 500: The ajax request is retried");
				svnExecute(sens, plugin, random)  ;
			} else {
				jQuery("#console_svn").html("Error "+x.status+": No data retrieved");
			}
		}); 
		
	} else if (sens=="toLocal") {
		
		jQuery("#wait_svn2").show();
		
		arguments = {
			action: 'svn_to_local', 
			plugin: plugin, 
			files: list
		} 

		//POST the data and append the results to the results div
		jQuery.post(ajaxurl, arguments, function(response) {
			jQuery("#wait_svn").hide();
			jQuery("#console_svn").html(response);
		}).error(function(x,e) { 
			if (x.status==0){
				//Offline
			} else if (x.status==500){
				jQuery("#console_svn").html("Error 500: The ajax request is retried");
				svnExecute(sens, plugin, random)  ;
			} else {
				jQuery("#console_svn").html("Error "+x.status+": No data retrieved");
			}
		});    	
	}
}




function repoToSvn(plugin) {
	jQuery("#wait_svn").show();
	jQuery("#svn_button").remove() ;
		
	var arguments = {
		action: 'repo_to_svn', 
		plugin : plugin, 
		comment : jQuery("#svn_comment").val()
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#svn_div").html(response);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#svn_div").html("Error 500: The ajax request is retried");
			repoToSvn(plugin) ; 
		} else {
			jQuery("#svn_div").html("Error "+x.status+": No data retrieved");
		}
	});    
}



