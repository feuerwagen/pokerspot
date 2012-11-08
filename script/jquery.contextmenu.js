// jQuery Context Menu Plugin
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
//
// More info: http://abeautifulsite.net/2008/09/jquery-context-menu-plugin/
//
// Terms of Use
//
// This plugin is dual-licensed under the GNU General Public License
//   and the MIT License and is copyright A Beautiful Site, LLC.
//
if(jQuery)( function() {
	$.extend($.fn, {
		contextMenu: function(o, callback) {
			// Defaults
			if( o.menu == undefined ) return false;
			if( o.inSpeed == undefined ) o.inSpeed = 150;
			if( o.outSpeed == undefined ) o.outSpeed = 75;
			// 0 needs to be -1 for expected results (no fade)
			if( o.inSpeed == 0 ) o.inSpeed = -1;
			if( o.outSpeed == 0 ) o.outSpeed = -1;
			// Loop each context menu
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
							
							// Detect mouse position
							var d = {}, x, y;
							if( self.innerHeight ) {
								d.pageYOffset = self.pageYOffset;
								d.pageXOffset = self.pageXOffset;
								d.innerHeight = self.innerHeight;
								d.innerWidth = self.innerWidth;
							} else if( document.documentElement &&
								document.documentElement.clientHeight ) {
								d.pageYOffset = document.documentElement.scrollTop;
								d.pageXOffset = document.documentElement.scrollLeft;
								d.innerHeight = document.documentElement.clientHeight;
								d.innerWidth = document.documentElement.clientWidth;
							} else if( document.body ) {
								d.pageYOffset = document.body.scrollTop;
								d.pageXOffset = document.body.scrollLeft;
								d.innerHeight = document.body.clientHeight;
								d.innerWidth = document.body.clientWidth;
							}
							(e.pageX) ? x = e.pageX : x = e.clientX + d.scrollLeft;
							(e.pageY) ? y = e.pageY : y = e.clientY + d.scrollTop;
							
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
				
				// Disable text selection
				
				if( $.browser.mozilla ) {
					$('#' + o.menu).each( function() { $(this).css({ 'MozUserSelect' : 'none' }); });
				} else if( $.browser.msie ) {
					$('#' + o.menu).each( function() { $(this).bind('selectstart.disableTextSelect', function() { return false; }); });
				} else {
					$('#' + o.menu).each(function() { $(this).bind('mousedown.disableTextSelect', function() { return false; }); });
				}
				// Disable browser context menu (requires both selectors to work in IE/Safari + FF/Chrome)
				$(el).add($('ul.contextMenu')).bind('contextmenu', function() { return false; });
				
			});
			return $(this);
		},
		
		hoverMenu: function(o, callback) {
			// Defaults
			if( o.menu == undefined ) return false; // id of the menu to show
			if( o.position == undefined ) o.position = 'right'; // position relative to the source of the call
			if( o.showArrow == undefined ) o.showArrow = true; // show arrow?
			if( o.animation == undefined ) o.animation = 'fade'; // which animation for show/hide?
			if( o.inSpeed == undefined ) o.inSpeed = 250; // speed of show animation
			if( o.outSpeed == undefined ) o.outSpeed = 75; // speed of hide animation
			// 0 needs to be -1 for expected results (no fade)
			if( o.inSpeed == 0 ) o.inSpeed = -1;
			if( o.outSpeed == 0 ) o.outSpeed = -1;
			
			// get attributes for animation
			var show, hide;
			switch (o.animation) {
				case 'slide':
					show = {height: 'show'};
					hide = {height: 'hide'};
					break;
				default:
				case 'fade':
					show = {opacity: 'show'};
					hide = {opacity: 'hide'};
					break;
			}
			
			// Prepare Container
			var $menu = $('#' + o.menu), $src;
			if (!$menu.hasClass('hoverButtons')) {
				$('<div class="hoverButtons hover_'+o.position+'"></div>').append($menu).appendTo('body');
				$menu.addClass('hoverMenu').removeAttr('id').parent('div.hoverButtons').attr('id', o.menu);
				$menu = $('#' + o.menu);
			}
			if (o.showArrow === true)
				$menu.prepend('<div class="tip_arrow"><div></div></div>');
			if(o.positionType != undefined)
				$menu.css('position', o.positionType);
							
			// add events
			$(this).live('hover', function(){
				$src = $(this);

				if (o.onShow) o.onShow($src);
				
				// get menu position
				var at, my, coll;
				switch (o.position) {
					case 'bottom':
						at = 'center bottom';
						my = 'center top';
						coll = 'fit';
						break;
					default:
					case 'right':
						at = 'right top';
						my = 'left top';
						coll = 'flip';
						break;
				}
				
				// show menu
				$menu.animate(show, o.inSpeed).position({
					of: $(this),
					at: at,
					my: my,
					collision: coll
				});
				
				// hide menu
				$src.hoverIntent({
					over: function() {},
					out: function(evt) {
						if (!$(evt.relatedTarget).is('#'+o.menu+', #'+o.menu+' *'))
							$menu.animate(hide, o.outSpeed);
					},
					timeout: 500
				});
				
				// When items are selected
				var exec = function(evt) {
					$menu.animate(hide, o.outSpeed);

					// Callback
					if(callback) ret = callback($(this), $src);
					
					evt.stopPropagation();
					if (ret === true) return true;
					return false;
				};
				$menu.find('li a').unbind('click');
				$menu.find('li.disabled a').click(function() {
					return false;
				});
				$menu.find('li:not(.disabled) a').click(exec);
			});
			
			$menu.hoverIntent({
				over: function() {},
				out: function(evt) {
					if (!$(evt.relatedTarget).is('#'+$src.attr('id')+', #'+$src.attr('id')+' *'))
						$(this).animate(hide, o.outSpeed);
				},
				timeout: 500
			});
			$('#bookings').bind('click', function(){
				$menu.animate(hide, o.outSpeed);
			});
							
			return $(this);
		},
		
		hoverButtons: function(o, callback) {
			// Defaults
			if( o.menu == undefined ) return false; // id of the menu to show
			if( o.hideOn == undefined ) o.hideOn = 'click'; // which event to hide on?
			if( o.animation == undefined ) o.animation = 'fade'; // which animation for show/hide?
			if( o.inSpeed == undefined ) o.inSpeed = 250; // speed of show animation
			if( o.outSpeed == undefined ) o.outSpeed = 75; // speed of hide animation
			if (o.offset == undefined) o.offset = 0;
			// 0 needs to be -1 for expected results (no fade)
			if( o.inSpeed == 0 ) o.inSpeed = -1;
			if( o.outSpeed == 0 ) o.outSpeed = -1;
			
			// get attributes for animation
			var show, hide;
			switch (o.animation) {
				case 'slide':
					show = {height: 'show'};
					hide = {height: 'hide'};
					break;
				default:
				case 'fade':
					show = {opacity: 'show'};
					hide = {opacity: 'hide'};
					break;
			}
			
			// Prepare Container
			var $menu = $('#' + o.menu), $src;
			$menu.addClass('hoverBar');
							
			// add events
			$(this).hoverIntent(function() {//.live('mouseenter', function(){
				$src = $(this);
				
				if( $src.hasClass('disabled') ) return;
				if (o.onShow) o.onShow($src);
								
				// show menu
				$menu.show().animate(show, o.inSpeed).position({
					of: $(this),
					at: 'right top',
					my: 'right top',
					offset: o.offset,
					collision: 'fit'
				});

				// When items are selected
				$menu.find('li a').unbind('click');
				$menu.find('li.disabled a').click(function() {
					return false;
				});
				$menu.find('li:not(.disabled) a').click(function() {
					// Callback
					if(callback) callback($(this), $src);
					$menu.animate(hide, o.outSpeed);
					return false;
				});
				
				// hide menu
				/*$src.hoverIntent({
					over: function() {},
					out: function(evt) {
						if (!$(evt.relatedTarget).is('#'+o.menu+', #'+o.menu+' *, #'+$src.attr('id')+' *'))
							$menu.animate(hide, o.outSpeed);
					},
					timeout: 500
				});*/
			}, 	function(evt) {
				if (!$(evt.relatedTarget).is('#'+o.menu+', #'+o.menu+' *, #'+$src.attr('id')+' *'))
					$menu.animate(hide, o.outSpeed);
			});
			$menu.hoverIntent({
				over: function() {},
				out: function(evt) {
					if (!$(evt.relatedTarget).is('#'+$src.attr('id')+', #'+$src.attr('id')+' *'))
						$(this).animate(hide, o.outSpeed);
				},
				timeout: 200
			});
										
			return $(this);
		},
		
		// Disable context menu items on the fly
		disableMenuItems: function(o) {
			if (o == undefined) {
				// Disable all
				$(this).find('li').addClass('disabled').removeClass('removed');
			} else {
				$(this).each( function() {
					for (var i = 0; i < o.length; i++) {
						$(this).find('li.' + o[i]).addClass('disabled').removeClass('removed');
					}
				});
			}
			return($(this));
		},
		
		// Enable context menu items on the fly
		enableMenuItems: function(o) {
			if( o == undefined ) {
				// Enable all
				$(this).find('li.disabled, li.removed').removeClass('disabled removed');
			} else {
				$(this).each( function() {
					for( var i = 0; i < o.length; i++ ) {
						$(this).find('li.' + o[i]).removeClass('disabled removed');
					}
				});
			}
			return($(this));
		},
		
		// Remove menu items on the fly
		removeMenuItems: function(o) {
			if (o == undefined) {
				// Disable all
				$(this).find('li').addClass('removed').removeClass('disabled');
			} else {
				$(this).each( function() {
					for (var i = 0; i < o.length; i++) {
						$(this).find('li.' + o[i]).addClass('removed').removeClass('disabled');
					}
				});
			}
			return($(this));
		},
		
		// Disable context menu(s)
		disableContextMenu: function() {
			$(this).each( function() {
				$(this).addClass('disabled');
			});
			return( $(this) );
		},
		
		// Enable context menu(s)
		enableContextMenu: function() {
			$(this).each( function() {
				$(this).removeClass('disabled');
			});
			return( $(this) );
		},
		
		// Destroy context menu(s)
		destroyContextMenu: function() {
			// Destroy specified context menus
			$(this).each( function() {
				// Disable action
				$(this).unbind('mousedown').unbind('mouseup');
			});
			return( $(this) );
		}
	});
})(jQuery);