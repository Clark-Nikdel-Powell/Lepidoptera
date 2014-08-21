jQuery(document).ready(function($) {
	
	var $template_class = ".handle_template";
	var $container_class = ".handle_container";
	var $field_class = ".handle_input";
	var $add_class = ".handle_add";
	var $remove_class = ".handle_remove";
	var $wrap_class = ".handle_wrapper";

	function $create_template($value) {
		var $template = $($template_class).html();
		var $count = $($field_class).size();

		$($container_class).append($template);
		var $id = $($container_class + " " + $field_class).last().attr("id");

		$($container_class + " " + $field_class).last().val($value);
		$($container_class + " " + $field_class).last().attr("id",$id + ($count-1));
	}

	function $remove_template($selector) {
		$($selector).parent($wrap_class).remove();
	}
	
	$("body").delegate($remove_class, "click", function() { $remove_template(this); });
	$($add_class).on("click",function() { $create_template(''); });

});