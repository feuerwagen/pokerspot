<?php /* Smarty version 2.6.22, created on 2012-10-31 22:47:05
         compiled from users_table.html */ ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
<div id="users">
<?php endif; ?>
<table>
<thead>
    <tr>
        <th>Benutzer</th>
        <th>Name</th>
        <th>E-Mail</th>
        <th>Gruppe</th>
    </tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>
    <tr class="tUser cmTrigger" id="<?php echo $this->_tpl_vars['user']->username; ?>
">
        <td><?php echo $this->_tpl_vars['user']->username; ?>
</td>
        <td><?php echo $this->_tpl_vars['user']->realname; ?>
</td>
        <td><?php echo $this->_tpl_vars['user']->email; ?>
</td>
        <td><?php echo $this->_tpl_vars['user']->status; ?>
</td>
    </tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php if ($this->_tpl_vars['permissions']['update'] === true || $this->_tpl_vars['permissions']['delete'] === true): ?>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#users\').find(\'tr.tUser\').contextMenu({
		menu: \'userMenu\'
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		openDialog($link);
	});
})
</script>
'; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
</div>
<?php if ($this->_tpl_vars['permissions']['create'] === true): ?>
<a class="dialog button form" href="admin/user/create/?width=500&height=500" title="Benutzer hinzufügen">Benutzer hinzufügen</a>
<?php endif; ?>
<?php if ($this->_tpl_vars['permissions']['update'] === true || $this->_tpl_vars['permissions']['delete'] === true): ?>
<ul id="userMenu" class="contextMenu">
    <?php if ($this->_tpl_vars['permissions']['update'] === true): ?><li class="edit"><a href="admin/user/update/?width=500&height=500" class="form" title="Benutzer bearbeiten">Bearbeiten</a></li><?php endif; ?>
    <?php if ($this->_tpl_vars['permissions']['delete'] === true): ?><li class="delete"><a href="admin/user/delete/?width=350&height=150" class="delete">Löschen</a></li><?php endif; ?>
</ul>
<?php endif; ?>
<?php endif; ?>