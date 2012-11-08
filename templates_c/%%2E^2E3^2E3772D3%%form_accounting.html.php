<?php /* Smarty version 2.6.22, created on 2010-08-13 01:43:08
         compiled from form_accounting.html */ ?>
<div id="messages"></div>
<form action="form/accounting/<?php echo $this->_tpl_vars['path']; ?>
" method="post" class="cmxform">
	<fieldset>
		<legend>Info</legend>
		<ul>
			<li><label for="renr">ReNr</label> <input type="Text" name="renr" value="<?php echo $this->_tpl_vars['acc']->renr; ?>
" readonly="readonly" /></li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>Daten</legend>
		<ul>
			<li><label for="value">Betrag</label> <input type="Text" name="value" value="<?php echo $this->_tpl_vars['acc']->value; ?>
" /> €</li>
			<li><label for="paid">Bezahlt</label> <input type="Text" id="paid" name="paid" value="<?php if ($this->_tpl_vars['acc']->paid !== false): ?><?php echo $this->_tpl_vars['acc']->paid->returnDate('d.m.Y'); ?>
<?php endif; ?>" /></li>
			<li><label for="comment">Bemerkung</label> <textarea name="comment"><?php echo $this->_tpl_vars['acc']->comment; ?>
</textarea></li>
		</ul>
	</fieldset>
</form>
<?php echo '
<script>
$(document).ready(function(){
	// datepicker for paid
	$.datepicker.setDefaults({
		showOtherMonths: true
	});
	$("#paid").datepicker();
});
</script>
'; ?>