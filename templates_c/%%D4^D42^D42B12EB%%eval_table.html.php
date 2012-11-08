<?php /* Smarty version 2.6.22, created on 2010-09-04 01:33:20
         compiled from eval_table.html */ ?>
<table>
<thead>
	<tr>
		<th>Datum</th>
		<th>Bewertung</th>
		<th>Gruppe / Kommentar</th>
		<th>Benutzer</th>
	</tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['evals']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['e']):
?>
	<tr>
		<td><?php echo $this->_tpl_vars['e']->created->returnDate('d.m.Y'); ?>
</td>
		<td><?php unset($this->_sections['x']);
$this->_sections['x']['name'] = 'x';
$this->_sections['x']['loop'] = is_array($_loop=$this->_tpl_vars['e']->rate) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['x']['show'] = true;
$this->_sections['x']['max'] = $this->_sections['x']['loop'];
$this->_sections['x']['step'] = 1;
$this->_sections['x']['start'] = $this->_sections['x']['step'] > 0 ? 0 : $this->_sections['x']['loop']-1;
if ($this->_sections['x']['show']) {
    $this->_sections['x']['total'] = $this->_sections['x']['loop'];
    if ($this->_sections['x']['total'] == 0)
        $this->_sections['x']['show'] = false;
} else
    $this->_sections['x']['total'] = 0;
if ($this->_sections['x']['show']):

            for ($this->_sections['x']['index'] = $this->_sections['x']['start'], $this->_sections['x']['iteration'] = 1;
                 $this->_sections['x']['iteration'] <= $this->_sections['x']['total'];
                 $this->_sections['x']['index'] += $this->_sections['x']['step'], $this->_sections['x']['iteration']++):
$this->_sections['x']['rownum'] = $this->_sections['x']['iteration'];
$this->_sections['x']['index_prev'] = $this->_sections['x']['index'] - $this->_sections['x']['step'];
$this->_sections['x']['index_next'] = $this->_sections['x']['index'] + $this->_sections['x']['step'];
$this->_sections['x']['first']      = ($this->_sections['x']['iteration'] == 1);
$this->_sections['x']['last']       = ($this->_sections['x']['iteration'] == $this->_sections['x']['total']);
?><div class="ui-stars-star ui-stars-star-on ui-stars-star-disabled"><a></a></div><?php endfor; else: ?><img src="images/skull" /><?php endif; ?></td>
		<td><h5><?php echo $this->_tpl_vars['e']->booking->getInfoString(); ?>
</h5><?php echo $this->_tpl_vars['e']->comment; ?>
</td>
		<td><?php echo $this->_tpl_vars['e']->user->realname; ?>
</td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>