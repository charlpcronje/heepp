$(document).ready( function() {
	// Hide all sub-folders at startup
	$(".php-file-tree").find("ul").hide();
	
	// Expand/collapse on click
	$(".pft-directory a").click( function() {
		$(this).parent().find("ul:first").slideToggle("medium");
		if( $(this).parent().attr('className') === "pft-directory") {
			return false;
        }
	});
});
