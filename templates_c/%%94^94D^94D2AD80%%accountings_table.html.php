<?php /* Smarty version 2.6.22, created on 2010-08-27 00:20:01
         compiled from accountings_table.html */ ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
<div id="accountings">
<?php endif; ?>
<h2>Alle Buchungen <?php echo $this->_tpl_vars['year']; ?>
: <?php echo $this->_tpl_vars['all']; ?>
 € (noch offen: <?php echo $this->_tpl_vars['open']; ?>
 €)</h2>
<table>
<thead>
    <tr>
        <th>ReNr</th>
        <th>Datum</th>
		<th>Art</th>
		<th>Gruppe</th>
		<th>Betrag</th>
		<th>Bezahlt</th>
		<th>Bemerkungen</th>
    </tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['numbers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acc']):
?>
    <tr  name="<?php echo $this->_tpl_vars['acc']->id; ?>
" id="<?php echo $this->_tpl_vars['acc']->id; ?>
"<?php if ($this->_tpl_vars['acc']->paid === false): ?> class="notpaid"<?php endif; ?>>
        <td><?php echo $this->_tpl_vars['acc']->id; ?>
</td>
		<td><?php echo $this->_tpl_vars['acc']->created->returnDate('d.m.Y'); ?>
</td>
		<td><?php echo $this->_tpl_vars['acc']->type; ?>
</td>
		<td><?php echo $this->_tpl_vars['acc']->booking->getInfoString(); ?>
</td>
        <td class="number"><?php echo $this->_tpl_vars['acc']->value; ?>
 €</td>
		<td><?php if ($this->_tpl_vars['acc']->paid !== false): ?><?php echo $this->_tpl_vars['acc']->paid->returnDate('d.m.Y'); ?>
<?php endif; ?></td>
		<td><?php echo $this->_tpl_vars['acc']->comment; ?>
</td>
    </tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php if ($this->_tpl_vars['permissions']['update'] === true || $this->_tpl_vars['permissions']['delete'] === true): ?>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#accountings\').find(\'tr\').contextMenu({
		menu: \'accMenu\'
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		openDialog($link);
	});
	
	$(\'#accountings\').find(\'table\').tablesorter({
		headers: { 
			1: {sorter: \'shortDate\'},
			4: {sorter: \'currency\'},
			5: {sorter: \'shortDate\'},
		}
	}).tableHover({clickClass: \'click\'});
})
</script>
'; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
</div>
<?php if ($this->_tpl_vars['permissions']['update'] === true || $this->_tpl_vars['permissions']['delete'] === true): ?>
<ul id="accMenu" class="contextMenu">
    <?php if ($this->_tpl_vars['permissions']['update'] === true): ?><li class="edit"><a href="admin/accounting/update/?width=500&height=450" class="form">Bearbeiten</a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['delete'] === true): ?><li class="delete"><a href="admin/accounting/delete/?width=350&height=150" class="delete">Löschen</a></li><?php endif; ?>
</ul>
<?php endif; ?>
<?php endif; ?>