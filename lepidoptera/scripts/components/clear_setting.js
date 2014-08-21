jQuery(document).ready(function($){
	$('.clear').on('click', function(){
		$(this).siblings('input').val('');
		return false;
	});
});