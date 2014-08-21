jQuery(document).ready(function($){

	$('.switchbox a').click(function(){

		var network_class = jQuery(this).attr('class');

		if (jQuery(this).hasClass('active')) {return false;}

		else {
			jQuery('.api_settings .active').removeClass('active');
			jQuery('.api_settings .'+network_class).addClass('active');
			return false;
		}
	});
});