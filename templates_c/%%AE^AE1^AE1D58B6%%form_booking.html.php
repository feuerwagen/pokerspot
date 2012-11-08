<?php /* Smarty version 2.6.22, created on 2010-08-25 02:14:55
         compiled from form_booking.html */ ?>
<div id="messages"></div>
<form action="form/booking/<?php echo $this->_tpl_vars['path']; ?>
" method="post" class="cmxform">
<div id="aform">
	<fieldset>
		<legend title="Bitte zumindest Mail-Adresse und / oder Telefonnummer für weitere Rückfragen eintragen. Wenn vorhanden, erleichtert die Angabe einer Adresse die spätere Erstellung der Rechnung.">Kontaktdaten</legend>
		<ul>
			<li><label for="organisation">Gruppe / Veranstaltungsart</label> <input type="Text" id="organisation" name="organisation" value="<?php echo $this->_tpl_vars['b']->organisation; ?>
"/></li>
			<li><label for="name">Name <em>*</em></label> <input type="Text" id="name" class="autocomplete" name="name" value="<?php echo $this->_tpl_vars['b']->name; ?>
"/></li>
			<li><label for="email">E-Mail</label> <input type="Text" id="email" class="autocomplete" name="email" value="<?php echo $this->_tpl_vars['b']->email; ?>
"/></li>
			<li><label for="phone">Telefon</label> <input type="Text" id="phone" class="autocomplete" name="phone" value="<?php echo $this->_tpl_vars['b']->phone; ?>
"/></li>
			<li><label for="adress">Adresse</label> <textarea name="adress" id="adress"><?php echo $this->_tpl_vars['b']->adress; ?>
</textarea></li>
		</ul>
	</fieldset>
	<fieldset>
		<legend title="Start- und Enddatum der Veranstaltung. Die Daten sind im Format TT.MM.JJJJ (z.B. 31.03.2006) einzugeben.">An- und Abreise</legend>
		<ul>
			<li><label for="start">Anreise <em>*</em></label> <input type="Text" id="start" name="start" value="<?php if (is_object ( $this->_tpl_vars['b']->start )): ?><?php echo $this->_tpl_vars['b']->start->returnDate('j.n.Y'); ?>
<?php endif; ?>"/></li>
			<li><label for="end">Abreise</label> <input type="Text" id="end" name="end" value="<?php if (is_object ( $this->_tpl_vars['b']->end )): ?><?php echo $this->_tpl_vars['b']->end->returnDate('j.n.Y'); ?>
<?php endif; ?>"/></li>
		</ul>
	</fieldset>
	<fieldset>
		<legend title="Hier die von der Gruppe belegten Häuser eintragen. Mindestens ein Haus muss gewählt sein.">Belegte Häuser</legend>
		<div id="housesList">
			<?php $_from = $this->_tpl_vars['houses']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['name']):
?>
			<input type="checkbox" id="<?php echo $this->_tpl_vars['k']; ?>
" class="hList" name="houses[]" value="<?php echo $this->_tpl_vars['k']; ?>
" <?php if (is_array ( $this->_tpl_vars['b']->houses ) && in_array ( $this->_tpl_vars['k'] , $this->_tpl_vars['b']->houses )): ?>checked="checked" <?php endif; ?>/><label for="<?php echo $this->_tpl_vars['k']; ?>
" class="button"><?php echo $this->_tpl_vars['name']; ?>
</label>
			<?php endforeach; endif; unset($_from); ?>
		</div>
	</fieldset>
	<fieldset>
		<legend title="Bei Auswahl des 'Pauschal-Tarifs' bitte den vereinbarten Rechnungsbetrag eingeben. Bei größeren Gruppen sollte die Zahlung einer Kaution vereinbart werden. Diese wird unter dahinter angegebenen ReNr verbucht.">Tarif &amp; Kaution</legend>
		<ul>
			<li><label for="start">Tarif</label> <select id="rate" name="rate" size="1">
				<option value="pauschal" <?php if ($this->_tpl_vars['b']->rate == 'pauschal'): ?> selected="selected"<?php endif; ?>>pauschal</option>
				<?php $_from = $this->_tpl_vars['rates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['rate']):
?>
				<option value="<?php echo $this->_tpl_vars['rate']; ?>
"<?php if ($this->_tpl_vars['b']->rate == $this->_tpl_vars['rate']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['rate']; ?>
 €</option>
				<?php endforeach; endif; unset($_from); ?>
			</select> <span id="rate_add"><?php if ($this->_tpl_vars['b']->rate == 'pauschal' || $this->_tpl_vars['b']->rate == ''): ?>mit <input type="text" id="price" name="price" value="<?php echo $this->_tpl_vars['b']->price; ?>
" /> € Rechnungsbetrag<?php endif; ?></span></li>
			<li><label for="guests">Personen</label> <input type="Text" id="guests" name="guests" value="<?php if ($this->_tpl_vars['b']->guests > 0): ?><?php echo $this->_tpl_vars['b']->guests; ?>
<?php endif; ?>"/></li>
			<?php echo $this->_tpl_vars['b']->additional; ?>

		</ul>
	</fieldset>
	<fieldset>
		<legend title="Vorgemerkte Anmeldungen erscheinen gelb im Kalender. Zusätzlich kann angegeben werden, wie lange die Vormerkung bestehen soll. Nach Ablauf dieser Frist wird die Anmeldung automatisch storniert.">Weitere Angaben</legend>
		<ul>
			<li><input type="checkbox" id="mark" name="mark" value="1" <?php if ($this->_tpl_vars['b']->mark === true): ?>checked="checked" <?php endif; ?><?php if ($this->_tpl_vars['permission'] === true): ?>disabled="disabled" /><input type="hidden" name="mark" value="<?php if ($this->_tpl_vars['b']->mark === true): ?>1<?php endif; ?>" /><?php else: ?> /><?php endif; ?><label for="mark">Vormerkung</label> bis zum <input type="Text" name="expires" id="expires" value="<?php if (is_object ( $this->_tpl_vars['b']->expires )): ?><?php echo $this->_tpl_vars['b']->expires->returnDate('j.n.Y'); ?>
<?php endif; ?>"/></li>
			<li><label for="comment">Kommentar</label> <textarea name="comment"><?php echo $this->_tpl_vars['b']->comment; ?>
</textarea></li>
		</ul>
	</fieldset>
</div>
</form>
<?php echo '
<script>
$(document).ready(function(){
	// buttons for houses and mark field
	$(\'input.hList\').button();
	$(\'#mark\').button();
	//$(\'#housesList\').buttonset();

	// show only one fieldset at once
	$(\'#aform\').accordion({
		header: \'legend\',
		autoHeight: false
	});
	
	// help when hovering legend fields
	$(\'legend\').tipTip({delay: 1000});
	
	// autocomplete
	$("#organisation").autocomplete({
		source: function (request, response) {
			$.post("form/booking/autocomplete/organisation.html", {term: request.term, call: \'ajax\'}, response, \'json\');
		},
		minLength: 2,
		select: function(event, ui) {
			$(\'#name\').val(ui.item.name);
			$(\'#phone\').val(ui.item.phone);
			$(\'#email\').val(ui.item.email);
			$(\'#adress\').text(ui.item.adress);
			$(\'#guests\').val(ui.item.guests);
			$("#rate").find(\'option[value=\'+ui.item.rate+\']\').attr("selected","selected");
			if (ui.item.rate != \'pauschal\' && ui.item.rate != \'\') {
				$("#rate_add").empty();
			} else {
				$(\'#rate_add\').html(\'mit <input type="text" name="price" value="\'+ui.item.price+\'" /> € Rechnungsbetrag\');
			}
		}
	});
	$(\'#name\').autocomplete({
		source: function (request, response) {
			$.post("form/booking/autocomplete/name.html", {term: request.term, call: \'ajax\'}, response, \'json\');
		},
		minLength: 2,
		select: function(event, ui) {
			if ($(\'#organisation\').val() == \'\')
				$(\'#organisation\').val(ui.item.organisation);
			$(\'#phone\').val(ui.item.phone);
			$(\'#email\').val(ui.item.email);
			$(\'#adress\').text(ui.item.adress);
		}
	});
	$(\'#phone\').autocomplete({
		source: function (request, response) {
			$.post("form/booking/autocomplete/phone.html", {term: request.term, call: \'ajax\'}, response, \'json\');
		},
		minLength: 2
	});
	$(\'#email\').autocomplete({
		source: function (request, response) {
			$.post("form/booking/autocomplete/mail.html", {term: request.term, call: \'ajax\'}, response, \'json\');
		},
		minLength: 2
	});
	$(\'#adress\').autocomplete({
		source: function (request, response) {
			$.post("form/booking/autocomplete/adress.html", {term: request.term, call: \'ajax\'}, response, \'json\');
		},
		minLength: 2
	});
	
	// datepicker for start, end, expires
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
	$("#expires").datepicker();
	
	// add / remove additional form field when rate is selected
	var add = \'mit <input type="text" name="price" value="" /> € Rechnungsbetrag\';
	$(\'#rate\').change(function() {
		if ($(this).val() == \'pauschal\')
			$(\'#rate_add\').html(add);
		else {
			if ($(\'#rate_add\').html() != \'\')
				add = $(\'#rate_add\').html();
			$(\'#rate_add\').empty();
		}
	});
});
</script>
'; ?>