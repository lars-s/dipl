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