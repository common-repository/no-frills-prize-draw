document.set_to_post_id = null; //If you're having privilege issues uploading new images to media library, try changin this id to 1, 10, 100 or any other number

(function ($, root, undefined) {
	
	$(function () {
		
		'use strict';


		// Uploading files
		var file_frame;
		var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id


		//selecting thumbnails
		jQuery('.img-button').bind('click', function( event ){

		    event.preventDefault();
		    openMedia();

		});


		function openMedia(){
			// If the media frame already exists, reopen it.
		    if ( file_frame ) {
		      // Set the post ID to what we want
		      file_frame.uploader.uploader.param( 'post_id', document.set_to_post_id );
		      // Open frame
		      file_frame.open();
		      return;
		    } else {
		      // Set the wp.media post id so the uploader grabs the ID we want when initialised
		      wp.media.model.settings.post.id = document.set_to_post_id;
		    }

		    // Create the media frame.
		    file_frame = wp.media.frames.file_frame = wp.media({
		      title: jQuery( this ).data( 'uploader_title' ),
		      button: {
		        text: jQuery( this ).data( 'uploader_button_text' ),
		      },
		      multiple: true
		    });

		    // When an image is selected, run a callback.
		    file_frame.on( 'select', function() {

		      // We set multiple to false so only get one image from the uploader
		      //attachment = file_frame.state().get('selection').first().toJSON();
		      var selection = file_frame.state().get('selection');

			  selection.map( function( attachment ) {

			      attachment = attachment.toJSON();

			      createThumbnail(attachment.id);

			  });
			  	      
		      // Restore the main post ID
		      wp.media.model.settings.post.id = wp_media_post_id;

		    });

		    // Finally, open the modal
		    file_frame.open();
		}


		function createThumbnail(id){

			      var data = {

						'action': 'get_prize_image',

						'id': id

					};

					$('#prize_img').val(id);

					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					jQuery.post(ajaxurl, data, function(response) {

						if(response) {

							$(".album_thumbnail").remove();
							$(".prize-title").remove();

							$('.thumbnailer').prepend("<div class='prize-title'>Prize Image<br><br></div><div class='album_thumbnail' data-id='"+id+"'>"+response+"</div>");

						}

					});

		  }

		  
		  // Restore the main ID when the add media button is pressed
		  jQuery('a.add_media').on('click', function() {
		   // wp.media.model.settings.post.id = wp_media_post_id;
		  });


			
	});
	
})(jQuery, this);

		  //also piggyback a force delete call in edit page
          function forceconfdel(){
                jQuery('#nfpd_prize_draw_delete_form').submit();
          }