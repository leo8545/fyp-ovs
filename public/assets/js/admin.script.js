(function($) {
	("use strict");
	/**
	 * Adds menu open class to open menu list item
	 */
	const path = window.location.pathname;
	$("aside ul > li").each(function(v, i) {
		let menuLink = $(this)
			.find("a")
			.attr("href");
		if (path.indexOf(menuLink) > -1) {
			$(this).addClass("menu-open");
		}
	});
	$(document).ready(function() {});
})(jQuery);
