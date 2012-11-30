// jQuery Poker Plugin
//
// Version 0.1
//
// (C) Elias Müller
// 

/**
 * TODO:
 * - create poker tables for each table in list (div-element inside of #pokertable) OK
 * - poll tables, on which the current user is seated + currently viewed table -> update table and indicator in list
 * - bind actions to buttons, keys (fold,check/call,bet/raise,join/leave) IA
 * - react on hide of table-div (custom event / trigger): stop polling (viewing) / poll and add marker to list (seated) IA
 * - reload sidebar when player counts change
 */
if(jQuery)( function() {
	$.extend($.fn, {
		poker: function(o, callback) {
			
			// Debug flag
			var debug = true;

			// Table registry
		    var $tables = {};

		    // Table list
		    var $list = $(this);

		    // Defaults
		    if(o == undefined) var o = {};
		    o.positions = new Array();
		    o.positions[2] = new Array(1,6);
		    o.positions[3] = new Array(1,4,8);
		    o.positions[4] = new Array(1,3,6,9);
		    o.positions[5] = new Array(1,3,5,7,9);
		    o.positions[6] = new Array(1,4,5,6,7,8);
		    o.positions[7] = new Array(1,3,4,5,7,8,9);
		    o.positions[8] = new Array(1,3,4,5,6,7,8,9);
		    o.positions[9] = new Array(1,2,3,4,5,7,8,9,10);
		    o.positions[10] = new Array(1,2,3,4,5,6,7,8,9,10);

			// Handle poll data: update table according to poll response
			function handlePollData(idtable, data) {
				$table = $tables[idtable];
				$table.data('timestamp', data.timestamp);

				if (debug) console.log(data);
				
				// update players
				displayPlayers(idtable, data);
				$table.find('button').button();
				$table.find('#radio').buttonset();

				// display community cards
				$table.find('.cards > div').empty();
				if (typeof data.game == 'object' && data.game != null) {
					if (data.game.flop !== false) {
						$.each(data.game.flop, function(key, value) {
							$table.find('.cards > div').append('<div class="card card_'+value+'"></div>');
						});
					}
					if (data.game.turn !== false) {
						$table.find('.cards > div').append('<div class="card card_'+data.game.turn+'"></div>');
					}
					if (data.game.river !== false) {
						$table.find('.cards > div').append('<div class="card card_'+data.game.river+'"></div>');
					}

					// display pot (or name of winning hand on showdown) 
					if (debug) console.log(typeof(data.winning_hand));
					if (typeof(data.winning_hand) == 'object') {
						$.each(data.winning_hand, function(key, hand) {
							$table.find('.cards > div').append('<div class="pot">'+hand+'</div>');
						});
					} else {
						var pots = false;
						$.each(data.game.pot, function(key, pot) {
							if (pot > 0 && pot !== false) {
								pots = (pots === false) ? pot : pots + ', ' + pot;	
							}
						});
						if (pots !== false) {
							$table.find('.cards > div').append('<div class="pot">'+pots+'</div>');
						}
					}
				}
				
				// update buttons
				if (data.actions == false) {
					$table.find('#pcontrols').find('.pcover').removeClass('spin').show();
				} else {
					var player = data.players[data.self],
						player_stack = parseInt(player.stack)+parseInt(player.bet),
						bet_val = 0,
						btext, bfunc = 0;

					$table.find('#pcontrols')
						.find('button').button('disable').end()
						.find('#p_bet_value').attr('disabled', 'disabled');

					$.each(data.actions, function(key, param) {
						switch (key) {
							case 'fold':
								$table.find('#p_fold').button('enable');
								break;
							case 'bet':
								if (param > player_stack) {
									btext = 'All-in';
									bfunc = player_stack;
								} else {
									btext = 'Bet';
									bfunc = param;
								}	

								$table.find('#p_bet_raise span').text(btext);
								$table.find('#p_bet_value').val(bfunc).attr({
									'min': bfunc,
									'max': player_stack,
									'step': bfunc
								}).removeAttr('disabled');
								$table.find('#p_bet_raise').button('enable').addClass('bet').removeClass('raise');
								bet_val = bfunc;
								break;
							case 'raise':
								if (param > player_stack) {
									btext = 'All-in';
									bfunc = player_stack;
								} else {
									btext = 'Raise to';
									bfunc = param;
								}	

								$table.find('#p_bet_raise span').text(btext);
								$table.find('#p_bet_value').val(bfunc).attr({
									'min': bfunc,
									'max': player_stack,
									'step': bfunc,
								}).removeAttr('disabled');
								$table.find('#p_bet_raise').button('enable').addClass('raise').removeClass('bet');
								bet_val = bfunc;
								break;
							case 'check':
								$table.find('#p_check_call span').text('Check');
								$table.find('#p_check_call').button('enable').addClass('check').removeClass('call');
								break;
							case 'call':
								$table.find('#p_check_call span').text(((param == parseInt(player.stack))?'All-in':'Call '+param));
								$table.find('#p_check_call').button('enable').data('value', param).addClass('call').removeClass('check');
								break;
							case 'debug':
								console.log(param);
								break;
						}
					});

					// buttons for bet/raise value
					if (bet_val > 0) {
						if (data.game.flop === false) {
							$table.find('#p_bet_min').data('value', bet_val).button('enable');
							bfunc = (bet_val*2 > player_stack) ? 'disable' : 'enable';
							$table.find('#p_bet_step1').data('value', bet_val*2).button({label: "X2"}).button(bfunc);
							bfunc = (bet_val*3 > player_stack) ? 'disable' : 'enable';
							$table.find('#p_bet_step2').data('value', bet_val*3).button({label: "X3"}).button(bfunc);
							bfunc = (bet_val*4 > player_stack) ? 'disable' : 'enable';
							$table.find('#p_bet_step3').data('value', bet_val*4).button({label: "X4"}).button(bfunc);
							$table.find('#p_bet_max').data('value', player_stack).button('enable');
						} else {
							$table.find('#p_bet_min').data('value', bet_val).button('enable');
							bfunc = (data.game.pot*0.5 > player_stack || data.game.pot*0.5 < bet_val) ? 'disable' : 'enable';
							$table.find('#p_bet_step1').data('value', Math.round(data.game.pot/2)).button({label: "1/2"}).button(bfunc);
							bfunc = (data.game.pot*0.75 > player_stack || data.game.pot*0.75 < bet_val) ? 'disable' : 'enable';
							$table.find('#p_bet_step2').data('value', Math.round(data.game.pot*0.66)).button({label: "2/3"}).button(bfunc);
							bfunc = (data.game.pot > player_stack || data.game.pot < bet_val) ? 'disable' : 'enable';
							$table.find('#p_bet_step3').data('value', data.game.pot).button({label: "Pot"}).button(bfunc);
							$table.find('#p_bet_max').data('value', player_stack).button('enable');
						}
					}
					
					$table.find('#pcontrols').find('.pcover').hide();
				}

				// update log
				if (data.log != null) {
					$.each(data.log, function(key, value) {
						$table.find('#plog textarea').append("\n"+value);
					});
				}	
			};

			// Handle table loading
			function handleTableLoad(idtable, data) {
				$table = $tables[idtable];
			
				$tables[idtable].append('<div id="ptable" data-idtable="'+idtable+'" data-timestamp="'+data.timestamp+'"><div class="cards"><div></div></div></div><div id="plog"><textarea readonly></textarea><button id="p_clear_log">Löschen</button><button id="p_save_log">Speichern</button></div><div id="pcontrols"><div class="pcover"></div><button id="p_fold" class="big fold">Fold</button><button id="p_check_call" class="big">Check</button><div class="pbet"><button id="p_bet_raise" class="big">Bet</button><input type="number" id="p_bet_value" min="10" max="500" value="10" /></div><div id="radio"><button id="p_bet_min" class="small">Min</button><button id="p_bet_step1" class="small">X1</button><button id="p_bet_step2" class="small">X2</button><button id="p_bet_step3" class="small">X3</button><button id="p_bet_max" class="small">Max</button></div></div>');

				// update table according to received data
				handlePollData(idtable, data);		

				// TODO: ajax responses -> error handling

				// join table
				$table.on('click', 'button.join', function() {
					var form = 'Buyin: <input type="number" id="jbuyin" min="'+$(this).data('blind')*100+'" step="'+$(this).data('blind')+'" value="'+$(this).data('blind')*100+'" /><input type="hidden" id="jseat" name="seat" value="'+$(this).data('seat')+'" /><input type="hidden" id="jtable" name="table" value="'+$(this).data('idtable')+'" />';
					$('#dialog').html(form).dialog({
						modal: true,
						title: 'Join Table',
						show: 'fade',
						hide: 'fade',
						buttons: {
							'Speichern': {
								type: 'button',
								call: function(evt) {
									$.post('form/poker/join/'+$(this).find('#jtable').val(), {
										seat: $(this).find('#jseat').val(),
										stack: $(this).find('#jbuyin').val(),
										call: 'ajax'
									}, function(data) {
										if (debug) console.log(data);
										$('#ptable').data('seated', 1);
										// close dialog
										$('#dialog').dialog('destroy').empty();
									}, 'text');
								},
								id: 'create'
							},
							'Abbrechen': {
								type: 'button',
								call: function() {
									$(this).dialog('destroy').empty();
								}
							}
						}
					});
				});

				// leave table
				$table.on('click', 'button.leave', function() {
					$.post('form/poker/leave/'+$('#ptable').data('idtable'), {call: 'ajax'}, function(data) {
						if (typeof data.messages !== 'object') {
							$('#ptable').data('seated', 0);
						}
					}, 'json');
				});

				// bet
				$table.on('click', 'button.bet', function() {
					$(this).parent().find('.pcover').addClass('spin').show();
					if (debug) console.log($(this).parents('#pcontrols').find('#p_bet_value').val());
					$.post('form/poker/bet/'+$('#ptable').data('idtable'), {
						value: $(this).parents('#pcontrols').find('#p_bet_value').val(),
						call: 'ajax'
					}, function(data) {
						//handlePollData($('#ptable').data('idtable'), data);
					}, 'text');
				});

				// raise
				$table.on('click', 'button.raise', function() {
					$(this).parent().find('.pcover').addClass('spin').show();
					if (debug) console.log($(this).parents('#pcontrols').find('#p_bet_value').val());
					$.post('form/poker/raise/'+$('#ptable').data('idtable'), {
						value: $(this).parents('#pcontrols').find('#p_bet_value').val(),
						call: 'ajax'
					}, function(data) {
						//handlePollData($('#ptable').data('idtable'), data);
					}, 'text');
				});

				// check
				$table.on('click', 'button.check', function() {
					$(this).parent().find('.pcover').addClass('spin').show();
					$.post('form/poker/check/'+$('#ptable').data('idtable'), {call: 'ajax'}, function(data) {
						if (debug) console.log(data);
						//handlePollData($('#ptable').data('idtable'), data);
					}, 'json');
				});

				// fold
				$table.on('click', 'button.fold', function() {
					$(this).parent().find('.pcover').addClass('spin').show();
					$.post('form/poker/fold/'+$('#ptable').data('idtable'), {call: 'ajax'}, function(data) {
						if (debug) console.log(data);
						//handlePollData($('#ptable').data('idtable'), data);
					}, 'text');
				});

				// call
				$table.on('click', 'button.call', function() {
					$(this).parent().find('.pcover').addClass('spin').show();
					$.post('form/poker/call/'+$('#ptable').data('idtable'), {
						value: $(this).data('value'),
						call: 'ajax'
					}, function(data) {
						if (debug) console.log(data);
						//handlePollData($('#ptable').data('idtable'), data);
					}, 'json');
				});

				// set bet/raise value
				$table.on('click', '#radio button', function() {
					$(this).parents('#pcontrols').find('#p_bet_value').val($(this).data('value'));
				});

				// all-in button
				$table.on('click', '#p_bet_max', function() {
					var $button =  $('#p_bet_raise').find('span');
					if ($button.text() != 'All-in') {
						$button.data('label', $button.text());
					}
					$button.text('All-in');
					$('#p_bet_value').attr('disabled', 'disabled');
					$table.on('click', 'button:not(#p_bet_max)', function() {
						$('#p_bet_value').removeAttr('disabled');
						$button.text($button.data('label'));
					});
				});

				// clear log
				$table.on('click', '#p_clear_log', function() {
					$(this).parent('#plog').find('textarea').text();
				});

				// save log
				$table.on('click', '#p_save_log', function() {
					// TODO
				});
			};

			function displayPlayers(idtable, data) {
				$table = $tables[idtable];
				$table.find('#ptable').find('.player').remove();

				// display players: loop through player numbers -> display seated players or join form
				for (var i = 0; i < data.table.seats; i++) {
					var info = '', seat = i, folded = '';
					// hide join-button, when player seated
					if (data.self == false) {
						info = '<h5>Player '+(i+1)+'</h5><button class="join" data-seat="'+(i+1)+'" data-blind="'+data.table.blind+'" data-idtable="'+idtable+'">Join Table</button>';
					} 
					
					if (typeof(data.players[i+1]) == 'object') {
						var card1 = 'blank', card2 = 'blank';
						
						if (data.players[i+1].fold == 'fold') {
							folded = ' folded';
						}
						
						if ((i+1) == data.self && data.cards != null) {
							card1 = data.cards[0];
							card2 = data.cards[1];
						} else if (data.player_hands && data.player_hands[i+1] != null) {
							// display cards on showdown
							card1 = data.player_hands[i+1][0];
							card2 = data.player_hands[i+1][1];
						}
						info = (data.players[i+1].waiting == true || data.game == null) ? ((data.game == null) ? '<p>Waiting for other players</p>' : '<p>Waiting for next game</p>') : '<div class="card f1 card_'+card1+'"></div><div class="card f2 card_'+card2+'"></div>';
						info += '<h5>'+data.players[i+1].name+'</h5>';
						info += '<div class="pot">';

						// display dealer button
						if (data.players[i+1].button == true) {
							info += '<div class="pbutton">D</div>';
						}
						// display player bet
						if (data.players[i+1].bet > 0) {
							 info += data.players[i+1].bet;
						}
						info += '</div>';
						info += '<div class="stack">' + ((data.winner_pots && data.winner_pots[i+1] != null) ? (data.players[i+1].stack - data.winner_pots[i+1]) + ' <span class="win">+' + data.winner_pots[i+1] + '</span>' : data.players[i+1].stack) + '</div>';

						// display 'leave' button
						if ((i+1) == data.self && data.players[i+1].leaves !== true) {
							info += '<button class="leave">Leave Table</button>';
						}

						// display leave message
						if (data.players[i+1].leaves === true) {
							info += '<div class="msg_leave">Player leaves table after current game.</div>';
						}
					} 

					// TODO: hide empty seats, if player is seated

					// reorder players, if current user seated
					if (data.self !== false) seat = (i+1-data.self);
					if (seat < 0) seat += parseInt(data.table.seats);
					$table.find('#ptable').append('<div class="player player_' + o.positions[data.table.seats][seat] + ((data.winner_pots && data.winner_pots[i+1] != null)?' winner':'') + ((data.active == i+1)?' pactive':'') + folded + '" data-position="'+(i+1)+'">'+info+'</div>');
				};
			}

			// Replace active table
			function replaceTable(idtable) {
				$('#pokertable')
					.children('div').each(function() {
						// remove other visible tables and store them into the registry
						var oldid = $(this).data('idtable');
						if (oldid != '') {
							$tables[oldid] = $(this).detach();
							if ($tables[oldid].data('seated') != 1) {
								$tables[oldid].data('active', 0);
							} 
						}
					}).end()
					.empty()
					.append($tables[idtable]);
				//$tables[idtable] = $('#atable'+idtable);
				$('#atable'+idtable).data('active', 1);

				// start polling
				if ($('#atable'+idtable).data('seated') != 1) {
					poll(idtable);
				}

				$list.children('li.active').removeClass('active');
				$list.find('#ptable'+idtable).not('.active').addClass('active');
			}

			// Main Long Poll function
    		function poll(idtable) {
		        // Open an AJAX call to the server's Long Poll PHP file
		        $.post('form/poker/poll/'+idtable, {call: 'ajax', timestamp: $tables[idtable].data('timestamp')}, function(data) {
		        	if (debug) console.log(data);
		            // Callback to handle message sent from server
		            if ($.isEmptyObject(data) == false) {
		            	if (debug) console.log(typeof data.showdown);
		            	if (typeof data.showdown == 'object') {
		            		// add additional handlePollData, if game showdown
		            		data.showdown.game.winning_hand = data.showdown.winning_hand;
		            		data.showdown.game.player_hands = data.showdown.player_hands;
		            		data.showdown.game.winner_pots = data.showdown.winner_pots;
		            		data.showdown.game.self = data.self;
		            		handlePollData(idtable, data.showdown.game);
		            		// wait for 4 sec, then display new game
		            		setTimeout(function() {handlePollData(idtable, data)}, 4000);
		            	} else {
		            		handlePollData(idtable, data);
		            	}
		            }

		            // Open the Long Poll again if table is active
		            if ($tables[idtable].data('active') == 1 || $tables[idtable].data('seated') == 1)
		            	poll(idtable);
		        }, 'json');
		    };
			
			// Loop each poker table
			if ($list.children('li').length > 0) {
				$list.children('li').each(function() {
					var $el = $(this);
					var idtable = $el.data('idtable');
					var $table = $('<div />', {
						id: 'atable' + idtable,
						'class': 'atable'
					});
					$table.data('idtable', idtable);
					$tables[idtable] = $table;

					// load active tables in background
					if ($(this).find('span.unread').count == 1) { // player is seated, so load table
						$.post('form/poker/load/'+idtable, {call: 'ajax'}, function(data) {
							handleTableLoad(idtable, data);
						}, 'text');
					}

					// load table into main div, when selected
					$el.on('click', function() {
						idtable = $(this).data('idtable');
						if ($(this).hasClass('active')) { // table is loaded & visible
							return false;
						} else if ($tables[idtable].children().length > 0) { // table is loaded, just make visible
							replaceTable(idtable);
						} else { // table not loaded
							$.post('form/poker/load/'+idtable, {call: 'ajax'}, function(data) {
								handleTableLoad(idtable, data);
								replaceTable(idtable);		
							}, 'json');
						}
						return false;
					});
				});
			}
			// load specific table
			else if ($list.data('idtable') != '') {
				var idtable = $list.data('idtable');
				var $table = $('<div />', {
					id: 'atable' + idtable,
					'class': 'atable'
				});
				$table.data('idtable', idtable);
				$tables[idtable] = $table;
				$.post('form/poker/load/'+idtable, {call: 'ajax'}, function(data) {
					handleTableLoad(idtable, data);
					$list.empty().append($tables[idtable]);
					$('#atable'+idtable).data('active', 1);

					// start polling
					poll(idtable);
				}, 'json');
			}
			return $(this);
		},
	});
})(jQuery);