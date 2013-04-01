jQuery( document ).ready( function( $ ) {
	
	//Refresh the profile image thumbnail
	function mt_ajax_thumbnail_refresh() {
		var post_id = jQuery( "#metronet_post_id" ).val();
		$.post( ajaxurl, { 
				action: 'metronet_get_thumbnail', 
				post_id: post_id, 
			}, 
			function( response ) {
				jQuery( "#metronet-profile-image a" ).html( response.thumb_html );
				jQuery( "#metronet-pte" ).html( response.crop_html );
			},
			'json'
		);
	};

	$('#metronet-upload-link a, #metronet-profile-image a.add_media').on( "click", function(e) {
		//Assign the default view for the media uploader
		var uploader = wp.media({
			state: 'featured-image',
			states: [ new wp.media.controller.FeaturedImage() ],
		});
		uploader.state('featured-image').set( 'title', metronet_profile_image.set_profile_text );
		
		//Create featured image button
		uploader.on( 'toolbar:create:featured-image', function( toolbar ) {
				this.createSelectToolbar( toolbar, {
					text: metronet_profile_image.set_profile_text
				});
			}, uploader );
		
		//For when the featured thumbnail is set
		uploader.mt_featured_set = function( id ) {
			var settings = wp.media.view.settings;
			$.post( ajaxurl, { 
					action: 'metronet_add_thumbnail', 
					post_id: settings.post.id, 
					user_id: jQuery( "#metronet_profile_id" ).val(), 
					thumbnail_id: id,
					_wpnonce: settings.post.nonce 
				}, 
				function( response ) {
					jQuery( "#metronet-profile-image a" ).html( response.thumb_html );
					jQuery( "#metronet-pte" ).html( response.crop_html );
				},
				'json'
			);
		};
		
		//For when the featured image is clicked
		uploader.state('featured-image').on( 'select', function() {
			var settings = wp.media.view.settings,
				selection = this.get('selection').single();

			if ( ! settings.post.featuredImageId )
				return;
			
			settings.post.featuredImageId = selection.id;
			uploader.mt_featured_set( selection ? selection.id : -1 );
		} );
				
		//For when the window is closed (update the thumbnail)
		uploader.on('escape', function(){
			mt_ajax_thumbnail_refresh();
		});
		
		//Open the media uploader
		uploader.open();
		return false;
	});
	
	//For when Thickbox is closed - Trac - http://core.trac.wordpress.org/ticket/19189#comment:24
	$(document).on('tb_unload', '#TB_window', function(e){
		mt_ajax_thumbnail_refresh();
	});	
} );