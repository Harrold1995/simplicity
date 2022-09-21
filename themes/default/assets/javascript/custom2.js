/*!
 * Scripts
 */
head.ready(function() {
	(function(globals){
		"use strict";
		globals.GLOB = {};
	}(this));
	//var $ = jQuery.noConflict();
	var 
		html_tag = $('html'),
		body_tag = $('body'),
		
		nav_id = $('#nav'),
		skip_id = $('#skip'),
		top_id = $('#top'),	
		
		check_a = $('.check-a'),
		date_input = $('input[type="date"], input.date'),
		form_search = $('.form-search'),
		list_square = $('.list-square'),
		table_c = $('.table-c'),
		table_tag = $('table:not(.table-c)'),
		popup_tag = $('[class^="popup"]'),
		select_tag = $('select'),

		form_children = $('form > *:not(fieldset), fieldset > *')
	;
	var Default = {
		utils : {
			links : function(){
				$('a[rel*=external]').on('click',function(e){
					e.preventDefault();
					window.open($(this).attr('href'));						  
				});
				skip_id.find('a').attr('aria-hidden',true).on('focus',function(){
					$(this).attr('aria-hidden',false);
				}).on('blur',function(){
					$(this).attr('aria-hidden',true);
				});
				
				$('a.print').on('click',function(){ window.print(); return false; });
			},
			mails : function(){
				$('.email:not(:input, div)').each(function(index){
					var em = $(this).text().replace('//','@').replace(/\//g,'.');
					$(this).text(em).attr('href','mailto:'+em);
				});
			},
			forms : function(){
				form_search.find('label:not(.hidden) + :input:not(select,button)').each(function(){
					$(this).attr('placeholder',$(this).parent().children('label').text()).parent().children('label').addClass('hidden').attr('aria-hidden',true);
				});
				
				form_children.each(function(k,v){ $(v).css('z-index',(form_children.length-k)); });

				// $(':checkbox, :radio').each(function () {
				// 	if ($(this).is('[checked]')) {
				// 		$(this).prop('checked', true).parent('label').addClass('active');
				// 	} else {
				// 		$(this).prop('checked', false).removeAttr('checked');
				// 	}
				// });
				// check_a.add(table_d.find('.check')).add(table_c.find('.check')).add(list_input).add(list_choose).add(table_e.find('.check')).find('label').each(function () {
				// 	$(this).addClass($(this).children(':checkbox, :radio').attr('type'));
				// }).children(':checkbox, :radio').after('<div class="input"></div>').addClass('hidden').attr('aria-hidden', true).on('click', function () {
				// 	if ($(this).is(':radio')) {
				// 		$(this).parents('p, ul:first').find('label').removeClass('active');
				// 	}
				// 	$(this).parent('label').toggleClass('active');
				// });
/*        // duplicate in custom.js
				$('input[type="checkbox"], input[type="radio"]').each(function(){ 
					console.log("checked");
					if($(this).is('[checked]'))
					   $(this).prop('checked',true).parent('label').addClass('active'); 
					else $(this).prop('checked',false).removeAttr('checked'); });
					
				check_a.find('label').append('<div id="customCheck" class="input"></div>').each(function(){ $(this).addClass($(this).children('input').attr('type')); }).children('input').addClass('hidden').attr('aria-hidden',true).on('click',function(){
					if($(this).parent().hasClass('radio')) { 
						$(this).parents('p, ul:first').find('label').removeClass('active');
					}
					$(this).parent('label').toggleClass('active'); 
					console.log("checked");

				});	 */
				
				/*select_tag.each(function(){
					$(this).wrap('<span class="select"></span>');
					if($(this).is('[class]')){
						$(this).parent().addClass($(this).attr('class'));	
					}
				});*/
			},
			top : function(){
				top_id.append('<a href="./" class="menu" role="button" aria-controls="mobile" aria-expanded="false" data-target="#mobile"></a>').after('<nav id="mobile" aria-expanded="false" focusable="false" aria-hidden="true"></nav><div id="shadow"></div>');
				var mobile_id = $('#mobile');
				nav_id.children().clone().appendTo(mobile_id);
				$('#shadow').add(top_id.children('.menu')).add(nav_id.find(':header')).on('click',function(){ 
					html_tag.each(function(){
						if($(this).is('.menu-active')){
							$(this).removeClass('menu-active');	
							mobile_id.attr('focusable',false).attr('aria-hidden',true).add(top_id.children('.menu')).attr('aria-expanded',false);
						} else {
							$(this).addClass('menu-active');
							mobile_id.attr('focusable',true).attr('aria-hidden',false).add(top_id.children('.menu')).attr('aria-expanded',true);	
						}
					});
					return false;
				});	
				mobile_id.each(function(){
					$(this).find(':header').remove();
					$(this).find('span.hidden').parents('li').addClass('has-no');
				});
			},
			resize : function(){
				$(window).on('resize',function(){
					table_tag.each(function(){
						$(this).find(list_square).each(function(){ $(this).parent('.has-list-square').css('width',$(this).outerWidth()); });
						$(this).find('tr.active + tr.details').prev().children('td:first-child').each(function(){
							$(this).children('.bg2').css('height',$(this).parent().outerHeight()+$(this).parent().next('.details').outerHeight()).css('width',$(this).parents('table').outerWidth());
						});
						$(this).parent('.table-wrapper').each(function(){
							$(this).css('max-height',$(window).height()-$(this).offset().top-30-$(this).next().outerHeight());
							console.log("resize1");
						});
						$(this).find('.bg').each(function(){
							$(this).css('width',$(this).parents('tr:first').outerWidth());
						});
					});
					$('.has-table-c').find('td:first-child .bg3').each(function(){
						$(this).css('width',$(this).parents('table:first').outerWidth());
					});
				});
			},
			responsive : function(){
				var 
				desktop_hide = $('.desktop-hide'),
				desktop_only = $('.desktop-only'),
				
				tablet_hide = $('.tablet-hide'),
				tablet_only = $('.tablet-only'),				
				
				mobile_hide = $('.mobile-hide'),
				mobile_only = $('.mobile-only');
				
				enquire.register('screen and (min-width: 1001px)',function(){ 
					desktop_only.add(tablet_hide).add(mobile_hide).removeAttr('aria-hidden focusable');
					desktop_hide.add(tablet_only).add(mobile_only).attr('aria-hidden',true).attr('focusable',false);
				}).register('screen and (min-width: 761px) and (max-width: 1000px)',function(){
					desktop_hide.add(tablet_only).add(mobile_hide).removeAttr('aria-hidden focusable');
					desktop_only.add(tablet_hide).add(mobile_only).attr('aria-hidden',true).attr('focusable',false);
				}).register('screen and (max-width: 760px)',function(){
					desktop_hide.add(tablet_hide).add(mobile_only).removeAttr('aria-hidden focusable');
					desktop_only.add(tablet_only).add(mobile_hide).attr('aria-hidden',true).attr('focusable',false);
				});	
				if(!$.browser.mobile){
					date_input.attr('type','text').parent().addClass('is-date');
				} else {
					date_input.attr('type','date').parent().addClass('is-date');
				}
			},

			// tables : function(){
			// 	table_c.each(function(){
			// 		$(this).addClass('mobile-hide').clone().removeClass('mobile-hide').addClass('mobile-only').insertAfter($(this));
			// 		$(this).next('.mobile-only').wrap('<div class="table-c-wrapper"></div>');
			// 		$(this).DataTable({
			// 			scrollY: $(window).height()-$(this).prev().offset().top-$(this).prev().outerHeight()-164-22,
			// 			scrollCollapse: true,
			// 			ordering: false,
			// 			paging: false,
			// 			searching: false,
			// 			responsive: true,
			// 			initComplete: function(settings, json) {
			// 				if($(this).is('.a')){
			// 					$(this).parents('.dataTables_wrapper').addClass('has-table-c mobile-hide a').find('td:first-child').each(function(){
			// 						$(this).append('<div class="bg3" style="width:'+$(this).parents('table:first').outerWidth()+'px;"></div>')
			// 					});
			// 				} else {
			// 					$(this).parents('.dataTables_wrapper').addClass('has-table-c mobile-hide');
			// 				}
			// 			}
			// 		});
			// 	})
			// },
			miscellaneous : function(){
				$('i[class^="icon-"]').attr('aria-hidden',true);
				console.log("miscellaneous running");
				table_tag.attr('tabindex',-1).each(function(){
					$(this).find(list_square).each(function(){ $(this).parent('td').addClass('has-list-square').css('width',$(this).outerWidth()); });
					$(this).find('a i').parent('a').addClass('has-icon');
					$(this).find('table').addClass('inside');
					if(!$(this).is('.inside')){
						if($(this).is('.table-b')){
							$(this).wrap('<div class="table-b-wrapper" tabindex="-1"><div class="table-wrapper" tabindex="-1"></div></div>');
						} else {
							$(this).wrap('<div class="table-wrapper tabindex="-1"></div>');	
						}
					}
					$(this).parent('.table-wrapper').each(function(){
						if($(this).is(':last-child')){
							$(this).after('<div></div>');	
						}
						$(this).css('max-height',$(window).height()-$(this).offset().top-30-$(this).next().outerHeight());
						console.log("resize2");
					});
					$(this).find('tr.active + tr.details').prev().children('td:first-child').each(function(){
						$(this).append('<div class="bg2"></div>').children('.bg2').css('height',$(this).parent().outerHeight()+$(this).parent().next('.details').outerHeight()).css('width',$(this).parents('table').outerWidth());
					});
					$(this).find('tr > td:first-child').append('<div class="bg"></div>').children('.bg').each(function(){
						$(this).css('width',$(this).parents('tr:first').outerWidth());
					});
				});
				html_tag.addClass('theme-r');


			}
		},
		ie : {
			css : function() {
				if(html_tag.is('.lt-ie11')){ $('input[placeholder], textarea[placeholder]').placeholder(); }
				if(html_tag.is('.lt-ie9')){
					body_tag.append('<p class="lt-ie9">Your browser is ancient! Please <a target="_blank" href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>').css('padding-top','28px');
					$(':last-child').addClass('last-child');
				}
			}
		}

	};

	//Default.utils.links();
	//Default.utils.mails();
	Default.utils.forms();
	//Default.utils.miscellaneous();
	//Default.utils.resize();
	//Default.utils.top();
	//Default.utils.responsive();
	//Default.utils.tables();
	//Default.ie.css();

	//Feedback
	/* $.get('feedback/getSupportEmails', (data) => {
		const dataJSON = JSON.parse(data);

		Feedback({
			url:'feedback/sendEmail',
			supportEmails: dataJSON.supportEmails
		});
	}); */
});

/*!*/