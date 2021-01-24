$(document).ready(function() {
	var max_fields      = 10; //maximum input boxes allowed
	var wrapper   		= $(".input_fields_wrap"); //Fields wrapper
	var add_button      = $(".add_field_button"); //Add button ID
	var parent_element = 'p';
	var html = '<' + parent_element + '>' + '<textarea class="form-control" name="videos[]"></textarea><a href="#" class="remove_field float-right">Retirer</a>' + '</' + parent_element + '>';

	$(wrapper).append(html);// add firsst html block
	var x = 1; //initlal text box count
	$(add_button).click(function(e){ //on add input button click
		e.preventDefault();
		if(x < max_fields){ //max input box allowed
			x++; //text box increment
			$(wrapper).append(html); //add input box
		}
	});
	
	$(wrapper).on("click",".remove_field", function(e){ 
		e.preventDefault(); 
		$(this).parent(parent_element).remove();
		x--;
	})
});