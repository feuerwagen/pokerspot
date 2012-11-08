<?php /* Smarty version 2.6.22, created on 2012-10-31 21:33:00
         compiled from table_open.html */ ?>
<h2>Offene Buchungen: <?php echo $this->_tpl_vars['open']; ?>
 €</h2>
<table id="accountings_open">
<thead>
    <tr>
        <th>ReNr</th>
        <th>Datum</th>
		<th>Art</th>
		<th>Gruppe</th>
		<th>Betrag</th>
		<th>Bemerkungen</th>
    </tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['numbers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acc']):
?>
    <tr name="<?php echo $this->_tpl_vars['acc']->id; ?>
" id="<?php echo $this->_tpl_vars['acc']->id; ?>
"<?php if ($this->_tpl_vars['acc']->mark === true): ?> class="notpaid"<?php endif; ?>>
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
		<td><?php echo $this->_tpl_vars['acc']->comment; ?>
</td>
    </tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php echo '
<script>
$(document).ready(function() {
'; ?>

<?php if ($this->_tpl_vars['permissions']['update'] === true || $this->_tpl_vars['permissions']['delete'] === true): ?>
<?php echo '
	$(\'#accountings_open\').find(\'tr\').contextMenu({
		menu: \'accMenu\'
	}, function($link, $el) {
		$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
		openDialog($link);
	});
'; ?>

<?php endif; ?>
<?php echo '
	$(\'#accountings_open\').tablesorter({
		headers: { 
			1: {sorter: \'shortDate\'},
			4: {sorter: \'currency\'},
			5: {sorter: false}
		}
	}).tableHover({clickClass: \'click\'});
})
</script>
'; ?>

<?php if ($this->_tpl_vars['permissions']['update'] === true || $this->_tpl_vars['permissions']['delete'] === true): ?>
<ul id="accMenu" class="contextMenu">
    <?php if ($this->_tpl_vars['permissions']['update'] === true): ?><li class="edit"><a href="admin/accounting/update/?width=500&height=450" class="form">Bearbeiten</a></li><?php endif; ?>
	<?php if ($this->_tpl_vars['permissions']['delete'] === true): ?><li class="delete"><a href="admin/accounting/delete/?width=350&height=150" class="delete">Löschen</a></li><?php endif; ?>
</ul>
<?php endif; ?>