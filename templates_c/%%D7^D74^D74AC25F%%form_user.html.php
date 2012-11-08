<?php /* Smarty version 2.6.22, created on 2012-10-31 22:46:40
         compiled from form_user.html */ ?>
<div id="messages"></div>
<form action="form/user/<?php echo $this->_tpl_vars['path']; ?>
" method="post" class="cmxform">
	<fieldset>
		<legend>Persönliche Daten</legend>
		<ul>
			<li><label for="realname">Name</label> <input type="Text" name="realname" value="<?php echo $this->_tpl_vars['user']->realname; ?>
"/></li>
			<li><label for="email">E-Mail</label> <input type="Text" name="email" value="<?php echo $this->_tpl_vars['user']->email; ?>
"/></li>
			<li><label for="status">Status</label> <?php if (! is_array ( $this->_tpl_vars['status'] )): ?><?php echo $this->_tpl_vars['status']; ?>
<?php else: ?>
				<select name="status" size="1">
				<?php $_from = $this->_tpl_vars['status']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['s']):
?>
					<option value="<?php echo $this->_tpl_vars['s']->id; ?>
"<?php if ($this->_tpl_vars['user']->idstatus == $this->_tpl_vars['s']->id): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['s']->name; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
				</select>
			<?php endif; ?></li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>Anmeldedaten</legend>
		<ul>
			<li><label for="username">Benutzername</label> <input type="Text" name="username" value="<?php echo $this->_tpl_vars['user']->username; ?>
" <?php if ($this->_tpl_vars['user']->username != ''): ?>readonly="readonly"<?php endif; ?>/></li>
			<li><label for="password">Passwort</label> <input type="Password" name="password" /></li>
			<li><label for="password_confirm">Passwort bestätigen</label> <input type="Password" name="password_confirm" /></li>
		</ul>
	</fieldset>
</form>