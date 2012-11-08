<?php /* Smarty version 2.6.22, created on 2012-11-05 03:28:03
         compiled from modules_table.html */ ?>
<?php $_from = $this->_tpl_vars['groups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['name'] => $this->_tpl_vars['group']):
?>
<h3><?php echo $this->_tpl_vars['name']; ?>
</h3>
<table class="modules">
<thead>
    <tr>
        <th>Aktiviert</th>
        <th>Id</th>
		<th>Name</th>
        <th>Version</th>
        <th>Beschreibung</th>
    </tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['group']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m']):
?>
<?php $this->assign('id', $this->_tpl_vars['m']->id); ?>
    <tr>
        <td><?php if ($this->_tpl_vars['name'] != 'Kern' && $this->_tpl_vars['permission'] == true && ( ( $this->_tpl_vars['m']->active === false && $this->_tpl_vars['m']->can_act === true ) || ( $this->_tpl_vars['m']->active === true && $this->_tpl_vars['m']->can_deact === true ) )): ?><a href="form/module/activate/<?php echo $this->_tpl_vars['m']->id; ?>
" class="toggle <?php if ($this->_tpl_vars['m']->active === true): ?>active<?php endif; ?>"><img src="images/buttons/button_<?php if ($this->_tpl_vars['m']->active === true): ?>1<?php else: ?>0<?php endif; ?>.png" /></a><?php else: ?><img src="images/buttons/button_<?php if ($this->_tpl_vars['m']->active === true): ?>1<?php else: ?>0<?php endif; ?>_no.png" /><?php endif; ?></td>
        <td><?php echo $this->_tpl_vars['m']->id; ?>
</td>
		<td><?php echo $this->_tpl_vars['m']->name; ?>
</td>
        <td><?php echo $this->_tpl_vars['m']->version; ?>
</td>
        <td><?php echo $this->_tpl_vars['m']->description; ?>

            <?php if (is_array ( $this->_tpl_vars['m']->requires )): ?><br/>Abhängig von: <?php $_from = $this->_tpl_vars['m']->requires; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach[$this->_tpl_vars['id']] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach[$this->_tpl_vars['id']]['total'] > 0):
    foreach ($_from as $this->_tpl_vars['mod']):
        $this->_foreach[$this->_tpl_vars['id']]['iteration']++;
?><?php if (($this->_foreach[$this->_tpl_vars['id']]['iteration'] <= 1) !== true): ?>, <?php endif; ?><span class="<?php if ($this->_tpl_vars['modules'][$this->_tpl_vars['mod']]->active === true): ?>true<?php else: ?>false<?php endif; ?>"><?php echo $this->_tpl_vars['mod']; ?>
</span><?php endforeach; endif; unset($_from); ?><?php endif; ?>
        </td>
    </tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php endforeach; endif; unset($_from); ?>