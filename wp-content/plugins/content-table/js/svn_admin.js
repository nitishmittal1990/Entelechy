/* =====================================================================================
*
*  Toggle folder
*
*/

function folderToggle(num) {
	jQuery.fn.fadeThenSlideToggle = function(speed, easing, callback) {
	  	if (this.is(":hidden")) {
			return this.slideDown(speed, easing).fadeTo(speed, 1, easing, callback);
	  	} else {
			return this.fadeTo(speed, 0, easing).slideUp(speed, easing, callback);
	  	}
	};
	
	
	jQuery("#folder_"+num).fadeThenSlideToggle(500);
	
	if (jQuery("#minus_"+num).is(":visible")) {
		jQuery("#minus_"+num).hide() ; 		
		jQuery("#plus_"+num).show() ; 
	} else {
		jQuery("#minus_"+num).show() ; 		
		jQuery("#plus_"+num).hide() ; 
	}

	return false ; 
}

function diffToggle(num) {
	
	jQuery.fn.fadeThenSlideToggle = function(speed, easing, callback) {
	  	if (this.is(":hidden")) {
			return this.slideDown(speed, easing).fadeTo(speed, 1, easing, callback);
	  	} else {
			return this.fadeTo(speed, 0, easing).slideUp(speed, easing, callback);
	  	}
	};
	
	
	jQuery("#diff_"+num).fadeThenSlideToggle(500);

	return false ; 
}

function showSvnPopup(md5, plugin, type) {
	jQuery("#wait_svn_"+md5).show();
	var arguments = {
		action: 'svn_show_popup', 
		plugin : plugin, 
		sens : type
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery('body').append(response);
		jQuery("#wait_svn_"+md5).hide();
	});
}

function svnToRepo(plugin) {
	jQuery("#wait_svn").show();
	jQuery("#confirm_to_svn").hide() ; 
	jQuery('.toModify').attr('disabled', true);
	jQuery('.toPut').attr('disabled', true);
	jQuery('.toDelete').attr('disabled', true);
	jQuery('.toPutFolder').attr('disabled', true);
	jQuery('.toDeleteFolder').attr('disabled', true);
	
	list = new Array() ; 	
	
	tick = jQuery('.toModify') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'modify')) ; 
		}
	}
	tick = jQuery('.toPut') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'add')) ; 
		}
	}
	tick = jQuery('.toDelete') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'delete')) ; 
		}
	}
	tick = jQuery('.toPutFolder') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'add_folder')) ; 
		}
	}
	tick = jQuery('.toDeleteFolder') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'delete_folder')) ; 
		}
	}
	
	var arguments = {
		action: 'svn_to_repo', 
		plugin: plugin, 
		comment: jQuery("#svn_comment").val(), 
		files: list
	} 

	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_svn").hide();
		jQuery("#console_svn").html(response);
	});    
}


function svnToLocal(plugin) {
	jQuery("#wait_svn").show();
	jQuery("#confirm_to_svn").hide() ; 
	jQuery('.toModify').attr('disabled', true);
	jQuery('.toPut').attr('disabled', true);
	jQuery('.toDelete').attr('disabled', true);
	jQuery('.toPutFolder').attr('disabled', true);
	jQuery('.toDeleteFolder').attr('disabled', true);
	
	list = new Array() ; 	
	
	tick = jQuery('.toModify') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'modify')) ; 
		}
	}
	tick = jQuery('.toPut') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'add')) ; 
		}
	}
	tick = jQuery('.toDelete') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'delete')) ; 
		}
	}
	tick = jQuery('.toPutFolder') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'add_folder')) ; 
		}
	}
	tick = jQuery('.toDeleteFolder') ; 
	for (var i=0 ; i<tick.length ; i++) {
		if (tick.eq(i).attr('checked')=='checked') {
			list.push(new Array(tick.eq(i).val(), 'delete_folder')) ; 
		}
	}
	
	var arguments = {
		action: 'svn_to_local', 
		plugin: plugin, 
		files: list
	} 

	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_svn").hide();
		jQuery("#console_svn").html(response);
	});    
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
	});    
}