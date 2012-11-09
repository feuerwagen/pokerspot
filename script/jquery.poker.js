// jQuery Poker Plugin
//
// Version 0.1
//
// (C) Elias Müller
// 

/**
 * TODO:
 * - create poker tables for each table in list (div-element inside of #pokertable)
 * - poll tables, on which the current user is seated + currently viewed table -> update table and indicator in list
 * - exit polling + destroy table when user leaves 
 * - bind actions to buttons, keys (fold,check/call,bet/raise,join/leave)
 */
if(jQuery)( function() {
	$.extend($.fn, {
		poker: function(o, callback) {
			// Defaults
			if( o.menu == undefined ) return false;
			if( o.inSpeed == undefined ) o.inSpeed = 150;
			if( o.outSpeed == undefined ) o.outSpeed = 75;
			// 0 needs to be -1 for expected results (no fade)
			if( o.inSpeed == 0 ) o.inSpeed = -1;
			if( o.outSpeed == 0 ) o.outSpeed = -1;
			
			// Loop each poker table
			$(this).each( function() {
				var el = $(this);
				var offset = $(el).offset();
				// Add contextMenu class
				$('#' + o.menu).addClass('contextMenu');
				// Simulate a true right click
				$(this).mousedown( function(evt) {
					evt.stopPropagation();
					$(this).mouseup( function(e) {
						e.stopPropagation();
						var srcElement = $(this);
						$(this).unbind('mouseup');						
						if( evt.button == 2 ) {
							// Hide context menus that may be showing
							$(".contextMenu").hide();
							// Get this context menu
							var menu = $('#' + o.menu);

							if( el.hasClass('disabled') ) return false;
							if (o.onShow) o.onShow(srcElement);
							
							// Show the menu
							//$(document).unbind('click');
							$(menu).css({ top: y, left: x }).fadeIn(o.inSpeed);
							// Hover events
							$(menu).find('a').mouseover( function() {
								$(menu).find('LI.hover').removeClass('hover');
								$(this).parent().addClass('hover');
							}).mouseout( function() {
								$(menu).find('LI.hover').removeClass('hover');
							});
							
							// Keyboard
							var keys = function(e) {
								switch( e.keyCode ) {
									case 38: // up
										if( $(menu).find('li.hover').size() == 0 ) {
											$(menu).find('li:last').addClass('hover');
										} else {
											$(menu).find('li.hover').removeClass('hover').prevAll('li:not(.disabled)').first().addClass('hover');
											if( $(menu).find('li.hover').size() == 0 ) 
												$(menu).find('li:last').addClass('hover');
										}
									break;
									case 40: // down
										if( $(menu).find('li.hover').size() == 0 ) {
											$(menu).find('li:first').addClass('hover');
										} else {
											$(menu).find('LI.hover').removeClass('hover').nextAll('LI:not(.disabled)').first().addClass('hover');
											if( $(menu).find('LI.hover').size() == 0 ) $(menu).find('LI:first').addClass('hover');
										}
									break;
									case 13: // enter
										$(menu).find('li.hover a').trigger('click');
									break;
									case 27: // esc
										$(document).trigger('click');
									break
								}
							};
							$(document).keydown(keys);
							
							// When items are selected
							var exec = function(evt) {
								//$(document).unbind('click').unbind('keypress');

								$(menu).fadeOut(o.outSpeed);

								// Callback
								if(callback) ret = callback( $(this), $(srcElement), {x: x - offset.left, y: y - offset.top, docX: x, docY: y} );
								evt.stopPropagation();
								if (ret === true) return true;
								return false;
							};
							$('#' + o.menu).find('a').unbind('click');
							$('#' + o.menu).find('li:not(.disabled) a').click(exec);
							
							// Hide bindings
							var hide = function() {
								$(document).unbind('click', hide).unbind('keypress', keys);
								$(menu).fadeOut(o.outSpeed);
								return false;
							};
							setTimeout( function() { // Delay for Mozilla
								$(document).click(hide);
								/*$(document).click( function() {
									//$(document).unbind('click').unbind('keypress');
									$(menu).fadeOut(o.outSpeed);
									return false;
								});*/
							}, 0);
						}
					});
				});				
			});
			return $(this);
		},
	});
})(jQuery);