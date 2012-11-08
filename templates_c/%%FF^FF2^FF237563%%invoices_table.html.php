<?php /* Smarty version 2.6.22, created on 2010-09-06 21:49:44
         compiled from invoices_table.html */ ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
<div id="invoices">
<?php endif; ?>
<table>
<thead>
    <tr>
        <th>Rechnungsdatum</th>
        <th>Gäste</th>
		<th>Zahlung</th>
		<th>Rechnung erstellt von</th>
    </tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['invoices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['inv']):
?>
    <tr name="<?php echo $this->_tpl_vars['inv']->booking->id; ?>
" id="<?php echo $this->_tpl_vars['inv']->booking->id; ?>
" class="tInvoice">
        <td><?php echo $this->_tpl_vars['inv']->created->returnDate('d.m.Y'); ?>
</td>
		<td><?php echo $this->_tpl_vars['inv']->booking->getInfoString(); ?>
</td>
		<td><?php if ($this->_tpl_vars['inv']->renr == 'bar'): ?><img src="images/buttons/button_1.png" /> <?php echo $this->_tpl_vars['inv']->renr; ?>
<?php else: ?><img src="images/buttons/button_<?php if ($this->_tpl_vars['inv']->renr->paid !== false): ?>1<?php else: ?>0<?php endif; ?>.png" /> <?php echo $this->_tpl_vars['inv']->renr->value; ?>
 €<?php endif; ?></td>
		<td><?php echo $this->_tpl_vars['inv']->user->realname; ?>
</td>
    </tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php if ($this->_tpl_vars['permissions']['update'] === true || $this->_tpl_vars['permissions']['delete'] === true): ?>
<?php echo '
<script>
$(document).ready(function() {
	$(\'#invoices\').find(\'tr.tInvoice\').contextMenu({
		menu: \'invMenu\'
	}, function($link, $el) {
		if ($link.attr(\'class\') != \'\') {
			$link.url().attr(\'file\', $el.attr(\'id\')+\'.html\');
			openDialog($link);
		} else {
			$link.url().attr(\'file\', \'rechnung_\'+$el.attr(\'id\')+\'.pdf\');
			window.open($link.attr(\'href\'));
		}
	});
})
</script>
'; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['call'] != 'load'): ?>
</div>
<?php if ($this->_tpl_vars['permissions']['update'] === true || $this->_tpl_vars['permissions']['delete'] === true): ?>
<ul id="invMenu" class="contextMenu">
    <?php if ($this->_tpl_vars['permissions']['update'] === true): ?><li class="b_edit"><a href="admin/invoice/update/?width=800&height=600" class="form" title="Rechnung bearbeiten">Bearbeiten</a></li><?php endif; ?>
	<li class="view"><a href="files/pdf/">Anzeigen</a></li>
    <?php if ($this->_tpl_vars['permissions']['delete'] === true): ?><li class="delete separator"><a href="admin/invoice/delete/?width=350&height=150" class="delete">Löschen</a></li><?php endif; ?>
</ul>
<?php endif; ?>
<?php endif; ?>