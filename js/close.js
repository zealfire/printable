function ($) {
	Drupal.behaviors.print = {
		attach: function(context) {
			$(window).load(function() {
				window.print();
			})
		}
	}
}(jQuery);