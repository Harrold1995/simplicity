/*!
 * Scripts
 */
/*global head, jQuery, enquire, window */
head.ready(function () {
	(function (globals) {
		"use strict";
		globals.GLOB = {};
	}(this));
	var
		html_tag = $('html'),
		body_tag = $('body'),

		nav_id = $('#nav'),
		skip_id = $('#skip'),
		top_id = $('#top'),

		accordion_a = $('.accordion-a'),
		check_a = $('.check-a'),
		checklist_a = $('.checklist-a'),
		cols_a = $('.cols-a'),
		cols_c = $('.cols-c'),
		date_input = $('input[type="date"], input.date'),
		input_search = $('.input-search'),
		form_charge = $('.form-charge'),
		form_children = $('form > *:not(fieldset), fieldset > *'),
		form_double = $('.form-double'),
		form_fixed = $('.form-fixed'),
		form_search = $('.form-search'),
		list_choose = $('.list-choose'),
		list_input = $('.list-input'),
		list_square = $('.list-square'),
		list_tree = $('.list-tree'),
		list_users = $('.list-users'),
		module_print = $('.module-print'),
		nav_a = $('.nav-a'),
		table_c = $('.table-c:not(.noscript)'),
		table_d = $('.table-d'),
		table_e = $('.table-e'),
		table_tag = $('table:not(.table-c, .table-e)'),
		popup_tag = $('[class^="popup"]'),
		select_tag = $('select'),
		tabs_class = $('[class*="tabs"]:not(.tabs-inner, .tabs-header)');
	var Default = {
		utils: {
			links: function () {
				$('a[rel*=external]').on('click', function (e) {
					e.preventDefault();
					window.open($(this).attr('href'));
				});
				skip_id.find('a').attr('aria-hidden', true).on('focus', function () {
					$(this).attr('aria-hidden', false);
				}).on('blur', function () {
					$(this).attr('aria-hidden', true);
				});

				$('a.print').on('click', function () {
					window.print();
					return false;
				});
			},
			mails: function () {
				$('.email:not(:input, div)').each(function () {
					var em = $(this).text().replace('//', '@').replace(/\//g, '.');
					$(this).text(em).attr('href', 'mailto:' + em);
				});
			},
			forms: function () {
				form_search.add(input_search).add(form_fixed).add(form_double).find('label:not(.hidden) + :input:not(select,button)').each(function () {
					$(this).attr('placeholder', $(this).parent().children('label').text()).parent().children('label').addClass('hidden').attr('aria-hidden', true);
				});

				form_children.each(function (k, v) {
					$(v).css('z-index', (form_children.length - k));
				});

				$(':checkbox, :radio').each(function () {
					if ($(this).is('[checked]')) {
						$(this).prop('checked', true).parent('label').addClass('active');
					} else {
						$(this).prop('checked', false).removeAttr('checked');
					}
				  	
				});
				
				check_a.add(table_d.find('.check')).add(table_c.find('.check')).add(list_input).add(list_choose).add(table_e.find('.check')).add(checklist_a).add(list_tree.find('.check')).find('label').each(function () {
					$(this).addClass($(this).children(':checkbox, :radio').attr('type'));
				}).children(':checkbox, :radio').after('<div class="input"></div>').addClass('hidden').attr('aria-hidden', true).on('click', function () {
					console.log($(this));
					if ($(this).is(':radio')) {
						$(this).parents('p, ul:first').find('label').removeClass('active');
					}
					$(this).parent('label').toggleClass('active');
				});

				/*select_tag.each(function () {
					$(this).wrap('<span class="select"></span>');
					if ($(this).is('[class]')) {
						$(this).parent().addClass($(this).attr('class'));
					}
				});*/

				form_fixed.append('<a class="close" href="./">Close</a>').children('a.close').on('click', function () {
					$(this).parent().slideUp();
					return false;
				});
			},
			top: function () {
				top_id.append('<a href="./" class="menu" role="button" aria-controls="mobile" aria-expanded="false" data-target="#mobile"></a>').after('<nav id="mobile" aria-expanded="false" focusable="false" aria-hidden="true"></nav><div id="shadow"></div>');
				var mobile_id = $('#mobile');
				nav_id.children().clone().appendTo(mobile_id);
				$('#shadow').add(top_id.children('.menu')).add(nav_id.find(':header')).on('click', function () {
					html_tag.each(function () {
						if ($(this).is('.menu-active')) {
							$(this).removeClass('menu-active');
							mobile_id.attr('focusable', false).attr('aria-hidden', true).add(top_id.children('.menu')).attr('aria-expanded', false);
						} else {
							$(this).addClass('menu-active');
							mobile_id.attr('focusable', true).attr('aria-hidden', false).add(top_id.children('.menu')).attr('aria-expanded', true);
						}
					});
					return false;
				});
				mobile_id.each(function () {
					$(this).find(':header').remove();
					$(this).find('span.hidden').parents('li').addClass('has-no');
				});
			},
			resize: function () {
				$(window).on('resize load', function () {
					table_tag.each(function () {
						$(this).find(list_square).each(function () {
							$(this).parent('.has-list-square').css('width', $(this).outerWidth());
						});
						$(this).find('tr.active + tr.details').prev().children('td:first-child').each(function () {
							$(this).children('.bg2').css('height', $(this).parent().outerHeight() + $(this).parent().next('.details').outerHeight()).css('width', $(this).parents('table').outerWidth());
						});
						$(this).parent('.table-wrapper').each(function () {
							if ($(this).is('.table-d-wrapper')) {
								$(this).css('max-height', $(window).height() - $(this).offset().top - 80 - $(this).next().outerHeight());
								console.log("resize3");
							} else {
								$(this).css('max-height', $(window).height() - $(this).offset().top - 30 - $(this).next().outerHeight());
								console.log("resize1");
							}
						});
						$(this).find('.bg').each(function () {
							$(this).css('width', $(this).parents('tr:first').outerWidth());
						});
					});
					$('.has-table-c').find('td:first-child .bg3').each(function () {
						$(this).css('width', $(this).parents('table:first').outerWidth());
					});
					$('.has-table-c').find('td:first-child > .shadow').each(function () {
						$(this).css('width', $(this).parents('tr').outerWidth());
					});
					popup_tag.each(function () {
						$(this).find(module_print).find('.inset').css('max-height', $(window).height() - 342);
					});
					cols_a.find(list_users).each(function () {
						$(this).css('max-height', $(window).height() - $(this).offset().top);
					});
					cols_c.find('header > .accordion-a').each(function () {
						$(this).css('max-height', $(window).height() - $(this).offset().top);
					});
				});
				$(window).on('load', function () {
					table_tag.each(function () {
						$(this).find('tr.active + tr.details').prev().children('td:first-child').each(function () {
							$(this).children('.bg2').css('height', $(this).parent().outerHeight() + $(this).parent().next('.details').outerHeight()).css('width', $(this).parents('table').outerWidth());
						});
					});
				});
			},
			responsive: function () {
				var
					desktop_hide = $('.desktop-hide'),
					desktop_only = $('.desktop-only'),

					tablet_hide = $('.tablet-hide'),
					tablet_only = $('.tablet-only'),

					mobile_hide = $('.mobile-hide'),
					mobile_only = $('.mobile-only');

				enquire.register('screen and (min-width: 1001px)', function () {
					desktop_only.add(tablet_hide).add(mobile_hide).removeAttr('aria-hidden focusable');
					desktop_hide.add(tablet_only).add(mobile_only).attr('aria-hidden', true).attr('focusable', false);
				}).register('screen and (min-width: 761px) and (max-width: 1000px)', function () {
					desktop_hide.add(tablet_only).add(mobile_hide).removeAttr('aria-hidden focusable');
					desktop_only.add(tablet_hide).add(mobile_only).attr('aria-hidden', true).attr('focusable', false);
				}).register('screen and (max-width: 760px)', function () {
					desktop_hide.add(tablet_hide).add(mobile_only).removeAttr('aria-hidden focusable');
					desktop_only.add(tablet_only).add(mobile_hide).attr('aria-hidden', true).attr('focusable', false);
				});
				if (!$.browser.mobile) {
					//date_input.attr('type', 'text').parent().addClass('is-date');
				} else {
					//date_input.attr('type', 'date').parent().addClass('is-date');
				}
			},
			popups: function () {
				popup_tag.semanticPopup();
				popup_tag.each(function () {
					$(this).find(module_print).find('.inset').css('max-height', $(window).height() - 342);
				});
			},
			tables: function () {

				var getScrollY = function (el) {
					if (el.is('.da.in-cols-d')) {
						return 270;
					} else if (el.is('.da.after-cols-d')) {
						return ($(window).height() - $(el).offset().top - 164 - 59);
					} else if (el.is('.dc')) {
						return $(window).height() - $(el).offset().top - 200;
					} else if (el.is('.e')) {
						return $(window).height() - $(el).offset().top - 310;
					} else {
						return $(window).height() - $(el).prev().offset().top - $(el).prev().outerHeight() - 164 - 22;
					}
				};

				table_c.find('tbody > tr').each(function () {
					var td = $(this).children('td[colspan]');
					if (td.length === 1)
						for (i in new Array((+td.attr('colspan') - 1)).fill()) {
							$(this).append('<td style="display:none"></td>');
						}
				});

				table_c.find('tbody tr td table').addClass('not-dt');

				table_c.each(function () {
					$(this).parents('.cols-d').find('.table-c').addClass('in-cols-d');
					$(this).prev('.cols-d').next().addClass('after-cols-d');
					$(this).addClass('mobile-hide').clone().removeClass('mobile-hide').addClass('mobile-only').insertAfter($(this));
					$(this).next('.mobile-only').wrap('<div class="table-c-wrapper mobile-only"></div>').each(function () {
						if ($(this).is('.a')) {
							$(this).parents('.table-c-wrapper').addClass('a');
						} else if ($(this).is('.b')) {
							$(this).parents('.table-c-wrapper').addClass('b');
						} else if ($(this).is('.c')) {
							$(this).parents('.table-c-wrapper').addClass('c');
						} else if ($(this).is('.e')) {
							$(this).parents('.table-c-wrapper').addClass('e');
						} else if ($(this).is('.dc')) {
							$(this).find('.shadow').each(function () {
								$(this).css('width', $(this).parents('tr').outerWidth());
								var iiF = false,
									tn = $(this).parents('tr').nextAll().filter(function (k, v) {
										if (iiF === true) {
											return false;
										}
										if (!$(v).hasClass('details')) {
											iiF = true;
											return false;
										}
										return true;
									});
								$(this).css('height', ($(this).parents('tr').outerHeight() * (tn.length + 1)) - 4);
							});
						}
						if ($(this).is('.text-center')) {
							$(this).parents('.table-c-wrapper').addClass('text-center');
						}
						$(this).find('.shadow').each(function () {
							$(this).css('width', $(this).parents('tr').outerWidth());
						});
					});

					$(this).DataTable({
						scrollY: getScrollY($(this)),
						scrollCollapse: true,
						ordering: false,
						paging: false,
						searching: false,
						responsive: true,
						initComplete: function () {
							if ($(this).is('.a')) {
								$(this).parents('.dataTables_wrapper').addClass('has-table-c mobile-hide a').find('td:first-child').each(function () {
									$(this).append('<div class="bg3" style="width:' + $(this).parents('table:first').outerWidth() + 'px;"></div>');
								});
							} else if ($(this).is('.b')) {
								$(this).parents('.dataTables_wrapper').addClass('has-table-c mobile-hide b');
							} else if ($(this).is('.c')) {
								$(this).parents('.dataTables_wrapper').addClass('has-table-c mobile-hide c');
							} else if ($(this).is('.d:not(.dc)')) {
								$(this).parents('.dataTables_wrapper').addClass('has-table-c mobile-hide d');
							} else if ($(this).is('.dc')) {
								$(this).parents('.dataTables_wrapper').addClass('has-table-c mobile-hide d dc');
								$(this).find('.shadow').each(function () {
									$(this).css('width', $(this).parents('tr').outerWidth());
									var iiF = false,
										tn = $(this).parents('tr').nextAll().filter(function (k, v) {
											if (iiF === true) {
												return false;
											}
											if (!$(v).hasClass('details')) {
												iiF = true;
												return false;
											}
											return true;
										});
									$(this).css('height', ($(this).parents('tr').outerHeight() * (tn.length + 1)) - 4);
								});
							} else if ($(this).is('.e')) {
								$(this).parents('.dataTables_wrapper').addClass('has-table-c mobile-hide e');
							} else {
								$(this).parents('.dataTables_wrapper').addClass('has-table-c mobile-hide');
							}
							if ($(this).is('.text-center')) {
								$(this).parents('.dataTables_wrapper').addClass('text-center');
							}
						}
					});
				});
				table_d.each(function () {
					$(this).DataTable({
						scrollY: $(window).height() - $(this).parents('.table-d-wrapper').next().outerHeight() - $(this).parents('.table-d-wrapper').offset().top - 120,
						scrollCollapse: true,
						ordering: false,
						paging: false,
						searching: false,
						responsive: true,
						initComplete: function () {
							$(this).parents('.table-d-wrapper').addClass('has-data-table');
						}
					});
				});
			},
			miscellaneous: function () {
				$('i[class^="icon-"]').attr('aria-hidden', true);
				table_tag.attr('tabindex', -1).each(function () {
					$(this).find(list_square).each(function () {
						$(this).parent('td').addClass('has-list-square').css('width', $(this).outerWidth());
					});
					$(this).find('a i').parent('a').addClass('has-icon');
					$(this).find('table').addClass('inside');
					if (!$(this).is('.inside')) {
						if ($(this).is('.table-b')) {
							$(this).wrap('<div class="table-b-wrapper" tabindex="-1"><div class="table-wrapper" tabindex="-1"></div></div>');
						} else {
							$(this).wrap('<div class="table-wrapper" tabindex="-1"></div>');
						}
					}
					if ($(this).is('.table-d')) {
						$(this).parent('.table-wrapper').addClass('table-d-wrapper');
					}
					$(this).parent('.table-wrapper').each(function () {
						if ($(this).is(':last-child')) {
							$(this).addClass('last-child').after('<div></div>');
						}
						if ($(this).is('.table-d-wrapper')) {
							$(this).css('max-height', $(window).height() - $(this).offset().top - 80 - $(this).next().outerHeight());
							console.log("resize5");
						} else {
							$(this).css('max-height', $(window).height() - $(this).offset().top - 30 - $(this).next().outerHeight());
							console.log("resize10");
						}
					});
					$(this).find('tr.active + tr.details').prev().children('td:first-child').each(function () {
						$(this).append('<div class="bg2"></div>').children('.bg2').css('height', $(this).parent().outerHeight() + $(this).parent().next('.details').outerHeight()).css('width', $(this).parents('table').outerWidth());
					});
					$(this).find('tr > td:first-child').append('<div class="bg"></div>').children('.bg').each(function () {
						$(this).css('width', $(this).parents('tr:first').outerWidth());
					});
				});
				table_e.each(function () {
					$(this).wrap('<div class="table-e-wrapper"></div>');
				});
				form_charge.find('a.btn, section > div p a').on('click', function () {
					$(this).closest('section').toggleClass('toggle');
					return false;
				});
				module_print.wrapInner('<div class="inset"></div>');
				nav_a.find(':header').wrapInner('<a href="./"></a>').children('a').on('click', function () {
					$(this).parents('.nav-a').toggleClass('toggle');
					return false;
				});
				cols_a.find(list_users).each(function () {
					$(this).css('max-height', $(window).height() - $(this).offset().top);
				});
				cols_c.find('header > .accordion-a').each(function () {
					$(this).css('max-height', $(window).height() - $(this).offset().top);
				});
				accordion_a.semanticAccordion().children(':header.toggle').next().show();
				tabs_class.semanticTabs();
				list_tree.each(function () {
					$(this).find('select').on('change', function () {
						$(this).parent().addClass('chosen');
					}).find('option[selected]:not([disabled])').parent().parent().addClass('chosen');
				});
			}
		},
		ie: {
			css: function () {
				if (html_tag.is('.lt-ie11')) {
					$('input[placeholder], textarea[placeholder]').placeholder();
				}
				if (html_tag.is('.lt-ie9')) {
					body_tag.append('<p class="lt-ie9">Your browser is ancient! Please <a target="_blank" href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>').css('padding-top', '28px');
					$(':last-child').addClass('last-child');
				}
			}
		}

	};

	Default.utils.links();
	Default.utils.mails();
	Default.utils.forms();
	Default.utils.miscellaneous();
	Default.utils.popups();
	Default.utils.resize();
	Default.utils.top();
	Default.utils.responsive();
	Default.utils.tables();
	Default.ie.css();
});

/*!*/
