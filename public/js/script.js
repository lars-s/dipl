$(".nav li a").on("click", function() {
	$("li.active").removeClass("active");
	$(this).parent("li").addClass("active");
});


$(".ccbox.mainwrapper").on("click", ".recommendations-wrapper", function() {
	window.location.href = $(this).data('link') ;
})