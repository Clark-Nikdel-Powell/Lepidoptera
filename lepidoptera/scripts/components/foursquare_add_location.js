
jQuery(document).ready(function(){

var $ = jQuery;

$('.foursquare_settings').on('click' , '.add_foursquare' , function(){

	$('.add_foursquare').detach();

	/*jshint multistr: true */
	$('.foursquare_settings .form-table tbody').append('\
		<tr valign="top">\
			<th scope="row"><a href="#" class="add_foursquare">Add Location +</a></th>\
			<td>\
				<input type="text" id="foursquare_url" name="foursquare_url[]" value="" style="width:70%" />\
				<button class="foursquare_delete"><i class="icon-trash"></i></button>\
			</td>\
		</tr>');

	return false;

});

});
