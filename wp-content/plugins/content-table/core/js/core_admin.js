/* =====================================================================================
*
*  Get the plugin Info
*
*/

function pluginInfo(id_div, url, plugin_name) {
	
	//POST the data and append the results to the results div
	rand = Math.floor(Math.random()*3000) ; 
	window.setTimeout(function() {
		var arguments = {
			action: 'pluginInfo', 
			plugin_name : plugin_name, 
			url : url
		} 
		
		jQuery.post(ajaxurl, arguments, function(response) {
			jQuery('#'+id_div).html(response);
		}).error(function(x,e) { 
			if (x.status==0){
				//Offline
			} else if (x.status==500){
				jQuery('#'+id_div).html("Error 500: The ajax request is retried");
				pluginInfo(id_div, url, plugin_name) ; 
			} else {
				jQuery('#'+id_div).html("Error "+x.status+": No data retrieved");
			}
		});
		
	}, rand) ; 
}

/* =====================================================================================
*
*  Get the core Info
*
*/

function coreInfo(md5, url, plugin_name, current_core, current_finger, author, src_wait, msg_wait) {
	
	//POST the data and append the results to the results div
	rand = Math.floor(Math.random()*3000) ; 
	window.setTimeout(function() {
		var arguments = {
			action: 'coreInfo', 
			plugin_name : plugin_name, 
			current_finger : current_finger, 
			current_core : current_core, 
			author : author, 
			md5 : md5, 
			src_wait : src_wait, 
			msg_wait : msg_wait, 
			url : url
		} 
		
		waitImg = "<p>"+msg_wait+"<img id='corePluginWait_"+md5+"' src='"+src_wait+"'></p>" ;
		
		jQuery('#corePlugin_'+md5).html(waitImg);

		jQuery.post(ajaxurl, arguments, function(response) {
			jQuery('#corePlugin_'+md5).html(response);
		}).error(function(x,e) { 
			if (x.status==0){
				//Offline
			} else if (x.status==500){
				jQuery('#corePlugin_'+md5).html("Error 500: The ajax request is retried");
				coreInfo(md5, url, plugin_name, current_core, current_finger, author); 
			} else {
				jQuery('#corePlugin_'+md5).html("Error "+x.status+": No data retrieved");
			}
		});
		
	}, rand) ; 
}

/* =====================================================================================
*
*  Update the core
*
*/

function coreUpdate(md5, url, plugin_name, current_core, current_finger, author, from, to, src_wait, msg_wait) {
	var arguments = {
		action: 'coreUpdate', 
		plugin_name : plugin_name, 
		current_finger : current_finger, 
		current_core : current_core, 
		author : author, 
		url : url,
		from : from, 
		md5 : md5, 
		src_wait : src_wait, 
		msg_wait : msg_wait, 
		to : to
	} 
	
	waitImg = "<p>"+msg_wait+"<img id='corePluginWait_"+md5+"' src='"+src_wait+"'></p>" ;
	jQuery('#corePlugin_'+md5).html(waitImg);
	
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery('#corePlugin_'+md5).html(response);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery('#corePlugin_'+md5).html("Error 500: The ajax request is retried");
			coreUpdate(md5, url, plugin_name, current_core, current_finger, author, from, to) ; 
		} else {
			jQuery('#corePlugin_'+md5).html("Error "+x.status+": No data retrieved");
		}
	});
	
	return false ; 
}


/* =====================================================================================
*
*  Change the version of the plugin
*
*/

function changeVersionReadme(md5, plugin) {
	jQuery("#wait_changeVersionReadme_"+md5).show();
	var arguments = {
		action: 'changeVersionReadme', 
		plugin : plugin
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery('body').append(response);
		jQuery("#wait_changeVersionReadme_"+md5).hide();
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery('body').append("Error 500: The ajax request is retried");
			changeVersionReadme(md5, plugin) ; 
		} else {
			jQuery('body').append("Error "+x.status+": No data retrieved");
		}
	});
}


/* =====================================================================================
*
*  Save the version and the readme txt
*
*/

function saveVersionReadme(plugin) {
	jQuery("#wait_save").show();
	readmetext = jQuery("#ReadmeModify").val() ; 
	versiontext = jQuery("#versionNumberModify").val() ; 
	var arguments = {
		action: 'saveVersionReadme', 
		readme : readmetext, 
		plugin : plugin,
		version : versiontext
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery('#readmeVersion').html(response);
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery('#readmeVersion').html("Error 500: The ajax request is retried");
			saveVersionReadme(plugin) ;
		} else {
			jQuery('#readmeVersion').html("Error "+x.status+": No data retrieved");
		}
	});
}

/* =====================================================================================
*
*  Save todoList
*
*/

function saveTodo(md5, plugin) {
	jQuery("#wait_savetodo_"+md5).show();
	jQuery("#savedtodo_"+md5).hide();
	textTodo = jQuery("#txt_savetodo_"+md5).val() ; 
	
	var arguments = {
		action: 'saveTodo', 
		textTodo: textTodo, 
		plugin : plugin
	} 
	//POST the data and append the results to the results div
	jQuery.post(ajaxurl, arguments, function(response) {
		jQuery("#wait_savetodo_"+md5).hide();
		if (response!="ok") {
			jQuery("#errortodo_"+md5).html(response);
		} else {
			jQuery("#savedtodo_"+md5).show();
			jQuery("#errortodo_"+md5).html("");
		}
	}).error(function(x,e) { 
		if (x.status==0){
			//Offline
		} else if (x.status==500){
			jQuery("#errortodo_"+md5).html("Error 500: The ajax request is retried");
			saveTodo(md5, plugin) ;  
		} else {
			jQuery("#errortodo_"+md5).html("Error "+x.status+": No data retrieved");
		}
	});
}


