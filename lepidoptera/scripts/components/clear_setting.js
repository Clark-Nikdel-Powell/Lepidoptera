jQuery(document).ready(function(){

var $ = jQuery;

$('.clear').on('click', function(){

	$(this).siblings('input').val('');
	return false;

});

});