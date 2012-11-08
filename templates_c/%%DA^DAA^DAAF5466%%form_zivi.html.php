<?php /* Smarty version 2.6.22, created on 2010-08-20 23:24:11
         compiled from form_zivi.html */ ?>
<div id="messages"></div>
<form action="form/zivi/vacation" method="post" class="cmxform">
	<fieldset>
		<legend>Bestehende Urlaubstermine</legend>
		<ul>
			<?php $_from = $this->_tpl_vars['vacs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
			<li><?php echo $this->_tpl_vars['v']['start']->returnDate('j.n.Y'); ?>
 – <?php echo $this->_tpl_vars['v']['end']->returnDate('j.n.Y'); ?>
 <input type="checkbox" name="delete[]" value="<?php echo $this->_tpl_vars['v']['id']; ?>
" /> Löschen?</li>
			<?php endforeach; endif; unset($_from); ?>
		</ul>
	</fieldset>
	<fieldset>
		<legend>Neuen Urlaubstermin eintragen</legend>
		<ul>
			<li><label for="start">Beginn</label> <input type="Text" id="start" name="start" value=""/></li>
			<li><label for="end">Ende</label> <input type="Text" id="end" name="end" value=""/></li>
		</ul>
	</fieldset>
</form>
<?php echo '
<script>
$(document).ready(function(){
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
});
</script>
'; ?>