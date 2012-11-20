// update current path
function updatePath(hash) {
	$(location).attr('hash', hash);
	
	path = $(document).url().attr('source').substr($(document).url().attr('base').length);
}

// display error messages after form submission
function displayMessage(messages) {
	if (typeof messages == 'object') {
		var all = '', error = false;
		$.each(messages, function(type, val){
			var text = '';
			$.each(val, function(ind, msg) {
				text += '<p>' + msg + '</p>';
			})
			all += '<div class="'+type+'">'+text+'</div>';
			if (type == 'error') error = true;
		});
	 						
		if ($('#messages').size() == 0) {
			el = ($('#fixed').size() == 0) ? '#content' : '#fixed';
			$('<div id="messages"></div>').html(all).prependTo(el).slideDown();
		} else {
			$('#messages').fadeOut('fast', function(){
				$('#messages').html(all).slideDown();
			});
		}
		$('#content, #fixed').find('#messages').click(function() {
			$(this).fadeOut();
		});
		if (error == false)
			$('#content, #fixed').find('#messages div').delay(1500).fadeOut();
	}
}

// handle json data returned after form submission
function processJson(data, status, xhr, $form) {
	$('.ui-dialog-buttonpane').find('button, a').each(function() {
		$(this).button('enable');
	});
	
	// was invalid data submitted? -> mark input elements as in data.fields
	if ($.isEmptyObject(data.fields) == false) {
		$form.find("input.error").removeClass('error');
		// mark all fields with errors
		$.each(data.fields, function(key, field){
			$form.find("input[name='"+field+"']").addClass("error");
		});
	} else if (typeof data.messages !== 'object' || (typeof data.messages == 'object' && typeof data.messages.error !== 'object' && typeof data.messages.warning !== 'object')) {
		// reload all relevant DOM parts
		if (typeof data.reload == 'object') {
			$.each(data.reload, function(module, ids){
				$.each(ids, function(key, id){	
					var sub = (typeof key == 'string') ? key + '_' : '';
					$('#'+id).load('form/'+module+'/'+sub+'reload/'+id, {call: 'load', path: path});
				});
			});
		}
		// close dialog
		$('#dialog').dialog('destroy').empty();
		
		// open dialog for invoice
		if (typeof data.dialog == 'object') {
			var option = data.dialog.option;
			if (data.dialog.module == 'invoice') {
				$('#dialog').html(option.message).dialog({
					modal: true,
					title: option.title,
					show: 'fade',
					hide: 'fade',
					width: 450,
					height: 150,
					maxWidth: $(window).width(),
					maxHeight: $(window).height(),
					buttons: {
						'Anzeigen': {
							type: 'button',
							call: function() {
								window.open('files/pdf/rechnung_'+option.id+'.pdf', '_blank');
								$(this).dialog('destroy').empty();
							},
							id: 'create'
						},
						'Mail verschicken': {
							type: 'link',
							call: function() {
								$link = $('.ui-dialog-buttonpane a.form');
								$(this).dialog('destroy');
								openDialog($link);
							},
							id: 'create',
							'class': 'form',
							href: 'admin/mail/send/'+option.id+'.html?width=700&height=520'
						},
						'Abbrechen': {
							type: 'button',
							call: function() {
								$(this).dialog('destroy').empty();
							}
						}
					}
				});
			}
		}
	}
	// display error messages / success message
	displayMessage(data.messages);
}

// get button types for dialog
function getButtons($elem) {
	if ($elem.hasClass('form')) {
		var b = {
			'Speichern': {
				type: 'button',
				call: function(evt) {
					$(this).find('form').ajaxSubmit({
						dataType: 'json',
						data: {call: 'ajax'},
						success: processJson
					});
				},
				id: 'create'
			},
			'Abbrechen': {
				type: 'button',
				call: function() {
					$(this).dialog('destroy').empty();
				}
			}
		};
		$('#dialog').delegate('input', 'keypress', function(e){
			c = e.which ? e.which : e.keyCode;
			if (c == 13) {
				e.preventDefault();
				$('#create').click();
				return false;
			}
		});
	} else if ($elem.hasClass('send')) {
		var b = {
			'Senden': {
				type: 'button',
				call: function(evt) {
					$(this).find('form').ajaxSubmit({
						dataType: 'json',
						data: {call: 'ajax'},
						success: processJson
					});
				},
				id: 'create'
			},
			'Abbrechen': {
				type: 'button',
				call: function() {
					$(this).dialog('destroy').empty();
				}
			}
		};
		$('#dialog').delegate('input', 'keypress', function(e){
			c = e.which ? e.which : e.keyCode;
			if (c == 13) {
				e.preventDefault();
				$('#create').click();
				return false;
			}
		});
	} else if ($elem.hasClass('delete')) {
		var b = {
			'Löschen': {
				type: 'button',
				call: function() {
					$.post($(this).find('input[type=hidden]').val(), {call: 'ajax'}, processJson, 'json');
				},
				id: 'delete'
			},
			'Abbrechen': {
				type: 'button',
				call: function() {
					$(this).dialog('destroy').empty();
				}
			}
		};
	} else if ($elem.hasClass('confirm')) {
		var b = {
			'OK': {
				type: 'button',
				call: function() {
					$.post($(this).find('input[type=hidden]').val(), {call: 'ajax'}, processJson, 'json');
				},
				id: 'create'
			},
			'Abbrechen': {
				type: 'button',
				call: function() {
					$(this).dialog('destroy').empty();
				}
			}
		};
	} else {
		var b = {
			'OK': {
				type: 'button',
				call: function() {
					$(this).dialog('destroy').empty();
				}
			}
		};
	}
	return b;
}

function openDialog($link) {
	$('#messages').remove();
	var b = getButtons($link);
	$('#dialog').load($link.attr('href'), {call: 'ajax'}).dialog({
		modal: true,
		title: $link.attr('title'),
		show: 'fade',
		hide: 'fade',
		width: parseInt($link.url().param('width')),
		height: parseInt($link.url().param('height')),
		maxWidth: $(window).width(),
		maxHeight: $(window).height(),
		buttons: b
	});
}

$(document).ready(function() {	
	// error messages for ajax calls	
	$.ajaxSetup({
        error: function(jqXHR, exception) {
            if (jqXHR.status === 0) {
                all = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                all = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                all = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                all = 'Requested JSON parse failed.';
                console.log(jqXHR.responseText);
            } else if (exception === 'timeout') {
                all = 'Time out error.';
            } else if (exception === 'abort') {
                all = 'Ajax request aborted.';
            } else {
                all = 'Uncaught Error.<br><br>' + jqXHR.responseText;
                console.log(jqXHR.responseText);
            }

            displayMessage({'error': [all]});
        }
    });

	$(document).ajaxError(function(e,xhr,settings,exception) {
		var all;
		if(xhr.status==0) {
			all = 'Keine Verbindung zum Server möglich!\n Prüfe bitte Deine Netzwerkverbindung.';
		} else if(xhr.status==404) {
			all = 'Bitte den Administrator benachrichtigen: Die aufgerufene URL wurde nicht gefunden.';
		} else if(xhr.status==500) {
			all = 'Bitte den Administrator benachrichtigen: Interner Server-Fehler.';
		} else if(e=='parsererror') {
			all = 'Bitte den Administrator benachrichtigen: Einlesen der empfangenen Daten fehlgeschlagen.';
			console.log(xhr.responseText);
		} else if(e=='timeout') {
			all = 'Bitte den Administrator benachrichtigen: Anfrage wurde abgebrochen.';
		} else {
			all = 'Bitte den Administrator benachrichtigen: Unbekannter Fehler.<br><br>'+xhr.responseText;
			console.log(xhr.responseText);
			console.log(e);
			console.log(exception);
		}

		displayMessage({'error': [all]});
	
	});
	
	// JqueryUI dialog for forms and confirmations
	$('a.dialog').live('click', function(){
		openDialog($(this));
		return false;
	});
		
    // button styling
	$('input:submit, button, a.button').button();
		
	// tooltips
	$('#user').find('a.dialog').tipTip();
	$('#buttons').find('a').tipTip({defaultPosition: 'left'});

	// display submenu after menu link is cicked
    $('#nav a').click(function() {
        var s = $(this).parent().attr('id');
		
		// only if not a normal link
        if ($(this).attr('href') == '#') {
            $('#bar').load("form/system/submenu/"+s, {path: path, call: 'load'});
            $(this).parent().parent('ul').children('.active').removeClass("active");
            $(this).parent().addClass("active");
            return false;
        }
    });

	// toggle status of elements (i.e. rights, modules)
	$("a.toggle").live('click', function(){
		var $link = $(this);
		if ($link.hasClass('active')) {
			var action = 'remove', img = '<img src="images/buttons/button_0.png" />';
			$link.removeClass('active');
		} else {
			var action = 'set', img = '<img src="images/buttons/button_1.png" />';
			$link.addClass('active');
		}
		
		$.post($link.attr("href"), {option: action, call: 'ajax'}, function(data) {
			//$('#messages').html(data);
			if ($.isEmptyObject(data)) {
				$link.html(img);
				$('#messages').hide();
			} else
				displayMessage(data.messages);
		}, 'json');
		return false;
	});
	
	// init options for tablesorter
	$.tablesorter.defaults.dateFormat = 'de'; 
	//$.tablesorter.defaults.debug = true;
	$.tablesorter.defaults.textExtraction = function(el) {
		return $(el).text();
	};
	$.tablesorter.addParser({ 
        id: 'rate', 
        is: function(s) { 
            return false; // no auto-detection
        }, 
        format: function(s) { 
            return s.toLowerCase().replace(/[ €]/g,'').replace(/pauschal/,1000).replace(/\((\d*)\)/,"$1"); // data normalization 
        }, 
        type: 'numeric' // numeric or text 
    });
});