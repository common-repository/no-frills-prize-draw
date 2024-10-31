
(function ($, root, undefined) {
	
	$(function () {
		
		'use strict';


		  	//Generate a slideshow shortcode
		  if($('#shortcode-generator').length>0){
		  	$('#shortcode-generator').on("click",function(){
		  		$(this).text("Generating...");

		  		var shortcode = "[nfpd_list_page entry_url='"+$('#entry_url').val()+"' display_as='"+$('#display_as').val()+"' ]";

		  		$(this).text("Generate another Shortcode");
		  		$('#shortcodes').html(shortcode);

		  	});
		  }

			
	});
	
})(jQuery, this);

