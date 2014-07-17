jQuery(document).ready(function(){

var $ = jQuery;

$('.foursquare_settings').on('click' , '.foursquare_delete' , function(){

	$('.add_foursquare').detach();

	$(this).closest('tr').detach();

	$('.foursquare_settings .form-table tr:last-child th').append('<a href="#" class="add_foursquare">Add Location +</a>');

	return false;

});

});
