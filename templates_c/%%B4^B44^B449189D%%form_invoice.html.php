<?php /* Smarty version 2.6.22, created on 2010-08-26 17:35:01
         compiled from form_invoice.html */ ?>
<div id="messages"></div>
<form action="form/invoice/<?php echo $this->_tpl_vars['path']; ?>
" method="post" class="cmxform" id="i_form">
<div id="aform">
	<fieldset>
		<legend title="Bei Tarif entweder den Übernachtungstarif der Gruppe auswählen und die Gesamtzahl der Übernachtungen eingeben oder „pauschal“ auswählen und den Rechnungsbetrag eingeben. Der Kommentar erscheint später in der Buchungsliste.">Rechnungsdaten</legend>
		<ul>
			<li><label for="payment">Zahlungsweise</label> <select id="payment" name="payment">
				<option value="bar">Bar</option>
				<option value="<?php echo $this->_tpl_vars['renr']; ?>
"<?php if ($this->_tpl_vars['inv']->renr != 'bar'): ?> selected="selected"<?php endif; ?>>ReNr <?php echo $this->_tpl_vars['renr']; ?>
</option>
				<option value="<?php echo $this->_tpl_vars['renr']; ?>
" id="split">Beides (Bar + ReNr <?php echo $this->_tpl_vars['renr']; ?>
)</option>
			</select> <span id="payment_add"></span></li>
			<li><label for="rate">Tarif</label> <select id="rate" name="rate" size="1">
				<?php $_from = $this->_tpl_vars['rates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['rate']):
?>
				<option value="<?php echo $this->_tpl_vars['rate']['value']; ?>
"<?php if ($this->_tpl_vars['inv']->booking->rate == $this->_tpl_vars['rate']['value']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['rate']['value']; ?>
<?php if ($this->_tpl_vars['rate']['value'] != 'pauschal'): ?> €<?php endif; ?></option>
				<?php endforeach; endif; unset($_from); ?>
			</select> <span id="rate_add"><?php if ($this->_tpl_vars['inv']->booking->rate == 'pauschal'): ?>mit <input type="text" name="price" value="<?php echo $this->_tpl_vars['inv']->booking->price; ?>
" size="4" style="width:50px" /> € Rechnungsbetrag<?php endif; ?></span></li>
			<li><label for="value">Übernachtungen</label> <input type="Text" id="value" name="value" value="<?php echo $this->_tpl_vars['inv']->nights; ?>
"/></li>
			<li><label for="comment">Kommentar</label> <input type="Text" id="comment" name="comment" value=""/></li>
		</ul>
	</fieldset>
	<fieldset>
		<legend title="Hier bitte weitere Rechnungsposten wie z.B. Sauna, Feuerholz o.ä. eintragen; diese werden dann der Rechnung hinzugefügt.">Weitere Rechnungsposten</legend>
		<ul>
			<li>
				<table id="i_add">
				<thead>
					<tr>
						<th>Posten (Name)</th>
						<th>Betrag</th>
					</tr>
				</thead>
				<tbody>
				<?php $_from = $this->_tpl_vars['inv']->items; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['add']):
?>
					<tr class="i_add">
						<td><input type="text" class="i_add_title" name="add_title[]" value="<?php echo $this->_tpl_vars['add']['title']; ?>
" /></td>
						<td><input type="text" class="i_add_value" name="add_value[]" value="<?php echo $this->_tpl_vars['add']['value']; ?>
" /> € <span class="i_delete"><button class="i_add_delete" title="Löschen">Löschen</button></span></td>
					</tr>
				<?php endforeach; endif; unset($_from); ?>
					<tr class="i_add">
						<td><input type="text" class="i_add_title" name="add_title[]" value="" /></td>
						<td><input type="text" class="i_add_value" name="add_value[]" value="" /> € <span class="i_delete"></span></td>
					</tr>
				</tbody>
				</table>
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend title="Hier bitte die Belegungsdaten korrigieren. Die Eingabe einer Rechnungsadresse ist zwingend erforderlich!">Belegungsdaten</legend>
		<ul>
			<li><label for="start">Anreise</label> <input type="Text" id="start" name="start" value="<?php echo $this->_tpl_vars['inv']->booking->start->returnDate('j.n.Y'); ?>
"/></li>
			<li><label for="end">Abreise</label> <input type="Text" id="end" name="end" value="<?php echo $this->_tpl_vars['inv']->booking->end->returnDate('j.n.Y'); ?>
"/></li>
			<li><label for="adress">Rechnungsadresse</label> <textarea name="adress" id="adress"><?php echo $this->_tpl_vars['inv']->booking->adress; ?>
</textarea></li>
		</ul>
	</fieldset>
	<fieldset>
		<legend title="Bitte den automatisch generierten Rechnungstext überprüfen und ggf. korrigieren. ACHTUNG: Adresse, Datum und Betreff werden dem Text automatisch hinzugefügt! Diese Angaben also nicht manuell zum Rechnungstext hinzufügen!">Rechnungstext</legend>
		<ul>
			<li><textarea id="text" name="text" style="width:100%;height:300px"><?php echo $this->_tpl_vars['inv']->text; ?>
</textarea></li>
		</ul>
	</fieldset>
</div>
</form>
<?php echo '
<script>
$(document).ready(function(){
	// show only one fieldset at once
	$(\'#aform\').accordion({
		header: \'legend\',
		autoHeight: false
	});
	
	// help when hovering legend fields
	$(\'legend\').tipTip({delay: 1000});
	
	// datepicker for start, end
	$.datepicker.setDefaults({
		showOtherMonths: true
	});
	$("#start").datepicker({
		beforeShow: function() {
			return {maxDate: $(\'#end\').val()};
		}
	});
	$("#end").datepicker({
		beforeShow: function() {
			return {minDate: $(\'#start\').val()};
		}
	});
	
	// change form field when payment type is selected
	var add = \'mit <input type="text" name="sbar" value="" size="4" style="width:50px" /> € in Bar\';
	$(\'#payment\').change(function() {
		if ($(this).children(\':selected\').attr(\'id\') == \'split\')
			$(\'#payment_add\').html(add);
		else {
			if ($(\'#payment_add\').html() != \'\') 
				add = $(\'#payment_add\').html();
			$(\'#payment_add\').empty();
		}
	});
	
	// change form field when rate is selected
	var r = \'mit <input type="text" name="price" value="" size="4" style="width:50px" /> € Rechnungsbetrag\';
	$(\'#rate\').change(function() {
		if ($(this).val() == \'pauschal\')
			$(\'#rate_add\').html(r);
		else {
			if ($(\'#rate_add\').html() != \'\') 
				r = $(\'#rate_add\').html();
			$(\'#rate_add\').empty();
		}
	});
	
	// load invoice text if form field is changed
	$url = $(\'#i_form\').url();
	$(\'#i_form\').delegate(\'input, select\', \'change\', function() {
		$(\'#i_form\').ajaxSubmit({
			url: \'form/invoice/get/\'+$url.attr(\'file\'),
			data: {call: \'load_text\'},
			success: function(data) {
				$(\'#text\').val(data);
			}
		});
	});
	
	// add new form row if current rows are filled
    $("#i_add").delegate(\'input\', \'change\', function(){
		var is_empty = false;
		$el = $(this);
		$(".i_add").each(function(){
			if ($(this).find(\'input.i_add_title\').val() !== \'\' && $(this).find(\'input.i_add_value\').val() !== \'\') {
				// add delete button
				$(this).find(\'span.i_delete\').html(\'<button class="i_add_delete" title="Löschen">Löschen</button>\');
				$(\'button.i_add_delete\').button(\'destroy\').button({
					icons: {
						primary: \'icon-delete\'
					},
					text: false
				});
			} else {
				is_empty = true;
			}
		});
		
		if (is_empty == false) {
            $("#i_add").find(\'tbody\').append(\'<tr class="i_add"><td><input type="text" class="i_add_title" name="add_title[]" /></td><td><input type="text" class="i_add_value" name="add_value[]" /> € <span class="i_delete"></span></td></tr>\');
        }
    });

	// remove form row
	$(\'button.i_add_delete\').button({
		icons: {
			primary: \'icon-delete\'
		},
		text: false
	}).live(\'click\', function() {
		$(this).parents(\'tr\').remove();
		$(\'#i_form\').delay(200).ajaxSubmit({
			url: \'form/invoice/get/\'+$url.attr(\'file\'),
			data: {call: \'load_text\'},
			success: function(data) {
				$(\'#text\').val(data);
			}
		});
	});
});
</script>
'; ?>