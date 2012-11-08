<?php /* Smarty version 2.6.22, created on 2010-08-10 20:01:15
         compiled from form_rate.html */ ?>
<div id="messages"></div>
<form action="form/booking/<?php echo $this->_tpl_vars['path']; ?>
" method="post" class="cmxform">
	<fieldset>
		<legend>Daten</legend>
		<ul>
			<li><label for="value">Betrag</label> <input type="Text" name="value" value="<?php echo $this->_tpl_vars['rate']->value; ?>
" <?php if ($this->_tpl_vars['rate']->value != ''): ?>readonly="readonly"<?php endif; ?>/> €</li>
			<li><label for="color">Farbe</label> <input type="Text" id="color" name="color" value="<?php echo $this->_tpl_vars['rate']->color; ?>
" /></li>
			<li><label for="active">Aktiv</label> <input type="checkbox" name="active" value="1" <?php if ($this->_tpl_vars['rate']->active === true): ?>checked="checked" <?php endif; ?>/></li>
		</ul>
	</fieldset>
</form>
<?php echo '
<script>
	$(document).ready(function() {
		$(\'#color\').ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			onBeforeShow: function() {
				$(this).ColorPickerSetColor(this.value);
			},
			onShow: function (colpkr) {
				$(colpkr).fadeIn();
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut();
				return false;
			},
			onChange: function (hsb, hex, rgb, el) {
				$(el).val(hex);
			}
		}).bind(\'keyup\', function(){
			$(this).ColorPickerSetColor(this.value);
		});
	})
</script>
'; ?>