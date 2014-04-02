// For removing error tags on forms
(function($){
	$(document).ready(function(){
		
		// Turn off any errors on form fields when focussed
		$('input, textarea, select').on('focus', function(){
			// Find an error for this field
			var name = $(this).attr('name');
			name = name.replace('[]', '');
			$('.error-' + name).animate({
				'opacity':0
			},{
				'complete':function(){
					$(this).css('display', 'none');
				}
			});
		});
		
		
	});
}(jQuery));