<?php /* Smarty version 2.6.22, created on 2010-09-05 20:43:03
         compiled from rights_table.html */ ?>
<div id="groups">
<table>
<thead>
    <tr>
		<th></th>
	<?php $_from = $this->_tpl_vars['groups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['group']):
?>
        <th class="tGroup cmTrigger" id="<?php echo $this->_tpl_vars['group']->id; ?>
"><?php echo $this->_tpl_vars['group']->name; ?>
</th>
	<?php endforeach; endif; unset($_from); ?>
    </tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['modules']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['module'] => $this->_tpl_vars['title']):
?>
	<tr>
		<td colspan="<?php echo $this->_tpl_vars['count']; ?>
"><strong><?php echo $this->_tpl_vars['title']; ?>
</strong></td>
	</tr>
	<?php $_from = $this->_tpl_vars['names'][$this->_tpl_vars['module']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['name'] => $this->_tpl_vars['right']):
?>
    <tr>
		<td><?php echo $this->_tpl_vars['right']; ?>
</td>
		<?php $_from = $this->_tpl_vars['groups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['group']):
?>
        <td><?php if ($this->_tpl_vars['permission'] == true): ?><a href="form/group/activate/<?php echo $this->_tpl_vars['id']; ?>
.html?right=<?php echo $this->_tpl_vars['module']; ?>
:<?php echo $this->_tpl_vars['name']; ?>
" class="toggle <?php if ($this->_tpl_vars['rights'][$this->_tpl_vars['module']][$this->_tpl_vars['name']][$this->_tpl_vars['id']] === true): ?>active<?php endif; ?>"><?php endif; ?><img src="images/buttons/button_<?php if ($this->_tpl_vars['rights'][$this->_tpl_vars['module']][$this->_tpl_vars['name']][$this->_tpl_vars['id']] === true): ?>1<?php else: ?>0<?php endif; ?>.png" /><?php if ($this->_tpl_vars['permissions']['activate'] === true): ?></a><?php endif; ?></td>
		<?php endforeach; endif; unset($_from); ?>
    </tr>
	<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php if ($this->_tpl_vars['permissions']['delete'] === true): ?>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#groups\').find(\'th.tGroup\').contextMenu({
		menu: \'groupMenu\'
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
<a class="dialog button form" href="admin/group/create/?width=500&height=200" title="Gruppe hinzufügen">Gruppe hinzufügen</a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['delete'] === true): ?>
<ul id="groupMenu" class="contextMenu">
    <li class="delete"><a href="admin/group/delete/?width=350&height=150" class="delete">Löschen</a></li>
</ul>
	<?php endif; ?>
<?php endif; ?>