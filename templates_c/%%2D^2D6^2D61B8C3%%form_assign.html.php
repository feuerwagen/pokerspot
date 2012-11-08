<?php /* Smarty version 2.6.22, created on 2010-09-01 03:08:24
         compiled from form_assign.html */ ?>
<div id="messages"></div>
<form action="form/mail/assign/<?php echo $this->_tpl_vars['m']->id; ?>
.html" method="post" class="cmxform">
	<fieldset>
		<legend>Zugehörige Belegung</legend>
		<ul>
			<li><input type="text" id="assign" name="assign" value="<?php if (is_object ( $this->_tpl_vars['m']->booking )): ?><?php echo $this->_tpl_vars['m']->booking->organisation; ?>
 (<?php echo $this->_tpl_vars['m']->booking->name; ?>
, <?php echo $this->_tpl_vars['m']->booking->email; ?>
)<?php endif; ?>" /><input type="hidden" id="booking" name="idbooking" value="<?php if (is_object ( $this->_tpl_vars['m']->booking )): ?><?php echo $this->_tpl_vars['m']->booking->id; ?>
<?php endif; ?>" /></li>
		</ul>
	</fieldset>
</form>
<?php echo '
<script>
$(document).ready(function(){
	$(\'#assign\').autocomplete({
		source: function (request, response) {
			$.post("form/mail/autocomplete/", {term: request.term, call: \'ajax\'}, response, \'json\');
		},
		minLength: 2,
		select: function(event, ui) {
			$(\'#booking\').val(ui.item.idbooking);
		}
	});
});
</script>
'; ?>