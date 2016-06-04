$(".nav li a").on("click", function() {
	$("li.active").removeClass("active");
	$(this).parent("li").addClass("active");
});


$(".main.container").on("click", ".recommendations-wrapper, .linkbox", function() {
	var link = $(this).data('link');
	
	if (link) {
		window.location.href = $(this).data('link') ;	
	}
})

$("input.search").on("keypress", function() {
	if (event.keyCode == 38) {
		event.preventDefault();
		$("input[type='submit'").click();
    }
})
$(function() {
	if (!$("#tags").size()) {
		return false;
	}
    var availableTags = tags;
    function split( val ) {
    	return val.split( /,\s*/ );
    }
    function extractLast( term ) {
    	return split( term ).pop();
    }

    $( "#tags" )
    // don't navigate away from the field on tab when selecting an item
    .bind( "keydown", function( event ) {
    if ( event.keyCode === $.ui.keyCode.TAB &&
    		$( this ).autocomplete( "instance" ).menu.active ) {
    	event.preventDefault();
    	}
    })
    .autocomplete({
    	minLength: 0,
    	source: function( request, response ) {
    		// delegate back to autocomplete, but extract the last term
    		response( $.ui.autocomplete.filter(
    		availableTags, extractLast( request.term ) ) );
    	},
    	focus: function() {
    	// prevent value inserted on focus
    	return false;
    	},
    	select: function( event, ui ) {
    		var terms = split( this.value );
    		// remove the current input
    		terms.pop();
    		// add the selected item
    		terms.push( ui.item.value );
    		// add placeholder to get the comma-and-space at the end
    		terms.push( "" );
    		this.value = terms.join( ", " );
    		return false;
    	}
     });
});