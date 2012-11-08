<?php /* Smarty version 2.6.22, created on 2010-08-10 03:22:52
         compiled from form_house.html */ ?>
<div id="messages"></div>
<form action="form/booking/<?php echo $this->_tpl_vars['path']; ?>
" method="post" class="cmxform">
	<fieldset>
		<legend>Daten</legend>
		<ul>
			<li><label for="realname">Name</label> <input type="Text" name="realname" value="<?php echo $this->_tpl_vars['house']->realname; ?>
"/></li>
			<li><label for="name">Kürzel</label> <input type="Text" name="name" value="<?php echo $this->_tpl_vars['house']->name; ?>
" <?php if ($this->_tpl_vars['house']->name != ''): ?>readonly="readonly"<?php endif; ?>/></li>
			<li><label for="active">Aktiv</label> <input type="checkbox" name="active" value="1" <?php if ($this->_tpl_vars['house']->active === true): ?>checked="checked" <?php endif; ?>/></li>
		</ul>
	</fieldset>
</form>