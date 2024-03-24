(function ($) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	jQuery(function () {

		/**
		 * Control tab
		 */
		$("#pi_fsnw_show_all").on("change", function () {
			if ($(this).is(":checked")) {
				$("#pi_control .row").not("#row_pi_fsnw_show_all").fadeOut();
			} else {
				$("#pi_control .row").not("#row_pi_fsnw_show_all").fadeIn()
			}
		});
		$("#pi_fsnw_show_all").trigger('change');

		jQuery("#pi_fsnw_persistent_bar").on("change", function () {
			if (jQuery(this).is(":checked")) {
				controlHowLongToShow(true);
				console.log(true);
			} else {
				controlHowLongToShow(false);
				console.log(false);
			}
		});

		jQuery("#pi_fsnw_persistent_bar").trigger('change');

		showOrhideWhenEnabled('#row_pi_fsnw_mobile_breakpoint', '#pi_fsnw_disable_mobile', 'show');

		showOrhideWhenEnabled('#row_pi_fsnw_linear_progress_color', '#pi_fsnw_linear_progress_enabled', 'show');
		showOrhideWhenEnabled('#row_pi_fsnw_linear_progress_background_color', '#pi_fsnw_linear_progress_enabled', 'show');
		showOrhideWhenEnabled('#row_pi_fsnw_circular_progress_image', '#pi_fsnw_circular_progress_enabled', 'show');
		showOrhideWhenEnabled('#row_pi_fsnw_progress_bar_thickness', '#pi_fsnw_linear_progress_enabled', 'show');
	});


	function controlHowLongToShow(state) {
		if (state) {
			jQuery("#row_pi_fsnw_how_long_to_show").fadeOut();
		} else {
			jQuery("#row_pi_fsnw_how_long_to_show").fadeIn();
		}
	}

	function showOrhideWhenEnabled(elements_to_hide_show, based_on_this_element, what_to_do = "show") {
		jQuery(based_on_this_element).on("change", function () {
			if (what_to_do == "show") {

				if (jQuery(this).is(":checked")) {
					jQuery(elements_to_hide_show).fadeIn();
				} else {
					jQuery(elements_to_hide_show).fadeOut();
				}
			}

			if (what_to_do == "hide") {

				if (jQuery(this).is(":checked")) {
					jQuery(elements_to_hide_show).fadeOut();
				} else {
					jQuery(elements_to_hide_show).fadeIn();
				}
			}
		});

		jQuery(based_on_this_element).trigger('change');
	}

})(jQuery);
