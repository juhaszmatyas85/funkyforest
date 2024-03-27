(function ($) {
	'use strict';

	jQuery(function ($) {
		var message = {
			fadeInTime: 600,
			fadeOuttime: 600,
			howLongToShow: pisol_fsnw.howLongToShow,
			showContinues: pisol_fsnw.showContinues,
			show: function () {
				message.hide_progress();
				var is_blank = jQuery('.pisol-bar-container').data('blank');
				if (jQuery('.pisol-bar-container').data('blank') != true) {
					if (message.show_bar_for_user()) {
						jQuery('.pisol-bar-container').fadeIn(message.fadeInTime);

						if (jQuery('.pisol-bar-container').length && jQuery('.pisol-shortcode-bar-container').length == 0) {
							jQuery('body').addClass('pisol-bar-open');
						}

						if (jQuery('.pisol-shortcode-bar-container').length) {
							jQuery('body').addClass('pisol-shortcode-bar-open');
						}

						if (!message.showContinues) {
							setTimeout(function () { message.hide(); }, message.howLongToShow);
						}
					}
				} else {
					jQuery('.pisol-bar-container').data('blank', 'false');
				}
			},

			hide: function (close_clicked) {
				if (typeof close_clicked == undefined) {
					close_clicked = false;
				}
				message.show_progress();
				jQuery('.pisol-bar-container').fadeOut(message.fadeOuttime);
				jQuery('body').removeClass('pisol-bar-open');
				jQuery('body').removeClass('pisol-shortcode-bar-open');
				if (pisol_fsnw.bar_close_behaviour == 'close_completely' && close_clicked) {
					pisol_fsnw_createCookie('pisol_fsnw_disable_bar_completely', 'close_completely');
				}
			},

			show_trigger: function () {
				jQuery(document).on('pi_fsnw_show_trigger', function () {
					message.show();
				})
			},

			show_bar_for_user: function () {
				if (pisol_fsnw.bar_close_behaviour == 'close_completely') {
					if (pisol_fsnw_readCookie('pisol_fsnw_disable_bar_completely') == 'close_completely') {
						return false;
					}
				}
				return true;
			},

			close_button: function () {
				jQuery('.pisol-fsnw-close').click(function () {
					message.hide(true);
				});
			},

			hide_progress: function () {
				jQuery('#pi-progress-circle').fadeOut();
			},

			show_progress: function () {

				jQuery('#pi-progress-circle').fadeIn();

			},

			close_progress: function () {
				jQuery('#pi-progress-circle').on('click', function () {
					message.hide_progress();
					message.show();
				});
			},

			isJson: function (item) {
				item = typeof item !== "string"
					? JSON.stringify(item)
					: item;

				try {
					item = JSON.parse(item);
				} catch (e) {
					return false;
				}

				if (typeof item === "object" && item !== null) {
					return true;
				}

				return false;
			},

			update_cart: function () {
				// update total amount after click add_to_cart
				var parent = this;
				jQuery(document).ajaxComplete(function (event, jqxhr, settings) {
					var ajax_link = settings.url;

					if (ajax_link && ajax_link != 'undefined' && ajax_link.search(/wc-ajax=add_to_cart/i) >= 0 || ajax_link.search(/wc-ajax=remove_from_cart/i) >= 0 || ajax_link.search(/wc-ajax=get_refreshed_fragments/i) >= 0 || ajax_link.search(/wc-ajax=update_order_review/i) >= 0 || ajax_link.search(/admin-ajax\.php/i) >= 0) {

						if (ajax_link.search(/admin-ajax\.php/i) >= 0) {
							return;
						}

						jQuery.ajax({
							url: pisol_fsnw.ajax_url,
							cache: false,
							type: 'POST',
							dataType: 'json',
							data: {
								action: 'get_cart_fsnw'
							},
							success: function (response) {
								if (ajax_link.search(/wc-ajax=get_refreshed_fragments/i) >= 0) {
									if (message.isJson(response) && response.min_order !== false) {
										if (parseInt(response.total_percent) == 0) {
											return;
										}
									}
								}

								if (message.isJson(response) && response.min_order !== false) {
									var percent = response.percent > 100 ? 1 : (parseFloat(response.percent) / 100);

									message.markCompleted(percent);

									if (jQuery('#pi-progress-circle').length) {
										jQuery('#pi-progress-circle').circleProgress('value', percent);
									}

									jQuery(".pisol-bar-container").progressbar({
										value: (percent * 100)
									});

									jQuery(".pisol-bar-message").html(response.message_bar);
									parent.popup(response.message_bar, ajax_link, percent);
									message.show();

								} else {
									jQuery(".pisol-bar-message").html("");
									message.hide();
									message.hide_progress();
									message.clearPopupMsg();
								}

							}
						});
					}
				});
			},

			markCompleted: function (percent) {
				if (percent >= 1) {
					jQuery('.pisol-bar-container').addClass('requirement-completed');
				} else {
					jQuery('.pisol-bar-container').removeClass('requirement-completed');
				}
			},

			clearPopupMsg: function () {
				localStorage.removeItem("pisol_fsn_popup_msg");

			},

			isSameMessage: function (msg) {
				var stored_msg = localStorage.getItem('pisol_fsn_popup_msg');
				if (stored_msg == msg) {
					return true;
				} else {
					localStorage.setItem('pisol_fsn_popup_msg', msg);
					return false;
				}
			},

			popup: function (msg, ajax_link, percent) {

				if (ajax_link === undefined) {
					ajax_link = "";
				}

				if (message.isSameMessage(msg)) return;

				/**
				 * using this we disable the popup on refresh fragment event on non cart and checkout page
				 */
				if (ajax_link.search(/wc-ajax=get_refreshed_fragments/i) >= 0 && typeof pisol_fsnw_popup !== undefined && pisol_fsnw_popup.disable_refresh_fragment) {
					return;
				}

				if (typeof pisol_fsnw_popup !== undefined && pisol_fsnw_popup.enabled == true) {

					if (!message.popupCanOpenForThisPage()) return;

					if (percent >= 1) {
						var ext_class = "requirement-completed";
					} else {
						var ext_class = '';
					}

					jQuery.magnificPopup.open({
						items: {
							src: '<div class="pisol-popup ' + ext_class + ' ">' + msg + '</div>',
							type: 'inline'
						},
						closeOnBgClick: false
					});
				}
			},

			closePopup: function () {
				jQuery(document).on('click', ".mfp-close", function () {
					if (typeof pisol_fsnw_popup !== undefined && pisol_fsnw_popup.closing_option == 'close_for_page') {
						window.close_popup_for_this_page = true;
					}
				})
			},

			popupCanOpenForThisPage: function () {
				if (typeof pisol_fsnw_popup !== undefined && pisol_fsnw_popup.closing_option == 'close_for_page') {
					if (typeof window.close_popup_for_this_page != undefined && window.close_popup_for_this_page == true) {
						return false;
					}
				}
				return true;
			},

			onload: function () {
				var parent = this;
				jQuery(document).ready(function () {
					jQuery.ajax({
						url: pisol_fsnw.ajax_url,
						cache: false,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'get_cart_fsnw'
						},
						success: function (response) {

							if (message.isJson(response) && response.min_order !== false) {
								if (parseInt(response.total_percent) == 0) {
									return;
								}
							}


							if (message.isJson(response) && response.min_order !== false) {
								var percent = response.percent > 100 ? 1 : (parseFloat(response.percent) / 100);

								message.markCompleted(percent);

								if (jQuery('#pi-progress-circle').length) {
									jQuery('#pi-progress-circle').circleProgress('value', percent);
								}

								jQuery(".pisol-bar-container").progressbar({
									value: (percent * 100)
								});

								jQuery(".pisol-bar-message").html(response.message_bar);

								if (pisol_fsnw_popup.initial_load == true) {
									parent.popup(response.message_bar, ajax_link, percent);
								}

								message.show();

							} else {
								jQuery(".pisol-bar-message").html("");
								message.hide();
								message.hide_progress();
							}

						}
					});
				});
			}
		};
		message.show();
		message.onload();
		message.update_cart();
		message.close_button();
		message.close_progress();
		message.closePopup();
	});

	/**
	 * 
	 * recheck old shipping method and new shipping method 
	 * to make sure the old and new are same 
	 * if old and new shipping method are not same it reloads the shipping method 
	 */
	function shippingMethodRule() {

		$(document).ajaxComplete(function (event, jqxhr, settings) {
			var ajax_link = settings.url;
			if (ajax_link && ajax_link != "undefined" && ajax_link.search(/wc-ajax=update_order_review/i) >= 0) {
				if (jqxhr && jqxhr.responseJSON != undefined && jqxhr.responseJSON.fragments != undefined && jqxhr.responseJSON.fragments.old_method != undefined && jqxhr.responseJSON.fragments.new_method != undefined) {
					if (jqxhr.responseJSON.fragments.old_method != jqxhr.responseJSON.fragments.new_method) {
						jQuery("body").trigger("update_checkout");
					}
				}
			}
		});

	}

	jQuery(function ($) {
		if (pisol_fsnw.percent !== false) {
			var percent = pisol_fsnw.percent > 100 ? 1 : (parseFloat(pisol_fsnw.percent) / 100);
			jQuery('#pi-progress-circle').circleProgress({
				value: percent,
				size: pisol_fsnw.diameter,
				fill: {
					gradient: ["red", "orange"]
				}
			});

			$(".pisol-bar-container").progressbar({
				value: (percent * 100)
			});
		}

		shippingMethodRule();
	});

})(jQuery);

function pisol_fsnw_createCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	}
	else var expires = "";
	document.cookie = name + "=" + value + expires + "; path=/";
}

function pisol_fsnw_readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}