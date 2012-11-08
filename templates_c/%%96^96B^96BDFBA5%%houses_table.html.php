<?php /* Smarty version 2.6.22, created on 2010-08-14 01:14:34
         compiled from houses_table.html */ ?>
<div id="houses">
<table>
<thead>
    <tr>
        <th>Aktiv</th>
        <th>Name</th>
        <th>Kürzel</th>
    </tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['houses']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['house']):
?>
    <tr id="<?php echo $this->_tpl_vars['house']['id']; ?>
">
        <td><?php if ($this->_tpl_vars['permissions']['activate'] == true): ?><a href="form/booking/house_activate/<?php echo $this->_tpl_vars['house']['id']; ?>
.html" class="toggle <?php if ($this->_tpl_vars['house']['active'] == 1): ?>active<?php endif; ?>"><?php endif; ?><img src="images/buttons/button_<?php echo $this->_tpl_vars['house']['active']; ?>
.png" /><?php if ($this->_tpl_vars['permissions']['activate'] == true): ?></a><?php endif; ?></td>
        <td><?php echo $this->_tpl_vars['house']['realname']; ?>
</td>
        <td><?php echo $this->_tpl_vars['house']['name']; ?>
</td>
    </tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php if ($this->_tpl_vars['permissions']['update'] === true): ?>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#houses\').find(\'tr\').contextMenu({
		menu: \'houseMenu\'
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		openDialog($link);
	});
})
</script>
'; ?>

<?php endif; ?>
</div>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
	<?php if ($this->_tpl_vars['permissions']['create'] === true): ?>
<a class="dialog button form" href="admin/booking/house_create/?width=500&height=320" title="Haus hinzufügen">Haus hinzufügen</a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['update'] === true): ?>
<ul id="houseMenu" class="contextMenu">
    <li class="edit"><a href="admin/booking/house_update/?width=500&height=320" class="form">Bearbeiten</a></li>
</ul>
	<?php endif; ?>
<?php endif; ?>