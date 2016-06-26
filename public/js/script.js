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

$(".rec-div.ccbox div.recommendation").on("click", function() {
	if ($(this).data("id") !== "") {
		window.location.href = $(this).data("id");
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
    		foo();
    		return false;
    	}
     });
});

function foo() {
	getRecommendationForAssignee(
			$("select.tech option:selected").text(),
			$("select.comp option:selected").text(),
			$(".taginputs").val()
	);
}

function getRecommendationForAssignee(technology, company, tags) {
	$.ajax({
		url: "get-recommandation-assignee",
		data: {technology: technology, company: company, tags: tags},
		method: "POST",
		success: function(data) {
			$(".rec-list *").remove();				
			
			$.each(data, function(key, value) {
				var plural = value["score"] > 1 ? "e" : "";
				$(".rec-list")
					.append("<ul class='rec' data-id='" + value["id"] + "'>" +
						"<li>" + value["fullname"] + "</li>" +
						"<li class='score'>" + value["score"] + " Punkt" + plural + "</li></ul>");
			})			

			if (!$(".rec-list *").size()) {
				$(".rec-list").append("<p>Keine Treffer!</p>");
			} else {
				$(".rec-list").prepend("<ul class='headline'><li>Name</li><li>Empfehlungswert</li></ul>");
			}
			
		}
	})
}

$(".rec-list").on("click", "ul", function() {
	var id = $(this).data("id");
	$("select[name='assignee']").focus();
	$("select[name='assignee'] option[value='" + id + "'").prop("selected", true);
})


$("select.comp, select.tech, input.taginputs").on("change", function() {
	foo();
})

$("body").on("click", "div.overlay", function() {
	if (!$(this).hasClass("unbreakable")) {
		$(this).remove();	
	}
})

$(".usesolution").on("click", function() {
	$(".overlay").show();
	$("#solution-form textarea").val("..");
	var id = $(this).next(".recommendation").data("id");
	
	$.ajax({
		url: "",
		data: {solution: id, useExistingSolution: true},
		method: "POST",
		success: function(data) {
			window.location.href = "../my-open-tasks";	
		}
	})
})

$("form#decline-form").on("submit", function(e) {
	var errorOverlay = "<div class='overlay'><div class='ccbox mainwrapper'><span class='close'>X</span>" ;

	if ($("textarea[name='problems']").val() == "") {
		e.preventDefault();
		$("textarea[name='problems']").addClass("error");
		errorOverlay += "<h2>Bitte eine Begründung eingeben!</h2>";
	}
	
	errorOverlay += "</div></div>";
	$("body").append(errorOverlay);
})

$("form#solution-form").on("submit", function(e) {
	var errorOverlay = "<div class='overlay'><div class='ccbox mainwrapper'><span class='close'>X</span>" ;

	if ($("textarea[name='solution']").val() == "") {
		e.preventDefault();
		$("textarea[name='solution']").addClass("error");
		errorOverlay += "<h2>Bitte eine Lösung eingeben!</h2>";
	}
	
	errorOverlay += "</div></div>";
	$("body").append(errorOverlay);
})

$("form#new").on("submit", function(e) {
	var errorOverlay = "<div class='overlay'><div class='ccbox mainwrapper'><span class='close'>X</span>" ;
	
	if ($("select[name='assignee'] option:selected").val() == -1) {
		e.preventDefault();
		$("select[name='assignee']").addClass("error");
		errorOverlay += "<h2>Bitte einen Mitarbeiter zuweisen!</h2>";
	}
	
	if ($("textarea[name='content']").val() == "") {
		e.preventDefault();
		$("textarea[name='content']").addClass("error");
		errorOverlay += "<h2>Bitte eine Beschreibung eingeben!</h2>";
	}
	
	if ($("input[name='tags']").val() == "") {
		e.preventDefault();
		$("input[name='tags']").addClass("error");
		errorOverlay += "<h2>Bitte Tags angeben!</h2>";
	}
	
	errorOverlay += "</div></div>";
	$("body").append(errorOverlay);
})

$("body").on("focus" ,".error", function() {
	$(this).removeClass("error");
})